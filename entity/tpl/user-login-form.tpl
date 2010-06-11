<div class="frm">
    <form action="<?php echo $user_action ?>" method="post">
        <input type="submit" value="log in" class="btn" />
        <div><?php echo _("login:"); ?></div>
        <input type="text" name="user_login" class="txt" style="text-align: center;" />
        <div><?php echo _("password:"); ?></div>
        <input type="password" name="user_pwd" class="txt" style="text-align: center;" />
    </form>
</div>