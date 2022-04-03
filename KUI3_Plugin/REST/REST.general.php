<?php
function KUIrest_GenNavigation()
{
    $PrintMenu = [
        [
            'Title' => 'Portada',
            'Icon'  => '/wp-content/uploads/2019/06/icons8-casa-480.png',
            'Url'   => '/',
            'Menu'  => false,
            'KUI'   => true,
        ],
        [
            'Title' => 'Blog',
            'Icon'  => '/wp-content/uploads/2019/06/icons8-literatura-48.png',
            'Url'   => '/blog',
            'KUI'   => true,
            'Menu'  => [
                [
                    'Title' => 'Articulos',
                    'Icon'  => '/wp-content/uploads/2019/07/icons8-google-blog-search-48.png',
                    'KUI'   => true,
                    'Url'   => '/blog/articulos',
                ],
                [
                    'Title' => 'Reviews',
                    'Icon'  => '/wp-content/uploads/2019/06/icons8-estrella-de-navidad-48.png',
                    'KUI'   => true,
                    'Url'   => '/blog/reviews',
                ],
                [
                    'Title' => 'Noticias',
                    'Icon'  => '/wp-content/uploads/2019/07/icons8-buz%C3%B3n-con-carta-48.png',
                    'KUI'   => true,
                    'Url'   => '/blog/noticias',
                ],
            ],
        ],
        [
            'Title' => 'Tutoriales',
            'Icon'  => '/wp-content/uploads/2020/09/icons8-youtube-play-48.png',
            'Url'   => '/videos',
            'KUI'   => true,
            'Menu'  => false,
        ],
        [
            'Title' => 'GNU/Linux',
            'Icon'  => '/wp-content/themes/karlasflex/imaging/assets/icons/menu/linux.svg',
            'Url'   => '/blog/publicaciones/linux',
            'KUI'   => true,
            'Menu'  => [
                [
                    'Title' => 'Linuxpedia',
                    'Icon'  => '/wp-content/themes/karlasflex/imaging/assets/icons/menu/wiki.png',
                    'Url'   => '/wiki/',
                    'Menu'  => false,
                    'KUI'   => true,
                ],
                [
                    'Title' => 'Historia de GNU',
                    'Icon'  => '/wp-content/themes/karlasflex/imaging/assets/icons/menu/gnu.svg',
                    'Url'   => '/gnulinux-historia/',
                    'Menu'  => false,
                    'KUI'   => true,
                ],
                /*[
            'Title' => 'Guia de Linux',
            'Icon'  => '/wp-content/uploads/2019/11/shelf.png',
            'Url'   => '/guia-linux',
            'Menu'  => false
            ],*/
            ],
        ],
        [
            'Title' => 'Eventos',
            'Icon'  => '/wp-content/uploads/2019/10/icons8-calendar-48.png',
            'Url'   => '/eventos',
            'Menu'  => false,
        ],
        [
            'Title' => 'Conóceme',
            'Icon'  => '/wp-content/uploads/2019/06/icons8-alumna-48.png',
            'Url'   => '/informacion/sobre-mi',
            'KUI'   => true,
            'Menu'  => [
                [
                    'Title' => 'Mi Setup',
                    'Icon'  => '/wp-content/uploads/2019/07/icons8-ordenador-port%C3%A1til-48.png',
                    'Url'   => '/informacion/mi-setup',
                    'KUI'   => true,
                ],
            ],
        ],
        [
            'Title' => 'Colaboraciones',
            'Icon'  => '/wp-content/uploads/2020/07/icons8-gente-trabajando-juntos-48.png',
            'Url'   => '/colaboraciones',
            'Menu'  => false,
        ],
        [
            'Title' => 'Viernes de Escritorio',
            'Icon'  => '/wp-content/uploads/2021/03/brush.png',
            'Url'   => '/viernesdeescritorio',
            'KUI'   => true,
            'Menu'  => false,
        ],
        [
            'Title' => 'Grupo de Telegram',
            'Icon'  => '/wp-content/uploads/2019/06/icons8-aplicacic3b3n-telegrama-48.png',
            'Url'   => '/telegram',
            'Menu'  => [
                /*[
                'Title' => 'Miembros',
                'Icon'  => '/wp-content/uploads/2021/02/icons8-user-group-48.png',
                'Url'   => '/telegram/miembros',
                ],*/
                [
                    'Title' => 'Normas',
                    'Icon'  => '/wp-content/uploads/2021/02/icons8-quran-48.png',
                    'Url'   => '/telegram/normas',
                    'KUI'   => true,
                ],
                [
                    'Title' => 'Comandos',
                    'Icon'  => '/wp-content/uploads/2021/02/icons8-console-48.png',
                    'Url'   => '/telegram/comandos',
                    'KUI'   => true,
                ],
            ],
        ],
    ];

    return $PrintMenu;
}
function KUIrest_GenVideolist()
{
    $VideoList = get_terms([
        'taxonomy'   => 'list',
        'orderby'    => 'parent',
        'order'      => 'ASC',
        'hide_empty' => true,
        'parent'     => false,
    ]);

    $ReturnArray = [];

    foreach ($VideoList as $List) {
        $Display = $List->name;
        $Slug    = $List->slug;

        $ReturnArray[] = [
            'Title' => $Display,
            'Goto'  => '/videos/' . $Slug,
            'Child' => false,
        ];

        $VideoListChilds = get_terms([
            'taxonomy'   => 'list',
            'orderby'    => 'parent',
            'order'      => 'ASC',
            'hide_empty' => true,
            'parent'     => $List->term_id,
        ]);

        foreach ($VideoListChilds as $Child) {
            $ReturnArray[] = [
                'Title' => $Child->name,
                'Goto'  => '/videos/' . $List->slug . '/' . $Child->slug,
                'Child' => true,
            ];
        }
    }

    return $ReturnArray;
}
function KUIrest_GenDesktopslist($Data)
{

    $Year      = (int) isset($Data['Year']) ? sanitize_text_field($Data['Year']) : date('Y');
    $ShowYears = (boolean) isset($Data['ShowYears']) ? sanitize_text_field($Data['ShowYears']) : false;

    if ($ShowYears) {
        $Desktops = new WP_Query([
            'post_type'      => 'viernesdeescritorio',
            'posts_per_page' => -1,
            'order'          => 'DESC',
            'orderby'        => 'date',
        ]);

        $Weeks = [];
        while ($Desktops->have_posts()) {
            $Desktops->the_post();
            if (isset($Weeks[(int) get_the_date('Y')])) {
                continue;
            }

            $Weeks[(int) get_the_date('Y')] = [
                'Title' => get_the_date('Y'),
                'Goto'  => '/viernesdeescritorio/' . get_the_date('W') . '/' . get_the_date('Y'),
            ];
        }

        krsort($Weeks);
    } else {
        $Desktops = new WP_Query([
            'post_type'      => 'viernesdeescritorio',
            'date_query'     => [
                [
                    'year' => (int) $Year,
                ],
            ],
            'order'          => 'DESC',
            'orderby'        => 'date',
            'posts_per_page' => -1,
        ]);

        while ($Desktops->have_posts()) {
            $Desktops->the_post();
            if (isset($Weeks[(int) get_the_date('W')])) {
                continue;
            }

            $Weeks[(int) get_the_date('W')] = [
                'Title' => sprintf('Semana #%1$s del %2$s', get_the_date('W'), get_the_date('Y')),
                'Goto'  => '/viernesdeescritorio/' . get_the_date('W') . '/' . get_the_date('Y'),
            ];
        }

        krsort($Weeks);

        $Weeks = ['featured' => [
            'Title' => 'Escritorios Destacados',
            'Goto'  => '/viernesdeescritorio/featured/' . $Year,
        ]] + $Weeks;

    }

    return $Weeks;
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'menu',
        [
            'methods'  => 'GET',
            'callback' => 'KUIrest_GenNavigation',
        ]
    );
    register_rest_route('kui', 'videolist',
        [
            'methods'  => 'GET',
            'callback' => 'KUIrest_GenVideolist',
        ]
    );
    register_rest_route('kui', 'desktopweeks',
        [
            'methods'  => 'GET',
            'callback' => 'KUIrest_GenDesktopslist',
        ]
    );
});
