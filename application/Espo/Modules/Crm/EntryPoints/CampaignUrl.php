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
 * along with NadlaniCrm. If not, see http://www.gnu.org/licenses/.phpppph
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "NadlaniCrm" word.
 ************************************************************************/

namespace Nadlani\Modules\Crm\EntryPoints;

use \Nadlani\Core\Utils\Util;

use \Nadlani\Core\Exceptions\NotFound;
use \Nadlani\Core\Exceptions\Forbidden;
use \Nadlani\Core\Exceptions\BadRequest;
use \Nadlani\Core\Exceptions\Error;

class CampaignUrl extends \Nadlani\Core\EntryPoints\Base
{
    public static $authRequired = false;

    public function run()
    {
        if (empty($_GET['id']) || empty($_GET['queueItemId'])) {
            throw new BadRequest();
        }
        $queueItemId = $_GET['queueItemId'];
        $trackingUrlId = $_GET['id'];

        $queueItem = $this->getEntityManager()->getEntity('EmailQueueItem', $queueItemId);
        $trackingUrl = $this->getEntityManager()->getEntity('CampaignTrackingUrl', $trackingUrlId);

        if (!$queueItem || !$trackingUrl) {
            throw new NotFound();
        }

        $target = null;
        $campaign = null;

        $targetType = $queueItem->get('targetType');
        $targetId = $queueItem->get('targetId');

        if ($targetType && $targetId) {
            $target = $this->getEntityManager()->getEntity($targetType, $targetId);
        }

        $campaignId = $trackingUrl->get('campaignId');
        if ($campaignId) {
            $campaign = $this->getEntityManager()->getEntity('Campaign', $campaignId);
        }

        if ($campaign && $target) {
            $campaignService = $this->getServiceFactory()->create('Campaign');
            $campaignService->logClicked($campaignId, $queueItemId, $target, $trackingUrl, null, $queueItem->get('isTest'));
        }

        ob_clean();
        header('Location: ' . $trackingUrl->get('url') . '');
        die;
    }
}

