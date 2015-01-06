{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="pageTitle">
	<h2>{$lblGalleria|ucfirst}: {$lblAlbums|ucfirst}</h2>
	{option:showGalleriaAddAlbum}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_album'}" class="button icon iconAdd"><span>{$lblAddAlbum|ucfirst}</span></a>
	</div>
	{/option:showGalleriaAddAlbum}
</div>
{option:dataGrid}
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}
{option:!dataGrid}{$msgNoAlbums|ucfirst}{/option:!dataGrid}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}