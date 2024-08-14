<?php

namespace Presta\MailReceiverBundle\Tests\Validator\Constraints;

use Presta\MailReceiverBundle\Validator\Constraints\ValidRegex;
use Presta\MailReceiverBundle\Validator\Constraints\ValidRegexValidator;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidRegexValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ValidRegexValidator
    {
        return new ValidRegexValidator();
    }

    /**
     * @dataProvider valid
     */
    public function testRegexIsValid(?string $value): void
    {
        $this->validator->validate($value, new ValidRegex());
        $this->assertNoViolation();
    }

    public function testRegexIsNotValid(): void
    {
        $this->validator->validate('{0-9]+', new ValidRegex());

        $this->buildViolation('This regex is not valid.')
            ->setParameter('{{ value }}', '"{0-9]+"')
            ->setCode(ValidRegex::IS_INVALID_ERROR)
            ->assertRaised();
    }

    public function testUnexpectedConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('', new NotNull());
    }

    /**
     * @dataProvider unexpectedValue
     */
    public function testUnexpectedValue($value): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate($value, new ValidRegex());
    }

    public function valid(): \Generator
    {
        yield 'Plain text is valid regex' => ['hello'];
        yield 'Regex without start nor end is valid regex' => ['[a-z]+'];
        yield 'Regex with start and end is valid regex' => ['^[0-9]{2}/[0-9]{2}/[0-9]{4}$'];
    }

    public function unexpectedValue(): \Generator
    {
        yield 'DateTime objects are not regex' => [new \DateTime('2019-01-01')];
        yield 'Arrays are not regex' => [[]];
        yield 'Integers are not regex' => [1];
        yield 'Doubles are not regex' => [1.23];
        yield 'Booleans are not regex' => [true];
    }
}
