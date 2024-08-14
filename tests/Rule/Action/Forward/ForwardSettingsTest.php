<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Action\Forward;

use Presta\MailReceiverBundle\Rule\Action\Forward;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;
use Presta\MailReceiverBundle\Tests\Rule\ComponentWithSettingsTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Mailer\MailerInterface;

class ForwardSettingsTest extends ComponentWithSettingsTestCase
{
    use ProphecyTrait;

    private function action(): Forward
    {
        return new Forward($this->prophesize(MailerInterface::class)->reveal(), Services::translator());
    }

    public function testDefaults(): void
    {
        self::assertSame(
            ['to' => null], //todo
            $this->action()->defaults()
        );
    }

    /**
     * @dataProvider validSettings
     */
    public function testValidSettings(array $settings): void
    {
        $form = $this->form($this->action());
        self::assertValidSettings($form, $settings);
    }

    /**
     * @dataProvider invalidSettings
     */
    public function testInvalidSettings(array $settings): void
    {
        $form = $this->form($this->action());
        self::assertInvalidSettings($form, $settings);
    }

    public function validSettings(): \Generator
    {
        yield '"to" accept standard email' => [['to' => 'yeugone@prestaconcept.net']];
        yield '"to" accept aliased email' => [['to' => 'yeugone+foo@prestaconcept.net']];
    }

    public function invalidSettings(): \Generator
    {
        yield '"to" is required' => [[]];
        yield '"to" cannot be null' => [['to' => null]];
        yield '"to" cannot be empty' => [['to' => '']];
        yield '"to" must be valid email' => [['to' => 'not an email']];
    }
}
