<?php
/**
 * Copyright (c) 2010-2011, Eli Alejandro <iscelialejandro@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Eli Alejandro nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Zend
 * @package     ZFImage
 * @subpackage  Draw
 * @author      Eli Alejandro Moreno López <iscelialejandro@gmail.com>
 * @copyright   Copyright (c) 2010-2011 Eli Alejandro <iscelialejandro@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 *              http://creativecommons.org/licenses/BSD/deed.es_MX  BSD License (español)
 * @version     $Id: WaterMark.php 9 2011-04-15 22:25:23Z rurouni $
 *
 */

/**
 * @see ZFImage_Plugin_Interface
 */
require_once 'ZFImage/Plugin/Interface.php';
/**
 * @see ZFImage_Plugin_Base
 */
require_once 'ZFImage/Plugin/Base.php';

class ZFImage_Draw_WaterMark extends ZFImage_Plugin_Base implements ZFImage_Plugin_Interface
{
    // PROPIEDADES
    /**
     * Tipo de Plugin
     * @var string
     */
    public $_type_id        = "Draw";
    /**
     * Nombre del plugin
     * @var string
     */
    public $_sub_type_id    = "WaterMark";
    /**
     * Versión
     * @var int
     */
    public $_version        = 0.1;
    /**
     * Imagen a incrustar como marca de agua
     * @var ZFImage_Image
     */
    public $_watermark      = null;
    /**
     * Posición
     * @var string
     */
    public $_position       = "br";
    /**
     * Posición en X
     * @var int
     */
    public $_position_x     = null;
    /**
     * Posición en Y
     * @var int
     */
    public $_position_y     = null;

    /**
     *
     * @param ZFImage_Image $image
     * @param string $position OPTIONAL default "br"
     */
    public function  __construct( ZFImage_Image $image, $position = "br" )
    {
        $this->_watermark   = $image;
        $this->_position    = $position;
    }
    /**
     * Posición
     * @param String|int $x
     * <strong>string param</strong><br />
     * "tl" = top-left [Arriba-Izquierda]<br />
     * "tm" = top-middle [Arriba-Centro]<br />
     * "tr" = top-right [Arriba-Derecha]<br />
     * "ml" = middle-left [Medio-Izquierda]<br />
     * "mm" = middle-middle [Medio-Medio]<br />
     * "mr" = middle-right [Medio-Derecha]<br />
     * "bl" = bottom-left [Bajo-Izquierda]<br />
     * "bm" = bottom-middle [Bajo-Centro]<br />
     * "br" = bottom-right [Bajo-Derecha]<br />
     * "tile"
     * @param int $y [Opcional]
     */
    public function setPosition( $x, $y = null )
    {
        if ( $y != null ){
            $this->_position = "user";
            $this->_position_x = $args[0];
            $this->_position_y = $args[1];
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
            $this->_watermark->attach(new ZFImage_Fx_Resize( floor($width) ) );
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
