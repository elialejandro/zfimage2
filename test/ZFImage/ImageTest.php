<?php

use ZFImage\Image;
use ZFImage\Fx\Crop;
use ZFImage\Fx\Resize;

class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateImageInstance()
    {
        $image = new Image();
        $this->assertInstanceOf("ZFImage\Image", $image);
    }
    
    public function testCreateImageBlank()
    {
        $image = new Image();
        $image->createImage(100, 100, 'FFFFFF');
        $this->assertEquals(100, $image->getHeight());
        $this->assertEquals(100, $image->getWidth());
    }
    
    public function testReziseImage()
    {
        $image = new Image();
        $image->createImage(200, 200);
        $result = (boolean) $image->attach(new Resize(150,100));
        $this->assertTrue($result);
        $image->evaluateFxStack();
        
        $this->assertEquals(150, $image->getWidth());
        $this->assertEquals(100, $image->getHeight());
    }
    
    public function testCropImage()
    {
        $image = new Image();
        $image->createImage(200, 200);
        $result = (boolean) $image->attach(new Crop(50,100));
        $this->assertTrue($result);
        $image->evaluateFxStack();
        
        $this->assertEquals(50, $image->getWidth());
        $this->assertEquals(100, $image->getHeight());
    }
}