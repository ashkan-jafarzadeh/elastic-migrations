<?php

namespace ElasticMigrations;

interface ModuleHandlerInterface
{

    /**
     * get the list of modules
     *
     * @return array
     */
    public function list(): array;



    /**
     * check if the module is valid or not
     *
     * @param string|null $module
     *
     * @return bool
     */
    public function isValid(?string $module): bool;



    /**
     * get the module path
     *
     * @param string|null $module
     * @param string|null $additive
     *
     * @return string
     */
    public function getPath(?string $module, ?string $additive = null): string;
}
