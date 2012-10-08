<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * RavenException.
 *
 * Thrown when Raven returns anything other than a successful authentication.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenException extends AuthenticationException
{
}
