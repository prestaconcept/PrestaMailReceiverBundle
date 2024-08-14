<?php

namespace Presta\MailReceiverBundle\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Rule\Condition\RuleConditionInterface;
use Presta\MailReceiverBundle\Rule\ConditionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ConditionRegistry;
use Prophecy\PhpUnit\ProphecyTrait;

class ConditionRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function testList(): void
    {
        $conditions = new ConditionRegistry([
            'foo' => $foo = $this->prophesize(RuleConditionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleConditionInterface::class)->reveal(),
        ]);

        self::assertSame(['foo', 'bar'], $conditions->list(), 'ConditionRegistry list every registered conditions');
    }

    public function testGet(): void
    {
        $conditions = new ConditionRegistry([
            'foo' => $foo = $this->prophesize(RuleConditionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleConditionInterface::class)->reveal(),
        ]);

        self::assertSame($foo, $conditions->get('foo'), 'ConditionRegistry retrieved "foo" condition');
        self::assertSame($bar, $conditions->get('bar'), 'ConditionRegistry retrieved "bar" condition');
    }

    public function testGetNotFound(): void
    {
        $this->expectException(ConditionNotRegisteredException::class);

        $conditions = new ConditionRegistry([
            'foo' => $foo = $this->prophesize(RuleConditionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleConditionInterface::class)->reveal(),
        ]);

        $conditions->get('not found');
    }
}
