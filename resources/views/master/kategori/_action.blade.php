<?php
switch ($column) {
    case 'action': ?>

		<div class="btn-group btn-action-2">
		    <a title="Edit" href="<?php echo route('obat', array_merge($param, ['page' => get('page', 1), 'id' => $kategori['id']])) ?>"  class="btn btn-sm btn-default"><i class="fa fa-pencil"></i></a>
		    
		    <a title="Delete" href="<?php echo route('kategori-delete', ['id' => $kategori['id']]) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
		</div>

<?php

	default:
		break;
 }?>