<?php
function KUIrest_test(){





}

add_action('rest_api_init', function ()
{
    register_rest_route( 'kui', 'test', [
        'methods' => 'GET',
        'callback' => 'KUIrest_test',
        'args'                => [
            'slug' => [
                'default' => '',
            ],
        ]]);

});