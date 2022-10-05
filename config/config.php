<?php

use Nwidart\Modules\Language\Commands\LanguageFileToDatabaseCommand;

return [
   'cache' => false,

    /*
    |--------------------------------------------------------------------------
    | Package commands
    |--------------------------------------------------------------------------
    |
    | Here you can define which commands will be visible and used in your
    | application. If for example you don't use some of the commands provided
    | you can simply comment them out.
    |
    */
    'commands' => [
            LanguageFileToDatabaseCommand::class,
    ],
];