<?php

namespace App\Console\Commands;

use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Illuminate\Console\Command;

class EsSyncProducts extends Command
{
    protected $signature = 'es:sync-products {--chunk=1000}';
    protected $description = 'Indexeer alle Product records via Bulk API naar Elasticsearch';

    public function handle(Client $client): int
    {
        $index = 'products';
        $chunk = (int) $this->option('chunk');

        Product::query()->orderBy('id')->chunk($chunk, function ($products) use ($client, $index) {
            $ops = [];

            foreach ($products as $p) {
                $doc = [
                    'id'          => (string) $p->id,
                    'sku'         => $p->sku,
                    'name'        => $p->name,
                    'description' => $p->description,
                    'price'       => (int) round($p->price * 100), // scaled_float (centen)
                    'categories'  => $p->categories?->pluck('slug')->all() ?? [], // pas aan op jouw relatie
                    'in_stock'    => (bool) $p->in_stock,
                    'created_at'  => optional($p->created_at)?->toAtomString(),
                    'updated_at'  => optional($p->updated_at)?->toAtomString(),
                ];

                $ops[] = ['index' => ['_index' => $index, '_id' => (string) $p->id]];
                $ops[] = $doc;
            }

            if ($ops) {
                $resp = $client->bulk(['body' => $ops, 'refresh' => true]);
                $errors = $resp->asArray()['errors'] ?? false;
                if ($errors) {
                    $this->error('Bulk had errors (zie output)'); 
                    $this->line(json_encode($resp->asArray(), JSON_PRETTY_PRINT));
                } else {
                    $this->info('Bulk ok: ' . count($ops)/2 . ' docs');
                }
            }
        });

        return self::SUCCESS;
    }
}
