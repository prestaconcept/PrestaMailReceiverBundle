<?php

namespace Presta\MailReceiverBundle\Rule;

use Symfony\Component\Form\FormBuilderInterface;

interface SettingsConfiguratorInterface
{
    public function configure(FormBuilderInterface $builder): void;
}
