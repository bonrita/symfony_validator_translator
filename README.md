# How to use the module

Install the module.

#### Overriding Symfony translations
Sometimes you may need to override the Symfony translation.
In that case just copy the original message from the Symfony
class and add it to your translation file as the `"msgid"` 
for that particular language whose translation you are 
overriding into your custom module.  

### Import the new translation into Drupal.  
Example:  
Let's assume you want to override the original translated 
string of the Dutch language. The original translated
 Dutch string is `Dit is geen geldig internationaal bankrekeningnummer (IBAN).`
```
msgid "This is not a valid International Bank Account Number (IBAN)."  
msgstr "Dit is geen valid IBAN nummer" 
``` 
Now the error message that will be shown will be coming from 
your custom module. To revert back to the Symfony translation, 
just delete the custom translation, update the Drupal translations
and you will then see the original Symfony translation.

### Known problems  
There are modules that will make your application break once 
you enable this module. This is because those modules are type coupling their 
dependencies instead of coding against an interface. That type of programming 
approach leads to the application breaking when a decorator pattern is used.

Below are known modules that are type coupling their dependencies.
- [Entity clone](https://www.drupal.org/project/entity_clone)
- [Entity Browser](https://www.drupal.org/project/entity_browser)

**Solution:**  
You will need to patch those modules by changing their dependencies to type hint 
an interface instead of a concrete or implemented class. In this case the above 
modules make PHP throw a TypeError caused by them injecting the 
concrete class ~~"Drupal\Core\StringTranslation\TranslationManager"~~
instead of injecting the interface : 
`"Drupal\Core\StringTranslation\TranslationInterface"` 
in their constructors.

Please do patch them by injecting the above interface in their constructors 
instead of them injecting the implemented class.

###### Example:  
In `Drupal\entity_browser\Permissions`  
The constructor is like below:  
```
  /**
   * Constructs Permissions object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity manager service.
   * @param \Drupal\Core\StringTranslation\TranslationManager $translation
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, 
TranslationManager $translation) {
```
Please do change it to 
```
  /**
   * Constructs Permissions object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity manager service.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, 
TranslationInterface $translation) {
```

Take note of the `TranslationManager` changing to `TranslationInterface`.

**Note**  
There are more contributed modules you may be using that are not
following good design patterns than the one mentioned above. So 
take note of the errors you get when your application breaks. 
In that case you must patch them as described above.
