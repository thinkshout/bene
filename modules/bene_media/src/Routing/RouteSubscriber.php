<?php

namespace Drupal\bene_media\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\bene_media\Form\EntityEmbedDialog;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Entity Embed dialog route.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $collection
      ->get('entity_embed.dialog')
      ->setDefault('_form', EntityEmbedDialog::class);
  }

}
