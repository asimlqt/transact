<?php

namespace Asimlqt\Transact\Retry;

class RetryNone implements RetryPolicy
{
    private $tries = 0;
    
    public function canRetry()
    {
        return $this->tries === 0;
    }
    
    public function tried()
    {
        $this->tries++;
    }
}