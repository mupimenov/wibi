<form action="<?php echo $page_action ?>" method="post">
    <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
    <input type="hidden" name="page_id" value="<?php echo $page_id ?>" />
    <input type="hidden" name="page_author" value="<?php echo $page_author ?>" />
    <div class="page-date"><?php echo $page_date ?></div>
    <textarea name="page_title" class="txt wide sizable page-title"><?php echo $page_title ?></textarea>
    <textarea name="page_body" class="txt wide sizable page-body"><?php echo $page_body ?></textarea>    
    <div><?php echo _("stick the page"); ?><input type="checkbox" name="page_lock" value="1" <?php echo $lock_checked; ?> /></div>
    <?php self::invoke_listeners(self::$within_render_page_form, $parms); ?>    
    <?php echo $page_remove; ?>    
</form>