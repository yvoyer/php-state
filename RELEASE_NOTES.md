# Release notes

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

