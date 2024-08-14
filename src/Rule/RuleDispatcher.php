<?php

namespace Presta\MailReceiverBundle\Rule;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Rule\Action\RuleActionHandlingException;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Psr\Log\LoggerInterface;

final class RuleDispatcher
{
    /**
     * @var ActionRegistry
     */
    private $actions;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ActionRegistry $actions, LoggerInterface $logger)
    {
        $this->actions = $actions;
        $this->logger = $logger;
    }

    public function dispatch(Email $email, Rule $rule, RuleExecution $execution): void
    {
        $interrupted = false;
        foreach ($rule->getActions() as $ruleAction) {
            if ($interrupted) {
                $execution->skippedAction($ruleAction);

                continue;
            }

            $action = $this->actions->get($ruleAction->getType());

            try {
                $action->handle($email, $ruleAction->getSettings());
                $execution->performedAction($ruleAction);

                if ($ruleAction->isBreakpoint()) {
                    $this->logger->debug(
                        'Mail hit breakpoint',
                        [
                            'email' => $email->getId(),
                            'breakpoint' => json_encode($ruleAction->getSettings()),
                        ]
                    );

                    $interrupted = true;
                }
            } catch (RuleActionHandlingException $exception) {
                $execution->failedAction($ruleAction, $exception);
            }
        }
    }
}
