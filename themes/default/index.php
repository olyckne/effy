<?php echo $this->getHeader(); ?>

<?php calculateWidth($hasSidebar1, $hasSidebar2, $classSidebar1, $classSidebar2, $classContent); ?>

<div id="ef-content-wrap">
<?php if($hasSidebar1):?>
<aside id="ef-sidebar1" class="<?php echo $classSidebar1;?>">
	<?php $this->renderView('sidebar1'); ?>
</aside> 
<?php endif; ?>
	<article id="ef-content" class="<?php echo $classContent;?>">
		<?php echo $this->html->getFeedback(); ?>	
		<?php $this->renderView('content'); ?>
	</article>

<?php if($hasSidebar2): ?>
<aside id="ef-sidebar2" class="<?php echo $classSidebar2;?>">
	<?php $this->renderView('sidebar2'); ?>
</aside>
<?php endif;?>

<div class="ef-triptych span-8">
	<?php $this->renderView('triptych1'); ?>
</div>

<div class="ef-triptych span-8">
	<?php $this->renderView('triptych2'); ?>
</div>
<div class="ef-triptych span-8 last">
	<?php $this->renderView('triptych3'); ?>	
</div>
</div>

</div> <!-- End container -->
<?php echo $this->getFooter(); ?>