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

namespace Nadlani\Core\Formula\Functions\RecordGroup;

use \Nadlani\ORM\Entity;
use \Nadlani\Core\Exceptions\Error;

class ExistsType extends \Nadlani\Core\Formula\Functions\Base
{
    protected function init()
    {
        $this->addDependency('entityManager');
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            throw new Error();
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) < 3) {
            throw new Error();
        }

        $entityType = $this->evaluate($item->value[0]);

        $whereClause = [];

        $i = 1;
        while ($i < count($item->value) - 1) {
            $key = $this->evaluate($item->value[$i]);
            $value = $this->evaluate($item->value[$i + 1]);
            $whereClause[$key] = $value;
            $i = $i + 2;
        }

        return !!$this->getInjection('entityManager')->getRepository($entityType)->where($whereClause)->findOne();
    }
}
