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


class ImportTest extends \PHPUnit\Framework\TestCase
{
    protected $objects;

    protected $importService;


    protected function setUp()
    {
        $this->objects['serviceFactory'] = $this->getMockBuilder('\Nadlani\Core\ServiceFactory')->disableOriginalConstructor()->getMock();

        $this->objects['config'] = $this->getMockBuilder('\Nadlani\Core\Utils\Config')->disableOriginalConstructor()->getMock();

        $this->objects['fileManager'] = $this->getMockBuilder('\Nadlani\Core\Utils\File\Manager')->disableOriginalConstructor()->getMock();

        $this->objects['metadata'] = $this->getMockBuilder('\Nadlani\Core\Utils\Metadata')->disableOriginalConstructor()->getMock();

        $this->objects['acl'] = $this->getMockBuilder('\Nadlani\Core\Acl')->disableOriginalConstructor()->getMock();


        $this->importService = new \Nadlani\Services\Import();
        $this->importService->inject('serviceFactory', $this->objects['serviceFactory']);
        $this->importService->inject('config', $this->objects['config']);
        $this->importService->inject('fileManager', $this->objects['fileManager']);
        $this->importService->inject('metadata', $this->objects['metadata']);
        $this->importService->inject('acl', $this->objects['acl']);

    }

    protected function tearDown()
    {
        $this->importService = NULL;
    }


    function testImportRow()
    {
        $this->assertTrue(true);
    }
}

