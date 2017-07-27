<?php

namespace Drupal\bene_core\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;

/**
 * Provides a 'SubNavigationBlock' block.
 *
 * @Block(
 *  id = "bene_sub_navigation_block",
 *  admin_label = @Translation("Bene Sub Navigation"),
 *  category = @Translation("Bene")
 * )
 */
class SubNavigationBlock extends BlockBase {

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
    $build = [
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    // Add breadcrumbs excluding current page.
    $breadcrumbs = $this->getBreadcrumbs();
    $breadcrumb_links = [];
    /** @var Link $breadcrumb */
    foreach ($breadcrumbs as $breadcrumb) {
      $breadcrumb_links[] = Link::fromTextAndUrl($breadcrumb->getText(), $breadcrumb->getUrl());
    }

    $build['breadcrumbs'] = [
      '#theme' => 'item_list',
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
      ];
    }

    $page_child_links = [];

    $page_children = $this->getChildMenuLinks();
    foreach ($page_children as $page_child) {
      $page_child_links[] = Link::createFromRoute($page_child['title'], $page_child['route_name'], $page_child['route_parameters']);
    }

    $build['page_children'] = [
      '#theme' => 'item_list',
      '#items' => $page_child_links,
    ];

    return $build;
  }

  /**
   * Gets an array of breadcrumb links up to (not including) the current page.
   *
   * @return array
   *   Array of Link objects representing breadcrumbs.
   */
  private function getBreadcrumbs() {
    /** @var \Drupal\Core\Routing\RouteMatch $current_route */
    $current_route = \Drupal::service('current_route_match')->getCurrentRouteMatch();
    $breadcrumbs = \Drupal::service('breadcrumb')->build($current_route)->getLinks();

    return $breadcrumbs;
  }

  /**
   * Gets the title of the current page.
   *
   * @return array or string
   *   The page title as a string or render array.
   *
   * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Controller%21TitleResolver.php/class/TitleResolver/8.2.x
   */
  private function getPageTitle() {
    /** @var \Drupal\Core\Routing\RouteMatch $current_route */
    $current_route = \Drupal::service('current_route_match')->getCurrentRouteMatch();
    $title = \Drupal::service('title_resolver')->getTitle(\Drupal::request(), $current_route->getRouteObject());

    return $title;
  }

  /**
   * Gets an array of menu link definitions for child nodes of the current node.
   *
   * Based on the current node's menu link.
   *
   * @return array
   *   Array of menu link definitions (each definition is in array format).
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function getChildMenuLinks() {
    $child_menu_links = [];

    // Get the current node from the current route.
    /** @var \Drupal\node\Entity\Node $node */
    $node = \Drupal::routeMatch()->getParameter('node');
    if (!empty($node)) {
      /** @var \Drupal\Core\Menu\MenuLinkManager $menu_link_manager */
      $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
      $menu_links = $menu_link_manager->loadLinksByRoute('entity.node.canonical', ['node' => $node->id()]);

      // Get the menu link associated with the current node.
      /** @var \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent $current_menu_link */
      $current_menu_link = reset($menu_links);

      // Get all child menu links of the current node's menu link.
      if (!empty($current_menu_link)) {
        $child_menu_ids = $menu_link_manager->getChildIds($current_menu_link->getPluginId());

        foreach ($child_menu_ids as $menu_link_id) {
          $child_menu_links[] = $menu_link_manager->getDefinition($menu_link_id);
        }
      }
    }

    return $child_menu_links;
  }

}
