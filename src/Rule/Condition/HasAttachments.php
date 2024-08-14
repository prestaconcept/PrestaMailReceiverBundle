<?php

namespace Presta\MailReceiverBundle\Rule\Condition;

use PhpMimeMailParser\Parser;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\ComponentWithDescriptionInterface;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HasAttachments implements RuleConditionInterface, ComponentWithDescriptionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * HasAttachments constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function satisfy(Email $email, array $settings = []): bool
    {
        $parser = new Parser();
        $parser->setText($email->getRaw());

        return count($parser->getAttachments()) > 0;
    }

    public function describe(array $settings = []): string
    {
        return $this->translator->trans('rule.description.conditions.has_attachment', [], 'PrestaMailReceiverBundle');
    }
}
