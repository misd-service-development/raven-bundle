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

/**
 * RavenTest.
 *
 * @group functional
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenTest extends WebTestCase
{
    protected static $testCase = 'Raven';
    protected static $config = 'config.yml';

    protected static function createClient()
    {
        return parent::createClient(array('test_case' => self::$testCase, 'root_config' => self::$config));
    }

    protected function route($name, $parameters = array(), $absolute = false)
    {
        return self::$kernel->getContainer()->get('router')->generate($name, $parameters, $absolute);
    }

    public function testWelcome()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', $this->route('unsecured'));
        $this->assertContains('This is unsecured.', $crawler->text());

        $crawler = $client->request('GET', $this->route('secured'));
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertContains('This is secured. You are', $crawler->text());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->deleteTmpDir(self::$testCase);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteTmpDir(self::$testCase);
    }
}
