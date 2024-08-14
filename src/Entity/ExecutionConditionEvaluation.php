<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LogicException;

#[ORM\Entity]
#[ORM\Table(name: 'presta_mail_receiver_execution_condition_evaluation')]
class ExecutionConditionEvaluation
{
    use NormalizeExceptionTrait;

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var Execution
     */
    #[ORM\ManyToOne(targetEntity: Execution::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $execution;

    /**
     * @var string
     */
    #[ORM\Column(name: 'condition_type', type: 'string', length: 100, nullable: false)]
    private $conditionType;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(name: 'condition_settings', type: 'json', nullable: false)]
    private $conditionSettings = [];

    /**
     * @var bool
     */
    #[ORM\Column(name: 'satisfied', type: 'boolean', nullable: false)]
    private $satisfied;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(name: 'error', type: 'json', nullable: false)]
    private $error;

    /**
     * @param array<string, mixed> $error
     */
    public function __construct(
        Execution $execution,
        RuleCondition $condition,
        bool $satisfied,
        array $error
    ) {
        if ($condition->getType() === null) {
            throw new LogicException('Condition has no type');
        }

        $this->execution = $execution;
        $this->conditionType = $condition->getType();
        $this->conditionSettings = $condition->getSettings();
        $this->satisfied = $satisfied;
        $this->error = $error;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExecution(): Execution
    {
        return $this->execution;
    }

    public function getConditionType(): string
    {
        return $this->conditionType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConditionSettings(): array
    {
        return $this->conditionSettings;
    }

    public function isSatisfied(): bool
    {
        return $this->satisfied;
    }

    /**
     * @return array<string, mixed>
     */
    public function getError(): array
    {
        return $this->error;
    }
}
