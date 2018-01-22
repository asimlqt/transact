<?php

namespace Asimlqt\Transact;

class TransactionManager
{
    /**
     * @var \SplQueue
     */
    protected $queue;
    
    /**
     * @var \SplStack
     */
    protected $stack;

    /**
     * @var Intent
     */
    protected $intent;
    
    public function __construct()
    {
        $this->queue = new \SplQueue();
        $this->stack = new \SplStack();
        $this->intent = new Intent();
    }

    public function addAction(Action $action)
    {
        $this->queue->enqueue($action);
        return $this;
    }

    public function setIntent(Intent $intent)
    {
        $this->intent = $intent;
        return $this;
    }
    
    public function execute()
    {
        try {
            while($this->queue->count() > 0) {
                $action = $this->queue->dequeue();
                $action->setIntent($this->intent);
                $action->execute();
                $this->stack->push($action);
            }
        } catch(\Exception $e) {
            $this->revert();
        }
    }

    protected function revert()
    {
        try {
            while($this->stack->count() > 0) {
                $this->stack->pop()->revert();
            }
        } catch(\Exception $e) {
            throw new TransactionFailedException();
        }
    }
}