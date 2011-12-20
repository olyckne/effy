<?php echo $this->getDoctype(); ?>

<head>
	
	<meta charset="<?php echo $charset;?>">

	<?php foreach ($meta as $key => $value): ?>
	<meta name="<?php echo $key;?>" content="<?php echo $value;?>">	
	<?php endforeach; ?>

	<title><?php echo $pageTitle;?></title>


	<!-- Blueprint -->
	<link rel="stylesheet" href="<?php echo $urlPath;?>/styles/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="<?php echo $urlPath;?>/styles/blueprint/print.css" type="text/css" media="print">
	<link rel="stylesheet" href="<?php echo $urlPath;?>/styles/blueprint/regions.css">
	<!--[if lt IE 8]>
  	<link rel="stylesheet" href="<?php echo $urlPath;?>/styles/blueprint/ie.css" type="text/css" media="screen, projection">
	<![endif]-->
		
	<!-- End blueprint -->

	<!-- Theme specific style -->
<?php foreach($styles as $key => $value): ?>
	<link rel="stylesheet" href="<?php echo $value;?>" title="<?php echo $key;?>">
<?php endforeach; ?>

<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<div class="container">

<header id="ef-header">
	<div id="ef-header-top">
		<?php $this->renderView('header-top'); ?>
	</div>

	<div id="ef-pageLogo">
		<?php echo $this->pageTitle;?>
	</div>


	<?php $this->renderMenu();?>
</header>