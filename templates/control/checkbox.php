<input type="hidden" value="<?=$value ?? 0?>" name="<?=$column?>" />
<input id="<?=$column?>" type="checkbox" <?=$checked?> onChange="handleCheck(event)"/>
