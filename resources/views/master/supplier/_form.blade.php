<div class="box box-success <?= !isset($supplier['id']) ? 'collapsed-box' : '' ?>">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$supplier['id']) ? 'Update supplier' : 'Tambah supplier Baru'; ?></h3>
    	<?php if(!isset($supplier['id'])): ?>
    	<div class="box-tools">
			<button style="margin-top: -5px;" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
		</div>
	<?php endif; ?>
		
    </div>
    <div class="box-body" <?= !isset($supplier['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="supplier-form" action="<?php echo route('supplier', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$supplier['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($supplier['nama']) ? $supplier['nama']: ""?>" placeholder="Nama supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>

				</div>
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="alamat" class="control-label">Alamat</label>
					<div>
					   <input type="text" name="alamat" id="alamat" class="form-control" value="<?=isset($supplier['alamat']) ? $supplier['alamat']: ""?>" placeholder="Alamat supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['alamat'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['alamat'])) ? $errors['alamat'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="kota" class="control-label">Kota</label>
					<div>
					   <input type="text" name="kota" id="kota" class="form-control" value="<?=isset($supplier['kota']) ? $supplier['kota']: ""?>" placeholder="Kota supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['kota'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['kota'])) ? $errors['kota'][0] : '' ;?></small>
				</div>			

                 <div class="form-group" style="margin-top: 40px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$supplier['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>

			</div>
			<div class="col-xs-6 col-md-6">
				
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="telepon" class="control-label">Telepon</label>
					<div>
					   <input type="text" name="telepon" id="telepon" class="form-control" value="<?=isset($supplier['telepon']) ? $supplier['telepon']: ""?>" placeholder="Telepon supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['telepon'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['telepon'])) ? $errors['telepon'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="no_rekening" class="control-label">No Rekening supplier</label>
					<div>
					   <input type="text" name="no_rekening" id="no_rekening" class="form-control" value="<?=isset($supplier['no_rekening']) ? $supplier['no_rekening']: ""?>" placeholder="No Rekening supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['no_rekening'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['no_rekening'])) ? $errors['no_rekening'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="email" class="control-label">Email</label>
					<div>
					   <input type="text" name="email" id="email" class="form-control" value="<?=isset($supplier['email']) ? $supplier['email']: ""?>" placeholder="Email supplier">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['email'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['email'])) ? $errors['email'][0] : '' ;?></small>
				</div>	
	            
            </div>
            
        </form>
    </div>
</div>
