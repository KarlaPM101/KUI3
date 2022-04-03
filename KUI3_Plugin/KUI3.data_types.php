<?php
add_action('init', function() {
    /* USUARIOS */
    register_post_type('kui_user',[
        'label'                 => 'KUI Usuarios',
        'description'           => 'Sistema de usuarios unificado',
        'labels'                => [
            'name'                  => 'KUI Usuarios',
            'singular_name'         => 'Usuario',
            'menu_name'             => 'KUI3',
            'name_admin_bar'        => 'KUI3 Usuarios',
            'parent_item_colon'     => 'Principal',
            'all_items'             => 'Usuarios',
            'add_new_item'          => 'Añadir usuario',
            'add_new'               => 'Añadir usuario',
            'new_item'              => 'Añadir usuario',
            'edit_item'             => 'Editar usuario',
            'update_item'           => 'Actualizar usuario',
            'view_item'             => 'Ver usuario',
            'search_items'          => 'Buscar usuario',
            'not_found'             => 'Usuario no encontrado',
            'not_found_in_trash'    => 'Usuario no encontrado en la papelera',
        ],
        'public'                => false,
        'hierarchical'          => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => 'kui3.php',
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => false,
        'menu_position'         => 1,
        'menu_icon'             => '/wp-content/plugins/KUI3/assets/icons/kui20.png',
        'capability_type'       => 'post',
        'supports'              => [
            'title',
            'custom-fields',
        ],
    ]);
    register_post_meta( 'kui_user', 'user_email', [
        'type'              => 'string',
        'description'       => 'Correo electrónico',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'user_display_name', [
        'type'              => 'string',
        'description'       => 'Nombre de Usuario',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'user_display_photo', [
        'type'              => 'string',
        'description'       => 'URL Foto',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'user_passwd', [
        'type'              => 'string',
        'description'       => 'Contraseña',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'user_bio', [
        'type'              => 'string',
        'description'       => 'Biografía de usuario',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'sso_ticket', [
        'type'              => 'string',
        'description'       => 'Ticket SSO',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'activation_status', [
        'type'              => 'boolean',
        'description'       => 'Activación: Estado',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'activation_code', [
        'type'              => 'string',
        'description'       => 'Activación: Código',
        'single'            => true,
    ]);
    register_post_meta( 'kui_user', 'rel_telegram_user_id', [
        'type'              => 'integer',
        'description'       => 'Usuario de Telegram',
        'single'            => true,
    ]);

    /* COMENTARIOS */
    register_post_type('kui_comment',[
        'label'                 => 'KUI Comentarios',
        'description'           => 'Sistema de comentarios unificado',
        'labels'                => [
            'name'                  => 'KUI Comentarios',
            'singular_name'         => 'Comentario',
            'menu_name'             => 'KUI3',
            'name_admin_bar'        => 'KUI3 Comentarios',
            'parent_item_colon'     => 'Principal',
            'all_items'             => 'Comentarios',
            'add_new_item'          => 'Añadir comentario',
            'add_new'               => 'Añadir comentario',
            'new_item'              => 'Añadir comentario',
            'edit_item'             => 'Editar comentario',
            'update_item'           => 'Actualizar comentario',
            'view_item'             => 'Ver comentario',
            'search_items'          => 'Buscar comentario',
            'not_found'             => 'Comentario no encontrado',
            'not_found_in_trash'    => 'Comentario no encontrado en la papelera',
        ],
        'public'                => false,
        'hierarchical'          => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => 'kui3.php',
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => false,
        'menu_position'         => 1,
        'menu_icon'             => '/wp-content/plugins/KUI3/assets/icons/kui20.png',
        'capability_type'       => 'post',
        'supports'              => [
            'title',
            'editor',
            'custom-fields',
        ],
    ]);
    register_post_meta( 'kui_comment', 'comment_user_id', [
        'type'              => 'integer',
        'description'       => 'ID Usuario',
        'single'            => true,
    ]);
    register_post_meta( 'kui_comment', 'comment_article_id', [
        'type'              => 'integer',
        'description'       => 'ID Artículo',
        'single'            => true,
    ]);
    register_post_meta( 'kui_comment', 'comment_display_name', [
        'type'              => 'string',
        'description'       => 'Nombre a mostrar',
        'single'            => true,
    ]);
    register_post_meta( 'kui_comment', 'comment_karma', [
        'type'              => 'integer',
        'description'       => 'Karma',
        'single'            => true,
    ]);

    /* SUSCRIPCIONES */
    register_post_type('kui_suscription',[
        'label'                 => 'KUI Suscripciones',
        'description'           => 'Sistema de suscripciones unificado',
        'labels'                => [
            'name'                  => 'KUI Suscripciones',
            'singular_name'         => 'Suscripción',
            'menu_name'             => 'KUI3',
            'name_admin_bar'        => 'KUI3 Suscripciones',
            'parent_item_colon'     => 'Principal',
            'all_items'             => 'Suscripciones',
            'add_new_item'          => 'Añadir suscripción',
            'add_new'               => 'Añadir suscripción',
            'new_item'              => 'Añadir suscripción',
            'edit_item'             => 'Editar suscripción',
            'update_item'           => 'Actualizar suscripción',
            'view_item'             => 'Ver suscripción',
            'search_items'          => 'Buscar suscripción',
            'not_found'             => 'Suscripción no encontrada',
            'not_found_in_trash'    => 'Suscripción no encontrada en la papelera',
        ],
        'public'                => false,
        'hierarchical'          => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => 'kui3.php',
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => false,
        'menu_position'         => 1,
        'menu_icon'             => '/wp-content/plugins/KUI3/assets/icons/kui20.png',
        'capability_type'       => 'post',
        'supports'              => [
            'title',
            'custom-fields',
        ],
    ]);
    register_post_meta( 'kui_suscription', 'reference', [
        'type'              => 'string',
        'description'       => 'Referencia interna',
        'single'            => true,
    ]);
    register_post_meta( 'kui_suscription', 'activation_status', [
        'type'              => 'boolean',
        'description'       => 'Activación: Estado',
        'single'            => true,
    ]);
    register_post_meta( 'kui_suscription', 'activation_code', [
        'type'              => 'string',
        'description'       => 'Activación: Código',
        'single'            => true,
    ]);
    register_post_meta( 'kui_suscription', 'interest_1', [
        'type'              => 'boolean',
        'description'       => 'Interés: GNU/Linux',
        'single'            => true,
    ]);
    register_post_meta( 'kui_suscription', 'interest_2', [
        'type'              => 'boolean',
        'description'       => 'Interés: Windows',
        'single'            => true,
    ]);
    register_post_meta( 'kui_suscription', 'interest_3', [
        'type'              => 'boolean',
        'description'       => 'Interés:  YouTube',
        'single'            => true,
    ]);

    /* BOLETINES */
    register_post_type('kui_boletin',[
        'label'                 => 'KUI Boletines',
        'description'           => 'Sistema de boletines unificado',
        'labels'                => [
            'name'                  => 'KUI Boletines',
            'singular_name'         => 'Boletín',
            'menu_name'             => 'KUI3',
            'name_admin_bar'        => 'KUI3 Boletines',
            'parent_item_colon'     => 'Principal',
            'all_items'             => 'Boletines',
            'add_new_item'          => 'Añadir boletín',
            'add_new'               => 'Añadir boletín',
            'new_item'              => 'Añadir boletín',
            'edit_item'             => 'Editar boletín',
            'update_item'           => 'Actualizar boletín',
            'view_item'             => 'Ver boletín',
            'search_items'          => 'Buscar boletín',
            'not_found'             => 'Boletín no encontrada',
            'not_found_in_trash'    => 'Boletín no encontrada en la papelera',
        ],
        'public'                => false,
        'hierarchical'          => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_ui'               => true,
        'show_in_menu'          => 'kui3.php',
        'show_in_nav_menus'     => false,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => false,
        'menu_position'         => 1,
        'menu_icon'             => '/wp-content/plugins/KUI3/assets/icons/kui20.png',
        'capability_type'       => 'post',
        'supports'              => [
            'title',
            'editor',
            'custom-fields',
        ],
    ]);
    register_post_meta( 'kui_boletin', 'boletine_mail', [
        'type'              => 'string',
        'description'       => 'Email',
        'single'            => true,
    ]);
    register_post_meta( 'kui_boletin', 'boletine_sended', [
        'type'              => 'boolean',
        'description'       => 'Estado: Enviado',
        'single'            => true,
    ]);
    register_post_meta( 'kui_boletin', 'boletine_opened', [
        'type'              => 'boolean',
        'description'       => 'Estado: Abierto',
        'single'            => true,
    ]);
    register_post_meta( 'kui_boletin', 'boletine_reference', [
        'type'              => 'string',
        'description'       => 'Referencia Interna',
        'single'            => true,
    ]);
});

add_action('add_meta_boxes',function (){
    /* USUARIOS -> Principal */
    add_meta_box(
        'kui3_user_general',
        'KUI3 Otras opciones',
        function ($Post){
            wp_nonce_field(basename(__FILE__), 'kui3_user_noonce');
            ?>
            <div class="KUI3_Admin_Forms">
                <h4>Datos usuario</h4>
                <label>
                    <span>Nombre / Nickname</span>
                    <span><input name="kui_user_name" type="text" placeholder="Nombre a mostrar" value="<?php echo get_post_meta($Post->ID, 'user_display_name', true); ?>"></span>
                </label>
                <label>
                    <span>Correo electrónico:</span>
                    <span><input name="kui_user_email" type="text" placeholder="Correo electrónico" value="<?php echo get_post_meta($Post->ID, 'user_email', true); ?>"></span>
                </label>
                <hr>
                <h4>Fotografía</h4>
                <div class="Group">
                    <label style="width: 120px">
                        <span><img alt="." style="width: 100px;height: 100px;border-radius: 5px;" src="<?php echo get_post_meta($Post->ID, 'user_display_photo', true)?'/wp-content/uploads/kui_system/users_profiles/'.get_post_meta($Post->ID, 'user_display_photo', true).'.jpg':'/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg'; ?>"></span>
                    </label>
                    <label style="width: calc(100% - 120px)">
                        <span><input name="kui_user_image" placeholder="URL de imagen" type="text" value="<?php echo get_post_meta($Post->ID, 'user_display_photo', true); ?>"></span>
                    </label>
                </div>
                <hr>
                <h4>Biografía</h4>
                <label class="_Grow">
                    <span>Descripción de la biografía</span>
                    <span><textarea name="kui_user_bio" placeholder="Descripción pública del usuario"><?php echo get_post_meta($Post->ID, 'user_bio', true); ?></textarea></span>
                </label>
            </div>
            <?php
        },
        'kui_user',
        'normal',
        'high'
    );

    /* USUARIOS -> MetaOpciones */
    add_meta_box(
        'kui3_user_meta',
        'KUI3 Datos de Usuario',
        function ($Post){
            wp_nonce_field(basename(__FILE__), 'kui3_user_noonce');
            echo KUI3_Administration_Styles();
            ?>
            <div class="KUI3_Admin_Forms">
                <h4>Contraseña</h4>
                <label>
                    <span><input name="kui_user_passwd" placeholder="Sin modificar" type="password" value=""></span>
                </label>

                <hr>
                <h4>Requiere activación</h4>
                <div class="Group">
                    <label style="width: 25px">
                        <span><input name="kui_user_activation_status" placeholder="" type="checkbox"<? echo get_post_meta($Post->ID, 'activation_status', true)?'':' checked="checked"'; ?>></span>
                    </label>
                    <label style="width: calc(100% - 25px)">
                        <span><input name="kui_user_activation_code" placeholder="Código de activación" type="text" value="<?php echo get_post_meta($Post->ID, 'activation_code', true); ?>"></span>
                    </label>
                </div>
            </div>
            <?php
        },
        'kui_user',
        'side',
        'high'
    );

    /* COMENTARIOS -> Principal */
    add_meta_box(
        'kui3_user_general',
        'KUI3 Comentario',
        function ($Post){
            wp_nonce_field(basename(__FILE__), 'kui3_user_noonce');
            echo KUI3_Administration_Styles();
            ?>
            <div class="KUI3_Admin_Forms">
                <h4>Datos de comentario</h4>
                <label>
                    <span>Nombre a mostrar</span>
                    <span><input name="kui_comment_name" type="text" placeholder="Nombre a mostrar como autor en caso de anonimato" value="<?php echo get_post_meta($Post->ID, 'comment_display_name', true); ?>"></span>
                </label>
                <h4>Anidación</h4>
                <label>
                    <span>ID Artículo</span>
                    <span>
                        <select name="kui_comment_article">
                        <?php
                        $Articles = [];
                        foreach (['post','videopost','viernesdeescritorio','colaboracion','page'] as $Art_Type)
                        {
                            $Art_Query = get_posts([
                                'post_type' => $Art_Type,
                                'order' => 'DESC',
                                'orderby' => 'date',
                                'numberposts' => -1
                            ]);
                            foreach ($Art_Query as $P)
                            {
                                $Articles[get_the_date('Ymd',$P->ID).'_'.$P->ID] = $P->ID;
                            }
                            krsort($Articles);
                        }
                        foreach ($Articles as $ArticleID)
                        {
                            echo sprintf('<option%3$s value="%1$s">%2$s</option>',$ArticleID,get_the_title($ArticleID),get_post_meta($Post->ID, 'comment_article_id', true)==$ArticleID?' selected':'');
                        }
                        ?>
                        </select>
                    </span>
                </label>
                <label>
                    <span>ID Usuario</span>
                    <span>
                        <select name="kui_comment_user">
                            <option style="color: dimgray;" value="">- Sin usuario -</option>
                        <?php
                            $Users = [];
                            $Usr_Query = get_posts([
                                'post_type'     => 'kui_user',
                                'numberposts'   => -1
                            ]);
                            foreach ($Usr_Query as $P)
                            {
                                $Users[get_post_meta($P->ID,'user_email',true).'_'.$P->ID] = $P->ID;
                            }
                            ksort($Usr_Query);

                        foreach ($Users as $UsrID)
                        {
                            echo sprintf('<option%3$s value="%1$s">%2$s</option>',$UsrID,get_post_meta($UsrID,'user_email',true),get_post_meta($Post->ID, 'comment_user_id', true)==$UsrID?' selected':'');
                        }
                        ?>
                        </select>
                    </span>
                </label>
            </div>
            <?php
        },
        'kui_comment',
        'normal',
        'high'
    );

    /* SUSCRIPCIONES */
    add_meta_box(
        'kui3_user_meta',
        'KUI3 Suscripción',
        function ($Post){
            wp_nonce_field(basename(__FILE__), 'kui3_user_noonce');
            echo KUI3_Administration_Styles();
            ?>
            <div class="KUI3_Admin_Forms">
                <h4>Intereses</h4>
                <div class="Group">
                    <label>GNU/Linux<span><input class="Input_Int" name="kui_int_1" placeholder="" type="checkbox"<? echo get_post_meta($Post->ID, 'interest_1', true)?' checked="checked"':''; ?>></span></label>
                    <label>Windows<span><input class="Input_Int" name="kui_int_2" placeholder="" type="checkbox"<? echo get_post_meta($Post->ID, 'interest_2', true)?' checked="checked"':''; ?>></span></label>
                    <label>YouTube<span><input class="Input_Int" name="kui_int_3" placeholder="" type="checkbox"<? echo get_post_meta($Post->ID, 'interest_3', true)?' checked="checked"':''; ?>></span></label>
                </div>
                <hr>
                <h4>Requiere activación</h4>
                <div class="Group">
                    <label style="width: 25px">
                        <span><input name="kui_sub_activation" placeholder="" type="checkbox"<? echo get_post_meta($Post->ID, 'activation_status', true)?'':' checked="checked"'; ?>></span>
                    </label>
                    <label style="width: calc(100% - 25px)">
                        <span><input name="kui_sub_activation_code" placeholder="Código de activación" type="text" value="<?php echo get_post_meta($Post->ID, 'activation_code', true); ?>"></span>
                    </label>
                </div>

                <hr>
                <h4>Referencia interna</h4>
                <label>
                    <span><input name="kui_sub_reference" placeholder="Referencia interna" type="text" value="<?php echo get_post_meta($Post->ID, 'reference', true); ?>"></span>
                </label>

            </div>
            <?php
        },
        'kui_suscription',
        'side',
        'high'
    );
});

add_action('save_post',function ($ID,$Post){
    if (!isset($_POST['kui3_user_noonce']) || !wp_verify_nonce($_POST['kui3_user_noonce'], basename(__FILE__)))
        return $ID;
    if(!current_user_can("edit_post", $ID))
        return $ID;
    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $ID;

    /* GARDAR -> KUI User */
    if('kui_user' == $Post->post_type)
    {
        $KUI_User_Name = false;
        if(isset($_POST['kui_user_name']))
        {
            $KUI_User_Name = $_POST['kui_user_name'];
        }
        update_post_meta($ID, 'user_display_name',$KUI_User_Name);

        $KUI_User_Email = false;
        if(isset($_POST['kui_user_email']))
        {
            $KUI_User_Email = $_POST['kui_user_email'];
        }
        update_post_meta($ID, 'user_email',$KUI_User_Email);

        $KUI_User_Image = false;
        if(isset($_POST['kui_user_image']))
        {
            $KUI_User_Image = $_POST['kui_user_image'];
        }
        update_post_meta($ID, 'user_display_photo',$KUI_User_Image);

        if(isset($_POST['kui_user_passwd']) && $_POST['kui_user_passwd'])
        {
            $KUI_User_Passwd = $_POST['kui_user_passwd'];
            update_post_meta($ID, 'user_passwd',md5('KUI3_PASSWD_'.$KUI_User_Passwd));
        }

        $KUI_User_Activation = false;
        if(isset($_POST['kui_user_activation_status']))
        {
            $KUI_User_Activation = $_POST['kui_user_activation_status'];
        }
        update_post_meta($ID, 'activation_status',$KUI_User_Activation?0:1);

        $KUI_User_Activation_Code = false;
        if(isset($_POST['kui_user_activation_code']))
        {
            $KUI_User_Activation_Code = $_POST['kui_user_activation_code'];
        }
        update_post_meta($ID, 'activation_code',$KUI_User_Activation_Code);

        $KUI_User_Bio = false;
        if(isset($_POST['kui_user_bio']))
        {
            $KUI_User_Bio = $_POST['kui_user_bio'];
        }
        update_post_meta($ID, 'user_bio',$KUI_User_Bio);
    }

    /* GARDAR -> KUI User */
    if('kui_comment' == $Post->post_type)
    {
        $KUI_Display_Name = false;
        if(isset($_POST['kui_comment_name']))
        {
            $KUI_Display_Name = $_POST['kui_comment_name'];
        }
        update_post_meta($ID, 'comment_display_name',$KUI_Display_Name);

        $KUI_User_ID = false;
        if(isset($_POST['kui_comment_user']))
        {
            $KUI_User_ID = $_POST['kui_comment_user'];
        }
        update_post_meta($ID, 'comment_user_id',$KUI_User_ID);

        $KUI_Article_ID = false;
        if(isset($_POST['kui_comment_article']))
        {
            $KUI_Article_ID = $_POST['kui_comment_article'];
        }
        update_post_meta($ID, 'comment_article_id',$KUI_Article_ID);
    }

    /* GARDAR -> KUI Subscription */
    if('kui_suscription' == $Post->post_type)
    {
        $KUI_Int1 = false;
        if(isset($_POST['kui_int_1']))
        {
            $KUI_Int1 = $_POST['kui_int_1'];
        }
        update_post_meta($ID, 'interest_1',$KUI_Int1?1:0);

        $KUI_Int2 = false;
        if(isset($_POST['kui_int_2']))
        {
            $KUI_Int2 = $_POST['kui_int_2'];
        }
        update_post_meta($ID, 'interest_2',$KUI_Int2?1:0);

        $KUI_Int3 = false;
        if(isset($_POST['kui_int_3']))
        {
            $KUI_Int3 = $_POST['kui_int_3'];
        }
        update_post_meta($ID, 'interest_3',$KUI_Int3?1:0);

        $KUI_Reference = false;
        if(isset($_POST['kui_sub_reference']))
        {
            $KUI_Reference = $_POST['kui_sub_reference'];
        }
        update_post_meta($ID, 'reference',$KUI_Reference);

        $KUI_User_Activation = false;
        if(isset($_POST['kui_sub_activation']))
        {
            $KUI_User_Activation = $_POST['kui_sub_activation'];
        }
        update_post_meta($ID, 'activation_status',$KUI_User_Activation?0:1);

        $KUI_User_Activation_Code = false;
        if(isset($_POST['kui_sub_activation_code']))
        {
            $KUI_User_Activation_Code = $_POST['kui_sub_activation_code'];
        }
        update_post_meta($ID, 'activation_code',$KUI_User_Activation_Code);
    }

    return $ID;
}, 10, 3);

add_action('do_meta_boxes',function (){
    $KUI_User_Removes = [
        'authordiv',
        'categorydiv',
        'commentstatusdiv',
        'commentsdiv',
        'formatdiv',
        'pageparentdiv',
        'postcustom',
        'postexcerpt',
        'postimagediv',
        'revisionsdiv',
        'slugdiv',
        //'submitdiv',
        'tagsdiv-post_tag',
        'trackbacksdiv',
        'advanced-page-visit-counter-boxes',
        'pods-meta-mas-campos',
    ];
    foreach ($KUI_User_Removes as $n){
        foreach (['kui_user','kui_comment','kui_suscription'] as $PType){
            remove_meta_box($n, $PType, 'normal');
            remove_meta_box($n, $PType, 'side');
        }
    }
});