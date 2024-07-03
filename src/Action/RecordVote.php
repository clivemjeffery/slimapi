<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\Action;

use Doctrine\ORM\EntityManager;
use Nyholm\Psr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\DoctrineDemo\Domain\Candidate;
use Slim\Routing\RouteContext;
use function json_encode;

final readonly class RecordVote implements RequestHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // CJ: this was the only way I found to get the id from the route
        // in this pretty complex way of setting them up

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $id = $route->getArgument('id');
        
        $candidate = $this->em
            ->getRepository(Candidate::class)
            ->find($id );

        $candidate->receiveVote();
        $this->em->persist($candidate);
        $this->em->flush();

        $body = Psr7\Stream::create(json_encode($candidate, JSON_PRETTY_PRINT) . PHP_EOL);

        return new Psr7\Response(201, ['Content-Type' => 'application/json'], $body);
    }
}
