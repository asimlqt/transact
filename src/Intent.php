<?php

namespace Asimlqt\Transact;

class Intent implements IntentInterface
{
    private $data = [];
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    public function get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        
        return null;
    }
}
