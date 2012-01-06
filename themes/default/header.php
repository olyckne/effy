<?php echo $this->getDoctype(); ?>

<head>
	
	<meta charset="<?php echo $charset;?>">

	<?php foreach ($meta as $key => $value): ?>
	<meta name="<?php echo $key;?>" content="<?php echo $value;?>">	
	<?php endforeach; ?>

	<title><?php echo $siteTitle;?></title>


	<!-- Blueprint -->
	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/print.css" type="text/css" media="print">
	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/regions.css">
	<!--[if lt IE 8]>
  	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/ie.css" type="text/css" media="screen, projection">
	<![endif]-->
	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/src/form.css">
	<link rel="stylesheet" href="<?php echo $url_path;?>/styles/blueprint/src/typography.css">
	
	<!-- End blueprint -->
	<!-- Theme specific style -->
<?php foreach($styles as $key => $value): ?>
	<link rel="stylesheet" href="<?php echo $value;?>" type="<?php echo $key;?>">
<?php endforeach; ?>

<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<?php echo $this->renderView('head'); ?>
</head>
<body>
<div class="container">

<header id="ef-header">
	<div id="ef-header-top">
		<?php $this->renderView('header-top'); ?>
	</div>

	<div id="ef-pageLogo">
		<a href='<?php echo $this->site_url;?>'><?php echo $this->siteTitle;?></a>
	</div>


	<?php $this->renderMainMenu();?>
</header>