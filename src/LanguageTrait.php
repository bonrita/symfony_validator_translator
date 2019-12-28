<?php

namespace Drupal\symfony_validator_translator;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a trait for the translation classes.
 *
 * The trait provides a helper method to get the language code from either the
 * options passed into the t() function or from the language manager service.
 *
 * @see \Drupal\symfony_validator_translator\DTranslationManager
 * @see \Drupal\symfony_validator_translator\CacheTranslator
 *
 * @package Drupal\symfony_validator_translator
 */
trait LanguageTrait {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private $languageManager;

  /**
   * Get the language code.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The untranslated string.
   *
   * @return mixed|string
   *   The language code.
   */
  private function getLanguageCode(TranslatableMarkup $translated_string) {
    return empty($translated_string->getOption('langcode')) ? $this->languageManager->getCurrentLanguage()->getId() : $translated_string->getOption('langcode');
  }

}
