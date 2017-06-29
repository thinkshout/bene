<?php

namespace Drupal\bene_media_twitter\Plugin\MediaEntity\Type;

use Drupal\bene_media\InputMatchInterface;
use Drupal\bene_media\SourceFieldInterface;
use Drupal\bene_media\SourceFieldPluginTrait;
use Drupal\bene_media\ValidationConstraintMatchTrait;
use Drupal\media_entity_twitter\Plugin\MediaEntity\Type\Twitter as BaseTwitter;

/**
 * Input-matching version of the Twitter media type.
 */
class Twitter extends BaseTwitter implements InputMatchInterface, SourceFieldInterface {

  use ValidationConstraintMatchTrait;
  use SourceFieldPluginTrait;

}
