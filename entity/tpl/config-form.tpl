<form action="<?php echo $config_action; ?>" method="post" class="frm">
        <h6><?php echo _("general site settings"); ?></h6>
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <div><?php echo _("site title:"); ?></div>
        <input type="text" name="site_title" class="txt" value="<?php echo $site_title; ?>" />
        <div><?php echo _("site url"); ?></div>
        <input type="text" name="site_url" class="txt" value="<?php echo $site_url; ?>" />
        <div><?php echo _("number of posts on main page"); ?></div>
        <input type="text" name="page_last_limit" class="txt" value="<?php echo $page_last_limit; ?>" />
</form>
