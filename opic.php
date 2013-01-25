<?php
/*
Plugin Name: OPIC
Plugin URI: http://github.com/osulibraries/wp-opic
Description: Replace Ohio State users' gravatars with OPIC
Author: James Paul Muir
Version: 1.0
Author URI: http://library.osu.edu
*/

function opic_get_avatar($avatar, $id_or_email, $size, $default){
  $name_n = null;
  $user_id = null;
  $email = null;

  if(is_numeric($id_or_email)){
    $user_id = $id_or_email;
  } elseif(is_object($id_or_email)) {
    if(!empty($id_or_email->user_id)){
      $user_id = $id_or_email->user_id;
    } elseif (!empty($id_or_email->comment_author_email) ) {
      $email = $id_or_email->comment_author_email;
    }
  } else{
    $email = $id_or_email;
  }

  if($user_id){
    $user = get_userdata($user_id);
    $email = $user->user_email;
  }

  if($email && preg_match('/([^@]+)@osu\.edu$/', $email, $matches)){
      $name_n = $matches[1];
  }
  
  if($name_n){
    //round up to nearest 50px, up to 400
    $width = min(400, ceil($size/50)*50);
    $opic_url = "https://opic.osu.edu/{$name_n}?width={$width}";
    $avatar = preg_replace("/src='([^']+)'/", "src='$opic_url'", $avatar);
  }
  
  return $avatar;
}


function opic_avatar_options($user){
  if(preg_match('/[^@]+@osu\.edu$/', $user->user_email, $matches)):
    $opic_img_tag = get_avatar($matches[0]);
    echo <<<HTML
      <h3>Profile Picture</h3>
      
      <table class="form-table">
        <tr>
          <th><label>Your Opic</label></th>
          <td width="100">
            {$opic_img_tag}
            <p><a class="button" href="https://opic.osu.edu/" target="_blank">Update Opic</a></p>
          </td>
          <td>
            <p><strong>What's an Opic?</strong><br />It’s the avatar that appears beside your name whenever you post or leave a comment on this site.</p>
            <p><strong>How do I update my Opic?</strong><br />Visit the official <a href="https://opic.osu.edu/">Opic</a> site to manage your Opic.</p>
            <p><strong>Where can I learn more?</strong><br />Please refer to the <a href="https://opic.osu.edu/faq" target="_blank">Opic FAQ</a>.</p>
          </td>
        </tr>
      </table>
HTML;
  endif;
  }

function opic_default_avatar_select($avatar_list){
  $avatar_list = "<i>Default settings are not enabled while Opic plugin is active.";
  return $avatar_list;
}


add_filter('get_avatar','opic_get_avatar', null, 5);
add_filter('default_avatar_select','opic_default_avatar_select');
add_action('show_user_profile', 'opic_avatar_options');
add_action('edit_user_profile', 'opic_avatar_options');
