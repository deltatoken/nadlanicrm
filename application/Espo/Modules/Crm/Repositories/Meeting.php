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

namespace Nadlani\Modules\Crm\Repositories;

use Nadlani\ORM\Entity;
use Nadlani\Core\Utils\Util;

class Meeting extends \Nadlani\Core\Repositories\Event
{
    protected function beforeSave(Entity $entity, array $options = [])
    {
        if (!$entity->isNew() && $entity->isAttributeChanged('parentId')) {
            $entity->set('accountId', null);
        }

        $parentId = $entity->get('parentId');
        $parentType = $entity->get('parentType');

        if ($entity->isAttributeChanged('parentId') || $entity->isAttributeChanged('parentType')) {
            $parent = null;
            if ($parentId && $parentType) {
                if ($this->getEntityManager()->hasRepository($parentType)) {
                    $columnList = ['id', 'name'];
                    if ($this->getEntityManager()->getMetadata()->get($parentType, ['fields', 'accountId'])) {
                        $columnList[] = 'accountId';
                    }
                    if ($parentType === 'Lead') {
                        $columnList[] = 'status';
                        $columnList[] = 'createdAccountId';
                        $columnList[] = 'createdAccountName';
                    }
                    $parent = $this->getEntityManager()->getRepository($parentType)->select($columnList)->get($parentId);
                }
            }
            $accountId = null;
            $accountName = null;

            if ($parent) {
                if ($parent->getEntityType() == 'Account') {
                    $accountId = $parent->id;
                    $accountName = $parent->get('name');
                } else if ($parent->getEntityType() == 'Lead') {
                    if ($parent->get('status') == 'Converted') {
                        if ($parent->get('createdAccountId')) {
                            $accountId = $parent->get('createdAccountId');
                            $accountName = $parent->get('createdAccountName');
                        }
                    }
                }
                if (!$accountId && $parent->get('accountId') && $parent->getRelationParam('account', 'entity') == 'Account') {
                    $accountId = $parent->get('accountId');
                }
                if ($accountId) {
                    $entity->set('accountId', $accountId);
                    $entity->set('accountName', $accountName);
                }
            }

            if (
                $entity->get('accountId')
                &&
                !$entity->get('accountName')
            ) {
                $account = $this->getEntityManager()->getRepository('Account')->select(['id', 'name'])->get($entity->get('accountId'));
                if ($account) {
                    $entity->set('accountName', $account->get('name'));
                }
            }
        }

        parent::beforeSave($entity, $options);

        if ($entity->hasLinkMultipleField('assignedUsers')) {
            $assignedUserIdList = $entity->getLinkMultipleIdList('assignedUsers');
            foreach ($assignedUserIdList as $assignedUserId) {
                $entity->addLinkMultipleId('users', $assignedUserId);
                $entity->setLinkMultipleName('users', $assignedUserId, $entity->getLinkMultipleName('assignedUsers', $assignedUserId));
            }
        } else {
            $assignedUserId = $entity->get('assignedUserId');
            if ($assignedUserId) {
                $entity->addLinkMultipleId('users', $assignedUserId);
                $entity->setLinkMultipleName('users', $assignedUserId, $entity->get('assignedUserName'));
            }
        }

        if ($entity->isNew()) {
            $currentUserId = $this->getEntityManager()->getUser()->id;
            if ($entity->hasLinkMultipleId('users', $currentUserId)) {
                $entity->setLinkMultipleColumn('users', 'status', $currentUserId, 'Accepted');
            }
        }
    }
}
