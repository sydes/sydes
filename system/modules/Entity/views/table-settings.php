<div class="row column-sorter-wrapper">
    <div class="col-6">
        <h6><?=t('all_columns');?></h6>
        <ul class="column-sorter all">
<?php foreach ($all as $key => $title) {
    if (in_array($key, $selected)) { continue; } ?>
            <li><input type="hidden" name="select[]" value="<?=$key;?>"><?=$title;?></li>
<?php } ?>
        </ul>
    </div>
    <form class="col-6 ajaxed" id="form-column-sorter" action="<?=$path;?>" method="post">
        <h6><?=t('selected_columns');?></h6>
        <ul class="column-sorter selected">
<?php foreach ($selected as $key) { ?>
            <li><input type="hidden" name="select[]" value="<?=$key;?>"><?=$all[$key];?></li>
<?php } ?>
        </ul>
    </form>
</div>
