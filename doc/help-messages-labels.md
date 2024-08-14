# Help messages and labels

Some PHP interfaces can be implemented to override displayed labels or to add messages to help the user.\
They can be applied on `Actions` or `Conditions`

#### ComponentWithDescriptionInterface

This PHP interface allows you to override the labels showed in the rules list and in the executed actions on an execution

```php
<?php

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;

class CreateSomethingAction implements RuleActionInterface, ComponentWithDescriptionInterface
{
    public function handle(Email $email, array $settings = []): void
    {
        // ...
    }

    public function describe(array $settings = []): string
    {
        return 'Does something because we coded it';
    }
}
```

#### ComponentWithHelpInterface

When editing an execution, there are lists to add conditions or actions to the execution.\
This PHP interface allows you to set the text showed by hovering over the elements in these lists

```php
<?php

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithHelpInterface;

class CreateSomethingAction implements RuleActionInterface, ComponentWithHelpInterface
{
    public function handle(Email $email, array $settings = []): void
    {
        // ...
    }

    public function help(): string
    {
        return 'Does something';
    }
}
```

#### ComponentWithLabelInterface

When editing an execution, there are lists to add conditions or actions to the execution.\
This PHP interface allows you to override the element labels in these lists

```php
<?php

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithLabelInterface;

class CreateSomethingAction implements RuleActionInterface, ComponentWithLabelInterface
{
    public function handle(Email $email, array $settings = []): void
    {
        // ...
    }

    public function label(array $settings = []): string
    {
        return '[Tag] Does something';
    }
}
```
