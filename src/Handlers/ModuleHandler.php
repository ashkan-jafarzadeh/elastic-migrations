<?php

namespace ElasticMigrations\Handlers;

use ElasticMigrations\ModuleHandlerInterface;

class ModuleHandler implements ModuleHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function list(): array
    {
        return [];
    }



    /**
     * @inheritDoc
     */
    public function isValid($module): bool
    {
        return true;
    }



    /**
     * @inheritDoc
     */
    public function getPath($module, ?string $additive = null): string
    {
        return "";
    }
}
