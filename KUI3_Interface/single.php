<?php
get_header();

$GLOBALS['PgTitle'] = get_the_title();
global $wp_query;

$KUI3           = new KUI_REST([]);
update_post_meta(get_the_ID(), 'kui3_visits', get_post_meta(get_the_ID(), 'kui3_visits', true) + 1);

if ($_COOKIE['kui3_noscript'] === '1'):
    locate_template("Parts/page_head.php", true);
    ?>
    <div class="KUI3_SiteBody _Desktop" style="overflow-y: auto;">
        <div class="KUI3_BodyMerge">
            <div class="KUI3_BodyGroup _IsPage" style="">
                <div class="KUI3_BodyGroup_Large _Strech">
                    <div class="KUI3_BodyGroup_Container">
                        <div class="KUI3_ContentFormal">
                            <?php
                            echo get_the_content();
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                locate_template("Parts/page_aside.php", true);
                ?>
            </div>
        </div>
    </div>
<?php
endif;

get_footer();