<div class="frm">
    <form action="<?php echo $user_action ?>" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
        <input type="submit" value="<?php echo _("save config"); ?>" class="btn" />
        <div><?php echo _("user name:"); ?></div>
        <input type="text" name="user_name" class="txt" value="<?php echo $user_name; ?>" />
        <div><?php echo _("user password (repeat twice):"); ?></div>
        <input type="password" name="user_pwd" class="txt" />
        <input type="password" name="user_rpwd" class="txt" />
    </form>
</div>