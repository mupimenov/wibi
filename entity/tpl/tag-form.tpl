<form action="<?php echo $tag_action ?>" method="post" class="frm">
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <input type="hidden" name="tag_id" value="<?php echo $tag_id ?>" />
        <textarea name="tag_name" class="txt sizable tag-name"><?php echo $tag_name; ?></textarea>
        <?php echo $tag_remove; ?>
        <div>&nbsp;</div>
</form>
