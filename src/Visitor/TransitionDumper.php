<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use Star\Component\State\TransitionVisitor;

final class TransitionDumper implements TransitionVisitor
{
    /**
     * @var string[][][]
     */
    private array $structure = [];
    private string $currentTransition;

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

    public function visitFromState(string $state, array $attributes): void
    {
        $this->structure[$this->currentTransition]['from'][] = $state;
    }

    public function visitToState(string $state, array $attributes): void
    {
        $this->structure[$this->currentTransition]['to'][] = $state;
    }
}
