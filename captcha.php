<?php
session_start();
// Set a fixed CAPTCHA code
$captcha_code = "123r3";
$_SESSION["captcha_code"] = $captcha_code;

// Create the image
$target_layer = imagecreatetruecolor(70, 30);
$captcha_background = imagecolorallocate($target_layer, 176, 224, 230);
imagefill($target_layer, 0, 0, $captcha_background);
$captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
$line_color = imagecolorallocate($target_layer, 0, 0, 0); // Warna garis
$dot_color = imagecolorallocate($target_layer, 0, 0, 0); // Warna titik

// Add the text to the image
imagestring($target_layer, 5, 5, 5, $captcha_code, $captcha_text_color);

// Draw random lines
for ($i = 0; $i < 2; $i++) {
    imageline($target_layer, rand(0, 70), rand(0, 30), rand(0, 70), rand(0, 30), $line_color);
}

// Draw random dots
for ($i = 0; $i < 10; $i++) {
    imagefilledellipse($target_layer, rand(0, 70), rand(0, 30), 2, 2, $dot_color);
}

// Set the content type header
header("Content-type: image/jpeg");

// Output the image
imagejpeg($target_layer);
imagedestroy($target_layer);
