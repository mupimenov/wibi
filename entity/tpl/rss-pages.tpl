<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<?php echo '<?xml-stylesheet type="text/css" href="'.Utils::root().'html/style.css'.'"?>'; ?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" 
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
    xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title><?php echo Config::getValue('site_title'); ?></title>
        <link><?php echo Utils::root(); ?></link>        
        <dc:language><?php echo WB_LANG; ?></dc:language>        
        <dc:rights>as is</dc:rights>
        <sy:updatePeriod>daily</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>
        <?php foreach ($ps as $p) { ?>
        <item>
            <title><?php echo $p->title; ?></title>
            <link><?php echo preg_replace('/\&/', '&#x26;', Utils::path("page", "view", array("id" => $p->id))); ?></link>
            <guid><?php echo preg_replace('/\&/', '&#x26;', Utils::path("page", "view", array("id" => $p->id))); ?></guid>
            <description><![CDATA[
                <?php echo Format::full($p->body); ?>
         ]]></description>            
        </item>
        <?php } ?>
    </channel>
</rss>
