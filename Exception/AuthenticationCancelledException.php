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

use Exception;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * AuthenticationCancelledException.
 *
 * Thrown when the user cancels the Raven authentication process.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class AuthenticationCancelledException extends AuthenticationException
{
    public function __construct($extraInformation = null, $code = 0, Exception $previous = null)
    {
        parent::__construct('The user cancelled the authentication request', $extraInformation, $code, $previous);
    }
}
