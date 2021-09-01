<?php
namespace Danae\Faylin\Store;

use League\Flysystem\Filesystem;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;


// Class that defines a wrapper around a filesystem for storing content
final class Store
{
  // The filesystem to use with the store
  private $filesystem;

  // The stream factory to use with the store
  private $streamFactory;

  // The supported content types for stored files
  protected $supportedContentTypes;

  // The supported size for stored files
  protected $supportedSize;


  // Constructor
  public function __construct(Filesystem $filesystem, StreamFactoryInterface $streamFactory, array $supportedContentTypes, int $supportedSize)
  {
    $this->filesystem = $filesystem;
    $this->streamFactory = $streamFactory;
    $this->supportedContentTypes = $supportedContentTypes;
    $this->supportedSize = $supportedSize;
  }

  // Return the filesystem
  public function getFilesystem(): Filesystem
  {
    return $this->filesystem;
  }

  // Return the stream factory
  public function getStreamFactory(): StreamFactoryInterface
  {
    return $this->streamFactory;
  }

  // Return if a file exists
  public function has(string $file): bool
  {
    return $this->filesystem->fileExists($file);
  }

  // Read a stream containing the contents of a file
  public function read(string $file): StreamInterface
  {
    // Read the file contents and decompress them
    $contents = $this->filesystem->read($file);
    $contents = gzdecode($contents);

    // Create and return a stream containing the contents
    return $this->streamFactory->createStream($contents);
  }

  // Write the contents of a file from a stream
  public function write(string $file, StreamInterface $stream): void
  {
    // Rewind the stream
    $stream->rewind();

    // Read the contents from the stream and compress them
    $contents = $stream->getContents();
    $contents = gzencode($contents);

    // Write the file contents
    $this->filesystem->write($file, $contents);
  }

  // Delete the contents of a file
  public function delete(string $file): void
  {
    // Delete the file
    $this->getFilesystem()->delete($file);
  }

  // Return the supported content types
  public function getSupportedContentTypes(): array
  {
    return array_keys($this->supportedContentTypes);
  }

  // Return the supported content formats
  public function getSupportedContentFormats(): array
  {
    return array_values($this->supportedContentTypes);
  }

  // Return if a content type is supported
  public function isContentTypeSupported(string $contentType): bool
  {
    return array_key_exists($contentType, $this->supportedContentTypes);
  }

  // Return if a content format is supported
  public function isContentFormatSupported(string $contentFormat): bool
  {
    return in_array($contentFormat, $this->supportedContentTypes);
  }

  // Convert a content type to a format
  public function convertContentTypeToFormat(string $contentType): string
  {
    return $this->supportedContentTypes[$contentType] ?? null;
  }

  // Convert a content format to a type
  public function convertContentFormatToType(string $contentFormat): string
  {
    $result = array_search($contentFormat, $this->supportedContentTypes);
    return $result !== false ? $result : null;
  }

  // Return the supported size
  public function getSupportedSize(): int
  {
    return $this->supportedSize;
  }

  // Return if a size is supported
  public function isSizeSupported(int $size): bool
  {
    return $size >= 0 && $size <= $this->supportedSize;
  }
}
