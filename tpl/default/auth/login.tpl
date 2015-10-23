<!--
    Cerberus Framework

    This is a template visualize the framework
    functionality.

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<html>
<head>
    <title>Cerberus - Do it simple and do it efficiently</title>
    <link rel="stylesheet" href="{$smarty.const.T_CSSURL}/default.css" />
</head>
<body>
<div class="banner">
    <h1>
        <img src="{$smarty.const.T_IMGURL}/logo.png" alt="cerberus_logo" width="115px"/>
        <label>Cerberus Framework</label></h1>
    <div id="main" class="message">
            <!--    Not an action, let's go home  -->
            {include 'auth/form.tpl'}
    </div>
</div>
</body>
<script src="{$smarty.const.JSURL}/jquery.js"></script>
<script src="{$smarty.const.JSURL}/md5.js"></script>
<script src="{$smarty.const.JSURL}/html.js"></script>
<script src="{$smarty.const.JSURL}/main.js"></script>
</html>