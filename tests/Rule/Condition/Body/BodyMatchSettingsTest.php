<?php

namespace Presta\MailReceiverBundle\Tests\Rule\Condition\Body;

use Presta\MailReceiverBundle\Rule\Condition\BodyMatch;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsConfigurator;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettingsDescriptor;
use Presta\MailReceiverBundle\Tests\Fixtures\Services;
use Presta\MailReceiverBundle\Tests\Rule\ComponentWithSettingsTestCase;

class BodyMatchSettingsTest extends ComponentWithSettingsTestCase
{
    private function condition(): BodyMatch
    {
        $descriptor = new MatchSettingsDescriptor(Services::translator());

        return new BodyMatch(new MatchSettings(), new MatchSettingsConfigurator($descriptor), $descriptor);
    }

    public function testDefaults(): void
    {
        self::assertSame(
            MatchSettingsConfigurator::DEFAULTS,
            $this->condition()->defaults()
        );
    }

    /**
     * @dataProvider validSettings
     */
    public function testValidSettings(array $settings): void
    {
        $form = $this->form($this->condition());
        self::assertValidSettings($form, $settings);
    }

    /**
     * @dataProvider invalidSettings
     */
    public function testInvalidSettings(array $settings): void
    {
        $form = $this->form($this->condition());
        self::assertInvalidSettings($form, $settings);
    }

    public function validSettings(): \Generator
    {
        yield '"value" can be just text if "operator" is contains' => [
            [
                'operator' => MatchSettings::OPERATOR_CONTAINS,
                'value' => 'abc',
            ],
        ];
        yield '"value" can be just text if "operator" is equals' => [
            [
                'operator' => MatchSettings::OPERATOR_EQUALS,
                'value' => 'abc',
            ],
        ];
        yield '"value" can be regexp' => [
            [
                'operator' => MatchSettings::OPERATOR_MATCH,
                'value' => '[0-9]{2}/[0-9]{2}/[0-9]{4}',
            ],
        ];
    }

    public function invalidSettings(): \Generator
    {
        yield 'both "operator" and "value" are required' => [[]];
        yield 'nor "operator" nor "value" can be null' => [['operator' => null, 'value' => null]];
        yield 'nor "operator" nor "value" can be empty' => [['operator' => '', 'value' => '']];
        yield '"value" must be valid regexp' => [['operator' => MatchSettings::OPERATOR_CONTAINS, 'value' => '[0-9}']];
        yield '"operator" must one in a list' => [['operator' => 0, 'value' => 'abc']];
    }
}
