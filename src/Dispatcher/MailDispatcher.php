<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Dispatcher;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Model\Execution;
use Presta\MailReceiverBundle\Model\GroupExecution;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Repository\RuleGroupRepository;
use Presta\MailReceiverBundle\Rule\ApplyRule;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MailDispatcher
{
    public function __construct(
        private RuleGroupRepository $ruleGroupRepository,
        private ApplyRule $applyRule,
        private LoggerInterface $logger,
    ) {
    }

    public function dispatchEmail(Email $email): Execution
    {
        $execution = new Execution($email);
        $groups = $this->ruleGroupRepository->findAll();

        foreach ($groups as $group) {
            $groupExecution = new GroupExecution($group, $execution);
            $execution->addGroupExecution($groupExecution);

            $rulesElements = $group->getRulesElements();
            foreach ($rulesElements as $ruleGroupElement) {
                $rule = $ruleGroupElement->getRule();

                $ruleExecution = new RuleExecution($rule, $groupExecution);
                $groupExecution->addRuleExecution($ruleExecution);

                $email->updateStatus($this->applyRule->applyRule($rule, $email, $ruleExecution));
                if ($email->getStatus() === Email::STATUS_TREATED && $ruleGroupElement->isBreakpoint()) {
                    $this->logger->log(
                        LogLevel::DEBUG,
                        'Mail hit breakpoint',
                        [
                            'email' => $email->getId(),
                            'breakpoint' => $ruleGroupElement->getRule()
                                ->getName(),
                        ]
                    );
                    break;
                }
            }
        }
        $this->logger->log(
            ApplyRule::LOG_LEVEL_STATUS_MAP[$email->getStatus()] ?? LogLevel::DEBUG,
            'Changed email status.',
            ['email' => $email->getId(), 'status' => $email->getStatus()]
        );

        return $execution;
    }
}
