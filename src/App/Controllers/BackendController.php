<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// Controller that defines routes for the backend root
final class BackendController extends AbstractController
{
  // The supported content types for uploaded files
  protected $supportedContentTypes;

  // The supported size for uploaded files
  protected $supportedSize;


  // Return the capabilities of the API
  public function capabilities(Request $request, Response $response)
  {
    // Create the body
    $body = [
      'supportedContentTypes' => $this->supportedContentTypes,
      'supportedSize' => $this->supportedSize,
    ];

    // Return the response
    return $this->serialize($request, $response, $body);
  }
}
