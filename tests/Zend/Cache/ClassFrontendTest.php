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
 * @todo: Should this class be named Zend_Cache_Something?
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class test
{
    private $_string = 'hello !';

    public static function foobar($param1, $param2)
    {
        echo "foobar_output($param1, $param2)";
        return "foobar_return($param1, $param2)";
    }

    public function foobar2($param1, $param2)
    {
        echo($this->_string);
        echo "foobar2_output($param1, $param2)";
        return "foobar2_return($param1, $param2)";
    }

    public function foobar3($param1, $param2)
    {
        echo $this->dummyMethod($param1, $param2);
    }

    private function dummyMethod($param1, $param2)
    {
        return "foobar_output($param1,$param2)";
    }

    public function throwException()
    {
        echo 'throw exception';
        throw new Exception('test exception');
    }
}

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_ClassFrontendTest extends PHPUnit\Framework\TestCase
{
    private $_instance1;
    private $_instance2;
    protected $_backend1;
    protected $_backend2;

    public function setUp(): void
    {
        if (!$this->_instance1) {
            $options1 = array(
                'cached_entity' => 'test'
            );
            $this->_instance1 = new Zend_Cache_Frontend_Class($options1);
            $this->_backend1  = new Zend_Cache_Backend_Test();
            $this->_instance1->setBackend($this->_backend1);
        }
        if (!$this->_instance2) {
            $options2 = array(
                'cached_entity' => new test()
            );
            $this->_instance2 = new Zend_Cache_Frontend_Class($options2);
            $this->_backend2  = new Zend_Cache_Backend_Test();
            $this->_instance2->setBackend($this->_backend2);
        }
    }

    public function tearDown(): void
    {
        unset($this->_instance1);
        unset($this->_instance2);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCorrectCall1()
    {
        $options = array(
            'cache_by_default' => false,
            'cached_entity'    => 'test'
        );
        $test = new Zend_Cache_Frontend_Class($options);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCorrectCall2()
    {
        $options = array(
            'cache_by_default' => false,
            'cached_entity'    => new test()
        );
        $test = new Zend_Cache_Frontend_Class($options);
    }

    public function testConstructorBadCall()
    {
        $options = array(
            'cached_entity' => new test(),
            0               => true,
        );
        $this->expectException(Zend_Cache_Exception::class);
        $test = new Zend_Cache_Frontend_Class($options);
    }

    public function testCallCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance1->foobar('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }

    public function testCallCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance1->foobar('param3', 'param4');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param3, param4)', $return);
        $this->assertEquals('foobar_output(param3, param4)', $data);
    }

    public function testCallCorrectCall3()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance2->foobar2('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }

    public function testCallCorrectCall4()
    {
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance2->foobar2('param3', 'param4');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar2_return(param3, param4)', $return);
        $this->assertEquals('hello !foobar2_output(param3, param4)', $data);
    }

    public function testCallCorrectCall5()
    {
        // cacheByDefault = false
        $this->_instance1->setOption('cache_by_default', false);
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance1->foobar('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    public function testCallCorrectCall6()
    {
        // cacheByDefault = false
        // cachedMethods = array('foobar')
        $this->_instance1->setOption('cache_by_default', false);
        $this->_instance1->setOption('cached_methods', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance1->foobar('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('bar', $return);
        $this->assertEquals('foo', $data);
    }

    public function testCallCorrectCall7()
    {
        // cacheByDefault = true
        // nonCachedMethods = array('foobar')
        $this->_instance1->setOption('cache_by_default', true);
        $this->_instance1->setOption('non_cached_methods', array('foobar'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance1->foobar('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar_return(param1, param2)', $return);
        $this->assertEquals('foobar_output(param1, param2)', $data);
    }

    /**
     * @group GH-125
     */
    public function testCallCorrectCall8()
    {
        $this->_instance2->setOption('cache_by_default', true);
        $this->_instance2->setOption('cached_methods', array('foobar3'));
        ob_start();
        ob_implicit_flush(false);
        $return = $this->_instance2->foobar3('param1', 'param2');
        $data   = ob_get_clean();
        ob_implicit_flush(true);

        $this->assertNull($return);
        $this->assertEquals('foobar_output(param1,param2)', $data);
    }

    public function testConstructorWithABadCachedEntity()
    {
        $this->expectException(Zend_Cache_Exception::class);
        $options = array(
            'cached_entity' => array()
        );
        $instance = new Zend_Cache_Frontend_Class($options);
    }

    /**
     * @group ZF-5034
     * @doesNotPerformAssertions
     */
    public function testCallingConstructorWithInvalidOptionShouldNotRaiseException()
    {
        $options = array(
            'cached_entity'           => new test(),
            'this_key_does_not_exist' => true
        );
        $test = new Zend_Cache_Frontend_Class($options);
    }

    /**
     * @ZF-10521
     */
    public function testOutputBufferingOnException()
    {
        ob_start();
        ob_implicit_flush(false);

        echo 'start';
        try {
            $this->_instance2->throwException();
            $this->fail('An exception should be thrown');
        } catch (Exception $e) {
        }
        echo 'end';

        $output = ob_get_clean();
        $this->assertEquals('startend', $output);
    }

    /**
     * @group ZF-11337
     */
    public function testThrowExceptionOnInvalidCallback()
    {
        $this->expectException('Zend_Cache_Exception');
        $this->_instance2->unknownMethod();
    }
}
