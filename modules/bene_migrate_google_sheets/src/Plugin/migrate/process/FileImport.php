<?php

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\bene_migrate_google_sheets\Plugin\migrate\process\FileImport.
 */

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateSkipProcessException;

/**
 * Import a file as a side-effect of a migration.
 *
 * Fetches the file, and yields a file ID.
 *
 * @MigrateProcessPlugin(
 *   id = "file_import"
 * )
 */
class FileImport extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      // Skip this item if there's no URL.
      throw new MigrateSkipProcessException();
    }

    // Save the file, return its ID.
    $file = system_retrieve_file($value, 'public://', TRUE, FILE_EXISTS_REPLACE);
    return $file->id();
  }

}
