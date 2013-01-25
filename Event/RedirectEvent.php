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
 * RedirectEvent.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RedirectEvent extends Event
{
    public $params;

    public function __construct(array $params = array())
    {
        $this->params = $params;
    }
}
