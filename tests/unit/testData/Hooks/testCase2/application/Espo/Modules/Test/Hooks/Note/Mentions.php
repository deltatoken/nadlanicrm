<?php

namespace tests\unit\testData\Hooks\testCase2\application\Nadlani\Modules\Test\Hooks\Note;

class Mentions extends \Nadlani\Hooks\Note\Mentions
{
    public static $order = 9;

    public function beforeSave(\Nadlani\ORM\Entity $entity)
    {

    }
}