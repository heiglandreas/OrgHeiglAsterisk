<?php
/**
 * Copyright (c) 2012-2012 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    Org\Heigl\Util
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.beta
 * @since      17.07.2012
 */

namespace Org\Heigl\Util;

use \PHPUnit_Framework_TestCase;
;
/**
 * Autoload testing
 *
 * @package    Org\Heigl\Util
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.beta
 * @since      17.07.2012
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiatingAutoloader()
    {
        $autoloader = Autoloader::getInstance();
        $this->assertSame(Autoloader::getInstance(), $autoloader);
    }

    public function testIsNotInstantiable()
    {
        $reflectionClass = new \ReflectionClass(__NAMESPACE__ . '\\Autoloader');
        $this->assertFalse($reflectionClass->isInstantiable());
    }

    public function testSettingAutoloader()
    {
        $autoloader = Autoloader::getInstance();
        $autoloaderCallback = array($autoloader, '__autoload');
        $this->assertContains($autoloaderCallback, spl_autoload_functions());
        spl_autoload_unregister($autoloaderCallback);
        $this->assertNotContains($autoloaderCallback, spl_autoload_functions());
        Autoloader::registerAutoload();
        $this->assertContains($autoloaderCallback, spl_autoload_functions());
    }

    public function testingClassLoading()
    {
        $autoloader = Autoloader::getInstance();
        $this->assertTrue($autoloader->__autoload(__NAMESPACE__ . '\\Autoloader'));
        $this->assertFalse($autoloader->__autoload( '\\Autoloader'));
        $this->assertFalse($autoloader->__autoload( __NAMESPACE__ . '\\Foo'));
    }

    public function testForTestCleanup()
    {
        $reflectionClass = new \ReflectionClass(__NAMESPACE__ . '\\Autoloader');
        $this->assertTrue($reflectionClass->hasMethod('testcleaner'));
        $autoloader = Autoloader::getInstance();
        $autoloader->testcleaner();
        $this->assertNotSame($autoloader, Autoloader::getInstance());
    }

}