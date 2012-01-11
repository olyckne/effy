<?php
	header("Content-Type:text/xml");
?>
<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
	<loc>
	<?php 
	// You might want to change this
	echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] 
	?>
	</loc>
	<lastmod>
	<?php 
	// And this, if you don't actually edit your site everyday
	echo @date("Y-m-d") 
	?>
	</lastmod>
	<changefreq>monthly</changefreq>
	<priority>0.8</priority>
</url>

</urlset>