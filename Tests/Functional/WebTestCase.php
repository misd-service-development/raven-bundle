<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

abstract class WebTestCase extends BaseWebTestCase
{
    public static function assertRedirect($response, $location)
    {
        self::assertTrue(
            $response->isRedirect(),
            'Response is not a redirect, got status code: ' . $response->getStatusCode()
        );
        self::assertEquals('http://localhost' . $location, $response->headers->get('Location'));
    }

    protected function deleteTmpDir($testCase)
    {
        if (!file_exists($dir = sys_get_temp_dir() . '/' . Kernel::VERSION . '/' . $testCase)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getKernelClass()
    {
        require_once __DIR__ . '/app/AppKernel.php';

        return 'Misd\RavenBundle\Tests\Functional\AppKernel';
    }

    protected static function createKernel(array $options = array())
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            $options['test_case'],
            isset($options['root_config']) ? $options['root_config'] : 'config.yml',
            isset($options['environment']) ? $options['environment'] : 'ravenbundletest',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
