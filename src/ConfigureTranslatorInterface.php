<?php

namespace Drupal\symfony_validator_translator;

/**
 * This interface exposes public methods.
 *
 * The public methods are responsible for configuring the Symfony translator.
 *
 * @package Drupal\symfony_validator_translator
 */
interface ConfigureTranslatorInterface {

  /**
   * Configure the translator.
   *
   * @param string $lang_code
   *   The language code.
   */
  public function configure(string $lang_code);

  /**
   * Check if the translator needs configuring.
   *
   * @param string $lang_code
   *   The language code.
   *
   * @return bool
   *   True if need re-configuring.
   */
  public function doesNeedConfiguring(string $lang_code);

}
