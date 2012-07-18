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
class AbstractSocketTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingStreamReturnsInstance()
    {
        $stream = new TestSocket();
        $this->assertInstanceOf('\Org\Heigl\Io\Socket\AbstractSocket', $stream);
        $this->assertInstanceOf('\Org\Heigl\Io\Socket\SocketInterface', $stream);
        $this->assertAttributeEquals('', 'protocol', $stream);
        $this->assertAttributeEquals('', 'host', $stream);
        $this->assertAttributeEquals(0, 'port', $stream);
    }

    public function testSettingType()
    {
        $stream = new TestSocket();
        $this->assertAttributeEquals('', 'protocol', $stream);
        $this->assertSame($stream, $stream->setProtocol('tcp'));
        $this->assertAttributeEquals('tcp', 'protocol', $stream);
    }

    public function testSettingHost()
    {
        $stream = new TestSocket();
        $this->assertAttributeEquals('', 'host', $stream);
        $this->assertSame($stream, $stream->setHost('localhost'));
        $this->assertAttributeEquals('localhost', 'host', $stream);
    }

    public function testSettingPort()
    {
        $stream = new TestSocket();
        $this->assertAttributeEquals(0, 'port', $stream);
        $this->assertSame($stream, $stream->setPort('7'));
        $this->assertAttributeSame(7, 'port', $stream);
    }

    /**
     * @expectedException \Org\Heigl\Io\IoException
     */
    public function testConnectionToInvalidHostThrowsException()
    {
        $obj = new TestSocket();
        $obj->setHost('foo')->setPort(123)->setProtocol('bar');
        $method = \UnitTestHelper::getMethod($obj, 'connect');
        $result = $method->invoke($obj);
    }

    /**
     * @expectedException \Org\Heigl\Io\UnexpectedValueException
     */
    public function testConnectionWithoutHostThrowsException()
    {
        $obj = new TestSocket();
        $obj->setPort(123)->setProtocol('bar');
        $method = \UnitTestHelper::getMethod($obj, 'connect');
        $result = $method->invoke($obj);
    }

    /**
     * @expectedException \Org\Heigl\Io\UnexpectedValueException
     */
    public function testConnectionWithoutTypeThrowsException()
    {
        $obj = new TestSocket();
        $obj->setHost('foo')->setPort(123);
        $method = \UnitTestHelper::getMethod($obj, 'connect');
        $result = $method->invoke($obj);
    }

    /**
     * @expectedException \Org\Heigl\Io\UnexpectedValueException
     */
    public function testConnectionWithoutPortThrowsException()
    {
        $obj = new TestSocket();
        $obj->setHost('foo')->setProtocol('bar');
        $method = \UnitTestHelper::getMethod($obj, 'connect');
        $result = $method->invoke($obj);
    }
    
    /**
     * @expectedException \Org\Heigl\Io\IoException
     */
    public function testConnectionWithWrongPortThrowsException()
    {
    	$obj = new GenericSocket();
    	$obj->setHost('localhost')->setPort(11112)->setProtocol('tcp');
    	$method = \UnitTestHelper::getMethod($obj, 'connect');
    	$result = $method->invoke($obj);
    }
    

    /**
     * This test only works when invoked via ANT as it uses the echoserver
     */
    public function testGettingSocket()
    {
        try {
            $obj = new TestSocket();
            $obj->setHost('localhost')->setPort(11111)->setProtocol('tcp');
            $method = \UnitTestHelper::getMethod($obj, 'getSocket');
            $result = $method->invoke($obj);
            $this->assertTrue(is_resource($result));
        } catch(\Exception $e) {
            $this->assertTrue('false', $e->getMessage());
        }
    }

    public function testConnectionToLocalEchoServer()
    {
        try {
            $obj = new TestSocket();
            $obj->setHost('localhost')->setPort(11111)->setProtocol('tcp');
            $method = \UnitTestHelper::getMethod($obj, 'connect');
            $result = $method->invoke($obj);
            $this->assertTrue(true);
        } catch(\Exception $e) {
            $this->assertTrue('false', $e->getMessage());
        }
    }

    /**
     * @dataProvider gettingRemoteProvider
     */
    public function testGettingRemote($protocol, $host, $port, $expected)
    {
        $obj = new TestSocket();
        $obj->setHost($host)->setPort($port)->setProtocol($protocol);
        $method = \UnitTestHelper::getMethod($obj, 'getConnectionUri');
        $this->assertEquals($expected, $method->invoke($obj));
    }

    public function gettingRemoteProvider()
    {
        return array(
            array('tcp', 'localhost', 1111, 'tcp://localhost:1111'),
            array('foo', 'bar', '1', 'foo://bar:1'),
        );
    }
}

class TestSocket extends AbstractSocket
{
	public function send($content){}
	public function receive($timeout = null){}
	public function expect($val, $timeout = null){}
}