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
<?php if ($isV9) { ?>
    <select id="visible-select2-<?=$akID?>" name="<?=$view->field('visibleGroups')?>[]" class="form-control" <?= $allowMultiple ? 'multiple' : ''; ?> style="display:none;">
        <?php foreach ($optionGroups as $group): ?>
            <option value="<?= $group['id'] ?>" <?= in_array($group['id'], $visibleGroups) ? 'selected' : ''; ?>><?= $group['label'] ?></option>
        <?php endforeach; ?>
    </select>
    <script>
        $(document).ready(function () {
            var selectElement = $('#visible-select2-<?=$akID?>');

            selectElement.select2({
                data: <?=json_encode($optionGroups)?>.map(function(item) {
                    return { id: item.id, text: item.label };
                }),
                tags: true,
                tokenSeparators: [','],
                createTag: function (params) {
                    return params.term.length >= 1 ? { id: params.term, text: params.term } : null;
                }
            });
        });
    </script>
<?php }?>
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
