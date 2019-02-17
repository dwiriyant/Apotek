<?php 
if (session('success') || session('error')){ 
?>
<div class="row">
	<div class="col-md-12">
		<?php if (session('error')) {?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<h4><i class="icon fa fa-ban"></i> Error!</h4>
			<?php echo session('error')?>
		</div>
		<?php }else{?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<h4><i class="icon fa fa-check"></i> Success!</h4>
			<?php echo session('success') ?>
		</div>
		<?php }?>
	</div>
</div>
<?php }?>