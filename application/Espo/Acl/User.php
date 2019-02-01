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

namespace Nadlani\Acl;

use \Nadlani\ORM\Entity;
use \Nadlani\Entities\User as EntityUser;

class User extends \Nadlani\Core\Acl\Base
{
    public function checkIsOwner(\Nadlani\Entities\User $user, Entity $entity)
    {
        return $user->id === $entity->id;
    }

    public function checkEntityCreate(EntityUser $user, Entity $entity, $data)
    {
        if (!$user->isAdmin()) {
            return false;
        }
        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }
        return $this->checkEntity($user, $entity, $data, 'create');
    }

    public function checkEntityDelete(EntityUser $user, Entity $entity, $data)
    {
        if ($entity->id === 'system') {
            return false;
        }
        if (!$user->isAdmin()) {
            return false;
        }
        if ($entity->isSystem()) {
            return false;
        }
        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }
        return parent::checkEntityDelete($user, $entity, $data);
    }

    public function checkEntityEdit(EntityUser $user, Entity $entity, $data)
    {
        if ($entity->id === 'system') {
            return false;
        }
        if ($entity->isSystem()) {
            return false;
        }
        if (!$user->isAdmin()) {
            if ($user->id !== $entity->id) {
                return false;
            }
        }
        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }
        return $this->checkEntity($user, $entity, $data, 'edit');
    }
}
