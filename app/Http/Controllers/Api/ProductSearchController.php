<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Elastic\Elasticsearch\Client;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public function __invoke(Request $request, Client $es)
    {
        $q = (string) $request->query('q', '');

        $res = $es->search([
            'index' => 'products',
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'     => $q,
                        'fields'    => ['name^5', 'description^2'],
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ],
        ])->asArray();

        return response()->json($res);
    }
}
