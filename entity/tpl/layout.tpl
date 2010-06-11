<?php
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?php echo Utils::get_title(); ?></title>
        <link href="<?php echo Utils::root()."html/style.css"; ?>" rel="stylesheet" type="text/css" />
        <link title="RSS for pages" type="application/rss+xml" rel="alternate" href="<?php echo Utils::path("rss", "pages"); ?>"/>
        <script type="text/javascript" src="<?php echo Utils::root()."html/wb_funcs.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo Utils::root()."html/sx_sizable.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo Utils::root()."html/wb_tags.js"; ?>"></script>
        <script type="text/javascript" src="<?php echo Utils::root()."html/wb_sort_comments.js"; ?>"></script>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h1><?php echo Utils::link(Config::getValue("site_title"), Utils::root()); ?></h1>
                <div id="user">
                    <?php echo $user; ?>
                </div>
            </div>
            <div id="leftbar">
                <div id="log">
                    <?php echo $log; ?>
                </div>
                <div id="content">
                    <?php echo $content; ?>
                </div>
                <script type="text/javascript">
                    sx_appendTextareas();
                </script>
            </div>
            <div id="sidebar">
                <?php echo $sidebar; ?>
            </div>
            <div id="footer-empty"></div>
        </div>
        <div id="footer"><div>Copyright &copy; 2010 <?php echo Utils::link(Config::getValue("site_title"),  Utils::root()); ?></div></div>
    </body>
</html>
