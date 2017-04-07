# State Pattern

[![Build Status](https://travis-ci.org/yvoyer/php-state.svg?branch=master)](https://travis-ci.org/yvoyer/php-state)

This package help you to build a workflow for a designated context, that can be encapsulated in your context.
With this package, you do not require the logic to be on the application side.

## Example of usage

Given you have a `Post` implementation that can have multiple states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator

The post's allowed workflow should be as follow:

    +-----------++------------+------------+------------+
    | from / to ||    draft   | published  |  archived  |
    +===========++============+============+============+
    | draft     || disallowed |   allowed  | disallowed |
    +-----------++------------+------------+------------+
    | published || disallowed | disallowed |   allowed  | 
    +-----------++------------+------------+------------+
    | archived  || disallowed | disallowed | disallowed |
    +-----------++------------+------------+------------+

Many states may be considered to have a specific meaning, using attributes:

    +-----------++------------+------------+
    | from / to || is_active  | is_closed  |
    +===========++============+============+
    | draft     ||   false    |   true     |
    +-----------++------------+------------+
    | published ||   true     |   false    |
    +-----------++------------+------------+
    | archived  ||   false    |   true     |   
    +-----------++------------+------------+

You can setup your `Post` object using the following configuration:

```php
// Post
    /**
     * @return StateMachine
     */
    private function workflow()
    {
        return StateBuilder::build()
            ->allowTransition(self::TRANSITION_PUBLISH, self::STATE_DRAFT, self::STATE_PUBLISHED)
            ->allowTransition(self::TRANSITION_TO_DRAFT, self::STATE_PUBLISHED, self::STATE_DRAFT)
            ->allowTransition(self::TRANSITION_ARCHIVE, self::STATE_PUBLISHED, self::STATE_ARCHIVED)
            ->addAttribute(self::ATTRIBUTE_ACTIVE, self::STATE_PUBLISHED)
            ->addAttribute(self::ATTRIBUTE_CLOSED, [self::STATE_ARCHIVED, self::STATE_DRAFT])
            ->create($this->state);
    }

``` 

### Attributes
 Atributes are used to mark as state as having a certain signification to the context. 
 
 Ie. Given your state needs to be considered as being valid while another state should not, you just need to add the `is_valid`attribute to the states that needs it. In doing so, you'lol be able to add a method `isValid()` on your context that will be defined as follow:
 
 ```php
public function isValid ()
 {
     return $this->machine()-hasAttribute ();
 }
 $machine
 ```
 
## Installation

Using composer, add the following require in your `composer.json`.

```json 
    "require": {
        "star/php-state": "~1.0"
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
