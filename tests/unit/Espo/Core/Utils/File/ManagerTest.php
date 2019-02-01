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

namespace tests\unit\Nadlani\Core\Utils\File;
use tests\unit\ReflectionHelper;
use Nadlani\Core\Utils\Util;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    protected $objects;

    protected $filesPath= 'tests/unit/testData/FileManager';
    protected $cachePath = 'tests/unit/testData/cache/FileManager';

    protected $reflection;

    protected function setUp()
    {
        $this->objects['config'] = $this->getMockBuilder('\Nadlani\Core\Utils\Config')->disableOriginalConstructor()->getMock();

        $this->object = new \Nadlani\Core\Utils\File\Manager();

        $this->reflection = new ReflectionHelper($this->object);
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }

    public function testGetFileName()
    {
        $this->assertEquals('Donwload', $this->object->getFileName('Donwload.php'));

        $this->assertEquals('Donwload', $this->object->getFileName('/Donwload.php'));

        $this->assertEquals('Donwload', $this->object->getFileName('\Donwload.php'));

        $this->assertEquals('Donwload', $this->object->getFileName('application/Nadlani/EntryPoints/Donwload.php'));
    }

    public function testGetContents()
    {
        $result = file_get_contents($this->filesPath.'/getContent/test.json');
        $this->assertEquals($result, $this->object->getContents( array($this->filesPath, 'getContent/test.json') ));
    }

    public function testPutContents()
    {
        $testPath= $this->filesPath.'/setContent';

        $result= 'next value';
        $this->assertTrue($this->object->putContents(array($testPath, 'test.json'), $result));

        $this->assertEquals($result, $this->object->getContents( array($testPath, 'test.json')) );

        $this->assertTrue($this->object->putContents(array($testPath, 'test.json'), 'initial value'));
    }

    public function testConcatPaths()
    {
        $input = Util::fixPath('application/Nadlani/Resources/metadata/app/panel.json');
        $result = Util::fixPath('application/Nadlani/Resources/metadata/app/panel.json');

        $this->assertEquals($result, $this->reflection->invokeMethod('concatPaths', array($input)) );


        $input = array(
            'application',
            'Nadlani/Resources/metadata/',
            'app',
            'panel.json',
        );
        $result = Util::fixPath('application/Nadlani/Resources/metadata/app/panel.json');

        $this->assertEquals($result, $this->reflection->invokeMethod('concatPaths', array($input)) );


        $input = array(
            'application/Nadlani/Resources/metadata/app',
            'panel.json',
        );
        $result = Util::fixPath('application/Nadlani/Resources/metadata/app/panel.json');

        $this->assertEquals($result, $this->reflection->invokeMethod('concatPaths', array($input)) );


        $input = array(
            'application/Nadlani/Resources/metadata/app/',
            'panel.json',
        );
        $result = Util::fixPath('application/Nadlani/Resources/metadata/app/panel.json');

        $this->assertEquals($result, $this->reflection->invokeMethod('concatPaths', array($input)) );
    }

    public function testGetDirName()
    {
        $input = 'data/logs/espo.log';
        $result = 'logs';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        $input = 'data/logs/espo.log/';
        $result = 'logs';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        $input = 'application/Nadlani/Resources/metadata/entityDefs';
        $result = 'entityDefs';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        $input = 'application/Nadlani/Resources/metadata/entityDefs/';
        $result = 'entityDefs';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        //path doesn't exists. Be careful to use "/" at the beginning
        $input = '/application/Nadlani/Resources/metadata/entityDefs';
        $result = 'metadata';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        $input = 'notRealPath/logs/espo.log';
        $result = 'logs';
        $this->assertEquals($result, $this->object->getDirName($input, false));

        $input = 'tests/unit/testData/FileManager/getContent';
        $result = 'getContent';
        $this->assertEquals($result, $this->object->getDirName($input, false));
    }

    public function testGetDirNameFullPath()
    {
        $input = 'data/logs/espo.log';
        $result = 'data/logs';
        $this->assertEquals($result, $this->object->getDirName($input));

        $input = 'data/logs/espo.log/';
        $result = 'data/logs';
        $this->assertEquals($result, $this->object->getDirName($input));

        $input = 'application/Nadlani/Resources/metadata/entityDefs';
        $result = 'application/Nadlani/Resources/metadata/entityDefs';
        $this->assertEquals($result, $this->object->getDirName($input));

        $input = 'application/Nadlani/Resources/metadata/entityDefs/';
        $result = 'application/Nadlani/Resources/metadata/entityDefs';
        $this->assertEquals($result, $this->object->getDirName($input));

        //path doesn't exists. Be careful to use "/" at the beginning
        $input = '/application/Nadlani/Resources/metadata/entityDefs';
        $result = '/application/Nadlani/Resources/metadata';
        $this->assertEquals($result, $this->object->getDirName($input));

        $input = 'notRealPath/logs/espo.log';
        $result = 'notRealPath/logs';
        $this->assertEquals($result, $this->object->getDirName($input));

        $input = 'tests/unit/testData/FileManager/getContent';
        $result = 'tests/unit/testData/FileManager/getContent';
        $this->assertEquals($result, $this->object->getDirName($input, true));
    }

    public function testUnsetContents()
    {
        $testPath = $this->filesPath.'/unsets/test.json';

        $initData = '{"fields":{"someName":{"type":"varchar","maxLength":40},"someName2":{"type":"varchar","maxLength":36}}}';
        $this->object->putContents($testPath, $initData);

        $unsets = 'fields.someName2';
        $this->assertTrue($this->object->unsetContents($testPath, $unsets));

        $result = '{"fields":{"someName":{"type":"varchar","maxLength":40}}}';
        $this->assertJsonStringEqualsJsonFile($testPath, $result);
    }

    public function testIsDirEmpty()
    {
        $this->assertFalse($this->object->isDirEmpty('application'));
        $this->assertFalse($this->object->isDirEmpty('tests/unit/Nadlani'));
        $this->assertFalse($this->object->isDirEmpty('tests/unit/Nadlani/Core/Utils/File'));

        $dirPath = 'tests/unit/testData/cache/EmptyDir';
        if (file_exists($dirPath) || mkdir($dirPath, 0755)) {
            $this->assertTrue($this->object->isDirEmpty($dirPath));
        }
    }

    public function testGetParentDirName()
    {
        $input = 'application/Nadlani/Resources/metadata/entityDefs';
        $result = 'metadata';
        $this->assertEquals($result, $this->object->getParentDirName($input, false));

        $input = 'application/Nadlani/Resources/metadata/entityDefs/';
        $result = 'metadata';
        $this->assertEquals($result, $this->object->getParentDirName($input, false));

        //path doesn't exists. Be careful to use "/" at the beginning
        $input = '/application/Nadlani/Resources/metadata/entityDefs';
        $result = 'metadata';
        $this->assertEquals($result, $this->object->getParentDirName($input, false));

        //path doesn't exists. Be careful to use "/" at the beginning
        $input = '/application/Nadlani/Resources/metadata/entityDefs';
        $result = '/application/Nadlani/Resources/metadata';
        $this->assertEquals($result, $this->object->getParentDirName($input));

        $input = 'notRealPath/logs/espo.log';
        $result = 'notRealPath/logs';
        $this->assertEquals($result, $this->object->getParentDirName($input));

        $input = 'tests/unit/testData/FileManager/getContent';
        $result = 'tests/unit/testData/FileManager';
        $this->assertEquals($result, $this->object->getParentDirName($input, true));
    }

    public function testGetSingeFileListAll()
    {
        $input = array (
          'custom' =>
          array (
            'Nadlani' =>
            array (
              'Custom' =>
              array (
                'Modules' =>
                array (
                  'ExtensionTest' =>
                  array (
                    0 => 'File.json',
                    1 => 'File.php',
                  ),
                ),
              ),
            ),
          ),
        );

        $result = array (
            'custom',
            'custom/Nadlani',
            'custom/Nadlani/Custom',
            'custom/Nadlani/Custom/Modules',
            'custom/Nadlani/Custom/Modules/ExtensionTest',
            'custom/Nadlani/Custom/Modules/ExtensionTest/File.json',
            'custom/Nadlani/Custom/Modules/ExtensionTest/File.php',
        );
        $result = array_map('\Nadlani\Core\Utils\Util::fixPath', $result);

        $this->assertEquals($result, $this->reflection->invokeMethod('getSingeFileList', array($input)));
    }

    public function testGetSingeFileListOnlyFiles()
    {
        $input = array (
          'custom' =>
          array (
            'Nadlani' =>
            array (
              'Custom' =>
              array (
                'Modules' =>
                array (
                  'ExtensionTest' =>
                  array (
                    0 => 'File.json',
                    1 => 'File.php',
                  ),
                ),
              ),
            ),
          ),
        );

        $result = array (
            Util::fixPath('custom/Nadlani/Custom/Modules/ExtensionTest/File.json'),
            Util::fixPath('custom/Nadlani/Custom/Modules/ExtensionTest/File.php'),
        );

        $this->assertEquals($result, $this->reflection->invokeMethod('getSingeFileList', array($input, true)));
    }

    public function testGetSingeFileListOnlyDirs()
    {
        $input = array (
          'custom' =>
          array (
            'Nadlani' =>
            array (
              'Custom' =>
              array (
                'Modules' =>
                array (
                  'ExtensionTest' =>
                  array (
                    0 => 'File.json',
                    1 => 'File.php',
                  ),
                ),
              ),
            ),
          ),
        );

        $result = array (
            'custom',
            'custom/Nadlani',
            'custom/Nadlani/Custom',
            'custom/Nadlani/Custom/Modules',
            'custom/Nadlani/Custom/Modules/ExtensionTest',
        );
        $result = array_map('\Nadlani\Core\Utils\Util::fixPath', $result);

        $this->assertEquals($result, $this->reflection->invokeMethod('getSingeFileList', array($input, false)));
    }

    public function fileListSets()
    {
        return array(
          array( 'Set1', array(
                'custom',
                'custom/Nadlani',
                'custom/Nadlani/Custom',
                'custom/Nadlani/Custom/Modules',
                'custom/Nadlani/Custom/Modules/TestModule',
                'custom/Nadlani/Custom/Modules/TestModule/SubFolder',
                'custom/Nadlani/Custom/Modules/TestModule/SubFolder/Tester.txt',
            )
          ),

          array( 'Set2', array(
                'custom',
                'custom/Nadlani',
                'custom/Nadlani/Custom',
                'custom/Nadlani/Custom/Resources',
                'custom/Nadlani/Custom/Resources/metadata',
                'custom/Nadlani/Custom/Resources/metadata/entityDefs',
                'custom/Nadlani/Custom/Resources/metadata/entityDefs/Account.json',
            )
          ),

          array( 'Set3', array(
                'custom',
                'custom/test.file',
            )
          ),
        );
    }

    /**
     * @dataProvider fileListSets
     */
    public function testRemoveWithEmptyDirs($name, $result)
    {
        $path = 'tests/unit/testData/FileManager/Remove/' . $name;
        $cachePath = $this->cachePath . '/' . $name;

        $fileList = array (
            $cachePath . '/custom/Nadlani/Custom/Modules/ExtensionTest/File.json',
            $cachePath . '/custom/Nadlani/Custom/Modules/ExtensionTest/File.php',
        );
        $result = array_map('\Nadlani\Core\Utils\Util::fixPath', $result);

        $res = $this->object->copy($path, $cachePath, true);
        if ($res) {
            $this->assertTrue($this->object->remove($fileList, null, true));
            $this->assertEquals($result, $this->object->getFileList($cachePath, true, '', null, true));
        }
    }

    public function existsPathSet()
    {
        return array(
          array( 'application/Nadlani/Core/Application.php', 'application/Nadlani/Core/Application.php', ),
          array( 'application/Nadlani/Core/NotRealApplication.php', 'application/Nadlani/Core'),
          array( array('application', 'Nadlani/Core', 'NotRealApplication.php'), 'application/Nadlani/Core'),
          array( 'application/NoNadlani/Core/Application.php', 'application'),
          array( 'notRealPath/Nadlani/Core/Application.php', '.'),
        );
    }

    /**
     * @dataProvider existsPathSet
     */
    public function testGetExistsPath($input, $result)
    {
        $this->assertEquals($result, $this->reflection->invokeMethod('getExistsPath', array($input)) );
    }

    public function testCopyTestCase1()
    {
        $path = 'tests/unit/testData/FileManager/copy/testCase1';
        $cachePath = $this->cachePath . '/copy/testCase1';

        $expectedResult = [
            'custom/Nadlani/Custom/Modules/ExtensionTest/File.json',
            'custom/Nadlani/Custom/Modules/ExtensionTest/File.php',
            'custom/Nadlani/Custom/Modules/TestModule/SubFolder/Tester.txt',
        ];

        $result = $this->object->copy($path, $cachePath, true);

        if ($result) {
            $this->assertEquals($expectedResult, $this->object->getFileList($cachePath, true, '', true, true));
            $this->object->removeInDir($cachePath);
        }
    }

    public function testCopyTestCase2()
    {
        $path = 'tests/unit/testData/FileManager/copy/testCase2';
        $cachePath = $this->cachePath . '/copy/testCase2';

        $expectedResult = [
            'custom/Nadlani/Custom/test1.php',
            'data/test2.php',
            'data/upload/5a86d9bf1154968dc',
            'test0.php'
        ];

        $result = $this->object->copy($path, $cachePath, true);

        if ($result) {
            $this->assertEquals($expectedResult, $this->object->getFileList($cachePath, true, '', true, true));
            $this->object->removeInDir($cachePath);
        }
    }

    public function testCopyTestCase3()
    {
        $path = 'tests/unit/testData/FileManager/copy/testCase3';
        $cachePath = $this->cachePath . '/copy/testCase3';

        $expectedResult = [
            'custom/Nadlani/Custom/test1.php',
            'data/test2.php',
            'data/upload/5a86d9bf1154968dc',
            'test0.php'
        ];

        $fileList = $this->object->getFileList($path, true, '', true, true);

        $this->assertEquals($expectedResult, $fileList, "Expected Result and File List");

        $result = $this->object->copy($path, $cachePath, true, $fileList);

        if ($result) {
            $this->assertEquals($expectedResult, $this->object->getFileList($cachePath, true, '', true, true), "Expected Result and List of copied files");
            $this->object->removeInDir($cachePath);
        }
    }

    public function testCopyTestCase4()
    {
        $path = 'tests/unit/testData/FileManager/copy/testCase4';
        $cachePath = $this->cachePath . '/copy/testCase4';

        $expectedResult = [
            'custom',
            'custom/Nadlani',
            'custom/Nadlani/Custom',
            'custom/Nadlani/Custom/test1.php',
            'data',
            'data/test2.php',
            'data/upload',
            'data/upload/5a86d9bf1154968dc',
            'test0.php'
        ];

        $fileList = $this->object->getFileList($path, true, '', null, true);

        $this->assertEquals($expectedResult, $fileList, "Expected Result and File List");

        $result = $this->object->copy($path, $cachePath, true, $fileList);

        if ($result) {
            $this->assertEquals($expectedResult, $this->object->getFileList($cachePath, true, '', null, true), "Expected Result and List of copied files");
            $this->object->removeInDir($cachePath);
        }
    }

}