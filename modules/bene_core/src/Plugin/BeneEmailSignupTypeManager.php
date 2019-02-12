<?php

namespace Drupal\bene_core\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Bene email signup type plugin manager.
 *
 * Provides a way to manage email sign up plugins, for example you might have
 * Mailchimp or Salsa email list service plugins. Defines a new plugin type
 * and how instances will be discovered, instantiated, etc.
 *
 * The plugin manager is also declared as a service in
 * bene_core.services.yml so it can be easily accessed and used
 * anytime we need to work with BeneEmailSignupType plugins.
 */
class BeneEmailSignupTypeManager extends DefaultPluginManager {
  /**
   * Constructs a new BeneEmailSignupTypeManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */

  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    // We replace the $subdir parameter with our own value.
    // This tells the plugin manager to look for BeneEmailSignupType plugins in
    // the 'src/Plugin/BeneEmailSignupType' subdirectory of any enabled modules.
    // This also serves to define the PSR-4 subnamespace in which
    // BeneEmailSignup plugins will live. Example:
    // Drupal\bene_core\Plugin\BeneEmailSignupType\BeneMailchimpPlugin.
    // Drupal\bene_core\Plugin\BeneEmailSignupType\BeneSalsaPlugin.
    $subdir = 'Plugin/BeneEmailSignupType';

    // The name of the interface that plugins should adhere to. Drupal will
    // enforce this as a requirement. If a plugin does not implement this
    // interface, than Drupal will throw an error.
    $plugin_interface = 'Drupal\bene_core\Plugin\BeneEmailSignupTypeInterface';

    // The name of the annotation class that contains the plugin definition.
    $plugin_definition_annotation_name = 'Drupal\bene_core\Annotation\BeneEmailSignupType';

    parent::__construct($subdir, $namespaces, $module_handler, $plugin_interface, $plugin_definition_annotation_name);

    // This allows the plugin definitions to be altered by an alter hook. The
    // parameter defines the name of the hook, thus:
    // hook_bene_core_bene_email_signup_type_info_alter().
    $this->alterInfo('bene_core_bene_email_signup_type_info');

    // This sets the caching method for our plugin definitions. Plugin
    // definitions are discovered by examining the $subdir defined above, for
    // any classes with a $plugin_definition_annotation_name. The annotations
    // are read, and then the resulting data is cached using the provided cache
    // backend. For our BeneEmailSignupType plugin type, we've specified the
    // cache.default service be used in the bene_core.services.yml file. The 2nd
    // argument is a cache key prefix. Out of the box Drupal with the default
    // cache backend setup will store our plugin definition in the cache_default
    // table using the bene_email_signup_type_info key. All that is
    // implementation details however, all we care about is that caching for
    // our plugin definition is taken care of by this call.
    $this->setCacheBackend($cache_backend, 'bene_core_bene_email_signup_type_plugins');
  }

}
