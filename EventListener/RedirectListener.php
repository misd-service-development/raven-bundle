<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\EventListener;

use Misd\RavenBundle\Event\RedirectEvent;

/**
 * RedirectListener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RedirectListener
{
    private $description;

    /**
     * Constructor.
     *
     * @param string|null $description Site description.
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Listen for the Raven redirect event and add the site description.
     *
     * @param RedirectEvent $event Redirect event.
     */
    public function onRavenRedirect(RedirectEvent $event)
    {
        if (null !== $this->description) {
            $event->params['desc'] = $this->description;
        }
    }
}
