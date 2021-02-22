# Example: Pull Request

This example contains the definition of a pull request, taken from the symfony workflow component documentation.

Link to Symfony: https://symfony.com/doc/current/workflow/workflow-and-state-machine.html

## The Configuration / XML

This is the full XML for defining the pull request state-machine.
An explanation of the different parts can be found here: [Configuration](../configuration.md)

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd">
    <state_machine name="pull_request">
        <initial_state>start</initial_state>
        <states>
            <state name="start" translate="true">Start</state>
            <state name="coding" translate="true">Coding</state>
            <state name="test" translate="true">Test</state>
            <state name="review" translate="true">Review</state>
            <state name="merged" translate="true">Merged</state>
            <state name="closed" translate="true">Closed</state>
        </states>
        <transitions>
            <transition name="submit">
                <label translate="true">Submit</label>
                <from name="start"/>
                <to name="test"/>
            </transition>
            <transition name="update">
                <label translate="true">Update</label>
                <from name="coding"/>
                <from name="review"/>
                <to name="test"/>
            </transition>
            <transition name="wait_for_review">
                <label translate="true">Wait for Review</label>
                <from name="test"/>
                <to name="review"/>
            </transition>
            <transition name="request_change">
                <label translate="true">Request Change</label>
                <from name="review"/>
                <to name="coding"/>
            </transition>
            <transition name="accept">
                <label translate="true">Accept</label>
                <from name="review"/>
                <to name="merged"/>
            </transition>
            <transition name="reject">
                <label translate="true">Reject</label>
                <from name="review"/>
                <to name="closed"/>
            </transition>
            <transition name="reopen">
                <label translate="true">Reopen</label>
                <from name="closed"/>
                <to name="review"/>
            </transition>
        </transitions>
    </state_machine>
</config>
```

## Usage

## Prerequisites

The first thing needed is an class/object, which implements the `StatefulInterface`

```php
<?php

namespace Vendor\Example\Model;

use Renttek\StateMachine\Model\StatefulInterface;

class PullRequest implements StatefulInterface
{
    // ...
    private string $state;

    public function getState(): string
    {
        return $this->state;
    }
    
    public function setState(string $state): void
    {
        $this->state = $state;
    }
    // ...
}
```

Next the state-machine can be instantiated Factory

```php
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

/** @var \Renttek\StateMachine\Model\StateMachineFactory $stateMachineFactory */
$stateMachineFactory = $objectManager->get(\Renttek\StateMachine\Model\StateMachineFactory::class);

$stateMachine = $stateMachineFactory->get('pull_request');
```

Important: don't use the ObjectManager directly in your code! This is only done for demonstration purposes.

## Getting the initial state

Getting the initial state, e.g. when creating a nw PullRequest is done via the method `getInitialState`

```php
/** @var \Renttek\StateMachine\Model\StateMachine $stateMachine */
$initialState = $stateMachine->getInitialState();

$pullRequest = new \Vendor\Example\Model\PullRequest();
$pullRequest->setState($initialState->getName());
```

## Checking possible transitions

To check if a transition can be applied, you have to pass the object in question and the name of the transition to `can`

```php
/** @var \Renttek\StateMachine\Model\StateMachine $stateMachine */
/** @var \Vendor\Example\Model\PullRequest $pullRequest */

$canApply = $stateMachine->can($pullRequest, 'submit');
```

If you want to know which transitions are possible, without calling `can` for each one, you can call `getPossibleTransitions`

```php
/** @var \Renttek\StateMachine\Model\StateMachine $stateMachine */
/** @var \Vendor\Example\Model\PullRequest $pullRequest */

$transitions = $stateMachine->getPossibleTransitions($pullRequest);

foreach ($transitions as $transition) {
    echo $transition->getLabel();
}
```

## Applying a transitions

A transition can then be applied using the method `apply`

```php
/** @var \Renttek\StateMachine\Model\StateMachine $stateMachine */
/** @var \Vendor\Example\Model\PullRequest $pullRequest */

$stateMachine->apply($pullRequest, 'submit');
```

This will set the state of th object using the `setState` method from the `StatefulInterface`

## Validating an object

Given that an object can return any string when calling `getState`, you can check if the returned value is indeed an existing state:

```php
/** @var \Renttek\StateMachine\Model\StateMachine $stateMachine */
/** @var \Vendor\Example\Model\PullRequest $pullRequest */

$isValid = $stateMachine->hasValidState($pullRequest);
```
