<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Condition\Body;

use PHPUnit\Framework\TestCase;
use Presta\MailReceiverBundle\Entity\Email;
use Presta\MailReceiverBundle\Rule\Condition\BodyMatch;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;
use Presta\MailReceiverBundle\Tests\Fixtures\Emails;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;

class BodyMatchConditionTest extends TestCase
{
    private function condition(): BodyMatch
    {
        $descriptor = new MatchSettingsDescriptor(Services::translator());

        return new BodyMatch(new MatchSettings(), new MatchSettingsConfigurator($descriptor), $descriptor);
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
        yield 'Body equals string' => [
            Emails::cheerz(),
            [
                'operator' => MatchSettings::OPERATOR_EQUALS,
                'value' => <<<TXT
I hope you are all doing well.

Cheers,
Yann

TXT
    ,
            ],
        ];
        yield 'Body contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'hope'],
        ];
        yield 'Body match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '[a-zA-Z0-9]+(\s)?$'],
        ];
    }

    public function notSatisfy(): \Generator
    {
        yield 'Body not equals string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_EQUALS, 'value' => 'doing well'],
        ];
        yield 'Body not contains string' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => 'tartine'],
        ];
        yield 'Body not match regex' => [
            Emails::cheerz(),
            ['operator' => MatchSettings::OPERATOR_MATCH, 'value' => '^[0-9]+$'],
        ];
    }
}
