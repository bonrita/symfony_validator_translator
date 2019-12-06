<?php

namespace Drupal\symfony_validator_translator;


use Drupal\Core\Site\Settings;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ConfigureTranslator implements IConfigureTranslator {

  /**
   * @var null|string
   */
  private $activeLanguage;

  /**
   * @var \Symfony\Component\Translation\Loader\LoaderInterface
   */
  private $loader;

  /**
   * @var \Symfony\Component\Translation\TranslatorInterface
   */
  private $translator;

  /**
   * DTranslationManager constructor.
   *
   * @param \Symfony\Component\Translation\TranslatorInterface $translator
   * @param \Symfony\Component\Translation\Loader\LoaderInterface $loader
   */
  public function __construct(TranslatorInterface $translator, LoaderInterface $loader) {
    $this->translator = $translator;
    $this->loader = $loader;
  }

  /**
   * {@inheritdoc}
   */
  public function configure(string $lang_code) {
    if (!$this->activeLanguage || $this->activeLanguage <> $lang_code) {
      $path = $this->basePath() . '/symfony/validator/Resources/translations/validators.' . $lang_code . '.xlf';
      $this->translator->addLoader('xlf', $this->loader);
      $this->translator->setLocale($lang_code);
      $this->translator->addResource('xlf', $path, $lang_code);
      $this->activeLanguage = $lang_code;
    }
  }

  /**
   * Configure the base path.
   */
  private function basePath() {
    if ($custom_path = Settings::get('vendor_file_path')) {
      return $custom_path;
    }
    elseif (is_dir('../vendor')) {
      return '../vendor';
    }
    elseif (is_dir('vendor')) {
      return 'vendor';
    }

    throw new \Exception('The vendor directory cannot be found');
  }

}