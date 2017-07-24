<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Url;

/**
 * Provides a 'HomePageFeature' block.
 *
 * @Block(
 *  id = "bene_home_page_feature_block",
 *  admin_label = @Translation("Bene Home Page Feature"),
 *  category = @Translation("Bene")
 * )
 */
class HomePageFeatureBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'lead' => '',
      'title' => '',
      'link_label' => '',
      'link_title' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['lead'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lead'),
      '#description' => '',
      '#default_value' => $this->configuration['lead'],
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => '',
      '#default_value' => $this->configuration['title'],
      '#required' => TRUE,
    ];
    $form['link'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Link'),
    ];
    $form['link']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link label'),
      '#description' => '',
      '#default_value' => $this->configuration['link']['label'],
    ];
    $form['link']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Link URL'),
      '#description' => '',
      '#default_value' => $this->configuration['link']['url'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['lead'] = $form_state->getValue('lead');
    $this->configuration['title'] = $form_state->getValue('title');
    $link = $form_state->getValue('link');
    $this->configuration['link'] = [
      'label' => $link['label'],
      'url' => $link['url'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['lead'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['lead'],
      '#prefix' => '<span class="address">',
      '#suffix' => '</span>',
    ];
    $build['title'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['title'],
      '#prefix' => '<span class="title">',
      '#suffix' => '</span>',
    ];
    $build['link'] = [
      '#type' => 'markup',
      '#markup' => '<a href="' . $this->configuration['link']['url'] . '">' . $this->configuration['link']['label'] . '</a>',
    ];

    return $build;
  }

}
