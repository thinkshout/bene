<?php

/**
 * @file
 * Contains \Drupal\bene_migrate_google_sheets\Plugin\migrate\process\FileImport.
 */

namespace Drupal\bene_migrate_google_sheets\Plugin\migrate\process;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Form\FormState;

/**
 * Import a link url during migration.
 *
 * Given a url like /about, translates it into internal:/about but given an
 * external url, leaves it as-is. This matches the behavior of urls posted via
 * form fields.
 *
 * Otherwise, upon further saving of the entity you get this error:
 * InvalidArgumentException: The URI '/about' is invalid. You must use a valid
 * URI scheme. in Drupal\Core\Url::fromUri() (line 280...
 *
 * @MigrateProcessPlugin(
 *   id = "link_url_import"
 * )
 */
class LinkURLImport extends ProcessPluginBase {
  /**
   * Transforms user-friendly urls into DB-friendly values.
   *
   * The goal of this is to simply access
   * Drupal\link\Plugin\Field\FieldWidget\LinkWidget::getUserEnteredStringAsUri
   * but that function is static protected.
   *
   * @param mixed $value
   *  Comment.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *  Comment.
   * @param \Drupal\migrate\Row $row
   *  Comment.
   * @param string $destination_property
   *  Comment.
   *
   * @return mixed
   *  Description.
   */

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $field_base_definition = new BaseFieldDefinition();
    $empty_form_state = new FormState();

    // We don't need any values in these to use getUserEnteredStringAsUri.
    $empty_plugin_definition =
      $empty_settings =
      $empty_third_party_settings =
      $empty_form = [];

    // All values populated, create an empty instance of LinkWidget.
    $link_widget = new LinkWidget('link_default', $empty_plugin_definition, $field_base_definition, $empty_settings, $empty_third_party_settings);

    // Use massageFormValues to call getUserEnteredStringAsUri.
    $as_if_it_were_a_form = $link_widget->massageFormValues([[('uri' => $value)], $empty_form, $empty_form_state];

    return isset($as_if_it_were_a_form[0]['uri']) ? $as_if_it_were_a_form[0]['uri'] : $value;
  }

}
