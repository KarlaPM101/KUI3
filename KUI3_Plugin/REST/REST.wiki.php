<?php
function KUIREST_ENDPOINT_Wiki_Categories($REST_DATA)
{
    if (isset($REST_DATA['all']) && $REST_DATA['all']) {
        $Categories_Array     = [];
        $Categories_Main_List = get_terms([
            'taxonomy'   => 'wiki_cat',
            'hide_empty' => false,
        ]);
        if ($Categories_Main_List) {
            foreach ($Categories_Main_List as $Categories_Main_List_I) {
                if ($Categories_Main_List_I->term_id == 683651434) {
                    continue;
                }

                $Categories_Array[] = [
                    'ID'    => $Categories_Main_List_I->term_id,
                    'Title' => $Categories_Main_List_I->name,
                ];
            }
        }

        return $Categories_Array;
    } elseif (isset($REST_DATA['term']) && $REST_DATA['term']) {
        $Categories_Posts_Array = [];
        $Categories_Term_ID     = (int) sanitize_text_field($REST_DATA['term']);

        $Categories_Posts_Query = new WP_Query(isset($REST_DATA['norder']) ? [
            'post_type'      => 'wiki_post',
            'posts_per_page' => -1,
            'orderby'        => 'name',
            'order'          => 'ASC',
            'tax_query'      => [
                [
                    'taxonomy'         => 'wiki_cat',
                    'field'            => 'term_id',
                    'terms'            => $Categories_Term_ID,
                    'include_children' => false,
                ],
            ],
        ] : [
            'post_type'      => 'wiki_post',
            'posts_per_page' => -1,
            'orderby'        => [
                'meta_value_num' => 'DESC',
                'name'           => 'ASC',
            ],
            'meta_key'       => 'importance',
            'tax_query'      => [
                [
                    'taxonomy'         => 'wiki_cat',
                    'field'            => 'term_id',
                    'terms'            => $Categories_Term_ID,
                    'include_children' => false,
                ],
            ],
        ]);

        if ($Categories_Posts_Query->have_posts()) {
            while ($Categories_Posts_Query->have_posts()) {
                $Categories_Posts_Query->the_post();

                $Current_Post_Image            = get_post_thumbnail_id(get_the_ID());
                list($Src_Url, $Src_W, $Src_H) = wp_get_attachment_image_src($Current_Post_Image, 'full');

                $Title = get_the_title();

                if ($Categories_Term_ID === 683651452) {
                    if ($SEOtitle = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true)) {
                        $Rendered = wpseo_replace_vars($SEOtitle, get_post(get_the_ID()));
                        $Title    = substr($Rendered, 0, strpos($Rendered, ' - '));
                    }
                }

                $Categories_Posts_Array[] = [
                    'Title'       => $Title,
                    'Url'         => get_the_permalink(),
                    'Description' => nl2br(get_the_excerpt()),
                    'Image'       => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'Image_W'     => (float) $Src_W,
                    'Image_H'     => (float) $Src_H,
                ];
            }
        }

        return $Categories_Posts_Array;
    } else {
        $Categories_Array     = [];
        $Categories_Main_List = get_terms([
            'taxonomy' => 'wiki_cat',
        ]);
        if ($Categories_Main_List) {
            foreach ($Categories_Main_List as $Categories_Main_List_I) {
                if ($Categories_Main_List_I->name == 'Categorias') {
                    $Categories_Child_List = get_terms([
                        'taxonomy'   => 'wiki_cat',
                        'child_of'   => $Categories_Main_List_I->term_id,
                        'orderby'    => [
                            'importance_clause' => 'DESC',
                        ],
                        'meta_query' => [
                            'importance_clause' => [
                                'key'     => 'importance',
                                'value'   => '-1',
                                'compare' => '>=',
                                'type'    => 'NUMERIC',
                            ],
                        ],
                    ]);
                    if ($Categories_Child_List) {
                        foreach ($Categories_Child_List as $Categories_Child_List_I) {
                            $Categories_Posts_Query = new WP_Query([
                                'post_type'      => 'wiki_post',
                                'posts_per_page' => -1,
                                'orderby'        => 'name',
                                'order'          => 'ASC',
                                'tax_query'      => [
                                    [
                                        'taxonomy'         => 'wiki_cat',
                                        'field'            => 'term_id',
                                        'terms'            => $Categories_Child_List_I->term_id,
                                        'include_children' => false,
                                    ],
                                ],
                            ]);
                            $Categories_Posts_Array = [];
                            if ($Categories_Posts_Query->have_posts()) {
                                while ($Categories_Posts_Query->have_posts()) {
                                    $Categories_Posts_Query->the_post();

                                    $Categories_Posts_Array[] = [
                                        'Title' => get_the_title(),
                                        'Url'   => get_the_permalink(),
                                    ];
                                }
                            }

                            $Categories_Array[] = [
                                'Title'       => $Categories_Child_List_I->name,
                                'Description' => $Categories_Child_List_I->description,
                                'Image'       => get_term_meta($Categories_Child_List_I->term_id, 'image', true),
                                'Posts'       => $Categories_Posts_Array,
                            ];
                        }
                    }
                }
            }
        }

        return $Categories_Array;
    }
}
function KUIREST_ENDPOINT_Wiki_Editor($REST_DATA)
{
    $KUI = new KUI_REST([
        'sso' => $REST_DATA['sso'],
    ]);
    $KUI->UserSession_Authenticate();
    $KUI->UserSession_Requiered();

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        if (!isset($REST_DATA['wiki_id'])) {
            return new WP_Error('wiki_no_id', 'Se requiere un ID.');
        }

        $Wiki_ID = (int) sanitize_text_field($REST_DATA['wiki_id']);

        $Wiki_Data = get_post($Wiki_ID);

        if (!$Wiki_Data) {
            return new WP_Error('wiki_not_found', 'El artículo no existe.');
        }

        if (get_post_type($Wiki_ID) !== 'wiki_post' && get_post_type($Wiki_ID) !== 'revision') {
            return new WP_Error('wiki_not_found', 'Artículo inválido.');
        }

        $sA = ['&gt;', '&amp;bsol;', '&amp;'];
        $sB = ['>', '\\', '&'];

        if (get_post_type($Wiki_ID) === 'revision') {
            $Parent = get_post($Wiki_Data->post_parent);

            if (!$Parent) {
                return new WP_Error('wiki_huerf', 'Artículo húerfano.');
            }

            $TermList = wp_get_post_terms($Parent->ID, 'wiki_cat', ['fields' => 'ids']);

            $MetaData = get_post_meta($Parent->ID, 'revisions_metadata', true);
            $GetMetas = [];
            if ($MetaData) {
                $MetaData = json_decode($MetaData, true);

                if (isset($MetaData[$Wiki_ID])) {
                    $GetMetas = $MetaData[$Wiki_ID];
                }
            }

            $Revision_Thumbnail = get_the_post_thumbnail_url($Parent->ID, 'large');
            if (isset($GetMetas['Thumbnail'])) {
                $Find = attachment_url_to_postid(str_replace(WP_CONTENT_DIR . "/uploads/", '', $GetMetas['Thumbnail']));

                if ($Find !== 0) {
                    list($Src_Url, $Src_W, $Src_H) = wp_get_attachment_image_src($Find, 'full');
                    $Revision_Thumbnail            = $Src_Url;
                }
            }

            $Revision_Category = isset($GetMetas['Cat']) ? $GetMetas['Cat'] : (isset($TermList[0]) ? $TermList[0] : 0);
            $Revision_SEOtitle = isset($GetMetas['Seo']) ? $GetMetas['Seo'] : get_post_meta($Wiki_Data->ID, '_yoast_wpseo_title', true);

            return [
                'ID'       => $Parent->ID,
                'Title'    => $Wiki_Data->post_title,
                'SEOTitle' => substr($Revision_SEOtitle, 0, strpos($Revision_SEOtitle, ' - ')),
                'Content'  => str_replace($sA, $sB, $Wiki_Data->post_content),
                'Excerpt'  => $Wiki_Data->post_excerpt,
                'Image'    => $Revision_Thumbnail,
                'Category' => $Revision_Category,
                'Revision' => [
                    'ID'       => $Wiki_ID,
                    'Metadata' => $GetMetas,
                ],
            ];
        } else {
            $TermList          = wp_get_post_terms($Wiki_Data->ID, 'wiki_cat', ['fields' => 'ids']);
            $Revision_SEOtitle = get_post_meta($Wiki_Data->ID, '_yoast_wpseo_title', true);

            return [
                'ID'       => $Wiki_Data->ID,
                'Title'    => $Wiki_Data->post_title,
                'SEOTitle' => substr($Revision_SEOtitle, 0, strpos($Revision_SEOtitle, ' - ')),
                'Content'  => str_replace($sA, $sB, $Wiki_Data->post_content),
                'Excerpt'  => $Wiki_Data->post_excerpt,
                'Image'    => get_the_post_thumbnail_url($Wiki_Data->ID, 'large'),
                'Category' => isset($TermList[0]) ? $TermList[0] : 0,
                'Revision' => false,
            ];
        }

    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $KUI->Floody_Dispatch('Wiki_Edition', 6, 300, 3600);

        if (!isset($REST_DATA['title'])) {
            return new WP_Error('update_failed', 'Se requiere un campo: title');
        }
        if (!isset($REST_DATA['content'])) {
            return new WP_Error('update_failed', 'Se requiere un campo: content');
        }

        $Post_Title    = sanitize_text_field($REST_DATA['title']);
        $Post_SEOTitle = sanitize_text_field($REST_DATA['seotitle']);
        $Post_Content  = $REST_DATA['content'];
        $Post_Excerpt  = sanitize_text_field($REST_DATA['excerpt']);
        $Post_Category = (int) sanitize_text_field($REST_DATA['category']);

        if (isset($REST_DATA['comment'])) {
            $Post_Comment = sanitize_text_field($REST_DATA['comment']);
        } else {
            $Post_Comment = "";
        }

        if (!$Post_Title) {
            return new WP_Error('update_failed', 'Campo vacío: title.');
        }
        if (strlen($Post_Title) < 3 || strlen($Post_Title) > 30) {
            return new WP_Error('update_failed', 'Campo de longitud no aceptada: title (3 <> 30 carácteres).');
        }

        if (!$Post_SEOTitle) {
            return new WP_Error('update_failed', 'Campo vacío: seotitle.');
        }
        if (strlen($Post_SEOTitle) < 3 || strlen($Post_SEOTitle) > 125) {
            return new WP_Error('update_failed', 'Campo de longitud no aceptada: seotitle (3 <> 125 carácteres).');
        }

        if (!$Post_Content) {
            return new WP_Error('update_failed', 'Campo vacío: content.');
        }

        if (!$Post_Excerpt) {
            return new WP_Error('update_failed', 'Campo vacío: excerpt.');
        }
        if (strlen($Post_Excerpt) < 3 || strlen($Post_Excerpt) > 256) {
            return new WP_Error('update_failed', 'Campo de longitud no aceptada: excerpt (3 <> 256 carácteres).');
        }

        $Category_Data = get_term($Post_Category);
        if (!$Category_Data || !$Post_Category) {
            return new WP_Error('update_failed', 'Categoría no existe.');
        }

        $Is_New = false;

        if (isset($REST_DATA['wiki_id']) && $REST_DATA['wiki_id'] != 0) {
            $Wiki_ID = (int) sanitize_text_field($REST_DATA['wiki_id']);
        } else {
            $KUI->Floody_Dispatch('Wiki_Creation', 3, 1800, 3600 * 48);

            $Wiki_ID = wp_insert_post([
                'post_type'    => 'wiki_post',
                'post_title'   => $Post_Title,
                'post_content' => $Post_Content,
                'post_excerpt' => $Post_Excerpt,
            ]);
            $Is_New = true;
        }

        if (!$Is_New) {
            if (!$Post_Comment) {
                return new WP_Error('update_failed', 'Campo vacío: comment.');
            }
            if (strlen($Post_Comment) < 3 || strlen($Post_Comment) > 256) {
                return new WP_Error('update_failed', 'Campo de longitud no aceptada: comment (3 <> 256 carácteres).');
            }
        }

        $Wiki_Data = get_post($Wiki_ID);

        if (!$Wiki_Data) {
            return new WP_Error('wiki_not_found', 'El artículo no existe.');
        }

        if (get_post_type($Wiki_ID) !== 'wiki_post') {
            return new WP_Error('wiki_not_found', 'Artículo inválido.', ['ID' => $Wiki_ID]);
        }

        $Generated_Code = implode('-', [
            substr(md5(uniqid()), 0, 8),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 12),
        ]);

        if (isset($_FILES['image']['tmp_name'])) {
            $File_GUID                  = $Generated_Code;
            $File_Directory_Destination = WP_CONTENT_DIR . "/uploads/kui_system/wikipost/";
            $File_Directory_Temporal    = $_FILES['image']['tmp_name'];
            $File_MimeType              = mime_content_type($File_Directory_Temporal);
            $File_Size                  = filesize($File_Directory_Temporal);
            $Accepted_MimeTypes         = [
                'image/png',
                'image/jpg',
                'image/jpeg',
                'image/bmp',
            ];

            if (!file_exists($File_Directory_Temporal)) {
                return new WP_Error('invalid_file', 'El archivo no se ha subido');
            }
            if (!in_array($File_MimeType, $Accepted_MimeTypes)) {
                return new WP_Error('invalid_mimetype', 'MimeType no aceptado', ['mime' => $File_MimeType]);
            }
            if ($File_Size > 10 * 1000 * 1000) {
                return new WP_Error('invalid_size', 'Tamaño de archivo excedido', ['size' => $File_Size]);
            }

            if ($File_MimeType === 'image/png' || $File_MimeType === 'image/bmp' || $File_MimeType === 'image/jpg' || $File_MimeType === 'image/jpeg') {
                if ($File_MimeType === 'image/png') {
                    $GD_Resource = imagecreatefrompng($File_Directory_Temporal);
                }
                if ($File_MimeType === 'image/bmp') {
                    $GD_Resource = imagecreatefrombmp($File_Directory_Temporal);
                }
                if ($File_MimeType === 'image/jpg' || $File_MimeType === 'image/jpeg') {
                    $GD_Resource = imagecreatefromjpeg($File_Directory_Temporal);
                }

                if ($GD_Resource) {

                    $thumb_width     = 1024;
                    $thumb_height    = 576;
                    $width           = imagesx($GD_Resource);
                    $height          = imagesy($GD_Resource);
                    $original_aspect = $width / $height;
                    $thumb_aspect    = $thumb_width / $thumb_height;
                    if ($original_aspect >= $thumb_aspect) {
                        $new_height = $thumb_height;
                        $new_width  = $width / ($height / $thumb_height);
                    } else {
                        $new_width  = $thumb_width;
                        $new_height = $height / ($width / $thumb_width);
                    }
                    $GD_Background = imagecreatetruecolor($thumb_width, $thumb_height);
                    imagefill($GD_Background, 0, 0, imagecolorallocate($GD_Background, 255, 255, 255));

                    // Resize and crop
                    imagecopyresampled($GD_Background,
                        $GD_Resource,
                        0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                        0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                        0, 0,
                        $new_width, $new_height,
                        $width, $height);
                    imagejpeg($GD_Background, $File_Directory_Destination . "$File_GUID.jpg", 100);
                    imagedestroy($GD_Resource);
                    imagedestroy($GD_Background);
                }
                unlink($File_Directory_Temporal);
            }

            $Old_Image = get_post_thumbnail_id($Wiki_Data->ID);
            if ($Old_Image) {
                $Old_Image_Path = get_attached_file($Old_Image);
                if (file_exists($Old_Image_Path)) {
                    //unlink($Old_Image_Path);
                }
            }

            $FilePath = $File_Directory_Destination . "$File_GUID.jpg";

            $MimeType   = wp_check_filetype($FilePath, null);
            $Attachment = [
                'post_mime_type' => $MimeType['type'],
                'post_parent'    => $Wiki_ID,
                'post_title'     => $Post_Title,
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            $Attachment_ID = wp_insert_attachment($Attachment, $FilePath, $Wiki_ID);
            set_post_thumbnail($Wiki_ID, $Attachment_ID);

            if (!function_exists('wp_crop_image')) {
                include(ABSPATH.'wp-admin/includes/image.php');
            }
            wp_generate_attachment_metadata($Attachment_ID,$FilePath);

            $Folders_ID = FileBird\Model\Folder::newOrGet('Wikis', FileBird\Model\Folder::newOrGet('KUI3 System', 0));
            FileBird\Model\Folder::setFoldersForPosts($Attachment_ID, $Folders_ID);
        } elseif (isset($REST_DATA['revision'])) {
            $Is_Revision = (int) sanitize_text_field($REST_DATA['revision']);

            $MetaData = get_post_meta($Wiki_Data->ID, 'revisions_metadata', true);
            $GetMetas = [];
            if ($MetaData) {
                $MetaData = json_decode($MetaData, true);

                if (isset($MetaData[$Is_Revision])) {
                    $GetMetas = $MetaData[$Is_Revision];
                }
            }

            if (isset($GetMetas['Thumbnail'])) {
                if (file_exists($GetMetas['Thumbnail'])) {
                    $Find = attachment_url_to_postid(str_replace(WP_CONTENT_DIR . "/uploads/", '', $GetMetas['Thumbnail']));
                    if ($Find !== 0) {
                        set_post_thumbnail($Wiki_Data->ID, $Find);
                    }
                }
            }

        }

        $Post_Old_Content = get_the_content(null, false, $Wiki_Data->ID);

        $Post_Content = str_replace('\\', '&bsol;', $Post_Content);

        wp_insert_post([
            'ID'            => $Wiki_ID,
            'post_type'     => 'wiki_post',
            'post_status'   => 'publish',
            'post_title'    => $Post_Title,
            'post_content'  => $Post_Content . ' ',
            'post_excerpt'  => $Post_Excerpt,
            'post_date'     => get_the_date('Y-m-d H:i:s', $Wiki_ID),
            'post_date_gmt' => get_gmt_from_date(get_the_date('Y-m-d H:i:s', $Wiki_ID), 'Y-m-d H:i:s'),
        ]);

        $RevisionLastUser = get_post_meta($Wiki_Data->ID, 'kui_user_id', true);
        $RevisionHistory  = get_post_meta($Wiki_Data->ID, 'authors', true);
        $RevisionMetaData = get_post_meta($Wiki_Data->ID, 'revisions_metadata', true);
        if ($RevisionHistory) {
            $RevisionHistory = json_decode($RevisionHistory, true);
        } else {
            $RevisionHistory = [];
        }
        if ($RevisionMetaData) {
            $RevisionMetaData = json_decode($RevisionMetaData, true);
        } else {
            $RevisionMetaData = [];
        }

        $Revisions      = wp_get_post_revisions($Wiki_ID);
        $Revisions_Last = array_shift($Revisions);

        if ($Revisions_Last) {
            if (!isset($RevisionHistory[$Revisions_Last->ID])) {
                $RevisionHistory[$Revisions_Last->ID] = $RevisionLastUser;
            }
            if (!isset($RevisionMetaData[$Revisions_Last->ID])) {
                $Old_Image_Path = false;
                $Old_Image      = get_post_thumbnail_id($Wiki_Data->ID);
                if ($Old_Image) {
                    $Old_Image_Path = get_attached_file($Old_Image);
                }

                $CatID = 0;
                $Cats  = wp_get_post_terms($Wiki_Data->ID, 'wiki_cat');
                if (isset($Cats[0])) {
                    $CatID = $Cats[0]->term_id;
                }

                $Post_Old_Content = str_replace(array("\n", "\r"), array('\n', '\r'), $Post_Old_Content);
                $Post_New_Content = str_replace(array("\n", "\r"), array('\n', '\r'), $Post_Content);

                $Diff_OpCodes = FineDiff::getDiffOpcodes($Diff_FromText = $Post_Old_Content, $Post_New_Content, FineDiff::$characterGranularity);
                ob_start();
                FineDiff::renderUTF8FromOpcode($Diff_FromText, $Diff_OpCodes, function ($opcode, $from, $from_offset, $from_len) {
                    if ($opcode === 'c') {
                        echo substr($from, $from_offset, $from_len);
                    } else if ($opcode === 'd') {
                        echo '<del>', substr($from, $from_offset, $from_len), '</del>';
                    } else /* if ( $opcode === 'i' ) */{
                        echo '<ins>', substr($from, $from_offset, $from_len), '</ins>';
                    }
                });
                $Diff_ToText = ob_get_clean();
                ob_start();
                FineDiff::renderUTF8FromOpcode($Diff_FromText, $Diff_OpCodes, function ($opcode) {
                    echo $opcode;
                });
                $Diff_CC   = ob_get_clean();
                $Diff_CC_I = substr_count($Diff_CC, 'i');
                $Diff_CC_D = substr_count($Diff_CC, 'd');
                $Diff_CC_C = substr_count($Diff_CC, 'c');

                $RevisionMetaData[$Revisions_Last->ID] = [
                    'User_ID'   => $RevisionLastUser,
                    'Thumbnail' => $Old_Image_Path,
                    'Cat'       => $CatID,
                    'Seo'       => get_post_meta($Wiki_Data->ID, '_yoast_wpseo_title', true),
                    'Dif'       => [
                        'Text' => $Diff_ToText,
                        'mC'   => $Diff_CC_C,
                        'mD'   => $Diff_CC_D,
                        'mI'   => $Diff_CC_I,
                    ],
                    'Comment'   => get_post_meta($Wiki_Data->ID, 'comment', true),
                ];
            }
        }

        update_post_meta($Wiki_Data->ID, 'kui_user_id', $KUI->UserSession_CurrentUser());
        update_post_meta($Wiki_Data->ID, 'authors', json_encode($RevisionHistory));
        update_post_meta($Wiki_Data->ID, 'revisions_metadata', wp_slash(json_encode($RevisionMetaData)));
        update_post_meta($Wiki_Data->ID, '_yoast_wpseo_title', $Post_SEOTitle . " - (Wiki) Karla's Project");
        update_post_meta($Wiki_Data->ID, 'comment', $Post_Comment);

        if ($Is_New === true) {
            update_post_meta($Wiki_Data->ID, 'importance', "0");
            update_post_meta($Wiki_Data->ID, 'creator', $KUI->UserSession_CurrentUser());
        }

        wp_set_post_terms($Wiki_Data->ID, [$Category_Data->term_id], 'wiki_cat');

        return [
            'ID'       => $Wiki_ID,
            'Title'    => $Post_Title,
            'SEOTitle' => substr($Post_SEOTitle, 0, strpos($Rendered, ' - ')),
            'Content'  => $Post_Content,
            'Comment'  => $Post_Comment,
            'Excerpt'  => $Post_Excerpt,
            'Image'    => get_the_post_thumbnail_url($Wiki_ID, 'large'),
            'Url'      => get_the_permalink($Wiki_ID),
        ];
    }

    return [];
}
function KUIREST_ENDPOINT_Wiki_Revisions($REST_DATA)
{
    $KUI = new KUI_REST([]);

    if (!isset($REST_DATA['wiki_id'])) {
        return new WP_Error('wiki_no_id', 'Se requiere un ID.');
    }

    $Print          = [];
    $Wiki_ID        = (int) sanitize_text_field($REST_DATA['wiki_id']);
    $Wiki_Data      = get_post($Wiki_ID);
    $Wiki_MetaData  = get_post_meta($Wiki_ID, 'revisions_metadata', true);
    $Wiki_Revisions = wp_get_post_revisions($Wiki_ID);

    if (get_post_type($Wiki_ID) !== 'wiki_post') {
        return [];
    }

    if ($Wiki_MetaData) {
        $Wiki_MetaData = json_decode($Wiki_MetaData, true);
    } else {
        $Wiki_MetaData = [];
    }

    $Wiki_Revisions_Keys = array_keys($Wiki_Revisions);

    if (!$Wiki_Revisions_Keys) {
        return [];
    }

    $Last_Author  = (int) get_post_meta($Wiki_Data->ID, 'kui_user_id', true);
    $Last_Date    = mysql2date('d F Y H:i', $Wiki_Data->post_modified_gmt, true) . " UTC";
    $Last_Diff    = $Wiki_Data->post_content;
    $Last_Diff_a  = 0;
    $Last_Diff_d  = 0;
    $Last_Comment = get_post_meta($Wiki_Data->ID, 'comment', true);

    $Print[] = [
        'ID'      => $Wiki_Data->ID,
        'Author'  => $Last_Author ? [
            'Photo' => $KUI->Displays_Users_Photo($Last_Author),
            'Name'  => $KUI->Displays_Users_Name($Last_Author),
        ] : false,
        'Date'    => $Last_Date,
        'Comment' => $Last_Comment,
        'm_Add'   => (int) $Last_Diff_a,
        'm_Mod'   => 0,
        'm_Del'   => (int) $Last_Diff_d,
        'm'       => nl2br(str_replace(['\n', '\r'], ["\n", "\r"], trim($Last_Diff))),
    ];

    for ($I = 0; $I < count($Wiki_Revisions); $I++) {
        $Revision_ID       = (int) $Wiki_Revisions_Keys[$I];
        $Revision_Metadata = $Wiki_MetaData[$Revision_ID];
        $Revision_Current  = $Wiki_Revisions[$Revision_ID];

        if (isset($Revision_Metadata['User_ID'])
            && get_post_status((int) $Revision_Metadata['User_ID']) !== false
            && get_post_type((int) $Revision_Metadata['User_ID']) === 'kui_user') {
            $Last_Author = (int) $Revision_Metadata['User_ID'];
        }

        if (isset($Revision_Metadata['Comment'])) {
            $Last_Comment = $Revision_Metadata['Comment'];
        }

        if (isset($Revision_Metadata['Dif']['mI'])) {
            $Last_Diff_a = $Revision_Metadata['Dif']['mI'];
        }
        if (isset($Revision_Metadata['Dif']['mC'])) {
            $Last_Diff_d = $Revision_Metadata['Dif']['mC'];
        }

        if ($I == 0 && isset($Revision_Metadata['Dif']['Text']) && isset($Print[0])) {
            $Print[0]['m']     = preg_replace('/\<(ins|del)\>([\\\\|r|n]*)\<\/(ins|del)\>/is', '$2', $Revision_Metadata['Dif']['Text']);
            $Print[0]['m']     = trim(nl2br(str_replace(['\n', '\r'], ["\n", "\r"], $Print[0]['m'])));
            $Print[0]['m_Add'] = $Revision_Metadata['Dif']['mI'];
            $Print[0]['m_Del'] = $Revision_Metadata['Dif']['mC'];
        }

        $Revision_Other_ID = (int) $Wiki_Revisions_Keys[$I + 1];
        if (isset($Wiki_MetaData[$Revision_Other_ID])) {
            $Revision_Metadata_Other = $Wiki_MetaData[$Revision_Other_ID];

            if (isset($Revision_Metadata_Other['Dif']['Text'])) {
                $Last_Diff   = preg_replace('/\<(ins|del)\>([\\\\|r|n]*)\<\/(ins|del)\>/is', '$2', $Revision_Metadata_Other['Dif']['Text']);
                $Last_Diff   = trim(nl2br(str_replace(['\n', '\r'], ["\n", "\r"], $Last_Diff)));
                $Last_Diff_a = $Revision_Metadata_Other['Dif']['mI'];
                $Last_Diff_d = $Revision_Metadata_Other['Dif']['mC'];
            }
        }

        $Last_Date = mysql2date('d F Y H:i', $Revision_Current->post_modified_gmt, true) . " UTC";

        if ($I === count($Wiki_Revisions) - 1) {
            $Last_Comment = '<em>Versión original</em>';
        }

        $Print[] = [
            'ID'      => $Revision_Current->ID,
            'Author'  => $Last_Author ? [
                'Photo' => $KUI->Displays_Users_Photo($Last_Author),
                'Name'  => $KUI->Displays_Users_Name($Last_Author),
            ] : false,
            'Date'    => $Last_Date,
            'Comment' => $Last_Comment,
            'm_Add'   => (int) $Last_Diff_a,
            'm_Mod'   => 0,
            'm_Del'   => (int) $Last_Diff_d,
            'm'       => $Last_Diff,
        ];
    }

    return $Print;
}
add_action('rest_api_init', function () {
    register_rest_route('kui', 'wiki/categories',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Wiki_Categories',
        ]
    );
    register_rest_route('kui', 'wiki/editor',
        [
            'methods'  => 'GET,POST',
            'callback' => 'KUIREST_ENDPOINT_Wiki_Editor',
        ]
    );
    register_rest_route('kui', 'wiki/revisions',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Wiki_Revisions',
        ]
    );
});
