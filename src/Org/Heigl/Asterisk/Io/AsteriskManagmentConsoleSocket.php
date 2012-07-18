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
 * @link      http://github.com/heiglandreas/Asterisk
 * @since     16.07.2012
 */

namespace Org\Heigl\Asterisk\Io;

use \Org\Heigl\Io\IoException,
    \Org\Heigl\Io\UnexpectedValueException,
	\Org\Heigl\Io\Socket\AbstractSocket
;

/**
 * This class handles network-connections to an AsteriskManagementConsole-Server
 *
 * @package   Org\Heigl\Asterisk
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   1.0.beta
 * @link      http://github.com/heiglandreas/Asterisk
 * @since     16.07.2012
 */
class AsteriskManagmentConsoleSocket extends AbstractSocket
{
	/**
	 * THe lineend for an asterisk-server
	 * 
	 * @var string LINEEND
	 */
	const LINEEND = "\r\n";
	
	/**
	 * This is the default protocol to be used for this interface
	 * 
	 * @var string $protocol
	 */
	protected $protocol = 'tcp';
	
	/**
	 * This is the default port for the AsteriskManagementConsole
	 * 
	 * @var int $port
	 */
	protected $port = 5038;
	
    /**
     * Send the given request followed by a LINEEND to the server.
     *
     * @param string $request The request to send
     *
     * @see SocketInterface::send()
     * @throws IoException
     * @return integer Number of bytes written to remote host
     */
    public function send($request)
    {

        $socket = $this->getSocket();
        $result = fwrite($socket, $request . self::LINEEND);

        if ($result === false) {
            throw new IoException('Could not send request to ' . $this->host);
        }

        return $result;
    }


    /**
     * Get a line from the stream.
     *
     * @param integer $timeout Per-request timeout value if applicable
     *
     * @see SocketInterface::receive()
     * @throws IoException
     * @return string
     */
    public function receive($timeout = null)
    {
        $socket = $this->getSocket();

        // Adapters may wish to supply per-commend timeouts according to appropriate RFC
        if ($timeout !== null) {
            stream_set_timeout($socket, $timeout);
        }

        // Retrieve response
        $response = fgets($socket, 1024);

        // Check meta data to ensure connection is still valid
        $info = stream_get_meta_data($socket);

        if (!empty($info['timed_out'])) {
            throw new IoException($this->host . ' has timed out');
        }

        if ($response === false) {

            //throw new IoException('Could not read from ' . $this->_host);
        }

        return $response;
    }


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
    public function expect($codes, $timeout = null)
    {
        $exp = false;

        if (!is_array($codes)) {
            $codes = array($codes);
        }

        do {
            try{
                $result = $this->receive($timeout);
            }catch(IoException $e) {
                break;
            }
            foreach ( $codes as $code ) {
                if ( false !== strpos($result, $code) ) {
                    $exp = true;
                }
            }
        } while ( '' != $result);

        if ( false === $exp ) {
            throw new IoException('The Expected String could not be found in the response');
        }

        return $result;
    }
}