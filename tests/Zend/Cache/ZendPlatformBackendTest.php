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
class Zend_Cache_ZendPlatformBackendTest extends Zend_Cache_CommonBackendTestCase
{
    protected $_instance;
    protected $_className = 'Zend_Cache_Backend_ZendPlatform';

    public function setUp($notag = false): void
    {
        if (!defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') ||
            constant('TESTS_ZEND_CACHE_PLATFORM_ENABLED') === false) {
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
            return;
        } elseif (!function_exists('accelerator_license_info')) {
            $this->markTestSkipped('Extension for Zend Platform is not loaded');
            return;
        }

        if (!function_exists('output_cache_get')) {
            $this->markTestSkipped('Zend Platform is not installed, skipping test');
            return;
        }
        $this->_instance = new Zend_Cache_Backend_ZendPlatform(array());
        parent::setUp($notag);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_ZendPlatform();
    }

    public function testRemoveCorrectCall()
    {
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertTrue($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));
    }

    public function testGetWithAnExpiredCacheId()
    {
        sleep(2);
        $this->_instance->setDirectives(array('lifetime' => 1));
        $this->assertEquals('bar : data to cache', $this->_instance->load('bar', true));
        $this->assertFalse($this->_instance->load('bar'));
        $this->_instance->setDirectives(array('lifetime' => 3600));
    }

    // Because of limitations of this backend...
    public function testCleanModeNotMatchingTags2()
    {
    }
    public function testCleanModeNotMatchingTags3()
    {
    }
    public function testCleanModeOld()
    {
    }
    public function testCleanModeNotMatchingTags()
    {
    }
}
