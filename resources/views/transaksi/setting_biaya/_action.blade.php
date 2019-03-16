<?php
switch ($column) {
    case 'action': ?>

		<div class="btn-group btn-action-2">
		    <a title="Edit" href="<?php echo route('setting-biaya', array_merge($param, ['page' => get('page', 1), 'id' => $setting_biaya['id']])) ?>"  class="btn btn-sm btn-default button-edit"><i class="fa fa-pencil"></i></a>
		    
		    <a title="Delete" href="<?php echo route('setting-biaya-delete', ['id' => $setting_biaya['id']]) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
		</div>

<?php

	default:
		break;
 }?>