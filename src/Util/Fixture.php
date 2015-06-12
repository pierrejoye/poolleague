<?php

namespace Pool\Util;

use Pool\Util\Fixture\TwoWays;

class Fixture
{
    public static function factory($type = 'default')
    {
        return new TwoWays();
    }
}
