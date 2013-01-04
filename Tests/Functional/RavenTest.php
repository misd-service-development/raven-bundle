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

    public function test200Response()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', $this->route('unsecured'));
        $this->assertContains('This is unsecured.', $crawler->text());

        $crawler = $client->request('GET', $this->route('secured'));
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertContains('This is secured. You are', $crawler->text());
        $this->assertEquals($this->route('secured', array(), true), $client->getRequest()->getUri());

        $crawler = $client->request('GET', $this->route('secured'));
        $this->assertContains('This is secured. You are', $crawler->text());
        $this->assertEquals($this->route('secured', array(), true), $client->getRequest()->getUri());

        $client->restart();

        $crawler = $client->request('GET', $this->route('secured', array('param1' => 'foo', 'param2' => 'bar')));
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();
        $this->assertContains('This is secured. You are', $crawler->text());
        $this->assertEquals(
            $this->route('secured', array('param1' => 'foo', 'param2' => 'bar'), true),
            $client->getRequest()->getUri()
        );
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\AuthenticationCancelledException
     */
    public function test410Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=410');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test510Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=510');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test520Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=520');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test530Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=530');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test540Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=540');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test560Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=560');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function test570Response()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=570');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testUnknownResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&status=999');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\LoginTimedOutException
     */
    public function testTimedOutResponse()
    {
        $client = $this->createClient();

        $client->request('GET', '/secured');
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&expired=true');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testInvalidResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=invalid');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testIncompleteResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=incomplete');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testWrongKidResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=kid');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testWrongUrlResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=url');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testWrongAuthResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=auth');
        $client->followRedirect();
        $client->followRedirect();
    }

    /**
     * @expectedException \Misd\RavenBundle\Exception\RavenException
     */
    public function testWrongSsoResponse()
    {
        $client = $this->createClient();

        $client->request('GET', $this->route('secured'));
        $client->request('GET', $client->getResponse()->getTargetUrl() . '&problem=sso');
        $client->followRedirect();
        $client->followRedirect();
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
