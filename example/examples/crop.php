<?php

include_once 'config.php';

$image = new ZFImage\Image('images/desert.jpg');
$image->addPlugin(new ZFImage\Fx\Crop(400, 300));
$image->imageJpeg();
