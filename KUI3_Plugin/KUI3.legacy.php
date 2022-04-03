<?php
/*
 * CONFIGURACION DEL PANEL DE CONTROL
 * Reajustes de CSS para Tablas
 *
 * --
 */
add_action('admin_head',function (){
    ?>
    <style>
        table.table-view-list.posts,table.table-view-list.page {
            table-layout: auto !important;
        }
    </style>
    <?php
});

/*
 * CONFIGURACION DEL PANEL DE CONTROL
 * Removido de opciones antiguas
 *
 * --
 */
add_action('admin_init',function (){
    if(isset($_GET['show_all'])) return;
    
    global $submenu;
    global $menu;

    foreach ( $menu as $i => $item ) {
        unset( $menu[ $i ] );
    }
});

/*
 * CONFIGURACION DEL PANEL DE CONTROL
 * Opciones antiguas reconfiguradas
 *
 * --
 */
add_action('admin_init',function (){
    if(isset($_GET['show_all'])) return;

    global $submenu;
    global $menu;
    add_menu_page(
        'Contenido',
        'Contenido',
        'manage_options',
        'karlas/content',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/colors.png',
        10
    );
    $submenu['karlas/content'][500] = ['📘 &nbsp;&nbsp;Artículos', 'manage_options' , '/wp-admin/edit.php?post_type=post'];
    $submenu['karlas/content'][501] = ['📚 &nbsp;&nbsp;Tutoriales', 'manage_options' , '/wp-admin/edit.php?post_type=videopost'];
    $submenu['karlas/content'][502] = ['🖍 &nbsp;&nbsp;Wikiposts', 'manage_options' , '/wp-admin/edit.php?post_type=wiki_post'];
    $submenu['karlas/content'][503] = ['📢 &nbsp;&nbsp;Noticias', 'manage_options' , '/wp-admin/edit.php?post_type=noticia'];
    $submenu['karlas/content'][504] = ['📅 &nbsp;&nbsp;Eventos', 'manage_options' , '/wp-admin/edit.php?post_type=evento'];
    $submenu['karlas/content'][505] = ['👥 &nbsp;&nbsp;Colaboraciones', 'manage_options' , '/wp-admin/edit.php?post_type=colaboracion'];
    $submenu['karlas/content'][506] = ['🎨 &nbsp;&nbsp;Escritorios', 'manage_options' , '/wp-admin/edit.php?post_type=viernesdeescritorio'];
    $submenu['karlas/content'][507] = ['📋 &nbsp;&nbsp;Páginas', 'manage_options' , '/wp-admin/edit.php?post_type=page'];

    add_menu_page(
        'Feedy',
        'Feedy',
        'manage_options',
        'karlas/feedy',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/rss.png',
        50
    );
    $submenu['karlas/feedy'][500] = ['📢 &nbsp;&nbsp;Feedy', 'manage_options' , '/wp-admin/edit.php?post_type=feedy'];
    $submenu['karlas/feedy'][501] = ['🐦 &nbsp;&nbsp;Tuits', 'manage_options' , '/wp-admin/edit.php?post_type=tuit'];
    $submenu['karlas/feedy'][502] = ['📸 &nbsp;&nbsp;Instagram', 'manage_options' , '/wp-admin/edit.php?post_type=instagram'];
    $submenu['karlas/feedy'][503] = ['🎩 &nbsp;&nbsp;YT Miembros', 'manage_options' , '/wp-admin/edit.php?post_type=yt_miembro'];

    add_menu_page(
        'Telegram',
        'Telegram',
        'manage_options',
        'karlas/telegram',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/social/telegram.png',
        15
    );

    foreach ($menu as $i => $item) {
        if ("karlas/telegram" === $item[2]) {
            $menu[$i][2] = '/wp-admin/edit.php?post_type=telegram_subscribers';
        }
    }


    add_menu_page(
        'Multimedia',
        'Multimedia',
        'manage_options',
        'karlas/media',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/media.png',
        13
    );

    foreach ($menu as $i => $item) {
        if ("karlas/media" === $item[2]) {
            $menu[$i][2] = '/wp-admin/upload.php';
        }
    }

    $menu["45"] = array('','read',"separator45",'','wp-menu-separator');

    $menu["95"] = array('','read',"separator95",'','wp-menu-separator');


    add_menu_page(
        'KUI3',
        'KUI3',
        'manage_options',
        'kui3.php',
        function (){},
        '/wp-content/plugins/KUI3/assets/icons/kui20.png',
        14
    );
    


    add_menu_page(
        'Plugins',
        'Plugins',
        'manage_options',
        'karlas/plug',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/plugin.png',
        101
    );
    $menu["".(101).""][2] = '/wp-admin/plugins.php';

    add_menu_page(
        'Crontab',
        'Crontab',
        'manage_options',
        'karlas/cron',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/cron.png',
        102
    );
    $menu["".(102).""][2] = '/wp-admin/tools.php?page=crontrol_admin_manage_page';

    add_menu_page(
        'Códigos',
        'Códigos',
        'manage_options',
        'karlas/codes',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/code.png',
        130
    );
    $menu["".(130).""][2] = '/wp-admin/edit.php?post_type=custom-code';



    $menu["290"] = array('','read',"separator290",'','wp-menu-separator');

    add_menu_page(
        'Pods',
        'Pods',
        'manage_options',
        'karlas/pods',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/pod.png',
        300
    );
    $menu["".(300).""][2] = '/wp-admin/admin.php?page=pods';

    add_menu_page(
        'SEO',
        'SEO',
        'manage_options',
        'karlas/seo',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/seo.png',
        310
    );
    $submenu['karlas/seo'][500] = ['YOAST SEO', 'manage_options' , '/wp-admin/admin.php?page=wpseo_dashboard'];
    $submenu['karlas/seo'][501] = ['Configuración Buscador', 'manage_options' , '/wp-admin/admin.php?page=wpseo_titles'];




    /*add_menu_page(
        'Contacto',
        'Contacto',
        'manage_options',
        'karlas/contact',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/msn.png',
        310
    );
    $submenu['karlas/contact'][500] = ['📫 &nbsp;&nbsp;Mensajes', 'manage_options' , '/wp-admin/admin.php?page=cfdb7-list.php'];
    $submenu['karlas/contact'][501] = ['📃 &nbsp;&nbsp;Formularios', 'manage_options' , '/wp-admin/admin.php?page=wpcf7'];
    */


    $menu["950"] = array('','read',"separator990",'','wp-menu-separator');

    add_menu_page(
        'Configuración',
        'Configuración',
        'manage_options',
        'karlas/config',
        function (){},
        '/wp-content/themes/karlasflex/imaging/assets/icons/admin/cog.png',
        999
    );
    $submenu['karlas/config'][500] = ['General', 'manage_options' , '/wp-admin/options-general.php?preferred-view=classic'];
    $submenu['karlas/config'][501] = ['MySQL & FTP', 'manage_options' , 'https://wordpress.com/hosting-config/karlaperezyt.com'];
    $submenu['karlas/config'][502] = ['Enlaces', 'manage_options' , 'https://karlaperezyt.com/wp-admin/options-permalink.php'];
    $submenu['karlas/config'][503] = ['Redirecciones', 'manage_options' , 'https://karlaperezyt.com/wp-admin/tools.php?page=redirection.php'];
    //$submenu['karlas/config'][504] = ['Mailpoet', 'manage_options' , 'https://karlaperezyt.com/wp-admin/admin.php?page=mailpoet-newsletters'];
    //$submenu['karlas/config'][505] = ['Suscriptores Mailpoet', 'manage_options' , 'https://karlaperezyt.com/wp-admin/admin.php?page=mailpoet-subscribers'];
    $submenu['karlas/config'][506] = ['Jetpack', 'manage_options' , 'https://wordpress.com/settings/jetpack/karlaperezyt.com'];
    //$submenu['karlas/config'][507] = ['Estadísticas', 'manage_options' , 'https://karlaperezyt.com/wp-admin/admin.php?page=apvc-dashboard-page'];


    ksort($menu);
},19);

/*
 * CONFIGURACION DEL PANEL DE CONTROL
 * Ajuste de columnas en post antiguos
 *
 * --
 */
// POSTS
add_filter('manage_edit-post_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['title'] = 'Título';
    $Columns['categories'] = 'Categorías';
    $Columns['tags'] = 'Etiquetas';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-post_columns_filter',$Columns);
});
add_action('manage_post_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo get_the_post_thumbnail( $PostId, array(50, 50) );
    }
},10,2);
// PAGE
add_filter('manage_edit-page_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['title'] = 'Título';
    $Columns['slug'] = 'Dirección';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-page_columns_filter',$Columns);
});
add_action('manage_page_posts_custom_column',function ($Column,$PostId){
    if ( 'slug' === $Column ) {
        echo sprintf('<a href="%1$s" target="_blank">%1$s</a>',get_the_permalink($PostId));
    }
},10,2);
// VIDEOPOST
add_filter('manage_edit-videopost_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['title'] = 'Título';
    $Columns['categories'] = 'Categorías';
    $Columns['tags'] = 'Etiquetas';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-videopost_columns_filter',$Columns);
});
add_action('manage_videopost_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo get_the_post_thumbnail( $PostId, array(50, 50) );
    }
},10,2);
// NOTICIA
add_filter('manage_edit-noticia_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['title'] = 'Título';
    $Columns['categories'] = 'Categorías';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-noticia_columns_filter',$Columns);
});
add_action('manage_noticia_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo get_the_post_thumbnail( $PostId, array(50, 50) );
    }
},10,2);
// EVENTO
add_filter('manage_edit-evento_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['title'] = 'Título';
    $Columns['patrocinadores'] = 'Patrocinadores';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-evento_columns_filter',$Columns);
});
add_action('manage_evento_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo get_the_post_thumbnail( $PostId, array(50, 50) );
    }
    else if ( 'patrocinadores' === $Column ) {
        $Terms = get_the_terms($PostId,'patrocinador');
        $Display = [];
        $N = 0;
        foreach ($Terms as $Term)
        {
            $Display[] = sprintf('%1$s'."<a target='_blank' href='/wp-admin/term.php?taxonomy=patrocinador&tag_ID={$Term->term_id}&post_type=evento'>{$Term->name}</a>",$N==3?'<br>':'');
            $N++;
            if($N>3) $N = 0;
        }
        echo implode(', ',$Display);
    }
},10,2);
// COLABORACION
add_filter('manage_edit-colaboracion_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['colaborador'] = 'Colaborador';
    $Columns['title'] = 'Título';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-colaboracion_columns_filter',$Columns);
});
add_action('manage_colaboracion_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo get_the_post_thumbnail( $PostId, array(50, 50) );
    }
    else if ( 'colaborador' === $Column ) {
        echo get_post_meta($PostId,'colaborador_title',true);
    }
},10,2);
// ESCRITORIO
add_filter('manage_edit-viernesdeescritorio_columns',function (){

    $Columns = [];
    $Columns['cb'] = '&nbsp;';
    $Columns['image'] = 'Imagen';
    $Columns['title'] = 'Título';
    $Columns['puser'] = 'Autor';
    $Columns['score'] = 'Votaciones';
    $Columns['date'] = 'Fecha';

    return apply_filters('manage_edit-viernesdeescritorio_columns_filter',$Columns);
});
add_action('manage_viernesdeescritorio_posts_custom_column',function ($Column,$PostId){
    if ( 'image' === $Column ) {
        echo sprintf('<img src="%1$s" alt="viernesdeescritorio" style="width: 100px;height: 60px;">',get_the_post_thumbnail_url($PostId));
    }
    else if ( 'puser' === $Column ) {
        $UserID = get_post_meta($PostId,'kui_user_id',true);
        $TelegramID = telegram_getid(get_post_meta($PostId,'telegram_user_id',true));

        if(get_post_meta($PostId,'display_name',true))
            echo sprintf('<a href="/wp-admin/post.php?post=%2$s&action=edit">%1$s</a>',trim(get_post_meta($PostId,'display_name',true)),$PostId);
        if($TelegramID && $TelegramID > 0 && get_post_status($TelegramID)!==false)
            echo sprintf('<a href="/wp-admin/post.php?post=%2$s&action=edit">%1$s</a>',trim(get_post_meta($TelegramID,'telegram_first_name',true).' '.get_post_meta($TelegramID,'telegram_last_name',true)),$TelegramID);
        elseif($UserID && $UserID > 0 && get_post_status($UserID)!==false)
            echo sprintf('<a href="/wp-admin/post.php?post=%2$s&action=edit">%1$s</a>',trim(get_post_meta($UserID,'user_display_name',true)),$UserID);
        else
            echo sprintf('<a href="/wp-admin/post.php?post=%2$s&action=edit">%1$s</a>',trim(get_post_meta($PostId,'display_name',true)),$PostId);
    }
    else if ( 'score' === $Column ) {
        echo get_post_meta($PostId,'score',true);
    }
},10,2);
