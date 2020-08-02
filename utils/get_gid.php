<?php
$length = 15;
$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$gid = "";
$max = mb_strlen($chars, '8bit') - 1;
for ($i = 0; $i < $length; ++$i) {
  $gid .= $chars[random_int(0, $max)];
}

$after_G_chars = 'hijklmnopqrstuvwxyzHIJKLMNOPQRSTUVWXYZ';
$max_after_G_chars = mb_strlen($after_G_chars, '8bit') - 1;
$gid = $after_G_chars[random_int(0, $max_after_G_chars)] . $gid;

return $gid;
?>
