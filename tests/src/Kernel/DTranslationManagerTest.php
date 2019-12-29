<?php

namespace Drupal\Tests\symfony_validator_translator\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\symfony_validator_translator\CacheTranslatorInterface;

/**
 * This class makes sure that the standard translations from Drupal still work.
 *
 * It makes sure that the special Symfony translations work on top of the
 * Drupal translations.
 *
 * @group symfony_translations
 * @package Drupal\Tests\symfony_validator_translator\Kernel
 */
class DTranslationManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'language',
    'locale',
    'symfony_validator_translator',
  ];

  /**
   * The locale storage.
   *
   * @var \Drupal\locale\StringStorageInterface
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Add a default locale storage for all these tests.
    $this->storage = $this->container->get('locale.storage');

    $this->installSchema('locale', [
      'locales_location',
      'locales_source',
      'locales_target',
    ]);
  }

  /**
   * Test that the Drupal translations are still picked up.
   *
   * When the core translation manager is decorated to include
   * the Symfony validation translations.
   */
  public function testDrupalTranslations() {
    $langcode = 'nl';

    // Create test source string.
    $string = $this->container->get('locale.storage')->createString([
      'source' => 'omrodliofk',
    ])->save();

    // Create translation for new string and save it.
    $translation = $this->container->get('locale.storage')->createTranslation([
      'lid' => $string->lid,
      'language' => $langcode,
      'translation' => $this->randomMachineName(100),
      'customized' => 0,
    ])->save();

    // Use the t function to translate.
    $translated_string = t('omrodliofk', [], ['langcode' => $langcode])->render();

    $this->assertEquals($translated_string, $translation->getString(), 'The translated string is the same as the string that is stored in the Drupal locale storage.');
  }

  /**
   * Test a Symfony translation.
   */
  public function testSymfonyValidationTranslationString() {
    $string = $this->randomString(10);
    $translation = $this->randomString('8');

    // Mock cache object.
    $cache_mock = $this->getMockBuilder(CacheTranslatorInterface::class)->getMock();
    $cache_mock->expects($this->once())->method('cacheSymfonyTranslation')->willReturn($translation);
    $this->container->set('symfony_validator_translator.cache_translator', $cache_mock);

    // Get a Symfony translation.
    $translated_string = t('omrodliofk', [], ['langcode' => 'yy'])->render();

    $this->assertEquals($translation, $translated_string);
  }

}
