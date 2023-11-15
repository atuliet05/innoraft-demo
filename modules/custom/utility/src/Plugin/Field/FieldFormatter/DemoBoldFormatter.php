<?php

namespace Drupal\utility\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
/**
 * Plugin implementation of the 'demo_bold' formatter.
 *
 * @FieldFormatter(
 *   id = "demo_bold",
 *   label = @Translation("Demo Bold"),
 *   field_types = {
 *     "string",
 *   }
 * )
 */

/*
"text_long",
 *      "text_with_summary",
 * */
class DemoBoldFormatter extends FormatterBase {
// Above, we extend the class Formatter base into our own new class - ReverseStringFormatter

  /**
   * The viewElements function is where we're able to make modifications to
   * the FieldItemListInterface variable $items.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    // Initialize an array to store our processed items.
    $elements = [];

    // In this for loop we'll take the value of each item, and then reverse the string.
    foreach ($items as $delta => $item) {

     // $var = $item->getValue();
      $var = $item->value;
      //$reversed = '<b>'.$item_value .'</b>';
      // The nth item in the array is being set to a simple renderable array - at the simplest
      // our render array only needs a #markup key.
      //$element[$delta] = ['#markup' => $reversed];

     // $var = 'abcdefgh';
      $elements[$delta] = array(
          '#theme' => 'demo_bold_formatter',
          '#var' => $var,
      );

    }

    // Lastly, we need to return the $elements array so it gets output for rendering.
    return $elements;
  }

}
