{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblGalleria}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblGeneralSettings|ucfirst}</h3>
		</div>
		<div class="options">

		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblPagination|ucfirst}</h3>
		</div>
		<div class="options">

		</div>
	</div>
	
	<div class="box">
		<div class="heading">
			<h3>{$lblComments|ucfirst}</h3>
		</div>
		<div class="options">
		</div>
	</div>
	<div class="box">
		<div class="heading">
			<h3>{$lblNotifications|ucfirst}</h3>
		</div>
		<div class="options">
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSEO}</h3>
		</div>
		<div class="options">
		</div>
	</div>

	<div class="box">
		<div class="horizontal"></div>
	</div>
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}