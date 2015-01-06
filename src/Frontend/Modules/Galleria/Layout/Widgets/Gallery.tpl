{*
	variables that are available:
	- {$widgetGallery}: contains all the data for this widget
*}

{option:widgetGallery}
    <ul class="list-unstyled row galleria-gallery">
    {iteration:widgetGallery}
        <li class="col-xs-3">
            <a href="{$widgetGallery.image_800x}" title="{$widgetGallery.description}" class="colorbox"><img src="{$widgetGallery.image_128x128}" alt="{$widgetGallery.description} {$widgetGallery.filename}" title="{$widgetGallery.description} {$widgetGallery.filename}" class="img-responsive"></a>
            <span>{$widgetGallery.description}</span>
        </li>
    {/iteration:widgetGallery}
    </ul>
    <div class="clearfix">&nbsp;</div>
{/option:widgetGallery}
