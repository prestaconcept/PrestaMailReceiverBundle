<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Presta\MailReceiverBundle\Model\ActionResult;
use Presta\MailReceiverBundle\Model\Evaluation;
use Presta\MailReceiverBundle\Model\RuleExecution;
use Presta\MailReceiverBundle\Repository\ExecutionRepository;

#[ORM\Entity(repositoryClass: ExecutionRepository::class)]
#[ORM\Table(name: 'presta_mail_receiver_execution')]
class Execution
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var Email
     */
    #[ORM\ManyToOne(targetEntity: Email::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $email;

    /**
     * @var Rule
     */
    #[ORM\ManyToOne(targetEntity: Rule::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $rule;

    /**
     * @var DateTimeImmutable
     */
    #[ORM\Column(name: 'date', type: 'datetime_immutable', nullable: false)]
    private $date;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'satisfied', type: 'boolean', nullable: false)]
    private $satisfied = false;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'performed', type: 'boolean', nullable: false)]
    private $performed = false;

    /**
     * @var Collection<int, ExecutionConditionEvaluation>
     */
    #[ORM\OneToMany(mappedBy: 'execution', targetEntity: ExecutionConditionEvaluation::class, cascade: ['persist'])]
    private $evaluations;

    /**
     * @var Collection<int, ExecutionActionResult>
     */
    #[ORM\OneToMany(mappedBy: 'execution', targetEntity: ExecutionActionResult::class, cascade: ['persist'])]
    private $results;

    /**
     * @param Evaluation[]   $evaluations
     * @param ActionResult[] $results
     */
    public function __construct(
        Email $email,
        Rule $rule,
        DateTimeImmutable $date,
        bool $performed,
        bool $satisfied,
        iterable $evaluations,
        iterable $results,
    ) {
        $this->email = $email;
        $this->rule = $rule;
        $this->date = $date;
        $this->performed = $performed;
        $this->satisfied = $satisfied;

        $this->evaluations = new ArrayCollection();
        foreach ($evaluations as $evaluation) {
            $this->evaluations->add(
                new ExecutionConditionEvaluation(
                    $this,
                    $evaluation->getCondition(),
                    $evaluation->isSatisfied(),
                    $evaluation->getErrors()
                ),
            );
        }

        $this->results = new ArrayCollection();
        foreach ($results as $result) {
            $this->results->add(
                new ExecutionActionResult(
                    $this,
                    $result->getAction(),
                    $result->getResult(),
                    $result->getError()
                ),
            );
        }
    }

    public static function fromModel(Email $email, DateTimeImmutable $date, RuleExecution $execution): self
    {
        return new self(
            $email,
            $execution->getRule(),
            $date,
            $execution->isPerformed(),
            $execution->isSatisfied(),
            $execution->getEvaluations(),
            $execution->getResults(),
        );
    }

    public function __toString(): string
    {
        return sprintf(
            '%s / %s - %s',
            $this->email,
            $this->rule,
            $this->date->format(DATE_ATOM),
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function isSatisfied(): bool
    {
        return $this->satisfied;
    }

    public function setSatisfied(): void
    {
        $this->satisfied = true;
    }

    public function isPerformed(): bool
    {
        return $this->performed;
    }

    /**
     * @return ExecutionConditionEvaluation[]
     */
    public function getEvaluations(): array
    {
        return $this->evaluations->toArray();
    }

    /**
     * @return ExecutionActionResult[]
     */
    public function getResults(): array
    {
        return $this->results->toArray();
    }
}
