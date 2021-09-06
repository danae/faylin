<?php
namespace Danae\Faylin\App\Controllers;

use Imagecow\Image as ImagecowImage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface as UploadedFile;
use League\Flysystem\FilesystemException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Symfony\Component\Serializer\Serializer;

use Danae\Faylin\Model\Image;
use Danae\Faylin\Model\ImageTransform;
use Danae\Faylin\Model\SnowflakeGenerator;
use Danae\Faylin\Model\User;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for images
final class ImageController extends AbstractController
{
  // Return all images as a JSON response
  public function getImages(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the repository filter
    if ($authorizedUser !== null)
      $filter = ['$or' => [['public' => true], ['user' => $authorizedUser->getId()->toString()]]];
    else
      $filter = ['public' => true];

    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->findManyBy($filter, $options);

    // Return the response
    return $this->serialize($request, $response, $images);
  }

  // Return all images owned by a user as a JSON response
  public function getUserImages(Request $request, Response $response, User $user)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the repository filter
    if ($authorizedUser !== null)
      $filter = ['user' => $user->getId()->toString(), '$or' => [['public' => true], ['user' => $authorizedUser->getId()->toString()]]];
    else
      $filter = ['user' => $user->getId()->toString(), 'public' => true];

    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->findManyBy($filter, $options);

    // Return the response
    return $this->serialize($request, $response, $images)
      ->withStatus(200);
  }

  // Return all images owned by the authorized user as a JSON response
  public function getAuthorizedUserImages(Request $request, Response $response)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Return the response
    return $this->getUserImages($request, $response, $authorizedUser);
  }

  // Get an image as a JSON response
  public function getImage(Request $request, Response $response, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read this image
    if (!$this->canReadImage($image, $authorizedUser))
      throw ImageResolverMiddleware::createIdNotFound($request, $image->getId()->toString());

    // Return the response
    return $this->serialize($request, $response, $image);
  }

  // Patch an image and return the image as a JSON response
  public function patchImage(Request $request, Response $response, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this image
    if (!$this->canReadImage($image, $authorizedUser))
      throw ImageResolverMiddleware::createIdNotFound($request, $image->getId()->toString());
    if (!$this->canModifyImage($image, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the image with id \"{$image->getId()}\"");

    // Get and validate the body parameters
    $params = (new Validator())
      ->withOptional('title', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256')
      ->withOptional('public', 'bool')
      ->withOptional('nsfw', 'bool')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the image
    if ($params['title'] !== null)
      $image->setTitle($params['title']);
    if ($params['description'] !== null)
      $image->setDescription($params['description']);
    if ($params['public'] !== null)
      $image->setPublic($params['public']);
    if ($params['nsfw'] !== null)
      $image->setNsfw($params['nsfw']);

    // Update the image in the repository
    $this->imageRepository->update($image->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $image);
  }

  // Delete an image
  public function deleteImage(Request $request, Response $response, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this image
    if (!$this->canReadImage($image, $authorizedUser))
      throw ImageResolverMiddleware::createIdNotFound($request, $image->getId()->toString());
    if (!$this->canModifyImage($image, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the image with id \"{$image->getId()}\"");

    // Delete the image from the repository
    $this->imageRepository->delete($image);
    $this->imageStore->delete($image, $request);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Upload an image
  public function uploadImage(Request $request, Response $response, SnowflakeGenerator $snowflakeGenerator)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Get the uploaded file
    $file = $this->getUploadedFile($request, 'file');

    // Create the image
    $image = new Image();
    $image->generateId($snowflakeGenerator);
    $image->setName($image->getId()->toBase64());
    $image->setUser($authorizedUser);
    $image->setTitle($this->getUploadedFileNameWithoutExtension($file));

    // Write the image to the store with the contents of the uploaded file
    $this->imageStore->writeUploadedFile($image, $request, $file);

    // Create the image in the repository
    $this->imageRepository->insert($image);

    // Return the response
    return $this->serialize($request, $response, $image)
      ->withStatus(201);
  }

  // Replace an image
  public function replaceImage(Request $request, Response $response, Image $image)
  {
    // Get the authorized user
    $authorizedUser = $request->getAttribute('authUser');

    // Check if the authorized user can read and modify this image
    if (!$this->canReadImage($image, $authorizedUser))
      throw ImageResolverMiddleware::createIdNotFound($request, $image->getId()->toString());
    if (!$this->canModifyImage($image, $authorizedUser))
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the image with id \"{$image->getId()}\"");

    // Get the uploaded file
    $file = $this->getUploadedFile($request, 'file');

    // Write the image to the store with the contents of the uploaded file
    $this->imageStore->writeUploadedFile($image, $request, $file);

    // Update the image in the repository
    $this->imageRepository->update($image->setUpdatedAt(new \DateTime()));

    // Return the response
    return $this->serialize($request, $response, $image)
      ->withStatus(201);
  }

  // Download the contents of an image
  public function downloadImage(Request $request, Response $response, Image $image, ?string $format = null)
  {
    // Sanitize the format
    $contentType = $format !== null ? $this->capabilities->convertContentFormatToType(strtolower($format)) : $image->getContentType();

    // Get and validate the query parameters
    $query = (new Validator())
      ->withOptional('transform', 'string')
      ->withOptional('dl', 'bool:true', false)
      ->validate($request->getQueryParams(), ['allowExtraFields' => true])
      ->resultOrThrowBadRequest($request);

    // Read the image from the store as a response
    if ($query['transform'] !== null || $contentType !== $image->getContentType())
    {
      // Create the transform
      $transform = new ImageTransform($query['transform'], $contentType);

      // Return the transformed image response
      return $this->imageTransformExecutor->transformResponse($image, $transform, $request, $query['dl']);
    }
    else
    {
      // Return the image response
      return $this->imageStore->readResponse($image, $request, $query['dl']);
    }
  }


  // Return if the authorized user can read an image
  private function canReadImage(Image $image, ?User $authorizedUser): bool
  {
    // Check if the image is public
    if ($image->getPublic())
      return true;

    // Check if the authorized user can modify the image
    if (!$this->canModifyImage($image, $authorizedUser))
      return false;

    // All checks passed
    return true;
  }

  // Return if the authorized user can modify an image
  private function canModifyImage(Image $image, ?User $authorizedUser): bool
  {
    // Check if the authorized user is empty
    if ($authorizedUser === null)
      return false;

    // Check if the identifiers of the users match
    if ($image->getUser()->getId() != $authorizedUser->getId())
      return false;

    // All checks passed
    return true;
  }

  // Return an uploaded file from the request
  private function getUploadedFile(Request $request, string $name): UploadedFile
  {
    // Get the file from the request
    $files = $request->getUploadedFiles();
    if (!isset($files[$name]))
      throw new HttpBadRequestException($request, "No uploaded file has been provided");

    return $files[$name];
  }

  // Return the name of an uploaded file without the extension according to its content type
  private function getUploadedFileNameWithoutExtension(UploadedFile $file)
  {
    $contentType = $file->getClientMediaType();
    $contentTypeExtension = $this->capabilities->convertContentTypeToFormat($contentType);

    if ($contentTypeExtension !== null)
      return preg_replace("/\.{$contentTypeExtension}\$/i", "", $file->getClientFilename());
    else
      return $file->getClientFilename();
  }
}
