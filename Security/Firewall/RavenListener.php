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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Misd\RavenBundle\Exception\RavenException;
use Misd\RavenBundle\Exception\AuthenticationCancelledException;
use Misd\RavenBundle\Security\Authentication\Token\RavenUserToken;
use Misd\RavenBundle\Service\RavenService;

/**
 * RavenListener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $description;
    protected $service;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface       $securityContext       Security context.
     * @param AuthenticationManagerInterface $authenticationManager Authentication manager.
     * @param string                         $description           Resource description.
     * @param RavenService                   $service               Raven service.
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $description,
        RavenService $service
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->description = $description;
        $this->service = $service;
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

            $this->securityContext->setToken($this->authenticationManager->authenticate($token));
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
     * @param GetResponseEvent $event Get response event.
     * @param string           $url   Redirect URL.
     */
    protected function requestAuthentication(GetResponseEvent $event, $url)
    {
        $params['ver'] = 2;
        $params['url'] = urlencode($url);
        $params['desc'] = urlencode($this->description);

        $parameters = array();
        foreach ($params as $key => $val) {
            $parameters[] = $key . '=' . utf8_encode($val);
        }
        $parameters = '?' . implode('&', $parameters);

        $event->setResponse(new RedirectResponse($this->service->getUrl() . $parameters, 303));
    }
}
