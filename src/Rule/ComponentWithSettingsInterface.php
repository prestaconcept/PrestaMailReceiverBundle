<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;

interface ComponentWithSettingsInterface
{
    /**
     * @return array<string, mixed>
     */
    public function defaults(): array;

    public function configurator(): SettingsConfiguratorInterface;
}
