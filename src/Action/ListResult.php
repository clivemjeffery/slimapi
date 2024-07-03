<?php

// select c.*, count(v.id) from candidates AS c JOIN votes AS v ON (c.id = v.candidate_id) GROUP BY c.id ORDER BY count(v.id) DESC;


declare(strict_types=1);

namespace UMA\DoctrineDemo\Action;

use Doctrine\ORM\EntityManager;
use Nyholm\Psr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\DoctrineDemo\Domain\Candidate;
use function json_encode;

final readonly class ListResult implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query = $this->em->createQuery('SELECT c.name, c.party, count(v.id) as polled FROM UMA\DoctrineDemo\Domain\Candidate c JOIN c.votes v GROUP BY c.id ORDER BY count(v.id) DESC');
        $result = $query->getResult();

        $body = Psr7\Stream::create(json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL);

        return new Psr7\Response(200, ['Content-Type' => 'application/json'], $body);
    }
}
