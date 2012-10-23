<?php

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
}
