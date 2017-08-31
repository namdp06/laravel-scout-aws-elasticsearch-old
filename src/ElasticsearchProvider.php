<?php

namespace ScoutEngines\Elasticsearch;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Laravel\Scout\EngineManager;
use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder as ElasticBuilder;

class ElasticsearchProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if (getenv('ELASTICSERACH_DRIVER') == 'aws') {
            $this->AwsSolution();
        } else {
            $this->localSolution();
        }
    }

    public function AwsSolution()
    {
        app(EngineManager::class)->extend('elasticsearch', function($app) {
            $provider = CredentialProvider::fromCredentials(
                new Credentials(getenv('AWS_KEY'), getenv('AWS_SECRET'))
            );
            $handler = new ElasticsearchPhpHandler(getenv('AWS_REGION'), $provider);
            return new ElasticsearchEngine(ElasticBuilder::create()
                ->setHandler($handler)
                ->setHosts(config('scout.elasticsearch.hosts'))
                ->build(),
                config('scout.elasticsearch.index')
            );
        });
    }

    public function localSolution()
    {
        app(EngineManager::class)->extend('elasticsearch', function($app) {
            return new ElasticsearchEngine(ElasticBuilder::create()
                ->setHosts(config('scout.elasticsearch.hosts'))
                ->build(),
                config('scout.elasticsearch.index')
            );
        });
    }
}
