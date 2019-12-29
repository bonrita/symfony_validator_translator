<?php

namespace Drupal\symfony_validator_translator;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class has functionality that caches translations.
 *
 * The translations are read from the Symfony translation files.
 * It also caches Symfony translations that have already been used in the
 * Drupal UI.
 *
 * @package Drupal\symfony_validator_translator
 */
final class CacheTranslator implements CacheTranslatorInterface {

  use LanguageTrait;

  const SYMFONY_TRANSLATIONS_MESSAGES_CACHE_KEY_PREFIX = 'symfony_translations_';

  /**
   * The translator.
   *
   * @var \Symfony\Component\Translation\TranslatorInterface
   */
  private $translator;

  /**
   * The cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  private $cache;

  /**
   * The symfony domain in which the strings are retrieved from.
   *
   * @var string
   */
  private $domain = 'messages';

  /**
   * DTranslationManager constructor.
   *
   * @param \Symfony\Component\Translation\TranslatorInterface $translator
   *   The translator.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache.
   */
  public function __construct(TranslatorInterface $translator, LanguageManagerInterface $language_manager, CacheBackendInterface $cache) {
    $this->translator = $translator;
    $this->languageManager = $language_manager;
    $this->cache = $cache;
  }

  /**
   * Cache the translation on the fly if found.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The untranslated string.
   *
   * @return string|null
   *   The translated string.
   */
  public function cacheSymfonyTranslation(TranslatableMarkup $translated_string) {
    $lang = $this->getLanguageCode($translated_string);
    $cache_translations_key = $this->getTranslationKey($lang);
    $cached_translations = $this->getCachedTranslations($translated_string);

    if (array_key_exists($translated_string->getUntranslatedString(), $this->getCachedMessages($lang))) {
      $translation = $this->translator->trans($translated_string->getUntranslatedString(), $translated_string->getArguments(), $this->domain, $lang);
      $messages = [$translated_string->getUntranslatedString() => $translation] + $cached_translations;
      $this->cache->set($cache_translations_key, $messages);
      return $translation;
    }

    return NULL;
  }

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
  public function getCachedTranslation(TranslatableMarkup $translated_string) {
    $cached_translations = $this->getCachedTranslations($translated_string);
    if (array_key_exists($translated_string->getUntranslatedString(), $cached_translations)) {
      return $cached_translations[$translated_string->getUntranslatedString()];
    }

    return NULL;
  }

  /**
   * Get a list of cached translations.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The translated string.
   *
   * @return array
   *   A list of cached translations.
   */
  private function getCachedTranslations(TranslatableMarkup $translated_string): array {
    $lang = $this->getLanguageCode($translated_string);
    $cache_translations_key = $this->getTranslationKey($lang);
    $translations = $this->cache->get($cache_translations_key) ? $this->cache->get($cache_translations_key)->data : [];
    return $translations;
  }

  /**
   * When cache is cleared the messages are cached during that process.
   *
   * @param string $lang_code
   *   The language code.
   *
   * @return array
   *   A list of cached messages.
   */
  private function getCachedMessages(string $lang_code): array {
    $cache_messages_key = $this->getTranslationKey($lang_code);
    $cached_messages = $this->cache->get($cache_messages_key) ? $this->cache->get($cache_messages_key)->data : [];

    if (empty($cached_messages)) {
      $catalogue = $this->translator->getCatalogue($lang_code);
      $cached_messages = $catalogue->all($this->domain);
      $this->cache->set($cache_messages_key, $cached_messages);
    }
    return $cached_messages;
  }

  /**
   * Get the translation key.
   *
   * @param string $lang_code
   *   The language code.
   *
   * @return string
   *   The translation key.
   */
  private function getTranslationKey(string $lang_code) {
    return self::SYMFONY_TRANSLATIONS_MESSAGES_CACHE_KEY_PREFIX . $this->domain . '_' . $lang_code;
  }

}
