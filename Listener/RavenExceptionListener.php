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

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * RavenExceptionListener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenExceptionListener
{
    /**
     * Watch for Raven-related exceptions.
     *
     * This stores the exception in the session and redirects to the same URI
     * minus `?WLS-Response`.
     *
     * @param GetResponseForExceptionEvent $event
     *
     * @see RavenRequestListener::onKernelRequest
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();

        if (false === $request->query->has('WLS-Response')) {
            return;
        }

        $request->query->remove('WLS-Response');
        $uri = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();
        if ($request->query->count() > 0) {
            $uri .= '?' . http_build_query($request->query->all());
        }
        $event->getRequest()->getSession()->set('raven_exception', $event->getException());
        $event->setResponse(new RedirectResponse($uri));
    }
}
