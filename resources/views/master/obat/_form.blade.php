<div class="box box-success <?= !isset($obat['id']) ? 'collapsed-box' : '' ?>">
    <div class="box-header no-shadow no-padding nav-tabs-custom" style="margin-bottom: 0px; min-height: 45px;">
    	<h3 class="box-title" style="padding:10px;"><?php echo (@$obat['id']) ? 'Update obat' : 'Tambah obat Baru'; ?></h3>
    	<?php if(!isset($obat['id'])): ?>
    	<div class="box-tools">
			<button style="margin-top: -5px;" id="btnAddObat" class='btn btn-xs btn-primary' data-widget='collapse'><i class='fa fa-plus'></i></button> 
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
					<div>
					   <input type="text" name="kode" id="kodeObat" class="form-control" value="<?=isset($obat['kode']) ? $obat['kode']: ""?>" placeholder="Kode Obat">
					</div>

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
				<div class="form-group <?php echo isset($errors['harga_grosir']) ? 'has-error' : '' ; ?>">
					<label for="harga_grosir" class="control-label">Harga Jual Grosir</label>
					<div>
					   <input type="text" data-currency="harga_grosir" class="form-control currency" value="<?=isset($obat['harga_jual_grosir']) ? $obat['harga_jual_grosir']: ""?>" placeholder="Harga Jual Grosir">
					   <input id="harga_grosir" type="hidden" class="form-control" name="harga_grosir" value="<?=isset($obat['harga_jual_grosir']) ? $obat['harga_jual_grosir']: ""?>">
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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
	<!-- <script type="text/javascript" src="js/script.js"></script> -->
<script>
/*
 * jQuery Scanner Detection
 *
 * Copyright (c) 2013 Julien Maurel
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 * https://github.com/julien-maurel/jQuery-Scanner-Detection
 *
 * Version: 1.2.1
 *
 */
(function($){
    $.fn.scannerDetection=function(options){

        // If string given, call onComplete callback
        if(typeof options==="string"){
            this.each(function(){
                this.scannerDetectionTest(options);
            });
            return this;
        }
		
	    // If false (boolean) given, deinitialize plugin
	    if(options === false){
	        this.each(function(){
	    	    this.scannerDetectionOff();
	        });
	        return this;
	    }

        var defaults={
            onComplete:false, // Callback after detection of a successfull scanning (scanned string in parameter)
            onError:false, // Callback after detection of a unsuccessfull scanning (scanned string in parameter)
            onReceive:false, // Callback after receiving and processing a char (scanned char in parameter)
            onKeyDetect:false, // Callback after detecting a keyDown (key char in parameter) - in contrast to onReceive, this fires for non-character keys like tab, arrows, etc. too!
            timeBeforeScanTest:100, // Wait duration (ms) after keypress event to check if scanning is finished
            avgTimeByChar:30, // Average time (ms) between 2 chars. Used to do difference between keyboard typing and scanning
            minLength:6, // Minimum length for a scanning
            endChar:[9,13], // Chars to remove and means end of scanning
	        startChar:[], // Chars to remove and means start of scanning
	        ignoreIfFocusOn:false, // do not handle scans if the currently focused element matches this selector
	        scanButtonKeyCode:false, // Key code of the scanner hardware button (if the scanner button a acts as a key itself) 
	        scanButtonLongPressThreshold:3, // How many times the hardware button should issue a pressed event before a barcode is read to detect a longpress
            onScanButtonLongPressed:false, // Callback after detection of a successfull scan while the scan button was pressed and held down
            stopPropagation:false, // Stop immediate propagation on keypress event
            preventDefault:false // Prevent default action on keypress event
        };
        if(typeof options==="function"){
            options={onComplete:options}
        }
        if(typeof options!=="object"){
            options=$.extend({},defaults);
        }else{
            options=$.extend({},defaults,options);
        }
        
        this.each(function(){
            var self=this, $self=$(self), firstCharTime=0, lastCharTime=0, stringWriting='', callIsScanner=false, testTimer=false, scanButtonCounter=0;
            var initScannerDetection=function(){
                firstCharTime=0;
                stringWriting='';
		        scanButtonCounter=0;
            };
	        self.scannerDetectionOff=function(){
		    $self.unbind('keydown.scannerDetection');
		    $self.unbind('keypress.scannerDetection');
	    }
	    self.isFocusOnIgnoredElement=function(){
            if(!options.ignoreIfFocusOn) return false;
		    if(typeof options.ignoreIfFocusOn === 'string') return $(':focus').is(options.ignoreIfFocusOn);
	        if(typeof options.ignoreIfFocusOn === 'object' && options.ignoreIfFocusOn.length){
		        var focused=$(':focus');
		        for(var i=0; i<options.ignoreIfFocusOn.length; i++){
			        if(focused.is(options.ignoreIfFocusOn[i])){
			            return true;
			        }
		        }
		    }
		    return false;
	    }
        self.scannerDetectionTest=function(s){
            // If string is given, test it
            if(s){
                firstCharTime=lastCharTime=0;
                stringWriting=s;
            }

		    if (!scanButtonCounter){
		        scanButtonCounter = 1;
		    }

			// If all condition are good (length, time...), call the callback and re-initialize the plugin for next scanning
			// Else, just re-initialize
			if(stringWriting.length>=options.minLength && lastCharTime-firstCharTime<stringWriting.length*options.avgTimeByChar){
		        if(options.onScanButtonLongPressed && scanButtonCounter > options.scanButtonLongPressThreshold) options.onScanButtonLongPressed.call(self,stringWriting,scanButtonCounter);
                    else if(options.onComplete) options.onComplete.call(self,stringWriting,scanButtonCounter);
                    $self.trigger('scannerDetectionComplete',{string:stringWriting});
                    initScannerDetection();
                    return true;
                }else{
                    if(options.onError) options.onError.call(self,stringWriting);
                    $self.trigger('scannerDetectionError',{string:stringWriting});
                    initScannerDetection();
                    return false;
                }
            }
            $self.data('scannerDetection',{options:options}).unbind('.scannerDetection').bind('keydown.scannerDetection',function(e){
			    // If it's just the button of the scanner, ignore it and wait for the real input
		        if(options.scanButtonKeyCode !== false && e.which==options.scanButtonKeyCode) {
                    scanButtonCounter++;
                    // Cancel default
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
		        // Add event on keydown because keypress is not triggered for non character keys (tab, up, down...)
                // So need that to check endChar and startChar (that is often tab or enter) and call keypress if necessary
                else if((firstCharTime && options.endChar.indexOf(e.which)!==-1) 
			    || (!firstCharTime && options.startChar.indexOf(e.which)!==-1)){
                    // Clone event, set type and trigger it
                    var e2=jQuery.Event('keypress',e);
                    e2.type='keypress.scannerDetection';
                    $self.triggerHandler(e2);
                    // Cancel default
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
                // Fire keyDetect event in any case!
                if(options.onKeyDetect) options.onKeyDetect.call(self,e);
                $self.trigger('scannerDetectionKeyDetect',{evt:e});
				
            }).bind('keypress.scannerDetection',function(e){
		        if (this.isFocusOnIgnoredElement()) return;
                if(options.stopPropagation) e.stopImmediatePropagation();
                if(options.preventDefault) e.preventDefault();

                if(firstCharTime && options.endChar.indexOf(e.which)!==-1){
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    callIsScanner=true;
                }else if(!firstCharTime && options.startChar.indexOf(e.which)!==-1){
                    e.preventDefault();
                    e.stopImmediatePropagation();
		            callIsScanner=false;
		        }else{
                    if (typeof(e.which) != 'undefined'){
                        stringWriting+=String.fromCharCode(e.which);
                    }
                    callIsScanner=false;
                }

                if(!firstCharTime){
                    firstCharTime=Date.now();
                }
                lastCharTime=Date.now();

                if(testTimer) clearTimeout(testTimer);
                if(callIsScanner){
                    self.scannerDetectionTest();
                    testTimer=false;
                }else{
                    testTimer=setTimeout(self.scannerDetectionTest,options.timeBeforeScanTest);
                }

                if(options.onReceive) options.onReceive.call(self,e);
                $self.trigger('scannerDetectionReceive',{evt:e});
            });
        });
        return this;
    }
})(jQuery);
</script>
<script>
	var click = false;

	$(document).scannerDetection({
		//https://github.com/kabachello/jQuery-Scanner-Detection
	
		timeBeforeScanTest: 200, // wait for the next character for upto 200ms
		avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
		preventDefault: true,
	
		endChar: [13],
		onComplete: function(barcode, qty){

			// callLoader();
			showForm();
			alidScan = true;
			$('#kodeObat').val (barcode);
			cekObatByKode(barcode);
			// endLoader();
		} // main callback function	,
		,
		onError: function(string, qty) {
			// $('#userInput').val ($('#userInput').val()  + string);
		}
		
	});

	$(this).data('clicked', true);

	function showForm() {
		if (!click) {
			click = true;
			$('#btnAddObat').click();
		}
	};

	function cekObatByKode(param) {
		$.ajax({
			url : base_url + 'obat/remote',
			method : 'post',
			data : {
				action : 'getObatByKode',
				kode : param
			},
			headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
			beforeSend : function(){
				callLoader();
			}
		}).always(function(){
			endLoader();
		}).done(function(data){
			if(data !== 'null'){
				data = JSON.parse(data);	
				var url = base_url + 'obat?id	=' + data.id;
				ajaxLoadForm(url, callbackForm);
			}
		}).fail(function(jqXHR, textStatus, errorThrown){
			if (jqXHR.status == 444)
				sessionExpireHandler();
			else
				callNoty('warning');
		});
	}
</script>