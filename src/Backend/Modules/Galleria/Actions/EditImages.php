<?php
namespace Backend\Modules\Galleria\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\Form as BackendForm;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Meta as BackendMeta;
use Backend\Modules\Galleria\Engine\Model as BackendGalleriaModel;

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author Wouter Verstuyf <wouter@webflow.be>
 */
class EditImages extends BackendBaseActionEdit
{

  /**
   * The album images
   *
   * @var array
   */
  private $images = array();

  /*
   * Form add image
   */
  private $frmAddImage;

  /*
   * Form delete image
   */
  private $frmDeleteImage;


  /**
   * Execute the action
   *
   * @return void
   */
  public function execute()
  {
    // get parameters
    $this->id = $this->getParameter('id', 'int');

    // does the item exists?
    if($this->id !== null && BackendGalleriaModel::existsAlbum($this->id))
    {
      // call parent, this will probably add some general CSS/JS or other required files
      parent::execute();

      // get all data for the item we want to edit
      $this->getData();

      // load the form
      $this->loadForm();

      // validate the form
      $this->validateForm();

      // parse the form
      $this->parse();

      // display the page
      $this->display();
    }

    // no item found, throw an exceptions, because somebody is fucking with our URL
    else $this->redirect(BackendModel::createURLForAction('albums') . '&error=non-existing');
  }

  /**
   * Get the data
   *
   * @return void
   */
  private function getData()
  {
    // get the album
    $this->record = BackendGalleriaModel::getAlbumFromId($this->id);

    // get the album images
    $this->images = BackendGalleriaModel::getImagesForAlbum($this->id);
  }

  /**
   * Load the form
   *
   * @return void
   */
  private function loadForm()
  {
    // create add image form
    $this->frmAddImage = new BackendForm('add_image');

    // create delete image form
    $this->frmDeleteImage = new BackendForm('delete_image');

    // Add file upload to the add_image form
    $this->frmAddImage->addImage('images');

    if(!empty($this->images))
    {
      // Add delete field to the image
      foreach($this->images as &$image)
      {
        // Create the checkbox and add to the delete_image form
        $chkDelete = $this->frmDeleteImage->addCheckbox("delete_" . $image["id"]);
        $txtDescription = $this->frmDeleteImage->addTextarea("description_" . $image["id"], $image['description']);

        // Add the parsed data to the array
        $image["field_delete"] = $chkDelete->parse();
        $image['field_description'] = $txtDescription->parse();
      }

      // Destroy the last $image (because of the reference) -- sugested by http://php.net/manual/en/control-structures.foreach.php
      unset($image);
    }
  }

  /**
   * Parse the form
   *
   * @return void
   */
  protected function parse()
  {

    // Add javascript file
    $this->header->addJS('Jquery.uploadify.min.js', null,false);
    $this->header->addJS('Edit.js', null,false);
    $this->header->addCSS('Uploadify.css');

    // call parent
    parent::parse();

    // assign the category
    $this->tpl->assign('album', $this->record);
    $this->tpl->assign('images', $this->images);

    if($this->frmAddImage) $this->frmAddImage->parse($this->tpl);

    if($this->frmDeleteImage) $this->frmDeleteImage->parse($this->tpl);

    //--Add data to Javascript
    $this->header->addJsData("Galleria", "id", $this->id);

    // can the category be deleted?
    if(BackendGalleriaModel::deleteAlbumAllowed($this->id)) $this->tpl->assign('showDelete', true);

    // get url
    $url = BackendModel::getURLForBlock($this->URL->getModule(), 'group');
    $url404 = BackendModel::getURL(404);
    if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
  }

  /**
   * Validate the form add image
   *
   * @return void
   */
  private function validateForm()
  {

    //--Check if the add-image form is submitted
    if($this->frmAddImage->isSubmitted())
    {
      //--Clean up fields in the form
      $this->frmAddImage->cleanupFields();

      //--Get image field
      $filImage = $this->frmAddImage->getField('images');

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
          $strFilename = BackendGalleriaModel::checkFilename(substr($filImage->getFilename(), 0, 0 - (strlen($filImage->getExtension()) + 1)), $filImage->getExtension());

          //--Fill in the item
          $item = array();
          $item["album_id"] = (int)$this->id;
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

          //--image provided?
          if($filImage->isFilled())
          {
            //--upload the image & generate thumbnails
            $filImage->generateThumbnails($imagePath, $item["filename"]);
          }

          //--Add item to the database
          BackendGalleriaModel::insert($item);

          //--Redirect
          $this->redirect(BackendModel::createURLForAction('edit_images') . '&id=' . $item["album_id"] . '&report=added-image&var=' . urlencode($item["filename"]));
        }
      }
    }

    //--Check if the delete-image form is submitted
    if($this->frmDeleteImage->isSubmitted())
    {
      //--Clean up fields in the form
      $this->frmDeleteImage->cleanupFields();

      //--Check if the image-array is not empty.
      if(!empty($this->images))
      {
        //--Loop the images
        foreach($this->images as $row)
        {
          //--Check if the delete parameter is filled in.
          if(\SpoonFilter::getPostValue("delete_" . $row["id"], null, "") == "Y")
          {
            //--Delete the image
            BackendGalleriaModel::delete($row["id"]);
          }

          //--Update item
          $item['id'] = $row['id'];
          $item['language'] = $row['language'];
          $item['description'] = \SpoonFilter::getPostValue("description_" . $row["id"], null, "");
          BackendGalleriaModel::updateImage($item);
        }

        $this->redirect(BackendModel::createURLForAction('edit_images') . '&id=' . $this->id . '&report=deleted-images');
      }
    }
  }
}
