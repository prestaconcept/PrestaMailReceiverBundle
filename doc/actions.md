# Actions

## Built-in actions
When creating a rule, 2 actions are already available :
- Forward : forward the email (with attachments) to a configurable email address
- Notify : send an email notification from and to a configurable email address

## Customized actions
You can create your own customized action by implementing the `RuleActionInterface` :
the actions list in a rule edition automatically retrieves all the classes implementing `RuleActionInterface`

> Example:
> ```php
> <?php
> 
> use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
> 
> class CreateSomethingAction implements RuleActionInterface
> {
>     public function handle(Email $email, array $settings = []): void
>     {
>         // do your stuff
>     }
> }
> ```
>
> ```yaml
> # translations/PrestaMailReceiverBundle.en.yaml
> rule:
>     action:
>         label_create_something_action: Create something
> ```
