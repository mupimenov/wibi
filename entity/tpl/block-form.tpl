<form action="<?php echo $block_action ?>" method="post" class="frm">
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <input type="hidden" name="block_id" value="<?php echo $block_id ?>" />
        <p><?php echo _("name:"); ?><input type="text" class="txt" maxlength="50" name="block_name" value="<?php echo $block_name; ?>" /></p>
        <textarea name="block_body" class="txt wide sizable comment-body"><?php echo $block_body; ?></textarea>
        <p><?php echo _("show block name?"); ?> <input type="checkbox" name="block_name_shown" value="1" <?php echo $block_name_shown; ?> /></p>
        <p><?php echo _("position:"); ?> <input type="text" class="txt" name="block_position" maxlength="3" size="3" value="<?php echo $block_position; ?>" /></p>
</form>
