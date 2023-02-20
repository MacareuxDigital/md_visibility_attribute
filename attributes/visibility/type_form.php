<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
$availableGroups = $availableGroups ?? [];
$optionGroups = $optionGroups ?? [];
$allowMultiple = $allowMultiple ?? false;
?>
<fieldset>
    <legend><?php echo t('Visibility Attribute Options')?></legend>
    <div class="form-group">
        <?= $form->label('optionGroups', t('Selectable Groups'))?>
        <?= $form->selectMultiple('optionGroups', $availableGroups, $optionGroups) ?>
    </div>
    <div class="form-group">
        <?= $form->label('allowMultiple', t('Allow Multiple Select'))?>
        <?= $form->checkbox('allowMultiple', 1, $allowMultiple) ?>
    </div>
</fieldset>