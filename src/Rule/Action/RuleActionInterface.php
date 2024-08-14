<?php

namespace Presta\MailReceiverBundle\Rule\Action;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Action\RuleActionHandlingException;

interface RuleActionInterface
{
    /**
     * @param Email                $email
     * @param array<string, mixed> $settings
     *
     * @throws RuleActionHandlingException
     */
    public function handle(Email $email, array $settings = []): void;
}
