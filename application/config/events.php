<?php

declare(strict_types=1);

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Event Listeners Configuration
| -------------------------------------------------------------------
| This file contains the mapping of events to their listeners.
| Each event can have multiple listeners that will be executed
| when the event is fired.
|
| Format:
| $config['event-name'] = [
|     'listener-class-name',
|     'another-listener-class-name'
| ];
|
| Example:
| $config['user.registered'] = [
|     'SendWelcomeEmail',
|     'LogUserRegistration'
| ];
|
*/

$config['events'] = [
    'user.registered' => [
        'SendWelcomeEmail',
    ]
];
