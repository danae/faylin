<?php
namespace Danae\Faylin\App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Danae\Faylin\Model\Collection;
use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\User;
use Danae\Faylin\Utils\Traits\RouteContextTrait;


// Controller that defines routes for the frontend root
final class FrontendController
{
  use RouteContextTrait;


  // The Twig renderer to use with the controller
  protected $twig;


  // Render the index template
  public function render(Request $request, Response $response)
  {
    return $this->twig->render($response, 'index.twig');
  }

  // Render a collection template
  public function renderCollection(Request $request, Response $response, Collection $collection)
  {
    // Create the render context
    $context = [];

    // Set the title of the collection page
    $context['title'] = "{$collection->getTitle()} by {$collection->getUser()->getTitle()}";

    // Set the OpenGraph tags of the collection
    $context['opengraph'] = [];
    $context['opengraph']['og:title'] = $collection->getTitle();
    $context['opengraph']['og:description'] = $collection->getDescription();
    $context['opengraph']['og:type'] = 'website';
    $context['opengraph']['og:url'] = $this->fullUrlFor($request, 'frontend.collection', ['collectionId' => $collection->getId()->toString()]);
    $context['opengraph']['og:site_name'] = 'fayl.in';

    if (!empty($collection->getImages()))
    {
      $context['opengraph']['og:type'] = 'image';
      $context['opengraph']['og:image'] = $this->fullUrlFor($request, 'images.download', ['imageName' => $collection->getImages()[0]->getName()]);
      $context['opengraph']['og:image:type'] = $collection->getImages()[0]->getContentType();
      $context['opengraph']['og:image:alt'] = $collection->getTitle();
    }

    // Render the frontend
    return $this->twig->render($response, 'index.twig', $context);
  }

  // Render an image template
  public function renderImage(Request $request, Response $response, Image $image)
  {
    // Create the render context
    $context = [];

    // Set the title of the collection page
    $context['title'] = "{$image->getTitle()} by {$image->getUser()->getTitle()}";

    // Set the OpenGraph tags of the collection
    $context['opengraph'] = [];
    $context['opengraph']['og:title'] = $image->getTitle();
    $context['opengraph']['og:description'] = $image->getDescription();
    $context['opengraph']['og:type'] = 'image';
    $context['opengraph']['og:url'] = $this->fullUrlFor($request, 'frontend.image', ['imageId' => $image->getId()->toString()]);
    $context['opengraph']['og:site_name'] = 'fayl.in';
    $context['opengraph']['og:image'] = $this->fullUrlFor($request, 'images.download', ['imageName' => $image->getName()]);
    $context['opengraph']['og:image:type'] = $image->getContentType();
    $context['opengraph']['og:image:alt'] = $image->getTitle();

    // Render the frontend
    return $this->twig->render($response, 'index.twig', $context);
  }

  // Render a user template
  public function renderUser(Request $request, Response $response, User $user)
  {
    // Create the render context
    $context = [];

    // Set the title of the collection page
    $context['title'] = "{$user->getTitle()}";

    // Set the OpenGraph tags of the collection
    $context['opengraph'] = [];
    $context['opengraph']['og:title'] = $user->getTitle();
    $context['opengraph']['og:description'] = $user->getDescription();
    $context['opengraph']['og:type'] = 'profile';
    $context['opengraph']['og:url'] = $this->fullUrlFor($request, 'frontend.user', ['userId' => $user->getId()->toString()]);
    $context['opengraph']['og:site_name'] = 'fayl.in';
    $context['opengraph']['og:profile:username'] = $user->getName();

    // Render the frontend
    return $this->twig->render($response, 'index.twig', $context);
  }
}
