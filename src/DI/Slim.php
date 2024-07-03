<?php

declare(strict_types=1);

namespace UMA\DoctrineDemo\DI;

use Doctrine\ORM\EntityManager;
use Faker;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ContentLengthMiddleware;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;
use UMA\DoctrineDemo\Action\CreateUser;
use UMA\DoctrineDemo\Action\ListUsers;
use UMA\DoctrineDemo\Action\ListCandidates;
use UMA\DoctrineDemo\Action\ListResult;
use UMA\DoctrineDemo\Action\RecordVote;


/**
 * A ServiceProvider for registering services related
 * to Slim such as request handlers, routing and the
 * App service itself that wires everything together.
 */
final readonly class Slim implements ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function provide(Container $c): void
    {
        $c->set(ListUsers::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new ListUsers(
                $c->get(EntityManager::class)
            );
        });

        $c->set(CreateUser::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new CreateUser(
                $c->get(EntityManager::class),
                Faker\Factory::create()
            );
        });

        $c->set(RecordVote::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new RecordVote(
                $c->get(EntityManager::class)
            );
        });

        $c->set(ListCandidates::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new ListCandidates(
                $c->get(EntityManager::class)
            );
        });

        $c->set(ListResult::class, static function(ContainerInterface $c): RequestHandlerInterface {
            return new ListResult(
                $c->get(EntityManager::class)
            );
        });

        $c->set(App::class, static function (ContainerInterface $c): App {
            /** @var array $settings */
            $settings = $c->get('settings');

            $app = AppFactory::create(null, $c);

            $app->addErrorMiddleware(
                $settings['slim']['displayErrorDetails'],
                $settings['slim']['logErrors'],
                $settings['slim']['logErrorDetails']
            );
            

            $app->add(new ContentLengthMiddleware());

            $app->get('/users', ListUsers::class);
            $app->get('/candidates', ListCandidates::class);
            $app->get('/result', ListResult::class);
            $app->post('/users', CreateUser::class);
            
            // can't find how to read the id from inside RecordVote
            // why is that so hard?
            $app->put('/vote/{id}', RecordVote::class);
            
            // shows a simpler way to tset the callanle and get args
            $app->get('/hello/{name}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
                $name = $args['name'];
                $response->getBody()->write("Hello, $name");
                return $response;
            });
            
            return $app;
        });
    }
}
