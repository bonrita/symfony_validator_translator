<?php


namespace Drupal\Tests\symfony_validator_translator\Unit;


use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\symfony_validator_translator\DTranslationManager;
use Drupal\symfony_validator_translator\ICacheTranslator;
use Drupal\symfony_validator_translator\IConfigureTranslator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Translation\Translator;

/**
 * Class DTranslatorTest
 *
 * @package Drupal\Tests\symfony_validator_translator\Unit
 * @group symfony_translations
 * @coversDefaultClass \Drupal\symfony_validator_translator\DTranslationManager
 */
class DTranslationManagerTest extends UnitTestCase {

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $decoarated;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $translator;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $languageDefault;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $configureTranslator;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $cache;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $language;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $languageManager;

  protected function setUp() {
    parent::setUp();
    $this->decoarated = $this->getMockBuilder(TranslationManager::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->translator = $this->getMockBuilder(Translator::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->languageDefault = $this->getMockBuilder(LanguageDefault::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->configureTranslator = $this->getMockBuilder(
      IConfigureTranslator::class
    )
      ->disableOriginalConstructor()
      ->getMock();

    $this->cache = $this->getMockBuilder(ICacheTranslator::class)
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
      $this->decoarated,
      $this->translator,
      $this->configureTranslator,
      $this->languageManager,
      $this->cache
    );

    $title = $this->getRandomGenerator()->word(8);
    $untranslatedString =  new TranslatableMarkup($title);
    $translate->translateString($untranslatedString);
  }

}
