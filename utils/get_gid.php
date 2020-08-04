<?php

//gid is always exactly 15 chars.

function make($length, $chars)
{
  $gid = "";
  $max = mb_strlen($chars, '8bit') - 1;
  for ($i = 0; $i < $length; ++$i) {
    $gid .= $chars[random_int(0, $max)];
  }
  return $gid;
}

$gid = make(1, "hjklmnpqrstvwyz");
$gid .= make(1, "aeiou");
$gid .= make(1, "bcdfgklmnprstvwxyz");
$gid .= make(1, "bcdfghjklmnpqrstvwyz");
$gid .= make(1, "aeiou");
$gid .= make(1, "bcdfgklmnprstvwxyz");
$gid .= make(9, '0123456789abcdefghijklmnopqrstuvwxyz');

return $gid;
?>
