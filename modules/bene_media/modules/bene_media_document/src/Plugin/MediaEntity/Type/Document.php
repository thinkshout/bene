<?php

namespace Drupal\bene_media_document\Plugin\MediaEntity\Type;

use Drupal\bene_media\FileInputExtensionMatchTrait;
use Drupal\bene_media\InputMatchInterface;
use Drupal\bene_media\SourceFieldInterface;
use Drupal\bene_media\SourceFieldPluginTrait;
use Drupal\media_entity_document\Plugin\MediaEntity\Type\Document as BaseDocument;

/**
 * Input-matching version of the Document media type.
 */
class Document extends BaseDocument implements InputMatchInterface, SourceFieldInterface {

  use FileInputExtensionMatchTrait;
  use SourceFieldPluginTrait;

}
