<?php

namespace ElasticMigrations\Analyzers;

use ElasticMigrations\AnalyzerInterface;

/**
 * A combined analyzer of persian and english
 */
class QuarkAnalyzer implements AnalyzerInterface
{

    /**
     * @inheritDoc
     */
    public static function analysis(): array
    {
        return [
             "char_filter" => [
                  "zero_width_spaces" => [
                       "type"     => "mapping",
                       "mappings" => [
                            "\u200C=>\u0020",
                       ],
                  ],
                  "persian_filters"   => static::persianCharFilters(),
             ],
             "filter"      => [
                  "persian_stop"               => [
                       "type"      => "stop",
                       "stopwords" => "_persian_",
                  ],
                  "english_stop"               => [
                       "type"      => "stop",
                       "stopwords" => "_english_",
                  ],
                  "english_keywords"           => [
                       "type"     => "keyword_marker",
                       "keywords" => [
                            "example",
                       ],
                  ],
                  "english_stemmer"            => [
                       "type"     => "stemmer",
                       "language" => "english",
                  ],
                  "english_possessive_stemmer" => [
                       "type"     => "stemmer",
                       "language" => "possessive_english",
                  ],
             ],
             "analyzer"    => [
                  "quark"   => static::getAnalyzer(),
                  "default" => static::getAnalyzer(),
             ],
        ];
    }



    /**
     * get the analyzer
     *
     * @return array
     */
    private static function getAnalyzer(): array
    {
        return [
             "tokenizer"   => "standard",
             "char_filter" => [
                  "zero_width_spaces",
                  "persian_filters",
             ],
             "filter"      => [
                  "lowercase",
                  "decimal_digit",
                  "arabic_normalization",
                  "persian_normalization",
                  //"persian_stop",
                  "english_possessive_stemmer",
                  "english_stop",
                  "english_keywords",
                  "english_stemmer",
             ],
        ];
    }



    /**
     * get the persian character filters
     *
     * @return array
     */
    private static function persianCharFilters(): array
    {
        return [
             "type"     => "mapping",
             "mappings" => [
                  "‌ =>  ",
                  "۱ => 1",
                  "۲ => 2",
                  "۳ => 3",
                  "۴ => 4",
                  "۵ => 5",
                  "۶ => 6",
                  "۷ => 7",
                  "۸ => 8",
                  "۹ => 9",
                  "ّ => ",
                  "ّـ=> ",
                  "۰ => 0",
                  "، => ,",
                  "آ => ا",
                  "ي => ی",
             ],
        ];
    }
}
