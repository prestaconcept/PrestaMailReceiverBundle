<?php

namespace Presta\MailReceiverBundle\Rule\Condition\Match;

use Presta\MailReceiverBundle\Rule\Condition\InvalidSettingsException;
use Presta\MailReceiverBundle\Rule\Condition\Match\MatchSettings;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MatchSettingsDescriptor
{
    private const OPERATOR_MAP = [
        MatchSettings::OPERATOR_MATCH => 'match',
        MatchSettings::OPERATOR_CONTAINS => 'contains',
        MatchSettings::OPERATOR_EQUALS => 'equals',
    ];

    public function __construct(private TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function settings(string $id, array $settings): string
    {
        $operator = $settings['operator'] ?? null;
        if ($operator === null) {
            throw InvalidSettingsException::missing('operator', $settings);
        }

        $value = $settings['value'] ?? null;
        if ($value === null) {
            throw InvalidSettingsException::missing('value', $settings);
        }

        return $this->translator->trans(
            $id,
            ['%value%' => $value, '%operator%' => $this->operator($operator)],
            'PrestaMailReceiverBundle'
        );
    }

    public function operator(int $operator): string
    {
        return $this->translator->trans(
            sprintf('rule.description.conditions.operator.%s', self::OPERATOR_MAP[$operator] ?? 'unknown'),
            [],
            'PrestaMailReceiverBundle'
        );
    }
}
