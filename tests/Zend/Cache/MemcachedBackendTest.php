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
class Zend_Cache_MemcachedBackendTest extends Zend_Cache_CommonExtendedBackendTestCase
{
    protected $_instance;
    protected $_skipped;
    protected $_className = 'Zend_Cache_Backend_Memcached';

    public function setUp($notag = true): void
    {
        if (!defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') ||
            constant('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') === false) {
            $this->_skipped = true;
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
            return;
        } elseif (!extension_loaded('memcache')) {
            $this->_skipped = true;
            $this->markTestSkipped("Extension 'memcache' is not loaded");
            return;
        } else {
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_HOST', '127.0.0.1');
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PORT', 11211);
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT', true);
            }
        }

        if (!class_exists('Memcached')) {
            $this->_skipped = true;
            $this->markTestSkipped('Memcached is not installed, skipping test');
            return;
        }

        $serverValid = array(
            'host'       => TESTS_ZEND_CACHE_MEMCACHED_HOST,
            'port'       => TESTS_ZEND_CACHE_MEMCACHED_PORT,
            'persistent' => TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT
        );
        $serverFail = array(
            'host'       => 'not.exist',
            'port'       => TESTS_ZEND_CACHE_MEMCACHED_PORT,
            'persistent' => TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT
        );
        $options = array(
            'servers' => array($serverValid, $serverFail)
        );
        $this->_instance = new Zend_Cache_Backend_Memcached($options);
        parent::setUp($notag);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_instance);
        if ($this->_skipped === false) {
            // We have to wait after a memcache flush
            sleep(1);
        }
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_Memcached();
    }

    public function testCleanModeOld()
    {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('old');
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeMatchingTags()
    {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('matchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeNotMatchingTags()
    {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('notMatchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testGetWithCompression()
    {
        $this->_instance->setOption('compression', true);
        $this->testGetWithAnExistingCacheIdAndUTFCharacters();
    }

    public function testConstructorWithAnAlternativeSyntax()
    {
        $server = array(
            'host'       => TESTS_ZEND_CACHE_MEMCACHED_HOST,
            'port'       => TESTS_ZEND_CACHE_MEMCACHED_PORT,
            'persistent' => TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT
        );
        $options = array(
            'servers' => $server
        );
        $this->_instance = new Zend_Cache_Backend_Memcached($options);
        $this->testGetWithAnExistingCacheIdAndUTFCharacters();
    }

    // Because of limitations of this backend...
    public function testGetWithAnExpiredCacheId()
    {
    }
    public function testCleanModeMatchingTags2()
    {
    }
    public function testCleanModeNotMatchingTags2()
    {
    }
    public function testCleanModeNotMatchingTags3()
    {
    }
    public function testSaveCorrectCall()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveCorrectCall();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithNullLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithSpecificLifeTime()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithSpecificLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testGetMetadatas($notag = false)
    {
        parent::testGetMetadatas(true);
    }

    public function testGetFillingPercentage()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testGetFillingPercentage();
    }

    public function testGetFillingPercentageOnEmptyBackend()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testGetFillingPercentageOnEmptyBackend();
    }
}
