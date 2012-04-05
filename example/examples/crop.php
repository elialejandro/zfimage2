<?php

include_once 'config.php';

$image = new ZFImage_Image('images/desert.jpg');
$image->addPlugin(new ZFImage_Fx_Crop(400, 300));
$image->imageJpeg();
