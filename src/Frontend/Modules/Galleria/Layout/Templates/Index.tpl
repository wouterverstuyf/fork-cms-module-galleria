{option:items}
    <ul class="list-unstyled">
        {iteration:items}
            <li>
                <h3><a href="{$items.full_url}" title="{$items.title}">{$items.title}</a></h3>

                <div class="row">
                    <div class="col-sm-2">
                        {option:items.image}
                            <a href="{$items.full_url}" title="{$items.title}">
                                <img src="{$items.image.image_800x}" alt="{$items.title}" title="{$items.title}" class="img-responsive"/>
                            </a>
                        {/option:items.image}

                    </div>
                    <!-- /.col-sm-2 -->
                    <div class="col-sm-9">
                        {$items.description}
                        <a href="{$items.full_url}" title="{$items.title}">
                            {$lblViewMorePhotos}
                        </a>
                    </div>
                    <!-- /.col-sm-9 -->
                </div>
            </li>
        {/iteration:items}
    </ul>
{/option:items}