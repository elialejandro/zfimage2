<?php

include_once "config.php";

$image = new ZFImage\Image("images/desert.jpg");
$watermark = new ZFImage\Image("images/watermark.png");
$image->addPlugin(new ZFImage\Fx\Resize(400));
$image->addPlugin(new ZFImage\Draw\WaterMark($watermark));
$image->imageJpeg();
