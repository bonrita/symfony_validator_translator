<?php

namespace Drupal\Tests\symfony_validator_translator\Unit;

use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\symfony_validator_translator\DTranslationManager;
use Drupal\symfony_validator_translator\CacheTranslatorInterface;
use Drupal\symfony_validator_translator\ConfigureTranslatorInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Translation\Translator;

/**
 * Class DTranslatorTest.
 *
 * @package Drupal\Tests\symfony_validator_translator\Unit
 * @group symfony_translations
 * @coversDefaultClass \Drupal\symfony_validator_translator\DTranslationManager
 */
class DTranslationManagerTest extends UnitTestCase {

  /**
   * The decorated service.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $decorated;

  /**
   * The symfony translator.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $translator;

  /**
   * The language default.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $languageDefault;

  /**
   * The configure translator.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $configureTranslator;

  /**
   * The cache.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $cache;

  /**
   * The language.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $language;

  /**
   * The language manager.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $languageManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->decorated = $this->getMockBuilder(TranslationManager::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->translator = $this->getMockBuilder(Translator::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->languageDefault = $this->getMockBuilder(LanguageDefault::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->configureTranslator = $this->getMockBuilder(
      ConfigureTranslatorInterface::class
    )
      ->disableOriginalConstructor()
      ->getMock();

    $this->cache = $this->getMockBuilder(CacheTranslatorInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->language = $this->getMockBuilder(Language::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->languageManager = $this->getMockBuilder(LanguageManager::class)
      ->disableOriginalConstructor()
      ->getMock();

  }

  /**
   * @covers ::translateString
   */
  public function testTranslateString() {

    $this->language->expects($this->once())->method('getId')->willReturn('en');
    $this->languageManager->expects($this->once())->method('getCurrentLanguage')->willReturn($this->language);
    $this->cache->expects($this->once())->method('getCachedTranslation');
    $this->cache->expects($this->once())->method('cacheSymfonyTranslation');

    $translate = new DTranslationManager(
      $this->decorated,
      $this->translator,
      $this->configureTranslator,
      $this->languageManager,
      $this->cache
    );

    $title = $this->getRandomGenerator()->word(8);
    $untranslatedString = new TranslatableMarkup($title);
    $translate->translateString($untranslatedString);
  }

}
