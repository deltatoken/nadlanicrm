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

namespace tests\unit\Nadlani\Core\Utils\Database;

use tests\unit\ReflectionHelper;
use Nadlani\Core\Utils\Util;

class HelperTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    protected $objects;

    protected $reflection;

    protected function setUp()
    {
        $this->objects['config'] = $this->getMockBuilder('\\Nadlani\\Core\\Utils\\Config')->disableOriginalConstructor()->getMock();

        $this->objects['config']->expects($this->any())
            ->method('get')
            ->with($this->equalTo('database'))
            ->will($this->returnValue([
                'driver' => 'pdo_mysql',
                'dbname' => 'test',
                'user' => 'test_database',
                'password' => 'test_user',
                'host' => 'localhost',
                'port' => '',
                'charset' => 'utf8mb4'
        ]));
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }

    protected function initDatabaseHelper($config = null)
    {
        $this->object = new \Nadlani\Core\Utils\Database\Helper($config);
        $this->reflection = new ReflectionHelper($this->object);

        return $this->object;
    }

    public function testGetDbalConnection()
    {
        $this->initDatabaseHelper(null);

        $this->assertNull($this->object->getDbalConnection());
    }

    public function testGetDbalConnectionWithConfig()
    {
        $this->initDatabaseHelper($this->objects['config']);

        $this->assertInstanceOf('\\Doctrine\\DBAL\\Connection', $this->object->getDbalConnection());
    }

    public function testGetMaxIndexLength()
    {
        $this->initDatabaseHelper(null);

        $this->assertEquals(1000, $this->object->getMaxIndexLength());
        $this->assertEquals(1000, $this->object->getMaxIndexLength('table_name'));
        $this->assertEquals(2000, $this->object->getMaxIndexLength('table_name', 2000));
        $this->assertEquals(1000, $this->object->getTableMaxIndexLength('table_name'));
        $this->assertEquals(2000, $this->object->getTableMaxIndexLength('table_name', 2000));
    }

    public function testGetDatabaseVersion()
    {
        $this->initDatabaseHelper(null);

        $this->assertNull($this->reflection->invokeMethod('getDatabaseVersion'));
    }

    public function testGetTableEngine()
    {
        $this->initDatabaseHelper(null);

        $this->assertNull($this->reflection->invokeMethod('getTableEngine'));
        $this->assertEquals('InnoDB', $this->reflection->invokeMethod('getTableEngine', array(null, 'InnoDB')));
    }

    public function testIsSupportsFulltext()
    {
        $this->initDatabaseHelper(null);

        $this->assertFalse($this->object->isSupportsFulltext());
        $this->assertFalse($this->object->isSupportsFulltext('table_name'));
        $this->assertTrue($this->object->isSupportsFulltext('table_name', true));
        $this->assertFalse($this->object->isTableSupportsFulltext('table_name'));
        $this->assertTrue($this->object->isTableSupportsFulltext('table_name', true));
    }

    public function testGetDatabaseType()
    {
        $this->initDatabaseHelper(null);

        $this->assertEquals('MySQL', $this->object->getDatabaseType());
        $this->assertEquals('MariaDB', $this->object->getDatabaseType('MariaDB'));
    }
}
