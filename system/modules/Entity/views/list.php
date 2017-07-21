<div class="card box entity-filter">
    <div class="card-header" data-toggle="collapse" data-target=".filter-collapser" style="cursor:pointer;">
        <?=t('filter');?>
    </div>
    <div class="filter-collapser collapse">
        <div class="card-block">
            <?=$listing->filter();?>
        </div>
    </div>
</div>

<div class="card box entity-listing">
    <div class="card-block">
        <?=$listing->table();?>
    </div>
    <div class="card-footer">
        <?=$listing->nav();?>
    </div>
</div>
