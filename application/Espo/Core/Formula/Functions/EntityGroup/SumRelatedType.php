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

namespace Nadlani\Core\Formula\Functions\EntityGroup;

use \Nadlani\ORM\Entity;
use \Nadlani\Core\Exceptions\Error;

class SumRelatedType extends \Nadlani\Core\Formula\Functions\Base
{
    protected function init()
    {
        $this->addDependency('entityManager');
        $this->addDependency('selectManagerFactory');
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            throw new Error();
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) < 2) {
            throw new Error();
        }

        $link = $this->evaluate($item->value[0]);

        if (empty($link)) {
            throw new Error("No link passed to sumRelated function.");
        }

        $field = $this->evaluate($item->value[1]);

        if (empty($field)) {
            throw new Error("No field passed to sumRelated function.");
        }

        $filter = null;
        if (count($item->value) > 2) {
            $filter = $this->evaluate($item->value[2]);
        }

        $entity = $this->getEntity();

        $entityManager = $this->getInjection('entityManager');

        $foreignEntityType = $entity->getRelationParam($link, 'entity');

        if (empty($foreignEntityType)) {
            throw new Error();
        }

        $foreignSelectManager = $this->getInjection('selectManagerFactory')->create($foreignEntityType);

        $foreignLink = $entity->getRelationParam($link, 'foreign');

        if (empty($foreignLink)) {
            throw new Error("No foreign link for link {$link}.");
        }

        $selectParams = $foreignSelectManager->getEmptySelectParams();

        if ($filter) {
            $foreignSelectManager->applyFilter($filter, $selectParams);
        }

        $selectParams['select'] = [[$foreignLink . '.id', 'foreignId'], 'SUM:' . $field];

        $foreignSelectManager->addJoin($foreignLink, $selectParams);

        $selectParams['groupBy'] = [$foreignLink . '.id'];

        $selectParams['whereClause'][] = [
            $foreignLink . '.id' => $entity->id
        ];

        $entityManager->getRepository($foreignEntityType)->handleSelectParams($selectParams);

        $sql = $entityManager->getQuery()->createSelectQuery($foreignEntityType, $selectParams);

        $pdo = $entityManager->getPDO();
        $sth = $pdo->prepare($sql);
        $sth->execute();
        $rowList = $sth->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($rowList)) {
            return 0;
        }

        return $rowList[0]['SUM:' . $field];
    }
}