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

    // TODO: Is this the best way to use an entity reference field with a block?
    $form['background_image'] = [
      '#title' => 'Background image',
      '#description' => '',
      '#field_parents' => [],
      '#required' => FALSE,
      '#delta' => 0,
      '#id' => 'edit-field-background-image-media',
      '#type' => 'details',
      '#open' => TRUE,
      'target_id' => [
        '#type' => 'hidden',
        '#id' => 'edit-field-background-image-media-target-id',
        '#attributes' => [
          'id' => 'edit-field-background-image-media-target-id',
        ],
        '#default_value' => '',
        '#ajax' => [
          'callback' => [
            0 => 'Drupal\\bene_media\\Plugin\\Field\\FieldWidget\\EntityReferenceBrowserWidget',
            1 => 'updateWidgetCallback',
          ],
          'wrapper' => 'edit-field-background-image-media',
          'event' => 'entity_browser_value_updated',
        ],
      ],
      'entity_browser' => [
        '#type' => 'entity_browser',
        '#entity_browser' => 'media_browser_modal',
        '#cardinality' => 1,
        '#selection_mode' => 'selection_append',
        '#default_value' => [],
        '#entity_browser_validators' => [
          'entity_type' => [
            'type' => 'media',
          ],
        ],
        '#widget_context' => [
          'target_bundles' => [
            'image' => 'image',
          ],
        ],
        '#custom_hidden_id' => 'edit-field-background-image-media-target-id',
        '#process' => [
          0 => [
            0 => '\\Drupal\\entity_browser\\Element\\EntityBrowserElement',
            1 => 'processEntityBrowser',
          ],
          1 => [
            0 => 'Drupal\\bene_media\\Plugin\\Field\\FieldWidget\\EntityReferenceBrowserWidget',
            1 => 'processEntityBrowser',
          ],
        ],
      ],
      '#attached' => [
        'library' => [
          0 => 'entity_browser/entity_reference',
        ],
      ],
      'current' => [
        '#theme_wrappers' => [
          0 => 'container',
        ],
        '#attributes' => [
          'class' => [
            0 => 'entities-list',
          ],
        ],
        '#prefix' => '<p>You can select one media.</p>',
        'items' => [],
      ],
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

    // Saving the background image entity reference value as a string here,
    // to be converted when rendered. Looks like this:
    // media:4
    // TODO: Better way to do this?
    $background_image = $form_state->getValue('background_image');
    $this->configuration['background-image'] = $background_image['target_id'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $background_image_path = '';
    if (!empty($this->configuration['background-image'])) {
      // 'background-image' contains an entity reference ID. It looks like this:
      // media:4, where 4 is the media entity ID. Split the string to get it.
      $background_image_parts = explode(':', $this->configuration['background-image']);

      // Load the background image media entity and retrieve file path.
      /** @var Media $background_media */
      $background_media = \Drupal::entityManager()->getStorage('media')->load($background_image_parts[1]);
      /** @var File $file */
      $file = File::load($background_media->get('image')->getValue()[0]['target_id']);
      $background_uri = $file->getFileUri();

      $background_image_path = file_create_url($background_uri);
    }

    $build['feature'] = [
      '#type' => 'fieldset',
      '#id' => 'bene-home-page-feature',
      '#attributes' => [
        'data-background' => $background_image_path,
      ],
    ];

    $build['feature']['lead'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['lead'],
      '#prefix' => '<span class="lead">',
      '#suffix' => '</span>',
    ];
    $build['feature']['title'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['title'],
      '#prefix' => '<span class="title">',
      '#suffix' => '</span>',
    ];
    $build['feature']['link'] = [
      '#type' => 'markup',
      '#markup' => '<a href="' . $this->configuration['link']['url'] . '">' . $this->configuration['link']['label'] . '</a>',
    ];

    return $build;
  }

}
