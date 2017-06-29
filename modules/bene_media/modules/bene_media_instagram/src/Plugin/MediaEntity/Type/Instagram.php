<?php

namespace Drupal\bene_media_instagram\Plugin\MediaEntity\Type;

use Drupal\bene_media\InputMatchInterface;
use Drupal\bene_media\SourceFieldInterface;
use Drupal\bene_media\SourceFieldPluginTrait;
use Drupal\bene_media\ValidationConstraintMatchTrait;
use Drupal\media_entity_instagram\Plugin\MediaEntity\Type\Instagram as BaseInstagram;

/**
 * Input-matching version of the Instagram media type.
 */
class Instagram extends BaseInstagram implements InputMatchInterface, SourceFieldInterface {

  use ValidationConstraintMatchTrait;
  use SourceFieldPluginTrait;

}
