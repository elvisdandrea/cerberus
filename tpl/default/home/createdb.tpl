<div class="text">
    <a href="{$smarty.const.BASEDIR}home">Go back!</a>
    <form action="{$smarty.const.BASEDIR}home/savedbfile">
        <label>Creating an Ecrypted Database File</label>
        <span><label for="conname">Connection Name:</label><input type="text" id="conname" name="conname" /></span>
        <span><label for="host">Host</label><input type="text" id="host" name="host" /></span>
        <span><label for="user">User</label><input type="text" id="user" name="user" /></span>
        <span><label for="pass">Password</label><input type="text" id="pass" name="pass" /></span>
        <span><label for="db">Database</label><input type="text" id="db" name="db" /></span>
        <span><input type="submit" value="Create" /></span>
    </form>
    <label id="alert" class="alert"></label>
</div>