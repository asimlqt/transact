<?php

namespace Asimlqt\Transact\Retry;

class RetryOnce implements RetryPolicy
{
    private $tries = 0;
    
    public function canRetry()
    {
        return $this->tries <= 1;
    }
    
    public function tried() {
        $this->tries++;
    }
    
//    public function retry()
//    {
//        if ($this->retries++ > 1) {
//            throw new RetriesExhaustedException();
//        }
//        
//        return true;
//    }
}