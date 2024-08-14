# Installation

## Files

Install the bundle:
```console
composer require presta/mail-receiver-bundle
```

Enable the bundle:
```diff
# config/bundles.php
return [
+    Presta\MailReceiverBundle\PrestaMailReceiverBundle::class => ['all' => true],
];
```

## Database

Update your schema to create the tables required for our entities:
```console
bin/console doctrine:schema:update 
```

> Or create a migration if you have `DoctrineMigrationsBundle` installed:
> ```console
> bin/console doctrine:migrations:diff
> bin/console doctrine:migrations:migrate
> ```
