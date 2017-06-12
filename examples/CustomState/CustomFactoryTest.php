<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\StateAssertion;

final class CustomFactoryTest extends \PHPUnit_Framework_TestCase
{
    use StateAssertion;

    public function test_it_should_be_unlocked_by_default()
    {
        $door = new Door();
        $this->assertFalse($door->isLocked());
        $this->assertTrue($door->isUnlocked());
        $this->assertTrue($door->handleIsTurnable());

        return $door;
    }

    /**
     * @param Door $door
     *
     * @depends test_it_should_be_unlocked_by_default
     */
    public function test_it_should_not_allow_to_transition_from_unlocked_to_unlocked(Door $door)
    {
        $this->assertTrue($door->isUnlocked());
        $this->assertInvalidTransition(
            DoorState::UNLOCK,
            Door::class,
            DoorState::UNLOCKED
        );
        $door->unlock();
   }

    /**
     * @param Door $unlockedDoor
     *
     * @depends test_it_should_be_unlocked_by_default
     *
     * @return Door
     */
    public function test_it_should_lock_the_unlocked_door(Door $unlockedDoor)
    {
        $unlockedDoor->lock();

        $this->assertTrue($unlockedDoor->isLocked());
        $this->assertFalse($unlockedDoor->isUnlocked());
        $this->assertTrue($unlockedDoor->handleIsTurnable());

        return $unlockedDoor;
    }

    /**
     * @param Door $door
     *
     * @depends test_it_should_lock_the_unlocked_door
     */
    public function test_it_should_not_allow_to_transition_from_locked_to_locked(Door $door)
    {
        $this->assertTrue($door->isLocked());
        $this->assertInvalidTransition(
            DoorState::LOCK,
            Door::class,
            DoorState::LOCKED
        );
        $door->lock();
    }

    /**
     * @param Door $locked
     *
     * @return Door
     *
     * @depends test_it_should_lock_the_unlocked_door
     */
    public function test_it_should_unlock_the_locked_door(Door $locked)
    {
        $locked->unlock();
        $this->assertFalse($locked->isLocked());
        $this->assertTrue($locked->isUnlocked());
        $this->assertTrue($locked->handleIsTurnable());

        return $locked;
    }
}
