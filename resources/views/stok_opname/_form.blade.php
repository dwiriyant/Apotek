<div class="box box-success <?= !isset($setting_biaya['id']) ? 'collapsed-box' : '' ?>">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$setting_biaya['id']) ? 'Update Biaya' : 'Tambah Biaya Baru'; ?></h3>
    	<?php if(!isset($setting_biaya['id'])): ?>
    	<div class="box-tools">
			<button style="margin-top: -5px;" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
		</div>
	<?php endif; ?>
		
    </div>
    <div class="box-body" <?= !isset($setting_biaya['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="setting-biaya-form" action="<?php echo route('setting-biaya', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$setting_biaya['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama Biaya</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($setting_biaya['nama']) ? $setting_biaya['nama']: ""?>" placeholder="Nama Biaya">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>

				</div>

				<div class="form-group <?php echo isset($errors['deskripsi']) ? 'has-error' : '' ; ?>">
					<label for="deskripsi" class="control-label">Deskripsi</label>
					<div>
					   <input type="text" name="deskripsi" id="deskripsi" class="form-control" value="<?=isset($setting_biaya['deskripsi']) ? $setting_biaya['deskripsi']: ""?>" placeholder="Deskripsi">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['deskripsi'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['deskripsi'])) ? $errors['deskripsi'][0] : '' ;?></small>

				</div>
				
				<div class="form-group <?php echo isset($errors['harga_satuan']) ? 'has-error' : '' ; ?>">
					<label for="biaya" class="control-label">Biaya</label>
					<div>
					   <input data-currency="biaya" type="text" class="form-control currency" value="<?=isset($setting_biaya['biaya']) ? $setting_biaya['biaya']: ""?>" placeholder="Biaya">
                       <input id="biaya" type="hidden" class="form-control" name="biaya" value="<?=isset($setting_biaya['biaya']) ? $setting_biaya['biaya']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['biaya'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['biaya'])) ? $errors['biaya'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['periode']) ? 'has-error' : '' ; ?>">
	                <input type="hidden" name="periode" class="dt-value" value="<?= isset($setting_biaya['periode']) ? $setting_biaya['periode'] : date('Y-m-d',strtotime('now')) ;?>">
	                <label for="periode" class="control-label">Periode</label>
	                
	                <div class="input-group date2">
	                    <input type="text" autocomplete="off" class="form-control" id="periode" placeholder="Periode" value="<?= date('D, j M Y', (isset($setting_biaya['periode']) ? strtotime($setting_biaya['periode']) : strtotime('now')))?>">
	                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	                </div>
	                <?php if (isset($errors['periode'])) {?>
	                    <small class="help-block"><i class="fa fa-times-circle-o"></i> <?php echo $errors['periode'][0]?></small>
	                <?php }?>

	                <small class="help-block" style="<?php echo (isset($errors['periode'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['periode'])) ? $errors['periode'][0] : '' ;?></small>
	            </div>

			</div>
			<div class="col-xs-6 col-md-6">
				<div class="form-group" style="margin-top: 25px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$setting_biaya['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>
	            
            </div>
            
        </form>
    </div>
</div>
