<?php

/*
 * This file is part of the MisdRavenBundle for Symfony2.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\RavenBundle\Test\Security\Authentication\Token;

use PHPUnit_Framework_TestCase;
use Misd\RavenBundle\Security\Authentication\Token\RavenUserToken,
    Misd\RavenBundle\Security\User\RavenUser;

/**
 * RavenUserToken test.
 *
 * @author Chris Wilkinson <chris.wilkinson@admin.cam.ac.uk>
 */
class RavenUserTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Misd\RavenBundle\Security\Authentication\Token\RavenUserToken::__construct
     */
    public function testConstructor()
    {
        $token = new RavenUserToken('test0001');
        $this->assertEquals('test0001', $token->getUsername());

        $user = new RavenUser('test0001');
        $token = new RavenUserToken($user);
        $this->assertEquals($user, $token->getUser());
    }

    /**
     * @covers \Misd\RavenBundle\Security\Authentication\Token\RavenUserToken::factory
     */
    public function testFactory()
    {
        $wlsResponse = '2!200!!20121024T140812Z!1351087692-2052-8!http://example.cam.ac.uk/!test0001!pwd!!36000!!901!j8xWV8.XvwGoqIyHqXk9eyXeexGZLCc7sFfFciXoSwYnjn.BEvcFAgQy2j9Yt76WNad3Bvja8pLWAGLaAFryjXxnxSLrygy.VREHa5c-DH.UzFlUXssBS1.8LnZv1BVLS12qnqtzMjfgn8lCHnYdMDJ1ZV7pbV0hi-GcGUfgIYk_';

        $token = RavenUserToken::factory($wlsResponse);

        $this->assertEquals(2, $token->getAttribute('ver'));
        $this->assertEquals(200, $token->getAttribute('status'));
        $this->assertNull($token->getAttribute('msg'));
        $this->assertInstanceOf('\DateTime', $token->getAttribute('issue'));
        $this->assertEquals('2012-10-24 14:08:12', $token->getAttribute('issue')->format('Y-m-d H:i:s'));
        $this->assertEquals('1351087692-2052-8', $token->getAttribute('id'));
        $this->assertEquals('http://example.cam.ac.uk/', $token->getAttribute('url'));
        $this->assertEquals('pwd', $token->getAttribute('auth'));
        $this->assertEquals('', $token->getAttribute('sso'));
        $this->assertEquals(36000, $token->getAttribute('life'));
        $this->assertNull($token->getAttribute('params'));
        $this->assertEquals(901, $token->getAttribute('kid'));
        $this->assertEquals(
            'j8xWV8.XvwGoqIyHqXk9eyXeexGZLCc7sFfFciXoSwYnjn.BEvcFAgQy2j9Yt76WNad3Bvja8pLWAGLaAFryjXxnxSLrygy.VREHa5c-DH.UzFlUXssBS1.8LnZv1BVLS12qnqtzMjfgn8lCHnYdMDJ1ZV7pbV0hi-GcGUfgIYk_',
            $token->getAttribute('sig')
        );
    }

    /**
     * @covers \Misd\RavenBundle\Security\Authentication\Token\RavenUserToken::getCredentials
     */
    public function testGetCredentials()
    {
        $token = new RavenUserToken('test0001');

        $this->assertEquals('', $token->getCredentials());
    }
}
