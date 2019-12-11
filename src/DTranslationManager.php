<?php

namespace Drupal\symfony_validator_translator;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\StringTranslation\Translator\TranslatorInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface as SymfonyTranslator;

/**
 * Class DTranslationManager
 *
 * @package Drupal\symfony_validator_translator
 */
final class DTranslationManager implements TranslationInterface, TranslatorInterface{

  use LanguageTrait;

  /**
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  private $decorated;

  /**
   * @var \Symfony\Component\Translation\TranslatorInterface
   */
  private $translator;

  /**
   * @var \Drupal\symfony_validator_translator\ICacheTranslator
   */
  private $cache;

  /**
   * @var \Drupal\symfony_validator_translator\IConfigureTranslator
   */
  private $configureTranslator;

  /**
   * DTranslationManager constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $decorated
   * @param \Symfony\Component\Translation\TranslatorInterface $translator
   * @param \Drupal\symfony_validator_translator\IConfigureTranslator $configure_translator
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   * @param \Drupal\symfony_validator_translator\ICacheTranslator $cache
   */
  public function __construct(TranslationInterface $decorated, SymfonyTranslator $translator, IConfigureTranslator $configure_translator, LanguageManagerInterface $language_manager, ICacheTranslator $cache) {
    $this->decorated = $decorated;
    $this->translator = $translator;
    $this->languageManager = $language_manager;
    $this->cache = $cache;
    $this->configureTranslator = $configure_translator;
  }

  /**
   * @inheritDoc
   */
  public function translate($string, array $args = [], array $options = []) {
    return $this->decorated->translate($string, $args,$options);
  }

  /**
   * @inheritDoc
   * @throws \Exception
   */
  public function translateString(TranslatableMarkup $translated_string) {

    if ($translation = $this->getSymfonyTranslation($translated_string)) {
      return $translation;
    }

    return $this->decorated->translateString($translated_string);
  }

  /**
   * @inheritDoc
   */
  public function formatPlural(
    $count,
    $singular,
    $plural,
    array $args = [],
    array $options = []
  ) {
    return $this->decorated->formatPlural($count, $singular, $plural, $args, $options);
  }

  /**
   * @inheritDoc
   */
  public function getStringTranslation($langcode, $string, $context) {
    return $this->decorated->getStringTranslation($langcode, $string, $context);
  }

  /**
   * Sets the default langcode.
   *
   * This method has been put here as part of the original decorated class.
   * It is missing in any of the implemented interfaces
   *
   * @param string $langcode
   *   A language code.
   */
  public function setDefaultLangcode($langcode) {
    $this->decorated->setDefaultLangcode($langcode);
  }

  /**
   * @inheritDoc
   */
  public function reset() {
    $this->decorated->reset();
  }

  /**
   * Appends a translation system to the translation chain.
   *
   * This method has been put here as part of the original decorated class.
   * It is missing in any of the implemented interfaces
   *
   * @param \Drupal\Core\StringTranslation\Translator\TranslatorInterface $translator
   *   The translation interface to be appended to the translation chain.
   * @param int $priority
   *   The priority of the logger being added.
   *
   * @return $this
   */
  public function addTranslator(TranslatorInterface $translator, $priority = 0) {
    return $this->decorated->addTranslator($translator, $priority);
  }

  /**
   * Get a symfony translation.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *
   * @return mixed|string|null
   * @throws \Exception
   */
  private function getSymfonyTranslation(TranslatableMarkup $translated_string) {
    $lang = $this->getLanguageCode($translated_string);
    $string_translation = $this->getStringTranslation($lang, $translated_string->getUntranslatedString(), $translated_string->getOption('context'));
    if (!$string_translation && $this->translator instanceof TranslatorBagInterface) {
      // Configure the translator.
      if ($this->configureTranslator->doesNeedConfiguring($lang)) {
        $this->configureTranslator->configure($lang);
      }

      // Try getting the translated string from the cache.
      if($translation = $this->cache->getCachedTranslation($translated_string)) {
        return $translation;
      }

      // Cache the translation on the fly if found.
      if ($translation = $this->cache->cacheSymfonyTranslation($translated_string)) {
        return $translation;
      }

    }

    return NULL;
  }

}
