<form action="<?php echo $page_action ?>" method="post">
    <input type="hidden" name="page_qcreate" value="1" />
    <input type="submit" value="<?php echo _("create"); ?>" class="btn" />
    <textarea name="page_body" class="txt wide sizable"><?php echo $page_body ?></textarea>
</form>
