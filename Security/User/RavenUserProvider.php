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

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * RavenUserProvider.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenUserProvider implements UserProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @return RavenUser Raven user
     */
    public function loadUserByUsername($username)
    {
        return new RavenUser($username, array('ROLE_USER'));
    }

    /**
     * {@inheritdoc}
     *
     * @return RavenUser Raven user
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof RavenUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Misd\RavenBundle\Security\User\RavenUser';
    }
}
