<form action="<?php echo $activity_action ?>" method="post" class="frm">
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>" />
        <p><?php echo _("url:"); ?><input type="text" class="txt" maxlength="100" name="activity_url" value="<?php echo $activity_url; ?>" /></p>        
</form>
