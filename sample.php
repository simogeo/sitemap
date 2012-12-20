<?php

include_once('class.sitemap.php');


$sitemap = new sitemap('http://www.mywebsite.com', './');
$sitemap->generate();

echo '<h2>Displaying entries number</h2>';
echo $sitemap->getCounter(). ' entries have been added to the file.<br />';

echo '<h2>Displaying log</h2>';
echo $sitemap->getLog();

?>