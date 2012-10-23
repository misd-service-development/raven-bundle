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
use Misd\RavenBundle\Service\RavenService;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * RavenAuthenticationProvider.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $service;

    /**
     * Constructor.
     *
     * @param UserProviderInterface $userProvider User provider.
     * @param RavenService          $service      Raven service.
     */
    public function __construct(UserProviderInterface $userProvider, RavenService $service)
    {
        $this->userProvider = $userProvider;
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if (
            $user instanceof UserInterface &&
            200 === $token->getAttribute('status') &&
            true === $this->validateToken($token)
        ) {
            if ((time() - $token->getAttribute('issue')->getTimestamp() > 30)) {
                throw new RavenException('Login attempt timed out');
            }
            $authenticatedToken = new RavenUserToken($user, $user->getRoles());

            return $authenticatedToken;
        }

        throw new RavenException('The Raven authentication failed.');
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
            default:
                throw new Exception('OpenSSL error');
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof RavenUserToken;
    }
}
