README
======

This package allows easy interaction with an Asterisk-Server.

Currently this package handles the following:

* login to the asterisk-server
* logout from the asterisk-server
* originate a call [#]_.

Requirements
------------

This package has the following requirements:

* PHP-Version >= 5.3

Installation
------------
 
Either via PEAR or composer or copy the 'Org/Heigl'-folder somewhere 
to your PHP-include-directory. More information can be found in the doc-section

Usage
-----

::

    <?php
    use \Org\Heigl\Asterisk\AsteriskManager;
    // Initiate the Asterisk Instance
    $asteriskSocket = new AsteriskManagementConsoleSocket('asteriskserver');
    $asterisk = new AsteriskManager($asteriskserver);
    
    // Login
    $asterisk->login($user, $password);
    
    // Initiate a call
    $asterisk->initiateCall($number, $myPhone, $context);
    
    // Now your phone should ring and after lifting the receiver it should connect 
    // to the number you wanted to call.
    ?>

Documentation
-------------

More documentation can be found in the 'doc'-folder

.. [#] http://www.voip-info.org/wiki/view/Asterisk+Manager+API+Action+Originate

