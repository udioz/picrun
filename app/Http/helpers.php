<?php
use LanguageDetection\Language;

function phrase_sanitize($phrase) {
  $phrase = urldecode($phrase);
  $phrase = str_replace('.',' ',$phrase);
  $phrase = preg_replace('/[^\w\s]+/u','' , $phrase);
  $phrase = preg_replace('!\s+!', ' ', $phrase);
  return $phrase;
}

function google_detect_language($phrase) {
  $data = [
    'q' => $phrase,
    'key' => config('picrun.googleapis_key'),
  ];

  $response = Curl::to(config('picrun.google_detect_api_url'))
      ->withData($data)
      ->get();

  $response = json_decode($response);

  if (isset($response->data->detections[0][0])) {
      $language = $response->data->detections[0][0]->language;
      if ($language == 'iw') $language = 'he';
  } else {
      $language = 'unknown';
  }

  if (!str_contains(config('picrun.supported_languages'),$language))
    $language = 'unknown';

  return $language;
}

function detect_language($phrase) {
  $ld = new Language;
  $langs = $ld->detect($phrase)->close();
  $lang = isset(array_keys($langs)[0]) ? array_keys($langs)[0] : 'en';
  return $lang;
}

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
