# Core concepts from PrestaMailReceiverBundle

## Rules

A rule defines the condition(s) to be checked, as well as the actions to be taken
if one or more conditions (according to conditions definition) are checked.

> [!NOTE]
> To be executed, a rule must be part of a group of rules.


## Rule Groups

A rule group defines a set of rules, that are to be executed together.
It is handy when you have several ways to tackle the same goal.
The rules in a group are tried one after the other, and if you configured the `breakpoint`,
you can stop the group wherever you want.


## Conditions

Conditions are the analysis phase of the process.
It is here to tell whether it is a mail you want to process, or not.
You will want to check all the details of the email, and decide whether you will act or not.

### Built-in conditions

- [`BodyMatch`](../src/Rule/Condition/BodyMatch.php): the received email body either
  - contains a configured string
  - is equal to a configured string
  - corresponds to a configured regular expression
- [`HasAttachments`](../src/Rule/Condition/HasAttachments.php): the received email has at least 1 attachment
- [`RecipientMatch`](../src/Rule/Condition/RecipientMatch.php): the received email recipient either
  - contains a configured string
  - is equal to a configured string
  - corresponds to a configured regular expression
- [`SenderMatch`](../src/Rule/Condition/SenderMatch.php): the received email sender either
  - contains a configured string
  - is equal to a configured string
  - corresponds to a configured regular expression
- [`SubjectMatch`](../src/Rule/Condition/SubjectMatch.php): the received email subject either
  - contains a configured string
  - is equal to a configured string
  - corresponds to a configured regular expression

### Create your own condition

You can create your own condition by implementing the `RuleConditionInterface` :
the conditions list in a rule edition automatically retrieves all the services implementing this interface.

```php
<?php

namespace App\MailReceiverBundle\Condition;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchDependenciesTrait;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;

final class CheckSomethingCondition implements RuleConditionInterface
{
    use MatchDependenciesTrait;

    public function satisfy(Email $email, array $settings = []): bool
    {
        // todo your condition here
    }
}
```

> [!TIP]
> You can customize labels, descriptions, and even add settings to your custom condition.
> Have a look to the dedicated documentation: [Customize components](customize-components.md).


## Actions

Actions is where your business logic will be.
Whenever the email match the configured condition, the actions will be called to perform that logic.

### Built-in actions

- [`Forward`](../src/Rule/Action/Forward.php): forward the email (with attachments) to a configurable email address
- [`Notify`](../src/Rule/Action/Notify.php): send an email notification from and to a configurable email address

### Create your own action

You can create your own action by implementing the `RuleActionInterface` :
the actions list in a rule edition automatically retrieves all the services implementing this interface.

```php
<?php

namespace App\MailReceiverBundle\Action;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;

class DoSomethingAction implements RuleActionInterface
{
    public function handle(Email $email, array $settings = []): void
    {
        // todo your action here
    }
}
```

> [!TIP]
> You can customize labels, descriptions, and even add settings to your custom action.
> Have a look to the dedicated documentation: [Customize components](customize-components.md).
