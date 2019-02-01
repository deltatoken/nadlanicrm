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

namespace Nadlani\Core\Upgrades\Actions\Base;
use Nadlani\Core\Exceptions\Error;
use Nadlani\Core\Utils\Util;

class Install extends \Nadlani\Core\Upgrades\Actions\Base
{
    /**
     * Main installation process
     *
     * @param  string $processId Upgrade/Extension ID, gotten in upload stage
     * @return bool
     */
    public function run($data)
    {
        $processId = $data['id'];

        $GLOBALS['log']->debug('Installation process ['.$processId.']: start run.');

        if (empty($processId)) {
            throw new Error('Installation package ID was not specified.');
        }

        $this->setProcessId($processId);

        $this->initialize();

        /** check if an archive is unzipped, if no then unzip */
        $packagePath = $this->getPackagePath();
        if (!file_exists($packagePath)) {
            $this->unzipArchive();
            $this->isAcceptable();
        }

        //check permissions copied and deleted files
        $this->checkIsWritable();

        $this->beforeRunAction();

        $this->backupExistingFiles();

        //beforeInstallFiles
        if (!$this->copyFiles('before')) {
            $this->throwErrorAndRemovePackage('Cannot copy beforeInstall files.');
        }

        /* run before install script */
        if (!isset($data['skipBeforeScript']) || !$data['skipBeforeScript']) {
            $this->runScript('before');
        }

        /* remove files defined in a manifest "deleteBeforeCopy" */
        $this->deleteFiles('deleteBeforeCopy', true);

        /* copy files from directory "Files" to NadlaniCrm files */
        if (!$this->copyFiles()) {
            $this->throwErrorAndRemovePackage('Cannot copy files.');
        }

        /* remove files defined in a manifest */
        $this->deleteFiles('delete', true);

        $this->deleteFiles('vendor');
        $this->copyFiles('vendor');

        if (!isset($data['skipSystemRebuild']) || !$data['skipSystemRebuild']) {
            if (!$this->systemRebuild()) {
                $this->throwErrorAndRemovePackage('Error occurred while NadlaniCrm rebuild.');
            }
        }

        //afterInstallFiles
        if (!$this->copyFiles('after')) {
            $this->throwErrorAndRemovePackage('Cannot copy afterInstall files.');
        }

        /* run before install script */
        if (!isset($data['skipAfterScript']) || !$data['skipAfterScript']) {
            $this->runScript('after');
        }

        $this->afterRunAction();

        $this->finalize();

        /* delete unziped files */
        $this->deletePackageFiles();

        if ($this->getManifestParam('skipBackup')) {
            $this->getFileManager()->removeInDir($this->getPath('backupPath'), true);
        }

        $GLOBALS['log']->debug('Installation process ['.$processId.']: end run.');

        $this->clearCache();
    }

    protected function restoreFiles()
    {
        $GLOBALS['log']->info('Installer: Restore previous files.');

        $backupPath = $this->getPath('backupPath');
        $backupFilePath = Util::concatPath($backupPath, self::FILES);

        $backupFileList = $this->getRestoreFileList();
        $copyFileList = $this->getCopyFileList();
        $deleteFileList = array_diff($copyFileList, $backupFileList);

        $res = $this->copy($backupFilePath, '', true);
        if (!empty($deleteFileList)) {
            $res &= $this->getFileManager()->remove($deleteFileList, null, true);
        }

        if ($res) {
            $this->getFileManager()->removeInDir($backupPath, true);
        }

        return $res;
    }

    protected function throwErrorAndRemovePackage($errorMessage = '')
    {
        $this->restoreFiles();
        parent::throwErrorAndRemovePackage($errorMessage);
    }
}
