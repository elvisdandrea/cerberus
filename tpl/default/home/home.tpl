<html>
    <head>
        <title>Cerberus - Do it simple and do it efficient</title>
        <link rel="stylesheet" href="{$smarty.const.T_CSSURL}/default.css" />
    </head>
    <body>
        <div class="banner">
            <h1>
                <img src="{$smarty.const.T_IMGURL}/logo.png" alt="cerberus_logo" width="115px"/>
                <label>Cerberus Framework</label></h1>
            <div class="message">
                <div class="text">
                    Ok, let's get started.
                <ul>
                    <li>I created this framework because I needed something straight forward but still have high quality classes and tools.</li>
                    <li>Every request will be routed automatically to the controller/action on module folder. Yes, modular!</li>
                    <li>URL is automatically friendly-displayed in the most intuitive way. You may change it anyway.</li>
                    <li>Non ajax functions loads the home first and then automatically runs the action to get the inner content wherever you wish.</li>
                    <li>Ajax functions returns the inner content only with the javascript to replace the content you wish</li>
                    <li>This means that every click on the site can run over ajax just replacing the content you need, but when the full URL is directly called, the same content will appear and you don't have to do any extra code for it.</li>
                    <li>External links are automatically considered external.</li>
                    <li>Every module will have its separated template folder and it automatically finds it.</li>
                    <li>A variety of tools are in the lib folder. You can safely delete any of them you don't need.</li>
                    <li>You don't have to include/require lib or module files, the handler autoloads them when you call.</li>
                    <li>Just use Rest class in lib for response and you don't need an entire different universe for ReSTful apps, you can use the same damn logic.</li>
                    <li>This has a token based ReST authentication method, and you can choose where and how to store passwords.</li>
                    <li>Easy encryption method with random key, and you can create a secret passphrase to make it unique to you.</li>
                    <li>Model has a "DBgrid" that can automatically display database query result content and you can stylize it.</li>
                    <li>Multiple template support. Changing templates are simply change view's template name.</li>
                    <li>Prefer twig? Add it in lib folder and replace the "include" in View class.</li>
                </ul>
                    Fork me: <a href="https://github.com/elvisdandrea/cerberus">https://github.com/elvisdandrea/cerberus</a>
                    <div class="footer"><span>High quality software and over-engineering are two different things!</span></div>
                </div>
            </div>
        </div>
    </body>
</html>