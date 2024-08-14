<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Form\Type;

use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Form\Type\AbstractType;
use Presta\MailReceiverBundle\Form\Type\SettingsType;
use Presta\MailReceiverBundle\Form\Type\TemplateType;
use Presta\MailReceiverBundle\Rule\ComponentWithHelpInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Presta\MailReceiverBundle\Rule\ConditionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RuleConditionVirtualType extends AbstractType
{
    public function __construct(private ConditionRegistry $conditions)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var RuleCondition $entity */
        $entity = $options['entity'];

        $builder->add('type', TemplateType::class, ['template' => '@PrestaMailReceiver/form/condition_type.html.twig', 'help' => $this->getHelp($entity)]);

        $configurator = $this->getConfigurator($entity);
        if ($configurator) {
            $builder->add('settings', SettingsType::class, ['configurator' => $configurator, 'label' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('inherit_data', true)
            ->setDefault('data_class', RuleCondition::class)
            ->setRequired('entity')
            ->setAllowedTypes('entity', RuleCondition::class)
        ;
    }

    private function getConfigurator(RuleCondition $entity): ?SettingsConfiguratorInterface
    {
        $condition = $this->getCondition($entity);
        if (!$condition instanceof ComponentWithSettingsInterface) {
            return null;
        }

        return $condition->configurator();
    }

    private function getHelp(RuleCondition $entity): ?string
    {
        $condition = $this->getCondition($entity);
        if (!$condition instanceof ComponentWithHelpInterface) {
            return null;
        }

        return $condition->help();
    }

    private function getCondition(RuleCondition $condition): ?RuleConditionInterface
    {
        $type = $condition->getType();
        if ($type === null) {
            return null;
        }

        try {
            return $this->conditions->get($type);
        } catch (ConditionNotRegisteredException $exception) {
            return null; //todo
        }
    }
}
