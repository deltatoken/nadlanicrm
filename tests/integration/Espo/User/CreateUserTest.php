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

namespace tests\integration\Nadlani\User;

class CreateUserTest extends \tests\integration\Core\BaseTestCase
{
    protected $dataFile = 'User/Login.php';

    protected $userName = 'admin';
    protected $password = '1';

    public function testCreateUser()
    {
        $newUser = $this->createUser('tester');

        $this->assertInstanceOf('\\Nadlani\\Orm\\Entity', $newUser);
        $this->assertTrue(!empty($newUser->id));
        $this->assertEquals('tester', $newUser->get('userName'));
    }

    public function testCreateUserWithAttributes()
    {
        $newUser = $this->createUser(array(
            'userName' => 'tester',
            'firstName' => 'Test',
            'lastName' => 'Tester',
            'emailAddress' => 'test@tester.com',
        ));

        $this->assertInstanceOf('\\Nadlani\\Orm\\Entity', $newUser);
        $this->assertTrue(!empty($newUser->id));
        $this->assertEquals('tester', $newUser->get('userName'));
        $this->assertEquals('Test', $newUser->get('firstName'));
        $this->assertEquals('Tester', $newUser->get('lastName'));
        $this->assertEquals('test@tester.com', $newUser->get('emailAddress'));
    }

    public function testCreateUserWithRole()
    {
        $newUser = $this->createUser('tester', array(
            'assignmentPermission' => 'team',
            'userPermission' => 'team',
            'portalPermission' => 'not-set',
            'data' =>
            array (
                'Account' => false,
                'Call' =>
                array (
                    'create' => 'yes',
                    'read' => 'team',
                    'edit' => 'team',
                    'delete' => 'no',
                ),
            ),
            'fieldData' =>
            array (
                'Call' =>
                array (
                    'direction' =>
                    array (
                        'read' => 'yes',
                        'edit' => 'no',
                    ),
                ),
            ),
        ));

        $this->assertInstanceOf('\\Nadlani\\Orm\\Entity', $newUser);
        $this->assertTrue(!empty($newUser->id));
        $this->assertEquals('tester', $newUser->get('userName'));
    }

    public function testCreatePortalUserWithRole()
    {
        $newUser = $this->createUser(array(
                'userName' => 'tester',
                'lastName' => 'tester',
                'portalsIds' => array(
                    'testPortalId',
                ),
            ), array(
            'assignmentPermission' => 'team',
            'userPermission' => 'team',
            'portalPermission' => 'not-set',
            'data' => array (
                'Account' => false,
            ),
            'fieldData' => array (
                'Call' => array (
                    'direction' => array (
                        'read' => 'yes',
                        'edit' => 'no',
                    ),
                ),
            ),
        ), true);

        $this->assertInstanceOf('\\Nadlani\\Orm\\Entity', $newUser);
        $this->assertTrue(!empty($newUser->id));
        $this->assertEquals('tester', $newUser->get('userName'));
    }
}
