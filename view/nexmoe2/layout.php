<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <title><?php e(config('site_name'));?></title>
    <link rel="shortcut icon" href="//<?php e($_SERVER['HTTP_HOST'].'/'.substr(constant("VIEW_PATH"), strlen(ROOT)));?>theme/favicon.ico">
    <link rel="stylesheet" href="//cdnjs.loli.net/ajax/libs/mdui/0.4.1/css/mdui.css">
    <link rel="stylesheet" href="//<?php e($_SERVER['HTTP_HOST'].'/'.substr(constant("VIEW_PATH"), strlen(ROOT)));?>/theme/style.css">
    <script src="//cdnjs.loli.net/ajax/libs/mdui/0.4.1/js/mdui.min.js"></script>
    <style>
    /* 图标icon */
    .icon{
        float: left;
        width: 30px;
        height: 30px;
        background: url(<?php e('//'.$_SERVER['HTTP_HOST'].'/'.substr(constant("VIEW_PATH"), strlen(ROOT)));?>theme/align-right.png);
        background-size:100% 100%;
        transition: .6s;
    }
    /* 图标动画icon */
    .icon:hover{
        background: url(<?php e('//'.$_SERVER['HTTP_HOST'].'/'.substr(constant("VIEW_PATH"), strlen(ROOT)));?>theme/close.png);
        background-size:100% 100%;
        transform: rotate(360deg);
        -webkit-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
    }
    </style>
</head>
<body class="mdui-theme-primary-blue-grey mdui-theme-accent-blue">
    <header class="nav">
        <div class="navSize">
            <a href="/"><img class="avatar" src="//q.qlogo.cn/g?b=qq&nk=<?php e(config('qq'));?>&s=100"/></a>
            <div class="navRight">
                <ul class="navul">
                    <li class="navli"><label class="mdui-switch">看图模式: <input type="checkbox" id="image_view" <?php if ($_COOKIE["image_mode"] == "1") {echo "checked";} ?>><i class="mdui-switch-icon"></i></label></li>
                    <li class="navli"><a href="<?php e(config('blog_url'));?>" target="_blank">博客</a></li>
                    <li class="navli"><a href="/login">登陆</a></li>
                </ul>
                <div class="icon"></div>
            </div>
        </div>
    </header>
    <div class="mdui-container">
        <div class="mdui-container-fluid">
        <div class="mdui-toolbar nexmoe-item">
            <a href="/"><?php e(config('site_name'));?></a>
            <?php foreach((array)$navs as $n=>$l):?>
            <i class="mdui-icon material-icons mdui-icon-dark" style="margin:0;">chevron_right</i>
            <a href="<?php e($l);?>"><?php e($n);?></a>
            <?php endforeach;?>
            <!--<a href="javascript:;" class="mdui-btn mdui-btn-icon"><i class="mdui-icon material-icons">refresh</i></a>-->
        </div>
        </div>
        <?php view::section('content');?>
        <!-- 看图模式 -->
        <script>
            var ckname='image_mode';
            function getCookie(name) {
                var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
                if(arr=document.cookie.match(reg))
                    return unescape(arr[2]); 
                else
                    return null; 
            } 
            function setCookie(key,value,day){
                var exp = new Date(); 
                exp.setTime(exp.getTime() - 1); 
                var cval=getCookie(key); 
                if (cval!=null)
                    document.cookie= key + "=" + cval + ";expires=" + exp.toGMTString(); 
                var date = new Date();
                var nowDate = date.getDate();
                date.setDate(nowDate + day);
                var cookie = key + "=" + value + "; expires=" + date;
                document.cookie = cookie;
                return cookie;
            }
            $('#image_view').on('click', function () {
                if($(this).prop('checked') == true) {
                    setCookie(ckname,1,1);
                    window.location.href=window.location.href;
                } else {
                    setCookie(ckname,0,1);
                    window.location.href=window.location.href;
                }
            });
        </script>
      </div>
</body>
</html>