<?php

namespace Backend\Modules\Galleria\Ajax;


/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */


use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Core\Engine\Language as BL;
use Backend\Modules\Galleria\Engine\Model as BackendGalleriaModel;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Authentication as BackendAuthentication;

/**
 * This is an ajax handler
 *
 * @author Waldo Cosman <waldo@comsa.be>
 */
class Uploadify extends BackendBaseAJAXAction
{
	/**
	 * @var $frm
	 */
	private $frmAddImage;

	/**
	 * @var $id
	 */
	private $id;

	/**
	 * Execute the action
	 */
	public function execute()
	{

		parent::execute();

		//--Set post var to check submit
		$_POST["form"] = "add_image";

		// get parameters
		$this->id = \SpoonFilter::getPostValue('id', null, '', 'int');


		//--Load form
		$this->loadForm();

		//--Validate form
		$this->validateForm();


		// output
		$this->output(self::OK, null, BL::msg('Success'));
	}

	private function loadForm()
	{
		//--Create form instance
		$this->frm = new BackendForm('add_image');

		//--Add file upload to the add_image form
		$this->frm->addImage('images');
	}

	/**
	 * Validate the form add image
	 *
	 * @return void
	 */
	private function validateForm()
	{
		//--Check if the add-image form is submitted
		if($this->frm->isSubmitted())
		{

			//--Clean up fields in the form
			$this->frm->cleanupFields();

			//--Get image field
			$filImage = $this->frm->getField('images');

			//--Check if the field is filled in
			if($filImage->isFilled())
			{
				//--Image extension and mime type
				$filImage->isAllowedExtension(array('jpg', 'png', 'gif', 'jpeg'), BL::err('JPGGIFAndPNGOnly'));
				$filImage->isAllowedMimeType(array('image/jpg', 'image/png', 'image/gif', 'image/jpeg'), BL::err('JPGGIFAndPNGOnly'));

				//--Check if there are no errors.
				$strError = $filImage->getErrors();


				if($strError === null)
				{

					//--Get the filename
					$strFilename = BackendGalleriaModel::checkFilename(substr($filImage->getFilename(), 0, 0 - (strlen($filImage->getExtension())+1)), $filImage->getExtension());

					//--Fill in the item
					$item = array();
					$item["album_id"] = (int) $this->id;
					$item["user_id"] = BackendAuthentication::getUser()->getUserId();
					$item["language"] = BL::getWorkingLanguage();
					$item["filename"] = $strFilename;
					$item["description"] = "";
					$item["publish_on"] = BackendModel::getUTCDate();
					$item["hidden"] = "N";
					$item["sequence"] = BackendGalleriaModel::getMaximumImageSequence($this->id) + 1;
					//--the image path
					$imagePath = FRONTEND_FILES_PATH . '/Galleria/Images';

					//--create folders if needed
					if(!\SpoonDirectory::exists($imagePath . '/Source')) \SpoonDirectory::create($imagePath . '/Source');
					if(!\SpoonDirectory::exists($imagePath . '/128x128')) \SpoonDirectory::create($imagePath . '/128x128');
					if(!\SpoonDirectory::exists($imagePath . '/800x')) \SpoonDirectory::create($imagePath . '/800x');


					//--image provided?
					if($filImage->isFilled())
					{
						//--upload the image & generate thumbnails
						$filImage->generateThumbnails($imagePath, $item["filename"]);
					}

					//--Add item to the database
					BackendGalleriaModel::insert($item);
				}
			}
		}
	}
}
