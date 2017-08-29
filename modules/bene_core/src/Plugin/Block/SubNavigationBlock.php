<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Breadcrumb\BreadcrumbManager;
use Drupal\Core\Controller\TitleResolver;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Menu\MenuLinkManager;
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
   * BreadcrumbManager service.
   *
   * @var \Drupal\Core\Breadcrumb\BreadcrumbManager
   */
  protected $breadcrumbManager;

  /**
   * TitleResolver service.
   *
   * @var \Drupal\Core\Controller\TitleResolver
   */
  protected $titleResolver;

  /**
   * MenuLinkManager service.
   *
   * @var \Drupal\Core\Menu\MenuLinkManager*/
  protected $menuLinkManager;

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
   * @param \Drupal\Core\Breadcrumb\BreadcrumbManager $breadcrumbManager
   *   The BreadcrumbManager service.
   * @param \Drupal\Core\Controller\TitleResolver $titleResolver
   *   The TitleResolver service.
   * @param \Drupal\Core\Menu\MenuLinkManager $menuLinkManager
   *   The MenuLinkManager service.
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              CurrentRouteMatch $currentRouteMatch,
                              BreadcrumbManager $breadcrumbManager,
                              TitleResolver $titleResolver,
                              MenuLinkManager $menuLinkManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->currentRouteMatch = $currentRouteMatch;
    $this->breadcrumbManager = $breadcrumbManager;
    $this->titleResolver = $titleResolver;
    $this->menuLinkManager = $menuLinkManager;
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
      $container->get('breadcrumb'),
      $container->get('title_resolver'),
      $container->get('plugin.manager.menu.link')
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
    // Disable block cache as page title will change per-page.
    // TODO: Review this for potential caching with tags based on path.
    $build = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    // Add breadcrumbs excluding current page.
    $breadcrumbs = $this->getBreadcrumbs();
    $breadcrumb_links = [];
    /** @var \Drupal\Core\Link $breadcrumb */
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb_links[] = Link::fromTextAndUrl($breadcrumb->getText(), $breadcrumb->getUrl());
    }

    $build['breadcrumbs'] = [
      '#theme' => 'item_list',
      '#wrapper_attributes' => [
        'class' => [
          'bene-subnav-breadcrumbs',
        ],
      ],
      '#items' => $breadcrumb_links,
    ];

    // Add current page title.
    $page_title = $this->getPageTitle();
    // Account for the page title being returned as either a string or render
    // array.
    if (is_array($page_title)) {
      $build['page_title'] = $page_title;
    }
    else {
      $build['page_title'] = [
        '#type' => 'markup',
        '#markup' => $this->getPageTitle(),
        '#prefix' => '<div class="bene-subnav-page-title">',
        '#suffix' => '</div>',
      ];
    }

    $page_child_links = [];

    $page_children = $this->getChildMenuLinks();
    if (!empty($page_children)) {
      foreach ($page_children as $page_child) {
        $page_child_links[] = Link::createFromRoute($page_child['title'], $page_child['route_name'], $page_child['route_parameters']);
      }

      $build['page_children'] = [
        '#theme' => 'item_list',
        '#wrapper_attributes' => [
          'class' => [
            'bene-subnav-children',
          ],
        ],
        '#items' => $page_child_links,
      ];
    }

    return $build;
  }

  /**
   * Gets an array of breadcrumb links up to (not including) the current page.
   *
   * @return array
   *   Array of Link objects representing breadcrumbs.
   */
  private function getBreadcrumbs() {
    $current_route = $this->currentRouteMatch->getCurrentRouteMatch();
    $breadcrumbs = $this->breadcrumbManager->build($current_route)->getLinks();

    // Remove 'Home' link from breadcrumbs.
    if (isset($breadcrumbs[0]) && $breadcrumbs[0]->getUrl()->getRouteName() == '<front>') {
      unset($breadcrumbs[0]);
      // Reset array indexes.
      $breadcrumbs = array_values($breadcrumbs);
    }

    return $breadcrumbs;
  }

  /**
   * Gets the title of the current page.
   *
   * @return array|string
   *   The page title as a string or render array.
   *
   * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Controller%21TitleResolver.php/class/TitleResolver/8.2.x
   */
  private function getPageTitle() {
    $current_route = $this->currentRouteMatch->getCurrentRouteMatch();
    $title = $this->titleResolver->getTitle(\Drupal::request(), $current_route->getRouteObject());

    return $title;
  }

  /**
   * Gets an array of menu link definitions for child links of the current item.
   *
   * Based on the current entity's menu link.
   *
   * @return array
   *   Array of menu link definitions (each definition is in array format).
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getChildMenuLinks() {
    $child_menu_links = [];

    // Get the current entity from the current route.
    $route = \Drupal::routeMatch();
    $route_name = $route->getRouteName();
    $route_parameters = \Drupal::routeMatch()->getParameters()->all();
    $route_entity = reset($route_parameters);

    if (!empty($route_entity) && is_object($route_entity)) {
      $menu_links = $this->menuLinkManager->loadLinksByRoute($route_name, [$route_entity->getEntityTypeId() => $route_entity->id()]);

      // Get the menu link associated with the current entity.
      /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $current_menu_link */
      $current_menu_link = reset($menu_links);

      // Get all child menu links of the current entity's menu link.
      if (!empty($current_menu_link)) {
        $child_menu_ids = $this->menuLinkManager->getChildIds($current_menu_link->getPluginId());

        foreach ($child_menu_ids as $menu_link_id) {
          $child_menu_link = $this->menuLinkManager->getDefinition($menu_link_id);

          // Only display first-level child links.
          if ($child_menu_link['parent'] == $current_menu_link->getPluginId()) {
            $child_menu_links[] = $this->menuLinkManager->getDefinition($menu_link_id);
          }
        }
      }
    }

    return $child_menu_links;
  }

}
