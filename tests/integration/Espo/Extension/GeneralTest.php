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

namespace tests\integration\Nadlani\Extensions;

class GeneralTest extends \tests\integration\Core\BaseTestCase
{
    protected $dataFile = 'InitData.php';

    protected $userName = 'admin';
    protected $password = '1';

    protected $packagePath = 'Extension/General.zip';

    public function testUpload()
    {
        $fileData = file_get_contents($this->normalizePath($this->packagePath));
        $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

        $extensionManager = new \Nadlani\Core\ExtensionManager($this->getContainer());
        $extensionId = $extensionManager->upload($fileData);

        $this->assertStringMatchesFormat('%x', $extensionId);
        $this->assertFileExists('data/upload/extensions/' . $extensionId . 'z');
        $this->assertFileExists('data/upload/extensions/' . $extensionId); //directory
        //$this->assertDirectoryExists('data/upload/extensions/' . $extensionId);

        return $extensionId;
    }

    public function testInstall()
    {
        $extensionId = $this->testUpload();

        $extensionManager = new \Nadlani\Core\ExtensionManager($this->getContainer());
        $extensionManager->install(array('id' => $extensionId));

        $this->assertFileExists('data/upload/extensions/' . $extensionId . 'z');
        $this->assertFileNotExists('data/upload/extensions/' . $extensionId); //directory
        $this->assertFileExists('data/.backup/extensions/' . $extensionId); //directory

        $this->assertFileExists('application/Nadlani/Modules/Test'); //directory
        $this->assertFileExists('application/Nadlani/Modules/Test/Resources/metadata/scopes/TestEntity.json');
        $this->assertFileExists('client/modules/test'); //directory
        $this->assertFileExists('client/modules/test/src/views/test-entity/fields/custom-type.js');

        $this->assertFileNotExists('vendor/zendframework'); //directory
        $this->assertFileNotExists('extension.php');
        $this->assertFileNotExists('upgrade.php');

        return $extensionId;
    }

    public function testUninstall()
    {
        $extensionId = $this->testInstall();

        $extensionManager = new \Nadlani\Core\ExtensionManager($this->getContainer());
        $extensionManager->uninstall(array('id' => $extensionId));

        $this->assertFileNotExists('data/.backup/extensions/' . $extensionId); //directory
        $this->assertFileNotExists('data/upload/extensions/' . $extensionId); //directory
        $this->assertFileExists('data/upload/extensions/' . $extensionId . 'z');

        $this->assertFileNotExists('application/Nadlani/Modules/Test'); //directory
        $this->assertFileNotExists('application/Nadlani/Modules/Test/Resources/metadata/scopes/TestEntity.json');
        $this->assertFileNotExists('client/modules/test'); //directory
        $this->assertFileNotExists('client/modules/test/src/views/test-entity/fields/custom-type.js');

        $this->assertFileExists('vendor/zendframework'); //directory
        $this->assertFileExists('extension.php');
        $this->assertFileExists('upgrade.php');

        return $extensionId;
    }

    public function testDelete()
    {
        $extensionId = $this->testUninstall();

        $extensionManager = new \Nadlani\Core\ExtensionManager($this->getContainer());
        $extensionManager->delete(array('id' => $extensionId));

        $this->assertFileNotExists('data/.backup/extensions/' . $extensionId); //directory
        $this->assertFileNotExists('data/upload/extensions/' . $extensionId); //directory
        $this->assertFileNotExists('data/upload/extensions/' . $extensionId . 'z');

        $this->assertFileNotExists('application/Nadlani/Modules/Test'); //directory
        $this->assertFileNotExists('application/Nadlani/Modules/Test/Resources/metadata/scopes/TestEntity.json');
        $this->assertFileNotExists('client/modules/test'); //directory
        $this->assertFileNotExists('client/modules/test/src/views/test-entity/fields/custom-type.js');

        $this->assertFileExists('vendor/zendframework'); //directory
        $this->assertFileExists('extension.php');
        $this->assertFileExists('upgrade.php');
    }
}
