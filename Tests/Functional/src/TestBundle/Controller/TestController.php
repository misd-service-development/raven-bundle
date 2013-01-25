<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Tests\Functional\src\TestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;

/**
 * TestController.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class TestController extends ContainerAware
{
    public function unsecuredAction()
    {
        return new Response('This is unsecured.');
    }

    public function securedAction()
    {
        return new Response(sprintf(
            'This is secured. You are %s.',
            $this->container->get('security.context')->getToken()->getUsername()
        ));
    }
}
