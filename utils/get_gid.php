<?php
$length = 16;
$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$gid = "";
$max = mb_strlen($chars, '8bit') - 1;
for ($i = 0; $i < $length; ++$i) {
  $gid .= $chars[random_int(0, $max)];
}
return $gid;
?>
