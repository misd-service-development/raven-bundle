<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Security\Authentication\Provider;

use Exception;
use Misd\RavenBundle\Security\Authentication\Token\RavenUserToken;
use Misd\RavenBundle\Exception\RavenException;
use Misd\RavenBundle\Exception\LoginTimedOutException;
use Misd\RavenBundle\Service\RavenService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * RavenAuthenticationProvider.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenAuthenticationProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var RavenService
     */
    private $service;

    /**
     * @var Request
     */
    private $request;

    /**
     * Constructor.
     *
     * @param UserProviderInterface $userProvider User provider.
     * @param RavenService          $service      Raven service.
     * @param Container             $container    Service container.
     */
    public function __construct(UserProviderInterface $userProvider, RavenService $service, Container $container)
    {
        $this->userProvider = $userProvider;
        $this->service = $service;
        if ($container->isScopeActive('request')) {
            $this->request = $container->get('request');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        if ((time() - $token->getAttribute('issue')->getTimestamp() > 30)) {
            throw new LoginTimedOutException();
        } elseif (false === $this->validateToken($token)) {
            throw new RavenException('Invalid Raven response');
        } elseif ($token->getAttribute('kid') !== $this->service->getKid()) {
            throw new RavenException('Invalid Raven kid');
        } elseif ($token->getAttribute('url') !== $this->request->getUri()) {
            throw new RavenException('URL mismatch');
        } elseif ('pwd' !== $token->getAttribute('auth') && null !== $token->getAttribute('auth')) {
            throw new RavenException('Invalid Raven auth');
        } elseif ('pwd' !== $token->getAttribute('sso') && null === $token->getAttribute('auth')) {
            throw new RavenException('Invalid Raven sso');
        }

        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        return new RavenUserToken($user, $user->getRoles());
    }

    /**
     * Validate RavenUserToken.
     *
     * @param RavenUserToken $token Raven user token.
     *
     * @return bool true if the token is valid, false otherwise.
     *
     * @throws Exception
     */
    protected function validateToken(RavenUserToken $token)
    {
        $data = rawurldecode(
            implode(
                '!',
                array(
                    $token->getAttribute('ver'),
                    $token->getAttribute('status'),
                    $token->getAttribute('msg'),
                    $token->getAttribute('issue')->format('Ymd\THis\Z'),
                    $token->getAttribute('id'),
                    $token->getAttribute('url'),
                    $token->getUsername(),
                    $token->getAttribute('auth'),
                    $token->getAttribute('sso'),
                    $token->getAttribute('life'),
                    $token->getAttribute('params'),
                )
            )
        );

        $sig = base64_decode(
            preg_replace(
                array(
                    '/-/',
                    '/\./',
                    '/_/',
                ),
                array(
                    '+',
                    '/',
                    '=',
                ),
                rawurldecode($token->getAttribute('sig'))
            )
        );

        $key = openssl_pkey_get_public($this->service->getCertificate());

        $result = openssl_verify($data, $sig, $key);

        openssl_free_key($key);

        switch ($result) {
            case 1:
                return true;
                break;
            case 0:
                return false;
                break;
            // @codeCoverageIgnoreStart
            default:
                throw new Exception('OpenSSL error');
                break;
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof RavenUserToken;
    }
}
