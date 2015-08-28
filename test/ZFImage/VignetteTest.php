<?php

use ZFImage\Image;
use ZFImage\Fx\Vignette;

/**
 * Vignette test case.
 */
class VignetteTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Vignette
     */
    private $vignette;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->vignette = new Vignette();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated VignetteTest::tearDown()
        $this->vignette = null;
        
        parent::tearDown();
    }

    
    public function testTypeObject()
    {
        $this->assertInstanceOf("\\ZFImage\\Fx\\Vignette", $this->vignette);
    }
    
    /**
     * Tests Vignette->generate()
     */
    public function testConstruct()
    {
        $this->vignette = new Vignette();
        
        $this->assertNull($this->vignette->getImage());
        
        $image = new Image();
        $image->createImage();
        $this->vignette = new Vignette($image);
        $this->assertInstanceOf("\\ZFImage\\Image", $this->vignette->getImage());
    }
}

