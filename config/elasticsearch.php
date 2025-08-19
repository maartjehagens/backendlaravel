<?php

return [
    /*
     |------------------------------------------------------------------
     | Hosts (self-managed / lokaal)
     |------------------------------------------------------------------
     | Eén of meerdere hosts. Voor Docker-lokaal is http://127.0.0.1:9200 prima.
     */
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'http://127.0.0.1:9200'),
    ],

    /*
     |------------------------------------------------------------------
     | Elastic Cloud (optioneel)
     |------------------------------------------------------------------
     | Als je Elastic Cloud gebruikt, vul dan CLOUD_ID en API key in.
     */
    'cloud_id' => env('ELASTIC_CLOUD_ID'), // bv. "clustername:bG9jYWxob3N0JGUuLi4="
    'api_key'  => env('ELASTIC_API_KEY'),  // Base64 "id:key" of "encoded" variant
];
