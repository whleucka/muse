<div class="form-check <?=$class ?? ''?>">
<input type="hidden" value="<?=$value ?? 0?>" name="<?=$column?>" />
<input id="<?=$column?>" type="checkbox" class="form-check-input" <?=$checked?> onChange="handleCheck(event)"/>
</div>
