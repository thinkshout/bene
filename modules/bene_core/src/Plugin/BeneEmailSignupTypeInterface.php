<?php

namespace Drupal\bene_core\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines an interface for all Bene email signup type plugins.
 *
 * When defining a new plugin type you need to define an interface that all
 * plugins of the new type will implement. This ensures that consumers of the
 * plugin type have a consistent way of accessing the plugin's functionality. It
 * should include access to any public properties, and methods for accomplishing
 * whatever business logic anyone accessing the plugin might want to use.
 *
 * For example, an image manipulation plugin might have a "process" method that
 * takes a known input, probably an image file, and returns the processed
 * version of the file.
 *
 * In our case we'll define methods for accessing the human readable description
 * of a BeneEmailSignupType. As well as a method for returning a config form.
 */
interface BeneEmailSignupTypeInterface extends PluginInspectionInterface {

  /**
   * Provide a description of the email service.
   *
   * @return string
   *   A string description of the email service.
   */
  public function description();

  /**
   * Provide a title of the email service.
   *
   * @return string
   *   A string title of the email service.
   */
  public function title();

  /**
   * Provide a settings form for the email service.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   *
   * @return array
   *   Render array (form) for the settings.
   */
  public function settingsForm(array $configuration);

  /**
   * Opportunity to validate your settings form.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param array $form
   *   The form to validate.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The state of the form with current entries and selections.
   */
  public function validateSettingsForm(array $configuration, array &$form, FormStateInterface $form_state);

  /**
   * Called when submitting the settings form.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param $form
   *   The form definition array for the full configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return string
   *   Returns the id of the currently selected mailchimp signup block.
   */
  public function submitSettingsForm(array &$configuration, $form, FormStateInterface $form_state);

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   *
   * @return array
   *   A renderable array representing the content of the block.
   */
  public function buildEndUserEmailSignup(array $configuration);

  // Add get/set methods for your plugin type here.
}
