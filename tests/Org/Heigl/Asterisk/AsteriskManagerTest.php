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
 * @package   Org\Heigl\Asterisk
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   1.0.beta
 * @since     17.07.2012
 */

namespace Org\Heigl\Asterisk;

use Org\Heigl\Io\IoException;

use \PHPUnit_Framework_TestCase,
    \Org\Heigl\Io\Socket\SocketInterface,
    \Org\Heigl\Io\Socket\GenericSocket
;

/**
 * This class tests the functionality of the class Org\Heigl\Asterisk\AsteriskManager
 *
 * @package   Org\Heigl\Asterisk
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   1.0.beta
 * @since     17.7.2012
 */
class AsteriskManagerTest extends PHPUnit_Framework_TestCase
{
    public function testCreateNewAsteriskManager()
    {
        $stream = new TestSocket();
        $stream->return[] = 'Thanks for all the fish';
        $manager = new AsteriskManager($stream);
        $this->assertInstanceof('\Org\Heigl\Asterisk\AsteriskManager', $manager);
    }

    public function testCorrectLogin()
    {
        $stream = new TestSocket();
        $stream->return[] = 'Logged in';
        $stream->return[] = 'blueberry';
        $stream->return[] = 'Thanks for all the fish';
        $manager = new AsteriskManager($stream);
        try {
            $manager->login('foo','pass');
            $this->assertTrue(true);
        }catch(\Exception $e){
            $this->assertTrue(false);
        }

    }

    /**
     * @expectedException \Org\Heigl\Io\IoException
     */
    public function testFailingLogin()
    {
        $stream = new TestSocket();
        $stream->return[] = 'Fail';
        $manager = new AsteriskManager($stream);
        try {
            $manager->login('foo','pass');
            $this->assertTrue(true);
        }catch(\Exception $e){
            $this->assertTrue(false);
        }
        $stream->return[] = 'Thanks for all the fish';
        $stream->return[] = '';
    }

    public function testCorrectCallInitialisation()
    {}

    public function testFailingCallInitialisation()
    {}

    public function testLogout()
    {}

    public function testDestroyingManager()
    {}
}
class TestSocket extends GenericSocket
{

    public $return = array();

    public function send($value)
    {
        return strlen($value);
    }

    public function receive($timeout = null)
    {
        $return = array_shift($this->return);
        return $return;
    }
}
