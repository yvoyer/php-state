<?php declare(strict_types=1);

namespace Star\Component\State\Port\PHPStan;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type;
use Star\Component\State\StateMetadata;

final class StateMetadataExtension implements Type\DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return StateMetadata::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'transit';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type\Type {
        $reflection = $scope->getClassReflection();
        if ($reflection && $reflection->isSubclassOf(StateMetadata::class)) {
            return new Type\ObjectType($reflection->getName());
        }

        return new Type\ClassStringType();
    }
}
