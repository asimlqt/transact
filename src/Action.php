<?php

namespace Asimlqt\Transact;

use Asimlqt\Transact\Retry\RetryPolicy;
use Asimlqt\Transact\Retry\RetryNone;

abstract class Action implements ActionInterface
{
    private $intent;
    private $executeRetryPolicy;
    private $revertRetryPolicy;

    public function __construct()
    {
        $this->executeRetryPolicy = new RetryNone();
        $this->revertRetryPolicy = new RetryNone();
    }
    
    public function setIntent(Intent $intent)
    {
        $this->intent = $intent;
    }

    public function getIntent()
    {
        return $this->intent;
    }
    
    public function getExecuteRetryPolicy()
    {
        return $this->executeRetryPolicy;
    }
    
    public function getRevertRetryPolicy()
    {
        return $this->revertRetryPolicy;
    }
    
    public function setExecuteRetryPolicy(RetryPolicy $retryPolicy)
    {
        $this->executeRetryPolicy = $retryPolicy;
    }
    
    public function setRevertRetryPolicy(RetryPolicy $retryPolicy)
    {
        $this->revertRetryPolicy = $retryPolicy;
    }
}
