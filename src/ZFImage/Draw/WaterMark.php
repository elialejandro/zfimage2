<?php
/**
 * ZFImage
 *
 * @link      http://github.com/elialejandro/zfimage2 for the canonical source repository
 * @copyright Copyright (c) 2010-2014 Eli Alejandro
 * @license   https://github.com/elialejandro/zfimage2/blob/master/license.md New BSD License
 */

namespace ZFImage\Draw;

use ZFImage\Image;
use ZFImage\Plugin\Base;
use ZFImage\Plugin\PluginInterface;
use ZFImage\Fx\Resize;

class WaterMark extends Base implements PluginInterface
{
    /**
     * Plugin type
     * @var string
     */
    public $_type_id        = "Draw";
    
    /**
     * Plugin name
     * @var string
     */
    public $_sub_type_id    = "WaterMark";
    
    /**
     * Version
     * @var int
     */
    public $_version        = 0.1;
    
    /**
     * Image to use as watermark
     * 
     * @var ZFImage_Image
     */
    public $_watermark      = null;
    
    /**
     * Position
     * @var string
     */
    public $_position       = "br";
    
    /**
     * Position X
     * @var int
     */
    public $_position_x     = null;
    
    /**
     * Position Y
     * @var int
     */
    public $_position_y     = null;

    /**
     *
     * @param ZFImage\Image $image
     * @param string $position OPTIONAL default "br"
     */
    public function  __construct( Image $image, $position = "br" )
    {
        $this->_watermark   = $image;
        $this->_position    = $position;
    }
    
    /**
     * Posición
     * @param String|int $x Horizontal position
     * <strong>string param</strong><br />
     * "tl" = top-left<br />
     * "tm" = top-middle<br />
     * "tr" = top-right<br />
     * "ml" = middle-left<br />
     * "mm" = middle-middle<br />
     * "mr" = middle-right<br />
     * "bl" = bottom-left<br />
     * "bm" = bottom-middle<br />
     * "br" = bottom-right<br />
     * "tile"
     * @param int $y OPTIONAL vertical position
     */
    public function setPosition( $x, $y = null )
    {
        if ( $y != null ){
            $this->_position = "user";
            $this->_position_x = $x;
            $this->_position_y = $y;
        } else {
            $this->_position = $x;
        }
    }


    public function generate()
    {
        imagesavealpha($this->_owner->image, true);
        imagealphablending($this->_owner->image, true);

        imagesavealpha($this->_watermark->image, false);
        imagealphablending($this->_watermark->image, false);

        $width  = $this->_owner->getWidth();
        $height = $this->_owner->getHeight();

        //TODO: ARREGLAR
            $this->_watermark->attach(new Resize( floor($width) ) );
            $this->_watermark->evaluateFxStack();

        $watermark_width  = $this->_watermark->getWidth();
        $watermark_height = $this->_watermark->getHeight();

        switch ( $this->_position ) {
            case "tl":
                $x = 0;
                $y = 0;
                break;
            case "tm":
                $x = ( $width - $watermark_width )/2;
                $y = 0;
                break;
            case "tr":
                $x = $width - $watermark_width;
                $y = 0;
                break;
            case "ml":
                $x = 0;
                $y = ( $height - $watermark_height )/2;
                break;
            case "mm";
                $x = ( $width - $watermark_width )/2;
                $y = ( $height - $watermark_height )/2;
                break;
            case "mr":
                $x = $width - $watermark_width;
                $y = ( $height - $watermark_height )/2;
                break;
            case "bl":
                $x = 0;
                $y = $height - $watermark_height;
                break;
            case "bm":
                $x = ( $width - $watermark_width )/2;
                $y = $height - $watermark_height;
                break;
            case "br":
                $x = $width - $watermark_width;
                $y = $height - $watermark_height;
                break;
            case "user":
                $x = $this->_position_x - ($this->_watermark->handle_x/2);
                $y = $this->_position_y - ($this->_watermark->handle_y/2);
                break;
            default:
                $x = 0;
                $y = 0;
                break;
        }

        if ( $this->_position != "tile" ) {
            imagecopy( $this->_owner->image, $this->_watermark->image, $x, $y, 0, 0, $watermark_width, $watermark_height);
        } else {
            imagesettile($this->_owner->image, $this->_watermark->image);
            imagefilledrectangle($this->_owner->image, 0, 0, $width, $height, IMG_COLOR_TILED);
        }

        return true;
    }
}
