<?php

namespace tests\unit\testData\Hooks\testCase1\application\Nadlani\Modules\Crm\Hooks\Note;

class Mentions extends \Nadlani\Hooks\Note\Mentions
{
    public static $order = 9;

    public function beforeSave(\Nadlani\ORM\Entity $entity)
    {

    }

}