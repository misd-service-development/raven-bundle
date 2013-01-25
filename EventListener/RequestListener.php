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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Request listener.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RequestListener
{
    /**
     * Listen for the Raven response, store it in the session and redirect back
     * to the same page.
     *
     * @param GetResponseEvent $event Get response event.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false === $request->query->has('WLS-Response')) {
            return;
        }

        $wlsResponse = $request->query->get('WLS-Response');
        $request->query->remove('WLS-Response');
        $uri = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();
        if ($request->query->count() > 0) {
            $uri .= '?' . http_build_query($request->query->all());
        }
        $request->getSession()->set('wls_response', $wlsResponse);
        $event->setResponse(new RedirectResponse($uri));
    }
}
