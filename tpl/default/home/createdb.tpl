<!--
    Cerberus Framework

    This is a template of the form
    for encrypted database file creation

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<div class="text">
    <a class="btn btn-darkyellow" href="{$smarty.const.BASEDIR}home"><-- Go back</a>
    <h2>Creating an Ecrypted Database File</h2>
    <ul>
        <li>Different from probably everything you've seen, you can create as many connections as you need</li>
        <li>From the controller, you can change model's connection by simply specifying the connection name</li>
        <li>Or you can easily create and manipulate as many models as you need, each one using a different connection</li>
        <li>The connection files are saved in app/ifc/data directory and names are hashed</li>
    </ul>
    <form action="{$smarty.const.BASEDIR}home/savedbfile">
        <span><label for="conname">Connection Name:</label><input type="text" id="conname" name="conname" /></span>
        <span><label for="host">Host:</label><input type="text" id="host" name="host" /></span>
        <span><label for="user">User:</label><input type="text" id="user" name="user" /></span>
        <span><label for="pass">Password:</label><input type="text" id="pass" name="pass" /></span>
        <span><label for="db">Database:</label><input type="text" id="db" name="db" /></span>
        <span><input class="btn btn-green" type="submit" value="Create" /></span>
    </form>
    <label id="alert" class="alert"></label>
</div>