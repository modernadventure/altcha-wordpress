<?php

add_action(
  'register_form',
  function () {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_register();
    if (!empty($mode)) {
      altcha_wordpress_comments_render_widget($mode, true);
    }
  },
  10,
  0
);

add_action(
  'register_post',
  function ($user_login, $user_email, $errors) {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_register();
    if (!empty($mode)) {
      if ($plugin->verify($_POST['altcha']) === false) {
        return $errors->add(
          'altcha_error_message',
          '<strong>' . __('Error', 'altcha') . '</strong> : ' . __(AltchaPlugin::$message_cannot_submit, 'altcha')
        );
      }
    }
    return $errors;
  },
  10,
  3
);

add_action(
  'login_form',
  function () {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_login();
    if (!empty($mode)) {
      altcha_wordpress_comments_render_widget($mode, true);
    }
  },
  10,
  0
);

add_filter(
  'authenticate',
  function ($user, $username, $password) {
    if ($user instanceof WP_Error) {
      return $user;
    }
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_login();
    if (!empty($mode)) {
      if ($plugin->verify($_POST['altcha']) === false) {
        return new WP_Error("altcha-error", '<strong>' . __('Error', 'altcha') . '</strong> : ' . __(AltchaPlugin::$message_cannot_submit, 'altcha'));
      }
    }
    return $user;
  },
  20,
  3
);

add_action(
  'lostpassword_form',
  function () {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_reset_password();
    if (!empty($mode)) {
      altcha_wordpress_comments_render_widget($mode, true);
    }
  },
  10,
  0
);

add_filter(
  'lostpassword_post',
  function ($val) {
    if (is_user_logged_in()) {
        return $val;
    }
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_reset_password();
    if (!empty($mode)) {
      if ($plugin->verify($_POST['altcha']) === false) {
        wp_die('<strong>' . __('Error', 'altcha') . '</strong> : ' . __(AltchaPlugin::$message_cannot_submit, 'altcha'));
      }
    }
    return $val;
  },
  10,
  1
);	

add_action(
  'comment_form_after_fields',
  function () {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_comments();
    if (!empty($mode)) {
      altcha_wordpress_comments_render_widget($mode);
    }
  },
  10,
  0
);

add_action(
  'comment_form_logged_in_after',
  function () {
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_comments();
    if (!empty($mode)) {
      altcha_wordpress_comments_render_widget($mode);
    }
  },
  10,
  0
);

add_filter(
  'preprocess_comment',
  function ($comment) {
    // trackback or pingback
    if ($comment['comment_type'] != '' && $comment['comment_type'] != 'comment') {
      return $comment;
    }
    $plugin = AltchaPlugin::$instance;
    $mode = $plugin->get_integration_wordpress_comments();
    if (!empty($mode)) {
      if ($plugin->verify($_POST['altcha']) === false) {
        wp_die('<strong>' . __('Error', 'altcha') . '</strong> : ' . __(AltchaPlugin::$message_cannot_submit, 'altcha'));
      }
    }
    return $comment;
  },
  10,
  1
);

function altcha_wordpress_comments_render_widget($mode, $full_width = false)
{
  altcha_enqueue_scripts();
  altcha_enqueue_styles();
  echo AltchaPlugin::$instance->render_widget($mode, true);
}
