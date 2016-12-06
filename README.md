# State Pattern

[![Build Status](https://travis-ci.org/yvoyer/php-state.svg?branch=master)](https://travis-ci.org/yvoyer/php-state)

This package help you to build a workflow for a designated context, that can be encapsulated in your context.
With this package, you do not require the logic to be on the application side.

## Example of usage

Given you have a `Post` implementation that can have multiple states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator
* **Deleted**: The post is not visible to anyone.

The post's allowed workflow should be as follow:

    +-----------++------------+------------+------------+------------+
    | from / to ||    draft   | published  |  archived  |   deleted  |
    +===========++============+============+============+============+
    | draft     ||     N/A    |   allowed  | disallowed |   allowed  |
    +-----------++------------+------------+------------+------------+
    | published || allowed    |   N/A      |   allowed  | disallowed |
    +-----------++------------+------------+------------+------------+
    | archived  || allowed    | disallowed |    N/A     |   allowed  |
    +-----------++------------+------------+------------+------------+
    | deleted   || disallowed | disallowed | disallowed |     N/A    |
    +-----------++------------+------------+------------+------------+

You can setup your `Post` object using the following configuration:

```php

    // Post
    /**
     * @return StateMachine
     */
    private function workflow()
    {
        return StateMachine::create($this)
            ->whitelist(self::DRAFT, [self::PUBLISHED, self::DELETED])
            ->whitelist(self::PUBLISHED, [self::DRAFT, self::ARCHIVED])
            ->whitelist(self::ARCHIVED, [self::DRAFT, self::DELETED])
            // deleted post cannot have transitions
        ;
    }

```php

### Attributes

TODO

## Installation

Using composer, add the following require in your `composer.json`.

```

    "require": {
        "star/php-state": ">1.0"
    }

```

## Events

The state machine has an internal event handling systems.

Multiple events are triggered at different places, which enables you to hook into the system to add
behavior on certain transitions.

Subscribers that listens to these events will have their configured callback(s) called for any transitions.

* `StateEventStore::BEFORE_TRANSITION`: This event is performed before any transition on the context.
* `StateEventStore::AFTER_TRANSITION`: This event is performed after any transition is executed on the context.

In order to have more fine grained listening, Subscribers can also choose which transition to listen to
by using specifics transition events. These specifics events are dynamically configured using this format:

* `star_state.before.{context_alias}.{from_state}_to_{to_state}`: Triggers before the context's new state is set.
* `star_state.after.{context_alias}.{from_state}_to_{to_state}`: Triggers after the context's new state was set.

**{context_alias}**: All context classes must implement the `Star\Component\State\StateContext` interface. Doing so,
 you will need to define the alias for your context, that will be used for the custom events.
**{from_state}**: This is the string representation given to a `Star\Component\State\State::toString()` class.
**{to_state}**: This is the string representation given to a `Star\Component\State\State::toString()` class.
