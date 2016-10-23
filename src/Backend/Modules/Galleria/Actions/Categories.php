<?php
namespace Backend\Modules\Galleria\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Modules\Galleria\Engine\Model as BackendGalleriaModel;

/**
 * This is the Categories action
 *
 * @author John Poelman <john.poelman@bloobz.be>
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Categories extends BackendBaseAction
{
	/**
	 * Execute the action
	 *
	 * @return void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Loads the datagrid
	 *
	 * @return void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->dataGrid = new BackendDataGridDB(BackendGalleriaModel::QRY_DATAGRID_CAT, BL::getWorkingLanguage());

		// disable paging
		$this->dataGrid->setPaging(false);

		// set hidden columns
		$this->dataGrid->setColumnsHidden(array('language','sequence','meta_id','id','publish_on'));

		// set column URLs
		$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit_category') . '&amp;id=[id]');

		// add edit column
		$this->dataGrid->addColumn('add', null, BL::lbl('Add'), BackendModel::createURLForAction('add_album') . '&amp;id=[id]');
		$this->dataGrid->setHeaderLabels(array('add' => \SpoonFilter::ucfirst(BL::lbl('AddAlbum'))));
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]');
		$this->dataGrid->setHeaderLabels(array('edit' => \SpoonFilter::ucfirst(BL::lbl('EditCategory'))));
	}

	/**
	 * Parse & display the page
	 *
	 * @return void
	 */
	protected function parse()
	{
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
