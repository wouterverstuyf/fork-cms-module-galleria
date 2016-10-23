<?php

namespace Frontend\Modules\Galleria\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the galleria module
 *
 * @author John Poelman <john.poelman@bloobz.be>
 * @author Waldo Cosman <waldo@comsa.be>
 */

use Frontend\Core\Engine\Language AS FL;
use Frontend\Core\Engine\Model as FrontendModel;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Core\Engine\Url AS FrontendURL;
use Frontend\Modules\Tags\Engine\Model as FrontendTagsModel;
use Frontend\Modules\Tags\Engine\TagsInterface as FrontendTagsInterface;

class Model
{

	public static function getAlbumsForOverview()
	{
		$return = (array)FrontendModel::getContainer()->get('database')->getRecords('	SELECT i.*, m.url, m.data AS meta_data
		 														FROM galleria_albums AS i
																	INNER JOIN meta AS m ON m.id = i.meta_id
																WHERE i.language = ? AND show_in_overview = ?
																ORDER BY sequence ASC', array(FRONTEND_LANGUAGE, 'Y'));
		if(!empty($return))
		{
			//--Get link for the categories
			$albumLink = FrontendNavigation::getURLForBlock('Galleria', 'Detail');
			foreach($return as &$row)
			{

				//--Create url
				$row['full_url'] = $albumLink . '/' . $row['url'];

				//-- Unserialize
				if(isset($row['meta_data'])) $row['meta_data'] = @unserialize($row['meta_data']);

				$image = self::getImagesForAlbum($row['id'], 1);

				if(!empty($image))
				{
					foreach($image as $rowImage)
					{
						$row['image'] = $rowImage;
					}
				}
			}
		}

		return $return;
	}

	public static function getAlbum($url)
	{
		$return = (array)FrontendModel::getContainer()->get('database')->getRecord('	SELECT i.*, m.url, m.data AS meta_data
		 														FROM galleria_albums AS i
																	INNER JOIN meta AS m ON m.id = i.meta_id
																WHERE i.language = ? AND m.url = ?
																ORDER BY sequence ASC', array(FRONTEND_LANGUAGE, $url));
		if(!empty($return))
		{

			//-- Unserialize
			if(isset($return['meta_data'])) $return['meta_data'] = @unserialize($return['meta_data']);

			$return['images'] = self::getImagesForAlbum($return['id']);
		}

		return $return;
	}

	/**
	 * Get the images for an album
	 *
	 * @param int $id
	 *
	 * @return bool
	 */
	public static function getImagesForAlbum($id, $limit = 0, $random = false)
	{
		if($random == true)
		{
			$orderBy = "RAND()";
		}
		else
		{
			$orderBy = "sequence";
		}
		if($limit > 0)
		{
			$records = (array)FrontendModel::getContainer()->get('database')->getRecords('SELECT i.*
			 FROM galleria_images AS i
			 WHERE i.language = ? AND i.album_id = ?
			 ORDER BY '  . $orderBy . '
			 LIMIT ?', array(FRONTEND_LANGUAGE, (int)$id, $limit));
		}
		else
		{
			$records = (array)FrontendModel::getContainer()->get('database')->getRecords('SELECT i.*
			 FROM galleria_images AS i
			 WHERE i.language = ? AND i.album_id = ?
			 ORDER BY ' . $orderBy, array(FRONTEND_LANGUAGE, (int)$id));
		}

		//--Loop records
		if(!empty($records))
		{
			//--Get the thumbnail-folders
			$folders = FrontendModel::getThumbnailFolders(FRONTEND_FILES_PATH . '/Galleria/Images', true);


			//--Create the image-links to the thumbnail folders
			foreach($records as &$row)
			{
				foreach($folders as $folder)
				{
					$row['image_' . $folder['dirname']] = $folder['url'] . '/' . $folder['dirname'] . '/' . $row['filename'];
				}
			}
			//--Destroy the last $image (because of the reference) -- sugested by http://php.net/manual/en/control-structures.foreach.php
			unset($row);
		}

		return $records;
	}
}
