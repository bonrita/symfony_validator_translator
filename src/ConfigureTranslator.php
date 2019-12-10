<?php

namespace Drupal\symfony_validator_translator;


use Drupal\Core\Site\Settings;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ConfigureTranslator
 *
 * @package Drupal\symfony_validator_translator
 */
final class ConfigureTranslator implements IConfigureTranslator {

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
    $path = $this->basePath() . '/symfony/validator/Resources/translations/validators.' . $lang_code . '.xlf';
    $this->translator->addLoader('xlf', $this->loader);
    $this->translator->setLocale($lang_code);
    $this->translator->addResource('xlf', $path, $lang_code);
    $this->activeLanguage = $lang_code;
  }

  /**
   * {@inheritdoc}
   */
  public function doesNeedConfiguring(string $lang_code) {
    return (!$this->activeLanguage || $this->activeLanguage <> $lang_code);
  }

  /**
   * Configure the base path.
   *
   * @throws \Exception
   */
  private function basePath() {
    if ($custom_path = Settings::get('vendor_file_path')) {
      return $custom_path;
    }

    if (is_dir('../vendor')) {
      return '../vendor';
    }

    if (is_dir('vendor')) {
      return 'vendor';
    }

    throw new \RuntimeException('The vendor directory cannot be found');
  }

}
