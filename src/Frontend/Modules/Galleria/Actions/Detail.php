<?php
namespace Frontend\Modules\Galleria\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Modules\Galleria\Engine\Model as FrontendGalleriaModel;

/**
 * This is the Detail-action, it will display the overview of galleria posts
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Detail extends FrontendBaseBlock
{
	/**
	 * The record data
	 *
	 * @var array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->loadData();

		//--Add css
		$this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Galleria.css');
        $this->header->addCSS('/src/Frontend/Modules/' . $this->getModule() . '/Layout/Css/Colorbox.css');

		//--Add javascript
		$this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.colorbox-min.js');
        $this->header->addJS('/src/Frontend/Modules/' . $this->getModule() . '/Js/Jquery.cycle.all.js');

		$this->parse();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{

		//--Check the params
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		//--Get record
		$this->record = FrontendGalleriaModel::getAlbum($this->URL->getParameter(1));

		//--Redirect if empty
		if(empty($this->record))
		{
			$this->redirect(FrontendNavigation::getURL(404));
		}
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{
		$this->tpl->assign('item', $this->record);
	}
}
