<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2016 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Jose\Object\JWK;
use Jose\Object\JWKSet;

/**
 * Class JWKTest.
 *
 * @group Unit
 * @group JWK
 */
class JWKTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testKey()
    {
        $jwk = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'use'     => 'sign',
            'key_ops' => ['sign'],
            'alg'     => 'ES256',
            'bar'     => 'plic',
        ]);

        $this->assertEquals('EC', $jwk->get('kty'));
        $this->assertEquals('ES256', $jwk->get('alg'));
        $this->assertEquals('sign', $jwk->get('use'));
        $this->assertFalse($jwk->has('kid'));
        $this->assertEquals(['sign'], $jwk->get('key_ops'));
        $this->assertEquals('P-256', $jwk->get('crv'));
        $this->assertFalse($jwk->has('x5u'));
        $this->assertFalse($jwk->has('x5c'));
        $this->assertFalse($jwk->has('x5t'));
        $this->assertFalse($jwk->has('x5t#256'));
        $this->assertEquals('f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU', $jwk->get('x'));
        $this->assertEquals('x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0', $jwk->get('y'));
        $this->assertEquals('{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","use":"sign","key_ops":["sign"],"alg":"ES256","bar":"plic"}', json_encode($jwk));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The parameter "kty" is mandatory.
     */
    public function testBadConstruction()
    {
        new JWK([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The value identified by "ABCD" does not exist.
     */
    public function testBadCall()
    {
        $jwk = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'use'     => 'sign',
            'key_ops' => ['sign'],
            'alg'     => 'ES256',
            'bar'     => 'plic',
        ]);

        $jwk->get('ABCD');
    }

    /**
     *
     */
    public function testKeySet()
    {
        $jwk1 = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'use'     => 'sign',
            'key_ops' => ['sign'],
            'alg'     => 'ES256',
            'kid'     => '0123456789',
        ]);

        $jwk2 = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'd'       => 'jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI',
            'use'     => 'sign',
            'key_ops' => ['verify'],
            'alg'     => 'ES256',
            'kid'     => '9876543210',
        ]);

        $jwkset = new JWKSet();
        $jwkset = $jwkset->addKey($jwk1);
        $jwkset = $jwkset->addKey($jwk2);

        $this->assertEquals('{"keys":[{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","use":"sign","key_ops":["sign"],"alg":"ES256","kid":"0123456789"},{"kty":"EC","crv":"P-256","x":"f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU","y":"x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0","d":"jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI","use":"sign","key_ops":["verify"],"alg":"ES256","kid":"9876543210"}]}', json_encode($jwkset));
        $this->assertEquals(2, count($jwkset));
        $this->assertEquals(2, $jwkset->count());

        foreach ($jwkset as $key) {
            $this->assertEquals('EC', $key->get('kty'));
        }
        $this->assertEquals(2, $jwkset->key());

        $this->assertEquals('9876543210', $jwkset->getKey(1)->get('kid'));
        $jwkset = $jwkset->removeKey(1);
        $jwkset = $jwkset->removeKey(1);

        $this->assertEquals(1, count($jwkset));
        $this->assertEquals(1, $jwkset->count());

        $this->assertInstanceOf('\Jose\Object\JWKInterface', $jwkset->getKey(0));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Undefined index.
     */
    public function testKeySet2()
    {
        $jwk1 = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'use'     => 'sign',
            'key_ops' => ['sign'],
            'alg'     => 'ES256',
            'kid'     => '0123456789',
        ]);

        $jwk2 = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'd'       => 'jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI',
            'use'     => 'sign',
            'key_ops' => ['verify'],
            'alg'     => 'ES256',
            'kid'     => '9876543210',
        ]);

        $jwkset = new JWKSet();
        $jwkset = $jwkset->addKey($jwk1);
        $jwkset = $jwkset->addKey($jwk2);

        $jwkset->getKey(2);
    }

    public function testPrivateToPublic()
    {
        $private = new JWK([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'd'       => 'jpsQnnGQmL-YBIffH1136cspYG6-0iY7X1fCE9-E9LI',
            'use'     => 'sign',
            'key_ops' => ['verify'],
            'alg'     => 'ES256',
            'kid'     => '9876543210',
        ]);

        $public = $private->toPublic();

        $this->assertEquals(json_encode([
            'kty'     => 'EC',
            'crv'     => 'P-256',
            'x'       => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
            'y'       => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
            'use'     => 'sign',
            'key_ops' => ['verify'],
            'alg'     => 'ES256',
            'kid'     => '9876543210',
        ]), json_encode($public));
    }

    public function testLoadCertificateChain()
    {
        $key = \Jose\Factory\JWKFactory::createFromCertificateFile(
            __DIR__.'/../Certificates/Chain/google.crt',
            [
                'kid' => 'From www.google.com',
            ]
        );

        $this->assertEquals(
            '178f7e93a74ed73d88c29042220b9ae6e4b371cd',
            strtolower(bin2hex(\Base64Url\Base64Url::decode($key->get('x5t'))))
        );
        $this->assertEquals([
                'kty' => 'RSA',
                'n' => 'nCoEd1zYUJE6BqOC4NhQSLyJP_EZcBqIRn7gj8Xxic4h7lr-YQ23MkSJoHQLU09VpM6CYpXu61lfxuEFgBLEXpQ_vFtIOPRT9yTm-5HpFcTP9FMN9Er8n1Tefb6ga2-HwNBQHygwA0DaCHNRbH__OjynNwaOvUsRBOt9JN7m-fwxcfuU1WDzLkqvQtLL6sRqGrLMU90VS4sfyBlhH82dqD5jK4Q1aWWEyBnFRiL4U5W-44BKEMYq7LqXIBHHOZkQBKDwYXqVJYxOUnXitu0IyhT8ziJqs07PRgOXlwN-wLHee69FM8-6PnG33vQlJcINNYmdnfsOEXmJHjfFr45yaQ',
                'e' => 'AQAB',
                'x5t' => 'F49-k6dO1z2IwpBCIgua5uSzcc0',
                'x5t#256' => 'pBJP2vnKx7ruHKsy4yJddGUAwJ888-uyU-8_uwiK_TQ',
                'kid' => 'From www.google.com',
            ],
            $key->getAll()

        );
    }
}
