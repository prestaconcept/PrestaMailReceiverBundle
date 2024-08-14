# Settings for actions/conditions

The `ComponentWithSettingsInterface` PHP interfaces can be implemented to add fields in the actions/conditions rule form if you need to give some parameters to the `Actions` or `Conditions`.

```php
<?php

use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;

class CreateSomethingAction implements RuleActionInterface, ComponentWithSettingsInterface
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
                // add your fields here. Such as:
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