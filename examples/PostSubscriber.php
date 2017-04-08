<?php
///**
// * This file is part of the php-state project.
// *
// * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
// */
//
//namespace Star\Component\State\Example;
//
//use Star\Component\State\Event\ContextTransitionWasRequested;
//use Star\Component\State\Event\ContextTransitionWasSuccessful;
//use Star\Component\State\Event\StateEventStore;
//use Star\Component\State\Event\TransitionWasRequested;
//use Star\Component\State\Event\TransitionWasSuccessful;
//use Star\Component\State\EventSubscriber;
//
//final class PostSubscriber implements EventSubscriber
//{
//    /**
//     * @var bool|TransitionWasRequested
//     */
//    public $beforeTransition = false;
//
//    /**
//     * @var bool|TransitionWasSuccessful
//     */
//    public $afterTransition = false;
//
//    /**
//     * @var bool|ContextTransitionWasRequested
//     */
//    public $preSpecificTransition = false;
//
//    /**
//     * @var bool|ContextTransitionWasSuccessful
//     */
//    public $postSpecificTransition = false;
//
//    public static function getSubscribedEvents()
//    {
//        return [
//            StateEventStore::BEFORE_TRANSITION => 'beforeTransition',
//            StateEventStore::AFTER_TRANSITION => 'afterTransition',
//            StateEventStore::preTransitionEvent(Post::TRANSITION_PUBLISH, Post::ALIAS) => 'preSpecificTransition',
//            StateEventStore::postTransitionEvent(Post::TRANSITION_PUBLISH, Post::ALIAS) => 'postSpecificTransition',
//        ];
//    }
//
//    public function beforeTransition(TransitionWasRequested $event)
//    {
//        $this->beforeTransition = $event;
//    }
//
//    public function afterTransition(TransitionWasSuccessful $event)
//    {
//        $this->afterTransition = $event;
//    }
//
//    public function preSpecificTransition(ContextTransitionWasRequested $event)
//    {
//        $this->preSpecificTransition = $event;
//    }
//
//    public function postSpecificTransition(ContextTransitionWasSuccessful $event)
//    {
//        $this->postSpecificTransition = $event;
//    }
//}
//
