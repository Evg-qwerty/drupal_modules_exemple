Drupal
Источник
https://www.drupal.org
http://niklan.net/
Модуль
В корневой папке modules создаем папку для нашего модуля: helloworld. (При условии что наш модуль будет называться helloworld).
Создаем пустой файл helloworld.info.yml. офдок
# INFO: Комментарии указываются в yml через хэштег.
# Название модуля (отображается в списке модулей).
name: Hello World
# Описание модуля. Пишется исключительно на английском.
description: 'HelloWorld module - is my first module!'
# Объявляем что это модуль.
type: module
# Версия ядра для которого модуль.
core: 8.x
# Версия модуля.
version: 1.0
# Пакет для модуля. По-сути, просто раздел в модулях где будет располагаться наш модуль.
package: Examples


Теперь нам необходимо зарегистрировать url нашей будущей страницы и вывести на ней Hello World.
Для начала нам нужно создать контроллер, который будет отвечать за вывод на страницу:
Создаем в корневой папке модуля новую папку: src.
Создаем в папке src новую папку: Controller. Папка так и должна называться с большой буквы.
Теперь создаем файлик с нашим контроллером, его имя уже не особо имеет значения, давайте назовём его: HelloWorldController.php. Название должно начинаться с большой буквы. (ЗАКРЫВАЮЩИЙ ?> не нужен!)
<?php
/**
 * @file
 * Contains \Drupal\helloworld\Controller\HelloWorldController.
 * ^ Пишется по следующему типу:
 *  - \Drupal - это указывает что данный файл относится к ядру Drupal, ведь
 *    теперь там еще есть Symfony.
 *  - helloworld - название модуля.
 *  - Controller - тип файла. Папка src опускается всегда.
 *  - HelloWorldController - название нашего класса.
 */
/**
* Пространство имен нашего контроллера. Обратите внимание что оно схоже с тем
* что описано выше, только опускается название нашего класса.
*/
namespace Drupal\helloworld\Controller;
/**
* Используем друпальный класс ControllerBase. Мы будем от него наследоваться,
* а он за нас сделает все обязательные вещи которые присущи всем контроллерам.
*/
use Drupal\Core\Controller\ControllerBase;
/**
* Объявляем наш класс-контроллер.
*/
class HelloWorldController extends ControllerBase {
  /**
   * {@inheritdoc}
   *
   * В Drupal 8 очень многое строится на renderable arrays Render arrays и при отдаче
   * из данной функции содержимого для страницы, мы также должны вернуть
   * массив который спокойно пройдет через drupal_render().
   */
  public function helloWorld() {
     $output = array();
     $output['#title'] = 'HelloWorld page title';
     $output['#markup'] = 'Hello World!';
     return $output;
  }


}
Роутер 
https://www.drupal.org/node/2464207
в корневой папке модуля Создаем роутер helloworld.routing.yml. 
# Первым делом объявляется машинное имя роута. Оно составляетсям из:
# название_модуля.машинное_название_роута.
helloworld.hellopage:
 # Указываем путь роута, с лидирующим слешем.
 path: '/hello'
 # Значения по умолчанию
 defaults:
   # Функция контроллера отвечающая за содержимое.
   _controller: '\Drupal\helloworld\Controller\HelloWorldController::helloWorld'
 # В данном разделе указываются необходимые требования для роута.
 requirements:
   # Мы будем показывать страницу только тем, у кого есть права на просмотр
   # содержимого.
   _permission: 'view content'


Блок (Plugin)
Для того чтобы создать модуль типа блок, объявляем свой класс для блока, наследуя от BlockBase и указываем нужные нам методы;


build() 
Обязательный метод, должен всегда возвращать render array
public function build() {
 $block = [
   '#type' => 'markup',
   '#markup' => '<strong>Hello World!</strong>',
 ];
 return $block;
}
blockAccess()
Отвечает за права доступа. Возвращает TRUE/FALSE. Если TRUE, то блок будет доступен для просмотра, во всех остальных случаях не будет отображаться. Здесь вы можете описать любую нужную вам логику.
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;


protected function blockAccess(AccountInterface $account) {
  // Отображаем блок только пользователям у которых есть
  // право доступа 'administer blocks'.
  return AccessResult::allowedIfHasPermission($account, 'administer blocks');
}
defaultConfiguration()
Если вы хотите добавить свой блок форму с настройками, то в данном методе вы можете предоставить значения по умолчанию для блока.
public function defaultConfiguration() {
  // Если, допустим, ваш блок выводит последних зарегистрированных
  // пользователей и вы хотите дать возможность выбирать сколько
  // показывать пользователей в блоке, то вы можете указать значение
  // по умолчанию. Например 10.
  return array(
     'users_count' => 10,
  );
}
blockForm()
При помощи данного метода вы сможете объявить форму с настройками для данного блока используя Form API.
use Drupal\Core\Form\FormStateInterface;
public function blockForm($form, FormStateInterface $form_state) {
  // Получаем оригинальную форму для блока, и добавляем
  // наши новые элементы прямо к ней.
  $form = parent::blockForm($form, $form_state);


  // Получаем конфиги для данного блока.
  $config = $this->getConfiguration();


  // Добавляем наше поле к форме.
  $form['email'] = array(
     '#type' => 'email',
     '#title' => t('E-mail address to send notification'),
     '#default_value' => isset($config['email_to_send']) ? $config['email_to_send'] : '',
  );
  return $form;
}
blockValidate()
Как и в Form API, здесь вы можете провести валидацию введенных данных в форме. В данном случае, есть форма по умолчанию, следовательно, даже не объявляя buildForm() вы можете объявлять данный метод и проводить валидацию.
use Drupal\Core\Form\FormStateInterface;
public function blockValidate($form, FormStateInterface $form_state) {
  $ages = $form_state->getValue('ages');
  if (!is_numeric($ages)) {
     $form_state->setErrorByName('ages', t('Needs to be an interger'));
  }
}
blockSubmit()
В случае с данным методом, всё точно также как и с blockValidate(), вы можете определять его не имея собственной формы, тем самым внедряясь в процесс отправки данных формы с настройками для блока и внедрения собственной логики.
use Drupal\Core\Form\FormStateInterface;
public function blockSubmit($form, FormStateInterface $form_state) {
  $this->configuration['email_to_send'] = $form_state->getValue('email_to_send');
}
Render arrays
Render arrays
При создании модулей, блоков и т.п. данные для вывода нужно сформировать в специальный массив 
Например вывод простого блока с текстом


public function build() {
 $block = [
   '#type' => 'markup',
   '#markup' => '<strong>Hello World!</strong>',
 ];
 return $block;
}
Данные в массиве передаются в виде свойств


Property
Description
#type
The element type. If this array is an element, this will cause the default element properties to be loaded, so in many ways this is shorthand for a set of predefined properties which will have been arranged through a RenderElement plugin.
#cache
Mark the array as cacheable and determine its expiration time, etc. Once the given render array has been rendered, it will not be rendered again until the cache expires or is invalidated. Uses the Cache API. This property accepts the following subproperties:
'keys'
'contexts'
'tags'
'max-age'
'bin'
See Cacheability to learn more. It's very important to set #cache correctly!
#markup
Specifies that the array provides HTML markup directly. Unless the markup is very simple, such as an explanation in a paragraph tag, it is normally preferable to use #theme or #type instead, so that the theme can customize the markup. Note that the value is passed through \Drupal\Component\Utility\Xss::filterAdmin(), which strips known XSS vectors while allowing a permissive list of HTML tags that are not XSS vectors. (I.e, <script> and <style> are not allowed.)
#plain_text
Specifies that the array provides text that needs to be escaped. This value takes precedence over #markup if present.
#prefix/#suffix
A string to be prefixed or suffixed to the element being rendered
#pre_render
An array of functions which may alter the actual render array before it is rendered. They can rearrange, remove parts, set #printed = TRUE to prevent further rendering, etc.
#post_render
An array of functions which may operate on the rendered HTML after rendering. A #post_render function receives both the rendered HTML and the render array from which it was rendered, and can use those to change the rendered HTML (it could add to it, etc.). This is in many ways the same as #theme_wrappers except that the theming subsystem is not used.
#theme
A single theme function/template which will take full responsibility for rendering this array element, including its children. It has predetermined knowledge of the structure of the element.
#theme_wrappers
An array of theme hooks which will get the chance to add to the rendering after children have been rendered and placed into #children. This is typically used to add HTML wrappers around rendered children, and is commonly used when the children are being rendered recursively using their own theming information. It is rare to use it with #theme.




Пример простого блока
Для создания блока, нам, первым делом, нужно создать файл для класса, через который мы и объявим блок. Классы plugin-блоков хранятся в папке /src/Plugin/Block/BlockName.php
Допустим мы хотим сделать блок SimpleExampleBlock, для этого нам нужно создать файл по данному пути /src/Plugin/Block/SimpleBlockExample.php относительно корня модуля в котором мы его объявляем.
<?php
/**
* @file
* Contains \Drupal\helloworld\Plugin\Block\SimpleBlockExample.
*/
// Пространство имен для нашего блока.
// helloworld - это наш модуль.
namespace Drupal\helloworld\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
* Добавляем простой блок с текстом.
* Ниже - аннотация, она также обязательна.
*
* @Block(
*   id = "simple_block_example",
*   admin_label = @Translation("Simple block example"),
* )
*/
class SimpleBlockExample extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
     $block = [
        '#type' => 'markup',
        '#markup' => '<strong>Hello World!</strong>'
     ];
     return $block;
  }
}
Блок с собственной формой
Допустим, пусть у нас в форме будет два поля. Первое - текстовое, в котором мы будем вводить строку для содержимого нашего блока, а второе - числовое поле, в котором мы будем писать сколько раз вывести наше сообщение из первого поля в блоке. При этом, оба поля будут обязательными, первое будет требовать минимум 5 символов для ввода, а второе, чтобы введенное число было больше или равнялось единице (1).


Пусть наш блок называется PrintMyMessages, следовательно, нам нужно создать файл /src/Plugin/Block/PrintMyMessages.php. И следующего содержания:
<?php
/**
* @file
* Contains \Drupal\helloworld\Plugin\Block\PrintMyMessages.
*/
// Пространство имён для нашего блока.
namespace Drupal\helloworld\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
/**
* @Block(
*   id = "print_my_messages",
*   admin_label = @Translation("Print my messages"),
* )
*/
class PrintMyMessages extends BlockBase {
  /**
   * Добавляем наши конфиги по умолчанию.
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
     return array(
        'count' => 1,
        'message' => 'Hello World!',
     );
  }
  /**
   * Добавляем в стандартную форму блока свои поля.
   *
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
     // Получаем оригинальную форму для блока.
     $form = parent::blockForm($form, $form_state);
     // Получаем конфиги для данного блока.
     $config = $this->getConfiguration();
     // Добавляем поле для ввода сообщения.
     $form['message'] = array(
        '#type' => 'textfield',
        '#title' => t('Message to printing'),
        '#default_value' => $config['message'],
     );
     // Добавляем поле для количества сообщений.
     $form['count'] = array(
        '#type' => 'number',
        '#min' => 1,
        '#title' => t('How many times display a message'),
        '#default_value' => $config['count'],
     );


     return $form;
  }
  /**
   * Валидируем значения на наши условия.
   * Количество должно быть >= 1,
   * Сообщение должно иметь минимум 5 символов.
   *
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
     $count = $form_state->getValue('count');
     $message = $form_state->getValue('message');
     // Проверяем введенное число.
     if (!is_numeric($count) || $count < 1) {
        $form_state->setErrorByName('count', t('Needs to be an interger and more or equal 1.'));
     }
     // Проверяем на длину строки.
     if (strlen($message) < 5) {
        $form_state->setErrorByName('message', t('Message must contain more than 5 letters'));
     }
  }
  /**
   * В субмите мы лишь сохраняем наши данные.
   *
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
     $this->configuration['count'] = $form_state->getValue('count');
     $this->configuration['message'] = $form_state->getValue('message');
  }
  /**
   * Генерируем и выводим содержимое блока.
   *
   * {@inheritdoc}
   */
  public function build() {
     $config = $this->getConfiguration();
     $message = '';
     for ($i = 1; $i <= $config['count']; $i++) {
        $message .= $config['message'] . '<br />';
     }
     $block = [
        '#type' => 'markup',
        '#markup' => $message,
     ];
     return $block;
  }
}


