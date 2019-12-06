<?php

namespace Drupal\symfony_validator_translator;


use Drupal\Core\StringTranslation\TranslatableMarkup;

interface ICacheTranslator {

  /**
   * Get cached translation.
   *
   * Try getting the translated string from the cache.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *
   * @return mixed|null
   */
  public function getCachedTranslation(TranslatableMarkup $translated_string);

  /**
   * Cache the translation on the fly if found.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *
   * @return string|null
   */
  public function cacheSymfonyTranslation(TranslatableMarkup $translated_string);

}
