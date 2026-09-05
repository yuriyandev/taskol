<div class="modal" id="userForm">
    <div class="modal-header">
        <?php echo $text_user; ?>
    </div>
    <div class="modal-body">
        <div class="user-form" id="userForm">
            <div class="form-param">
                <label>
                <p><?php echo $text_user_name; ?></p>
                <input type="text" value="<?php echo isset($name) ? $name : ''; ?>" name="user[name]" placeholder="<?php echo $text_user_name; ?>" />
                </label>
            </div>

            <div class="form-param">
                <label>
                <p><?php echo $text_user_email; ?></p>
                <input type="text" value="<?php echo isset($email) ? $email : ''; ?>" name="user[email]" placeholder="<?php echo $text_user_email; ?>" />
                </label>
            </div>
            <div class="form-param">
                <label>
                <p><?php echo $text_user_password; ?></p>
                <input type="<?php echo $text_user_password; ?>" value="<?php echo isset($password) ? $password : ''; ?>" name="user[password]" placeholder="<?php echo $text_user_password; ?>" />
                </label>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="saveUser(<?php echo isset($user_id) ? $user_id : 0; ?>);" class="btn btn-success"><?php echo $text_save; ?></button>
    </div>
</div>