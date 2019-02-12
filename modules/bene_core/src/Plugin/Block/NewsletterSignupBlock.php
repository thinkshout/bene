<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\bene_core\Plugin\BeneEmailSignupTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'NewsletterSignupBlock' block.
 *
 * @Block(
 *  id = "bene_newsletter_signup_block",
 *  admin_label = @Translation("Bene Newsletter Signup"),
 *  category = @Translation("Bene")
 * )
 */
class NewsletterSignupBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The BeneEmailSignupTypeManager plugin manager.
   *
   * We use this to get all of the BeneEmailSignupType plugins.
   *
   * @var \Drupal\bene_core\Plugin\BeneEmailSignupTypeManager
   */
  protected $beneEmailSignupManager;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\bene_core\Plugin\BeneEmailSignupTypeManager $bene_email_signup_manager
   *   The Bene email signup type manager service. We're injecting this service
   *   so that we can use it to access the Bene email signup type plugins.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, BeneEmailSignupTypeManager $bene_email_signup_manager) {
    $this->beneEmailSignupManager = $bene_email_signup_manager;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   *
   * Override the parent method so we can inject our BeneEmailSignupType
   * plugin manager service into the controller. Dependency injection.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Inject the plugin.manager.bene_email_signup_type service
    // that represents our plugin manager as defined in the
    // bene_core.services.yml file.
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('plugin.manager.bene_email_signup_type'));
  }

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
      ],
    ];

    // Get the list of all the BeneEmailSignupType plugins defined on the system
    // from plugin manager. Note that at this point, what we have is
    // *definitions* of plugins, not the plugins themselves.
    $bene_email_signup_type_definitions = $this->beneEmailSignupManager->getDefinitions();

    // Output a list of the plugin definitions we now have, as radio buttons.
    foreach ($bene_email_signup_type_definitions as $bene_email_signup_type_definition) {
      // Here we use various properties from the plugin definition. These values
      // are defined in the annotation at the top of the plugin class: see
      // \Drupal\bene_core\Plugin\BeneEmailSignupType\BeneMailchimpPlugin.
      $plugin_id = $bene_email_signup_type_definition['id'];
      $form['style']['#options'][$plugin_id] = $bene_email_signup_type_definition['title'];
    }

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

    foreach ($bene_email_signup_type_definitions as $plugin_id => $bene_email_signup_type_definition) {
      // We now have a plugin instance. From here on it can be treated just as
      // any other object; have its properties examined, methods called, etc.
      $form[$plugin_id] = [
        '#type' => 'fieldset',
        '#title' => $bene_email_signup_type_definition['title'],
        '#states' => [
          'visible' => [
            ':input[name="settings[style]"]' => ['value' => $plugin_id],
          ],
        ],
      ];

      $plugin = $this->beneEmailSignupManager->createInstance($plugin_id);
      $form[$plugin_id]['settings'] = $plugin->settingsForm($this->configuration);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::validateConfigurationForm($form, $form_state);

    $style = $form_state->getValue('style');
    $bene_email_signup_type_definition = $this->beneEmailSignupManager->getDefinition($style, FALSE);
    if (isset($bene_email_signup_type_definition)) {
      $plugin = $this->beneEmailSignupManager->createInstance($style);
      $plugin->validateSettingsForm($this->configuration, $form_state);
    }
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

    $style = $form_state->getValue('style');
    $bene_email_signup_type_definition = $this->beneEmailSignupManager->getDefinition($style, FALSE);
    if (isset($bene_email_signup_type_definition)) {
      $plugin = $this->beneEmailSignupManager->createInstance($style);
      $plugin->submitSettingsForm($this->configuration, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $style = $this->configuration['style'];

    if ($style == 'external' || $style == 'embedded') {
      $build['signup'] = [
        '#type' => 'container',
        '#weight' => 1,
        '#attributes' => [
          'class' => 'external-newsletter',
        ],
      ];
      if ($this->configuration['title']) {
        $build['signup']['title'] = [
          '#type' => 'markup',
          '#markup' => $this->configuration['title'],
          '#prefix' => '<h2>',
          '#suffix' => '</h2>',
        ];
      }
      if ($this->configuration['signup_text']) {
        $build['signup']['signup_text'] = [
          '#type' => 'markup',
          '#markup' => $this->configuration['signup_text'],
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        ];
      }
    }
    switch ($style) {
      case 'external':

        // External link.
        if ($this->configuration['signup_text'] || $this->configuration['title']) {
          $build['signup']['link'] = [
            '#type' => 'link',
            '#title' => $this->configuration['external_link_label'],
            '#url' => Url::fromUri($this->configuration['external_link']),
            '#attributes' => [
              'class' => 'button',
            ],
          ];
        }
        else {
          $build['signup']['link'] = [
            '#type' => 'link',
            '#title' => $this->configuration['external_link_label'],
            '#url' => Url::fromUri($this->configuration['external_link']),
          ];
        };

        break;

      default:
        // For a plugin example see
        // \Drupal\bene_core\Plugin\BeneEmailSignupType\BeneMailchimpPlugin.
        $bene_email_signup_type_definition = $this->beneEmailSignupManager->getDefinition($style, FALSE);
        if (isset($bene_email_signup_type_definition)) {
          $plugin = $this->beneEmailSignupManager->createInstance($style);
          $build['signup']['form'] = $plugin->buildEndUserEmailSignup($this->configuration);
        }
        break;
    }

    return $build;
  }

}
