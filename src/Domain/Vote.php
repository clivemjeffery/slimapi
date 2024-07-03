<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\Domain;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity, Table(name: 'votes')]
final readonly class Vote
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ManyToOne(targetEntity: Candidate::class, cascade: ["all"], fetch: "EXTRA_LAZY")]
    private Candidate $candidate;

    public function setCandidate(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    public function getCandidate() : Candidate
    {
        return $this->candidate;
    }
}