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

namespace Nadlani\Core\ORM;

use \Nadlani\Core\Utils\Util;

class EntityManager extends \Nadlani\ORM\EntityManager
{
    protected $espoMetadata;

    private $hookManager;

    protected $user;

    protected $container;

    private $repositoryClassNameHash = array();

    private $entityClassNameHash = array();

    public function setContainer(\Nadlani\Core\Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getNadlaniMetadata()
    {
        return $this->espoMetadata;
    }

    public function setNadlaniMetadata($espoMetadata)
    {
        $this->espoMetadata = $espoMetadata;
    }

    public function setHookManager(\Nadlani\Core\HookManager $hookManager)
    {
        $this->hookManager = $hookManager;
    }

    public function getHookManager()
    {
        return $this->hookManager;
    }

    public function normalizeRepositoryName($name)
    {
        if (empty($this->repositoryClassNameHash[$name])) {
            $className = '\\Nadlani\\Custom\\Repositories\\' . Util::normilizeClassName($name);
            if (!class_exists($className)) {
                $className = $this->espoMetadata->getRepositoryPath($name);
            }
            $this->repositoryClassNameHash[$name] = $className;
        }
        return $this->repositoryClassNameHash[$name];
    }

    public function normalizeEntityName($name)
    {
        if (empty($this->entityClassNameHash[$name])) {
            $className = '\\Nadlani\\Custom\\Entities\\' . Util::normilizeClassName($name);
            if (!class_exists($className)) {
                $className = $this->espoMetadata->getEntityPath($name);
            }
            $this->entityClassNameHash[$name] = $className;
        }
        return $this->entityClassNameHash[$name];
    }
}

