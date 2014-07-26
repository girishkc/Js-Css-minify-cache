<?php
require(dirname(__FILE__).'/class/class.JsCssLoader.php');
require(dirname(__FILE__).'/libs/class.CssMin.php');
require(dirname(__FILE__).'/libs/class.JSMin.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1.0;"/>
    <title>TESTING CSS/JS Minify</title>
    <?php
    $loader = new JsCssLoader();
    $js_file = array('jquery.dataTables.js', 'jquery.js');

    $css_files = array('jquery-ui.css', 'datatable.css');

        $loader->loadJsFiles($js_file);
        $loader->loadCssFiles($css_files);
    ?>

</head>
<body>
    <p>
        Congradulations..! You successfulley minified css and js files!
    </p>
</body>

