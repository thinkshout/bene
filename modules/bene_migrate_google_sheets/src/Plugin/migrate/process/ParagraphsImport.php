<?php

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\bene_migrate_gogle_sheets\Plugin\migrate\process\ParagraphsImport.
 */


use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\migrate\MigrateException;

/**
 * Import paragraphs.
 *
 * @MigrateProcessPlugin(
 *   id = "paragraphs_import"
 * )
 */
class ParagraphsImport extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['paragraph_type'])) {
      throw new MigrateException('Specify a paragraph type.');
    }
    $paragraphs = [];
    $paragraph_values = [];
    foreach ($this->configuration['fields'] as $delta => $fields) {
      foreach ($fields as $field_name => $source_field) {
        if (is_array($source_field)) {
          if (isset($source_field['plugin'])) {
            switch ($source_field['plugin']) {
              case 'media_import':
                $migration = new MediaImport($source_field, $source_field['plugin'], []);
                break;

              case 'link_url_import':
                $migration = new LinkURLImport($source_field, $source_field['plugin'], []);
                break;

              default:
                $migration = FALSE;
            }
          }

          if ($migration) {
            $source_value = $row->getSourceProperty($source_field['source']);
            $field_value = $migration->transform($source_value, $migrate_executable, $row, $destination_property);
          }
          else {
            $migrate_executable->saveMessage('Currently only the media and Link URL plugins are supported paragraph field plugins.');
            continue;
          }

        }
        else {
          $field_value = $row->getSourceProperty($source_field);
        }

        // Extra handling for sub-elements in some forms like "link".
        if (strpos($field_name, '/') !== FALSE) {
          $field_name_array = explode('/', $field_name);
          $paragraph_values[$field_name_array[0]][$field_name_array[1]] = $field_value;
        }
        else {
          $paragraph_values[$field_name]['value'] = $field_value;
        }
      }
      // Don't create empty paragraphs.
      if (count($paragraph_values)) {
        $paragraph_values = array_merge(
          [
            'id' => NULL,
            'type' => $this->configuration['paragraph_type'],
          ],
          $paragraph_values);
        $paragraph = Paragraph::create($paragraph_values);
        $paragraph->save();

        $paragraphs[$delta] = $paragraph;
      }
    }

    return $paragraphs;
  }

}
