<?php

namespace Yoursquare;

/**
 * Wraps {@link https://github.com/stephenyoung/php-foursquare | php-foursquare} in a Yii extension
 * so it can easily be accessed from a Yii component (e.g. `Yii::app()->yoursquare->getClient()`).
 *
 * This also allows php-foursquare configurations to be set up in the Yii config files and then easily create
 * php-foursquare `FoursquareApi` instances using those configurations anywhere.
 *
 * To use, add this as a Yii component, and set the `clients` property to an array of
 * different `FoursquareApi` instance configurations that you want to use. Different clients are
 * set with different array keys.
 *
 * The php-foursquare library files are not included in this library. You need to download it yourself and
 * then set the `phpFoursquareLibPath` to its location.
 *
 * Here's an example Yii configuration using this library:
 *
 * <code>
 * ...
 * 'components' => array(
 *   'yoursquare' => array(
 *     'class' => '\\Yoursquare\\Yoursquare',
 *     'phpFoursquareLibPath' => '/path/to/php-foursquare/base/folder',
 *     'clients' => array(
 *       'default' => array(
 *         'clientId' => '{YOUR-FOURSQUARE-API-CLIENT-ID}',
 *         'clientSecret' => '{YOUR-FOURSQUARE-API-CLIENT-SECRET}',
 *         'redirectUri' => '',
 *         'version' => 'v2',
 *         'language' => 'en',
 *       ),
 *     ),
 *   ),
 * )
 * ...
 * </code>
 *
 * Then, access your `FoursquareApi` instance by:
 *
 * <code>
 * $client = \Yii::app()->yoursquare->getClient('default');
 * </code>
 *
 * Or, just:
 *
 * <code>
 * $client = \Yii::app()->yoursquare->getClient(); // Assumed "default"
 * </code>
 *
 * The above will return an instance of {@link FoursquareApi} with the config values passed to the constructor.
 * If a `FoursquareApi` instance with the same configuration key was previously created,
 * {@link getClient} will return the previously created instance. If you do not want this behavior,
 * you can use {@link createClient}.
 *
 * @see https://github.com/stephenyoung/php-foursquare/
 * @author Shiki
 */
class Yoursquare extends \CApplicationComponent
{
  /**
   * Should point to the root folder of the php-foursquare library files.
   *
   * @var string
   */
  public $phpFoursquareLibPath;

  /**
   * Client configurations. This is normally set up in Yii config files. This is an array
   * containing configurations for {@link FoursquareApi} instances that will be created using this
   * application component.
   *
   * Sample value:
   *
   * <code>
   * array(
   *   'default' => array(
   *     'clientId' => '{YOUR-FOURSQUARE-API-CLIENT-ID}',
   *     'clientSecret' => '{YOUR-FOURSQUARE-API-CLIENT-SECRET}',
   *     'redirectUri' => '',
   *     'version' => 'v2',
   *     'language' => 'en',
   *   ),
   * )
   * </code>
   *
   * @var array
   */
  public $clients;

  /**
   *
   * @var array
   */
  protected $_clientInstances = array();

  /**
   * {@inheritdoc}
   */
  public function init()
  {
    if (!class_exists('FoursquareApi', false)) {
      $path = rtrim($this->phpFoursquareLibPath, '/') . '/src/FoursquareAPI.class.php';
      require($path);
    }

    if (!is_array($this->clients))
      $this->clients = array();

    parent::init();
  }

  /**
   * Get an instance of `FoursquareApi` using the configuration pointed to by `$key`.
   * This will store the created instance locally and subsequent calls to this method using the same `$key`
   * will return the already created instance.
   *
   * @param string $key The client configuration key that can be found in {@link $clients}.
   * @return \FoursquareApi
   */
  public function getClient($key = 'default')
  {
    if (isset($this->_clientInstances[$key]))
      return $this->_clientInstances[$key];

    $this->_clientInstances[$key] = $this->createClient($key);
    return $this->_clientInstances[$key];
  }

  /**
   * Create an instance of `FoursquareApi` using the configuration pointed to by `$key`.
   *
   * @param string $key The client configuration key that can be found in {@link $clients}.
   * @return \FoursquareApi
   */
  public function createClient($key = 'default')
  {
    $config = isset($this->clients[$key]) ? $this->clients[$key] : array();
    // Apply defaults
    $config = array_merge(array(
      'clientId' => false,
      'clientSecret' => false,
      'redirectUri' => '',
      'version' => 'v2',
      'language' => 'en',
      'api_version' => '20130905'
    ), $config);

    return new \Yoursquare\FoursquareClient($config['clientId'], $config['clientSecret'], $config['redirectUri'],
      $config['version'], $config['language'], $config['api_version']);
  }
}
