<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_TwoLevelsBackendTest extends Zend_Cache_CommonExtendedBackendTestCase
{
    protected $_instance;
    private $_cache_dir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_TwoLevels', $data, $dataName);
    }

    public function setUp($notag = false): void
    {
        if ((!defined('TESTS_ZEND_CACHE_APC_ENABLED') ||
            constant('TESTS_ZEND_CACHE_APC_ENABLED') === false) &&
            (!defined('TESTS_ZEND_CACHE_WINCACHE_ENABLED') ||
            constant('TESTS_ZEND_CACHE_WINCACHE_ENABLED') === false)) {
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
            return;
        } elseif (!extension_loaded('apc') && !extension_loaded('apcu') && !extension_loaded('wincache')) {
            $this->markTestSkipped("Extension 'APC' and 'wincache' are not loaded");
            return;
        }

        $dir = $this->getTmpDir();
        @mkdir($dir);
        $this->_cache_dir   = $dir . DIRECTORY_SEPARATOR;
        $slowBackend        = 'File';
        $fastBackend        = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $this->_instance = new Zend_Cache_Backend_TwoLevels(
            array(
            'fast_backend'         => $fastBackend,
            'slow_backend'         => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
            )
        );
        parent::setUp($notag);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_instance);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCorrectCall()
    {
        $slowBackend        = 'File';
        $fastBackend        = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $test = new Zend_Cache_Backend_TwoLevels(
            array(
            'fast_backend'         => $fastBackend,
            'slow_backend'         => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
            )
        );
    }

    public function testSaveOverwritesIfFastIsFull()
    {
        $slowBackend = 'File';
        $fastBackend = $this->getMockBuilder('Zend_Cache_Backend_Apc')
            ->setMethods(array('getFillingPercentage'))
            ->getMock();
        $fastBackend->expects($this->at(0))
            ->method('getFillingPercentage')
            ->will($this->returnValue(0));
        $fastBackend->expects($this->at(1))
            ->method('getFillingPercentage')
            ->will($this->returnValue(90));


        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $cache = new Zend_Cache_Backend_TwoLevels(
            array(
            'fast_backend'         => $fastBackend,
            'slow_backend'         => $slowBackend,
            'slow_backend_options' => $slowBackendOptions,
            'stats_update_factor'  => 1
            )
        );

        $id = 'test' . uniqid();
        $this->assertTrue($cache->save(10, $id)); //fast usage at 0%

        $this->assertTrue($cache->save(100, $id)); //fast usage at 90%
        $this->assertEquals(100, $cache->load($id));
    }

    /**
     * @group ZF-9855
     */
    public function testSaveReturnsTrueIfFastIsFullOnFirstSave()
    {
        $slowBackend = 'File';
        $fastBackend = $this->getMockBuilder('Zend_Cache_Backend_Apc')
            ->setMethods(array('getFillingPercentage'))
            ->getMock();
        $fastBackend->expects($this->any())
            ->method('getFillingPercentage')
            ->will($this->returnValue(90));

        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $cache = new Zend_Cache_Backend_TwoLevels(
            array(
            'fast_backend'         => $fastBackend,
            'slow_backend'         => $slowBackend,
            'slow_backend_options' => $slowBackendOptions,
            'stats_update_factor'  => 1
            )
        );

        $id = 'test' . uniqid();

        $this->assertTrue($cache->save(90, $id)); //fast usage at 90%, failing for
        $this->assertEquals(90, $cache->load($id));

        $this->assertTrue($cache->save(100, $id)); //fast usage at 90%
        $this->assertEquals(100, $cache->load($id));
    }
}
