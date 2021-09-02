<?php
namespace Danae\Faylin\App;


// Class that defines the upload capabilities
final class Capabilities
{
  // The supported content types for uploaded files
  private $supportedContentTypes;

  // The supported size for uploaded files
  private $supportedSize;


  // Constructor
  public function __construct(array $supportedContentTypes, int $supportedSize)
  {
    $this->supportedContentTypes = $supportedContentTypes;
    $this->supportedSize = $supportedSize;
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
