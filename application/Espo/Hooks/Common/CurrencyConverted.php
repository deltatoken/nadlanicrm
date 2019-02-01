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

namespace Nadlani\Hooks\Common;

use Nadlani\ORM\Entity;
use Nadlani\Core\Utils\Util;

class CurrencyConverted extends \Nadlani\Core\Hooks\Base
{
    public static $order = 1;

    protected function init()
    {
        $this->addDependency('metadata');
        $this->addDependency('config');
    }

    protected function getMetadata()
    {
        return $this->getInjection('metadata');
    }

    protected function getConfig()
    {
        return $this->getInjection('config');
    }

    public function beforeSave(Entity $entity, array $options = array())
    {
        $fieldDefs = $this->getMetadata()->get(['entityDefs', $entity->getEntityType(), 'fields'], []);
        foreach ($fieldDefs as $fieldName => $defs) {
            if (!empty($defs['type']) && $defs['type'] === 'currencyConverted') {
                $currencyFieldName = substr($fieldName, 0, -9);
                $currencyCurrencyFieldName = $currencyFieldName . 'Currency';
                if (!$entity->isAttributeChanged($currencyFieldName) && !$entity->isAttributeChanged($currencyCurrencyFieldName)) {
                    continue;
                }
                if (!empty($fieldDefs[$currencyFieldName])) {
                    if ($entity->get($currencyFieldName) === null) {
                        $entity->set($fieldName, null);
                    } else {
                        $currency = $entity->get($currencyCurrencyFieldName);
                        $value = $entity->get($currencyFieldName);
                        if (!$currency) continue;
                        $rates = $this->getConfig()->get('currencyRates', array());
                        $baseCurrency = $this->getConfig()->get('baseCurrency');
                        $defaultCurrency = $this->getConfig()->get('defaultCurrency');
                        if ($defaultCurrency === $currency) {
                            $targetValue = $value;
                        } else {
                            $targetValue = $value;
                            $targetValue = $targetValue / (isset($rates[$baseCurrency]) ? $rates[$baseCurrency] : 1.0);
                            $targetValue = $targetValue * (isset($rates[$currency]) ? $rates[$currency] : 1.0);
                            $targetValue = round($targetValue, 2);
                        }
                        $entity->set($fieldName, $targetValue);
                    }
                }
            }
        }
    }
}
