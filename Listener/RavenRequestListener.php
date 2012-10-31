<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * RavenRequestListener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenRequestListener
{
    /**
     * Watch for Raven-related exceptions stored in the session.
     *
     * @param GetResponseEvent $event
     *
     * @throws \Exception
     *
     * @see RavenExceptionListener::onKernelException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getSession()->has('raven_exception')) {
            $exception = $request->getSession()->get('raven_exception');
            $event->getRequest()->getSession()->remove('raven_exception');

            throw $exception;
        }
    }
}
