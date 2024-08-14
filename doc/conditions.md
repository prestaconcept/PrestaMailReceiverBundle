# Conditions

## Built-in conditions

When creating a rule, you can set one or several conditions, and choosing if they all need to be verified or only one of them.

Conditions are applicable on 5 elements:
- body content
- has attachment(s)
- recipient
- sender
- subject

For these 5 elements, 3 operators are applicable:
- contains
- is equal to
- corresponds to a regular expression (Regex)

An input let you set the value

## Customized conditions

You can create your own customized condition by implementing the `RuleConditionInterface`

> Example:
> ```php
> <?php
> 
> use Presta\MailReceiverBundle\Entity\Email;
> use Presta\MailReceiverBundle\Rule\Condition\Match\MatchDependenciesTrait;
> use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
> 
> final class MyCondition implements RuleConditionInterface
> {
>     use MatchDependenciesTrait;
> 
>     public function satisfy(Email $email, array $settings = []): bool
>     {
>         // condition
>     }
> }
> ```
