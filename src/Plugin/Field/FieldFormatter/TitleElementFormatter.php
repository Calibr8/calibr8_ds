<?php

/**
 * @file
 * Contains \Drupal\calibr8_ds\Plugin\Field\FieldFormatter\TitleElementFormatter.
 */

namespace Drupal\calibr8_ds\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'title_element' formatter.
 *
 * @FieldFormatter(
 *   id = "title_element",
 *   label = @Translation("Title element"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class TitleElementFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $settings = $this->getSettings();

    // default elements
    $options = array(
      'h1' => 'H1',
      'h2' => 'H2',
      'h3' => 'H3',
      'h4' => 'H4',
      'h5' => 'H5',
      'h6' => 'H6',
      'field_title_style' => 'Title style (field)',
      'field_subtitle_style' => 'Subtitle style (field)',
    );
    $form['title_element'] = array(
      '#type' => 'select',
      '#title' => $this->t('Title element'),
      '#options' => $options,
      '#default_value' => $settings['title_element'],
    );
    $form['title_class'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Title class'),
      '#default_value' => $settings['title_class'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $settings = $this->getSettings();
    $summary[] = $this->t('Define a title element');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'title_element' => '',
      'title_class' => 'field--title',
        ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();
    $title_element = $this->getSetting('title_element');
    switch($title_element) {
      case 'h1':
      case 'h2':
      case 'h3':
      case 'h4':
      case 'h5':
      case 'h6':
        break;
      case 'field_title_style':
      case 'field_subtitle_style':
        $entity = $items->getEntity();
        if($entity->hasField($title_element)) {
          $title_element = $entity->get($title_element)->getValue();
          $title_element = reset($title_element);
          if(isset($title_element['value']) && (
            $title_element['value'] == 'h1' ||
            $title_element['value'] == 'h2' ||
            $title_element['value'] == 'h3' ||
            $title_element['value'] == 'h4' ||
            $title_element['value'] == 'h5'
          )) {
            $title_element = $title_element['value'];
          } else {
            $title_element = 'h2'; // default value
          }
        } else {
          $title_element = 'h2'; // default value
        }
        break;
      default:
        $title_element = 'h2'; // default value
    }

    // set class
    $class = $this->getSetting('title_class');

    foreach ($items as $delta => $item) {
      $element[$delta] = array(
        '#type' => 'markup',
        '#markup' => '<' . $title_element . ' class="' . $class . '">' . $item->value . '</' . $title_element . '>',
      );
    }

    return $element;
  }
}
