<?php

namespace Asimlqt\Transact\Retry;

class RetryAfter implements RetryPolicy
{
    private $tries = 0;
    private $microseconds;
    
    public function __construct($microseconds)
    {
        $this->microseconds = $microseconds;
    }
    
    public function canRetry()
    {
        return $this->tries <= 1;
    }
    
    public function tried() {
        $this->tries++;
        usleep($this->microseconds);
    }
}
