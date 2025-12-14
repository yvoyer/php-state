# Release notes

# 3.3.0

This release is a deprecation release. Deprecation warning will be issued when using a changed feature.

* Introduction of a `StateContext` to replace the context that could be mixed.

Before 3.3.0, a context could be `"post"` or an object that was converted to a FQCN.

Starting with 4.0, we'll only accept implementation of `StateContext`. You will need your context to
 implement the interface, or use the adapters [StringAdapterContext](src/Context/StringAdapterContext.php) or
 [ObjectAdapterContext](src/Context/ObjectAdapterContext.php) if you don't want to add new custom code.

# 3.2.0 

[3.2.0](https://github.com/yvoyer/php-state/releases/tag/3.2.0)

# 3.1.0

* [#30](https://github.com/yvoyer/php-state/pull/30)

# 3.0.0

* Bump php version to 7.4+
* Add strict type everywhere.

# 2.1.0

* Remove dependency to dispatcher Event in favor to contract Event for support of Symfony applications

# 2.0.0

* Bump minimum php version to php 7.1
* Add strict type and type hint 
* Add PHPStan, PHPCodeSniffer and Infection checks
* Signature for `ManyToOneTransition` changed from `__construct(string $name, string[] $from, string $to)` to `public function __construct(string $name, string $to, string ...$fromStates)`
* Add final keyword to some classes

# 1.0.0-beta

* Remove state interface (#21)
* Add TransitionCallback (#19)
* Add code coverage (#18)
* Add support for scrutinizer (#17)
* Update documentation (#14)
* Fix #15 - Add event on transition failure (#16)
* Add Doctrine Hydration support (#13)
* Add Metadata concept to create the state machine (#12)
* Add support for multiple from states to one state transition (#11)
