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
class Zend_Cache_WinCacheBackendTest extends Zend_Cache_CommonExtendedBackendTestCase
{
    protected $_instance;
    protected $_className = 'Zend_Cache_Backend_WinCache';

    public function setUp($notag = true): void
    {
        if (!defined('TESTS_ZEND_CACHE_WINCACHE_ENABLED') ||
            constant('TESTS_ZEND_CACHE_WINCACHE_ENABLED') === false) {
            $this->markTestSkipped('Tests are not enabled in TestConfiguration.php');
            return;
        } elseif (!extension_loaded('wincache')) {
            $this->markTestSkipped("Extension 'wincache' is not loaded");
            return;
        }

        $this->_instance = new Zend_Cache_Backend_WinCache(array());
        parent::setUp($notag);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_WinCache();
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

    // Because of limitations of this backend...
    public function testGetWithAnExpiredCacheId()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testCleanModeMatchingTags2()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testCleanModeNotMatchingTags2()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testCleanModeNotMatchingTags3()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsMatchingTags()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsMatchingTags2()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsMatchingTags3()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsMatchingTags4()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsNotMatchingTags()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsNotMatchingTags2()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetIdsNotMatchingTags3()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
    }

    public function testGetTags()
    {
        $this->markTestSkipped('This test skipped due to limitations in this adapter.');
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

    public function testGetMetadatas($notag = true)
    {
        parent::testGetMetadatas($notag);
    }
}
