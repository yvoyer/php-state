# State Pattern

This package help you to build a workflow for a designated context, that can be encapsulated in your context.
With this package, you do not require the logic to be on the application side.

## Usage

Given you have a `Post` implementation that can have multiple states:

* **Draft**: The post is visible only to the creator and moderators
* **Published**: The post is visible to all users
* **Archived**: The post is visible only to the creator

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
    class Post
    {
    private function
    }


```php


## Events

     *
     * ie. Given the context with alias 'car' has 3 states ("parked", "started", "stopped").
     *
     * The system would generate a custom event on each transition in the following format:
     *
     * - before.{context_alias}.{from_state}_to_{to_state}
     * - after.{context_alias}.{from_state}_to_{to_state}
     * - before.transition
     * - after.transition

## Context Workflow

In order to ensure all the possible transitions are tested,
instead of implementing all the possible conditions in the context object,
the transitions are represented in each state implementations.

    +----------+-----------+-------------+
    | is legal | original  | destination |
    +----------+-----------+-------------+
    | false    | enable    | enable      |
    | true     | suspended | enable      |
    | true     | disabled  | enable      |
    | true     | enable    | suspended   |
    | false    | suspended | suspended   |
    | false    | disabled  | suspended   |
    | true     | enable    | disabled    |
    | false    | suspended | disabled    |
    | false    | disabled  | disabled    |
    +----------+-----------+-------------+
