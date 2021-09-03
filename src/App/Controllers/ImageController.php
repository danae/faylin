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
    // Get the images
    $options = $this->createSelectOptions($request, ['sort' => '-createdAt']);
    $images = $this->imageRepository->findManyBy(['public' => true], $options);

    // Return the response
    return $this->serialize($request, $response, $images);
  }

  // Get an image as a JSON response
  public function getImage(Request $request, Response $response, Image $image)
  {
    // Return the response
    return $this->serialize($request, $response, $image);
  }

  // Patch an image and return the image as a JSON response
  public function patchImage(Request $request, Response $response, Image $image, User $authUser)
  {
    // Check if the authorized user owns this image
    if ($authUser->getId() !== $image->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to modify the image with id \"{$image->getId()}\"");

    // Get and validate the body parameters
    $params = (new Validator())
      ->withOptional('name', 'string|notempty|maxlength:256')
      ->withOptional('description', 'string|maxlength:256')
      ->withOptional('public', 'bool')
      ->withOptional('nsfw', 'bool')
      ->validate($request->getParsedBody())
      ->resultOrThrowBadRequest($request);

    // Modify the user
    if ($params['name'] !== null)
      $image->setName($params['name']);
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
  public function deleteImage(Request $request, Response $response, Image $image, User $authUser)
  {
    // Check if the authorized user owns this image
    if ($authUser->getId() !== $image->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to delete the image with id \"{$image->getId()}\"");

    // Delete the image from the repository
    $this->imageRepository->delete($image);
    $this->imageRepository->deleteFile($image);

    // Return the response
    return $response
      ->withStatus(204);
  }

  // Upload an image
  public function uploadImage(Request $request, Response $response, User $authUser, SnowflakeGenerator $snowflakeGenerator)
  {
    // Get the uploaded file
    $file = $this->getUploadedFile($request, 'file');

    // Create the image
    $image = new Image();
    $image->generateId($snowflakeGenerator);
    $image->setUser($authUser);
    $image->setName($this->getUploadedFileNameWithoutExtension($file));

    // Write the image to the store with the contents of the uploaded file
    $this->imageStore->writeUploadedFile($image, $request, $file);

    // Create the image in the repository
    $this->imageRepository->insert($image);

    // Return the response
    return $this->serialize($request, $response, $image)
      ->withStatus(201);
  }

  // Replace an image
  public function replaceImage(Request $request, Response $response, Image $image, User $authUser)
  {
    // Check if the image is owned by the authorized user
    if ($authUser->getId() !== $image->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to replace the image with id \"{$id->getId()}\"");

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
    if ($query['transform'] !== null || $format !== $this->capabilities->convertContentTypeToFormat($image->getContentType()))
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
