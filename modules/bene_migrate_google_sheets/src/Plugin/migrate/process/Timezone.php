<?php

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\bene_migrate_google_sheets\Plugin\migrate\process\Timezone.php.
 */


use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Fix times imported to store properly as the correct UTC time.
 *
 * @MigrateProcessPlugin(
 *   id = "timezone_import"
 * )
 */
class Timezone extends ProcessPluginBase {

  /**
   * Transforms given time into UTC time for storage.
   *
   * @param mixed $value
   *   Comment.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   Comment.
   * @param \Drupal\migrate\Row $row
   *   Comment.
   * @param string $destination_property
   *   Comment.
   *
   * @return mixed
   *   Description.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $timezone = \Drupal::configFactory()->get('system.date')->get('timezone.default');
    $date = new DrupalDateTime($value, $timezone);
    $date->setTimezone(new \DateTimeZone('UTC'));
    $final = $date->__toString();
    $final = str_replace(' UTC', '', $final);
    $final = str_replace(' ', 'T', $final);
    return $final;
  }

}
