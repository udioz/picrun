<?php

function imageSuffix($mime) {
    $suffix = explode("/",$mime);
    $suffix = $suffix[1];
    if ($suffix == 'jpeg') $suffix = 'jpg';
    return $suffix;
}


function count_words($string) {
    // Return the number of words in a string.
    $string= str_replace("&#039;", "'", $string);
    $t= array(' ', "\t", '=', '+', '-', '*', '/', '\\', ',', '.', ';', ':', '[', ']', '{', '}', '(', ')', '<', '>', '&', '%', '$', '@', '#', '^', '!', '?', '~'); // separators
    $string= str_replace($t, " ", $string);
    $string= trim(preg_replace("/\s+/", " ", $string));
    $num= 0;
    if (my_strlen($string)>0) {
        $word_array= explode(" ", $string);
        $num= count($word_array);
    }
    return $num;
}

function my_strlen($s) {
    // Return mb_strlen with encoding UTF-8.
    return mb_strlen($s, "UTF-8");
}
