<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Security\Authentication\Token;

use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * RavenUserToken.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenUserToken extends AbstractToken
{
    /**
     * Constructor.
     *
     * @param string $uid   User ID.
     * @param array  $roles Roles.
     */
    public function __construct($uid = '', array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($uid);

        if (!empty($uid)) {
            $this->setAuthenticated(true);
        }
    }

    /**
     * Build a token from a WLS response.
     *
     * @param string $wlsResponse WLS response.
     *
     * @return RavenUserToken Token.
     */
    public static function factory($wlsResponse)
    {
        list($ver, $status, $msg, $issue, $id, $url, $principal, $auth, $sso, $life, $params, $kid, $sig) = explode(
            '!',
            $wlsResponse
        );

        $token = new RavenUserToken($principal);

        $token->setAttributes(
            array(
                'ver' => (int) $ver,
                'status' => (int) $status,
                'msg' => $msg != '' ? (string) $msg : null,
                'issue' => new DateTime($issue),
                'id' => (string) $id,
                'url' => (string) $url,
                'auth' => $auth != '' ? (string) $auth : null,
                'sso' => (string) $sso,
                'life' => $life != '' ? (int) $life : null,
                'params' => $params != '' ? (string) $params : null,
                'kid' => (int) $kid,
                'sig' => (string) $sig,
            )
        );

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }
}
