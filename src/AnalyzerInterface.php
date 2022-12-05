<?php

namespace ElasticMigrations;

interface AnalyzerInterface
{
    /**
     * get the analysis array
     *
     * @return array
     */
    public static function analysis(): array;
}
