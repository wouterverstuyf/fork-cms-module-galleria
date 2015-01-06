<?php
namespace Backend\Modules\Galleria\Ajax;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Reorder albums
 *
 * @author Lester Lievens <lester.lievens@netlash.com>
 * @author John Poelman <john.poelman@bloobz.be>
 * @author Waldo Cosman <waldo@comsa.be>
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Galleria\Engine\Model as BackendGalleriaModel;
class AlbumSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		
		// get parameters
		$newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;

			// change sequence
			$item['sequence'] = $i + 1;

			// update sequence
			if(BackendGalleriaModel::existsAlbum($item['id'])) BackendGalleriaModel::updateAlbum($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
