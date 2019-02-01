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

namespace Nadlani\Services;

use \Nadlani\Core\Exceptions\Forbidden;
use \Nadlani\Core\Exceptions\Error;
use \Nadlani\Core\Exceptions\NotFound;

class ActionHistoryRecord extends Record
{
    protected $actionHistoryDisabled = true;

    protected $listCountQueryDisabled = true;

    protected $forceSelectAllAttributes = true;

    public function loadParentNameFields(\Nadlani\ORM\Entity $entity)
    {
        if ($entity->get('targetId') && $entity->get('targetType')) {
            $repository = $this->getEntityManager()->getRepository($entity->get('targetType'));
            if ($repository) {
                $target = $repository->where(array(
                    'id' => $entity->get('targetId')
                ))->findOne(array(
                    'withDeleted' => true
                ));
                if ($target && $target->get('name')) {
                    $entity->set('targetName', $target->get('name'));
                }
            }
        }
    }
}

