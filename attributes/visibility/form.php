<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\User\Group\Group[] $visibleGroups */
/* @var Concrete\Core\Attribute\View $view */
$visibleGroups = $visibleGroups ?? [];
/** @var \Concrete\Core\User\Group\Group[] $optionGroups */
$optionGroups = $optionGroups ?? [];
$allowMultiple = $allowMultiple ?? false;
$akID = $akID ?? null;
$isV9 = version_compare(app('config')->get('concrete.version'), '9.0.0', '>=');
?>
<div class="form-group">
<?php if ($isV9 && $allowMultiple) {
    foreach ($optionGroups as $option) {
        /** @var \Concrete\Core\User\Group\Group $option */ ?>
            <div class="form-check">
                <?=$form->checkbox($view->field('visibleGroups') . '[]', $option['id'], in_array($option['id'], $visibleGroups)); ?>
                <label class="form-check-label" for="<?= $view->field('atSelectOptionValue') . '_' . $option['id']; ?>">
                    <?=$option['label']?>
                </label>
            </div>
        <?php
    }
} ?>

    <?php if ($isV9 && !$allowMultiple) {
    $options = ['' => t('** None')];
    foreach ($optionGroups as $opt) {
        /** @var \Concrete\Core\User\Group\Group $opt */
        $options[$opt['id']] = $opt['label'];
    }
    echo $form->select($view->field('visibleGroups'), $options, empty($visibleGroups) ? '' : $visibleGroups[0]);
}?>
<?php if (!$isV9) {
    echo $form->hidden(
        $view->field('visibleGroups'),
        implode(',', $visibleGroups),
        ['data-select-and-add' => $akID, 'style' => 'width:100%']
    ); ?>
        <script>
            $(function () {
                $('input[data-select-and-add=<?=$akID?>]').selectize({
                    plugins: ['remove_button'],
                    valueField: 'id',
                    labelField: 'label',
                    searchField: 'label',
                    options: <?=json_encode($optionGroups)?>,
                    items: <?=json_encode($visibleGroups)?>,
                    openOnFocus: true,
                    create: false,
                    createFilter: function (input) {
                        return input.length >= 1;
                    },
                    <?php if ($allowMultiple) { ?>
                    delimiter: ',',
                    maxItems: 500,
                    <?php } else { ?>
                    maxItems: 1,
                    <?php } ?>
                });
            });
        </script>
    <?php
} ?>
</div>
