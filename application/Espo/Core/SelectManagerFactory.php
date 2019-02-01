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

namespace Nadlani\Core;

use \Nadlani\Core\Exceptions\Error;
use \Nadlani\Core\Utils\Util;

use \Nadlani\Core\InjectableFactory;

class SelectManagerFactory
{
    private $entityManager;

    private $user;

    private $acl;

    private $metadata;

    private $injectableFactory;

    private $FieldManagerUtil;

    public function __construct(
        $entityManager,
        \Nadlani\Entities\User $user,
        Acl $acl,
        AclManager $aclManager,
        Utils\Metadata $metadata,
        Utils\Config $config,
        Utils\FieldManagerUtil $fieldManagerUtil,
        InjectableFactory $injectableFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->acl = $acl;
        $this->aclManager = $aclManager;
        $this->metadata = $metadata;
        $this->config = $config;
        $this->fieldManagerUtil = $fieldManagerUtil;
        $this->injectableFactory = $injectableFactory;
    }

    public function create(string $entityType, ?\Nadlani\Entities\User $user = null) : \Nadlani\Core\SelectManagers\Base
    {
        $normalizedName = Util::normilizeClassName($entityType);

        $className = '\\Nadlani\\Custom\\SelectManagers\\' . $normalizedName;
        if (!class_exists($className)) {
            $moduleName = $this->metadata->getScopeModuleName($entityType);
            if ($moduleName) {
                $className = '\\Nadlani\\Modules\\' . $moduleName . '\\SelectManagers\\' . $normalizedName;
            } else {
                $className = '\\Nadlani\\SelectManagers\\' . $normalizedName;
            }
            if (!class_exists($className)) {
                $className = '\\Nadlani\\Core\\SelectManagers\\Base';
            }
        }

        if ($user) {
            $acl = $this->aclManager->createUserAcl($user);
        } else {
            $acl = $this->acl;
            $user = $this->user;
        }

        $selectManager = new $className(
            $this->entityManager,
            $user,
            $acl,
            $this->aclManager,
            $this->metadata,
            $this->config,
            $this->fieldManagerUtil,
            $this->injectableFactory
        );
        $selectManager->setEntityType($entityType);

        return $selectManager;
    }
}
