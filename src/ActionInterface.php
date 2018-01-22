<?php

namespace Asimlqt\Transact;

use Asimlqt\Transact\Retry\RetryPolicy;

interface ActionInterface
{
    public function execute();
    public function revert();
    public function setIntent(Intent $intent);
    public function getIntent();
    public function getExecuteRetryPolicy();
    public function getRevertRetryPolicy();
    public function setExecuteRetryPolicy(RetryPolicy $retryPolicy);
    public function setRevertRetryPolicy(RetryPolicy $retryPolicy);
}
