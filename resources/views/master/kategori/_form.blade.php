<script type="text/javascript">
    var community = 'exist';
</script>

<div class="box box-success collapsed-box">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$kategori['id']) ? 'Update kategori' : 'Tambah kategori Baru'; ?></h3>

		<button style="margin-top: -5px;" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
		
    </div>
    <div class="box-body" style="display: none;">
        <form method="post" id="kategori-form" action="<?php echo route('kategori', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$kategori['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama Kategori</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($kategori['nama']) ? $kategori['nama']: ""?>" placeholder="Kode kategori">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>

				</div>

			</div>
			<div class="col-xs-6 col-md-6">
				<div class="form-group" style="margin-top: 25px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$kategori['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>
	            
            </div>
            
        </form>
    </div>
</div>
