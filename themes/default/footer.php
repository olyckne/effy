<div id="ef-footer-wrap">
	<footer id="ef-footer" class="container">

	<?php $this->renderView('footer'); ?>

		<div class="ef-footer-column">
			<?php $this->renderView('footer-column1'); ?>
		<div class="ef-footer-column">
			
		</div>
		<div class="ef-footer-column">
			<?php $this->renderView('footer-column2'); ?>
		</div>

		<div class="ef-footer-column">
			<?php $this->renderView('footer-column3'); ?>
		</div>

		<div id="ef-footer-bottom">
			<?php $this->renderView('footer-bottom');?>
			<?php echo POWERED_BY; ?>
		</div>
	</footer>
</div>
</body>
</html>