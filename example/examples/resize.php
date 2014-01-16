<?php

include "config.php";

$image = new ZFImage\Image("images/desert.jpg");
$image->addPlugin(new ZFImage\Fx\Resize(400));
$image->imageJpeg();