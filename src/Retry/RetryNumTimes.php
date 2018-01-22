<?php

namespace Asimlqt\Transact\Retry;

class RetryNumTimes implements RetryPolicy
{
    private $tries = 0;
    private $numTries;
    
    public function __construct($numTries)
    {
        $this->numTries = $numTries;
    }
    
    public function canRetry()
    {
        return $this->tries <= $this->numTries;
    }
    
    public function tried()
    {
        $this->tries++;
    }
}
