<?php if($check_lists) { ?>
    <?php foreach($check_lists as $check_list) { ?>
    <div class="check-list" data-check-list-id="<?php echo $check_list['check_list_id']; ?>">
        <div class="check-list__header">
            <div class="check-list__name">
                <input type="text" name="check_list[<?php echo $check_list['check_list_id']; ?>][name]" value="<?php echo $check_list['name']; ?>" save-on-change />
            </div>

            <button class="btn btn-fill-danger" type="button" onclick="deleteItem('check_list', <?php echo $check_list['check_list_id']; ?>);">Удалить</button>
        </div>
        <div class="check-list__items sortable-check-list-items">
            <?php if($check_list['items']) { ?>
            <?php foreach($check_list['items'] as $check_list_item) { ?>
            <div class="check-list__item" data-check-list-item-id="<?php echo $check_list_item['check_list_item_id']; ?>">
                <div class="check-list__inputs">
                    <label class="custom-checkbox <?php echo $check_list_item['checked'] ? ' custom-checkbox--checked ' : ''; ?>">
                        <input type="checkbox" name="check_list_item[<?php echo $check_list_item['check_list_item_id']; ?>][checked]" <?php echo $check_list_item['checked'] ? ' checked ' : ''; ?> value="1" save-on-change />
                        <span></span>
                    </label>
                    <div class="check-list__item-name">
                        <div class="custom-textarea">
                            <textarea class="custom-textarea__field" name="check_list_item[<?php echo $check_list_item['check_list_item_id']; ?>][name]" save-on-change placeholder="Новый элемент"><?php echo $check_list_item['name']; ?></textarea>
                            <p class="custom-textarea__preview"><?php echo $check_list_item['name'] != '' ? $check_list_item['name'] : 'Новый элемент' ?></p>
                        </div>
                    </div>
                </div>
                <button class="btn btn-danger" type="button" onclick="deleteItem('check_list_item', <?php echo $check_list_item['check_list_item_id']; ?>);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <?php } ?>
            <?php } ?>
        </div>

        <button class="btn btn-fill" type="button" onclick="addItem('check_list_item', <?php echo $check_list['check_list_id']; ?>, 'name', '');">Добавить элемент</button>
    </div>
    <?php } ?>
<?php } ?>