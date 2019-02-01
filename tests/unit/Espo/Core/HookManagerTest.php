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

namespace tests\unit\Nadlani\Core;

use tests\unit\ReflectionHelper;

class HookManagerTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    protected $objects;

    protected $filesPath = 'tests/unit/testData/Hooks';

    protected function setUp()
    {
        $this->objects['container'] = $this->getMockBuilder('\\Nadlani\\Core\\Container')->disableOriginalConstructor()->getMock();

        $this->objects['metadata'] = $this->getMockBuilder('\\Nadlani\\Core\\Utils\\Metadata')->disableOriginalConstructor()->getMock();
        $this->objects['config'] = $this->getMockBuilder('\\Nadlani\\Core\\Utils\\Config')->disableOriginalConstructor()->getMock();
        $this->objects['fileManager'] = new \Nadlani\Core\Utils\File\Manager();

        $map = array(
          array('metadata', $this->objects['metadata']),
          array('config', $this->objects['config']),
          array('fileManager', $this->objects['fileManager']),
        );

        $this->objects['container']
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($map));

        $this->object = new \Nadlani\Core\HookManager($this->objects['container']);
        $this->reflection = new ReflectionHelper($this->object);
    }

    protected function tearDown()
    {
        $this->object = NULL;
        $this->reflection = NULL;
    }

    public function testIsHookExists()
    {
        $data = array (
            '\\Nadlani\\Hooks\\Note\\Stream' => 8,
            '\\Nadlani\\Hooks\\Note\\Mentions' => 9,
            '\\Nadlani\\Hooks\\Note\\Notifications' => 14,
        );

        $data = array (
          array (
            'className' => '\\Nadlani\\Hooks\\Note\\Stream',
            'order' => 8,
          ),
          array (
            'className' => '\\Nadlani\\Hooks\\Note\\Mentions',
            'order' => 9,
          ),
          array (
            'className' => '\\Nadlani\\Hooks\\Note\\Notifications',
            'order' => 14,
          ),
        );

        $this->assertTrue( $this->reflection->invokeMethod('hookExists', array('\\Nadlani\\Hooks\\Note\\Mentions', $data)) );
        $this->assertTrue( $this->reflection->invokeMethod('hookExists', array('\\Nadlani\\Modules\\Crm\\Hooks\\Note\\Mentions', $data)) );
        $this->assertTrue( $this->reflection->invokeMethod('hookExists', array('\\Nadlani\\Modules\\Test\\Hooks\\Note\\Mentions', $data)) );
        $this->assertTrue( $this->reflection->invokeMethod('hookExists', array('\\Nadlani\\Modules\\Test\\Hooks\\Common\\Stream', $data)) );
        $this->assertFalse( $this->reflection->invokeMethod('hookExists', array('\\Nadlani\\Hooks\\Note\\TestHook', $data)) );
    }

    public function testSortHooks()
    {
        $data = array (
            'Common' =>
            array (
              'afterSave' =>
              array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\AssignmentEmailNotification',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Notifications',
                    'order' => 10,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Stream',
                    'order' => 9,
                ),
              ),
              'beforeSave' =>
              array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Formula',
                    'order' => 5,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\NextNumber',
                    'order' => 10,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\CurrencyConverted',
                    'order' => 1,
                ),
              ),
            ),
            'Note' =>
            array (
              'beforeSave' =>
              array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
              ),
              'afterSave' =>
              array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Notifications',
                    'order' => 14,
                ),
              ),
            ),
        );

        $result = array (
          'Common' =>
          array (
            'afterSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\AssignmentEmailNotification',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Stream',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Notifications',
                    'order' => 10,
                ),
            ),
            'beforeSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\CurrencyConverted',
                    'order' => 1,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Formula',
                    'order' => 5,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\NextNumber',
                    'order' => 10,
                ),
            ),
          ),
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
            'afterSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Notifications',
                    'order' => 14,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->invokeMethod('sortHooks', array($data)) );
    }

    public function testCase1CustomHook()
    {
        $this->reflection->setProperty('paths', array(
            'corePath' => $this->filesPath . '/testCase1/application/Nadlani/Hooks',
            'modulePath' => $this->filesPath . '/testCase1/application/Nadlani/Modules/{*}/Hooks',
            'customPath' => $this->filesPath . '/testCase1/custom/Nadlani/Custom/Hooks',
        ));

        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Crm',
                'Test',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\tests\\unit\\testData\\Hooks\\testCase1\\custom\\Nadlani\\Custom\\Hooks\\Note\\Mentions',
                    'order' => 7,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase2ModuleHook()
    {
        $this->reflection->setProperty('paths', array(
            'corePath' => $this->filesPath . '/testCase2/application/Nadlani/Hooks',
            'modulePath' => $this->filesPath . '/testCase2/application/Nadlani/Modules/{*}/Hooks',
            'customPath' => $this->filesPath . '/testCase2/custom/Nadlani/Custom/Hooks',
        ));

        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Crm',
                'Test',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\tests\\unit\\testData\\Hooks\\testCase2\\application\\Nadlani\\Modules\\Crm\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase2ModuleHookReverseModuleOrder()
    {
        $this->reflection->setProperty('paths', array(
            'corePath' => $this->filesPath . '/testCase2/application/Nadlani/Hooks',
            'modulePath' => $this->filesPath . '/testCase2/application/Nadlani/Modules/{*}/Hooks',
            'customPath' => $this->filesPath . '/testCase2/custom/Nadlani/Custom/Hooks',
        ));

        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->once())
            ->method('getModuleList')
            ->will($this->returnValue(array(
                'Test',
                'Crm',
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\tests\\unit\\testData\\Hooks\\testCase2\\application\\Nadlani\\Modules\\Test\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function testCase3CoreHook()
    {
        $this->reflection->setProperty('paths', array(
            'corePath' => $this->filesPath . '/testCase3/application/Nadlani/Hooks',
            'modulePath' => $this->filesPath . '/testCase3/application/Nadlani/Modules/{*}/Hooks',
            'customPath' => $this->filesPath . '/testCase3/custom/Nadlani/Custom/Hooks',
        ));

        $this->objects['config']
            ->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValue(false));

        $this->objects['metadata']
            ->expects($this->at(0))
            ->method('getModuleList')
            ->will($this->returnValue(array(
            )));

        $this->reflection->invokeMethod('loadHooks');

        $result = array (
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\tests\\unit\\testData\\Hooks\\testCase3\\application\\Nadlani\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
          ),
        );

        $this->assertEquals($result, $this->reflection->getProperty('data'));
    }

    public function noTestGetHookList()
    {
        $this->reflection->setProperty('data', array (
          'Common' =>
          array (
            'afterSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\AssignmentEmailNotification',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Stream',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Notifications',
                    'order' => 10,
                ),
            ),
            'beforeSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\CurrencyConverted',
                    'order' => 1,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\Formula',
                    'order' => 5,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Common\\NextNumber',
                    'order' => 10,
                ),
            ),
          ),
          'Note' =>
          array (
            'beforeSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Mentions',
                    'order' => 9,
                ),
            ),
            'afterSave' =>
            array (
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Btest',
                    'order' => 9,
                ),
                array (
                    'className' => '\\Nadlani\\Hooks\\Note\\Notifications',
                    'order' => 14,
                ),
            ),
          ),
        ));

        $resultBeforeSave = array(
            '\\Nadlani\\Hooks\\Common\\CurrencyConverted',
            '\\Nadlani\\Hooks\\Common\\Formula',
            '\\Nadlani\\Hooks\\Note\\Mentions',
            '\\Nadlani\\Hooks\\Common\\NextNumber',
        );

        $resultAfterSave = array(
            '\\Nadlani\\Hooks\\Common\\AssignmentEmailNotification',
            '\\Nadlani\\Hooks\\Note\\Btest',
            '\\Nadlani\\Hooks\\Common\\Stream',
            '\\Nadlani\\Hooks\\Common\\Notifications',
            '\\Nadlani\\Hooks\\Note\\Notifications',
        );
    }
}