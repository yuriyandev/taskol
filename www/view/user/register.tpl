<?php echo $header; ?>
<div class="register-bg">
    <div class="register-form" id="registerForm">
        <div class="form-param">
            <label>
                <input type="text" value="<?php echo isset($name) ? $name : ''; ?>" name="user[name]" placeholder="<?php echo $text_user_name; ?>" />
            </label>
        </div>
        <div class="form-param">
            <label>
                <input type="text" value="<?php echo isset($email) ? $email : ''; ?>" name="user[email]" placeholder="<?php echo $text_user_email; ?>" />
            </label>
        </div>
        <div class="form-param">
            <label>
                <input type="password" value="<?php echo isset($password) ? $password : ''; ?>" name="user[password]" placeholder="<?php echo $text_user_password; ?>" />
            </label>
        </div>
        <div class="form-param">
            <button type="button" onclick="registerUser();" class="btn btn-register"><?php echo $text_enter_register; ?></button>
        </div>
    </div>
</div>
<style>
    .header-sticky {
        position: fixed;
        background-color: transparent;
    }
</style>
<?php echo $footer; ?>
