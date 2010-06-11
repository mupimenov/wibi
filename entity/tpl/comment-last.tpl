<div class="last-comments">
    <h3><?php echo _("Last comments"); ?></h3>
    <div>
    <?php
        foreach ($comments as $comment) {
            echo '<div class="last-comment">';
                echo '<h4 class="page-title">' . $comment["page_title"] . '</h4>';
                echo '<div class="comment">';
                    echo '<h6>' . $comment["comment_author"] . '<span class="comment-time">' . $comment["comment_time"] . '</span></h6>';
                    echo '<div class="comment-body">' . $comment["comment_body"] . '</div>';
                echo '</div>';
            echo '</div>';
        }
    ?>
    </div>
</div>
