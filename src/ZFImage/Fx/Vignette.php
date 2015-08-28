<?php
namespace ZFImage\Fx;

use ZFImage\Image;
use ZFImage\Plugin\Base;
use ZFImage\Plugin\PluginInterface;

class Vignette extends Base implements PluginInterface
{
    public $type_id = "effect";
    public $sup_type_id = "vignette";
    public $version = 0.1;
    
    /**
     * Image
     * 
     * @var ZFImage\Image
     */
    private $vignette;
    
    public function __construct(Image $vignette = null)
    {
       $this->vignette = $vignette; 
    }
    
    public function getImage()
    {
        return $this->vignette;
    }
    
    public function generate()
    {
        imagesavealpha($this->_owner->image, true);
        imagealphablending($this->_owner->image, false);
        
        $image_x = $this->_owner->imagesx();
        $image_y = $this->_owner->imagesy();
        
        $vignette_x = $this->vignette->imagesx();
        $vignette_y = $this->vignette->imagesy();
        
        for ($y=0;$y<$vignette_y;$y++) {
            for ($x=0;$x<$vignette_x;$x++) {
        
                $irgb = imagecolorat($this->_owner->image, $x, $y);
                $r   = ($irgb >> 16) & 0xFF;
                $g   = ($irgb >> 8) & 0xFF;
                $b   =  $irgb & 0xFF;
        
                $vrgb = imagecolorat($this->vignette->image,$x,$y);
                $a = ($vrgb >> 24) & 0xFF;
        
                $colour = imagecolorallocatealpha($this->_owner->image, $r, $g, $b, $a);
                imagesetpixel($this->_owner->image,$x,$y,$colour);
        
            }
        }
    }
}
