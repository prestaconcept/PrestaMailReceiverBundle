<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Model;

use LogicException;
use Presta\MailReceiverBundle\Entity\NormalizeExceptionTrait;
use Presta\MailReceiverBundle\Entity\RuleCondition;
use Throwable;

class Evaluation
{
    use NormalizeExceptionTrait;

    /**
     * @var array<string, mixed>
     */
    private array $errors;

    public function __construct(
        private RuleExecution $execution,
        private RuleCondition $condition,
        private bool $satisfied,
        Throwable $exception = null
    ) {
        if ($condition->getType() === null) {
            throw new LogicException('Condition has no type');
        }

        $this->errors = $exception ? $this->normalizeException($exception) : [];
    }

    public function getExecution(): RuleExecution
    {
        return $this->execution;
    }

    public function isSatisfied(): bool
    {
        return $this->satisfied;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getCondition(): RuleCondition
    {
        return $this->condition;
    }

    public function setCondition(RuleCondition $condition): void
    {
        $this->condition = $condition;
    }
}
