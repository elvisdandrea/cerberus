<style>
    body {
        clear: both;
        background: url("{$smarty.const.IMGURL}/bg.jpg") repeat scroll 0 0 rgba(0, 0, 0, 0);
        font-family: "Strait",sans-serif;
    }

    h1 {
        clear: both;
        color: #fff;
        padding: 30px;
        font-family: "Fjalla One",sans-serif;
        font-size: 50px;
        margin-top: 1px;
        text-shadow: 6px 1px 6px #333;
    }
    h1 img {
        float: left;
        margin-top: -0.5em;
        padding: 0 1em 0 0;
    }
    .text {
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
    .text li {
        padding: 0.3em;
        font-size: 15px;
    }
    .text label {
        line-height: 30px;
    }
    .banner {
        margin: 2.2em auto 0;
        width: 50%;
    }
    .message {
        background: none repeat scroll 0px 0px rgba(0, 0, 0, 0.25);
        text-shadow: 6px 1px 6px #333;
        padding: 1.2em;
    }
    .footer {
        text-align: center;
        margin: 1.8em 2.8em 0;
        font-size: 22px;
        font-weight: bold;
        color: #4cae4c;
    }
</style>
<div class="banner">
    <h1>
        <img src="{$smarty.const.IMGURL}/logo.png" alt="cerberus_logo" width="115px"/>
        <label>Cerberus Framework</label></h1>
    <div class="message">
        <div class="text">Ok, let's get started.
        <ul>
            <li>I created this framework because I needed something straight forward and still have high quality classes and tools.</li>
            <li>Straight forward, therefore faster!</li>
            <li>Every request will be routed automatically to the controller/action on module folder. Yes, modular ftw!</li>
            <li>Just use Rest class in lib for response and you don't need an entire different universe for ReSTful apps, you can use the same damn logic.</li>
            <li>We have a token based ReST authentication method, and you can choose where and how to store passwords.</li>
            <li>URL is automatically friendly-displayed in the most intuitive way. You may change it anyway.</li>
            <li>Non ajax functions loads the home first and then automatically runs the action to get the inner content wherever you wish.</li>
            <li>Ajax functions returns the inner content only with the javascript to replace the content you wish</li>
            <li>This means that every click on the site can run over ajax just replacing the content you need, but when the full URL is directly called, the same content will appear and you don't have to code it.</li>
            <li>External links are automatically considered external.</li>
            <li>Every module will have its separated template folder and it automatically finds it.</li>
            <li>A variety of tools are in the lib folder. You can safely delete any of them you don't need.</li>
            <li>Easy encryption method with random key, and you can create a secret passphrase to make it unique to you.</li>
            <li>Model has a "DBgrid" that can automatically display database query result content and you can stylize it.</li>
            <li>Prefer twig? Add it in lib folder and replace the "include" in View class.</li>
        </ul>
            <label>Fork me: <a href="https://github.com/elvisdandrea/cerberus">https://github.com/elvisdandrea/cerberus</a></label>
            <div class="footer"><span>High quality software and over-engineering are two different things!</span></div>
        </div>
    </div>
</div>