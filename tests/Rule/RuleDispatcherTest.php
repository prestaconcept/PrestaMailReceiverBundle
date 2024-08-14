<?php

namespace Presta\MailReceiverBundle\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleAction;
use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Entity\RuleGroupElement;
use Presta\MailReceiverBundle\Model\Execution;
use Presta\MailReceiverBundle\Model\GroupExecution;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Presta\MailReceiverBundle\Rule\RuleDispatcher;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;

class RuleDispatcherTest extends TestCase
{
    use ProphecyTrait;

    private function dispatcher(array $actions): RuleDispatcher
    {
        return new RuleDispatcher(new ActionRegistry($actions), new NullLogger());
    }

    public function testDispatch(): void
    {
        $dispatcher = $this->dispatcher([
            'foo' => ($foo = $this->prophesize(RuleActionInterface::class))->reveal(),
            'bar' => ($bar = $this->prophesize(RuleActionInterface::class))->reveal(),
            'not called' => ($notCalled = $this->prophesize(RuleActionInterface::class))->reveal(),
        ]);

        $rule = new Rule();
        $ruleGroup = new RuleGroup();

        $rule->addAction($fooRule1Action = new RuleAction());
        $fooRule1Action->setType('foo');
        $fooRule1Action->setSettings(['parameter' => 1]);

        $rule->addAction($fooRule2Action = new RuleAction());
        $fooRule2Action->setType('foo');
        $fooRule2Action->setSettings(['parameter' => '2']);

        $rule->addAction($barRuleAction = new RuleAction());
        $barRuleAction->setType('bar');

        $ruleGroup->addRulesElement(new RuleGroupElement($rule, $ruleGroup));

        $email = Emails::cheerz();
        $topExecution = new Execution($email);
        $ruleGroupExecution = new GroupExecution($ruleGroup, $topExecution);

        $dispatcher->dispatch($email, $rule, $execution = new RuleExecution($rule, $ruleGroupExecution));

        /** @var $foo RuleActionInterface */
        /** @var $bar RuleActionInterface */
        /** @var $notCalled RuleActionInterface */

        // for $fooRule1Action
        $foo->handle($email, ['parameter' => 1])
            ->shouldHaveBeenCalledTimes(1);

        // for $fooRule2Action
        $foo->handle($email, ['parameter' => '2'])
            ->shouldHaveBeenCalledTimes(1);

        // for $barRuleAction
        $bar->handle($email, [])
            ->shouldHaveBeenCalledTimes(1);

        // for $barRuleAction
        $notCalled->handle(Argument::any(), Argument::any())
            ->shouldNotHaveBeenCalled();

        self::assertCount(3, $results = $execution->getResults());

        self::assertSame('foo', $results[0]->getAction()->getType());
        self::assertSame(['parameter' => 1], $results[0]->getAction()->getSettings());
        self::assertSame('success', $results[0]->getResult());
        self::assertSame(null, $results[0]->getError());

        self::assertSame('foo', $results[1]->getAction()->getType());
        self::assertSame(['parameter' => '2'], $results[1]->getAction()->getSettings());
        self::assertSame('success', $results[1]->getResult());
        self::assertSame(null, $results[1]->getError());

        self::assertSame('bar', $results[2]->getAction()->getType());
        self::assertSame([], $results[2]->getAction()->getSettings());
        self::assertSame('success', $results[2]->getResult());
        self::assertSame(null, $results[2]->getError());
    }
}
