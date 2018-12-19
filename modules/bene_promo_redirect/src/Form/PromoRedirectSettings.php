<?php

namespace Drupal\bene_promo_redirect\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Custom Texts.
 */
class PromoRedirectSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bene_promo_redirect_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['bene_promo_redirect.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Add form header describing purpose and use of form.
    $form['header'] = [
      '#type' => 'markup',
      '#markup' => t('<h3>Temporarily redirect your homepage, or all pages, to a promotional or campaign page within our without the site.</h3><p>Use carefully! Misconfiguration can result in frustration.</p>'),
    ];

    $settings = $this->config('bene_promo_redirect.settings')->get();
    $form['dest'] = [
      '#title' => 'Redirect Destination',
      '#type' => 'textfield',
      '#default_value' => isset($settings['dest']) ? $settings['dest'] : '',
      '#description' => t('The URL to send visitors to. Start with "http" if external.'),
      // @todo validation
    ];

    $form['redirect_all'] = [
      '#title' => 'When should the redirect be applied?',
      '#type' => 'radios',
      '#default_value' => isset($settings['redirect_all']) ? $settings['redirect_all'] : 1,
      '#options' => [
        0 => 'Specific pages',
        1 => 'All Pages',
      ],
      '#description' => t('Administrative pages and login pages are never redirected. Adding "?promo_redirect=no" to the end of any path on the site will block the redirect as well.'),
    ];

    $form['paths'] = [
      '#title' => "Paths to redirect",
      '#type' => 'textarea',
      '#default_value' => isset($settings['paths']) ? implode("\r\n", $settings['paths']) : '/',
      '#description' => t('Place one path per line. Do not include the "HTTP" and the domain. For example, enter "/" to redirect just the homepage.'),
      '#states' => [
        'visible' => [
          ':input[name="redirect_all"]' => ['value' => 0],
        ],
      ],
    ];

    $form['enabled'] = [
      '#title' => "Activate Redirect",
      '#type' => 'checkbox',
      '#default_value' => isset($settings['enabled']) ? $settings['enabled'] : FALSE,
    ];

    $form['actions']['submit']['#value'] = t('Save');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $settings = $this->configFactory()->getEditable('bene_promo_redirect.settings');
    $values = $form_state->cleanValues()->getValues();
    foreach ($values as $field_key => $field_value) {
      switch ($field_key) {
        case "paths":
          $field_value = explode("\r\n", $field_value);
          break;

      }
      $settings->set($field_key, $field_value);
    }
    $settings->save();
    parent::submitForm($form, $form_state);
  }

}
