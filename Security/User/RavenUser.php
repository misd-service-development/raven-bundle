<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Security\User;

use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * RavenUser is the user implementation used by the Raven user provider.
 *
 * This should not be used for anything else.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
final class RavenUser implements UserInterface
{
    private $username;
    private $roles;

    /**
     * Constructor.
     *
     * @param string $username Username (=CRSid)
     * @param array $roles Roles
     *
     * @throws InvalidArgumentException
     */
    public function __construct($username, array $roles = array())
    {
        if (empty($username)) {
            throw new InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = $username;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
