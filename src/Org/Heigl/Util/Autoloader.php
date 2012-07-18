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
 * @package    \Org\Heigl\Util
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.beta
 * @since      17.07.2012
 */

namespace Org\Heigl\Util;

/**
 * This is the autoloader-Class
 *
 * @package    \Org\Heigl\Util
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  (c) 2012-2012 Andreas Heigl <andreas@heigl.org>
 * @license    http://www.opensource.org/license/MIT MIT-License
 * @version    1.0.beta
 * @since      17.07.2012
 */
class Autoloader
{
    /**
     * Holds this packages main Namespace
     *
     * @var string $nsp
     */
    private $nsp = null;

    /**
     * Holds the single instance of the Autoloader
     *
     * @var Autoloader $instance
     */
    private static $instance = null;

    /**
     * Create the autoloader object
     *
     * @return void
     */
    private function __construct()
    {
        $i = strrpos(__NAMESPACE__, '\\');
        $this->nsp = substr(__NAMESPACE__, 0, $i);
    }

    /**
     * Get to the autoloader object
     *
     * @return Autoloader
     */
    public static function getInstance()
    {
        if ( null === self::$instance ) {
            self::$instance = new Autoloader();
        }
        return self::$instance;
    }

    /**
     * autoload classes.
     *
     * @param string $className the name of the class to load
     *
     * @return void
     */
    public function __autoload($className)
    {

        if ( 0 !== strpos($className, $this->nsp) ) {
            return false;
        }
        $className = substr($className, strlen($this->nsp));
        $file = str_replace('\\', '/', $className) . '.php';
        $fileName = dirname(__DIR__) . DIRECTORY_SEPARATOR . $file;
        if ( ! is_readable(realpath($fileName)) ) {
            return false;
        }
        include_once $fileName;
        return true;
    }

    /**
     * Register this packages autoloader with the autoload-stack
     *
     * @return void
     */
    public static function registerAutoload()
    {
        $autoloader = Autoloader::getInstance();
        return spl_autoload_register(array($autoloader, '__autoload'));
    }

    /**
     * Cleanup the Autoloader for testing purposes!
     *
     * @return void
     */
    public static function testcleaner()
    {
        self::$instance = null;
    }
}