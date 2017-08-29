<?php

namespace Drupal\bene_core;

use Drupal\migrate\Event\MigrateImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides the logic needed to update field storage definitions when needed.
 */
class BeneUpdatesManager implements EventSubscriberInterface {

  /**
   * Listener for migration imports.
   */
  public function onMigrateImport(MigrateImportEvent $event) {
    $alias = '/home';
    $path = \Drupal::service('path.alias_manager')->getPathByAlias($alias);
    if ($path !== $alias) {
      \Drupal::configFactory()->getEditable('system.site')
        ->set('page.front', $path)
        ->save();
    }
  }

}
