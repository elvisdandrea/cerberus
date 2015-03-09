<!--
    Cerberus Framework

    This is a template of the form
    for user creation

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<div class="text">
    <a class="btn btn-darkyellow" href="{$smarty.const.BASEDIR}home"><-- Go back</a>
    <h2>Creating an User</h2>
    <form action="{$smarty.const.BASEDIR}home/createuser">
        <span><label for="user">User:</label><input type="text" id="user" name="user" /></span>
        <span><label for="pass">Password:</label><input type="text" id="pass" name="pass" /></span>
        <span><label for="passrepeat">Repeat Password:</label><input type="text" id="db" name="passrepeat" /></span>
        <span><input class="btn btn-green" type="submit" value="Create" /></span>
    </form>
    <label id="alert" class="alert"></label>
</div>