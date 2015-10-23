<!--
    Cerberus Framework

    This is a template of the form
    for encrypted database file creation

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<div class="text">
    <a class="btn btn-darkyellow" href="{$smarty.const.BASEDIR}home"><-- Go back</a>
    <h2>Login Page Example</h2>
    <ul>
        <li>When REQUIRE_LOGIN is set to 1, this is the page it will be loaded in case the user is not logged in yet</li>
        <li>All you need to do is create your query on database to check user login</li>
    </ul>
    <form action="{$smarty.const.BASEDIR}auth/login">
        <span><label for="user">User:</label><input type="text" id="user" name="user" /></span>
        <span><label for="pass">Password:</label><input type="text" id="pass" name="pass" /></span>
        <span><input class="btn btn-green" type="submit" value="Login" /></span>
    </form>
    <label id="alert" class="alert"></label>
</div>