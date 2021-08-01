<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\ImageRepository;
use Danae\Faylin\Model\UserRepository;
use Danae\Faylin\Utils\Snowflake;
use Danae\Faylin\Validator\Validator;


// Base class for controllers
abstract class AbstractController
{
  // The image repository to use with the controller
  protected $imageRepository;

  // The user repository to use with the controller
  protected $userRepository;

  // The serializer to use with the controller
  protected $serializer;

  // The supported content types for uploaded files
  protected $supportedContentTypes;

  // The supported size for uploaded files
  protected $supportedSize;


  // Constructor
  public function __construct(ImageRepository $imageRepository, UserRepository $userRepository, Serializer $serializer)
  {
    $this->imageRepository = $imageRepository;
    $this->userRepository = $userRepository;
    $this->serializer = $serializer;
  }

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
      'imageRepository' => $this->imageRepository,
      'userRepository' => $this->userRepository,
      'supportedContentTypes' => $this->supportedContentTypes,
      'supportedSize' => $this->supportedSize,
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
      ->validate(array_merge($defaults, $request->getQueryParams()))
      ->resultOrThrowBadRequest($request);

    // Check for sorting
    if ($query['sort'] !== null)
    {
      $options['order_by'] = [];
      foreach (explode(', ', $query['sort']) as $sort)
      {
        if (strpos($sort, '-') !== false)
          $options['order_by'][substr($sort, 1)] = 'desc';
        else
          $options['order_by'][$sort] = 'asc';
      }
    }

    // Check for pagination
    if ($query['perPage'] !== null)
    {
      $options['offset'] = $query['page'] * $query['perPage'];
      $options['limit'] = $query['perPage'];
    }

    // Return the options array
    return $options;
  }
}
