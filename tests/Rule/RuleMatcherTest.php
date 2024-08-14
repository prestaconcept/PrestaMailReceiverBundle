<?php

namespace Presta\MailReceiverBundle\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Rule;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Presta\MailReceiverBundle\Entity\RuleGroup;
use Presta\MailReceiverBundle\Entity\RuleGroupElement;
use Presta\MailReceiverBundle\Model\Execution;
use Presta\MailReceiverBundle\Model\GroupExecution;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Presta\MailReceiverBundle\Rule\RuleMatcher;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class RuleMatcherTest extends TestCase
{
    use ProphecyTrait;

    private function matcher(array $conditions): RuleMatcher
    {
        return new RuleMatcher(new ConditionRegistry($conditions));
    }

    public function testMatchOperatorOr(): void
    {
        $matcher = $this->matcher([
            'foo' => ($foo = $this->prophesize(RuleConditionInterface::class))->reveal(),
            'bar' => ($bar = $this->prophesize(RuleConditionInterface::class))->reveal(),
            'not called' => ($notCalled = $this->prophesize(RuleConditionInterface::class))->reveal(),
        ]);

        $rule = new Rule();
        $ruleGroup = new RuleGroup();
        $ruleGroup->addRulesElement(new RuleGroupElement($rule, $ruleGroup));

        $rule->addCondition($fooRule1Condition = new RuleCondition());
        $fooRule1Condition->setType('foo');
        $fooRule1Condition->setSettings(['parameter' => 1]);

        $rule->addCondition($fooRule2Condition = new RuleCondition());
        $fooRule2Condition->setType('foo');
        $fooRule2Condition->setSettings(['parameter' => '2']);

        $rule->addCondition($barRuleCondition = new RuleCondition());
        $barRuleCondition->setType('bar');

        $rule->setConditionOperator(Rule::OPERATOR_OR);

        $email = Emails::cheerz();
        $topExecution = new Execution($email);
        $ruleGroupExecution = new GroupExecution($ruleGroup, $topExecution);

        /** @var $foo RuleConditionInterface */
        /** @var $bar RuleConditionInterface */
        /** @var $notCalled RuleConditionInterface */

        // for $fooRule1Condition
        $foo->satisfy($email, ['parameter' => 1])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        // for $fooRule2Condition
        $foo->satisfy($email, ['parameter' => '2'])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        // for $barRuleCondition
        $bar->satisfy($email, [])
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        self::assertTrue(
            $matcher->match($email, $rule, $execution = new RuleExecution($rule, $ruleGroupExecution))
        );

        // for $barRuleCondition
        $notCalled->satisfy(Argument::any(), Argument::any())
            ->shouldNotHaveBeenCalled();

        self::assertCount(3, $evaluations = $execution->getEvaluations());

        self::assertSame('foo', $evaluations[0]->getCondition()->getType());
        self::assertSame(['parameter' => 1], $evaluations[0]->getCondition()->getSettings());
        self::assertFalse($evaluations[0]->isSatisfied());
        self::assertSame([], $evaluations[0]->getErrors());

        self::assertSame('foo', $evaluations[1]->getCondition()->getType());
        self::assertSame(['parameter' => '2'], $evaluations[1]->getCondition()->getSettings());
        self::assertFalse($evaluations[1]->isSatisfied());
        self::assertSame([], $evaluations[1]->getErrors());

        self::assertSame('bar', $evaluations[2]->getCondition()->getType());
        self::assertSame([], $evaluations[2]->getCondition()->getSettings());
        self::assertTrue($evaluations[2]->isSatisfied());
        self::assertSame([], $evaluations[2]->getErrors());
    }

    public function testMatchOperatorAnd(): void
    {
        $matcher = $this->matcher(
            [
                'foo' => ($foo = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'bar' => ($bar = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'not called' => ($notCalled = $this->prophesize(RuleConditionInterface::class))->reveal(),
            ]
        );

        $rule = new Rule();
        $ruleGroup = new RuleGroup();
        $ruleGroup->addRulesElement(new RuleGroupElement($rule, $ruleGroup));

        $rule->addCondition($fooRule1Condition = new RuleCondition());
        $fooRule1Condition->setType('foo');
        $fooRule1Condition->setSettings(['parameter' => 1]);

        $rule->addCondition($fooRule2Condition = new RuleCondition());
        $fooRule2Condition->setType('foo');
        $fooRule2Condition->setSettings(['parameter' => '2']);

        $rule->addCondition($barRuleCondition = new RuleCondition());
        $barRuleCondition->setType('bar');

        $rule->setConditionOperator(Rule::OPERATOR_AND);

        $email = Emails::cheerz();
        $topExecution = new Execution($email);
        $ruleGroupExecution = new GroupExecution($ruleGroup, $topExecution);

        /** @var $foo RuleConditionInterface */
        /** @var $bar RuleConditionInterface */
        /** @var $notCalled RuleConditionInterface */

        // for $fooRule1Condition
        $foo->satisfy($email, ['parameter' => 1])
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        // for $fooRule2Condition
        $foo->satisfy($email, ['parameter' => '2'])
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        // for $barRuleCondition
        $bar->satisfy($email, [])
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        self::assertTrue(
            $matcher->match($email, $rule, $execution = new RuleExecution($rule, $ruleGroupExecution))
        );

        // for $barRuleCondition
        $notCalled->satisfy(Argument::any(), Argument::any())
            ->shouldNotHaveBeenCalled();

        self::assertCount(3, $evaluations = $execution->getEvaluations());

        self::assertSame('foo', $evaluations[0]->getCondition()->getType());
        self::assertSame(['parameter' => 1], $evaluations[0]->getCondition()->getSettings());
        self::assertTrue($evaluations[0]->isSatisfied());
        self::assertSame([], $evaluations[0]->getErrors());

        self::assertSame('foo', $evaluations[1]->getCondition()->getType());
        self::assertSame(['parameter' => '2'], $evaluations[1]->getCondition()->getSettings());
        self::assertTrue($evaluations[1]->isSatisfied());
        self::assertSame([], $evaluations[1]->getErrors());

        self::assertSame('bar', $evaluations[2]->getCondition()->getType());
        self::assertSame([], $evaluations[2]->getCondition()->getSettings());
        self::assertTrue($evaluations[2]->isSatisfied());
        self::assertSame([], $evaluations[2]->getErrors());
    }

    public function testNoMatchOperatorOr(): void
    {
        $matcher = $this->matcher(
            [
                'foo' => ($foo = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'bar' => ($bar = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'not called' => ($notCalled = $this->prophesize(RuleConditionInterface::class))->reveal(),
            ]
        );

        $rule = new Rule();
        $ruleGroup = new RuleGroup();
        $ruleGroup->addRulesElement(new RuleGroupElement($rule, $ruleGroup));

        $rule->addCondition($fooRule1Condition = new RuleCondition());
        $fooRule1Condition->setType('foo');
        $fooRule1Condition->setSettings(['parameter' => 1]);

        $rule->addCondition($fooRule2Condition = new RuleCondition());
        $fooRule2Condition->setType('foo');
        $fooRule2Condition->setSettings(['parameter' => '2']);

        $rule->addCondition($barRuleCondition = new RuleCondition());
        $barRuleCondition->setType('bar');

        $rule->setConditionOperator(Rule::OPERATOR_OR);

        $email = Emails::cheerz();
        $topExecution = new Execution($email);
        $ruleGroupExecution = new GroupExecution($ruleGroup, $topExecution);


        /** @var $foo RuleConditionInterface */
        /** @var $bar RuleConditionInterface */
        /** @var $notCalled RuleConditionInterface */

        // for $fooRule1Condition
        $foo->satisfy($email, ['parameter' => 1])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        // for $fooRule2Condition
        $foo->satisfy($email, ['parameter' => '2'])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        // for $barRuleCondition
        $bar->satisfy($email, [])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        self::assertFalse(
            $matcher->match($email, $rule, $execution = new RuleExecution($rule, $ruleGroupExecution))
        );

        // for $barRuleCondition
        $notCalled->satisfy(Argument::any(), Argument::any())
            ->shouldNotHaveBeenCalled();

        self::assertCount(3, $evaluations = $execution->getEvaluations());

        self::assertSame('foo', $evaluations[0]->getCondition()->getType());
        self::assertSame(['parameter' => 1], $evaluations[0]->getCondition()->getSettings());
        self::assertFalse($evaluations[0]->isSatisfied());
        self::assertSame([], $evaluations[0]->getErrors());

        self::assertSame('foo', $evaluations[1]->getCondition()->getType());
        self::assertSame(['parameter' => '2'], $evaluations[1]->getCondition()->getSettings());
        self::assertFalse($evaluations[1]->isSatisfied());
        self::assertSame([], $evaluations[1]->getErrors());

        self::assertSame('bar', $evaluations[2]->getCondition()->getType());
        self::assertSame([], $evaluations[2]->getCondition()->getSettings());
        self::assertFalse($evaluations[2]->isSatisfied());
        self::assertSame([], $evaluations[2]->getErrors());
    }

    public function testNoMatchOperatorAnd(): void
    {
        $matcher = $this->matcher(
            [
                'foo' => ($foo = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'bar' => ($bar = $this->prophesize(RuleConditionInterface::class))->reveal(),
                'not called' => ($notCalled = $this->prophesize(RuleConditionInterface::class))->reveal(),
            ]
        );

        $rule = new Rule();
        $ruleGroup = new RuleGroup();
        $ruleGroup->addRulesElement(new RuleGroupElement($rule, $ruleGroup));

        $rule->addCondition($fooRule1Condition = new RuleCondition());
        $fooRule1Condition->setType('foo');
        $fooRule1Condition->setSettings(['parameter' => 1]);

        $rule->addCondition($fooRule2Condition = new RuleCondition());
        $fooRule2Condition->setType('foo');
        $fooRule2Condition->setSettings(['parameter' => '2']);

        $rule->addCondition($barRuleCondition = new RuleCondition());
        $barRuleCondition->setType('bar');

        $rule->setConditionOperator(Rule::OPERATOR_AND);

        $email = Emails::cheerz();
        $topExecution = new Execution($email);
        $ruleGroupExecution = new GroupExecution($ruleGroup, $topExecution);

        /** @var $foo RuleConditionInterface */
        /** @var $bar RuleConditionInterface */
        /** @var $notCalled RuleConditionInterface */

        // for $fooRule1Condition
        $foo->satisfy($email, ['parameter' => 1])
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        // for $fooRule2Condition
        $foo->satisfy($email, ['parameter' => '2'])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        // for $barRuleCondition
        $bar->satisfy($email, [])
            ->shouldBeCalledTimes(1)
            ->willReturn(false);

        self::assertFalse(
            $matcher->match($email, $rule, $execution = new RuleExecution($rule, $ruleGroupExecution))
        );

        // for $barRuleCondition
        $notCalled->satisfy(Argument::any(), Argument::any())
            ->shouldNotHaveBeenCalled();

        self::assertCount(3, $evaluations = $execution->getEvaluations());

        self::assertSame('foo', $evaluations[0]->getCondition()->getType());
        self::assertSame(['parameter' => 1], $evaluations[0]->getCondition()->getSettings());
        self::assertTrue($evaluations[0]->isSatisfied());
        self::assertSame([], $evaluations[0]->getErrors());

        self::assertSame('foo', $evaluations[1]->getCondition()->getType());
        self::assertSame(['parameter' => '2'], $evaluations[1]->getCondition()->getSettings());
        self::assertFalse($evaluations[1]->isSatisfied());
        self::assertSame([], $evaluations[1]->getErrors());

        self::assertSame('bar', $evaluations[2]->getCondition()->getType());
        self::assertSame([], $evaluations[2]->getCondition()->getSettings());
        self::assertFalse($evaluations[2]->isSatisfied());
        self::assertSame([], $evaluations[2]->getErrors());
    }
}
