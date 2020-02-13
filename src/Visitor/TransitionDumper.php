<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use Star\Component\State\TransitionVisitor;

final class TransitionDumper implements TransitionVisitor
{
    /**
     * @var string[][][]
     */
    private $structure = [];

    /**
     * @var string
     */
    private $currentTransition;

    /**
     * @return string[][][]
     */
    public function getStructure(): array
    {
        return $this->structure;
    }

    public function visitTransition(string $name): void
    {
        $this->currentTransition = $name;
    }

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitFromState(string $state, array $attributes): void
    {
        $this->structure[$this->currentTransition]['from'][] = $state;
    }

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitToState(string $state, array $attributes): void
    {
        $this->structure[$this->currentTransition]['to'][] = $state;
    }
}
