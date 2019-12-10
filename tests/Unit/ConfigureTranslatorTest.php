<?php

namespace Drupal\Tests\symfony_validator_translator\Unit;

use Drupal\symfony_validator_translator\ConfigureTranslator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class ConfigureTranslatorTest
 *
 * @package Drupal\Tests\symfony_validator_translator\Unit
 *
 * @group symfony_translations
 * @coversDefaultClass \Drupal\symfony_validator_translator\ConfigureTranslator
 */
class ConfigureTranslatorTest extends UnitTestCase {

  /**
   * For the first time the configurator should be configured.
   *
   * @covers ::doesNeedConfiguring
   */
  public function testIfTranslatorShouldBeConfigured() {
    $configureInstance = $this->instatiate();
    $this->assertTrue($configureInstance->doesNeedConfiguring('nl'));
  }

  /**
   * @covers ::configure
   */
  public function testconfigure() {
    $configureInstance = $this->instatiate(TRUE);
    $configureInstance->configure('nl');
  }

  /**
   * Make sure the translator is not configured if it was already configured.
   *
   * @covers ::doesNeedConfiguring
   */
  public function testTranslatorShouldNotBeConfigured() {
    $configureInstance = $this->instatiate(TRUE);
    // Configure translator.
    $configureInstance->configure('nl');

    // Translator should not need to be configured again as it was already
    //configured in the previous line.
    $this->assertFalse($configureInstance->doesNeedConfiguring('nl'));
  }

  protected function instatiate(bool $configure = FALSE) {
    $translator = $this->getMockBuilder(Translator::class)
      ->disableOriginalConstructor()
      ->getMock();

    $loader = $this->getMockBuilder(LoaderInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    if ($configure) {
      $translator->expects($this->once())->method('addLoader');
      $translator->expects($this->once())->method('setLocale');
      $translator->expects($this->once())->method('addResource');
    }

    return new ConfigureTranslator($translator, $loader);
  }

}
