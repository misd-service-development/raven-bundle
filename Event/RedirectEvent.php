<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Redirecting to Raven event.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RedirectEvent extends Event
{
    /**
     * Parameters to send to Raven.
     *
     * @var array
     */
    public $params;

    /**
     * Constructor.
     *
     * @param array $params Parameters to send to Raven.
     */
    public function __construct(array $params = array())
    {
        $this->params = $params;
    }
}
