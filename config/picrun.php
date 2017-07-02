<?php
// to get stuff from env use: env('APP_KEY', 'SomeRandomString!!!'),
return [

  'googleapis_key' => env('GOOGLEAPIS_KEY'),
  'googleapis_url' => env('GOOGLEAPIS_URL'),
  'google_images_cx' => env('GOOGLE_IMAGES_CX'),
  'google_videos_cx' => env('GOOGLE_VIDEOS_CX'),

  'google_images_required' => 20,
  'google_gifs_required' => 5,
  'google_stickers_required' => 5,
  'google_min_bytesize' => 15000,
  'google_max_bytesize' => 150000,

  'yandexapis_translate_key' => env('YANDEXAPIS_TRANSLATE_KEY'),
  'yandexapis_translate_url' => env('YANDEXAPIS_TRANSLATE_URL'),

  'yandexapis_dictionary_key' => env('YANDEXAPIS_DICTIONARY_KEY'),
  'yandexapis_dictionary_url' => env('YANDEXAPIS_DICTIONARY_URL'),

  'max_image_size'  => env('MAX_IMAGE_SIZE'),

  'aws_path'  => 'http://' . env('AWS_BUCKET') . env('AWS_URL'),


];
