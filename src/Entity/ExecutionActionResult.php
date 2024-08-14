<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use LogicException;
use Throwable;

#[ORM\Entity]
#[ORM\Table(name: 'presta_mail_receiver_execution_action_result')]
class ExecutionActionResult
{
    use NormalizeExceptionTrait;

    public const RESULT_SUCCESS = 'success';
    public const RESULT_FAILED = 'failed';
    public const RESULT_SKIPPED = 'skipped';

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
    #[ORM\ManyToOne(targetEntity: Execution::class, inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $execution;

    /**
     * @var string
     */
    #[ORM\Column(name: 'action_type', type: 'string', length: 100, nullable: false)]
    private $actionType;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(name: 'action_settings', type: 'json', nullable: false)]
    private $actionSettings = [];

    /**
     * @var string
     */
    #[ORM\Column(name: 'result', type: 'string', length: 50, nullable: false)]
    private $result;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(name: 'error', type: 'json', nullable: false)]
    private $error;

    public function __construct(
        Execution $execution,
        RuleAction $action,
        string $result,
        Throwable $error = null
    ) {
        if ($action->getType() === null) {
            throw new LogicException('Action has no type');
        }

        $this->execution = $execution;
        $this->actionType = $action->getType();
        $this->actionSettings = $action->getSettings();
        $this->result = $result;
        $this->error = $error ? $this->normalizeException($error) : [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExecution(): Execution
    {
        return $this->execution;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getActionSettings(): array
    {
        return $this->actionSettings;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @return array<string, mixed>
     */
    public function getError(): array
    {
        return $this->error;
    }
}
