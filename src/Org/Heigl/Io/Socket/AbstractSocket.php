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
 * @link       http://github.com/heiglandreas/Asterisk
 * @since      16.07.2012
 */

namespace Org\Heigl\Io\Socket;

use \Org\Heigl\Io\IoException,
    \Org\Heigl\Io\UnexpectedValueException;

/**
 * This class handles socket-connections
 *
 * @package    Org\Heigl\Io
 * @subpackage Socket
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.beta
 * @link       http://github.com/heiglandreas/Asterisk
 * @since      16.07.2012
 */
abstract class AbstractSocket implements SocketInterface
{
    /**
     * The characters to use as line-end.
     *
     * @var string LINEEND
     */
    const LINEEND = "\n";

    /**
     * The protocol to use
     *
     * @var string $protocol
     */
    protected $protocol = '';

    /**
     * The Server to connect to
     *
     * @var string $host
     */
    protected $host = '';

    /**
     * The port to connect to
     *
     * @var int $port
     */
    protected $port = 0;

    /**
     * Store the socket for this connection
     *
     * @var resource $socket
     */
    protected $socket = null;

    /**
     * Store the timeout for this stream
     *
     * @var int $timeout
     */
    protected $timeout = 10;

    /**
     * Cleanup before destroying
     *
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Set the connection protocol
     *
     * @param string $type The Type to use.
     *
     *  @return AbstractSocket
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Set the host to connect to
     *
     * @param string $host The host to conect to
     *
     * @return AbstractSocket
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }


    /**
     * Set the port to use
     *
     * @param int $port The port to conncet to
     *
     * @return AbstractSocket
     */
    public function setPort($port)
    {
        $this->port = (int) $port;
        return $this;
    }


    /**
     * Get the connection-URI
     *
     * @throws UnexpectedValueException
     * @return string
     */
    protected function getConnectionUri()
    {
        if ( ! $this->host ) {
            throw new UnexpectedValueException('No host given');
        }
        if ( ! $this->port ) {
            throw new UnexpectedValueException('No Port given');
        }
        if ( ! $this->protocol ) {
            throw new UnexpectedValueException('No Protocol given');
        }
        return $this->protocol . '://' . $this->host . ':' . $this->port;
    }

    /**
     * Get a valid socket
     *
     * @throws IoException
     * @return resource
     */
    protected function getSocket()
    {
        if ( ! is_resource($this->socket) ) {
            $this->connect();
        }

        return $this->socket;
    }


    /**
     * Connect to a server using the supplied transport and target
     *
     * An example $remote string may be 'tcp://mail.example.com:25' or 'ssh://hostname.com:2222'
     *
     * @throws IoException
     * @return boolean
     */
    protected function connect()
    {
        $errorNum = 0;
        $errorStr = '';

        $remote = $this->getConnectionUri();

        // open connection
        $this->socket = @stream_socket_client($remote, $errorNum, $errorStr, $this->timeout);

        if ($this->socket === false) {
            throw new IoException($errorStr);
        }

        if ( false === ($result = stream_set_timeout($this->socket, $this->timeout)) ) {
            throw new IoException('Could not set stream timeout');
        }

        return $result;
    }


    /**
     * Disconnect from remote host and free resource
     *
     * @return void
     */
    protected function disconnect()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }


    /**
     * Send the given request followed by a LINEEND to the server.
     *
     * @param string $request The request to send
     *
     * @see SocketInterface::send()
     * @throws IoException
     * @return integer Number of bytes written to remote host
     */
    abstract public function send($request);

    /**
     * Get a line from the stream.
     *
     * @param integer $timeout Per-request timeout value if applicable
     *
     * @see SocketInterface::receive()
     * @throws IoException
     * @return string
     */
    abstract public function receive($timeout = null);


    /**
     * Parse server response for successful codes
     *
     * Read the response from the stream and check for expected return value.
     * Throws an IoException if an unexpected code is returned.
     *
     * @param string|array $code    One or more strings that indicate a successful response
     * @param int          $timeout per-request timeout
     *
     * @throws IoException
     * @return string Last line of response string
     */
    abstract public function expect($code, $timeout = null);
}