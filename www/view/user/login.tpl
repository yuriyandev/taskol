<?php echo $header; ?>
<div class="login-bg">
    <div class="login-form" id="loginForm">
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
            <button type="button" onclick="loginUser();" class="btn btn-login"><?php echo $text_enter_login; ?></button>
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
