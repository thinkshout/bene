<?php

namespace Drupal\bene_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\text\Plugin\Field\FieldFormatter\TextSummaryOrTrimmedFormatter;
use Drupal\text\Plugin\Field\FieldFormatter\TextTrimmedFormatter;

/**
 * Plugin implementation of summary formatter that uses a fallback.
 *
 * @FieldFormatter(
 *   id = "bene_fallback_summary",
 *   label = @Translation("Summary or trimmed with fallback"),
 *   field_types = {
 *     "text_with_summary"
 *   },
 *   quickedit = {
 *     "editor" = "form"
 *   }
 * )
 */
class FallbackSummaryFormatter extends TextSummaryOrTrimmedFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Largely a duplicate of TextTrimmedFormatter::viewElements().
    $elements = [];

    $render_as_summary = function (&$element) {
      // Make sure any default #pre_render callbacks are set on the element,
      // because text_pre_render_summary() must run last.
      $element += \Drupal::service('element_info')->getInfo($element['#type']);
      // Add the #pre_render callback that renders the text into a summary.
      $element['#pre_render'][] = [TextTrimmedFormatter::class, 'preRenderSummary'];
      // Pass on the trim length to the #pre_render callback via a property.
      $element['#text_summary_trim_length'] = $this->getSetting('trim_length');
    };

    // The ProcessedText element already handles cache context & tag bubbling.
    // @see \Drupal\filter\Element\ProcessedText::preRenderText()
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'processed_text',
        '#text' => NULL,
        '#format' => $item->format,
        '#langcode' => $item->getLangcode(),
      ];

      if (!empty($item->summary)) {
        $elements[$delta]['#text'] = $item->summary;
      }
      else {
        $elements[$delta]['#text'] = $item->value;
        $render_as_summary($elements[$delta]);
      }
    }
    // No summary or normal value, fallback to first rich text paragraph.
    if (empty($elements)) {
      foreach ($items->getParent()->get('bene_content') as $paragraph) {
        if ($paragraph->entity->getType() == 'rich_text') {
          $elements[0] = [
            '#type' => 'processed_text',
            '#text' => $paragraph->entity->field_text->value,
            '#format' => $paragraph->entity->field_text->format,
            '#langcode' => $paragraph->entity->field_text->getLangcode(),
          ];
          $render_as_summary($elements[0]);
          break;
        }
      }
    }

    return $elements;
  }

}
