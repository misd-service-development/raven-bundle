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
 * Redirect to Raven event listener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RedirectListener
{
    /**
     * Site description.
     *
     * @var string|null
     */
    private $description;

    /**
     * Constructor.
     *
     * @param string|null $description Site description to send to Raven.
     */
    public function __construct($description)
    {
        $this->description = $description;
    }

    /**
     * Add the site description to the parameters sent to Raven.
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
