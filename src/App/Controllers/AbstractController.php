<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Danae\Faylin\Validator\Validator;


// Base class for controllers
abstract class AbstractController
{
  // The collection repository to use with the controller
  protected $collectionRepository;

  // The image repository to use with the controller
  protected $imageRepository;

  // The image store to use with the controller
  protected $imageStore;

  // The image transform executor to use with the controller
  protected $imageTransformExecutor;

  // The image transform store to use with the controller
  protected $imageTransformStore;

  // The session repository to use with the controller
  protected $sessionRepository;

  // The user repository to use with the controller
  protected $userRepository;

  // The serializer to use with the controller
  protected $serializer;

  // The capabilities to use with the controller
  protected $capabilities;


  // Return a response with the JSON-serialized data
  protected function serialize(Request $request, Response $response, $data): Response
  {
    $response->getBody()->write($this->serializer->serialize($data, 'json', $this->serializeContext($request, $response)));
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  // Return an array with a serialization context
  protected function serializeContext(Request $request, Response $response): array
  {
    return [
      'request' => $request,
      'response' => $response,
      'collectionRepository' => $this->collectionRepository,
      'imageRepository' => $this->imageRepository,
      'imageStore' => $this->imageStore,
      'imageTransformExecutor' => $this->imageTransformExecutor,
      'imageTransformStore' => $this->imageTransformStore,
      'sessionRepository' => $this->sessionRepository,
      'userRepository' => $this->userRepository,
      'capabilities' => $this->capabilities,
      'json_encode_options' => JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR,
    ];
  }


  // Create a select options array from a request
  protected function createSelectOptions(Request $request, array $defaults = []): array
  {
    // Create a new options array
    $options = [];

    // Get and validate the query parameters
    $query = (new Validator())
      ->withOptional('sort', 'string')
      ->withOptional('page', 'int:true|min:0', 0)
      ->withOptional('perPage', 'int:true|min:0')
      ->validate(array_merge($defaults, $request->getQueryParams()), ['allowExtraFields' => true])
      ->resultOrThrowBadRequest($request);

    // Check for sorting
    if ($query['sort'] !== null)
    {
      $options['sort'] = [];
      foreach (explode(', ', $query['sort']) as $sort)
      {
        if (strpos($sort, '-') !== false)
          $options['sort'][substr($sort, 1)] = -1;
        else
          $options['sort'][$sort] = 1;
      }
    }

    // Check for pagination
    if ($query['perPage'] !== null)
    {
      $options['skip'] = (int)$query['page'] * (int)$query['perPage'];
      $options['limit'] = (int)$query['perPage'];
    }

    // Return the options array
    return $options;
  }
}
