<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Action\Forward;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Action\Forward;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Mailer\MailerInterface;

class ForwardActionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy|MailerInterface
     */
    private $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailer = $this->prophesize(MailerInterface::class);
    }

    private function action(): Forward
    {
        return new Forward($this->mailer->reveal(), Services::translator());
    }

    /**
     * @dataProvider handle
     */
    public function testHandle(Email $email, array $settings): void
    {
        $this->action()->handle($email, $settings);
        //todo assertions
        self::assertTrue(true);
    }

    public function handle(): \Generator
    {
        yield '' => [Emails::cheerz(), ['to' => 'john@acme.org']];//todo
    }
}
