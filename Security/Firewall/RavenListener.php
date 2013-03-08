<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Security\Firewall;

use Misd\RavenBundle\Event\RavenEvents;
use Misd\RavenBundle\Event\RedirectEvent;
use Misd\RavenBundle\Exception\AuthenticationCancelledException;
use Misd\RavenBundle\Exception\RavenException;
use Misd\RavenBundle\Security\Authentication\Token\RavenUserToken;
use Misd\RavenBundle\Service\RavenServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * RavenListener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenListener implements ListenerInterface
{
    /**
     * Security context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Authentication manager.
     *
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Raven service.
     *
     * @var RavenServiceInterface
     */
    protected $raven;

    /**
     * Logger, or null.
     *
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface       $securityContext       Security context.
     * @param AuthenticationManagerInterface $authenticationManager Authentication manager.
     * @param EventDispatcherInterface       $dispatcher            Event dispatcher.
     * @param RavenServiceInterface          $raven                 Raven service.
     * @param LoggerInterface|null           $logger                Logger
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        EventDispatcherInterface $dispatcher,
        RavenServiceInterface $raven,
        LoggerInterface $logger = null
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->dispatcher = $dispatcher;
        $this->raven = $raven;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();

        if ($session->has('wls_response')) {
            // There's a Raven response to process

            $token = RavenUserToken::factory($session->get('wls_response'));
            $session->remove('wls_response');

            if (null !== $this->logger) {
                $this->logger->debug('Found WLS response', array('CRSid' => $token->getUsername()));
            }

            switch ($token->getAttribute('status')) {
                case 200:
                    // Successful authentication
                    break;
                case 410:
                    throw new AuthenticationCancelledException();
                    break;
                default:
                    switch ($token->getAttribute('status')) {
                        case 510:
                            $message = 'No mutually acceptable authentication types available';
                            break;
                        case 520:
                            $message = 'Unsupported protocol version';
                            break;
                        case 530:
                            $message = 'General request parameter error';
                            break;
                        case 540:
                            $message = 'Interaction would be required';
                            break;
                        case 560:
                            $message = 'WAA not authorised';
                            break;
                        case 570:
                            $message = 'Authentication declined';
                            break;
                        default:
                            $message = null;
                            break;
                    }
                    throw new RavenException($message, null, $token->getAttribute('status'));
                    break;
            }

            $token = $this->authenticationManager->authenticate($token);

            $this->securityContext->setToken($token);
            $this->dispatcher->dispatch(RavenEvents::LOGIN, new InteractiveLoginEvent($request, $token));
        } elseif (
            $this->securityContext->getToken() != null &&
            $this->securityContext->getToken()->getUser() instanceof UserInterface
        ) {
            // The user is already logged in
        } else {
            $this->requestAuthentication($event, $request->getUri());
        }
    }

    /**
     * Request Raven authentication.
     *
     * @param GetResponseEvent $responseEvent Get response event.
     * @param string           $url           Redirect URL.
     */
    protected function requestAuthentication(GetResponseEvent $responseEvent, $url)
    {
        $redirectEvent = new RedirectEvent(array('ver' => 2, 'url' => $url));

        $this->dispatcher->dispatch(RavenEvents::REDIRECT, $redirectEvent);

        $parameters = array();
        foreach ($redirectEvent->params as $key => $val) {
            $parameters[] = $key . '=' . utf8_encode(urlencode($val));
        }
        $parameters = '?' . implode('&', $parameters);

        if (null !== $this->logger) {
            $this->logger->debug('Redirecting to Raven');
        }

        $responseEvent->setResponse(new RedirectResponse($this->raven->getUrl() . $parameters, 303));
    }
}
