<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Condition\Sender;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;
use Presta\MailReceiverBundle\Rule\Condition\SenderMatch;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;

class SenderMatchConditionTest extends TestCase
{
    private function condition(): SenderMatch
    {
        $descriptor = new MatchSettingsDescriptor(Services::translator());

        return new SenderMatch(new MatchSettings(), new MatchSettingsConfigurator($descriptor), $descriptor);
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
        yield 'Sender equals string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_EQUALS, 'value' => 'yeugone@prestaconcept.net'],
        ];
        yield 'Sender contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'prestaconcept.net'],
        ];
        yield 'Sender match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '^[a-zA-Z0-9]+@prestaconcept\.net$'],
        ];
    }

    public function notSatisfy(): \Generator
    {
        yield 'Sender not equals string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_EQUALS, 'value' => 'john@prestaconcept.net'],
        ];
        yield 'Sender not contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'yahoo.fr'],
        ];
        yield 'Sender not match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '^[0-9]+@prestaconcept\.net$'],
        ];
    }
}
