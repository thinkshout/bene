<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a 'FooterBlock' block.
 *
 * @Block(
 *  id = "bene_footer_block",
 *  admin_label = @Translation("Bene Footer"),
 *  category = @Translation("Bene")
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'label_display' => '0',
      'address' => '',
      'phone' => '',
      'email' => '',
      'copyright' => '',
      'facebook' => '',
      'google' => '',
      'instagram' => '',
      'linkedin' => '',
      'pinterest' => '',
      'tumblr' => '',
      'twitter' => '',
      'youtube' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['menu_edit'] = [
      '#type' => 'markup',
      '#markup' => $this->t('To customize the menu in the footer, click here.'),
      '#prefix' => '<a class="menu-edit" href="/admin/structure/menu/manage/footer" target="_blank">',
      '#suffix' => '</a>',
    ];
    $form['address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address'),
      '#description' => '',
      '#default_value' => $this->configuration['address'],
      '#rows' => 5,
      '#cols' => 5,
      '#weight' => '10',
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
      '#description' => '',
      '#default_value' => $this->configuration['phone'],
      '#weight' => '11',
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => '',
      '#default_value' => $this->configuration['email'],
      '#weight' => '12',
    ];
    $form['copyright'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Copyright'),
      '#description' => '',
      '#default_value' => $this->configuration['copyright'],
      '#weight' => '13',
    ];
    $form['social'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Social media'),
      '#weight' => '14',
    ];
    $form['social']['facebook'] = [
      '#type' => 'url',
      '#title' => $this->t('Facebook'),
      '#description' => '',
      '#default_value' => $this->configuration['facebook'],
    ];
    $form['social']['google'] = [
      '#type' => 'url',
      '#title' => $this->t('Google+'),
      '#description' => '',
      '#default_value' => $this->configuration['google'],
    ];
    $form['social']['instagram'] = [
      '#type' => 'url',
      '#title' => $this->t('Instagram'),
      '#description' => '',
      '#default_value' => $this->configuration['instagram'],
    ];
    $form['social']['linkedin'] = [
      '#type' => 'url',
      '#title' => $this->t('LinkedIn'),
      '#description' => '',
      '#default_value' => $this->configuration['linkedin'],
    ];
    $form['social']['pinterest'] = [
      '#type' => 'url',
      '#title' => $this->t('Pinterest'),
      '#description' => '',
      '#default_value' => $this->configuration['pinterest'],
    ];
    $form['social']['tumblr'] = [
      '#type' => 'url',
      '#title' => $this->t('Tumblr'),
      '#description' => '',
      '#default_value' => $this->configuration['tumblr'],
    ];
    $form['social']['twitter'] = [
      '#type' => 'url',
      '#title' => $this->t('Twitter'),
      '#description' => '',
      '#default_value' => $this->configuration['twitter'],
    ];
    $form['social']['youtube'] = [
      '#type' => 'url',
      '#title' => $this->t('YouTube'),
      '#description' => '',
      '#default_value' => $this->configuration['youtube'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $social = $form_state->getValue('social');
    $this->configuration['address'] = $form_state->getValue('address');
    $this->configuration['phone'] = $form_state->getValue('phone');
    $this->configuration['email'] = $form_state->getValue('email');
    $this->configuration['copyright'] = $form_state->getValue('copyright');
    $this->configuration['facebook'] = $social['facebook'];
    $this->configuration['google'] = $social['google'];
    $this->configuration['instagram'] = $social['instagram'];
    $this->configuration['linkedin'] = $social['linkedin'];
    $this->configuration['pinterest'] = $social['pinterest'];
    $this->configuration['tumblr'] = $social['tumblr'];
    $this->configuration['twitter'] = $social['twitter'];
    $this->configuration['youtube'] = $social['youtube'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $services = [
      'facebook',
      'google',
      'instagram',
      'linkedin',
      'pinterest',
      'tumblr',
      'twitter',
      'youtube',
    ];
    $build = [];
    $build['#attached'] = [
      'library' => [
        'bene_core/footer-block',
      ],
    ];
    $build['contact'] = [
      '#type' => 'fieldset',
      '#prefix' => '<div class="bene-contact-links">',
      '#suffix' => '</div>',
    ];
    $build['contact']['address'] = [
      '#type' => 'markup',
      '#markup' => $this->configuration['address'],
      '#prefix' => '<span class="address">',
      '#suffix' => '</span>',
    ];
    $build['contact']['phone'] = [
      '#type' => 'link',
      '#title' => $this->configuration['phone'],
      // Prepend 'tel:' to the telephone number.
      '#url' => Url::fromUri('tel:' . rawurlencode(preg_replace('/\s+/', '', $this->configuration['phone']))),
      '#options' => ['external' => TRUE],
      '#prefix' => '<span class="phone">',
      '#suffix' => '</span>',
    ];
    $build['contact']['email'] = [
      '#type' => 'link',
      '#title' => $this->configuration['email'],
      // Prepend 'mailto:' to the email address.
      '#url' => Url::fromUri('mailto:' . $this->configuration['email']),
      '#options' => ['external' => TRUE],
      '#prefix' => '<span class="email">',
      '#suffix' => '</span>',
    ];
    $build['social'] = [
      '#type' => 'fieldset',
      '#prefix' => '<div class="bene-social-links">',
      '#suffix' => '</div>',
    ];
    foreach ($services as $service) {
      if ($this->configuration[$service]) {
        $build['social'][$service]['#markup'] = '<a class="' . $service . '" href="' . $this->configuration[$service] . '">' . $service . '</a>';
      }
    }
    return $build;
  }

}
