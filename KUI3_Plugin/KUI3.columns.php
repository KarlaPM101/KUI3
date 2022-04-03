<?php
/*
 * CONFIGURACION DE COLUMNAS PERSONALIZADAS
 * KUI3 Usuarios
 *
 * --
 */
add_filter('manage_edit-kui_user_columns',function ($Columns){
    unset($Columns['cb']);
    unset($Columns['title']);
    unset($Columns['date']);

    $Columns['actions'] = 'Acciones';
    $Columns['user_display_name'] = 'Nombre';
    $Columns['user_email'] = 'Correo electrónico';
    $Columns['activation_status'] = 'Estado activación';

    return apply_filters('manage_edit-kui_user_columns_filter',$Columns);
});
add_filter('manage_edit-kui_user_sortable_columns',function ($Columns){
    $Columns['user_display_name'] = 'name';
    $Columns['activation_status'] = 'status';
    $Columns['user_email'] = 'email';
    return $Columns;
});
add_action('manage_kui_user_posts_custom_column',function ($Column,$PostID){
    switch ($Column)
    {
        case 'actions':
            printf('<span class="cpac_use_icons"></span>');
            break;
        case 'user_display_name':
            printf('<a href="/usuarios/'.$PostID.'" target="_blank">');
            if($Data = get_post_meta($PostID,'user_display_photo',true))
            {
                printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/uploads/kui_system/users_profiles/'.$Data.'.jpg',get_post_meta($PostID,'user_display_name',true));
            }
            else
            {
                printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg',get_post_meta($PostID,'user_display_name',true));
            }
            printf(get_post_meta($PostID,'user_display_name',true));
            printf('</a>');
            break;
        case 'user_email':
            printf('<a href="mailto:'.get_post_meta($PostID,'user_email',true).'">');
            printf(get_post_meta($PostID,'user_email',true));
            printf('</a>');
            break;
        case 'activation_status':
            printf(get_post_meta($PostID,'activation_status',true)=='1'?'<span style="display: block;background: #318041;width: 100px;text-align: center;padding: 3px;border-radius: 5px;color: white;font-weight: bold;">Verificado</span>':'<span style="display: block;background: #b02c1e;width: 100px;text-align: center;padding: 3px;border-radius: 5px;color: white;font-weight: bold;">Sin verificar</span>');
            break;
    }
}, 10, 2);
add_filter('bulk_actions-edit-kui_user', function () {
    return apply_filters('bulk_actions-edit-kui_user_filter',[]);
});
add_action('pre_get_posts',function ($Query ) {
    if(!is_admin()) return;
    if($Query->post_type=='kui_user')
    {
        $SelectOrder = $Query->get( 'orderby');

        if('name' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','user_display_name');
        }
        if('status' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','activation_status');
        }
        if('email' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','user_email');
        }
    }
});


/*
 * CONFIGURACION DE COLUMNAS PERSONALIZADAS
 * KUI3 Comentarios
 *
 * --
 */
add_filter('manage_edit-kui_comment_columns',function ($Columns){
    unset($Columns['cb']);
    unset($Columns['title']);
    unset($Columns['date']);

    $Columns['actions'] = 'Acciones';
    $Columns['date'] = 'Fecha';
    $Columns['name'] = 'Nombre';
    $Columns['email'] = 'Correo electrónico';
    $Columns['article'] = 'Artículo';
    $Columns['comment_text'] = 'Comentario';

    return apply_filters('manage_edit-kui_comment_columns_filter',$Columns);
});
add_filter('manage_edit-kui_comment_sortable_columns',function ($Columns){
    $Columns['name'] = 'name';
    return $Columns;
});
add_action('manage_kui_comment_posts_custom_column',function ($Column,$PostID){
    switch ($Column)
    {
        case 'actions':
            printf('<span class="cpac_use_icons"></span>');
            break;
        case 'name':
            $UserID = (int)get_post_meta($PostID,'comment_user_id',true);
            if($UserID && $Data = get_post_meta($UserID,'user_display_photo',true))
            {
                printf('<a href="?post='.$UserID.'&action=edit" target="_blank">');
                printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/uploads/kui_system/users_profiles/'.$Data.'.jpg',get_post_meta($UserID,'user_display_name',true));
                printf(get_post_meta($UserID,'user_display_name',true));
                printf('</a>');
            }
            else if($UserID)
            {
                printf('<a href="?post='.$UserID.'&action=edit" target="_blank">');
                printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg',get_post_meta($UserID,'user_display_name',true));
                printf(get_post_meta($UserID,'user_display_name',true));
                printf('</a>');
            }
            else
            {
                printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg',get_post_meta($PostID,'comment_display_name',true));
                printf(get_post_meta($PostID,'comment_display_name',true));
            }
            break;
        case 'email':
            $UserID = (int)get_post_meta($PostID,'comment_user_id',true);
            if($UserID)
            {
                printf('<a href="mailto:'.get_post_meta($UserID,'user_email',true).'">');
                printf(get_post_meta($UserID,'user_email',true));
                printf('</a>');
            }
            else
            {
                printf('<span style="font-style: italic;">Desconocido</span>');
            }
            break;
        case 'article':
            $ArticleID = (int)get_post_meta($PostID,'comment_article_id',true);
            printf('<a href="?post='.$ArticleID.'&action=edit" target="_blank">');
            printf('</a>');
            printf(get_the_title($ArticleID));
            break;
        case 'comment_text':
            printf(get_the_content(null,false,$PostID));
            break;
    }
}, 10, 2);
add_filter('bulk_actions-edit-kui_comment', function () {
    return apply_filters('bulk_actions-edit-kui_user_filter',[]);
});
add_action('pre_get_posts',function ($Query ) {
    if(!is_admin()) return;
    if($Query->post_type=='kui_comment')
    {
        $SelectOrder = $Query->get( 'orderby');

        if('name' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','comment_display_name');
        }
    }
});


/*
 * CONFIGURACION DE COLUMNAS PERSONALIZADAS
 * KUI3 Suscripciones
 *
 * --
 */
add_filter('manage_edit-kui_suscription_columns',function ($Columns){
    unset($Columns['cb']);
    unset($Columns['title']);
    unset($Columns['date']);

    $Columns['actions'] = 'Acciones';
    $Columns['email'] = 'Correo electrónico';
    $Columns['user'] = 'Usuario asociado';
    $Columns['intereses'] = 'Intereses';
    $Columns['status'] = 'Estado activación';
    $Columns['rate'] = 'Fidelidad';

    return apply_filters('manage_edit-kui_suscription_columns_filter',$Columns);
});
add_filter('manage_edit-kui_suscription_sortable_columns',function ($Columns){
    $Columns['email'] = 'email';
    $Columns['status'] = 'status';
    $Columns['rate'] = 'rate';
    return $Columns;
});
add_action('manage_kui_suscription_posts_custom_column',function ($Column,$PostID){
    switch ($Column)
    {
        case 'actions':
            printf('<span class="cpac_use_icons"></span>');
            break;
        case 'user':
            $UsersQuery = get_posts([
                'post_type'        => 'kui_user',
                'numberposts'      => 1,
                'meta_key'         => 'user_email',
                'meta_value'       => get_the_title($PostID),
            ]);
            if(isset($UsersQuery[0]))
            {
                $UserID = $UsersQuery[0]->ID;
                if($Data = get_post_meta($UserID,'user_display_photo',true))
                {
                    printf('<a href="?post='.$UserID.'&action=edit" target="_blank">');
                    printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/uploads/kui_system/users_profiles/'.$Data.'.jpg',get_post_meta($UserID,'user_display_name',true));
                    printf(get_post_meta($UserID,'user_display_name',true));
                    printf('</a>');
                }
                else
                {
                    printf('<a href="?post='.$UserID.'&action=edit" target="_blank">');
                    printf('<img alt="%2$s" src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;margin-right: 20px;">','/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg',get_post_meta($UserID,'user_display_name',true));
                    printf(get_post_meta($UserID,'user_display_name',true));
                    printf('</a>');
                }
            }
            else
            {
                printf('<span style="font-style: italic;">Sin usuario</span>');
            }
            break;
        case 'email':
            printf('<a href="mailto:'.get_the_title($PostID).'">');
            printf(get_the_title($PostID));
            printf('</a>');
            break;
        case 'status':
            printf(get_post_meta($PostID,'activation_status',true)=='1'?'<span style="display: block;background: #318041;width: 100px;text-align: center;padding: 3px;border-radius: 5px;color: white;font-weight: bold;">Verificado</span>':'<span style="display: block;background: #b02c1e;width: 100px;text-align: center;padding: 3px;border-radius: 5px;color: white;font-weight: bold;">Sin verificar</span>');
            break;
        case 'rate':
            $Rate = (int)get_post_meta($PostID,'rate',true);

            if($Rate>90){
                $Color = '#318041';
            }
            elseif($Rate>60){
                $Color = '#5a8031';
            }
            elseif($Rate>49){
                $Color = '#7b8031';
            }
            elseif($Rate>30){
                $Color = '#7b8031';
            }
            else{
                $Color = '#803f31';
            }

            printf('<span style="display: block;background: '.$Color.';width: 50px;text-align: center;padding: 3px;border-radius: 5px;color: white;font-weight: bold;">' .number_format($Rate).' %%</span>');
            break;
        case 'intereses':
            printf('<div style="display: flex;flex-wrap: nowrap;">');
            if(get_post_meta($PostID,'interest_1',true))
                printf('<img style="margin: 0 5px;" alt="GNU/Linux" src="https://karlaperezyt.com/wp-content/uploads/2019/01/icons8-linux-20.png">');
            if(get_post_meta($PostID,'interest_2',true))
                printf('<img style="margin: 0 5px;" alt="Windows" src="https://karlaperezyt.com/wp-content/uploads/2019/01/icons8-windows-xp-20.png">');
            if(get_post_meta($PostID,'interest_3',true))
                printf('<img style="margin: 0 5px;" alt="YouTube" src="https://karlaperezyt.com/wp-content/uploads/2019/07/icons8-youtube-play-20.png">');
            printf('</div>');
            break;
    }
}, 10, 2);
add_filter('bulk_actions-edit-kui_suscription', function () {
    return apply_filters('bulk_actions-edit-kui_user_filter',[]);
});
add_action('pre_get_posts',function ($Query ) {
    if(!is_admin()) return;
    if($Query->post_type=='kui_suscription')
    {
        $SelectOrder = $Query->get( 'orderby');

        if('email' == $SelectOrder ) {
            $Query->set('orderby','title');
        }
        if('status' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','activation_status');
        }
        if('rate' == $SelectOrder ) {
            $Query->set('orderby','meta_value_num');
            $Query->set('meta_key','rate');
        }
    }
});