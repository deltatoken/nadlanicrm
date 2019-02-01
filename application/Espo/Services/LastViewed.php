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

use \Nadlani\ORM\Entity;

class LastViewed extends \Nadlani\Core\Services\Base
{
    protected function init()
    {
        parent::init();
        $this->addDependency('serviceFactory');
        $this->addDependency('metadata');
    }

    public function getList($params)
    {
        $repository = $this->getEntityManager()->getRepository('ActionHistoryRecord');

        $actionHistoryRecordService = $this->getInjection('serviceFactory')->create('ActionHistoryRecord');

        $scopes = $this->getInjection('metadata')->get('scopes');

        $targetTypeList = array_filter(array_keys($scopes), function ($item) use ($scopes) {
            return !empty($scopes[$item]['object']);
        });

        $offset = $params['offset'];
        $maxSize = $params['maxSize'];

        $selectParams = [
            'whereClause' => [
                'userId' => $this->getUser()->id,
                'action' => 'read',
                'targetType' => $targetTypeList
            ],
            'orderBy' => [[4, true]],
            'select' => ['targetId', 'targetType', 'MAX:number', ['MAX:createdAt', 'createdAt']],
            'groupBy' => ['targetId', 'targetType']
        ];

        $collection = $repository->limit($offset, $params['maxSize'] + 1)->find($selectParams);

        foreach ($collection as $i => $entity) {
            $actionHistoryRecordService->loadParentNameFields($entity);
            $entity->set('id', \Nadlani\Core\Utils\Util::generateId());
        }

        if ($maxSize && count($collection) > $maxSize) {
            $total = -1;
            unset($collection[count($collection) - 1]);
        } else {
            $total = -2;
        }

        return (object) [
            'total' => $total,
            'collection' => $collection
        ];
    }
}

