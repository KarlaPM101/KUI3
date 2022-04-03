<?php
if ( is_active_sidebar( 'sidebar-1' ) ) {
    ?>
    <aside id="KUL_Sidebar">
        <?php
        if(is_singular(['page','post','videopost','noticia']) && !wp_is_mobile()):
            if(is_singular(['post','videopost','noticia']) && !get_post_meta(get_the_ID(),'youtubefull',true) && get_post_meta(get_the_ID(),'youtube',true))
            {
                ?>
                <section id="execphp-15" class="widget widget_execphp">
                    <h3 class="widget-title">Vídeo sobre el Artículo</h3>
                    <div class="execphpwidget">
                        <div style="margin:-24px -24px -29px; ">
                            <?php
                            echo '<iframe width="100%" height="195" src="https://www.youtube.com/embed/'.get_post_meta(get_the_ID(),'youtube',true).'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                            ?>
                        </div>
                    </div>
                </section>
                <?php
            }
            if(is_singular(['post','videopost']) || (is_page(['mi-setup','comandos','normas','7163','8929','718'])) || (post_password_required(9084)===false && is_page(9084)))
            {
                $TheContents = '';
                ob_start();
                the_widget('ezTOC_Widget');
                $TheContents = ob_get_clean();
                if($TheContents) {
                    ?>
                    <section id="ezw_tco-2" class="widget ez-toc">
                        <h3 class="widget-title">Índice de Contenidos</h3>
                        <?php
                        echo $TheContents
                        ?>
                    </section>
                    <?php
                }

            }
            if(is_singular(['post','videopost','noticia']) && $Sources = get_post_meta(get_the_ID(),'fuentes',true))
            {
                ?>
                <section id="execphp-14" class="widget widget_execphp">
                    <h3 class="widget-title">Recursos Externos</h3>
                    <div class="execphpwidget">
                        <div class="KUI_LinkListUL">
                            <?php echo $Sources; ?>
                        </div>
                    </div>
                </section>
                <?php
            }
        endif;
        ?>
        <?php
        // --> SIDEBAR :: Sections
        if(is_front_page() || is_home()) dynamic_sidebar('sidebar-section-main');
        elseif(is_page(['eventos']) || is_singular('evento')) dynamic_sidebar('sidebar-section-events');
        elseif(is_page(['guia-linux'])) dynamic_sidebar('sidebar-section-guial');
        elseif(is_page(['suscripcion','cancelar-suscripcion','faq','suscripcion-activada'])) dynamic_sidebar('sidebar-section-subs');
        elseif(is_page(['sobre-mi'])) dynamic_sidebar('sidebar-section-me');
        elseif(is_page(['mi-setup'])) dynamic_sidebar('sidebar-section-setup');
        elseif(is_page(['telegram','comandos','normas','miembros','7163','8929'])) dynamic_sidebar('sidebar-section-telegram');
        elseif(is_page(['videos'])) dynamic_sidebar('sidebar-section-videos');
        elseif(is_page(['muro'])) dynamic_sidebar('sidebar-section-mural');
        elseif(is_page(['contacto'])) dynamic_sidebar('sidebar-section-contact');
        elseif(is_page(['colaboraciones'])) dynamic_sidebar('sidebar-section-colaborates');
        elseif(is_page(['viernesdeescritorio'])) dynamic_sidebar('sidebar-section-viernes');
        elseif(is_page()) dynamic_sidebar('sidebar-1');
        // --> SIDEBAR :: Entradas
        if(is_singular(['post','videopost','noticia'])) dynamic_sidebar('sidebar-post-all');
        if(is_singular('post')) dynamic_sidebar('sidebar-post-post');
        elseif(is_singular('videopost')) dynamic_sidebar('sidebar-post-video');
        elseif(is_singular('noticia')) dynamic_sidebar('sidebar-post-noticia');
        elseif(is_singular('viernesdeescritorio')) dynamic_sidebar('sidebar-post-viernes');
        // --> SIDEBAR :: Tipologia Principal
        if(in_category(['blog-linux'])) dynamic_sidebar('sidebar-type-linux');
        elseif(in_category(['blog-windows'])) dynamic_sidebar('sidebar-type-windows');
        elseif(in_category(['review-linux'])) dynamic_sidebar('sidebar-type-linux');
        elseif(in_category(['review-windows'])) dynamic_sidebar('sidebar-type-windows');
        elseif(in_category(['tutorial-linux'])) dynamic_sidebar('sidebar-type-linux');
        elseif(in_category(['tutorial-windows'])) dynamic_sidebar('sidebar-type-windows');
        // --> SIDEBAR :: Tipologia Secundaria
        if(in_category(['review-linux','review-windows'])) dynamic_sidebar('sidebar-type-review');
        elseif(in_category(['tutorial-linux','tutorial-windows'])) dynamic_sidebar('sidebar-type-tutorial');
        // --> SIDEBAR :: Sections
        dynamic_sidebar('sidebar-all');
        ?>
        <?php
        if(!current_user_can('editor') && !current_user_can('administrator'))
        {
            dynamic_sidebar('sidebar-visitors');
        }
        ?>
    </aside>
    <?php
}
