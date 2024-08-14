<?php

namespace Presta\MailReceiverBundle\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Rule\Action\RuleActionInterface;
use Presta\MailReceiverBundle\Rule\ActionNotRegisteredException;
use Presta\MailReceiverBundle\Rule\ActionRegistry;
use Prophecy\PhpUnit\ProphecyTrait;

class ActionRegistryTest extends TestCase
{
    use ProphecyTrait;

    public function testList(): void
    {
        $actions = new ActionRegistry([
            'foo' => $foo = $this->prophesize(RuleActionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleActionInterface::class)->reveal(),
        ]);

        self::assertSame(['foo', 'bar'], $actions->list(), 'ActionRegistry list every registered actions');
    }

    public function testGet(): void
    {
        $actions = new ActionRegistry([
            'foo' => $foo = $this->prophesize(RuleActionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleActionInterface::class)->reveal(),
        ]);

        self::assertSame($foo, $actions->get('foo'), 'ActionRegistry retrieved "foo" action');
        self::assertSame($bar, $actions->get('bar'), 'ActionRegistry retrieved "bar" action');
    }

    public function testGetNotFound(): void
    {
        $this->expectException(ActionNotRegisteredException::class);

        $actions = new ActionRegistry([
            'foo' => $foo = $this->prophesize(RuleActionInterface::class)->reveal(),
            'bar' => $bar = $this->prophesize(RuleActionInterface::class)->reveal(),
        ]);

        $actions->get('not found');
    }
}
