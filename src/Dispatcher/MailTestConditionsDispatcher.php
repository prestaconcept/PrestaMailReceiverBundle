<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Dispatcher;

use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Model\Execution;
use Presta\MailReceiverBundle\Model\GroupExecution;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Repository\RuleGroupRepository;
use Presta\MailReceiverBundle\Rule\RuleMatcher;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MailTestConditionsDispatcher
{
    public function __construct(
        private RuleGroupRepository $ruleGroupRepository,
        private RuleMatcher $ruleMatcher,
        private LoggerInterface $logger
    ) {
    }

    public function testConditionsDispatch(Email $email): Execution
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

                if (!$this->ruleMatcher->match($email, $rule, $ruleExecution)) {
                    continue;
                }

                $ruleExecution->setSatisfied(true);
                if ($ruleGroupElement->isBreakpoint()) {
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

        return $execution;
    }
}
