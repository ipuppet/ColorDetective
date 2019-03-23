<?php session_start() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>ColorDetective</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <!--加载css-->
    <link rel="stylesheet" href="./static/library/animate.css">
    <link rel="stylesheet" href="./static/library/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="./static/library/jquery/jquery.fullPage.css">
    <link rel="stylesheet" href="./static/css/style.css">
    <!--加载js-->
    <script src="./static/library/jquery/jquery-3.3.1.js"></script>
    <script src="./static/library/jquery/jquery.fullPage.min.js"></script>
    <script src="./static/library/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div id="mesBox"></div>
<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(__FILE__) . DS);
define('__VIEW__', __ROOT__ . 'view' . DS);
/*$WEB = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$WEB = $WEB == '/' ? '/' : $WEB . '/';
define('__WEB__', $WEB);*/
include './base/controller.php';
$controller = new controller();
$controller->run();
?>
<script>
    var timeOutFlag;
    function showMsg(msg, time, animateTime = 300) {
        var e = $('#mesBox');
        if (e.css('display') !== 'none'){
            e.fadeOut(animateTime);
            clearTimeout(timeOutFlag);
        }
        e.text(msg).fadeIn(animateTime);
        timeOutFlag = setTimeout(function () {
            e.fadeOut(animateTime)
        }, time)
    }
</script>
</body>
</html>