<?php
use Illuminate\Support\Facades\Route;
Route::get('/test-decrypt/{enc}', function($enc){
  try {
    $dec = decrypt($enc);
    return "dec: $dec";
  } catch(Exception $e){
    return "fail: ".$e->getMessage()." enc: $enc";
  }
});
