<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'HomePageFeature' block.
 *
 * @Block(
 *  id = "bene_home_page_feature_block",
 *  admin_label = @Translation("Bene home page feature"),
 *  category = @Translation("Bene")
 * )
 */
class HomePageFeatureBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'block_id' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['block_id'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'block_content',
      '#selection_settings' => [
        'target_bundles' => ['bene_background_image_hero'],
      ],
      '#title' => $this->t('Background image hero block'),
      '#default_value' => $this->configuration['block_id'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['block_id'] = $form_state->getValue('block_id');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $block_id = $this->configuration['block_id'];

    if (!empty($block_id)) {
      $block = \Drupal::entityTypeManager()->getStorage('block_content')->load($block_id);

      if (!empty($block)) {
        $rendered_block = render(\Drupal::entityTypeManager()
          ->getViewBuilder('block_content')
          ->view($block));

        $build['block'] = [
          '#type' => 'markup',
          '#markup' => $rendered_block,
        ];
      }
    }

    return $build;
  }

}