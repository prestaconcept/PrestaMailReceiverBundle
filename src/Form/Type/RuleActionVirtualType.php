<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Form\Type;

use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Form\Type\AbstractType;
use Presta\MailReceiverBundle\Form\Type\SettingsType;
use Presta\MailReceiverBundle\Form\Type\TemplateType;
use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ActionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\ComponentWithHelpInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RuleActionVirtualType extends AbstractType
{
    public function __construct(private ActionRegistry $actions)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var RuleAction $entity */
        $entity = $options['entity'];

        $builder->add(
            'type',
            TemplateType::class,
            [
                'template' => '@PrestaMailReceiver/form/action_type.html.twig',
                'help' => $this->getHelp($entity),
                'translation_domain' => 'PrestaMailReceiverBundle',
            ]
        );

        $configurator = $this->getConfigurator($entity);
        if ($configurator) {
            $builder->add('settings', SettingsType::class, ['configurator' => $configurator, 'label' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('inherit_data', true)
            ->setDefault('data_class', RuleAction::class)
            ->setRequired('entity')
            ->setAllowedTypes('entity', RuleAction::class)
        ;
    }

    private function getConfigurator(RuleAction $entity): ?SettingsConfiguratorInterface
    {
        $action = $this->getAction($entity);
        if (!$action instanceof ComponentWithSettingsInterface) {
            return null;
        }

        return $action->configurator();
    }

    private function getHelp(RuleAction $entity): ?string
    {
        $action = $this->getAction($entity);
        if (!$action instanceof ComponentWithHelpInterface) {
            return null;
        }

        return $action->help();
    }

    private function getAction(RuleAction $action): ?RuleActionInterface
    {
        $type = $action->getType();
        if ($type === null) {
            return null;
        }

        try {
            return $this->actions->get($type);
        } catch (ActionNotRegisteredException $exception) {
            return null; //todo
        }
    }
}
