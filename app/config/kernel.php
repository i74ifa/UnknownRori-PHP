<?php

/**
 * This is where to setting kernel option
 */
return [
    /**
     * see this documentation to learn more about session configuration
     * https://www.php.net/manual/en/session.configuration.php
     */
    "session" => [
        "name" => 'UnknownRori_PHP',
    ],

    /**
     * set allowed request resource
     */
    "regex" => "/\.(?:png|jpg|jpeg|gif|css|js|ico)$/",

    /**
     * This is used to set additional ENV variable
     */
    "ENV" => [
        'ERROR_STYLE' => "background:#f00; color:white; max-width: 100vw; white-space: pre-wrap; padding: 2rem; line-height: 1.5rem",
    ]
];
