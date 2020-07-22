<div class="box box-success <?= !isset($stok_minimal['id']) ? 'collapsed-box' : '' ?>">
    
    <div class="box-body" <?= !isset($stok_minimal['id']) ? 'style="display: none;"' : '' ?>>
        <form method="post" id="stok_minimal-form" action="<?php echo route('stok_minimal', $param)?>" enctype="multipart/form-data">
        	{{ csrf_field() }}
            <input type="hidden" name="id" id="form-id" value="<?php echo htmlEncode(@$stok_minimal['id'])?>" />

            <div class="col-xs-6 col-md-6">
	           	
				<div class="form-group <?php echo isset($errors['nama']) ? 'has-error' : '' ; ?>">
					<label for="stok" class="control-label">Stok Minimal</label>
					<div>
					   <input type="text" name="stok" id="stok" class="form-control" value="<?=isset($stok_minimal['value']) ? $stok_minimal['value']: ""?>" placeholder="Jumlah Minimal">
					</div>

					<small class="help-block" style="<?php echo (isset($errors['nama'])) ? '' : 'display:none;' ?>"><i class="fa fa-times-circle-o"></i> <?php  echo (isset($errors['nama'])) ? $errors['nama'][0] : '' ;?></small>

				</div>

			</div>
			<div class="col-xs-6 col-md-6">
				<div class="form-group" style="margin-top: 25px;">
	                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo @$stok_minimal['id'] ? 'Save Changes' : 'Save New'; ?></button>
	                <a href="<?php echo route($route, $param)?>" class="btn btn-default button-reset">Reset</a>
	            </div>
	            
            </div>
            
        </form>
    </div>
</div>
