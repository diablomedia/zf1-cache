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
class Zend_Cache_FileBackendTest extends Zend_Cache_CommonExtendedBackendTestCase
{
    protected $_instance;
    protected $_instance2;
    protected $_cache_dir;
    protected $_className = 'Zend_Cache_Backend_File';

    public function setUp($notag = false): void
    {
        $tmpDir           = $this->mkdir();
        $this->_cache_dir = $tmpDir . DIRECTORY_SEPARATOR;
        $this->_instance  = new Zend_Cache_Backend_File(
            array(
            'cache_dir' => $this->_cache_dir,
            )
        );

        $logger = new Zend_Log(new Zend_Log_Writer_Null());
        $this->_instance->setDirectives(array('logger' => $logger));

        parent::setUp($notag);

        set_error_handler(
            static function ($errno, $errstr) {
                throw new \Exception($errstr, $errno);
            },
            E_USER_NOTICE
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->_instance);
        restore_error_handler();
    }

    public function testSetDeprecatedHashedDirectoryUmask()
    {
        try {
            $cache = new Zend_Cache_Backend_File(
                array(
                'cache_dir'              => $this->_cache_dir,
                'hashed_directory_umask' => 0700,
                )
            );
            $this->fail('Missing expected E_USER_NOTICE error');
        } catch (\Exception $e) {
            if ($e->getCode() != E_USER_NOTICE) {
                throw $e;
            }

            $this->assertStringContainsString('hashed_directory_umask', $e->getMessage());
        }
    }

    public function testSetDeprecatedCacheFileUmask()
    {
        try {
            $cache = new Zend_Cache_Backend_File(
                array(
                    'cache_dir'        => $this->_cache_dir,
                    'cache_file_umask' => 0700,
                )
            );
            $this->fail('Missing expected E_USER_NOTICE error');
        } catch (\Exception $e) {
            if ($e->getCode() != E_USER_NOTICE) {
                throw $e;
            }

            $this->assertStringContainsString('cache_file_umask', $e->getMessage());
        }
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_File(array());
    }

    public function testConstructorWithABadFileNamePrefix()
    {
        $this->expectException(Zend_Cache_Exception::class);
        $class = new Zend_Cache_Backend_File(
            array(
            'file_name_prefix' => 'foo bar'
            )
        );
    }

    public function testGetWithANonExistingCacheIdAndANullLifeTime()
    {
        $this->_instance->setDirectives(array('lifetime' => null));
        $this->assertFalse($this->_instance->load('barbar'));
    }

    public function testSaveCorrectCallWithHashedDirectoryStructure()
    {
        $this->_instance->setOption('hashed_directory_level', 2);
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertTrue($res);
    }

    public function testCleanModeAllWithHashedDirectoryStructure()
    {
        // clean files created in setUp (without hashed directory level) first
        $this->assertTrue($this->_instance->clean('all'));

        // set the hashed directory mode
        $this->_instance->setOption('hashed_directory_level', 2);

        // save the data again
        $this->_instance->save('bar : data to cache', 'bar');
        $this->_instance->save('bar2 : data to cache', 'bar2');
        $this->_instance->save('bar3 : data to cache', 'bar3');

        // now delete them
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testSaveWithABadCacheDir()
    {
        $this->_instance->setOption('cache_dir', '/foo/bar/lfjlqsdjfklsqd/');
        $res = $this->_instance->save('data to cache', 'foo', array('tag1', 'tag2'));
        $this->assertFalse($res);
    }

    public function testShouldProperlyCleanCacheNoMatterTheCacheId()
    {
        // the 'zzz' and 'ďťň' keys will be sorted after internal-metadatas file
        $keys = array(
            '9230de5449e0c818ed4804587ed422d5',
            'zzz',
            'Zend_LocaleC_cs_CZ_date_',
            'ďťň'
        );

        foreach ($keys as $key) {
            $this->_instance->save('data to cache', $key);
        }

        $this->assertTrue($this->_instance->clean(Zend_Cache::CLEANING_MODE_ALL));
    }

    /**
     * The CLEANING_MODE_ALL should delete also old orphaned metadatafiles
     */
    public function testShouldDeleteOldMetadataFiles()
    {
        // simulate orphaned metadata file
        $fn = $this->_cache_dir
            . DIRECTORY_SEPARATOR
            . 'zend_cache---internal-metadatas---7a38619e110f03740970cbcd5310f33f';
        $file = fopen($fn, 'a+');
        fclose($file);

        $this->assertFileExists($fn);
        $this->assertTrue($this->_instance->clean(Zend_Cache::CLEANING_MODE_ALL));
        $this->assertFileDoesNotExist($fn);
    }
}
