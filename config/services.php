<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\EntityManager;
use Presta\MailReceiverBundle\Admin\EmailAdmin;
use Presta\MailReceiverBundle\Admin\ExecutionAdmin;
use Presta\MailReceiverBundle\Admin\RuleActionAdmin;
use Presta\MailReceiverBundle\Admin\RuleAdmin;
use Presta\MailReceiverBundle\Admin\RuleConditionAdmin;
use Presta\MailReceiverBundle\Admin\RuleGroupAdmin;
use Presta\MailReceiverBundle\Admin\RuleGroupElementAdmin;
use Presta\MailReceiverBundle\Command\ArchiveEmailCommand;
use Presta\MailReceiverBundle\Command\ReceiveEmailCommand;
use Presta\MailReceiverBundle\Controller\EmailController;
use Presta\MailReceiverBundle\Controller\ExecutionController;
use Presta\MailReceiverBundle\Controller\RuleController;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Execution;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Entity\RuleGroupElement;
use Presta\MailReceiverBundle\Storage\ExecutionStorage;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->private()

        ->load('Presta\\MailReceiverBundle\\', '../src/')
        ->exclude('../src/{Admin,DependencyInjection,Entity,Tests}')

        ->set(EmailAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'label' => 'email_received.name',
            'code' => 'presta_mail_receiver.admin.email',
            'model_class' => Email::class,
            'controller' => EmailController::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(RuleAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'label' => 'rule.name',
            'code' => 'presta_mail_receiver.admin.rule',
            'model_class' => Rule::class,
            'controller' => RuleController::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])
        ->args([
            '$code' => 'presta_mail_receiver.admin.rule',
            '$class' => Rule::class,
            '$baseControllerName' => RuleController::class,
        ])

        ->set(RuleConditionAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'code' => 'presta_mail_receiver.admin.rule_condition',
            'model_class' => RuleCondition::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(RuleActionAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'code' => 'presta_mail_receiver.admin.rule_action',
            'model_class' => RuleAction::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(ExecutionAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'label' => 'execution.name',
            'code' => 'presta_mail_receiver.admin.execution',
            'model_class' => Execution::class,
            'controller' => ExecutionController::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(RuleGroupAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'label' => 'rule_group.name',
            'code' => 'presta_mail_receiver.admin.rule_group',
            'model_class' => RuleGroup::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(RuleGroupElementAdmin::class)
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'code' => 'presta_mail_receiver.admin.rule_group_element',
            'model_class' => RuleGroupElement::class,
            'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
        ])
        ->call('setTranslationDomain', ['PrestaMailReceiverBundle'])

        ->set(ArchiveEmailCommand::class)
        ->args(['$archive' => '%presta.mail_receiver.archive%'])

        ->set(ReceiveEmailCommand::class)
        ->args(['$manager' => service('doctrine.orm.default_entity_manager')])

        ->set(ExecutionStorage::class)
        ->args(['$objectManager' => service('doctrine.orm.default_entity_manager')])
    ;
};
