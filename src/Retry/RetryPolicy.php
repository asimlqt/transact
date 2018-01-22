<?php

namespace Asimlqt\Transact\Retry;

interface RetryPolicy
{
    public function canRetry();
    public function tried();
}
