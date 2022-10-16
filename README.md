# PHP State machine

![Build Status](https://github.com/yvoyer/php-state/actions/workflows/php.yml/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yvoyer/php-state/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yvoyer/php-state/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yvoyer/php-state/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yvoyer/php-state/?branch=master)

This package help you build a workflow for a designated context, that can be encapsulated inside the given context.

It was designed to avoid having a hard dependency to the package. The library do not require you to implement any method.
All the code you need to write can be encapsulated inside your context class, and it stays hidden from your other object.


## Installation

`composer require star/state-machine`

## Features

## States

A state is just a name in which a context can find itself in. The state is usually kept in a persistence platform,
 or kept in the model using a [string](https://github.com/yvoyer/php-state/blob/master/examples/ContextUsingBuilderTest.php)
 representation or a [StateMetadata](https://github.com/yvoyer/php-state/blob/master/examples/ContextUsingCustomMetadataTest.php) class.

## Transitions

A transition is an action on the context that will move from one state to the other. A transition can only
 have one destination state, since there is no way for the machine to know which state to go to. On the other hand,
a transition may have multiple starting states.

If no transition contains the context's current state as a start start, an exception will be raised (unless another
  [TransitionCallback](https://github.com/yvoyer/php-state/blob/master/src/Callbacks/) is given).

## Attributes

Attributes are used to mark a state as having a meaning to the context.

Ie. Given you need a state to be considered active or closed while another state should not,
 you just need to add the `is_active` and `is_closed` attributes to the states that needs them.

```php
// your context using the builder
public function isActive()
{
    return $this->stateMachine()-hasAttribute("is_active");
}
```

```php
// your context using the metadata
public function isActive()
{
    return $this->state->hasAttribute("is_active");
}
```

## Examples of usage

Given you have a `Post` context that can have the following states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator

The post's allowed workflow should be as follow:

| Transitions | draft | published | archived |
| ----------- | ----- | --------- | -------- |
| draft       | N/A   | publish   | N/A      |
| published   | N/A   | N/A       | archive  |
| archived    | N/A   | unarchive | N/A      |

You `Post` class can be defined as one of the following pattern.

### Using the builder in your model

```php
class Post
{
    /**
     * @var string
     */
    private $state;

    public function publish()
    {
        $this->state = $this->stateMachine()->transit("publish", $this);
    }

    public function archive()
    {
        $this->state = $this->stateMachine()->transit("archive", $this);
    }

    public function unarchive()
    {
        $this->state = $this->stateMachine()->transit("unarchive", $this);
    }

    public function isClosed()
    {
        return $this->stateMachine()->hasAttribute("is_closed");
    }

    /**
     * @return StateMachine
     */
    private function stateMachine()
    {
        return StateBuilder::build()
            ->allowTransition("publish", "draft", "published")
            ->allowTransition("archive", "published", "archived")
            ->allowTransition("unarchive", "published", "draft")
            ->addAttribute("is_closed", ["archived", "drafted"])
            ->create($this->state);
    }
}
```

### Wrap the workflow in a class

If you have multiple models that can have the same workflow, defining a class that wraps the workflow can be done using
the [StateMetadata](https://github.com/yvoyer/php-state/blob/master/src/StateMetadata.php).

```php
final class MyStateWorkflow extends StateMetadata
{
    protected function __construct()
    {
        parent::__construct('pending');
    }

    protected function createMachine(StateBuilder $builder)
    {
        $builder->allowTransition("publish", "draft", "published")
        $builder->allowTransition("archive", "published", "archived")
        $builder->allowTransition("unarchive", "published", "draft")
        $builder->addAttribute("is_closed", ["archived", "drafted"])
    }
}

class Post
{
    /**
     * @var string
     */
    private $state;

    public function __construct()
    {
        $this->>state = new MyStateWorkflow();
    }

    public function publish()
    {
        $this->state = $this->state->transit("publish", $this);
    }

    public function archive()
    {
        $this->state = $this->state->transit("archive", $this);
    }

    public function unarchive()
    {
        $this->state = $this->state->transit("unarchive", $this);
    }

    public function isClosed()
    {
        return $this->state->hasAttribute("is_closed");
    }
}
```

## Persistence of state

The package supports the following persistence engine:

* [Doctrine](https://github.com/doctrine/doctrine2): Can be used using `@Embeddable`, see
 [Example of usage](https://github.com/yvoyer/php-state/blob/master/examples/DoctrineMappedContextTest.php).

## Events

The state machine has an internal event handling systems.

Multiple events are triggered at different places, which enables you to hook into the system to add
behavior on certain transitions.

Subscribers that listens to these events will have their configured callback(s) called for any transitions.

* `StateEventStore::BEFORE_TRANSITION`: The event is performed before any transition on the context. See `TransitionWasRequested`.
* `StateEventStore::AFTER_TRANSITION`: This event is performed after any transition is executed on the context. See `TransitionWasSuccessful`.
* `StateEventStore::FAILURE_TRANSITION`: This event is performed before the transition exception is triggered. See `TransitionWasFailed`.

**Subscribing a listener in the machine**

```php
$stateMachine->addListener(
    StateEventStore::BEFORE_TRANSITION,
    function(TransitionWasRequested $event) {
        // do something
    }
);
```

## Transition callbacks

When requesting a transition, another way to hook in the process is to pass
 a [TransitionCallback](https://github.com/yvoyer/php-state/blob/master/src/Callbacks/).

Transition callbacks allow to perform an action before, after or when the transition is not allowed.
By default, an exception is triggered. see [AlwaysThrowExceptionOnFailure](https://github.com/yvoyer/php-state/blob/master/src/Callbacks/AlwaysThrowExceptionOnFailure).

**Callback on a transition**

```php
$this->state->transit("transition", $this, new DoSomethingOnSuccessIfConditionMatches());
```
