# Features included in PrestaMailReceiverBundle

## Commands

- `presta:mail-receiver:receive` : Receive email content and store it
- `presta:mail-receiver:dispatch` : Dispatch waiting emails to matching rules actions
- `presta:mail-receiver:archive` : Remove emails that have spent a certain amount of time in a given status
  - `waiting`: 1 month
  - `treated`: 3 days
  - `unmatched`: 4 months
  - `errored`: 2 weeks

## Integration into Sonata

The bundle comes with Sonata interfaces for configuring rules and groups of rules, and for viewing incoming e-mails and rule executions.

### Menu
The available items are :
- `presta_mail_receiver.admin.email`
- `presta_mail_receiver.admin.rule`
- `presta_mail_receiver.admin.execution`
- `presta_mail_receiver.admin.rule_group`


> Example:
> ```yaml
> # sonata_admin.yaml
> sonata_admin:
>     dashboard:
>         groups:
>             presta_mail_receiver:
>                 label: Emails received
>                 icon: '<i class="fa fa-envelope" aria-hidden="true"></i>'
>                 translation_domain: admin
>                 items:
>                     - presta_mail_receiver.admin.email
>                     - presta_mail_receiver.admin.rule
>                     - presta_mail_receiver.admin.execution
>                     - presta_mail_receiver.admin.rule_group
> ```
>
> ![menu](img/menu.jpg)

### Emails received
#### list
![email-received-list.jpg](img/email-received-list.jpg)
#### details
![email-received-details.jpg](img/email-received-details.jpg)
#### processed tests
![email-received-tests.jpg](img/email-received-tests.jpg)

### Rules
#### list
![rules-list.jpg](img/rules-list.jpg)
#### edition
![rules-edition-general.jpg](img/rules-edition-general.jpg)
![rules-edition-conditions.jpg](img/rules-edition-conditions.jpg)
![rules-edition-actions.jpg](img/rules-edition-actions.jpg)
#### edition - add action/condition
![rules-edition-add-condition.jpg](img/rules-edition-add-condition.jpg)
![rules-edition-add-action.jpg](img/rules-edition-add-action.jpg)

### Executions
#### list
![executions-list.jpg](img/executions-list.jpg)
#### details
![executions-details.jpg](img/executions-details.jpg)

### Rules group
#### list
![rules-groups-list.jpg](img/rules-groups-list.jpg)
#### edition
![rules-groups-edition.jpg](img/rules-groups-edition.jpg)
