<div class="frm">
    <form action="<?php echo $config_action; ?>" method="post">
        <input type="submit" value="<?php echo _("save config"); ?>" class="btn" />
        <div><?php echo _("site title:"); ?></div>
        <input type="text" name="site_title" class="txt" value="<?php echo $site_title; ?>" />
        <div><?php echo _("site url"); ?></div>
        <input type="text" name="site_url" class="txt" value="<?php echo $site_url; ?>" />
        <div><?php echo _("number of posts on main page"); ?></div>
        <input type="text" name="page_last_limit" class="txt" value="<?php echo $page_last_limit; ?>" />
    </form>
</div>