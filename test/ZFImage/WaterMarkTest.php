<?php

use ZFImage\Image;
use ZFImage\Draw\WaterMark;

/**
 * WaterMark test case.
 */
class WaterMarkTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var WaterMark
     */
    private $waterMark;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $image = new Image();
        $image->createImage();
        $this->waterMark = new WaterMark($image);
        
        $this->assertInstanceOf("\\ZFImage\\Draw\\WaterMark", $this->waterMark);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->waterMark = null;
        
        parent::tearDown();
    }

    /**
     * Tests WaterMark->setPosition()
     */
    public function testSetPosition()
    {
        $this->waterMark->setPosition(10, 20);
        
        $this->assertEquals(10, $this->waterMark->_position_x);
        $this->assertEquals(20, $this->waterMark->_position_y);
    }
}

