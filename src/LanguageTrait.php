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
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  private $languageManager;

  private function getLanguageCode(TranslatableMarkup $translated_string) {
    return empty($translated_string->getOption('langcode')) ? $this->languageManager->getCurrentLanguage()->getId() : $translated_string->getOption('langcode');
  }

}
