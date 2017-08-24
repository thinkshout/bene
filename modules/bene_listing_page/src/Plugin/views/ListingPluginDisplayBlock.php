<?php

namespace Drupal\bene_listing_page\Plugin\views;

use Drupal\views\Plugin\views\display\Block;
use Drupal\views\Plugin\views\display\DisplayPluginBase;

/**
 * The plugin that handles a block.
 *
 * @ingroup views_display_plugins
 *
 * @ViewsDisplay(
 *   id = "bene_listing_page_plugin_display_block",
 *   title = @Translation("Block"),
 *   help = @Translation("Display the view as a block."),
 *   theme = "views_view",
 *   register_theme = FALSE,
 *   uses_hook_block = TRUE,
 *   contextual_links_locations = {"block"},
 *   admin = @Translation("Block")
 * )
 *
 * @see \Drupal\views\Plugin\block\ViewsBlock
 * @see \Drupal\views\Plugin\Derivative\ViewsBlock
 */
class ListingPluginDisplayBlock extends Block {

  /**
   * Block views use exposed widgets only if AJAX is set.
   */
  public function usesExposed() {
    return DisplayPluginBase::usesExposed();
  }

}
