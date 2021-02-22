# Renttek_StateMachine Magento Module

This module allows you to define state-machines via XML and use them in your code.

## Installation

1. Install it into your Magento 2 project with composer:
    ```
    composer require renttek/state-machine
    ```

2. Enable module
    ```
    bin/magento module:enable Renttel_StateMachine
    bin/magento setup:upgrade
    ```

## Configuration

All Configuration is done by placing an `state_machine.xml` into the `etc/` directory of any module.
Each Configuration file can define multiple state machines.
The Configuration files are validated against an XSD Schema.

A full explanation can be found here: [Configuration](docs/configuration.md)

## Usage

You can get a defined state-machine using `\Renttek\StateMachine\Model\StateMachineFactory` and use it like this:
```php
/** @var \Renttek\StateMachine\Model\StatefulInterface   $pullRequest */
/** @var \Renttek\StateMachine\Model\StateMachineFactory $stateMachineFactory */
$stateMachine = $stateMachineFactory->get('pull_request');

if ($stateMachine->can($pullRequest, 'request_change') {
    $stateMachine->apply($pullRequest, 'request_change');
} else {
    print_r($stateMachine->getPossibleTransitions($pullRequest));
}
```

The state-machine supports every object which implements the `\Renttek\StateMachine\Model\StatefulInterface`.

A full example using an basic pull-request workflow can be found here: [](docs/examples/pull-request.md)

## License

The MIT License (MIT).
Please see [License File](LICENSE) for more information.
