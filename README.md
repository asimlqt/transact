# Transact
Transact is a transaction library for PHP similar to the way database transactions work but for PHP code.

The purpose is to be able to execute multiple actions which might depend on the previous action completing successfully before eexecuting the next one and in case of failure you want to revert back to the original state.

## Installation
Use the following composer command to install
```
composer require asimlqt/transact
```

## Example
The following example creates 3 simple actions that echo some text during the `execute` and `revert` methods. This example will be referenced throughout the rest of the README and only changes will be listed.

```php
use Asimlqt\Transact\TransactionManager;
use Asimlqt\Transact\Action;
use Asimlqt\Transact\TransactionFailedException;

$transactionManager = new TransactionManager();

class Action1 extends Action {
    public function execute() {
        echo "Action1 execute\n";
    }
    public function revert() {
        echo "Action1 revert\n";
    }
}

class Action2 extends Action {
    public function execute() {
        echo "Action2 execute\n";
    }
    public function revert() {
        echo "Action2 revert\n";
    }
}

class Action3 extends Action {
    public function execute() {
        echo "Action3 execute\n";
    }
    public function revert() {
        echo "Action3 revert\n";
    }
}

$transactionManager
    ->addAction(new Action1())
    ->addAction(new Action2())
    ->addAction(new Action3());

try {
    $transactionManager->execute();
    echo "Transaction completed successfully\n";
} catch (TransactionFailedException $e) {
    echo $e->getMessage() . "\n";
}
```

The output of the program is

```
Action1 execute
Action2 execute
Action3 execute
Transaction completed successfully
```

## Order of execution

The actions are executed in the order they are added. When there is an error i.e. when an exception is thrown the `revert` methods will be called in reverse order. The `revert` method of the last successful action will be called first then the one before that etc. all the way to the first action.

For example if the third action threw an exception then the `revert` method of `Action2` will be called then the `revert` method of `Action1`:

```php
class Action3 extends Action {
    public function execute() {
        echo "Action3 execute\n";
        throw new Exception();
    }
    public function revert() {
        echo "Action3 revert\n";
    }
}
```

The output of will be:

```
Action1 execute
Action2 execute
Action3 execute
Action2 revert
Action1 revert
Transaction failed
```

You've probably noticed that the `Action3` execute method through an exception so why wasn't the revert method of `Action3` called? That's becasue each action should perform only one task, so if an action threw an exception becasue it couldn't complete the task then there is nothing to revert!

## Passing data to actions

Usually the actions will need some data to perform their tasks so to do this use the `Intent` object which is a simple wrapper around an array. It only has 2 methods `get` and `set`. This will automatically be injected into the actions before the execute method is called.

```php
$intent = new Asimlqt\Transact\Intent();
$intent->set("user", $user);
$transactionManager->setIntent($intent);
```

Then in the Action you can retrieve the user using:

```php
    public function execute() {
        $user = $this->getIntent()->get("user");
        ...
    }
```

> Note that the same intent object will be forwarded to all actions so it is possible to overwrite data. It can also be useful if you need to pass data from one action to another. 

## Retry Policies

If an action fails to complete it's task due to some external factor you might want to try the action again e.g. making an API request. Retry policies can be specified for both, `execute` and `revert`. They are action specific so you can only specify a retry policy for only one action or different policies for different actions. The following policies are currently available:

### RetryNone
This is the default policy if you don't explicitly specify one. this does not perform any retries.

### RetryOnce
This will immediately try the action again before marking it as failed.

### RetryNumTimes
This will try the action repeatedly for the specified number of times.

### RetryAfter
This will retry the request once more after a delay of specified microseconds.

You will need to set these on the Action objects before adding them to the transaction manager:

```php
$action1 = new Action1();
$action1->setExecuteRetryPolicy(new Asimlqt\Transact\Retry\RetryOnce());
$transactionManager->addAction($action1);
```