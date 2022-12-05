<?php declare(strict_types=1);

namespace ElasticMigrations\Console;

use Carbon\Carbon;
use ElasticMigrations\Filesystem\MigrationStorage;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class MakeCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'elastic:make:migration';

    /**
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * @var string
     */
    protected $class_name;

    /**
     * @var string
     */
    protected $table;



    public function handle(Filesystem $filesystem, MigrationStorage $migrationStorage): int
    {
        $name             = Str::snake(trim($this->argument('name')));
        $this->class_name = $name;

        $fileName  = sprintf('%s_%s', (new Carbon())->format('Y_m_d_His'), $name);
        $className = Str::studly($name);

        $stub    = $filesystem->get(__DIR__ . '/stubs/migration.blank.stub');
        $content = str_replace('DummyClass', $className, $stub);
        $content = str_replace('Table', $this->detectTableName(), $content);

        $migrationStorage->setModule($this->argument("module"));
        $migrationStorage->create($fileName, $content);

        $this->output->writeln('<info>Created migration:</info> ' . $fileName);

        return 0;
    }



    /**
     * @inheritDoc
     */
    protected function getArguments()
    {
        return [
             ['name', InputArgument::REQUIRED, 'The name of the migration'],
             ['module', InputArgument::OPTIONAL, 'The module name'],
        ];
    }



    /**
     * detect table name
     *
     * @return string
     */
    private function detectTableName(): string
    {
        if (Str::startsWith($this->class_name, "create_")) {
            return Str::before(Str::after($this->class_name, "create_"), "_index");
        }

        if (Str::contains($this->class_name, "_to_")) {
            return Str::before(Str::after($this->class_name, "_to_"), "_index");
        }

        if (Str::contains($this->class_name, "_from_")) {
            return Str::before(Str::after($this->class_name, "_from_"), "_index");
        }

        // It's not detected in cases other than the above.
        return "";
    }
}
