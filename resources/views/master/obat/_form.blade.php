<div id="box-obat" class="box box-success <?= !isset($obat['id']) ? 'collapsed-box' : '' ?>">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$obat['id']) ? 'Update obat' : 'Tambah obat Baru'; ?></h3>
    	<?php if(!isset($obat['id'])): ?>
    	<div class="box-tools">
			<button style="margin-top: -5px;" id="btnAddObat" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
		</div>
		<div class="pull-right text-center">
			<form style="margin-top: 5px;" id="form_import_obat" action="<?php echo url($route.'/import') ?>" method="post" enctype="multipart/form-data">
				{{ csrf_field() }}
				<div class="col-lg-8">
					<input type="file" style="margin-top: 5px;" required name="file" class="form-input-file"/>
				</div>
				<div class="col-lg-4">
					<button  class="btn btn-success btn-sm"><i class="fa fa-upload"></i> Import</button>
				</div>
			</form>
		</div>
		<?php endif; ?>
	</div>
    <div class="box-body" <?= !isset($obat['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="obat-form" action="<?php echo route('obat', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$obat['id'])?>" />

            <div class="col-xs-6 col-md-6">
				
				<div class="form-group <?php echo isset($errors['kode']) ? 'has-error' : '' ; ?>">
					<label for="kode" class="control-label">Kodes Obat</label>
					<div class="input-group">
						<input type="number" name="kode" id="kodeObat" class="form-control" value="<?=isset($obat['kode']) ? $obat['kode']: ""?>" placeholder="Kode Obat">
						<span id="cek-kode" class="input-group-addon" style="cursor:pointer"><i class="fa fa-check"> Cek kode</i></span>
					</div>

					<small id="kode-exist" class="help-block" style="display:none;"><span class="label label-warning">Kode Sudah Digunakan!</span></small>

					<small class="help-block" style="<?php echo (isset($errors['kode'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['kode'])) ? $errors['kode'][0] : '' ;?></small>

				</div>
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="nama" class="control-label">Nama Obat</label>
					<div>
					   <input type="text" name="nama" id="nama" class="form-control" value="<?=isset($obat['nama']) ? $obat['nama']: ""?>" placeholder="Nama Obat">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['name']) ? 'has-error' : '' ; ?>">
					<label for="kategori" class="control-label">Kategori Obat</label>
					<div>
					   <select class="form-control" name="kategori">
					   		@foreach($kategori as $kat)
                       		<option value="<?= $kat['id'] ?>" <?=@$obat['obat']=='1' ? 'selected' : ''?> ><?= $kat['nama']?></option>
					   		@endforeach
                        </select>
					</div>

					<small class="help-block" style="<?php echo (isset($errors['obat'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['obat'])) ? $errors['obat'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['tgl_kadaluarsa']) ? 'has-error' : '' ; ?>">
						<input type="hidden" name="tgl_kadaluarsa" class="dt-value" value="<?= isset($obat['tgl_kadaluarsa']) ? $obat['tgl_kadaluarsa'] : date('Y-m-d',strtotime('+3 year')) ;?>">
						<label for="tgl_kadaluarsa" class="control-label">Tanggal Kadaluarsa</label>
						
						<div class="input-group date2">
							<input type="text" autocomplete="off" class="form-control" placeholder="Schedule" value="<?= date('D, j M Y', (isset($obat['tgl_kadaluarsa']) ? strtotime($obat['tgl_kadaluarsa']) : strtotime('+3 year')))?>">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
						<?php if (isset($errors['tgl_kadaluarsa'])) {?>
							<small class="help-block"><i class="fa fa-times-circle-o"></i> <?php echo $errors['tgl_kadaluarsa'][0]?></small>
						<?php }?>

						<small class="help-block" style="<?php echo (isset($errors['tgl_kadaluarsa'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['tgl_kadaluarsa'])) ? $errors['tgl_kadaluarsa'][0] : '' ;?></small>
				</div>

				<div class="form-group <?php echo isset($errors['type']) ? 'has-error' : '' ; ?>">
					<label for="type" class="control-label">Status</label>
					<div>
						<select class="form-control" name="type">
							<option value="1" <?=@$obat['type']=='1' ? 'selected' : ''?> >Sendiri</option>
							<option value="2" <?=@$obat['type']=='2' ? 'selected' : ''?> >Konsinyasi</option>
						</select>
					</div>

					<small class="help-block" style="<?php echo (isset($errors['type'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['type'])) ? $errors['type'][0] : '' ;?></small>
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
					   <input data-currency="harga_satuan" type="text" class="form-control currency" value="<?=isset($obat['harga_jual_satuan']) ? $obat['harga_jual_satuan']: ""?>" placeholder="Harga Jual Satuan">
                       <input id="harga_satuan" type="hidden" class="form-control" name="harga_satuan" value="<?=isset($obat['harga_jual_satuan']) ? $obat['harga_jual_satuan']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_satuan'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_satuan'])) ? $errors['harga_satuan'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['harga_resep']) ? 'has-error' : '' ; ?>">
					<label for="harga_resep" class="control-label">Harga Jual Resep</label>
					<div>
					   <input type="text" data-currency="harga_resep" class="form-control currency" value="<?=isset($obat['harga_jual_resep']) ? $obat['harga_jual_resep']: ""?>" placeholder="Harga Jual Resep">
					   <input id="harga_resep" type="hidden" class="form-control" name="harga_resep" value="<?=isset($obat['harga_jual_resep']) ? $obat['harga_jual_resep']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_resep'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_resep'])) ? $errors['harga_resep'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['harga_pack']) ? 'has-error' : '' ; ?>">
					<label for="harga_pack" class="control-label">Harga Jual Pack</label>
					<div>
					   <input type="text" data-currency="harga_pack" class="form-control currency" value="<?=isset($obat['harga_jual_pack']) ? $obat['harga_jual_pack']: ""?>" placeholder="Harga Jual Pack">
					   <input id="harga_pack" type="hidden" class="form-control" name="harga_pack" value="<?=isset($obat['harga_jual_pack']) ? $obat['harga_jual_pack']: ""?>">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['harga_pack'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['harga_pack'])) ? $errors['harga_pack'][0] : '' ;?></small>
				</div>
				<div class="form-group <?php echo isset($errors['satuan']) ? 'has-error' : '' ; ?>">
					<label for="satuan" class="control-label">Satuan</label>
					<div>
						<select class="form-control" name="satuan">
							<option value="tablet" <?=@$obat['satuan']=='tablet' ? 'selected' : ''?> >Tablet</option>
							<option value="kapsul" <?=@$obat['satuan']=='kapsul' ? 'selected' : ''?> >Kapsul</option>
							<option value="botol" <?=@$obat['satuan']=='botol' ? 'selected' : ''?> >Botol</option>
							<option value="kotak" <?=@$obat['satuan']=='kotak' ? 'selected' : ''?> >Kotak</option>
							<option value="ml" <?=@$obat['satuan']=='ml' ? 'selected' : ''?> >ML</option>
							<option value="vial" <?=@$obat['satuan']=='vial' ? 'selected' : ''?> >Vial</option>
							<option value="tube" <?=@$obat['satuan']=='tube' ? 'selected' : ''?> >Tube</option>
							<option value="pot" <?=@$obat['satuan']=='pot' ? 'selected' : ''?> >Pot</option>
							<option value="supp" <?=@$obat['satuan']=='supp' ? 'selected' : ''?> >Supp</option>
							<option value="ampul" <?=@$obat['satuan']=='ampul' ? 'selected' : ''?> >Ampul</option>
						</select>
					</div>

					<small class="help-block" style="<?php echo (isset($errors['satuan'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['satuan'])) ? $errors['satuan'][0] : '' ;?></small>
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