<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Condition\Subject;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;
use Presta\MailReceiverBundle\Rule\Condition\SubjectMatch;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;

class SubjectMatchConditionTest extends TestCase
{
    private function condition(): SubjectMatch
    {
        $descriptor = new MatchSettingsDescriptor(Services::translator());

        return new SubjectMatch(new MatchSettings(), new MatchSettingsConfigurator($descriptor), $descriptor);
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
        yield 'Subject equals string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_EQUALS, 'value' => 'Dear John'],
        ];
        yield 'Subject contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'Dear'],
        ];
        yield 'Subject match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '^Dear [a-zA-Z0-9]+$'],
        ];
    }

    public function notSatisfy(): \Generator
    {
        yield 'Subject not equals string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_EQUALS, 'value' => 'Dear Yann'],
        ];
        yield 'Subject not contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'Yann'],
        ];
        yield 'Subject not match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '^Dear [0-9]*$'],
        ];
    }
}
