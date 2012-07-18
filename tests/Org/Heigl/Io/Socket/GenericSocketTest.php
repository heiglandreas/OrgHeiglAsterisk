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
 * @package    Org\Heigl\Io
 * @subpackage Socket
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.beta
 * @since      17.07.2012
 */

namespace Org\Heigl\Io\Socket;

use Org\Heigl\Io\IoException,
    Org\Heigl\Io\UnexpectedValueException,
    \PHPUnit_Framework_TestCase;

/**
 * This class tests the functionality of the class Org\Heigl\Io\Socket\AbstractSocket
 *
 * @package    Org\Heigl\Io
 * @subpackage Socket
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.beta
 * @since      17.7.2012
 */
class GenericSocketTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingStreamReturnsInstance()
    {
        $stream = new GenericSocket();
        $this->assertInstanceOf('\Org\Heigl\Io\Socket\AbstractSocket', $stream);
        $this->assertInstanceOf('\Org\Heigl\Io\Socket\SocketInterface', $stream);
        $this->assertAttributeEquals('', 'protocol', $stream);
        $this->assertAttributeEquals('', 'host', $stream);
        $this->assertAttributeEquals(0, 'port', $stream);
    }

    public function testSendingValues()
    {
        $stream = new GenericSocket();
        $stream->setProtocol('tcp')->setHost('localhost')->setPort(11111);
        $this->assertEquals(7, $stream->send('foobar'));
    }

    public function testReceivingValues()
    {
        $stream = new GenericSocket();
        $stream->setProtocol('tcp')->setHost('localhost')->setPort(11111);
        $this->assertEquals(7, $stream->send('foobar'));
        $this->assertEquals("[ECHO] foobar\n", $stream->receive());
        try{
            $stream->receive(2);
        }catch(IoException $e){
            $this->assertTrue(true);
        }
    }

    public function testExpectingValues(){
        $stream = new GenericSocket();
        $stream->setProtocol('tcp')->setHost('localhost')->setPort(11111);
        $this->assertEquals(23, $stream->send('foobar and other stuff'));
        $this->assertEquals("[ECHO] foobar and other stuff\n",$stream->expect('other', 1));

    }

}