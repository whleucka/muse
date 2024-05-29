<select class="form-select" name="<?=$column?>" title="<?=$title ?? ''?>">
	<option value="NULL">Please select an option</option>
	<?php foreach ($options as $option): ?>
	<option value="<?=$option->value?>" <?=($value == $option->value ? 'selected' : '')?>><?=$option->label?></option>
	<?php endforeach ?>
</select>
