{*
	variables that are available:
	- {$widgetSlideshow}: contains all the data for this widget
*}
{option:widgetSlideshow}
<ul class="gallery list-unstyled" data-cycle-fx="fade" data-cycle-slides="li" data-cycle-timeout="5000">
    {iteration:widgetSlideshow}
        <li>
            <img src="{$widgetSlideshow.image_270x270}" alt="{$widgetSlideshow.filename}" title="{$widgetSlideshow.filename}">
        </li>
    {/iteration:widgetSlideshow}
</ul>
{/option:widgetSlideshow}