<?php

include_once "config.php";

$image = new ZFImage_Image("images/desert.jpg");
$watermark = new ZFImage_Image("images/watermark.png");
$image->addPlugin(new ZFImage_Fx_Resize(400));
$image->addPlugin(new ZFImage_Draw_WaterMark($watermark));
$image->imageJpeg();
