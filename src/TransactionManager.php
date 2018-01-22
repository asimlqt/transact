<?php

namespace Asimlqt\Transact;

use Asimlqt\Transact\Retry\RetriesExhaustedException;
use Asimlqt\Transact\TransactionAbortedException;

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

    public function addAction(ActionInterface $action)
    {
        $this->queue->enqueue($action);
        return $this;
    }

    public function setIntent(IntentInterface $intent)
    {
        $this->intent = $intent;
        return $this;
    }
    
    public function execute()
    {
        while($this->queue->count() > 0) {
            $action = $this->queue->dequeue();
            $action->setIntent($this->intent);
            $retryPolicy = $action->getExecuteRetryPolicy();
            
            try {
                while (true) {
                    if (!$retryPolicy->canRetry()) {
                        throw new RetriesExhaustedException();
                    }
                    
                    try {
                        $action->execute();
                        break;
                    } catch(TransactionAbortedException $e) {
                        throw $e;
                    } catch(\Exception $e) {
                       $retryPolicy->tried();
                    }
                }
            } catch(TransactionAbortedException $e) {
                $this->revert();
                throw new TransactionFailedException(
                    "Transaction aborted",
                    $e->getCode(),
                    $e
                );
            } catch(RetriesExhaustedException $e) {
                $this->revert();
                throw new TransactionFailedException(
                    "Transaction failed",
                    $e->getCode(),
                    $e
                );
            }
            
            $this->stack->push($action);
        }

    }

    protected function revert()
    {
        while($this->stack->count() > 0) {
            $action = $this->stack->pop();
            $retryPolicy = $action->getRevertRetryPolicy();
            
            try {
                while (true) {
                    if (!$retryPolicy->canRetry()) {
                        throw new RetriesExhaustedException();
                    }
                    
                    try {
                        $action->revert();
                        break;
                    } catch(\Exception $e) {
                       $retryPolicy->tried();
                    }
                }
            } catch(RetriesExhaustedException $e) {
                throw new TransactionFailedException(
                    "Transaction failed",
                    $e->getCode(),
                    $e
                );
            }
        }
    }
}
