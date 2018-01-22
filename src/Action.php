<?php

namespace Asimlqt\Transact;

abstract class Action implements ActionInterface
{
    private $intent;

    public function setIntent(Intent $intent)
    {
        $this->intent = $intent;
    }

    public function getIntent()
    {
        return $this->intent;
    }
    
}
