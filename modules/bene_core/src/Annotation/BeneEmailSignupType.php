<?php

namespace Drupal\bene_core\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Bene email signup type item annotation object.
 *
 * Defines a new annotation type BeneEmailSignupType for use in
 * defining a plugin type. Documents the various properties that
 * can be used in annotations for plugins of this type.
 *
 * @see \Drupal\bene_core\Plugin\BeneEmailSignupTypeManager
 * @see plugin_api
 *
 * Note that the "@ Annotation" line below is required and should be the last
 * line in the docblock. It's used for discovery of Annotation definitions.
 *
 * @Annotation
 */
class BeneEmailSignupType extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * A brief, human readable, description of the BeneEmailSignupType type.
   *
   * This property is designated as being translatable because it will appear
   * in the user interface. This provides a hint to other developers that they
   * should use the Translation() construct in their annotation when declaring
   * this property.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The title of the plugin. It will appear as a radio button label.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
