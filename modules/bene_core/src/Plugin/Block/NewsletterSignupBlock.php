<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

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
      'style' => '1',
      'title' => 'Sign up for our Newsletter',
      'signup_text' => 'Receive updates about what we are doing',
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
        0 => $this->t('Disabled'),
        1 => $this->t('External'),
        2 => $this->t('Embedded Form'),
      ],
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => '',
      '#default_value' => $this->configuration['title'],
    ];
    $form['signup_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Signup text'),
      '#default_value' => $this->configuration['signup_text'],
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
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    return $build;
  }

}
