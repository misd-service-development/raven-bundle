<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array(
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new Misd\RavenBundle\MisdRavenBundle(),
    new Misd\RavenBundle\Tests\Functional\src\RavenBundle\RavenBundle(),
    new Misd\RavenBundle\Tests\Functional\src\TestBundle\TestBundle(),
);
