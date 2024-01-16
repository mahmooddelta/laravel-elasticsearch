<?php

namespace Afzali\LaravelElasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class ElasticBuilder
{
    private \Elastic\Elasticsearch\Client $client;
    private string $index;
    private $response;
    private int $size = 1000;
    private $perPage;
    private mixed $body;

    /**
     * @throws AuthenticationException
     */
    public function __construct()
    {
        $this->perPage = config('elasticsearch.per_page', 10);
        $this->client = ClientBuilder::create()
            ->setHosts([config('elasticsearch.set_host')])
            ->setBasicAuthentication(config('elasticsearch.user'), config('elasticsearch.password'))
            ->build();
    }

    public function index(string $index): static
    {
        $this->index = $index;
        return $this;
    }

    public function size($size): static
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function data(): static
    {
        $this->response = $this->search();
        $this->body = $this->response['hits']['hits'];
        return $this;
    }

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     */
    public function extractData(): static
    {
        $this->data();
        $items = [];
        foreach ($this->body as $hit) {
            $source = $hit['_source'];
            $items[] = $source;
        }
        $this->body = $items;
        return $this;
    }

    public function paginate($perPage = null): LengthAwarePaginator
    {
        if (is_null($perPage)) {
            $perPage = $this->perPage;
        }
        $currentPage = Paginator::resolveCurrentPage();

        $paginatedItems = array_slice($this->body, ($currentPage - 1) * $perPage, $perPage);
        $usersCollection = new Collection($paginatedItems);
        return new LengthAwarePaginator(
            $usersCollection,
            count($this->body),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(): \Elastic\Elasticsearch\Response\Elasticsearch|\Http\Promise\Promise
    {
        return $this->client->search($this->params());
    }

    private function params(): array
    {
        return [
            'index' => $this->index,
            'scroll' => '10m',
            'size' => $this->size,
        ];
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function getBody()
    {
        $this->response = $this->search();
        return $this->response['hits']['hits'];
    }

    public function count()
    {
        return $this->response['hits']['total']['value'];
    }

    public function get()
    {
        return $this->body;
    }
}
