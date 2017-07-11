<?php

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\bene_migrate_google_sheets\Plugin\migrate\process\MediaImport.
 */

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\media_entity\Entity\Media;

/**
 * Import a file to create a media entity as a side-effect of a migration.
 *
 * Fetches the file, and yields a media ID.
 *
 * How to use this plugin:
 *
 * The assumption is that this is used for an entity reference field that only
 * refers to Media entities. Plugin would look like this:
 *
 * For an Image entity:
 *
 * field_my_field_name/target_id:
 *   plugin: media_import
 *   source: myImageColumn
 *   process:
 *     bundle: 'image'
 *     name: myImageTitleColumn
 *     field_image/alt: myImageAltColumn
 * field_my_field_name/description: myImageDescriptionColumn
 *
 * For a Video entity:
 *
 * field_resource_video/target_id:
 *   plugin: media_import
 *   source: myVideoColumn
 *   process:
 *     bundle: 'video'
 *     field_video/value: myVideoColumn
 *
 * @MigrateProcessPlugin(
 *   id = "media_import"
 * )
 */
class MediaImport extends ProcessPluginBase {

  /**
   * Convert URL into a Media entity and return the entity ID.
   *
   * @param mixed $imported_value
   *   Raw value from migration source.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process. Normally, just transforming the value
   *   is adequate but very rarely you might need to change two columns at the
   *   same time or something like that.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @return int|mixed|null|string
   *   The newly transformed value.
   *
   * @throws \Drupal\migrate\MigrateException
   * @throws \Drupal\migrate\MigrateSkipProcessException
   */
  public function transform($imported_value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($imported_value)) {
      // Skip this item if there's no URL.
      throw new MigrateSkipProcessException();
    }

    foreach ($this->configuration['process'] as $key => $value) {
      $field_orig = explode('/', $key);
      if (isset($field_orig[1])) {
        $field_name = $field_orig[0];
        $field[$field_orig[1]] = $row->getSourceProperty($value);
      }
    }

    if (empty($field_name)) {
      $field_name = 'field_image';
    }

    // Save the file, return its ID.
    switch ($this->configuration['process']['bundle']) {
      case 'image':
      case 'icon':
        $file = system_retrieve_file($imported_value, 'public://', TRUE, FILE_EXISTS_REPLACE);

        if ($file) {
          // Create node object with attached file.
          $media = Media::create([
            'bundle' => $this->configuration['process']['bundle'],
            'name' => isset($field['title']) ? $field['title'] : $file->getFilename(),
            $field_name => [
              'target_id' => $file->id(),
              'alt' => isset($field['alt']) ? $field['alt'] : '',
            ],
          ]);
          $media->save();

          // Create an entity, return its ID.
          return $media->id();
        }
        break;

      case 'video':
        // Create media object with video file referenced in supplied field.
        $media = Media::create([
          'bundle' => 'video',
          $field_name => [
            'value' => $imported_value,
          ],
        ]);

        // Ensure we successfully created a media entity before saving it.
        if ($media) {
          $media->save();

          // Create an entity, return its ID.
          return $media->id();
        }
        break;
    }

    return NULL;
  }

}
