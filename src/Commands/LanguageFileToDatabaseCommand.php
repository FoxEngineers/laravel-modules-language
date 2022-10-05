<?php

namespace Nwidart\Modules\Language\Commands;

use Nwidart\Modules\Language\Contracts\TranslationInterface;
use Nwidart\Modules\Language\Services\TranslationRepository;
use Illuminate\Console\Command;

class LanguageFileToDatabaseCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:language-file-to-database {--filters=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install language files into database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $this->callSilent('cache:clear');
        $filters = $this->option('filters');
        /** @var TranslationRepository $translator */
        $translator = resolve(TranslationInterface::class);
        $translator->migrateToDatabase($filters);
        $this->info('Migrated language files to database');

        return 0;
    }
}