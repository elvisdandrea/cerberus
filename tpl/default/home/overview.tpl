<!--
    Cerberus Framework

    This is a template visualize the framework
    functionality.

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<div class="text">
    Ok, let's get it started.
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
    <span>Let's create an encrypted database connection file <a class="btn btn-green" href="{$smarty.const.BASEDIR}home/createdb">click me!</a></span>
    <span>Fork me: <a href="https://github.com/elvisdandrea/cerberus">https://github.com/elvisdandrea/cerberus</a></span>
    <div class="footer"><span>High quality software and over-engineering are two different things!</span></div>
</div>