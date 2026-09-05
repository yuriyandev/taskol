<div class="modal" id="listForm">
    <div class="modal-header">
        <?php echo $text_list_add; ?>
    </div>
    <div class="modal-body">
        <div class="list-form" id="listForm">
            <div class="form-param">
                <label>
                <p><?php echo $text_name; ?></p>
                <input type="text" value="<?php echo isset($name) ? $name : ''; ?>" name="list[name]" placeholder="<?php echo $text_name; ?>" />
                </label>
            </div>

            <div class="form-param">
                <label>
                <p><?php echo $text_description; ?></p>
                <textarea name="list[description]" placeholder="<?php echo $text_description; ?>"><?php echo isset($description) ? $description : ''; ?></textarea>
                </label>
            </div>

            <div class="form-param">
                <label>
                <p>Row</p>
                <input type="text" value="<?php echo isset($row) ? $row : ''; ?>" name="list[row]" placeholder="1" />
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="saveList(<?php echo isset($board_id) ? $board_id : 0; ?>, <?php echo isset($list_id) ? $list_id : 0; ?>);" class="btn btn-success"><?php echo $text_save; ?></button>

        <?php if(isset($list_id) && $list_id) { ?>
        <button type="button" onclick="deleteList(<?php echo isset($list_id) ? $list_id : 0; ?>);" class="btn btn-danger"><?php echo $text_delete; ?></button> 
        <?php } ?>
    </div>
</div>