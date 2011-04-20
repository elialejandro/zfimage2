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
 * @version     $Id: Resize.php 11 2011-04-15 22:40:45Z rurouni $
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

class ZFImage_Fx_Resize extends ZFImage_Plugin_Base implements ZFImage_Plugin_Interface
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

        $dst = new ZFImage_Image();
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

