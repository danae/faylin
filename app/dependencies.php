<?php
use DI\ContainerBuilder;
use League\Flysystem\Filesystem;
use MongoDB\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;
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

use Danae\Faylin\App\Capabilities;
use Danae\Faylin\App\Authorization\AuthorizationMiddleware;
use Danae\Faylin\App\Authorization\Jwt\JwtAuthorizationContext;
use Danae\Faylin\App\Authorization\Jwt\JwtAuthorizationStrategy;
use Danae\Faylin\App\Controllers\AuthorizationController;
use Danae\Faylin\App\Controllers\BackendController;
use Danae\Faylin\App\Controllers\CollectionController;
use Danae\Faylin\App\Controllers\FrontendController;
use Danae\Faylin\App\Controllers\ImageController;
use Danae\Faylin\App\Controllers\UserController;
use Danae\Faylin\Implementation\Flysystem\ImageStore;
use Danae\Faylin\Implementation\Flysystem\ImageTransformExecutor;
use Danae\Faylin\Implementation\Flysystem\ImageTransformStore;
use Danae\Faylin\Implementation\MongoDB\CollectionRepository;
use Danae\Faylin\Implementation\MongoDB\ImageRepository;
use Danae\Faylin\Implementation\MongoDB\SessionRepository;
use Danae\Faylin\Implementation\MongoDB\UserRepository;
use Danae\Faylin\Model\CollectionRepositoryInterface;
use Danae\Faylin\Model\ImageRepositoryInterface;
use Danae\Faylin\Model\ImageStoreInterface;
use Danae\Faylin\Model\ImageTransformExecutorInterface;
use Danae\Faylin\Model\ImageTransformStoreInterface;
use Danae\Faylin\Model\SessionRepositoryInterface;
use Danae\Faylin\Model\SnowflakeGenerator;
use Danae\Faylin\Model\UserRepositoryInterface;


// Return a function that adds dependencies to the container
return function(ContainerBuilder $containerBuilder)
{
  // Add definitions to the container
  $containerBuilder->addDefinitions([
    // Capabilities
    Capabilities::class => DI\autowire()
      ->constructorParameter('supportedContentTypes', DI\get('app.supportedContentTypes'))
      ->constructorParameter('supportedSize', DI\get('app.supportedSize')),

    // MongoDB client
    Client::class => DI\autowire()
      ->constructorParameter('uri', DI\get('mongodb.uri')),

    // Serializer
    Serializer::class => DI\autowire()
      ->constructor([new DateTimeNormalizer(), new CustomNormalizer(), new PropertyNormalizer()], [new JsonEncoder()]),

    // Repositories
    CollectionRepositoryInterface::class => DI\autowire(CollectionRepository::class)
      ->constructorParameter('databaseName', DI\get('mongodb.database'))
      ->constructorParameter('collectionName', DI\get('mongodb.collection.collections')),
    ImageRepositoryInterface::class => DI\autowire(ImageRepository::class)
      ->constructorParameter('databaseName', DI\get('mongodb.database'))
      ->constructorParameter('collectionName', DI\get('mongodb.collection.images')),
    SessionRepositoryInterface::class => DI\autowire(SessionRepository::class)
      ->constructorParameter('databaseName', DI\get('mongodb.database'))
      ->constructorParameter('collectionName', DI\get('mongodb.collection.sessions')),
    UserRepositoryInterface::class => DI\autowire(UserRepository::class)
      ->constructorParameter('databaseName', DI\get('mongodb.database'))
      ->constructorParameter('collectionName', DI\get('mongodb.collection.users')),

    // Dependencies for stores
    StreamFactoryInterface::class => DI\autowire(StreamFactory::class),
    ResponseFactoryInterface::class => DI\autowire(ResponseFactory::class),

    // Stores
    Filesystem::class => DI\autowire()
      ->constructor(DI\get('store.adapter')),
    ImageStoreInterface::class => DI\autowire(ImageStore::class)
      ->constructorParameter('fileNameFormat', DI\get('store.imageFileNameFormat')),
    ImageTransformStoreInterface::class => DI\autowire(ImageTransformStore::class)
      ->constructorParameter('fileNameFormat', DI\get('store.imageTransformCacheFileNameFormat')),
    ImageTransformExecutorInterface::class => DI\autowire(ImageTransformExecutor::class),

    // Authorization
    JwtAuthorizationContext::class => DI\autowire()
      ->constructorParameter('key', DI\get('authorization.signKey'))
      ->constructorParameter('algorithm', 'HS256'),
    JwtAuthorizationStrategy::class => DI\autowire(),
    AuthorizationMiddleware::class => function(ContainerInterface $container) {
      return new AuthorizationMiddleware([$container->get(JwtAuthorizationStrategy::class)]);
    },

    // Snowflake generator
    SnowflakeGenerator::class => DI\autowire()
      ->property('datacenterId', DI\get('snowflake.datacenter'))
      ->property('workerId', DI\get('snowflake.worker'))
      ->property('epoch', DI\get('snowflake.epoch')),

    // Twig
    TwigLoaderInterface::class => DI\autowire(TwigFilesystemLoader::class)
      ->constructorParameter('paths', '/'),
    Twig::class => DI\autowire(),
    TwigMiddleware::class => function(App $app) {
      return TwigMiddleware::createFromContainer($app, Twig::class);
    },

    // Backend controllers
    AuthorizationController::class => DI\autowire()
      ->property('collectionRepository', DI\get(CollectionRepositoryInterface::class))
      ->property('imageRepository', DI\get(ImageRepositoryInterface::class))
      ->property('imageStore', DI\get(ImageStoreInterface::class))
      ->property('imageTransformExecutor', DI\get(ImageTransformExecutorInterface::class))
      ->property('imageTransformStore', DI\get(ImageTransformStoreInterface::class))
      ->property('sessionRepository', DI\get(SessionRepositoryInterface::class))
      ->property('userRepository', DI\get(UserRepositoryInterface::class))
      ->property('serializer', DI\get(Serializer::class))
      ->property('capabilities', DI\get(Capabilities::class))
      ->property('authorizationContext', DI\get(JwtAuthorizationContext::class)),
    BackendController::class => DI\autowire()
      ->property('collectionRepository', DI\get(CollectionRepositoryInterface::class))
      ->property('imageRepository', DI\get(ImageRepositoryInterface::class))
      ->property('imageStore', DI\get(ImageStoreInterface::class))
      ->property('imageTransformExecutor', DI\get(ImageTransformExecutorInterface::class))
      ->property('imageTransformStore', DI\get(ImageTransformStoreInterface::class))
      ->property('sessionRepository', DI\get(SessionRepositoryInterface::class))
      ->property('userRepository', DI\get(UserRepositoryInterface::class))
      ->property('serializer', DI\get(Serializer::class))
      ->property('capabilities', DI\get(Capabilities::class)),
    CollectionController::class => DI\autowire()
      ->property('collectionRepository', DI\get(CollectionRepositoryInterface::class))
      ->property('imageRepository', DI\get(ImageRepositoryInterface::class))
      ->property('imageStore', DI\get(ImageStoreInterface::class))
      ->property('imageTransformExecutor', DI\get(ImageTransformExecutorInterface::class))
      ->property('imageTransformStore', DI\get(ImageTransformStoreInterface::class))
      ->property('sessionRepository', DI\get(SessionRepositoryInterface::class))
      ->property('userRepository', DI\get(UserRepositoryInterface::class))
      ->property('serializer', DI\get(Serializer::class))
      ->property('capabilities', DI\get(Capabilities::class)),
    ImageController::class => DI\autowire()
      ->property('collectionRepository', DI\get(CollectionRepositoryInterface::class))
      ->property('imageRepository', DI\get(ImageRepositoryInterface::class))
      ->property('imageStore', DI\get(ImageStoreInterface::class))
      ->property('imageTransformExecutor', DI\get(ImageTransformExecutorInterface::class))
      ->property('imageTransformStore', DI\get(ImageTransformStoreInterface::class))
      ->property('sessionRepository', DI\get(SessionRepositoryInterface::class))
      ->property('userRepository', DI\get(UserRepositoryInterface::class))
      ->property('serializer', DI\get(Serializer::class))
      ->property('capabilities', DI\get(Capabilities::class)),
    UserController::class => DI\autowire()
      ->property('collectionRepository', DI\get(CollectionRepositoryInterface::class))
      ->property('imageRepository', DI\get(ImageRepositoryInterface::class))
      ->property('imageStore', DI\get(ImageStoreInterface::class))
      ->property('imageTransformExecutor', DI\get(ImageTransformExecutorInterface::class))
      ->property('imageTransformStore', DI\get(ImageTransformStoreInterface::class))
      ->property('sessionRepository', DI\get(SessionRepositoryInterface::class))
      ->property('userRepository', DI\get(UserRepositoryInterface::class))
      ->property('serializer', DI\get(Serializer::class))
      ->property('capabilities', DI\get(Capabilities::class)),

    // Frontend controllers
    FrontendController::class => DI\autowire()
      ->property('twig', DI\get(Twig::class)),
  ]);
};
