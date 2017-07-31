<?php
// to get stuff from env use: env('APP_KEY', 'SomeRandomString!!!'),
return [

  'googleapis_key' => env('GOOGLEAPIS_KEY'),
  'googleapis_url' => env('GOOGLEAPIS_URL'),
  'google_images_cx' => env('GOOGLE_IMAGES_CX'),
  'google_videos_cx' => env('GOOGLE_VIDEOS_CX'),
  'google_translate_api_url' => env('GOOGLE_TRANSLATE_API_URL'),
  'google_detect_api_url' => env('GOOGLE_DETECT_API_URL'),
  'youtube_search_api_url' => env('YOUTUBE_SEARCH_API_URL'),

  'google_images_required' => 20,
  'google_gifs_required' => 10,
  'google_stickers_required' => 10,
  'google_min_bytesize' => 15000,
  'google_max_bytesize' => 250000,

  'yandexapis_translate_key' => env('YANDEXAPIS_TRANSLATE_KEY'),
  'yandexapis_translate_url' => env('YANDEXAPIS_TRANSLATE_URL'),

  'yandexapis_dictionary_key' => env('YANDEXAPIS_DICTIONARY_KEY'),
  'yandexapis_dictionary_url' => env('YANDEXAPIS_DICTIONARY_URL'),

  'max_image_size'  => env('MAX_IMAGE_SIZE'),

  'aws_path'  => 'http://' . env('AWS_BUCKET') . env('AWS_URL'),

  'giphy_api_key' => env('GIPHY_API_KEY'),
  'giphy_api_url' => env('GIPHY_API_URL'),
  'giphy_stickers_api_url' => env('GIPHY_STICKERS_API_URL'),

  'supported_languages' => env('PICRUN_SUPPORTED_LANGUAGES'),

];
