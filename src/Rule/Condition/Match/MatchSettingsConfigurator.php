<?php

namespace Presta\MailReceiverBundle\Rule\Condition\Match;

use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;
use Presta\MailReceiverBundle\Validator\Constraints\ValidRegex;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

final class MatchSettingsConfigurator implements SettingsConfiguratorInterface
{
    public const DEFAULTS = [
        'operator' => MatchSettings::OPERATOR_CONTAINS,
        'value' => 'Some text', //todo
    ];

    /**
     * @var MatchSettingsDescriptor
     */
    private $descriptor;

    public function __construct(MatchSettingsDescriptor $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    public function configure(FormBuilderInterface $builder): void
    {
        $builder
            ->add('operator', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    $this->descriptor->operator(MatchSettings::OPERATOR_MATCH) => MatchSettings::OPERATOR_MATCH,
                    $this->descriptor->operator(MatchSettings::OPERATOR_CONTAINS) => MatchSettings::OPERATOR_CONTAINS,
                    $this->descriptor->operator(MatchSettings::OPERATOR_EQUALS) => MatchSettings::OPERATOR_EQUALS,
                ],
                'constraints' => [new NotNull()],
                'choice_translation_domain' => false,
                'translation_domain' => 'PrestaMailReceiverBundle',
                'label' => 'rule.form.label.operator'
                ])
            ->add('value', TextType::class, [
                'required' => true,
                'constraints' => [new NotNull(), new ValidRegex()],
                'translation_domain' => 'PrestaMailReceiverBundle',
                'label' => 'rule.form.label.value'
            ])
        ;
    }
}
