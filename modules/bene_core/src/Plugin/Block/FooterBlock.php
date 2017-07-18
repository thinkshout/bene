<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuTreeParameters;
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
      'address' => '1234 Some Street
Portland, OR 97209',
      'phone' => '(123) 123-1234',
      'email' => 'info@synergygive.com',
      'additional_contact' => [
        'value' => '<p>Photograph by Some Person</p>',
        'format' => 'restricted_html',
      ],
      'additional_footer' => [
        'value' => '<p>Office hours: 8-4p Pacific</p>',
        'format' => 'restricted_html',
      ],
      'copyright' => '&copy; Copyright 2017 SynergyGive',
      'facebook' => 'https://facebook.com',
      'google' => 'https://google.com',
      'instagram' => 'https://instagram.com',
      'linkedin' => 'https://linkedin.com',
      'pinterest' => 'https://pinterest.com',
      'tumblr' => 'https://tumblr.com',
      'twitter' => 'https://twitter.com',
      'youtube' => 'https://youtube.com',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $footer_menu = \Drupal::menuTree()->load('footer', new MenuTreeParameters());
    $form['menu_preview'] = [
      '#type' => 'fieldset',
      '#title' => t('Footer Menu'),
    ];
    $form['menu_preview']['menu'] = \Drupal::menuTree()->build($footer_menu);
    $form['menu_preview']['menu_edit'] = [
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
    $form['additional_contact'] = [
      '#type' => 'text_format',
      '#title' => 'Additional contact content',
      '#format' => $this->configuration['additional_contact']['format'],
      '#default_value' => $this->configuration['additional_contact']['value'],
      '#weight' => '13',
    ];
    $form['additional_footer'] = [
      '#type' => 'text_format',
      '#title' => 'Additional footer content',
      '#format' => $this->configuration['additional_contact']['format'],
      '#default_value' => $this->configuration['additional_footer']['value'],
      '#weight' => '14',
    ];
    $form['copyright'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Copyright'),
      '#description' => '',
      '#default_value' => $this->configuration['copyright'],
      '#weight' => '15',
    ];
    $form['social'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Social media'),
      '#weight' => '16',
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
    $this->configuration['additional_contact'] = $form_state->getValue('additional_contact');
    $this->configuration['additional_footer'] = $form_state->getValue('additional_footer');
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

    // Build and render the footer menu.
    $menu_tree = \Drupal::menuTree();
    $menu_name = 'footer';

    // Build the typical default set of menu tree parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);

    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);

    // Transform the tree using the manipulators you want.
    $manipulators = [
      // Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);

    // Finally, build a renderable array from the transformed tree.
    $build['menu'] = $menu_tree->build($tree);
    $build['menu']['#weight'] = 0;
    $build['contact'] = [
      '#type' => 'container',
      '#weight' => 1,
      '#attributes' => [
        'class' => 'contact-links',
      ],
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
    $build['contact']['additional_contact'] = [
      '#type' => 'processed_text',
      '#text' => $this->configuration['additional_contact']['value'],
      '#format' => $this->configuration['additional_contact']['format'],
      '#prefix' => '<span class="additional-contact">',
      '#suffix' => '</span>',
    ];
    $build['additional_footer'] = [
      '#type' => 'processed_text',
      '#text' => $this->configuration['additional_footer']['value'],
      '#format' => $this->configuration['additional_footer']['format'],
      '#prefix' => '<span class="additional-footer">',
      '#suffix' => '</span>',
      '#weight' => 2,
    ];

    $build['social'] = [
      '#type' => 'container',
      '#weight' => 3,
      '#attributes' => [
        'class' => 'social-links',
      ],
    ];
    foreach ($services as $service) {
      if ($this->configuration[$service]) {
        $build['social'][$service]['#markup'] = '<a class="' . $service . '" href="' . $this->configuration[$service] . '">' . $service . '</a>';
      }
    }
    $build['copyright'] = [
      '#type' => 'markup',
      '#weight' => 4,
      '#markup' => $this->configuration['copyright'],
      '#prefix' => '<span class="copyright">',
      '#suffix' => '</span>',
    ];
    return $build;
  }

}
