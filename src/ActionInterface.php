<?php

namespace Asimlqt\Transact;

interface ActionInterface
{
    public function execute();
    public function revert();
    public function setIntent(Intent $intent);
    public function getIntent();
}
