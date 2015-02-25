<html>
<head>
    <title>Cerberus - Do it simple and do it efficiently</title>
</head>
<body>
<style>
    html {
        clear: both;
        background: url("{$smarty.const.IMGURL}/bg.jpg") repeat scroll 0 0 rgba(0, 0, 0, 0);
        font-family: "Strait",sans-serif;
    }

    h1 {
        clear: both;
        color: #fff;
        padding: 30px;
        font-family: "Fjalla One",sans-serif;
        font-size: 25px;
        margin-top: 1px;
        text-shadow: 6px 1px 6px #333;
    }
    label {
        clear: both;
        border: medium none;
        color: #98af95;
        font-family: "Strait",sans-serif;
        font-size: 18px;
        outline: medium none;
        padding: 6px 30px 6px 6px;
        margin: 0;
        display: block;
    }
    .banner {
        margin: 100px auto 0;
        width: 50%;
    }
    .message {
        background: none repeat scroll 0px 0px rgba(0, 0, 0, 0.25);
        text-shadow: 6px 1px 6px #333;
        padding: 1.2em;
    }
</style>
<div class="banner">
    <h1>
        <img src="{$smarty.const.IMGURL}/logo.png" alt="cerberus_logo" width="90px"/>
        Sorry, something went bad!</h1>
    <div class="message">
        {if (ENVDEV == '0')}
            <label>I know this is emarassing, but the server must be under maintenance. Please come back later.</label>
        {else}
            <label>
                {$error['message']} <br>
                <hr>
                {if (isset($error['file']))}
                    File: {$error['file']} <br>
                    Line: {$error['line']}
                    <hr>
                {/if}
                <label>Trace:</label>
            {foreach from=$trace item="action"}
                {if (isset($action['file']))}
                    <hr>
                    <ul>
                        <li>File: {$action['file']}</li>
                        <li>Line: {$action['line']}</li>
                        <li>Class: {$action['class']}</li>
                        <li>Function: {$action['function']}</li>
                    </ul>
                {/if}
            {/foreach}
            </label>
        {/if}
    </div>
</div>
</body>
</html>