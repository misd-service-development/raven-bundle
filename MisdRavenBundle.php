<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Misd\RavenBundle\DependencyInjection\MisdRavenExtension;
use Misd\RavenBundle\DependencyInjection\Security\Factory\RavenFactory;

/**
 * MisdRavenBundle.
 *
 * Allows users to authenticate with {@link http://raven.cam.ac.uk/ Raven}, the
 * University of Cambridge's central authentication service.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class MisdRavenBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new MisdRavenExtension());
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new RavenFactory());
    }
}
