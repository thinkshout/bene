<?php

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\bene_migrate_google_sheets\Plugin\migrate\process\ProcessEntityReferenceRevisions.
 */

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\Migration;
use Drupal\migrate\Row;

/**
 * Process a list of IDs into an array for Entity reference revisions field.
 *
 * @MigrateProcessPlugin(
 *   id = "entity_reference_revisions"
 * )
 */
class ProcessEntityReferenceRevisions extends Migration {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // This plugin only supports one migration whereas its parent supports
    // multiple.
    $migration_id = $this->configuration['migration'];
    /** @var \Drupal\migrate\Plugin\MigrationInterface[] $migration */
    $migration = $this->migrationPluginManager->createInstance($migration_id);

    // Loop through values and use the specified migration's map to look up the
    // corresponding destination value.
    $delimiter = $this->configuration['delimiter'];
    $destination_ids = [];
    foreach (explode($delimiter, $value) as $source_id) {
      // Break out of the loop as soon as a destination ID is found.
      if ($destination_id = $migration->getIdMap()->lookupDestinationId([$source_id])) {
        // IMPORTANT: wrapping $source_id in array in line above and grabbing
        // first item in line below are required because lookupDestinationId()
        // deals in arrays.
        $destination_ids[] = reset($destination_id);
      }
    }

    // For "Entity reference revisions" type fields we've got to specify a
    // target_id and a target_revision_id. Because this migration is the
    // initial creation of our entities we use the same ID for both.
    $output = [];
    foreach ($destination_ids as $dest_id) {
      $output[] = [
        'target_id' => $dest_id,
        'target_revision_id' => $dest_id,
      ];
    }

    return $output;
  }

}
