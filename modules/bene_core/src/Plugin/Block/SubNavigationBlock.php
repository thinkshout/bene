<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuLinkManager;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SubNavigationBlock' block.
 *
 * @Block(
 *  id = "bene_sub_navigation_block",
 *  admin_label = @Translation("Bene sub navigation"),
 *  category = @Translation("Bene")
 * )
 */
class SubNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * CurrentRouteMatch service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * MenuLinkManager service.
   *
   * @var \Drupal\Core\Menu\MenuLinkManager*/
  protected $menuLinkManager;

  /**
   * The menu link tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTree;

  /**
   * Constructs a new SubNavigationBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $currentRouteMatch
   *   The CurrentRouteMatch service.
   * @param \Drupal\Core\Menu\MenuLinkManager $menuLinkManager
   *   The MenuLinkManager service.
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menuTree
   *   The menu tree service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              CurrentRouteMatch $currentRouteMatch,
                              MenuLinkManager $menuLinkManager,
                              MenuLinkTreeInterface $menuTree) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentRouteMatch = $currentRouteMatch;
    $this->menuLinkManager = $menuLinkManager;
    $this->menuTree = $menuTree;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('plugin.manager.menu.link'),
      $container->get('menu.link_tree')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = [];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $menu_links = $this->menuLinkManager->loadLinksByRoute($this->currentRouteMatch->getRouteName(), $this->currentRouteMatch->getRawParameters()->all());
    if (empty($menu_links)) {
      return $build;
    }
    $menu_link = reset($menu_links);
    $menu_name = $this->getMenuName();

    $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
    // Only show one level.
    $parameters->setMaxDepth(1);
    // We default to the current item as root.
    $parameters->setRoot($menu_link->getPluginId());

    // Store initial parameters since menuTree->load alters them.
    $fallback_parameters = clone $parameters;

    $tree = $this->menuTree->load($menu_name, $parameters);

    // Check if the returned tree has children. If it doesn't get a new tree
    // starting with the parent of the current item.
    $tree_item = reset($tree);
    if (!$tree_item->hasChildren && $menu_link->getParent()) {
      $fallback_parameters->setRoot($menu_link->getParent());
      $tree = $this->menuTree->load($menu_name, $fallback_parameters);
      $tree_item = reset($tree);
    }

    // If we have at least one child item, render the menu.
    if ($tree_item->hasChildren) {
      $manipulators = [
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
      ];
      $tree = $this->menuTree->transform($tree, $manipulators);
      $build = $this->menuTree->build($tree);
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Even when the menu block renders to the empty string for a user, we want
    // the cache tag for this menu to be set: whenever the menu is changed, this
    // menu block must also be re-rendered for that user, because maybe a menu
    // link that is accessible for that user has been added.
    $cache_tags = parent::getCacheTags();
    if ($menu_name = $this->getMenuName()) {
      $cache_tags[] = 'config:system.menu.' . $menu_name;
    }
    return $cache_tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // ::build() uses MenuLinkTreeInterface::getCurrentRouteMenuTreeParameters()
    // to generate menu tree parameters, and those take the active menu trail
    // into account. Therefore, we must vary the rendered menu by the active
    // trail of the rendered menu.
    // Additional cache contexts, e.g. those that determine link text or
    // accessibility of a menu, will be bubbled automatically.
    if ($menu_name = $this->getMenuName()) {
      return Cache::mergeContexts(parent::getCacheContexts(), ['route.menu_active_trails:' . $menu_name]);
    }
    else {
      return parent::getCacheContexts();
    }
  }

  /**
   * Get the menu name for the current page.
   */
  private function getMenuName() {
    $menu_name = FALSE;
    $menu_links = $this->menuLinkManager->loadLinksByRoute($this->currentRouteMatch->getRouteName(), $this->currentRouteMatch->getRawParameters()->all());
    if ($menu_links) {
      $menu_link = reset($menu_links);
      $menu_name = $menu_link->getMenuName();
    }

    return $menu_name;
  }

}
