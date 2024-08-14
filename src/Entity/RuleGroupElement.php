<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'presta_mail_receiver_rule_group_element')]
class RuleGroupElement
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var Rule|null
     */
    #[ORM\ManyToOne(targetEntity: Rule::class)]
    private $rule;

    /**
     * @var RuleGroup
     */
    #[ORM\ManyToOne(targetEntity: RuleGroup::class, inversedBy: 'rulesElements')]
    private $group;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'sort_order', type: 'integer', nullable: false)]
    private $sortOrder;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'breakpoint', type: 'boolean', nullable: false)]
    private $breakpoint;

    public function __construct(Rule $rule, RuleGroup $group)
    {
        $this->rule = $rule;
        $this->group = $group;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setGroup(RuleGroup $group): void
    {
        $this->group = $group;
    }

    public function setRule(?Rule $rule): void
    {
        $this->rule = $rule;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function isBreakpoint(): ?bool
    {
        return $this->breakpoint;
    }

    public function setBreakpoint(bool $breakpoint): void
    {
        $this->breakpoint = $breakpoint;
    }

    public function getGroup(): RuleGroup
    {
        return $this->group;
    }
}
