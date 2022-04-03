<?php
function KUIrest_Searcher($Data)
{
    $SearchQuery = $Data['query'];

    $SearchQuery = urldecode($SearchQuery);
    $SearchQuery = sanitize_text_field($SearchQuery);

    if (!$SearchQuery) {
        return new WP_Error('query_not_defined', 'No se han definido términos de búsqueda.');
    }

    $Search = new WP_Query([
        'post_type'      => ['post', 'videopost'],
        'post_status'    => 'publish',
        's'              => $SearchQuery,
        'order'          => 'ASC',
        'orderby'        => 'relevance',
        'posts_per_page' => 10,
    ]);

    if (!$Search->have_posts()) {
        return new WP_Error('query_empty', 'No se han encontrado artículos ni tutoriales.', ['query' => $SearchQuery]);
    }

    $Results = [];

    while ($Search->have_posts()) {
        $Search->the_post();

        $Results[] = [
            'Caption' => get_the_title(),
            'Link'    => get_the_permalink(),
            'Image'   => get_the_post_thumbnail_url(),
        ];
    }

    return $Results;
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'searcher/(?P<query>.*)', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_Searcher',
        'args'     => [
            'query' => [
                'default' => '',
            ],
        ]]
    );
});
