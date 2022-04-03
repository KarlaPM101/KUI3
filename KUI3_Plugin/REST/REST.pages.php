<?php
function KUIrest_PageBySlug($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);

    $KUI3->Articles_Looping();

    $Page_ID     = $KUI3->Articles_Get_ID();
    $Page_Visits = $KUI3->Articles_Get_Visits();

    $Widgets = explode(';', get_post_meta($Page_ID, 'kui_widgets', true));
    $Widgets = array_filter($Widgets);
    $Widgets = array_unique($Widgets);

    return [
        'ID'      => $Page_ID,
        'Caption' => get_the_title($Page_ID),
        'Excerpt' => get_the_excerpt($Page_ID),
        'Date'    => sprintf('%1$s a las %2$s', get_the_date('', $Page_ID), get_the_time('', $Page_ID)),
        'Visits'  => $Page_Visits,
        'Widgets' => empty($Widgets) ? false : $Widgets,
    ];
}
function KUIrest_PageContentBySlug($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);

    $KUI3->Articles_Looping();

    return $KUI3->Articles_Get_Array(false, true);
}
function KUIrest_PageRefreshBySlug($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);

    $KUI3->Articles_Looping();

    return $KUI3->Articles_Get_Array(false, false, false, true);
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'page/byslug/(?P<slug>.*)/content', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_PageContentBySlug',
        'args'     => [
            'slug' => [
                'default' => '',
            ],
        ]]);
    register_rest_route('kui', 'page/byslug/(?P<slug>.*)/refresh', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_PageRefreshBySlug',
        'args'     => [
            'slug' => [
                'default' => '',
            ],
        ]]);
    register_rest_route('kui', /** @lang RegExp */'page/byslug/(?P<slug>.*(?!content|refresh))', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_PageBySlug',
        'args'     => [
            'slug' => [
                'default' => '',
            ],
        ]]);
});
