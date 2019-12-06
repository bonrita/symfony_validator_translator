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

}
