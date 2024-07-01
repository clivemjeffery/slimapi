<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\Action;

use Doctrine\ORM\EntityManager;
use Nyholm\Psr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\DoctrineDemo\Domain\Candidate;
use function json_encode;

final readonly class ListCandidates implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Candidate[] $canidates */
        $candidates = $this->em
            ->getRepository(Candidate::class)
            ->findAll();

        $body = Psr7\Stream::create(json_encode($candidates, JSON_PRETTY_PRINT) . PHP_EOL);

        return new Psr7\Response(200, ['Content-Type' => 'application/json'], $body);
    }
}
