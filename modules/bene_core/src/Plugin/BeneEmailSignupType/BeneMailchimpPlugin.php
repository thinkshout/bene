<?php

namespace Drupal\bene_core\Plugin\BeneEmailSignupType;

use Drupal\bene_core\Plugin\BeneEmailSignupTypeBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mailchimp_signup\Form\MailchimpSignupPageForm;

/**
 * Provides Mailchimp for email signup.
 *
 * @BeneEmailSignupType(
 *   id = "mailchimp_for_bene",
 *   title = "Mailchimp Form",
 *   description = @Translation("Connect to Mailchimp for email signups."),
 * )
 */
class BeneMailchimpPlugin extends BeneEmailSignupTypeBase {

  /**
   * Provides a form that collects settings for the newsletter signup.
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
  public function settingsForm(array $configuration) {
    $mailchimpSettingsForm = [];

    // Help the user turn on MailChimp.
    $moduleHandler = \Drupal::service('module_handler');
    if (!$moduleHandler->moduleExists('mailchimp') || !$moduleHandler->moduleExists('mailchimp_signup')) {
      $mailchimpSettingsForm['help_text'] = [
        '#type' => 'markup',
        '#markup' => 'Please <a href="/admin/modules?destination=/admin/structure/block/manage/benenewslettersignup">enable and configure MailChimp and a MailChimp Signup block</a> to begin.',
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
    else {
      // Help the User set a key.
      $mailchimp_config = \Drupal::config('mailchimp.settings');
      ('mailchimp.settings');
      $key = $mailchimp_config->get('api_key');

      if (!$key) {
        $mailchimpSettingsForm['help_text'] = [
          '#type' => 'markup',
          '#markup' => 'Please add a <a href="/admin/config/services/mailchimp?destination=/admin/structure/block/manage/benenewslettersignup">MailChimp API</a> key and create a signup block to begin.',
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
      else {
        // Rely on help text to encourage the user to create a signup block.
        $options = [];
        $signup_blocks = mailchimp_signup_load_multiple();
        $default_signup_block = '';

        foreach ($signup_blocks as $signup_key => $signup) {
          $options[$signup_key] = $signup->title;
          if ($configuration['signup_block'] == $signup_key) {
            $default_signup_block = $configuration['signup_block'];
          }
        }
        if ($options) {
          $mailchimpSettingsForm['signup_block'] = [
            '#type' => 'select',
            '#options' => $options,
            '#title' => 'Signup Block',
            '#description' => 'Select a MailChimp signup block or <a href="/admin/config/services/mailchimp/signup?destination=/admin/structure/block/manage/benenewslettersignup">create a new signup block</a>.',
            '#default_value' => $default_signup_block,
            '#required' => FALSE,
            '#states' => [
              'required' => [
                ':input[name="settings[style]"]' => ['value' => 'embedded'],
              ],
            ],
          ];
        }
        else {
          $mailchimpSettingsForm['signup_block_warning'] = [
            '#type' => 'markup',
            '#markup' => 'To use the MailChimp form style you will need to <a href="/admin/config/services/mailchimp/signup?destination=/admin/structure/block/manage/benenewslettersignup">create a new MailChimp signup block</a> and return to this page to select it.',
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ];
        }
      }
    }
    return $mailchimpSettingsForm;
  }

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
  public function validateSettingsForm(array $configuration, array &$form, FormStateInterface $form_state) {
    $mailchimp_settings = $form_state->getValue('mailchimp_for_bene');
    $has_value = $mailchimp_settings['signup_block'];
    if (!$has_value) {
      $form_state->setErrorByName('mailchimp_for_bene', t('A valid MailChimp signup block is required, please create one or choose a different style.'));
    }
  }

  /**
   * Called when submitting the settings form.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param array $form
   *   The form definition array for the full configuration form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitSettingsForm(array &$configuration, array $form, FormStateInterface $form_state) {
    $mailchimp_settings = $form_state->getValue('mailchimp_for_bene');
    $configuration['signup_block'] = $mailchimp_settings['signup_block']['signup_block'];
  }

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
  public function buildEndUserEmailSignup(array $configuration) {
    // Embedded a signup block.
    $moduleHandler = \Drupal::service('module_handler');
    if ($moduleHandler->moduleExists('mailchimp') && $moduleHandler->moduleExists('mailchimp_signup')) {

      $signup = mailchimp_signup_load($configuration['signup_block']);

      if ($signup) {
        $form = new MailchimpSignupPageForm();

        $form_id = 'mailchimp_signup_subscribe_block_' . $signup->id . '_form';
        $form->setFormID($form_id);
        $form->setSignup($signup);

        return \Drupal::formBuilder()->getForm($form);
      }
    }
  }

}
