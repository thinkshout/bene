<?php

namespace Drupal\bene_core\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for Bene email signup type plugins.
 *
 * Provides \Drupal\bene_core\BeneEmailSignupTypeBase.
 *
 * This is a helper class which makes it easier for other developers to
 * implement BeneEmailSignupType plugins in their own modules. In
 * BeneEmailSignupTypeBase we provide
 * some generic methods for handling tasks that are common to pretty much all
 * BeneEmailSignupType plugins. Thereby reducing the amount of boilerplate code
 * required to implement a BeneEmailSignupType plugin.
 *
 * In this case the description property can be read from
 * the @BeneEmailSignupType annotation. In most cases it is
 * probably fine to just use that value without
 * any additional processing. However, if an individual plugin
 * needed to provide special handling it could
 * just override the method in that class definition for that plugin.
 *
 * We intentionally declare our base class as abstract, and skip settingsForm()
 * method required by \Drupal\bene_core\BeneEmailSignupTypeInterface. This way
 * even if they are using our base class, developers will always be required to
 * define a settingsForm() method for their custom BeneEmailSignupType type.
 *
 * @see \Drupal\bene_core\Annotation\BeneEmailSignupType
 * @see \Drupal\bene_core\BeneEmailSignupTypeInterface
 */
abstract class BeneEmailSignupTypeBase extends PluginBase implements BeneEmailSignupTypeInterface {

  /**
   * Retrieve the @description property from the annotation and return it.
   *
   * @return string
   *   The description of this plugin type.
   */
  public function description() {
    return $this->pluginDefinition['description'];
  }

  /**
   * Retrieve the @title property from the annotation and return it.
   *
   * @return string
   *   The title of this plugin type.
   */
  public function title() {
    return $this->pluginDefinition['title'];
  }

  /**
   * Opportunity to validate your settings form.
   *
   * It appears here because it is optional, you do not have to override.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the form with current entries and selections.
   */
  public function validateSettingsForm(array $configuration, FormStateInterface $form_state) {
  }

  // Add common methods and abstract methods for your plugin type here.
}
