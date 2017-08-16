<?php

namespace Drupal\bene_event\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem as CoreDateRangeItem;

/**
 * Plugin implementation of the 'daterange' field type.
 *
 * @FieldType(
 *   id = "daterange",
 *   label = @Translation("Date range"),
 *   description = @Translation("Create and store date ranges."),
 *   default_widget = "daterange_default",
 *   default_formatter = "daterange_default",
 *   list_class =
 *   "\Drupal\datetime_range\Plugin\Field\FieldType\DateRangeFieldItemList"
 * )
 */
class DateRangeItem extends CoreDateRangeItem {

  /**
   * {@inheritdoc}
   *
   * The entire reason this class exists is to overwrite the Required property
   * of end_value. It is only safe to do this because in this module we fill the
   * end_value with the start_value when left empty. Keep an eye on:
   * https://www.drupal.org/node/2794481.
   *
   * @see bene_event_entity_presave();
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['end_value'] = DataDefinition::create('datetime_iso8601')
      ->setLabel(t('End date value'))
      ->setRequired(FALSE);

    return $properties;
  }

}
