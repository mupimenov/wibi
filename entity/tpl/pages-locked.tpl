<div id="pages-locked">
    <h6><?php echo _("sticked pages:"); ?></h6>
    <ul>
        <?php
        foreach ($locked_ps as $p) { ?>
        <li>
            <?php echo Utils::link($p->title, Utils::path("page", "view", array("id" => $p->id))); ?>
        </li>
        <?php } ?>
    </ul>
</div>
