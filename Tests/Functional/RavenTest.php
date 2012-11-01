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
    /**
     * @dataProvider getConfigs
     */
    public function testWelcome($config, $insulate)
    {
        $client = $this->createClient(array('test_case' => 'Raven', 'root_config' => $config));
        if ($insulate) {
            $client->insulate();
        }

        $crawler = $client->request('GET', '/');
        $this->assertContains('This is unsecured.', $crawler->text());

        $client->restart();

        $crawler = $client->request('GET', '/secured');
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertContains('This is secured. You are', $crawler->text());
    }

    public function getConfigs()
    {
        return array(
            // configfile, insulate
            array('config.yml', true),
            array('config.yml', false),
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->deleteTmpDir('RavenTest');
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteTmpDir('RavenTest');
    }
}
