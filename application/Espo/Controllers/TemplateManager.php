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

namespace Nadlani\Controllers;

use Nadlani\Core\Utils as Utils;
use \Nadlani\Core\Exceptions\NotFound;
use \Nadlani\Core\Exceptions\Error;
use \Nadlani\Core\Exceptions\Forbidden;
use \Nadlani\Core\Exceptions\BadRequest;

class TemplateManager extends \Nadlani\Core\Controllers\Base
{
    protected function checkControllerAccess()
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function getActionGetTemplate($params, $data, $request)
    {
        $name = $request->get('name');
        if (empty($name)) throw new BadRequest();
        $scope = $request->get('scope');
        $module = null;
        $module = $this->getMetadata()->get(['app', 'templates', $name, 'module']);
        $hasSubject = !$this->getMetadata()->get(['app', 'templates', $name, 'noSubject']);

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        $returnData = (object) [];
        $returnData->body = $templateFileManager->getTemplate($name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($name, 'subject', $scope, $module);
        }

        return $returnData;
    }

    public function postActionSaveTemplate($params, $data)
    {
        $scope = null;
        if (empty($data->name)) {
            throw new BadRequest();
        }
        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        if (isset($data->subject)) {
            $templateFileManager->saveTemplate($data->name, 'subject', $data->subject, $scope);
        }

        if (isset($data->body)) {
            $templateFileManager->saveTemplate($data->name, 'body', $data->body, $scope);
        }

        return true;
    }

    public function postActionResetTemplate($params, $data)
    {
        $scope = null;
        if (empty($data->name)) {
            throw new BadRequest();
        }
        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $module = null;
        $module = $this->getMetadata()->get(['app', 'templates', $data->name, 'module']);
        $hasSubject = !$this->getMetadata()->get(['app', 'templates', $data->name, 'noSubject']);

        $templateFileManager = $this->getContainer()->get('templateFileManager');

        if ($hasSubject) {
            $templateFileManager->resetTemplate($data->name, 'subject', $scope);
        }

        $templateFileManager->resetTemplate($data->name, 'body', $scope);

        $returnData = (object) [];
        $returnData->body = $templateFileManager->getTemplate($data->name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($data->name, 'subject', $scope, $module);
        }

        return $returnData;
    }
}
