<?php
function Friday_UpdatePodium()
{
    $SQL = "SELECT dd.score,
IFNULL(dd.tlg_d,dd.kusr_d) AS display,
CASE dd.tlg_i 
	WHEN NULL THEN 'k'
    ELSE 't' END
    
    AS TY,
    
    CASE dd.tlg_i 
	WHEN NULL THEN dd.kusr_i
    ELSE tlg_i END
    
    AS ID
    

FROM (SELECT scd.score AS score,
IFNULL(CONCAT((SELECT meta_value FROM wp_postmeta WHERE meta_key = 'telegram_first_name' AND post_id = CAST((SELECT ID FROM wp_posts WHERE post_title = scd.tlg) AS UNSIGNED)),' ',(SELECT meta_value FROM wp_postmeta WHERE meta_key = 'telegram_last_name' AND post_id = CAST((SELECT ID FROM wp_posts WHERE post_title = scd.tlg) AS UNSIGNED))),(SELECT meta_value FROM wp_postmeta WHERE meta_key = 'telegram_first_name' AND post_id = CAST((SELECT ID FROM wp_posts WHERE post_title = scd.tlg) AS UNSIGNED))) AS tlg_d,
(SELECT meta_value FROM wp_postmeta WHERE meta_key = 'user_display_name' AND post_id = scd.kusr) AS kusr_d,
scd.kusr AS kusr_i,
scd.tlg AS tlg_i
           
FROM (
    SELECT SUM(a.meta_value) AS score,
(SELECT b.meta_value FROM wp_postmeta AS b WHERE b.post_id = a.post_id AND b.meta_key = 'telegram_user_id') AS tlg,
(SELECT c.meta_value FROM wp_postmeta AS c WHERE c.post_id = a.post_id AND c.meta_key = 'kui_user_id') AS kusr

FROM wp_postmeta AS a
WHERE a.meta_key = 'score'

GROUP BY tlg, kusr
   
ORDER BY score DESC
    
LIMIT 50
    ) AS scd) AS dd;";

    global $wpdb;
    $Query = $wpdb->get_results($SQL);

    foreach ($Query as $Query_Row)
    {
        if($Query_Row->ID<=0){
            continue;
        }

        switch ($Query_Row->TY){
            case 't':
                update_post_meta(telegram_getid($Query_Row->ID),'desktop_points',$Query_Row->score);
                break;
            case 'k':
                update_post_meta($Query_Row->ID,'desktop_points',$Query_Row->score);
                break;
        }
    }
}