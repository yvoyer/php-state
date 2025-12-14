<?php declare(strict_types=1);

namespace Star\Component\State;

/**
 * An object representing the context that is managed by the state machine.
 */
interface StateContext
{
    /**
     * The state context identifier is used in message to identify the context.
     * If the context of the state was an object Post or Comment, the identifier could be "post", or "comment".
     *
     * @return string
     */
    public function toStateContextIdentifier(): string;
}
