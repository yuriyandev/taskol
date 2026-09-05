<div class="modal" id="boardForm">
    <div class="modal-header">
        <div><?php echo $text_board; ?></div>
        <button class="btn btn-fill" onclick="modal('boardForm', 'hide');"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
        <div class="board-form" id="boardForm">
            <div class="form-param">
                <label>
                <p><?php echo $text_name; ?></p>
                <input type="text" value="<?php echo isset($name) ? $name : ''; ?>" name="board[<?php echo $board_id; ?>][name]" placeholder="<?php echo $text_name; ?>" save-on-change />
                </label>
            </div>

            <div class="form-param">
                <label>
                <p><?php echo $text_description; ?></p>
                <textarea name="board[<?php echo $board_id; ?>][description]" placeholder="<?php echo $text_description; ?>" save-on-change><?php echo isset($description) ? $description : ''; ?></textarea>
                </label>
            </div>

            <div class="form-param">
                <label>
                <p><?php echo $text_private; ?></p>
                <select name="board[<?php echo $board_id; ?>][private]" save-on-change>
                    <option value="0" <?php echo isset($private) && !$private ? '' : 'selected'; ?> >Нет</option>
                    <option value="1" <?php echo isset($private) && $private ? 'selected' : ''; ?> >Да</option>
                </select>
                </label>
            </div>

            <div class="members form-param">
                <p>Участники</p>
                <div>
                    <table>
                        <thead>
                            <tr>
                                <td>Email</td>
                                <td>Только просмотр</td>
                                <td>Просмотр и Редактирование</td>
                                <td>Полный доступ</td>
                                <td>Удалить</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($members) && $members) { ?>
                            <?php foreach($members as $member) { ?>
                            <tr class="form">
                                <td>
                                    <label>
                                        <input type="hidden" name="board[<?php echo $board_id; ?>][members][<?php echo $member['member_id']; ?>][email]" value="<?php echo $member['email']; ?>" readonly /> <?php echo $member['email']; ?>
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="view" <?php echo $member['priv'] == 'view' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="edit" <?php echo $member['priv'] == 'edit' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="all" <?php echo $member['priv'] == 'all' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                    </label>
                                </td>
                                <td>
                                    <input type="radio" name="board[<?php echo $board_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="del" onchange="saveForm($(this).closest('.form'))" />
                                </td>
                            </tr>
                            <?php } ?>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="form">
                                <td>
                                    <label>
                                        <input type="text" name="board[<?php echo $board_id; ?>][members][0][email]" placeholder="Email" value="" />
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][0][priv]" value="view" checked />
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][0][priv]" value="edit" />
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <input type="radio" name="board[<?php echo $board_id; ?>][members][0][priv]" value="all" />
                                    </label>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="saveForm($(this).closest('.form'))">Добавить</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="saveBoard(<?php echo isset($board_id) ? $board_id : 0; ?>);" class="btn btn-success"><?php echo $text_save; ?></button>

        <?php if(isset($board_id) && $board_id) { ?>
        <button type="button" onclick="deleteBoard(<?php echo isset($board_id) ? $board_id : 0; ?>);" class="btn btn-danger"><?php echo $text_delete; ?></button> 
        <?php } ?>
    </div>
</div>