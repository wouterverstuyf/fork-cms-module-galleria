<?php

namespace Backend\Modules\Galleria\Ajax;

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\Galleria\Engine\Model as BackendGalleriaModel;

/**
 * Reorder categories
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class AlbumSequence extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     *
     * @return  void
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $newIdSequence = trim(\SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array) explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            // build item
            $item['id'] = (int) $id;

            // change sequence
            $item['sequence'] = $i + 1;

            // update sequence
            BackendGalleriaModel::updateAlbumSequence($item);
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
