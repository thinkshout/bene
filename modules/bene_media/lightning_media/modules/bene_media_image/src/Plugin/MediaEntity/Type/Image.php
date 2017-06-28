<?php

namespace Drupal\bene_media_image\Plugin\MediaEntity\Type;

use Drupal\bene_media\FileInputExtensionMatchTrait;
use Drupal\bene_media\InputMatchInterface;
use Drupal\bene_media\SourceFieldInterface;
use Drupal\bene_media\SourceFieldPluginTrait;
use Drupal\media_entity_image\Plugin\MediaEntity\Type\Image as BaseImage;

/**
 * Input-matching version of the Image media type.
 */
class Image extends BaseImage implements InputMatchInterface, SourceFieldInterface {

  use FileInputExtensionMatchTrait;
  use SourceFieldPluginTrait;

}
