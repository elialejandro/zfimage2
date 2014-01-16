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

class Resize extends Base implements PluginInterface
{
    public $_type_id   = 'Effect';
    public $_sub_type_id = 'Resize';
    public $_version   = 0.1;

    /**
     *
     * @param int $resize_x [Opcional]
     * @param int $resize_y [Opcional]
     */
    public function __construct( $resize_x = 0, $resize_y = 0)
    {
        $this->resize_x = $resize_x;
        $this->resize_y = $resize_y;
    }
    /**
     * Agregar nuevo tamaño
     * @param int $resize_x Requerido
     * @param int $resize_y [Opcional]
     */
    public function setResize( $resize_x = 0, $resize_y = 0)
    {
        $this->resize_x = $resize_x;
        $this->resize_y = $resize_y;
    }
    /**
     * Calcular nuevo tamaño
     * @return boolean
     */
    public function calculate()
    {
        $old_x = $this->_owner->getWidth();
        $old_y = $this->_owner->getHeight();

        // Cambiar el tamaño de la imagen a un tamaño específico
        // proporcional a la relación de aspecto
        // Predeterminando el antiguo tamaño
        $this->canvas_x = $old_x;
        $this->canvas_y = $old_y;

        if ( $this->resize_x > 0 and $this->resize_y > 0 ) {
            $this->canvas_x = $this->resize_x;
            $this->canvas_y = $this->resize_y;
        } elseif ( $this->resize_x > 0 ) {
            if ( $this->resize_x < $old_x ) {
                $this->canvas_x = $this->resize_x;
                $this->canvas_y = floor(($this->resize_x/$old_x) * $old_y );
            }
        } elseif ( $this->resize_y > 0 ) {
            if ( $this->resize_y < $old_y ) {
                $this->canvas_x = floor(($this->resize_y/$old_y) * $old_x );
                $this->canvas_y = $this->resize_y;
            }
        }

        return true;
    }

    /**
     * Generar nueva imagen con el nuevo tamaño
     * @return boolean
     */
    public function generate()
    {
        $src_x = $this->_owner->getWidth();
        $src_y = $this->_owner->getHeight();

        $this->calculate();

        $dst_x = $this->canvas_x;
        $dst_y = $this->canvas_y;

        $dst = new Image();
        $dst->createImageTrueColorTransparent($dst_x, $dst_y);

        imagecopyresampled(
                $dst->image,
                $this->_owner->image,
                0,
                0,
                0,
                0,
                $dst_x,
                $dst_y,
                $src_x,
                $src_y
        );

        $this->_owner->image = $dst->image;

        unset($dst);

        return true;
    }
}

