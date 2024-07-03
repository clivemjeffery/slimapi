<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;

#[Entity, Table(name: 'candidates')]
final readonly class Candidate implements JsonSerializable
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $name;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $party;

    #[OneToMany(targetEntity: Vote::class, mappedBy: 'candidate', cascade: ['persist'])]
    private Collection $votes;

    public function __construct(string $name, string $party)
    {
        $this->name = $name;
        $this->party = $party;
        $this->votes = new ArrayCollection();
    }

    public function receiveVote() : void
    {
        $vote = new Vote;
        $vote->setCandidate($this);
        $this->votes->add($vote);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParty(): string
    {
        return $this->party;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'party' => $this->getParty()
        ];
    }
}
