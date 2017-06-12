# State Pattern

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

## Example of usage

Given you have a `Post` implementation that can have multiple states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator

The post's allowed workflow should be as follow:

    +------------+------------+------------+------------+
    | from / to  |    draft   | published  |  archived  |
    +============+============+============+============+
    | draft      | disallowed | publish    | disallowed |
    +------------+------------+------------+------------+
    | published  | disallowed | disallowed | archive    | 
    +------------+------------+------------+------------+
    | archived   | disallowed | to_draft   | disallowed |
    +------------+------------+------------+------------+

You can setup your `Post` object using the following configuration:

* Using [in-context](https://github.com/yvoyer/php-state/blob/master/examples/Post.php#L109) configuration
* Using [custom state](https://github.com/yvoyer/php-state/blob/master/examples/CustomState/DoorCustomState.php#L37) builder

```php
class Post
{
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

* Using [in-context](https://github.com/yvoyer/php-state/blob/master/examples/Post.php#L109) configuration
* Using [custom state](https://github.com/yvoyer/php-state/blob/master/examples/CustomState/DoorCustomState.php#L46) builder

```php
class Post
{
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

## Events (Experimental)

**Note: Events are experimental, the API may be subject to changes, or removed.**

The state machine has an internal event handling systems.

Multiple events are triggered at different places, which enables you to hook into the system to add
behavior on certain transitions.

Subscribers that listens to these events will have their configured callback(s) called for any transitions.

* `StateEventStore::BEFORE_TRANSITION`: This event is performed before any transition on the context.
* `StateEventStore::AFTER_TRANSITION`: This event is performed after any transition is executed on the context.
