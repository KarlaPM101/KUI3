<?php
/**
 * Template Name: Portada
 */
get_header();

$KUI3 = new KUI_REST([]);

if ($_COOKIE['kui3_noscript'] === '1'):
    locate_template("Parts/page_head.php", true);
    ?>
    <div class="KUI3_SiteBody _Desktop" style="overflow-y: auto;">
        <div class="KUI3_BodyMerge _Gray">
            <div class="KUI3_BodyGroup">
                <div class="KUI3_BodyGroup_Large">
                    <h2 class="GroupTitle"><span class="_Text">Escritorios Destacados</span><span
                                class="_TextDesc">GNU/Linux y Windows with 💗</span>
                    </h2>
                    <div class="KUI3_BodyGroup_Container _AsyncTrilerDesktopGroup">
                        <?php
                        $Trailer_Desktops = $KUI3->Internal_Call("article/list?Type=viernesdeescritorio&Featured=true&Limit=4");
                        if ($Trailer_Desktops) {
                            ?>
                            <ul class="KUI3_Desktops_Trailer">
                                <?php
                                foreach ($Trailer_Desktops as $Trailer_Desktops_I) {
                                    ?>
                                    <li style="background-image: url(<?php echo $Trailer_Desktops_I['Image']; ?>);width:300px;height:168.75px;">
                                        <span class="_Score"><?php echo $Trailer_Desktops_I['Score']; ?></span>
                                        <span class="_Profile">
                                                    <img alt="<?php echo $Trailer_Desktops_I['Display_Name']; ?>"
                                                         src="<?php echo $Trailer_Desktops_I['Display_Photo']; ?>"><?php echo $Trailer_Desktops_I['Display_Name']; ?></span>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="KUI3_BodyGroup_Small"><h2 class="GroupTitle"><span class="_Text">Noticias Linuxeras</span>
                        <span class="_TextDesc">Recién sacadas del horno 😋</span>
                    </h2>
                    <div class="KUI3_BodyGroup_Container _AsyncTrilerNewsGroup">
                        <?php
                        $Trailer_News = $KUI3->Internal_Call("article/list?Type=noticia&Limit=4");
                        if ($Trailer_News) {
                            ?>
                            <ul class="KUI3_FrontNewsList">
                                <?php
                                foreach ($Trailer_News

                                as $Trailer_News_I){
                                ?>
                                <li>
                                    <a href="<?php echo $Trailer_News_I['Url']; ?>"><?php echo $Trailer_News_I['Title']; ?></a>
                                </li>
                                <li>
                                    <?php
                                    }
                                    ?>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="KUI3_BodyMerge _FeedyFrontPage">
            <div class="KUI3_BodyGroup">
                <div class="KUI3_BodyGroup_All">
                    <h2 class="GroupTitle"><span class="_Text">Contenido Reciente</span><span class="_TextDesc">Artículos y Tutoriales</span>
                    </h2>

                    <?php
                    $Content = $KUI3->Internal_Call("article/list?Type=post,videopost&Limit=60");
                    if ($Content) {
                        ?>
                        <ul class="KUI3_Listings_Feedy _NoColumn">
                            <?php
                            foreach ($Content as $Content_I) {
                                ?>
                                <a href="<?php echo $Content_I['Url']; ?>">
                                    <li class="_Article KUI3_Element_Box"><img
                                                alt="<?php echo $Content_I['Title']; ?>>"
                                                src="<?php echo $Content_I['Image']; ?>"
                                                class="mCS_img_loaded">
                                        <h2><?php echo $Content_I['Title']; ?></h2>
                                        <div class="_Date"><?php echo $Content_I['Date']['Date']; ?></div>
                                        <div class="_Description"><?php echo $Content_I['Excerpt']; ?></div>
                                    </li>
                                </a>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
endif;

get_footer();