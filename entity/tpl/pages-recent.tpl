<div id="pages-recent">
    <h6><?php echo _("recent pages:"); ?></h6>
    <ul>
        <?php
        foreach ($ps as $p) { ?>
        <li>
            <?php echo Utils::link($p->title, Utils::path("page", "view", array("id" => $p->id))); ?>
        </li>
        <?php } ?>
        <li><?php echo Utils::link(_("... show all"), Utils::path("page", "viewall")); ?></li>
    </ul>
</div>
