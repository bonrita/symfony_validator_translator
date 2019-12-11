<?php


namespace Drupal\Tests\symfony_validator_translator\Unit;


use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageDefault;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\symfony_validator_translator\CacheTranslator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Translation\Translator;

/**
 * Class CacheTranslatorTest
 *
 * @package Drupal\Tests\symfony_validator_translator\Unit
 *
 * @group symfony_translations
 * @coversDefaultClass \Drupal\symfony_validator_translator\CacheTranslator
 */
class CacheTranslatorTest extends UnitTestCase {

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
  private $language;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $cache;

  protected function setUp() {
    parent::setUp();
    $this->translator = $this->getMockBuilder(Translator::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->languageDefault = $this->getMockBuilder(LanguageDefault::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->language = $this->getMockBuilder(Language::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->cache = $this->getMockBuilder(CacheBackendInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

  }

  /**
   * @covers ::getCachedTranslation
   */
  public function testEmptyCachedTranslation(){
    $title = $this->getRandomGenerator()->word(5);
    $untranslatedString =  new TranslatableMarkup($title);

    $this->language->expects($this->once())->method('getId')->willReturn('en');
    $this->languageDefault->expects($this->once())->method('get')->willReturn($this->language);

    $cacheTranslator = new CacheTranslator($this->translator, $this->languageDefault, $this->cache);
    $this->assertNull($cacheTranslator->getCachedTranslation($untranslatedString));
  }

  /**
   * @covers ::getCachedTranslation
   */
  public function testTranslationExistsInCache(){
    $translated =  $this->getRandomGenerator()->word(4);
    $title = $this->getRandomGenerator()->word(8);
    $untranslatedString =  new TranslatableMarkup($title);

    $this->language->expects($this->once())->method('getId')->willReturn('nl');
    $this->languageDefault->expects($this->once())->method('get')->willReturn($this->language);

    $object = (new \stdClass());
    $object->data = [$title => $translated];
    $this->cache->expects($this->any())->method('get')->willReturn($object);

    $cacheTranslator = new CacheTranslator($this->translator, $this->languageDefault, $this->cache);
    $translation = $cacheTranslator->getCachedTranslation($untranslatedString);

    $this->assertEquals($translated, $translation);
  }


  /**
   * @covers ::cacheSymfonyTranslation
   */
  public function testCacheSymfonyTranslation() {
    $translated =  $this->getRandomGenerator()->word(4);
    $title = $this->getRandomGenerator()->word(8);
    $untranslatedString =  new TranslatableMarkup($title);

    $this->language->expects($this->any())->method('getId')->willReturn('nl');
    $this->languageDefault->expects($this->any())->method('get')->willReturn($this->language);

    $object = (new \stdClass());
    $object->data = [$title => $translated];
    $this->cache->expects($this->any())->method('get')->willReturn($object);

    $this->cache->expects($this->once())->method('set');
    $this->translator->expects($this->once())->method('trans');

    $cacheTranslator = new CacheTranslator($this->translator, $this->languageDefault, $this->cache);
    $cacheTranslator->cacheSymfonyTranslation($untranslatedString);
  }

}
