<?php
defined('C5_EXECUTE') or die('Access Denied.');
/** @var \Concrete\Core\Form\Service\Form $form */
/** @var \Concrete\Core\User\Group\Group[] $visibleGroups */
$visibleGroups = $visibleGroups ?? [];
/** @var \Concrete\Core\User\Group\Group[] $optionGroups */
$optionGroups = $optionGroups ?? [];
$allowMultiple = $allowMultiple ?? false;
$akID = $akID ?? null;
?>
<div class="form-group">
    <?php
    echo $form->hidden(
        $view->field('visibleGroups'),
        implode(',', $visibleGroups),
        ['data-select-and-add' => $akID, 'style' => 'width:100%']
    );
    ?>
    <script type="text/javascript">
        $(function () {
            $('input[data-select-and-add=<?=$akID?>]').selectize({
                plugins: ['remove_button'],
                valueField: 'id',
                labelField: 'label',
                searchField: 'text',
                options: <?=json_encode($optionGroups)?>,
                items: <?=json_encode($visibleGroups)?>,
                openOnFocus: false,
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
</div>
