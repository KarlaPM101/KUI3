<?php
/* ####################################################
 * SETUP INICIAL
 */####################################################

add_action('after_setup_theme', function () {
    load_theme_textdomain('karlasflexs', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(1920, 9999);
    register_nav_menus(
        array(
            'menu-1' => __('Menú Principal', 'karlasflexs'),
            'social' => __('Enlaces Sociales', 'karlasflexs'),
            'footer' => __('Pie de Página', 'karlasflexs'),
        )
    );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        )
    );
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');
    remove_theme_support( 'widgets-block-editor' );
});

/* ####################################################
 * DEFINICIONES
 */####################################################

add_action('after_setup_theme',function () {
    $GLOBALS['content_width'] = apply_filters('karlasflexs_content_width', 640);

    if(isset($_GET['_kui_noscript']) && $_GET['_kui_noscript']){
        setcookie('kui3_noscript','1',0);
    }

}, 0);

add_filter('show_admin_bar', '__return_false');

/* ####################################################
 * CABECERAS (CSS & JS)
 */####################################################

add_action('wp_head', function(){

}, 100);

/* ####################################################
 * GUTENBERG STYLES
 */####################################################

add_action('init', function() {
    register_block_style('core/image', [
        'name' => 'kui_decoration',
        'label' => 'KUI Decoration',
    ]);
    register_block_style('core/group', [
        'name' => 'kui_block',
        'label' => 'KUI Box',
    ]);
});

/* ####################################################
 * REWRITE RULES
 */####################################################

add_action('init', function () {
    /* Blog */
    add_rewrite_rule('^blog/([^/]*)/([^/]*)/?$', 'index.php?pagename=Blog&blg_category=$matches[1]&blg_subcategory=$matches[2]', 'top');
    add_rewrite_rule('^blog/([^/]*)/?$', 'index.php?pagename=Blog&blg_category=$matches[1]', 'top');
    /* Videos */
    add_rewrite_rule('^videos/([^/]*)/([^/]*)/([^/]*)/?$', 'index.php?page_id=6662&blg_category=$matches[1]&blg_subcategory=$matches[2]&blg_subsubcategory=$matches[3]', 'top');
    add_rewrite_rule('^videos/([^/]*)/([^/]*)/?$', 'index.php?page_id=6662&blg_category=$matches[1]&blg_subcategory=$matches[2]', 'top');
    add_rewrite_rule('^videos/([^/]*)/?$', 'index.php?page_id=6662&blg_category=$matches[1]', 'top');
    /* Telegram Profiles */
    add_rewrite_rule('^telegram/miembros/([^/]*)/?$', 'index.php?page_id=8408&member_id=$matches[1]', 'top');
    /* Viernes de Escritorio */
    add_rewrite_rule('^viernesdeescritorio/publicar/?$', 'index.php?page_id=8499&publish=true', 'top');
    add_rewrite_rule('^viernesdeescritorio/instrucciones/?$', 'index.php?page_id=8499&instrucciones=true', 'top');

    add_rewrite_rule('^viernesdeescritorio/([^/]*)/([^/]*)/?$', 'index.php?page_id=8499', 'top');

}, 10, 0);

add_action('init', function () {
    add_rewrite_tag('%blg_category%', '([^&]+)');
    add_rewrite_tag('%blg_subcategory%', '([^&]+)');
    add_rewrite_tag('%blg_subsubcategory%', '([^&]+)');
    add_rewrite_tag('%member_id%', '([^&]+)');
    add_rewrite_tag('%publish%', '([^&]+)');
    add_rewrite_tag('%instrucciones%', '([^&]+)');
}, 10, 0);

/* ####################################################
 * CARGA DE WIDGETS Y SIDEBARS
 */####################################################

add_action('widgets_init', function ()
{
    // --> Global.
    register_sidebar(
        [
            'name' => 'Global',
            'id' => 'sidebar-all',
            'description' => 'Sidebar global',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Por defecto.
    register_sidebar(
        [
            'name' => 'Por Defecto',
            'id' => 'sidebar-1',
            'description' => 'Sidebar por defecto',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Por defecto.
    register_sidebar(
        [
            'name' => 'Publicidad',
            'id' => 'sidebar-visitors',
            'description' => 'Sidebar que se muestra debajo del todo cuando un usuario es visitante.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Cabecera Web
    register_sidebar(
        [
            'name' => 'Header',
            'id' => 'sidebar-header',
            'description' => 'Sidebar cabecera',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> En todas las Entradas
    register_sidebar(
        [
            'name' => 'Entradas (Todas)',
            'id' => 'sidebar-post-all',
            'description' => 'Aparece en todas las entradas.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> En artículos
    register_sidebar(
        [
            'name' => 'Entradas (Artículos)',
            'id' => 'sidebar-post-post',
            'description' => 'Aparece en los artículos.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> En vídeos
    register_sidebar(
        [
            'name' => 'Entradas (Vídeos)',
            'id' => 'sidebar-post-video',
            'description' => 'Aparece en los vídeos.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> En vídeos
    register_sidebar(
        [
            'name' => 'Entradas (Noticias)',
            'id' => 'sidebar-post-noticia',
            'description' => 'Aparece en las noticias.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> En viernes de escritorio
    register_sidebar(
        [
            'name' => 'Entradas (Viernes)',
            'id' => 'sidebar-post-viernes',
            'description' => 'Aparece en los viernes de escritorio.',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Tipología Linux
    register_sidebar(
        [
            'name' => 'Tipología Principal (Linux)',
            'id' => 'sidebar-type-linux',
            'description' => 'Aparece contenido relacionado con GNU/Linux',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Tipología Windows
    register_sidebar(
        [
            'name' => 'Tipología Principal (Windows)',
            'id' => 'sidebar-type-windows',
            'description' => 'Aparece contenido relacionado con Windows',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Tipología Reviews
    register_sidebar(
        [
            'name' => 'Tipología Secundaria (Reviews)',
            'id' => 'sidebar-type-review',
            'description' => 'Aparece contenido relacionado con Reviews (GNU/Linux y Windows)',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Tipología Tutoriales
    register_sidebar(
        [
            'name' => 'Tipología Secundaria (Tutoriales)',
            'id' => 'sidebar-type-tutorial',
            'description' => 'Aparece contenido relacionado con Tutoriales (GNU/Linux y Windows)',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección FrontPage
    register_sidebar(
        [
            'name' => 'Sección de FrontPage',
            'id' => 'sidebar-section-main',
            'description' => 'Aparece la página principal (Contenido reciente)',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Eventos
    register_sidebar(
        [
            'name' => 'Sección de Eventos',
            'id' => 'sidebar-section-events',
            'description' => 'Aparece en la sección Eventos',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Guia
    register_sidebar(
        [
            'name' => 'Sección de Guia Linux',
            'id' => 'sidebar-section-guial',
            'description' => 'Aparece en la sección Guia Linux',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Suscripciones
    register_sidebar(
        [
            'name' => 'Sección de Suscripciones',
            'id' => 'sidebar-section-subs',
            'description' => 'Aparece en la sección Suscripciones',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Presentación
    register_sidebar(
        [
            'name' => 'Sección de Presentación',
            'id' => 'sidebar-section-me',
            'description' => 'Aparece en la sección Presentación',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Setup
    register_sidebar(
        [
            'name' => 'Sección de Setup',
            'id' => 'sidebar-section-setup',
            'description' => 'Aparece en la sección Setup',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Colaboración
    register_sidebar(
        [
            'name' => 'Sección de Colaboración',
            'id' => 'sidebar-section-colaborates',
            'description' => 'Aparece en la sección Colaboración',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Viernes
    register_sidebar(
        [
            'name' => 'Sección de Viernes',
            'id' => 'sidebar-section-viernes',
            'description' => 'Aparece en la sección Viernes de Escritorio',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Telegram
    register_sidebar(
        [
            'name' => 'Sección de Telegram',
            'id' => 'sidebar-section-telegram',
            'description' => 'Aparece en la sección Telegram',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Videos
    register_sidebar(
        [
            'name' => 'Sección de Videos',
            'id' => 'sidebar-section-videos',
            'description' => 'Aparece en la sección Videos (página principal)',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Mural
    register_sidebar(
        [
            'name' => 'Sección de Mural',
            'id' => 'sidebar-section-mural',
            'description' => 'Aparece en la sección Mural',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );
    // --> Sección Contacto
    register_sidebar(
        [
            'name' => 'Sección de Contacto',
            'id' => 'sidebar-section-contact',
            'description' => 'Aparece en la sección Contacto',
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ]
    );

});

add_action('widgets_init', function ()
{
    foreach (glob(WP_CONTENT_DIR.'/themes/karlasflex/widgets/*.php') as $Widget_File)
    {
        $Widget_Name = basename($Widget_File, ".php");
        require_once $Widget_File;
        if(class_exists($Widget_Name))
        {
            register_widget($Widget_Name);
        }
    }
});

/* ####################################################
 * CUSTOM FILTERS
 */####################################################

add_filter('the_password_form',function($Input){
    global $post;
    $post   = get_post( $post );
    $label  = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
    ob_start();
    echo '<style>@media screen and (min-width: 1109px){._padleft {margin: -25px 40px -25px -25px;
background: rgba(0,0,0,0.02);
padding: 25px;
border-right: 1px solid rgba(0,0,0,0.1);}}
    @media screen and (max-width: 1110px){._padright {display: none;}}</style>';
    echo '<div class="KUI_Box">';
    echo '<h2 class="KUI_Box_Title">Contenido protegido</h2>';
    echo '<div class="KUI_ColSpan2">';
    echo '<div class="KUI_Span2 _padleft">';
    echo '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">';
    echo '<p>';
    echo __( 'This content is password protected. To view it please enter your password below:' );
    echo '</p>';
    echo '<div style="max-width: 300px;margin-top: 10px;"><h4>Clave de acceso:</h4><input placeholder="Escríbela aquí..." name="post_password" id="' . $label . '" type="password" size="20" /><input type="submit" name="Submit" value="' . esc_attr_x( 'Enter', 'post password form' ) . '" /></div>';
    echo '</form>';
    echo '</div>';
    echo '<div class="KUI_Span2 ac _padright">';
    echo '<img alt="" src="https://karlaperezyt.com/wp-content/uploads/2021/04/stopithere.png" style="height: 180px;">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    return ob_get_clean();
});