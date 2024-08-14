<?php

namespace Presta\MailReceiverBundle\Rule\Condition\Match;

use InvalidArgumentException;
use Presta\MailReceiverBundle\Rule\Condition\InvalidSettingsException;

final class MatchSettings
{
    public const OPERATOR_MATCH = 1;
    public const OPERATOR_CONTAINS = 2;
    public const OPERATOR_EQUALS = 3;

    private const OPERATORS = [self::OPERATOR_MATCH, self::OPERATOR_CONTAINS, self::OPERATOR_EQUALS];

    /**
     * @param array<string, mixed> $settings
     */
    public function matchSettings(array $settings, ?string $haystack): bool
    {
        $operator = $settings['operator'] ?? null;
        if ($operator === null) {
            throw InvalidSettingsException::missing('operator', $settings);
        }

        $value = $settings['value'] ?? null;
        if ($value === null) {
            throw InvalidSettingsException::missing('value', $settings);
        }

        if ($haystack === null) {
            return false;
        }

        return $this->match($operator, $value, $haystack);
    }

    public function match(int $operator, string $value, string $haystack): bool
    {
        $value = $this->stringNormalize($value);
        $haystack = $this->stringNormalize($haystack);
        switch ($operator) {
            case self::OPERATOR_MATCH:
                return (bool)preg_match(sprintf('{%s}', $value), $haystack);
            case self::OPERATOR_CONTAINS:
                return strpos($haystack, $value) !== false;
            case self::OPERATOR_EQUALS:
                return $haystack === $value;
        }

        throw new InvalidArgumentException(
            sprintf('Expecting that operator was one of %s. %d given.', json_encode(self::OPERATORS), $operator)
        );
    }

    private function stringNormalize(string $string): string
    {
        $table = [
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c',
            'Ć' => 'C', 'ć' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i',
            'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R',
            'ŕ' => 'r',
        ];

        return strtolower(strtr($string, $table));
    }
}
