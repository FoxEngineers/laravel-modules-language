<?php

namespace Nwidart\Modules\Language\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Language\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected $commands = [
        Commands\LanguageFileToDatabaseCommand::class,
    ];

    public function register(): void
    {
        $this->commands(config('modules-language.commands', $this->commands));
    }

    public function provides(): array
    {
        return $this->commands;
    }
}
