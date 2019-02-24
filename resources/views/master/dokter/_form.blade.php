<div class="box box-success <?= !isset($dokter['id']) ? 'collapsed-box' : '' ?>">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$dokter['id']) ? 'Update dokter' : 'Tambah dokter Baru'; ?></h3>
    	<?php if(!isset($dokter['id'])): ?>
    	<div class="box-tools">
			<button style="margin-top: -5px;" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
		</div>
	<?php endif; ?>
		
    </div>
    <div class="box-body" <?= !isset($dokter['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="dokter-form" action="<?php echo route('dokter', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$dokter['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($dokter['nama']) ? $dokter['nama']: ""?>" placeholder="Nama Dokter">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="alamat" class="control-label">Alamat</label>
					<div>
					   <input type="text" name="alamat" id="alamat" class="form-control" value="<?=isset($dokter['alamat']) ? $dokter['alamat']: ""?>" placeholder="Alamat Dokter">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['alamat'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['alamat'])) ? $errors['alamat'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="telepon" class="control-label">Telepon</label>
					<div>
					   <input type="text" name="telepon" id="telepon" class="form-control" value="<?=isset($dokter['telepon']) ? $dokter['telepon']: ""?>" placeholder="Telepon Dokter">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['telepon'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['telepon'])) ? $errors['telepon'][0] : '' ;?></small>
				</div>


                 <div class="form-group" style="margin-top: 40px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$dokter['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>

			</div>
			<div class="col-xs-6 col-md-6">
	            

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="jk" class="control-label">Jenis Kelamin</label>
					<div>
					   <select class="form-control" name="jk">
                       		<option value="Laki laki" <?=@$dokter['jk']=='Laki laki'?'selected':''?> >Laki Laki</option>
                       		<option value="Perempuan" <?=@$dokter['jk']=='Perempuan'?'selected':''?> >Perempuan</option>
                        </select>
					</div>

					<small class="help-block" style="<?php echo (isset($errors['jk'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['jk'])) ? $errors['jk'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['tgl_lahir']) ? 'has-error' : '' ; ?>">
	                <input type="hidden" name="tgl_lahir" class="dt-value" value="<?= isset($obat['tgl_lahir']) ? $obat['tgl_lahir'] : date('Y-m-d',strtotime('now')) ;?>">
	                <label for="tgl-lahir" class="control-label">Tanggal Lahir</label>
	                
	                <div class="input-group date2">
	                    <input type="text" autocomplete="off" class="form-control" id="tgl-lahir" placeholder="Schedule" value="<?= date('D, j M Y', (isset($obat['tgl_lahir']) ? strtotime($obat['tgl_lahir']) : strtotime('now')))?>">
	                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	                </div>
	                <?php if (isset($errors['tgl_lahir'])) {?>
	                    <small class="help-block"><i class="fa fa-times-circle-o"></i> <?php echo $errors['tgl_lahir'][0]?></small>
	                <?php }?>

	                <small class="help-block" style="<?php echo (isset($errors['tgl_lahir'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['tgl_lahir'])) ? $errors['tgl_lahir'][0] : '' ;?></small>
	            </div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="jenis" class="control-label">Jenis Dokter</label>
					<div>
					   <input type="text" name="jenis" id="jenis" class="form-control" value="<?=isset($dokter['jenis']) ? $dokter['jenis']: ""?>" placeholder="Jenis Dokter">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['jenis'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['jenis'])) ? $errors['jenis'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="email" class="control-label">Email</label>
					<div>
					   <input type="text" name="email" id="email" class="form-control" value="<?=isset($dokter['email']) ? $dokter['email']: ""?>" placeholder="Email Dokter">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['email'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['email'])) ? $errors['email'][0] : '' ;?></small>
				</div>

            </div>
            
        </form>
    </div>
</div>
