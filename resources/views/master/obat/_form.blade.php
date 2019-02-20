<script type="text/javascript">
    var community = 'exist';
</script>

<div class="box box-success collapsed-box">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$obat['id']) ? 'Update Obat' : 'Tambah Obat Baru'; ?></h3>
		<button style="margin-top: -5px;" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 

    </div>
    <div class="box-body" style="display: none;">
        <form method="post" id="obat-form" action="<?php echo route('obat', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$obat['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['kode']) ? 'has-error' : '' ; ?>">
					<label for="kode" class="control-label">Kode Obat</label>
					<div>
					   <input type="text" name="kode" id="kode" class="form-control" value="<?=isset($obat['kode']) ? $obat['kode']: ""?>" placeholder="Kode Obat">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['kode'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['kode'])) ? $errors['kode'][0] : '' ;?></small>

				</div>
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama Obat</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($obat['name']) ? $obat['name']: ""?>" placeholder="Nama Obat">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="kategori" class="control-label">Kategori Obat</label>
					<div>
					   <select class="form-control" name="kategori">
					   		@foreach($kategori as $kat)
                       		<option value="0" <?=@$obat['kategori']=='1' ? 'selected' : ''?> >Obat Bebas</option>
					   		@endforeach
                        </select>
					</div>

					<small class="help-block" style="<?php echo (isset($errors['kategori'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['kategori'])) ? $errors['kategori'][0] : '' ;?></small>
				</div>
				<div id="schedule" class="form-group <?php echo isset($errors['tgl_kadaluarsa']) ? 'has-error' : '' ; ?>">
	                <input type="hidden" name="tgl_kadaluarsa" class="dt-value" value="<?= isset($obat['tgl_kadaluarsa']) ? date('Y-m-d H:i:s',$obat['tgl_kadaluarsa']) : date('Y-m-d H:i:s',strtotime('+3 year')) ;?>">
	                <label for="news-schedule" class="control-label">Tanggal Kadaluarsa</label>
	                
	                <div class="input-group date2">
	                    <input type="text" autocomplete="off" class="form-control" id="news-schedule" placeholder="Schedule" value="<?= date('D, j M Y H:i', (isset($obat['tgl_kadaluarsa']) ? $obat['tgl_kadaluarsa'] : strtotime('+3 year')))?>">
	                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	                </div>
	                <?php if (isset($errors['tgl_kadaluarsa'])) {?>
	                    <small class="help-block"><i class="fa fa-times-circle-o"></i> <?php echo $errors['tgl_kadaluarsa'][0]?></small>
	                <?php }?>

	                <small class="help-block" style="<?php echo (isset($errors['tgl_kadaluarsa'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['tgl_kadaluarsa'])) ? $errors['tgl_kadaluarsa'][0] : '' ;?></small>
	            </div>

                 <div class="form-group" style="margin-top: 40px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$obat['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>

			</div>
			<div class="col-xs-6 col-md-6">
				<div class="form-group <?php echo isset($errors['harga_satuan']) ? 'has-error' : '' ; ?>">
					<label for="harga_satuan" class="control-label">Harga Jual Satuan</label>
					<div>
					   <input data-currency="harga_satuan" type="text" class="form-control currency" value="<?=isset($obat['harga_satuan']) ? $obat['harga_satuan']: ""?>" placeholder="Harga Jual Satuan">
                       <input id="harga_satuan" type="hidden" class="form-control" name="harga_satuan" value="<?=isset($obat['harga_satuan']) ? $obat['harga_satuan']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_satuan'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_satuan'])) ? $errors['harga_satuan'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['harga_resep']) ? 'has-error' : '' ; ?>">
					<label for="harga_resep" class="control-label">Harga Jual Resep</label>
					<div>
					   <input type="text" data-currency="harga_resep" class="form-control currency" value="<?=isset($obat['harga_resep']) ? $obat['harga_resep']: ""?>" placeholder="Harga Jual Resep">
					   <input id="harga_resep" type="hidden" class="form-control" name="harga_resep" value="<?=isset($obat['harga_resep']) ? $obat['harga_resep']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_resep'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_resep'])) ? $errors['harga_resep'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['harga_grosir']) ? 'has-error' : '' ; ?>">
					<label for="harga_grosir" class="control-label">Harga Jual Grosir</label>
					<div>
					   <input type="text" data-currency="harga_grosir" class="form-control currency" value="<?=isset($obat['harga_grosir']) ? $obat['harga_grosir']: ""?>" placeholder="Harga Jual Grosir">
					   <input id="harga_grosir" type="hidden" class="form-control" name="harga_grosir" value="<?=isset($obat['harga_grosir']) ? $obat['harga_grosir']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_grosir'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_grosir'])) ? $errors['harga_grosir'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['stok']) ? 'has-error' : '' ; ?>">
					<label for="stok" class="control-label">Stok Obat</label>
					<div>
					   <input type="number" name="stok" id="stok" class="form-control" value="<?=isset($obat['stok']) ? $obat['stok']: ""?>" placeholder="Stok Obat">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['stok'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['stok'])) ? $errors['stok'][0] : '' ;?></small>
				</div>
	            
            </div>
            
        </form>
    </div>
</div>
