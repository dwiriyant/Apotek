<?php
switch ($column) {
    case 'action': ?>

		<div class="btn-group btn-action-3">
		    <a title="Edit" href="<?php echo route('dokter', array_merge($param, ['page' => get('page', 1), 'id' => $dokter['id']])) ?>"  class="btn btn-sm btn-default button-edit"><i class="fa fa-pencil"></i></a>
		    
		    <a title="Delete" href="<?php echo route('dokter-delete', ['id' => $dokter['id']]) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
		</div>

<?php

	default:
		break;
 }?>