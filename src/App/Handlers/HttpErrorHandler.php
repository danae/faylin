<?php
namespace Danae\Faylin\App\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler;
use Slim\Views\Twig;
use Symfony\Component\Serializer\Serializer;


// Class that defines an error handler for HTTP errors
final class HttpErrorHandler extends ErrorHandler
{
  // Constants for error types
  public const HTTP_400_BAD_REQUEST = 'BAD_REQUEST';
  public const HTTP_401_UNAUTHORIZED = 'UNAUTHORIZED';
  public const HTTP_403_FORBIDDEN = 'FORBIDDEN';
  public const HTTP_404_NOT_FOUND = 'NOT_FOUND';
  public const HTTP_405_METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
  public const HTTP_413_PAYLOAD_TOO_LARGE = 'PAYLOAD_TOO_LARGE';
  public const HTTP_415_UNSUPPORTED_MEDIA_TYPE = 'UNSUPPORTED_MEDIA_TYPE';
  public const HTTP_500_SERVER_ERROR = 'SERVER_ERROR';


  // Return a resopnse with an error message
  public function respond(): Response
  {
    $exception = $this->exception;

    $errorStatusCode = 500;
    $error = ['type' => self::HTTP_500_SERVER_ERROR, 'description' => 'An internal error has occurred while processing your request'];

    // Handle HTTP exceptions
    if ($exception instanceof HttpException)
    {
      $errorStatusCode = $exception->getCode();
      $error['description'] = $exception->getMessage();

      if ($exception instanceof HttpBadRequestException)
        $error['type'] = self::HTTP_400_BAD_REQUEST;
      else if ($exception instanceof HttpUnauthorizedException)
        $error['type'] = self::HTTP_401_UNAUTHORIZED;
      else if ($exception instanceof HttpForbiddenException)
        $error['type'] = self::HTTP_403_FORBIDDEN;
      else if ($exception instanceof HttpNotFoundException)
        $error['type'] = self::HTTP_404_NOT_FOUND;
      else if ($exception instanceof HttpMethodNotAllowedException)
        $error['type'] = self::HTTP_405_METHOD_NOT_ALLOWED;
      else if ($exception->getCode() === 413)
        $error['type'] = self::HTTP_413_PAYLOAD_TOO_LARGE;
      else if ($exception->getCode() === 415)
        $error['type'] = self::HTTP_415_UNSUPPORTED_MEDIA_TYPE;
    }

    // Handle other exceptions
    else if ($this->displayErrorDetails)
    {
      $error['description'] = $exception->getMessage();
      $error['file'] = $exception->getFile();
      $error['line'] = $exception->getLine();
    }

    // Create the body
    $body = ['error' => $error];

    // Create and return the response
    $response = $this->responseFactory->createResponse();
    $response->getBody()->write(json_encode($body, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    return $response
      ->withStatus($errorStatusCode)
      ->withHeader('Content-Type', 'application/json');
  }
}
