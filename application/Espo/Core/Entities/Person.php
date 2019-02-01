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

namespace Nadlani\Core\Entities;

class Person extends \Nadlani\Core\ORM\Entity
{
    public function _setLastName($value)
    {
        $this->setValue('lastName', $value);

        $firstName = $this->get('firstName');
        if (empty($firstName)) {
            $this->setValue('name', $value);
        } else {
            $this->setValue('name', $firstName . ' ' . $value);
        }
    }

    public function _setFirstName($value)
    {
        $this->setValue('firstName', $value);

        $lastName = $this->get('lastName');
        if (empty($lastName)) {
            $this->setValue('name', $value);
        } else {
            $this->setValue('name', $value . ' ' . $lastName);
        }
    }
}

