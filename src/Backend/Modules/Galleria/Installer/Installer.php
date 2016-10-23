<?php
namespace Backend\Modules\Galleria\Installer;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
use Backend\Core\Installer\ModuleInstaller;

/**
 * Installer for the galleria module
 *
 * @author John Poelman <john.poelman@bloobz.be>
 */
class Installer extends ModuleInstaller
{
	public function install()
	{
		// import the sql
		$this->importSQL(dirname(__FILE__) . '/Data/Install.sql');

		// install the module in the database
		$this->addModule('Galleria');

		// install the locale, this is set here beceause we need the module for this
		$this->importLocale(dirname(__FILE__) . '/Data/Locale.xml');

		// modulerights
		$this->setModuleRights(1, 'galleria');

		// actionrights
		$this->setActionRights(1, 'Galleria', 'albums');
		$this->setActionRights(1, 'Galleria', 'add_album');
		$this->setActionRights(1, 'Galleria', 'edit_album');
		$this->setActionRights(1, 'Galleria', 'edit_images');
		$this->setActionRights(1, 'Galleria', 'delete_album');
		$this->setActionRights(1, 'Galleria', 'categories');
		$this->setActionRights(1, 'Galleria', 'add_category');
		$this->setActionRights(1, 'Galleria', 'edit_category');
		$this->setActionRights(1, 'Galleria', 'delete_category');
		$this->setActionRights(1, 'Galleria', 'add');
		$this->setActionRights(1, 'Galleria', 'edit');
		$this->setActionRights(1, 'Galleria', 'delete');
		$this->setActionRights(1, 'Galleria', 'settings');

		// add extra's
		$GalleriaID = $this->insertExtra('Galleria', 'block', 'Galleria', null, null, 'N', 1000);

		// module navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationGalleriaId = $this->setNavigation($navigationModulesId, 'Galleria', 'galleria/albums');

		$this->setNavigation($navigationGalleriaId, 'Albums', 'galleria/albums', array(
				'galleria/add_album',
				'galleria/edit_album',
				'galleria/edit_images',
				'galleria/delete_album',
				'galleria/add',
				'galleria/edit',
				'galleria/delete'
		));

		$this->setNavigation($navigationGalleriaId, 'Categories', 'galleria/categories', array(
				'galleria/add_category',
				'galleria/edit_category',
				'galleria/delete_category'
		));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Galleria', 'galleria/settings');

		// loop languages
		foreach($this->getLanguages() as $language)
		{
			// check if a page for galleria already exists in this language
			// @todo refactor this if statement
			if((int) $this->getDB()->getVar('SELECT COUNT(id)
					FROM pages AS p
					INNER JOIN pages_blocks AS b ON b.revision_id = p.revision_id
					WHERE b.extra_id = ? AND p.language = ?',
					array($GalleriaID, $language)) == 0)
			{
				// insert galleria page
				$this->insertPage(
						array(
								'title' => 'Galleria',
								'type' => 'root',
								'language' => $language
						),
						null,
						array('extra_id' => $GalleriaID, 'position' => 'main'));
			}
		}
	}
}
