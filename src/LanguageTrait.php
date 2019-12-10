<?php


namespace Drupal\symfony_validator_translator;


use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Trait LanguageTraitor
 *
 * @package Drupal\symfony_validator_translator
 */
trait LanguageTrait {

  /**
   * @var \Drupal\Core\Language\LanguageDefault
   */
  private $languageDefault;

  private function getLanguageCode(TranslatableMarkup $translated_string) {
    return empty($translated_string->getOption('langcode')) ? $this->languageDefault->get()->getId() : $translated_string->getOption('langcode');
  }

}
