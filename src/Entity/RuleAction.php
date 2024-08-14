<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'presta_mail_receiver_rule_action')]
class RuleAction
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
    #[ORM\ManyToOne(targetEntity: Rule::class, inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private $rule;

    /**
     * @var int
     */
    #[ORM\Column(name: 'sort_order', type: 'integer', nullable: false)]
    #[Assert\NotNull]
    #[Assert\GreaterThanOrEqual(0)]
    private $sortOrder = 0;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'type', type: 'string', length: 100, nullable: false)]
    #[Assert\NotNull]
    private $type;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(name: 'settings', type: 'json', nullable: false)]
    #[Assert\NotNull]
    private $settings = [];

    /**
     * @var bool
     */
    #[ORM\Column(name: 'breakpoint', type: 'boolean', nullable: false)]
    private $breakpoint = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setRule(?Rule $rule): void
    {
        $this->rule = $rule;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function isBreakpoint(): bool
    {
        return $this->breakpoint;
    }

    public function setBreakpoint(bool $breakpoint): void
    {
        $this->breakpoint = $breakpoint;
    }
}
