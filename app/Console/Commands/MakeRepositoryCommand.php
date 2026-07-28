<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name? : Model name (e.g. TourSchedule, Destination)}';

    protected $description = 'Generate a repository contract and its implementation for a given model';

    public function handle(): int
    {
        $name = $this->argument('name');

        if (empty($name)) {
            $name = $this->chooseModel();
            if (empty($name)) {
                return Command::SUCCESS;
            }
        }

        $name = str($name)->replace(['\\', '/'], '')->trim()->studly();

        $contractPath = app_path("Repositories/Contracts/{$name}RepositoryInterface.php");
        $implementPath = app_path("Repositories/Implementations/{$name}RepositoryImplement.php");
        $modelClass = "App\\Models\\{$name}";

        if (File::exists($contractPath)) {
            $this->warn("Contract already exists: {$contractPath}");
        }

        if (File::exists($implementPath)) {
            $this->warn("Implementation already exists: {$implementPath}");
        }

        File::ensureDirectoryExists(dirname($contractPath));
        File::ensureDirectoryExists(dirname($implementPath));

        File::put($contractPath, $this->contractStub($name));

        File::put($implementPath, $this->implementStub($name, $modelClass));

        $this->info("Repository created: {$name}");
        $this->line("  Contract:      app/Repositories/Contracts/{$name}RepositoryInterface.php");
        $this->line("  Implementation: app/Repositories/Implementations/{$name}RepositoryImplement.php");

        return Command::SUCCESS;
    }

    private function contractStub(string $name): string
    {
        return <<<PHP
<?php

namespace App\Repositories\Contracts;

use App\Models\\{$name};
use Illuminate\Database\Eloquent\Collection;

interface {$name}RepositoryInterface extends BaseRepository
{
    //
}

PHP;
    }

    private function implementStub(string $name, string $modelClass): string
    {
        return <<<PHP
<?php

namespace App\Repositories\Implementations;

use {$modelClass};
use App\Repositories\Contracts\\{$name}RepositoryInterface;

class {$name}RepositoryImplement extends BaseRepositoryImplement implements {$name}RepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new {$name});
    }
}

PHP;
    }

    private function chooseModel(): ?string
    {
        $files = File::files(app_path('Models'));
        $models = collect($files)
            ->map(fn ($file) => $file->getFilenameWithoutExtension())
            ->filter(fn ($name) => $name !== 'User')
            ->values();

        if ($models->isEmpty()) {
            $this->info('No models found in app/Models.');
            return null;
        }

        $choice = $this->choice(
            'Select a model to generate its repository',
            $models->toArray(),
        );

        return $choice;
    }
}
