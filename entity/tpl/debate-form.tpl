<form action="<?php echo $debate_action; ?>" method="post" class="frm">
        <h6><?php echo _("intense debate"); ?></h6>
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <p><?php echo _("intense debate acct:"); ?></p>
        <textarea name="debate_acct" class="txt wide sizable"><?php echo $debate_acct; ?></textarea>
</form>
