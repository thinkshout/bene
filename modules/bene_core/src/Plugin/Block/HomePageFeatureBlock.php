<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media_entity\Entity\Media;

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

    $build['feature'] = [
      '#type' => 'fieldset',
    ];

    return $build;
  }

}
