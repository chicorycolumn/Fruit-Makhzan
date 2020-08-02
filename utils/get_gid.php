<?php

function make($length, $chars)
{
  $gid = "";
  $max = mb_strlen($chars, '8bit') - 1;
  for ($i = 0; $i < $length; ++$i) {
    $gid .= $chars[random_int(0, $max)];
  }
  return $gid;
}

// $after_G_chars = 'hijklmnopqrstuvwxyzHIJKLMNOPQRSTUVWXYZ';
// $max_after_G_chars = mb_strlen($after_G_chars, '8bit') - 1;
// $gid = $after_G_chars[random_int(0, $max_after_G_chars)] . $gid;

$gid = make(1, "hjklmnpqrstvwyz");
$gid .= make(1, "aeiou");
$gid .= make(1, "bcdfgklmnprstvwxyz");
$gid .= make(1, "bcdfghjklmnpqrstvwyz");
$gid .= make(1, "aeiou");
$gid .= make(1, "bcdfgklmnprstvwxyz");
$gid .= make(
  9,
  '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
);

return $gid;
?>
