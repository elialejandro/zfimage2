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
 * @subpackage  Fx
 * @author      Eli Alejandro Moreno López <iscelialejandro@gmail.com>
 * @copyright   Copyright (c) 2010-2011 Eli Alejandro <iscelialejandro@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 *              http://creativecommons.org/licenses/BSD/deed.es_MX  BSD License (español)
 * @version     $Id: Crop.php 8 2011-04-15 22:25:01Z rurouni $
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

class ZFImage_Fx_Crop extends ZFImage_Plugin_Base implements ZFImage_Plugin_Interface
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
     * o $align = "center, por defecto center.
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

        $crop = new ZFImage_Image();
        $crop->createImageTrueColorTransparent($this->_canvas_x, $this->_canvas_y);

        if ( $this->_align == "center" ) {
            $src_x = $this->_owner->handle_x - floor($this->_canvas_x/2);
            $src_y = $this->_owner->handle_y - floor($this->_canvas_y/2);
        } else if ( $this->_align == "top") {
            $src_x = 0;
            $src_y = 0;
        } else {
            throw new ZFImage_Exception("No se puede recortar la imagen porque la "
                                        . "alineación que a escogido es incorrecta.");
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
