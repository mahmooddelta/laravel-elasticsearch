<?php

namespace Afzali\LaravelElasticsearch;

use Illuminate\Support\Facades\Facade;

/**
 * Class Elastic
 *
 * @method static ElasticBuilder index(string $index)
 * @method static ElasticBuilder search()
 * @method static ElasticBuilder size()
 * @method static ElasticBuilder getBody()
 * @method static ElasticBuilder getData()
 * @method static ElasticBuilder paginate()
 * @method static ElasticBuilder count()
 * @method static ElasticBuilder get()
 * @method static ElasticBuilder extractData()
 */
class Elastic extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ElasticBuilder::class;
    }
}
