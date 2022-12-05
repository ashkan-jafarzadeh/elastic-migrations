<?php declare(strict_types=1);

namespace ElasticMigrations\Filesystem;

use ElasticMigrations\Exceptions\ModularHandleNotDefined;
use ElasticMigrations\ModuleHandlerInterface;
use ElasticMigrations\ReadinessInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrationStorage implements ReadinessInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $module;

    /**
     * @var ModuleHandlerInterface
     */
    private $module_handler;



    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem     = $filesystem;
        $this->module_handler = $this->resolveModuleHandler();
    }



    public function setModule($module): MigrationStorage
    {
        $this->module = Str::studly($module);

        return $this;
    }



    public function create(string $fileName, string $content): MigrationFile
    {
        if (!$this->filesystem->isDirectory($this->getModulePath())) {
            $this->filesystem->makeDirectory($this->getModulePath(), 0755, true);
        }

        $filePath = $this->resolvePath($fileName);
        $this->filesystem->put($filePath, $content);

        return new MigrationFile($filePath);
    }



    public function findByName(string $fileName): ?MigrationFile
    {
        $filePath = $this->resolvePath($fileName);

        return $this->filesystem->exists($filePath) ? new MigrationFile($filePath) : null;
    }



    public function findAll(): Collection
    {
        $files = [];

        foreach ($this->getPaths() as $path) {
            $files = array_merge($files, $this->filesystem->glob($path . '/*_*.php'));
        }

        return collect($files)->sort()->map(static function (string $filePath) {
            return new MigrationFile($filePath);
        })
        ;
    }



    private function resolvePath(string $fileName): string
    {
        foreach ($this->getPaths() as $path) {
            $path = $path . "/" . str_replace('.php', '', trim($fileName)) . ".php";
            if (File::exists($path)) {
                return $path;
            }
        }

        return sprintf('%s/%s.php', $this->getModulePath(), str_replace('.php', '', trim($fileName)));
    }



    public function isReady(): bool
    {
        return $this->filesystem->isDirectory($this->getKernelPath());
    }



    /**
     * get migration paths, based on the given module name.
     *
     * @return array
     */
    private function getPaths(): array
    {
        // Kernel Only...
        if ($this->module == "kernel") {
            return (array)$this->getKernelPath();
        }

        // One Module Only...
        if ($this->module and $this->module != "all") {
            return [$this->getModulePath()];
        }

        // All Modules...
        $array = (array)$this->getKernelPath();

        foreach ($this->module_handler->list() as $module) {
            $this->module = $module;
            $array[]      = $this->getModulePath();
        }

        $this->module = null;
        return $array;
    }



    /**
     * get kernel path.
     *
     * @return string
     */
    private function getKernelPath(): string
    {
        return config('elastic.migrations.storage_directory', '');
    }



    /**
     * get module migrations path.
     *
     * @return string
     */
    private function getModulePath(): ?string
    {
        if (!$this->module){
            return $this->getKernelPath();
        }

        if (!$this->module_handler->isValid($this->module)) {
            $this->error("Module $this->module does not exist.");
            return null;
        }

        return $this->module_handler->getPath($this->module,
             rtrim(config('modules.directories.elastic_migrations', ''), '/'));
    }



    /**
     * @throws ModularHandleNotDefined
     */
    private function resolveModuleHandler()
    {
        $handler = config("elastic.migrations.module_handler");
        if (class_exists($handler)) {
            $module_handler = new $handler();

            if ($module_handler instanceof ModuleHandlerInterface) {
                return $module_handler;
            }
        }

        throw new ModularHandleNotDefined("ModularHandler is not valid. It should implemented the ModuleHandlerInterface");
    }
}
