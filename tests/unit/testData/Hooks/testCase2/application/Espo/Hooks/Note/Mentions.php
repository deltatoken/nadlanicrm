<?php

namespace tests\unit\testData\Hooks\testCase2\application\Nadlani\Hooks\Note;

class Mentions extends \Nadlani\Core\Hooks\Base
{
    public static $order = 9;

    public function beforeSave(\Nadlani\ORM\Entity $entity)
    {

    }
}
