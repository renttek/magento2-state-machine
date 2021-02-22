# Configuration

This is the full configuration needed to define a state-machine for use in magento 2.
This XML must be placed inside any module as `etc/state_machine.xml`:

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

### States

This is the list of possible states in the state-machine.
Every state is referenced by the given `name`, which must be unique inside a state-machine.

The inner value of the `<state>` ta is the display label for use in the UI.
The `translate` attribute should be used as a flag if the display label should be translated.
(Currently it is the job of the user/dev using this to translate the text. This module performs no translations)
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd">
    <state_machine name="pull_request">
        <!-- ... -->
        <states>
            <state name="start" translate="true">Start</state>
            <!-- ... -->
        </states>
        <!-- ... -->
    </state_machine>
</config>
```

### Initial State

The initial state is just a pointer to an existing state, which should be used for new objects.
The inner value of the `<initial_state>` tag has to be the same value as one of the `<state>`'s name attribute¹.
Using this as the starting state is not enforced anywhere in the code / objects can be assigned any other state at start.

1) This is currently only enforced by the PHP code and not the XSD.

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd">
    <state_machine name="pull_request">
        <initial_state>start</initial_state>
        <!-- ... -->
    </state_machine>
</config>
```

### Transitions

Transitions are predefined changes of the state.
Every transition is referenced by the given `name`, which must be unique inside a state-machine.

Each transition has one Label, one target state (`<to>`) and one or more source states `<from>`.
The label behaves the same as the state definition: it contains a label, and a flag if the label should be translated.

The `to` tag / target state defines the new state of the processed object after applying a transition.
The `from` tags / source states define which states allow the use of this transition.

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Renttek_StateMachine:etc/state_machine.xsd">
    <state_machine name="pull_request">
        <initial_state>start</initial_state>
        <!-- ... -->
        <transitions>
            <!-- ... -->
            <transition name="update">
                <label translate="true">Update</label>
                <from name="coding"/>
                <from name="review"/>
                <to name="test"/>
            </transition>
            <!-- ... -->
        </transitions>
    </state_machine>
</config>
```
