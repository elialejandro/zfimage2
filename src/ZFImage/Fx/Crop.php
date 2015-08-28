<?php
/**
 * ZFImage
 *
 * @link      http://github.com/elialejandro/zfimage2 for the canonical source repository
 * @copyright Copyright (c) 2010-2014 Eli Alejandro
 * @license   https://github.com/elialejandro/zfimage2/blob/master/license.md New BSD License
 */

namespace ZFImage\Fx;

use ZFImage\Image;
use ZFImage\Plugin\Base;
use ZFImage\Plugin\PluginInterface;

class Crop extends Base implements PluginInterface
{
    public $_type_id        = "Effect";
    public $_sub_type_id    = "Crop";
    public $_version        = 0.2;

    /**
     * Tamaño en X
     * @var int
     */
    private $_crop_x    = 0;
    /**
     * Tamaño en Y
     * @var int
     */
    private $_crop_y    = 0;
    
    /**
     * Alineación
     * @var type 
     */
    private $_align     = "";

    /**
     * Ancho del lienzo
     * @var  int
     */
    private $_canvas_x  = 0;
    /**
     * Alto del lienzo
     * @var int
     */
    private $_canvas_y  = 0;

    /**
     * Cortar imagen
     * Tipos de Alineación
     * $align = "top"
     * o $align = "center", por defecto center.
     * 
     * @param int $crop_x Requerido
     * @param int $crop_y Requerido
     * @param string $align
     * $name Description
     */
    public function __construct( $crop_x, $crop_y, $align = "center" )
    {
        $this->_crop_x = $crop_x;
        $this->_crop_y = $crop_y;
        $this->_align  = $align;
    }
    /**
     * Nuevo tamaño
     * @param int $crop_x Requerido
     * @param int $crop_y Requerido
     */
    public function setCrop( $crop_x, $crop_y )
    {
        $this->_crop_x = $crop_x;
        $this->_crop_y = $crop_y;
    }
    /**
     * Alineación 
     * "top" o "center"
     * @param string $align
     */
    public function setAlign( $align = "center" ) 
    {
        $this->_align = $align;
    }
    
    /**
     * Calcula el area de cortado
     * @return boolean
     */
    public function calculate()
    {
        $old_x = $this->_owner->getWidth();
        $old_y = $this->_owner->getHeight();

        $this->_canvas_x = $old_x;
        $this->_canvas_y = $old_y;

        if ( $this->_crop_x > 0 ) {
            if ( $this->_canvas_x > $this->_crop_x ) {
                $this->_canvas_x = $this->_crop_x;
            }
        }
        if ( $this->_crop_y > 0 ) {
            if ( $this->_canvas_y > $this->_crop_y ) {
                $this->_canvas_y = $this->_crop_y;
            }
        }
        return true;
    }
    /**
     * Genara la imagen cortada
     * @return true
     */
    public function generate()
    {
        $this->calculate();

        $crop = new Image();
        $crop->createImageTrueColorTransparent($this->_canvas_x, $this->_canvas_y);

        // CROP ALIGN
        switch($this->_align) { 
            case "center": 
                $src_x = $this->_owner->handle_x - floor($this->_canvas_x/2);
                $src_y = $this->_owner->handle_y - floor($this->_canvas_y/2);
                break;
            case "top":
            default:
                $src_x = 0;
                $src_y = 0;
                break;
        }
        
        imagecopy(
                $crop->image,
                $this->_owner->image,
                0,
                0,
                $src_x,
                $src_y,
                $this->_canvas_x,
                $this->_canvas_y);

        $this->_owner->image = $crop->image;

        unset($crop);

        return true;
    }
}
