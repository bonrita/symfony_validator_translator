<?php

namespace Drupal\symfony_validator_translator;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Interface cacheTranslatorInterface.
 *
 * @package Drupal\symfony_validator_translator
 */
interface CacheTranslatorInterface {

  /**
   * Get cached translation.
   *
   * Try getting the translated string from the cache.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The untranslated string.
   *
   * @return mixed|null
   *   The translated string.
   */
  public function getCachedTranslation(TranslatableMarkup $translated_string);

  /**
   * Cache the translation on the fly if found.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The untranslated string.
   *
   * @return string|null
   *   The translated string.
   */
  public function cacheSymfonyTranslation(TranslatableMarkup $translated_string);

}
