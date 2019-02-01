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

class PermissionTest extends \PHPUnit\Framework\TestCase
{
    protected $object;

    protected $objects;

    protected $reflection;

    protected $fileList;

    protected function setUp()
    {
        $this->objects['fileManager'] = $this->getMockBuilder('\Nadlani\Core\Utils\File\Manager')->disableOriginalConstructor()->getMock();

        $this->object = new \Nadlani\Core\Utils\File\Permission($this->objects['fileManager']);

        $this->reflection = new ReflectionHelper($this->object);

        $this->fileList = array(
            'application/Nadlani/Controllers/Email.php',
            'application/Nadlani/Controllers/EmailAccount.php',
            'application/Nadlani/Controllers/EmailAddress.php',
            'application/Nadlani/Controllers/ExternalAccount.php',
            'application/Nadlani/Controllers/Import.php',
            'application/Nadlani/Controllers/Integration.php',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL/Calendar.json',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL/Call.json',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL/Case.json',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL/Contact.json',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL/Global.json',
            'application/Nadlani/Resources/layouts/User/filters.json',
            'application/Nadlani/Resources/metadata/app/acl.json',
            'application/Nadlani/Resources/metadata/app/defaultDashboardLayout.json'
        );
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }

    public function testGetSearchCount()
    {
        $search = 'application/Nadlani/Controllers/';
        $methodResult = $this->reflection->invokeMethod('getSearchCount', array($search, $this->fileList));
        $result = 6;
        $this->assertEquals($result, $methodResult);


        $search = 'application/Nadlani/Controllers/Email.php';
        $methodResult = $this->reflection->invokeMethod('getSearchCount', array($search, $this->fileList));
        $result = 1;
        $this->assertEquals($result, $methodResult);

        $search = 'application/Nadlani/Controllers/NotReal';
        $methodResult = $this->reflection->invokeMethod('getSearchCount', array($search, $this->fileList));
        $result = 0;
        $this->assertEquals($result, $methodResult);
    }

    public function testArrangePermissionList()
    {
        $result = array(
            'application/Nadlani/Controllers',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL',
            'application/Nadlani/Resources/layouts/User/filters.json',
            'application/Nadlani/Resources/metadata/app',
        );
        $this->assertEquals( $result, $this->object->arrangePermissionList($this->fileList) );
    }

    /*public function bestPossibleList()
    {
        $fileList = array(
            'application/Nadlani/Controllers',
            'application/Nadlani/Core',
            'application/Nadlani/Core/Cron',
            'application/Nadlani/Core/Loaders',
            'application/Nadlani/Core/Mail',
            'application/Nadlani/Core/Mail/Storage/Imap.php',
            'application/Nadlani/Core/SelectManagers/Base.php',
            'application/Nadlani/Core/Utils/Database/Orm',
            'application/Nadlani/Core/Utils/Database/Orm/Fields',
            'application/Nadlani/Core/Utils/Database/Orm/Relations',
            'application/Nadlani/Core/Utils',
            'application/Nadlani/Core/defaults/config.php',
            'application/Nadlani/Entities',
            'application/Nadlani/Hooks/Common/Stream.php',
            'application/Nadlani/Modules/Crm/Controllers/Opportunity.php',
            'application/Nadlani/Modules/Crm/Jobs/CheckInboundEmails.php',
            'application/Nadlani/Modules/Crm/Resources/i18n/de_DE',
            'application/Nadlani/Modules/Crm/Resources/i18n/en_US',
            'application/Nadlani/Modules/Crm/Resources/i18n/nl_NL',
            'application/Nadlani/Modules/Crm/Resources/i18n/pl_PL',
            'application/Nadlani/Modules/Crm/Resources/layouts/InboundEmail',
            'application/Nadlani/Modules/Crm/Resources/metadata/clientDefs/InboundEmail.json',
            'application/Nadlani/Modules/Crm/Resources/metadata/entityDefs',
            'application/Nadlani/Modules/Crm/Services',
            'application/Nadlani/Repositories',
            'application/Nadlani/Resources/i18n/de_DE',
            'application/Nadlani/Resources/i18n/en_US',
            'application/Nadlani/Resources/i18n/nl_NL',
            'application/Nadlani/Resources/i18n/pl_PL',
            'application/Nadlani/Resources/layouts/Email',
            'application/Nadlani/Resources/layouts/EmailAccount',
            'application/Nadlani/Resources/layouts/User/filters.json',
            'application/Nadlani/Resources/metadata/app',
            'application/Nadlani/Resources/metadata/clientDefs',
            'application/Nadlani/Resources/metadata/entityDefs',
            'application/Nadlani/Resources/metadata/integrations/Google.json',
            'application/Nadlani/Resources/metadata/scopes',
            'application/Nadlani/SelectManagers/EmailAccount.php',
            'application/Nadlani/Services',
            'install/core',
            'install/core/actions/settingsTest.php',
            'install/core/i18n/de_DE/install.json',
            'install/core/i18n/en_US/install.json',
            'install/core/i18n/es_ES/install.json',
            'install/core/i18n/nl_NL/install.json',
            'install/core/i18n/pl_PL/install.json',
            'install/core/i18n/ro_RO/install.json',
            'install/core/i18n/tr_TR/install.json',
            'install/js/install.js',
        );

        $result = array(
            'application/Nadlani/Controllers',
            'application/Nadlani/Core',
            'application/Nadlani/Entities',
            'application/Nadlani/Hooks/Common/Stream.php',
            'application/Nadlani/Modules/Crm',
            'application/Nadlani/Repositories',
            'application/Nadlani/Resources',
            'application/Nadlani/SelectManagers/EmailAccount.php',
            'application/Nadlani/Services',
            'install/core',
            'install/js/install.js',
        );
    }*/







}

?>
