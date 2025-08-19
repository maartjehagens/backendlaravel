<?php

namespace App\Console\Commands;

use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class EsInitProductsIndex extends Command
{
    protected $signature = 'es:init-products-index {--force}';
    protected $description = 'Maak (of hermaak) de Elasticsearch index voor products met mapping';

    public function handle(Client $client): int
    {
        $index = 'products';

        // eventueel oude index weggooien bij --force
        if ($this->option('force') && $client->indices()->exists(['index' => $index])->asBool()) {
            $client->indices()->delete(['index' => $index]);
        }

        if (!$client->indices()->exists(['index' => $index])->asBool()) {
            $client->indices()->create([
                'index' => $index,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        // eenvoudige analyzer die hoofd/kleine letters normaliseert
                        'analysis' => [
                            'analyzer' => [
                                'folded' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'standard',
                                    'filter' => ['lowercase', 'asciifolding']
                                ]
                            ]
                        ]
                    ],
                    'mappings' => [
                        'properties' => [
                            'id'          => ['type' => 'keyword'],
                            'sku'         => ['type' => 'keyword'],
                            'name'        => ['type' => 'text', 'analyzer' => 'folded'],
                            'description' => ['type' => 'text', 'analyzer' => 'folded'],
                            'price'       => ['type' => 'scaled_float', 'scaling_factor' => 100], // €19.95 -> 1995
                            'categories'  => ['type' => 'keyword'],
                            'in_stock'    => ['type' => 'boolean'],
                            'created_at'  => ['type' => 'date'],
                            'updated_at'  => ['type' => 'date'],
                        ]
                    ]
                ]
            ]);
            $this->info("Index [$index] aangemaakt.");
        } else {
            $this->info("Index [$index] bestaat al.");
        }

        return self::SUCCESS;
    }
}
