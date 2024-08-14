<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'presta_mail_receiver_rule')]
class Rule
{
    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';
    public const OPERATORS = [self::OPERATOR_AND, self::OPERATOR_OR];

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private $name;

    /**
     * @var Collection<int, RuleCondition>
     */
    #[ORM\OneToMany(
        mappedBy: 'rule',
        targetEntity: RuleCondition::class,
        cascade: ['persist', 'detach'],
        fetch: 'EAGER',
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    #[Assert\Count(min: 1, groups: ['RuleEdit'])]
    #[Assert\Valid]
    private $conditions;

    /**
     * @var string
     */
    #[ORM\Column(name: 'condition_operator', type: 'string', nullable: false)]
    private $conditionOperator = self::OPERATOR_AND;

    /**
     * @var Collection<int, RuleAction>
     */
    #[ORM\OneToMany(
        mappedBy: 'rule',
        targetEntity: RuleAction::class,
        cascade: ['persist', 'detach'],
        fetch: 'EAGER',
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    #[Assert\Count(min: 1, groups: ['RuleEdit'])]
    #[Assert\Valid]
    private $actions;

    public function __construct()
    {
        $this->conditions = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return RuleCondition[]
     */
    public function getConditions(): array
    {
        return $this->conditions->toArray();
    }

    public function addCondition(RuleCondition $condition): void
    {
        if (!$this->conditions->contains($condition)) {
            $condition->setRule($this);
            $this->conditions->add($condition);
        }
    }

    public function removeCondition(RuleCondition $condition): void
    {
        $this->conditions->removeElement($condition);
    }

    /**
     * @return RuleAction[]
     */
    public function getActions(): array
    {
        return $this->actions->toArray();
    }

    public function addAction(RuleAction $action): void
    {
        if (!$this->actions->contains($action)) {
            $action->setRule($this);
            $this->actions->add($action);
        }
    }

    public function removeAction(RuleAction $action): void
    {
        $this->actions->removeElement($action);
    }

    public function getConditionOperator(): string
    {
        return $this->conditionOperator;
    }

    public function setConditionOperator(string $operator): void
    {
        if (!in_array($operator, self::OPERATORS, true)) {
            throw new \UnexpectedValueException(sprintf(
                'Condition operator must one of %s. %s given.',
                json_encode(self::OPERATORS),
                $operator
            ));
        }

        $this->conditionOperator = $operator;
    }
}
