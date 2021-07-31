<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;


// Controller that defines routes for the frontend root
final class FrontendController extends AbstractController
{
  // The Twig renderer to use with the controller
  protected $twig;


  // Render the index template
  public function render(Request $request, Response $response)
  {
    return $this->twig->render($response, 'index.twig');
  }
}
