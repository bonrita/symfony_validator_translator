<?php

namespace Drupal\symfony_validator_translator;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\Translator\TranslatorInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface as SymfonyTranslator;

/**
 * This class decorates the original Drupal core translation manager.
 *
 * It adds new behaviour to the Drupal's translation manager that translates
 * the Symfony validation strings.
 *
 * @package Drupal\symfony_validator_translator
 */
final class DTranslationManager implements TranslationInterface, TranslatorInterface {

  use LanguageTrait;

  /**
   * The decorated service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  private $decorated;

  /**
   * The translator.
   *
   * @var \Symfony\Component\Translation\TranslatorInterface
   */
  private $translator;

  /**
   * The cache.
   *
   * @var \Drupal\symfony_validator_translator\CacheTranslatorInterface
   */
  private $cache;

  /**
   * The configure translator.
   *
   * @var \Drupal\symfony_validator_translator\ConfigureTranslatorInterface
   */
  private $configureTranslator;

  /**
   * DTranslationManager constructor.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $decorated
   *   The decorated service.
   * @param \Symfony\Component\Translation\TranslatorInterface $translator
   *   The Symfony translator.
   * @param \Drupal\symfony_validator_translator\ConfigureTranslatorInterface $configure_translator
   *   Configure translator.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\symfony_validator_translator\CacheTranslatorInterface $cache
   *   The cache.
   */
  public function __construct(
    TranslationInterface $decorated,
    SymfonyTranslator $translator,
    ConfigureTranslatorInterface $configure_translator,
    LanguageManagerInterface $language_manager,
    CacheTranslatorInterface $cache
  ) {
    $this->decorated = $decorated;
    $this->translator = $translator;
    $this->languageManager = $language_manager;
    $this->cache = $cache;
    $this->configureTranslator = $configure_translator;
  }

  /**
   * {@inheritdoc}
   */
  public function translate($string, array $args = [], array $options = []) {
    return $this->decorated->translate($string, $args, $options);
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  public function translateString(TranslatableMarkup $translated_string) {

    if ($translation = $this->getSymfonyTranslation($translated_string)) {
      return $translation;
    }

    return $this->decorated->translateString($translated_string);
  }

  /**
   * {@inheritdoc}
   */
  public function formatPlural(
    $count,
    $singular,
    $plural,
    array $args = [],
    array $options = []
  ) {
    return $this->decorated->formatPlural(
      $count,
      $singular,
      $plural,
      $args,
      $options
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getStringTranslation($langcode, $string, $context) {
    return $this->decorated->getStringTranslation($langcode, $string, $context);
  }

  /**
   * Sets the default langcode.
   *
   * This method has been put here as part of the original decorated class.
   * It is missing in any of the implemented interfaces.
   *
   * @param string $langcode
   *   A language code.
   */
  public function setDefaultLangcode($langcode) {
    $this->decorated->setDefaultLangcode($langcode);
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    $this->decorated->reset();
  }

  /**
   * Appends a translation system to the translation chain.
   *
   * This method has been put here as part of the original decorated class.
   * It is missing in any of the implemented interfaces.
   *
   * @param \Drupal\Core\StringTranslation\Translator\TranslatorInterface $translator
   *   The translation interface to be appended to the translation chain.
   * @param int $priority
   *   The priority of the logger being added.
   *
   * @return $this
   *   The translator.
   */
  public function addTranslator(
    TranslatorInterface $translator,
    $priority = 0
  ) {
    return $this->decorated->addTranslator($translator, $priority);
  }

  /**
   * Get a symfony validation translation string.
   *
   * A Symfony translation is a string that resides in the Symfony core
   * component translation file.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $translated_string
   *   The string to translate.
   *
   * @return string|null
   *   The translation.
   *
   * @throws \Exception
   */
  private function getSymfonyTranslation(TranslatableMarkup $translated_string) {
    $lang = $this->getLanguageCode($translated_string);
    $string_translation = $this->getStringTranslation(
      $lang,
      $translated_string->getUntranslatedString(),
      $translated_string->getOption('context')
    );
    if (!$string_translation && $this->translator instanceof TranslatorBagInterface) {
      // Configure the translator.
      if ($this->configureTranslator->doesNeedConfiguring($lang)) {
        $this->configureTranslator->configure($lang);
      }

      // Try getting the translated string from the cache.
      if ($translation = $this->cache->getCachedTranslation(
        $translated_string
      )) {
        return $translation;
      }

      // Cache the translation on the fly if found.
      if ($translation = $this->cache->cacheSymfonyTranslation(
        $translated_string
      )) {
        return $translation;
      }

    }

    return NULL;
  }

}
