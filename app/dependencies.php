<?php
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use League\Flysystem\Filesystem;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant as AuthorizationPasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant as AuthorizationRefreshTokenGrant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\App;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Twig\Loader\FilesystemLoader as TwigFilesystemLoader;
use Twig\Loader\LoaderInterface as TwigLoaderInterface;

use Danae\Faylin\App\Controllers\AuthorizationController;
use Danae\Faylin\App\Controllers\BackendController;
use Danae\Faylin\App\Controllers\FrontendController;
use Danae\Faylin\App\Controllers\ImageController;
use Danae\Faylin\App\Controllers\UserController;
use Danae\Faylin\Model\ClientRepository;
use Danae\Faylin\Model\ImageRepository;
use Danae\Faylin\Model\UserRepository;
use Danae\Faylin\Utils\Snowflake;


// Return a function that adds dependencies to the container
return function(ContainerBuilder $containerBuilder)
{
  // Add definitions to the container
  $containerBuilder->addDefinitions([
    // Database connection
    Connection::class => function(ContainerInterface $container) {
      return DriverManager::getConnection([
        'url' => $container->get('database.url')
      ]);
    },

    // Filesystem
    Filesystem::class => DI\autowire()
      ->constructor(DI\get('filesystem.adapter')),

    // Serializer
    Serializer::class => DI\autowire()
      ->constructor([new DateTimeNormalizer(), new CustomNormalizer(), new PropertyNormalizer()], [new JsonEncoder()]),

    // Snowflake generator
    Snowflake::class => DI\autowire()
      ->constructor(DI\get('snowflake.datacenter'), DI\get('snowflake.worker'), DI\get('snowflake.epoch')),

    // Dependencies for repositories
    StreamFactoryInterface::class => DI\autowire(StreamFactory::class),

    // Repositories
    ClientRepository::class => DI\autowire()
      ->constructorParameter('table', DI\get('database.table.clients'))
      ->method('create'),
    ImageRepository::class => DI\autowire()
      ->constructorParameter('table', DI\get('database.table.images'))
      ->method('create'),
    UserRepository::class => DI\autowire()
      ->constructorParameter('table', DI\get('database.table.users'))
      ->method('create'),

    // Authorization server and grants
    AuthorizationServer::class => DI\autowire()
      ->constructorParameter('privateKey', DI\get('authorization.privateKey'))
      ->constructorParameter('encryptionKey', DI\get('authorization.encryptionKey'))
      ->method('enableGrantType', DI\get(AuthorizationPasswordGrant::class), DI\get('authorization.accessTokenTTL'))
      ->method('enableGrantType', DI\get(AuthorizationRefreshTokenGrant::class), DI\get('authorization.accessTokenTTL')),
    AuthorizationPasswordGrant::class => DI\autowire()
      ->method('setRefreshTokenTTL', DI\get('authorization.refreshTokenTTL')),
    AuthorizationRefreshTokenGrant::class => DI\autowire()
      ->method('setRefreshTokenTTL', DI\get('authorization.refreshTokenTTL')),

    // Twig
    TwigLoaderInterface::class => DI\autowire(TwigFilesystemLoader::class)
      ->constructorParameter('paths', '/'),
    Twig::class => DI\autowire(),
    TwigMiddleware::class => function(App $app) {
      return TwigMiddleware::createFromContainer($app, Twig::class);
    },

    // Backend controllers
    AuthorizationController::class => DI\autowire()
      ->property('authorizationServer', DI\get(AuthorizationServer::class)),
    BackendController::class => DI\autowire()
      ->property('supportedContentTypes', DI\get('uploads.supportedContentTypes'))
      ->property('supportedSize', DI\get('uploads.supportedSize')),
    ImageController::class => DI\autowire()
      ->property('supportedContentTypes', DI\get('uploads.supportedContentTypes'))
      ->property('supportedSize', DI\get('uploads.supportedSize')),
    UserController::class => DI\autowire(),

    // Frontend controllers
    FrontendController::class => DI\autowire()
      ->property('twig', DI\get(Twig::class)),
  ]);
};
