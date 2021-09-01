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
use Danae\Faylin\Model\User;
use Danae\Faylin\Utils\Snowflake;
use Danae\Faylin\Validator\Validator;


// Controller that defines routes for images
final class ImageController extends AbstractController
{
  // The stream factory interface to use with the controller
  private $streamFactory;


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
    $now = new \DateTime();

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
    $image->setUpdatedAt($now);

    // Update the image in the repository
    $this->imageRepository->update($image);

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
  public function uploadImage(Request $request, Response $response, User $authUser, Snowflake $snowflake)
  {
    $now = new \DateTime();

    // Get the uploaded file
    $file = $this->getUploadedFile($request, 'file');
    $fileStream = $file->getStream();

    // Create the image
    $image = (new Image())
      ->setId($snowflake->generateBase64String())
      ->setCreatedAt($now)
      ->setUpdatedAt($now)
      ->setUser($authUser)
      ->setName($this->getUploadedFileNameWithoutExtension($file))
      ->setContentType($file->getClientMediaType())
      ->setContentLength($file->getSize())
      ->setChecksum(hash('sha256', $fileStream->getContents()));

    // Write the file stream
    $this->writeFile($request, $image, $fileStream);

    // Create the image in the repository
    $this->imageRepository->insert($image);

    // Return the response
    return $this->serialize($request, $response, $image)
      ->withStatus(201);
  }

  // Replace an image
  public function replaceImage(Request $request, Response $response, Image $image, User $authUser)
  {
    $now = new \DateTime();

    // Check if the image is owned by the authorized user
    if ($authUser->getId() !== $image->getUser()->getId())
      throw new HttpForbiddenException($request, "The current authorized user is not allowed to replace the image with id \"{$id->getId()}\"");

    // Get the uploaded file
    $file = $this->getUploadedFile($request, 'file');
    $fileStream = $file->getStream();

    // Update the image
    $image
      ->setUpdatedAt($now)
      ->setContentType($file->getClientMediaType())
      ->setContentLength($file->getSize())
      ->setChecksum(hash('sha256', $fileStream->getContents()));

    // Write the file stream
    $this->writeFile($request, $image, $fileStream);

    // Update the image in the repository
    $this->imageRepository->update($image);

    // Return the response
    return $this->serialize($request, $response, $image)
      ->withStatus(201);
  }

  // Download the contents of an image
  public function downloadImage(Request $request, Response $response, Image $image, ?string $format = null)
  {
    // Sanitize the format
    $format = $format !== null ? strtolower($format) : null;

    // Get and validate the query parameters
    $query = (new Validator())
      ->withOptional('transform', 'string')
      ->withOptional('dl', 'bool:true', false)
      ->validate($request->getQueryParams(), ['allowExtraFields' => true])
      ->resultOrThrowBadRequest($request);

    // Create the content stream
    $stream = $this->readFile($request, $image, $query['transform'], $format);

    // Create the format
    if ($format === null)
      $format = $this->supportedContentTypes[$image->getContentType()];

    // Create the content name
    $contentName = $image->getName();
    if (!preg_match("/\.{$format}\$/i", $contentName))
      $contentName .= '.' . $format;

    // Return the response
    return $response
      ->withHeader('Content-Type', array_search($format, $this->supportedContentTypes))
      ->withHeader('Content-Length', $stream->getSize())
      ->withHeader('Content-Disposition', ($query['dl'] ? "attachment" : "inline") . "; filename=\"{$contentName}\"")
      ->withHeader('ETag', "\"{$image->getChecksum()}\"")
      ->withBody($stream);
  }


  // Return and validate an uploaded file from the request
  private function getUploadedFile(Request $request, string $name): UploadedFile
  {
    // Get the file from the request
    $files = $request->getUploadedFiles();
    if (!isset($files[$name]))
      throw new HttpBadRequestException($request, "No uploaded file has been provided");

    $file = $files[$name];

    // Check if the upload succeeded
    if ($file->getError() === UPLOAD_ERR_INI_SIZE)
      throw new HttpException($request, "The size of the uploaded file is not supported based on the directive in the PHP configuration", 413);
    else if ($file->getError() === UPLOAD_ERR_FORM_SIZE)
      throw new HttpException($request, "The size of the uploaded file is not supported based on the directive in the upload form", 413);
    else if ($file->getError() === UPLOAD_ERR_PARTIAL)
      throw new HttpBadRequestException($request, "The uploaded file contains only partial data");
    else if ($file->getError() === UPLOAD_ERR_NO_FILE)
      throw new HttpBadRequestException($request, "The uploaded file does not contain any data");
    else if ($file->getError() === UPLOAD_ERR_NO_TMP_DIR)
      throw new HttpInternalServerErrorException($request, "Missing a temporary folder");
    else if ($file->getError() === UPLOAD_ERR_CANT_WRITE)
      throw new HttpInternalServerErrorException($request, "Failed to write the file to disk");
    else if ($file->getError() === UPLOAD_ERR_EXTENSION)
      throw new HttpInternalServerErrorException($request, "A PHP extension stopped the file upload");

    // Check the type of the file
    if (!array_key_exists($file->getClientMediaType(), $this->supportedContentTypes))
      throw new HttpException($request, "The type of the uploaded file is not supported, the supported types are " . implode(', ', array_keys($this->supportedContentTypes)), 415);

    // Check the size of the file
    if ($file->getSize() > $this->supportedSize)
      throw new HttpException($request, "The size of the uploaded file is not supported, the maximal supported size is {$this->supportedSize} bytes", 413);

    // Return the file
    return $file;
  }

  // Return the name of an uploaded file without the extension according to its content type
  private function getUploadedFileNameWithoutExtension(UploadedFile $file)
  {
    $contentType = $file->getClientMediaType();
    $contentTypeExtension = $this->supportedContentTypes[$contentType] ?? null;

    if ($contentTypeExtension !== null)
      return preg_replace("/\.{$contentTypeExtension}\$/i", "", $file->getClientFilename());
    else
      return $file->getClientFilename();
  }

  // Read a file stream from the image repository
  private function readFile(Request $request, Image $image, ?string $transform = null, ?string $format = null): StreamInterface
  {
    try
    {
      // Read a stream containing the contents of the image
      $stream = $this->imageRepository->readFile($image);

      // Check if the image stream needs to be transformed or converted
      if ($transform !== null || ($format !== null && $format !== $this->supportedContentTypes[$image->getContentType()]))
      {
        // Check if the requested format is a supported format
        if ($format !== null && !in_array($format, $this->supportedContentTypes))
          throw new HttpBadRequestException($request, "The requested type is not supported, the supported types are " . implode(', ', array_keys($this->supportedContentTypes)));

        // Create the image
        $imagecow = ImagecowImage::fromString((string)$stream, ImagecowImage::LIB_IMAGICK);
        if ($image->getContentType() === 'image/svg+xml' && $format !== null && $format !== 'svg')
          $imagecow->format('png');

        // Transform the image if applicable
        if ($transform !== null)
          $imagecow->transform($transform);

        // Convert the image if applicable
        if ($format !== null && $format !== $this->supportedContentTypes[$image->getContentType()])
          $imagecow->format($format);

        // Adjust the content metadata
        $stream = $this->streamFactory->createStream($imagecow->getString());
      }

      // Return the stream
      return $stream;
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not read the file: {$ex->getMessage()}", $ex);
    }
  }

  // Write a file stream to the image repository
  private function writeFile(Request $request, Image $image, StreamInterface $stream): void
  {
    try
    {
      // Write the contents of the image from the stream
      $this->imageRepository->writeFile($image, $stream);
    }
    catch (FilesystemException $ex)
    {
      throw new HttpInternalServerErrorException($request, "Could not write the file: {$ex->getMessage()}", $ex);
    }
  }
}
