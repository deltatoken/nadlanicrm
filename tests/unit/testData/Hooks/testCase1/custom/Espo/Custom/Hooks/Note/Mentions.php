<?php

namespace tests\unit\testData\Hooks\testCase1\custom\Nadlani\Custom\Hooks\Note;

class Mentions extends \Nadlani\Hooks\Note\Mentions
{
    public static $order = 7;

    public function beforeSave(\Nadlani\ORM\Entity $entity)
    {

    }
}