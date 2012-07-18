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

/**
 * This interface defines the basic methods a socket-class has to implement
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
interface SocketInterface
{

    /**
     * Send the given request to the server.
     *
     * @param string $request
     *
     * @throws IoException
     * @return integer|boolean Number of bytes writen to remote host
     */
    public function send($request);

    /**
     * Get a line from the stream.
     *
     * @param  integer $timeout Per-request timeout value if applicable
     *
     * @throws IoException
     * @return string
     */
    public function receive($timeout = null);

    /**
     * Check for matches in the server response
     *
     * Throws an IoException if one of the given regexes is not matched.
     *
     * @param string|array $code    One or more strings that indicate a successful response
     * @param int          $timeout per-request timeout
     *
     * @throws IoException
     * @return string Last line of response string
     */
    public function expect($code, $timeout = null);
}