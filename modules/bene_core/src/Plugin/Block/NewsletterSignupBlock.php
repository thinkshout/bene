<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'NewsletterSignupBlock' block.
 *
 * @Block(
 *  id = "bene_newsletter_signup_block",
 *  admin_label = @Translation("Bene Newsletter Signup"),
 *  category = @Translation("Bene")
 * )
 */
class NewsletterSignupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => '0',
      'style' => 'external',
      'title' => 'Sign up for our Newsletter',
      'signup_text' => 'Receive updates about what we are doing',
      'external_link' => 'https://mailchimp.com',
      'external_link_label' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['style'] = [
      '#type' => 'radios',
      '#title' => $this->t('Style'),
      '#default_value' => $this->configuration['style'],
      '#options' => [
        'disabled' => $this->t('Disabled'),
        'external' => $this->t('External'),
        'embedded' => $this->t('Embedded Form'),
      ],
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['title'],
      '#description' => $this->t('Optional, leave blank to use the link label as the only text.'),
    ];
    $form['signup_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Signup text'),
      '#default_value' => $this->configuration['signup_text'],
      '#description' => $this->t('Optional, leave blank to use the link label as the only text.'),
    ];
    $form['external_link_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('External link settings'),
      '#states' => [
        'visible' => [
          ':input[name="settings[style]"]' => ['value' => 'external'],
        ],
      ],
    ];
    $form['external_link_settings']['external_link'] = [
      '#type' => 'url',
      '#title' => $this->t('Link'),
      '#default_value' => $this->configuration['external_link'],
      '#required' => TRUE,
    ];
    $form['external_link_settings']['external_link_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link label'),
      '#default_value' => $this->configuration['external_link_label'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['style'] = $form_state->getValue('style');
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['signup_text'] = $form_state->getValue('signup_text');

    $external_link_settings = $form_state->getValue('external_link_settings');
    $this->configuration['external_link'] = $external_link_settings['external_link'];
    $this->configuration['external_link_label'] = $external_link_settings['external_link_label'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    switch ($this->configuration['style']) {
      case 'external':
        // External link.
        $build['external'] = [
          '#type' => 'container',
          '#weight' => 1,
          '#attributes' => [
            'class' => 'external-newsletter',
          ],
        ];
        if ($this->configuration['title']) {
          $build['external']['title'] = [
            '#type' => 'markup',
            '#markup' => $this->configuration['title'],
            '#prefix' => '<h4>',
            '#suffix' => '</h4>',
          ];
        }
        if ($this->configuration['signup_text']) {
          $build['external']['signup_text'] = [
            '#type' => 'markup',
            '#markup' => $this->configuration['signup_text'],
            '#prefix' => '<p>',
            '#suffix' => '</p>',
          ];
        }
        if ($this->configuration['signup_text'] || $this->configuration['title']) {
          $build['external']['link'] = [
            '#type' => 'link',
            '#title' => $this->configuration['external_link_label'],
            '#url' => Url::fromUri($this->configuration['external_link']),
            '#attributes' => [
              'class' => 'button',
            ],
          ];
        }
        else {
          $build['external']['link'] = [
            '#type' => 'link',
            '#title' => $this->configuration['external_link_label'],
            '#url' => Url::fromUri($this->configuration['external_link']),
          ];
        };
        break;

      case 'embedded':
        // Embedded link.
        // TODO: Define "Embedded" display. On hold per GitHub issue #69.
        $build = [];
        $build['embedded'] = [
          '#type' => 'container',
          '#weight' => 1,
          '#attributes' => [
            'class' => 'embedded-newsletter',
          ],
        ];
        break;

      default:
        // No link.
        $build = [];
    }

    return $build;
  }

}
