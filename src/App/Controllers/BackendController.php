<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// Controller that defines routes for the backend root
final class BackendController extends AbstractController
{
  // Return the capabilities of the API
  public function capabilities(Request $request, Response $response)
  {
    // Create the body
    $body = [
      'supportedContentTypes' => $this->capabilities->supportedContentTypes,
      'supportedSize' => $this->capabilities->supportedSize,
    ];

    // Return the response
    return $this->serialize($request, $response, $body);
  }
}
