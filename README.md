# PHP State machine

[![Build Status](https://travis-ci.org/yvoyer/php-state.svg?branch=master)](https://travis-ci.org/yvoyer/php-state)

This package help you to build a workflow for a designated context, that can be encapsulated inside the given context.

It was designed to avoid add a hard dependancy to the package. The library do not require you to implement any method.
All the code you need to do can be encapsulated inside your context class, and it stays hidden to your other object.


## Installation

Using composer, add the following require in your `composer.json`.

```json 
    "require": {
        "star/state-machine": "~1.0"
    }
```

## Features 

## States

A state is just a name in which a context can find itself in. The state is usually kept in a persistence platform, 
or kept in the model using it. 

## Transitions

A transition is an action on the model that will mode the context from one state to the other. A transition can only 
have one destination state, since there is no way (yet) for the machine to know which state to go to.

If no transition has the context's current state as a start start, an exception will be raised, since the transition
is not allowed.

example:

     +---------------------------------------------------+
     |                  Transitions                      |
     +------------+------------+------------+------------+
     |   State    |            |            |            |
     | from / to  |    draft   | published  |  archived  |
     +============+============+============+============+
     | draft      |     N/A    | publish    |    N/A     |
     +------------+------------+------------+------------+
     | published  |     N/A    |     N/A    | archive    | 
     +------------+------------+------------+------------+
     | archived   |     N/A    | to_draft   |    N/A     |
     +------------+------------+------------+------------+


## Attributes

Attributes are used to mark a state as having a certain signification to the context.
 
Ie. Given your state needs to be considered as being active or closed while another state should not,
 you just need to add the `is_active` and `is_closed` attributes to the states that needs it.
In doing so, you'll be able to add methods `isActive()`, `isClosed()` on your context that will be defined as follow:

    +-------------------+------------+------------+
    | state / attribute | is_active  | is_closed  |
    +===================+============+============+
    | draft             |   false    |   true     |
    +-------------------+------------+------------+
    | published         |   true     |   false    |
    +-------------------+------------+------------+
    | archived          |   false    |   true     |   
    +-------------------+------------+------------+

```php
class Post
{
    /**
     * @return StateMachine
     */
    private function stateMachine()
    {
        return StateBuilder::build()
            // ... your transitions here
            ->addAttribute("is_active", "published")
            ->addAttribute("is_closed", ["archived", "drafted"])
            ->create($this->state);
    }
    
    public function isActive()
    {
        return $this->stateMachine()-hasAttribute("is_active");
    } 

    public function isClosed()
    {
        return $this->stateMachine()-hasAttribute("is_closed");
    } 
}
```

## Example of usage

### Using the builder in your model

Given you have a `Post` implementation that can have multiple states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator

The post's allowed workflow should be as follow:

    +------------+------------+------------+------------+
    | from / to  |    draft   | published  |  archived  |
    +============+============+============+============+
    | draft      |     N/A    | publish    |    N/A     |
    +------------+------------+------------+------------+
    | published  |     N/A    |     N/A    | archive    | 
    +------------+------------+------------+------------+
    | archived   |     N/A    | to_draft   |    N/A     |
    +------------+------------+------------+------------+

You can setup your `Post` object using the following configuration:

```php
class Post
{
    /**
     * @var string The current state (as a string of your model)
     */
    private $state;

    public function publish()
    {
        // set the state to the next one after the transition is done (and if allowed)
        $this->state = $this->stateMachine()->transitContext("publish", $this);
    }

    /**
     * @return StateMachine
     */
    private function stateMachine()
    {
        return StateBuilder::build()
            ->allowTransition("publish", "draft", "published")
            ->allowTransition("to_draft", "published", "draft")
            ->allowTransition("archive", "published", "archived")
            ->addAttribute("is_active", "published")
            ->addAttribute("is_closed", ["archived", "drafted"])
            ->create($this->state);
    }
}
```

### Wrap the workflow in a class

If you have multiple models that can have the same workflow, defining a class that wraps the workflow can be done using
the [StateMetadata](https://github.com/yvoyer/php-state/blob/master/src/StateMetadata.php).

```php
/**
 * +---------------------------------------------------------+
 * |                           Transition                    |
 * +-----------+---------+------------+-----------+----------+
 * | From / To | pending | approved   | published | archived |
 * +-----------+---------+------------+-----------+----------+
 * | pending   |   N/A   |  approve   |    N/A    | discard  |
 * +-----------+---------+------------+-----------+----------+
 * | approved  |   N/A   |    N/A     |  publish  | archive  |
 * +-----------+---------+------------+-----------+----------+
 * | published |   N/A   |   remove   |    N/A    | archive  |
 * +-----------+---------+------------+-----------+----------+
 * | archived  | re-open | un-archive |    N/A    |    N/A   |
 * +-----------+---------+------------+-----------+----------+
 *
 * +-----------------------------------+
 * |           |       Attributes      |
 * +-----------+----------+------------+
 * | State     | is_draft | is_visible |
 * +-----------+----------+------------+
 * | pending   |   true   |   false    |
 * +-----------+----------+------------+
 * | approved  |   true   |   false    |
 * +-----------+----------+------------+
 * | published |   false  |   true     |
 * +-----------+----------+------------+
 * | archived  |   false  |   false    |
 * +-----------+----------+------------+
 */
final class MyStateWorkflow extends StateMetadata
{
    protected function initialState()
    {
        return 'pending';
    }
    
    protected function createMachine(StateBuilder $builder)
    {
        $builder->allowTransition('approve', 'pending', 'approved');
        $builder->allowTransition('discard', 'pending', 'archived');
        $builder->allowTransition('publish', 'approved', 'published');
        $builder->allowTransition('remove', 'published', 'approved');
        $builder->allowTransition('archive', ['approved', 'published'], 'archived');
        $builder->allowTransition('un-archive', 'archived', 'approved');
        
        // Custom transition
        $builder->allowCustomTransition('re-open', new ReOpenTransition());

        // attributes
        $builder->addAttribute('is_visible', 'published');
        $builder->addAttribute('is_draft', ['pending', 'approved']);
    }
}

// Custom transition definition
final class ReOpenTransition implements StateTransition
{
    public function isAllowed($from)
    {
        return 'archived' === $from;
    }

    public function onRegister(RegistryBuilder $registry)
    {
        $registry->registerState('archived', []);
        $registry->registerState('pending', []);
    }

    public function onStateChange(StateMachine $machine)
    {
        $machine->setCurrentState('pending');
    }

    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $visitor->visitFromState('archived');
        $visitor->visitToState('pending');
    }

    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry)
    {
        $registry->getState('archived')->acceptStateVisitor($visitor);
        $registry->getState('pending')->acceptStateVisitor($visitor);
    }
}

final class MyModel
{
    /**
     * @var MyStateWorkflow
     */
    public $state;

    public function __construct()
    {
        $this->state = new MyStateWorkflow();
    }

    public function publish()
    {
        $this->state = $this->state->transit('publish', $this);
    }

    public function approve()
    {
        $this->state = $this->state->transit('approve', $this);
    }

    public function discard()
    {
        $this->state = $this->state->transit('discard', $this);
    }

    public function reOpen()
    {
        $this->state = $this->state->transit('re-open', $this);
    }

    public function remove()
    {
        $this->state = $this->state->transit('remove', $this);
    }

    public function unPublish()
    {
        $this->state = $this->state->transit('un-publish', $this);
    }

    public function archive()
    {
        $this->state = $this->state->transit('archive', $this);
    }

    public function unArchive()
    {
        $this->state = $this->state->transit('un-archive', $this);
    }

    public function isDraft()
    {
        return $this->state->hasAttribute('is_draft');
    }

    public function isVisible()
    {
        return $this->state->hasAttribute('is_visible');
    }
}
```

## Events (Experimental)

**Note: Events are experimental, the API may be subject to changes, or removed.**

The state machine has an internal event handling systems.

Multiple events are triggered at different places, which enables you to hook into the system to add
behavior on certain transitions.

Subscribers that listens to these events will have their configured callback(s) called for any transitions.

* `StateEventStore::BEFORE_TRANSITION`: This event is performed before any transition on the context.
* `StateEventStore::AFTER_TRANSITION`: This event is performed after any transition is executed on the context.
