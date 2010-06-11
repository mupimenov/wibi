<form action="<?php echo $comment_action ?>" method="post">
        <input type="submit" value="<?php echo _("save"); ?>" class="btn" />
        <input type="hidden" name="page_id" value="<?php echo $page_id ?>" />
        <div><?php echo _("name:"); ?> <input type="text" class="txt" maxlength="50" size="25" name="comment_author" value="<?php echo $comment_author; ?>" /></div>
        <textarea name="comment_body" class="txt wide sizable comment-body"><?php echo $comment_body; ?></textarea>
        <input type="hidden" name="captcha_gthan" value="<?php echo $captcha_gthan; ?>" />
        <p><?php echo _("Type the nearest <a href=\"http://en.wikipedia.org/wiki/Prime_number\" title=\"Prime number (Wikipedia)\">prime number</a> greater than"); echo ' ' . $captcha_gthan; ?> <input type="text" class="txt" name="captcha_prime" maxlength="2" size="2" /></p>
</form>
