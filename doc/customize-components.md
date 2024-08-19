# Customize Components

> [!NOTE]  
> The term "Component" refer to `Actions` and `Conditions`.
> The following documentation, and the example shown are valid for both.

## Description, help and label

Some PHP interfaces can be implemented to override displayed labels or to add messages to help the user.

### Description

Implementing `ComponentWithDescriptionInterface` allows you to define a way to describe your component \
(and it's configuration) in the readonly view of the admin.

```php
<?php

namespace App\MailReceiverBundle\Action;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;

class DoSomethingAction implements RuleActionInterface, ComponentWithDescriptionInterface
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

### Help

Implementing `ComponentWithHelpInterface` allows you to define the help shown in the admin forms of your component.

```php
<?php

namespace App\MailReceiverBundle\Action;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithHelpInterface;

class DoSomethingAction implements RuleActionInterface, ComponentWithHelpInterface
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

### Label

Implementing `ComponentWithLabelInterface` allows you to define the label shown in the admin forms of your component.

```php
<?php

namespace App\MailReceiverBundle\Action;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithLabelInterface;

class DoSomethingAction implements RuleActionInterface, ComponentWithLabelInterface
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


## Configuration

Implementing `ComponentWithSettingsInterface` allows you to define extra form fields for your component in the admin.
This will declare configuration for it, and these values will be provided at process time.

```php
<?php

namespace App\MailReceiverBundle\Action;

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;

class DoSomethingAction implements RuleActionInterface, ComponentWithSettingsInterface
{
    public function handle(Email $email, array $settings = []): void
    {
        // parameters in $settings
    }

    public function defaults(): array
    {
        return [
            // ...
        ];
    }

    public function configurator(): SettingsConfiguratorInterface
    {
        return new class() implements SettingsConfiguratorInterface {
            public function configure(FormBuilderInterface $builder): void
            {
                // todo add your fields here, example:
                $builder->add('field', EmailType::class, [
                    'required' => true,
                    'translation_domain' => 'PrestaMailReceiverBundle',
                    'label' => 'rule.form.label.field',
                    'help' => 'rule.form.help.field',
                    'constraints' => [
                        new Assert\NotNull(),
                    ],
                ]);
            }
        };
    }
}
```
