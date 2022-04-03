<?php
/*
Plugin Name:    KUI3
Plugin URI:     https://karlaperezyt.com
Description:    Karlas User Interface v3
Version:        3
Author:         Karla Pérez
Author URI:     https://karlaperezyt.com
*/

/*
 * KUI3 DATA TYPES
 * Inicialización de Usuarios, Comentarios y suscripciones unificadas.
 */
require_once "KUI3.custom_hooks.php";
require_once "KUI3.legacy.php";

require_once "KUI3.menu.php";
require_once "KUI3.data_types.php";
require_once "KUI3.columns.php";
require_once "KUI3.markdown.php";

function KUI3_Administration_Styles()
{
    ob_start();
    ?>
    <style>
        .KUI3_Admin_Forms {
            background: white;
            margin: 10px -2px -2px;
            padding: 15px 15px 0;
            box-sizing: border-box;
            outline: 10px solid #f6f7f7;
            border: 1px solid #ddd;
            width: calc(100% + 4px);
        }
        .KUI3_Admin_Forms > h4 {
            font-size: 12px;
            margin: 20px 0 5px;
        }
        .KUI3_Admin_Forms > *:first-child {
            margin-top: 0;
        }
        .KUI3_Admin_Forms > label {
            display: block;
            background: rgba(0,0,0,0.02);
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            max-width: 380px;
        }
        .KUI3_Admin_Forms > ._Grow {
            max-width: 800px !important;
            width: 100% !important;
        }
        #postbox-container-2 .KUI3_Admin_Forms > label {
            display: inline-block;
            width: 380px;
            margin-right: 20px;
            max-width: 380px;
        }
        .KUI3_Admin_Forms > label > span {
            padding: 0 10px;
        }
        .KUI3_Admin_Forms label > span {
            display: block;
            font-size: 12px;
        }
        .KUI3_Admin_Forms label > span:first-of-type {
            margin-bottom: 10px;
            text-align: center;
        }
        .KUI3_Admin_Forms label > span > input, .KUI3_Admin_Forms label > span > textarea, .KUI3_Admin_Forms label > span > select {
            border-radius: 2px;
            border: 1px solid #bbb;
            box-shadow: inset 0 3px 5px rgba(0,0,0,0.03);
        }
        .KUI3_Admin_Forms label > span > input:hover, .KUI3_Admin_Forms label > span > textarea:hover, .KUI3_Admin_Forms label > span > select:hover {
            border-color: #df1aca;
        }
        .KUI3_Admin_Forms label > span > input:focus, .KUI3_Admin_Forms label > span > textarea:focus, .KUI3_Admin_Forms label > span > select:focus {
            outline: 3px solid #df1aca29;
            border-color: #df1aca;
        }
        .KUI3_Admin_Forms label > span > input[type="text"],.KUI3_Admin_Forms label > span > input[type="password"],.KUI3_Admin_Forms label > span > textarea,.KUI3_Admin_Forms label > span > select {
            width: 100%;
        }
        .KUI3_Admin_Forms label > span > textarea {
            height: 200px;
        }
        .KUI3_Admin_Forms label > span > input[type="checkbox"] {
            display: block;
            margin: 0 auto;
        }
        .KUI3_Admin_Forms > hr {
            border-top: 1px solid #ddd;
            border-bottom: none;
            margin: 20px -15px;
        }
        .KUI3_Admin_Forms > div.Group {
            background: rgba(0,0,0,0.02);
            border-radius: 5px;
            padding: 10px 0;
            margin-bottom: 20px;
            max-width: calc(500px + 20px);
            display: flex;
            align-items: stretch;
            flex-direction: row;
        }
        .KUI3_Admin_Forms > div.Group > label {
            margin: 0 5px;
            border-right: 1px solid rgba(0,0,0,0.1);
            padding: 0 15px;
            display: flex;
            flex-flow: column;
            justify-content: center;
        }
        .KUI3_Admin_Forms > div.Group > label:last-child {
            border-right-color: transparent;
        }
        .KUI3_Admin_Forms label > span:only-child {
            margin-bottom: 0;
        }

        #poststuff #post-body.columns-2 {
            margin-right: 400px;
        }
        #post-body.columns-2 #postbox-container-1 {
            width: 380px;
            margin-right: -400px;
        }
        #poststuff #post-body.columns-2 #side-sortables {
            width: 380px;
        }



        #postbox-container-2 {
            float: right;
            width: calc(100% - 400px) !important;
        }
        #post-body {
            flex-direction: row-reverse;
            width: 100%;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        #post-body-content {
            width: calc(100% - 400px);
            float: right;
        }
        #postbox-container-1 {
            width: 400px !important;
            margin: 0 !important;
            float: left !important;
        }
        #postbox-container-2 {
            width: calc(100% - 400px);
        }
    </style>
    <?php
    return ob_get_clean();
}


/*
 * REST API SERVER FILES
 * Archivos de inicialización REST
 *
 * --
 */
foreach (glob(WP_CONTENT_DIR.'/plugins/KUI3/REST/REST*php') as $RESTpath)
{
    require_once $RESTpath;
}


/*
 * SCRIPTS COMMON
 * Archivicos de funciones comunes
 *
 * --
 */
foreach (glob(WP_CONTENT_DIR.'/plugins/KUI3/Libraries/*.php') as $ScriptPath)
{
    require_once $ScriptPath;
}
foreach (glob(WP_CONTENT_DIR.'/plugins/KUI3/Scripts/SCRIPT.*.php') as $ScriptPath)
{
    require_once $ScriptPath;
}

/*
 * COPITO
 * Archivos de Copito Bot
 *
 * --
 */
require_once WP_CONTENT_DIR.'/plugins/KUI3/Copito/COPITO.php';






