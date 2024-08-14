<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Condition\HasAttachments;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Condition\HasAttachments;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;

class HasAttachmentsConditionTests extends TestCase
{
    private function condition(): HasAttachments
    {
        return new HasAttachments(Services::translator());
    }

    /**
     * @dataProvider satisfy
     */
    public function testSatisfy(Email $email, array $settings = []): void
    {
        self::assertTrue(
            $this->condition()->satisfy($email, $settings),
            'Condition is satisfied'
        );
    }

    /**
     * @dataProvider notSatisfy
     */
    public function testNotSatisfy(Email $email, array $settings = []): void
    {
        self::assertFalse(
            $this->condition()->satisfy($email, $settings),
            'Condition is not satisfied'
        );
    }

    public function satisfy(): \Generator
    {
        yield 'Satisfy static strings' => [Emails::cuteCatPicture(), []];
    }

    public function notSatisfy(): \Generator
    {
        yield 'Not satisfy static strings' => [Emails::cheerz(), []];
    }
}
