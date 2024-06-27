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
<?php if ($isV9) {
    $options = array_map(function ($group) {
        return [
            'id' => $group['id'],
            'label' => $group['label']
        ];
    }, $optionGroups);
    $options = array_column($options, 'label', 'id');
    ?>
    <div data-row="specific-visible">
        <div class="form-group" data-vue="cms">
            <concrete-select
                <?php if (isset($allowMultiple) && $allowMultiple) { ?>:multiple="true"
                name="visibleGroups[]" <?php } else { ?> name="visibleGroups" <?php } ?>
                :options='<?= json_encode($options) ?>'
                <?php if (isset($visibleGroups)) { ?>
                    <?php if (isset($allowMultiple) && $allowMultiple) { ?>:value='<?= json_encode(($visibleGroups)) ?>' <?php } else { ?>value='<?= $visibleGroups[0] ?>'<?php } ?>
                <?php } ?>
            >
            </concrete-select>
        </div>
    </div>

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
