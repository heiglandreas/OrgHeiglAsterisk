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

namespace Org\Heigl\Asterisk;

use Org\Heigl\Io\IoException;
use \Org\Heigl\Io\Socket\SocketInterface;


/**
 * This class handles AsteriskManager-calls
 *
 * @package   Org\Heigl\Asterisk
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2012-2012 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   1.0.beta
 * @link      http://github.com/heiglandreas/Asterisk
 * @since     16.07.2012
 */
class AsteriskManager
{
    /**
     * This holds the Socket-resource
     *
     * @var SocketInterface $socket The socket-object
     */
    protected $socket = null;

    /**
     * Create a new instance
     *
     * @param SocketInterface $socket The socket to use for communicating with the Asterisk server
     *
     * @return void
     */
    public function __construct(SocketInterface $socket)
    {
        $this->socket = $socket;
    }

    /**
     * Log into the Asterisk-Server with the given credentials
     *
     * @param string $user The username to login
     * @param string $pass The password for the user
     *
     * @throws IoException
     * @return AsteriskManager
     */
    public function login($user, $pass)
    {
        $this->socket->send('Action: Login');
        $this->socket->send('UserName: ' . $user);
        $this->socket->send('Secure: ' . $pass);
        $this->socket->send('');
        $this->socket->expect('Logged in');
        return $this;
    }

    /**
     * Log out of the Asterisk-server
     *
     * @throws IoException
     * @return AsteriskManager
     */
    public function logout()
    {
        $this->socket->send('Action: Logoff');
        $this->socket->send('');
        $this->socket->expect('Thanks for all the fish');
        return $this;
    }

    /**
     * Initiate a call
     *
     * Initiating a call causes the given <var>extension</var> to ring and on
     * lifting the receiver the given <var>number</var> is dialed.
     *
     * Additional information can be given like the <var>priority</var>, the
     * <var>context</var> and the <var>callerid</var> which will be displayed
     * to the called phone.
     *
     * @param string $extension The extension from which to call
     * @param string $number    The number to call
     * @param string $priority  The priority
     * @param string $context   The context
     * @param string $callerid  The caller-id
     *
     * @throws IoException
     * @return AsteriskManager
     */
    public function initiateCall($extension, $number, $priority = 1, $context = 'phone', $callerid = null)
    {

        $this->socket->send('Action: Originate');
        $this->socket->send('Channel: ' . $extension);
        $this->socket->send('Context: ' . $context);
        $this->socket->send('Exten: ' . $number);
        $this->socket->send('Priority: ' . $priority);
        if ( null !== $callerid )  {
            $this->socket->send('Callerid: ' . $callerid);
        }
        $this->socket->send('Action: Logoff');
        $this->socket->send('');
        $this->socket->expect('Nothing');

        return $this;

    }

    /**
     * Cleanup the instance
     *
     * @return void
     */
    public function __destruct()
    {
        $this->logout();
    }
}