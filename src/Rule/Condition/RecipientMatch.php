<?php

namespace Presta\MailReceiverBundle\Rule\Condition;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;
use Presta\MailReceiverBundle\Rule\ComponentWithSettingsInterface;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchDependenciesTrait;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Presta\MailReceiverBundle\Rule\SettingsConfiguratorInterface;

final class RecipientMatch implements RuleConditionInterface, ComponentWithSettingsInterface, ComponentWithDescriptionInterface
{
    use MatchDependenciesTrait;

    public function defaults(): array
    {
        return MatchSettingsConfigurator::DEFAULTS;
    }

    public function configurator(): SettingsConfiguratorInterface
    {
        return $this->settings;
    }

    public function satisfy(Email $email, array $settings = []): bool
    {
        return $this->match->matchSettings($settings, $email->getRecipient());
    }

    public function describe(array $settings = []): string
    {
        return $this->descriptor->settings('rule.description.conditions.recipient_match', $settings);
    }
}
