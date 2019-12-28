<?php

namespace Drupal\symfony_validator_translator;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * This interface exposes public methods that will be used to cache the Symfony
 * translations.
 *
 * @package Drupal\symfony_validator_translator
 */
interface CacheTranslatorInterface {

  /**
   * Get cached translation.
   *
   * Try getting the translated string from the cache.
   *
   * @param string $string
   *   The untranslated string.
   *
   * @param string $langcode
   *   The Language code.
   *
   * @return mixed|null
   *   The translated string.
   */
  public function getCachedTranslation(string $string, string $langcode);

  /**
   * Cache the translation on the fly if found.
   *
   * @param string $string
   *   The untranslated string.
   *
   * @param string $langcode
   *   The language code.
   *
   * @return string|null
   *   The translated string.
   */
  public function cacheSymfonyTranslation(string $string, string $langcode);

}
