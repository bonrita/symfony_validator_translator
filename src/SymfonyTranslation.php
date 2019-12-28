<?php


namespace Drupal\symfony_validator_translator;


use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\StringTranslation\Translator\TranslatorInterface;

final class SymfonyTranslation implements TranslatorInterface {

  /**
   * The configure translator.
   *
   * @var \Drupal\symfony_validator_translator\ConfigureTranslatorInterface
   */
  private $configureTranslator;

  /**
   * @var \Drupal\symfony_validator_translator\CacheTranslatorInterface
   */
  private $cache;

  public function __construct(
    ConfigureTranslatorInterface $configure_translator,
    CacheTranslatorInterface $cache) {
    $this->configureTranslator = $configure_translator;
    $this->cache = $cache;
  }

  /**
   * @inheritDoc
   */
  public function getStringTranslation($langcode, $string, $context) {
    // If the language is not suitable for the Symfony translation, then return.
    if ($langcode == LanguageInterface::LANGCODE_SYSTEM) {
      return FALSE;
    }

    // Configure the translator.
    if ($this->configureTranslator->doesNeedConfiguring($langcode)) {
      $this->configureTranslator->configure($langcode);
    }

    // Try getting the translated string from the cache.
    if ($translation = $this->cache->getCachedTranslation(
      $string, $langcode
    )) {
      return $translation;
    }
  }

  /**
   * @inheritDoc
   */
  public function reset() {
    // TODO: Implement reset() method.
  }

}
