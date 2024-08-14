<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Form\Type;

use Presta\MailReceiverBundle\Form\Type\AbstractType;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SettingsConfiguratorInterface $configurator */
        $configurator = $options['configurator'];
        $configurator->configure($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('configurator')
            ->setAllowedTypes('configurator', SettingsConfiguratorInterface::class)
        ;
    }
}
