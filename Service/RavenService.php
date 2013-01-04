<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Service;

/**
 * RavenService.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
interface RavenService
{
    /**
     * Get Raven URL.
     *
     * @return string Raven URL.
     */
    public function getUrl();

    /**
     * Get Raven public-key certificate.
     *
     * @return string Raven public-key certificate.
     */
    public function getCertificate();

    /**
     * Get Raven kid.
     *
     * @return int Raven kid.
     */
    public function getKid();
}
