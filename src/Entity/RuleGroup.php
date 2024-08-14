<?php

declare(strict_types=1);

namespace Presta\MailReceiverBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Presta\MailReceiverBundle\Repository\RuleGroupRepository;

#[ORM\Entity(repositoryClass: RuleGroupRepository::class)]
#[ORM\Table(name: 'presta_mail_receiver_rule_group')]
class RuleGroup
{
    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var null|string
     */
    #[ORM\Column(name: 'name', type: 'string', nullable: false)]
    private $name;

    /**
     * @var Collection<int, RuleGroupElement>
     */
    #[ORM\OneToMany(
        mappedBy: 'group',
        targetEntity: RuleGroupElement::class,
        cascade: ['persist', 'detach'],
        fetch: 'EAGER',
        orphanRemoval: true,
    )]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private $rulesElements;

    public function __construct()
    {
        $this->rulesElements = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, RuleGroupElement>
     */
    public function getRulesElements(): Collection
    {
        return $this->rulesElements;
    }

    public function addRulesElement(RuleGroupElement $element): void
    {
        if (!$this->rulesElements->contains($element)) {
            $this->rulesElements->add($element);
            $element->setGroup($this);
        }
    }

    public function removeRulesElement(RuleGroupElement $element): void
    {
        if ($this->rulesElements->contains($element)) {
            $this->rulesElements->removeElement($element);
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
