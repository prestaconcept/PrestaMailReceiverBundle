<?php

namespace Presta\MailReceiverBundle\Rule\Condition;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Action\RuleActionHandlingException;

interface RuleConditionInterface
{
    /**
     * @param Email                $email
     * @param array<string, mixed> $settings
     *
     * @return bool
     * @throws RuleActionHandlingException
     */
    public function satisfy(Email $email, array $settings = []): bool;
}
