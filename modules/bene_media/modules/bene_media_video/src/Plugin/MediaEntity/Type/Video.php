<?php

namespace Drupal\bene_media_video\Plugin\MediaEntity\Type;

use Drupal\bene_media\InputMatchInterface;
use Drupal\bene_media\SourceFieldInterface;
use Drupal\bene_media\SourceFieldPluginTrait;
use Drupal\media_entity\MediaBundleInterface;
use Drupal\video_embed_media\Plugin\MediaEntity\Type\VideoEmbedField;

/**
 * Input-matching version of the VideoEmbedField media type.
 */
class Video extends VideoEmbedField implements InputMatchInterface, SourceFieldInterface {

  use SourceFieldPluginTrait;

  /**
   * {@inheritdoc}
   */
  public function appliesTo($value, MediaBundleInterface $bundle) {
    return (boolean) $this->providerManager->loadProviderFromInput($value);
  }

}
