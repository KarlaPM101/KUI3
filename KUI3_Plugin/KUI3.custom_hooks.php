<?php
add_filter('the_content',function ($Post_Content_Filtered){
    if($_SERVER['REQUEST_URI']==='/wp-json/kui/page/byslug//gnulinux-historia//content'){
        $Wiki_Post = get_post(16174);

        if($Wiki_Post){
            update_post_meta($Wiki_Post->ID,'kui3_visits',get_post_meta($Wiki_Post->ID,'kui3_visits',true)+1);

            return KUI3_Do_Markdown($Wiki_Post->post_content);
        }
    }

    return $Post_Content_Filtered;
},10);