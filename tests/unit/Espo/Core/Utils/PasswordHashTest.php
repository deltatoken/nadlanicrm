<?php
/************************************************************************
 * This file is part of NadlaniCrm.
 *
 * NadlaniCrm - Open Source CRM application.
 * Copyright (C) 2014-2018 Pablo Rotem
 * Website: https://www.facebook.com/sites4u2
 *
 * NadlaniCrm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NadlaniCrm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NadlaniCrm. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "NadlaniCrm" word.
 ************************************************************************/

namespace tests\unit\Nadlani\Core\Utils;

use tests\unit\ReflectionHelper;

class PasswordHashTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    protected $objects;

    protected $reflection;

    protected $salt = 'bdaff81c7b8db54d';

    protected function setUp()
    {
        $this->objects['config'] = $this->getMockBuilder('\Nadlani\Core\Utils\Config')->disableOriginalConstructor()->getMock();

        $this->object = new \Nadlani\Core\Utils\PasswordHash($this->objects['config']);

        $this->reflection = new ReflectionHelper($this->object);
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }

    public function testGenerateSalt()
    {
        $salt = $this->object->generateSalt();

        $this->assertEquals(16, strlen($salt));
    }

    public function testNormalizeSalt()
    {
        $salt = $this->object->generateSalt();

        $result = '$6$' . $salt . '$';
        $this->assertEquals($result, $this->reflection->invokeMethod('normalizeSalt', array($salt)));
    }

    public function testGetSalt()
    {
        $this->objects['config']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->salt));

        $result = '$6$' . $this->salt . '$';
        $this->assertEquals($result, $this->reflection->invokeMethod('getSalt'));
    }

    public function testGetSaltException()
    {
        $this->expectException('\Nadlani\Core\Exceptions\Error');

        $this->reflection->invokeMethod('getSalt');
    }

    public function testHash()
    {
        $password = 'test-password';

        $this->objects['config']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->salt));

        $result = '4gDlJKdkj/MMo2axSwvvWUv0ktSUeGpis/wLcpL8aEBUxXTVa.rxFb1cfKzTiSE4ookBdNpLMheJmtZqzDSRA0';
        $this->assertEquals($result, $this->object->hash($password));
    }
}
