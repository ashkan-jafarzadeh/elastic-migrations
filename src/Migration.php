<?php

namespace ElasticMigrations;

use ElasticMigrations\Analyzers\QuarkAnalyzer;
use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;

abstract class Migration implements MigrationInterface
{
    /**
     * add quark analysis and the default fields to index
     *
     * @param Mapping  $mapping
     * @param Settings $settings
     *
     * @return void
     */
    public function additionalMigrations(Mapping $mapping, Settings $settings)
    {
        $mapping->date('created_at', ["format" => "yyyy-MM-dd HH:mm:ss"]);
        $mapping->date('updated_at', ["format" => "yyyy-MM-dd HH:mm:ss"]);
        $mapping->date('deleted_at', ["format" => "yyyy-MM-dd HH:mm:ss"]);

        $settings->analysis(QuarkAnalyzer::analysis());
    }
}
