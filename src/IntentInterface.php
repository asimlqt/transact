<?php

namespace Asimlqt\Transact;

interface IntentInterface
{
    public function set($key, $value);
    public function get($key);
}
