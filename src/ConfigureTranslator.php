<?php

namespace Drupal\symfony_validator_translator;


use Drupal\Core\Site\Settings;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validation;

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
   * Check if resource was set.
   *
   * @var bool
   */
  private $isResourceSet = NULL;

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
   * @throws \ReflectionException
   */
  public function configure(string $lang_code) {
    $this->translator->addLoader('xlf', $this->loader);
    $this->translator->setLocale($lang_code);
    $this->setResource($lang_code);
    $this->activeLanguage = $lang_code;
  }

  /**
   * {@inheritdoc}
   */
  public function doesNeedConfiguring(string $lang_code) {
    return (!$this->activeLanguage || $this->activeLanguage <> $lang_code);
  }

  /**
   * @param string $lang_code
   *
   * @throws \ReflectionException
   */
  private function setResource(string $lang_code) {
    if (!$this->isResourceSet) {
      $reflection = new \ReflectionClass(Validation::class);
      $path = str_replace('Validation.php', 'Resources/translations/validators.', $reflection->getFileName());
      $path .=  $lang_code . '.xlf';

      $this->translator->addResource('xlf', $path, $lang_code);
      $this->isResourceSet = TRUE;
    }
  }

}
