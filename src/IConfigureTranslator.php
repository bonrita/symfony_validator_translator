<?php

namespace Drupal\symfony_validator_translator;


interface IConfigureTranslator {

  /**
   * Configure the translator.
   *
   * @param string $lang_code
   *
   * @return void
   */
  public function configure(string $lang_code);

  /**
   * Check if the translator needs configuring.
   *
   * @param string $lang_code
   *
   * @return bool
   */
  public function doesNeedConfiguring(string $lang_code);

}
