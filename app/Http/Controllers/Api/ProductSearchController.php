<?php

namespace App\Http\Controllers\Api;

use Elastic\Elasticsearch\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProductSearchController extends Controller
{
    public function __invoke(Request $request, Client $client)
    {
        $index = 'products';

        $q          = trim((string) $request->input('q', ''));
        $category   = $request->input('category');        // bv: "papegaai"
        $inStock    = $request->boolean('in_stock', null); // true/false of null
        $priceMin   = $request->input('price_min');       // in euro’s
        $priceMax   = $request->input('price_max');
        $sort       = $request->input('sort', 'relevance'); // relevance | price_asc | price_desc | newest
        $page       = max(1, (int) $request->input('page', 1));
        $size       = min(100, max(1, (int) $request->input('size', 20)));
        $from       = ($page - 1) * $size;

        // basis query: match op meerdere velden met boosting
        $must = [];
        if ($q !== '') {
            $must[] = [
                'multi_match' => [
                    'query'  => $q,
                    'fields' => ['name^5', 'description^2'],
                    'fuzziness' => 'AUTO'
                ]
            ];
        } else {
            // geen zoekterm -> match_all (bijv. categoriepagina)
            $must[] = ['match_all' => (object)[]];
        }

        $filter = [];
        if ($category) {
            $filter[] = ['term' => ['categories' => $category]];
        }
        if (!is_null($inStock)) {
            $filter[] = ['term' => ['in_stock' => $inStock]];
        }
        // prijs in centen (scaled_float met scaling_factor 100)
        if (is_numeric($priceMin)) {
            $filter[] = ['range' => ['price' => ['gte' => (int) round($priceMin * 100)]]];
        }
        if (is_numeric($priceMax)) {
            $filter[] = ['range' => ['price' => ['lte' => (int) round($priceMax * 100)]]];
        }

        $sortClause = [];
        switch ($sort) {
            case 'price_asc':  $sortClause[] = ['price' => 'asc']; break;
            case 'price_desc': $sortClause[] = ['price' => 'desc']; break;
            case 'newest':     $sortClause[] = ['created_at' => 'desc']; break;
            default:           /* relevance */ ; break;
        }

        $body = [
            'from'  => $from,
            'size'  => $size,
            'query' => [
                'bool' => [
                    'must'   => $must,
                    'filter' => $filter,
                ]
            ],
        ];
        if ($sortClause) {
            $body['sort'] = $sortClause;
        }

        $resp = $client->search([
            'index' => $index,
            'body'  => $body,
        ])->asArray();

        // Maak een nette API-respons
        $hits = $resp['hits']['hits'] ?? [];
        $total = is_array($resp['hits']['total'] ?? null)
            ? ($resp['hits']['total']['value'] ?? 0) : 0;

        $items = array_map(function ($hit) {
            $src = $hit['_source'];
            // prijs terug naar euro’s
            $src['price'] = $src['price'] / 100;
            return [
                'id'          => $src['id'],
                'sku'         => $src['sku'],
                'name'        => $src['name'],
                'description' => $src['description'] ?? null,
                'price'       => $src['price'],
                'categories'  => $src['categories'] ?? [],
                'in_stock'    => $src['in_stock'] ?? false,
                'score'       => $hit['_score'] ?? null,
            ];
        }, $hits);

        return response()->json([
            'total' => $total,
            'page'  => $page,
            'size'  => $size,
            'items' => $items,
        ]);
    }
}
