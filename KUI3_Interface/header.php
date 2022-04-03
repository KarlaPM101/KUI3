<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="profile" href="https://gmpg.org/xfn/11" />
    <link rel="stylesheet" href="/wp-content/themes/karlasflex/imaging/md/editormd.min.css" />
    <script type="text/javascript" src="https://apis.google.com/js/platform.js"></script>
    <?php
    if ((current_user_can('editor') || current_user_can('administrator')))
    {
        echo '<link rel="stylesheet" href="/wp-content/themes/karlasflex/kui_engine/00000000_000_testing/KUI.css?_='.time().'" />';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/00000000_000_testing/jQuery.js?_=" type="text/javascript"></script>';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/00000000_000_testing/jQuery_Scroll.js?_=" type="text/javascript"></script>';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/00000000_000_testing/KUI_Testing.js?_='.time().'" type="text/javascript"></script>';
    }
    else
    {
        echo '<link rel="stylesheet" href="/wp-content/themes/karlasflex/kui_engine/20210918_001/KUI.css?Release=20210918_002" />';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/20210918_001/jQuery.js?Release=20210918" type="text/javascript"></script>';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/20210918_001/jQuery_Scroll.js?Release=20210918" type="text/javascript"></script>';
        echo '<script src="/wp-content/themes/karlasflex/kui_engine/20210918_001/KUI.js?Release=20210918" type="text/javascript"></script>';
    }
    ?>
    <script src="/wp-content/themes/karlasflex/imaging/md/editormd.min.js" type="text/javascript"></script>
    <script src="/wp-content/themes/karlasflex/imaging/md/languages/en.js" type="text/javascript"></script>
    <?php
    if($_COOKIE['kui3_noscript']!=='1'){
    ?>
    <noscript>
        <meta http-equiv="refresh" content="0;url=<?php echo $_SERVER['REQUEST_URI']."?_kui_noscript=1"; ?>">
    </noscript>

    <?php
    }

    wp_head();

    ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open();

if($_COOKIE['kui3_noscript']==='1'):

$GLOBALS['PgTitle'] = 'Página Principal';
?>
<noscript>
<div id="MOUNT">


<?php
locate_template( "Parts/page_navigation.php", true );
endif;
