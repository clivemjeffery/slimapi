<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
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

    public function __construct(string $name, string $party)
    {
        $this->name = $name;
        $this->party = $party;
        
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
