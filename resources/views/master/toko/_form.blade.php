<div class="box box-success <?= !isset($toko['id']) ? 'collapsed-box' : '' ?>">
    
    <div class="box-body" <?= !isset($toko['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="toko-form" action="<?php echo route('toko', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$toko['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama Apotek</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($toko['nama']) ? $toko['nama']: ""?>" placeholder="Kode toko">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>

				</div>

				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="alamat" class="control-label">Alamat Apotek</label>
					<div>
					   <input type="text" name="alamat" id="alamat" class="form-control" value="<?=isset($toko['alamat']) ? $toko['alamat']: ""?>" placeholder="alamat toko">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['alamat'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['alamat'])) ? $errors['alamat'][0] : '' ;?></small>

				</div>

				<div class="form-group <?php echo isset($errors['no_telp']) ? 'has-error' : '' ; ?>">
					<label for="no_telp" class="control-label">No Telp Apotek</label>
					<div>
					   <input type="text" name="no_telp" id="no_telp" class="form-control" value="<?=isset($toko['no_telp']) ? $toko['no_telp']: ""?>" placeholder="no_telp toko">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['no_telp'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['no_telp'])) ? $errors['no_telp'][0] : '' ;?></small>

				</div>

			</div>
			<div class="col-xs-6 col-md-6">
				<div class="form-group" style="margin-top: 25px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$toko['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>
	            
            </div>
            
        </form>
    </div>
</div>
