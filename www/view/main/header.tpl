<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $text_meta_title; ?></title>

  <base href="<?php echo $base; ?>" />

  <link rel="apple-touch-icon" sizes="180x180" href="assets/image/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/image/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/image/favicons/favicon-16x16.png">
  <link rel="manifest" href="assets/image/favicons/site.webmanifest">

  <link rel="stylesheet" href="assets/js/selectric/selectric.css">
  <link rel="stylesheet" href="assets/css/styles.css">

  <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
</head>
<body>
  <header class="header-sticky">
    <div class="container-fluid">
      <div class="header">
        <?php if($logged) { ?>
          <div class="header-left">
            <button class="btn btn-transparent-light" type="button" onclick="$('main').toggleClass('sidebar-active');"><i class="fas fa-stream"></i></button>

            <a href="" title="Home" class="logo"><?php echo $text_app_name; ?></a>

            <div class="private-mode-switcher hidden-xs">
              <input id="private-mode" type="checkbox" name="private_mode" value="1" onchange="setPrivateMode(this)" <?php echo $private ? ' checked ' : ''; ?> />
              <label for="private-mode"><i class="fas fa-user-secret"></i></label>
            </div>
          </div>

          <div class="header-right">
            <ul class="nav">
              <li><a href="index.php?action=board/view"><?php echo $text_boards; ?></a></li>
              <li><a href="index.php?action=report/view">Reports</a></li>
              <li><a href="index.php?action=user/logout"><?php echo $text_user_logout; ?></a></li>
            </ul>
          </div>
        <?php } else { ?>
          <a href="/" title="Home" class="logo"><?php echo $text_app_name; ?></a>

          <ul class="nav">
            <li><a href="index.php?action=user/login"><?php echo $text_user_login; ?></a></li>
            <li><a href="index.php?action=user/register"><?php echo $text_user_register; ?></a></li>
          </ul>
        <?php } ?>
      </div>
    </div>
  </header>

  <main>
    