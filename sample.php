<?php

include_once('class.sitemap.php');


$sitemap = new sitemap('http://www.mywebsite.com', './');
$sitemap->generate();

echo '<h2>Displaying entries number</h2>';
echo '<p>' . $sitemap->getCounter() . ' entries have been added to the file.</p>';

echo '<h2>Accessing sitemap file</h2>';
echo '<p><a href="./sitemap.xml">sitemap file</a></p>';

echo '<h2>Displaying log</h2>';
echo $sitemap->getLog();

?>