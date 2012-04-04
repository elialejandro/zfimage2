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
 * @author      Eli Alejandro Moreno López <iscelialejandro@gmail.com>
 * @copyright   Copyright (c) 2010-2011 Eli Alejandro <iscelialejandro@gmail.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 *              http://creativecommons.org/licenses/BSD/deed.es_MX  BSD License (español)
 * @version     $Id: Image.php 12 2011-04-19 20:52:58Z rurouni $
 *
 */

class ZFImage_Image
{
    /**
     * Identificador de la imagen
     * @var imagen_gd
     */
    public $image   = null;
    /**
     * Opciones de la imagen
     * @var array
     */
    private $settings     = array();
    /**
     * @var array
     */
    private $attachments  = array();
    /**
     * @var array
     */
    private $attachments_stack = array();

    //--------------------------------------------------------------------------
    /**
     * Manejable
     * @var boolean
     */
    public $mid_handle   = false;

    /**
     * GD Config
     * @var array
     */
    private $gd_info      = null;
    /**
     * GD Version
     * @var int
     */
    private $gd_version   = null;
    /**
     * Soporta imagenes GIF
     * @var boolean
     */
    private $gd_support_gif   = false;
    /**
     * Soporta imagenes PNG
     * @var boolean
     */
    private $gd_support_png   = false;
    /**
     * Soporta imagens JPEG
     * @var boolean
     */
    private $gd_support_jpg   = false;
    /**
     * Soporta fuentes TTF
     * @var boolean
     */
    private $gd_support_ttf   = false;

    /*
     * FILE INFO
     */
    /**
     * Dirección de la imagen
     * @var string
     */
    private $filepath       = null;
    /**
     * Nombre de la imagen
     * @var string
     */
    private $filename       = null;
    /**
     * Tamaño del archivo en bytes
     * @var int
     */
    private $filesize_bytes = 0;
    /**
     * Tamaño del archivo preformateado
     * @var string
     */
    private $filesize_formatted = null;
    /**
     * Ancho original del archivo
     * @var int
     */
    private $original_width     = 0;
    /**
     * Alto original del archivo
     * @var int
     */
    private $original_height    = 0;

    /**
     * Tipo de Imagen
     * @var int 
     */
    private $type = null;
    //--------------------------------------------------------------------------
    /**
     * @param string|int $src Ubicación de la imágen a abrir o ancho de la imágen
     * que será creada
     * @param int $height Alto de la imágen
     */
    public function  __construct()
    {
        $this->_detectGD();

        $this->mid_handle = true;

        $args = func_get_args();
        if ( count( $args ) == 1 ) {
            if ( !empty($args[0]) ) {
                $this->openImage( $args[0] );
            }
        } elseif ( count( $args) == 2 ) {
            $this->createImageTrueColor( $args[0], $args[1] );
        }
    }

    //--------------------------------------------------------------------------
	
    /**
     * Add a plugin FX or Draw
     * @param ZFImage_Plugin_Base $plugin
     * @return string|boolean
     */
    public function addPlugin( $plugin )
    {
    	return $this->attach($plugin);
    }
    
    /**
     * Attach a FX
     * @param ZFImage_Plugin_Base $child
     * @return string|boolean
     */
    public function attach( $child )
    {
        if ( !($child instanceof ZFImage_Plugin_Interface ) ) {
            return false;
        }

        $type = $child->getTypeId();
        if ( array_key_exists( $type, $this->attachments ) ) {
            $this->attachments[$type]++;
        } else {
            $this->attachments[$type] = 1;
        }

        $id = 'a_'.$type.'_'.$this->attachments[$type];

        $this->attachments_stack[$id] = $child;
        $this->attachments_stack[$id]->attachToOwner($this);

        return $id;
    }
    /**
     * Evaluar efectos
     * @return boolean
     */
    public function evaluateFxStack()
    {
        if ( is_array( $this->attachments_stack ) ) {
            foreach( $this->attachments_stack as $id => $attachment ) {
                switch($attachment->getTypeId()) {
                    case "Effect":
                    case "Draw":
                            $attachment->generate();
                            break;
                }
            }
        }
        return true;
    }

    //--------------------------------------------------------------------------
    /**
     * Crear Imagen
     * @param int $width
     * @param int $height
     * @param string $color Hexadecimal
     */
    public function createImage( $width = 100, $height = 100, $color = 'FFFFFF' )
    {
        $this->image = imagecreate($width, $height);
        if ( !empty($color)) {
            $this->imagefill(0,0,$color);
        }
    }
    /**
     * Crear imagen de color verdadero
     * @param int $width
     * @param int $height
     * @param string $color
     */
    public function createImageTrueColor( $width = 100, $height = 100, $color = 'FFFFFF' )
    {
        $this->image = imagecreatetruecolor($width, $height);
        if ( !empty($color) ) {
            $this->imagefill(0,0,$color);
        }
    }
    /**
     * Crear imagen transparente con color verdadero
     * @param int $width
     * @param int $height
     */
    public function createImageTrueColorTransparent( $width = 100, $height = 100 )
    {
        $this->image = imagecreatetruecolor($width, $height);
        $blank = imagecreatefromstring(base64_decode($this->_blankpng()));
        imagesavealpha($this->image, true);
        imagealphablending($this->image, false);
        imagecopyresized($this->image, $blank, 0, 0, 0, 0, $width, $height, imagesx($blank), imagesy($blank));
        imagedestroy($blank);
    }
    /**
     * Abrir archivo de imagen
     * @param string $src
     * @return boolean
     */
    public function openImage( $filename = "" )
    {
        if ( file_exists( $filename ) ) {
            if ( ($image_data = getimagesize($filename)) ) {
                $this->type = $image_data[2];
                switch ( $image_data[2] ) { // El elemento 2 hace referencia el tipo de imagen
                    case IMAGETYPE_GIF:
                        if ( $this->gd_support_gif ) {
                            $this->image = imagecreatefromgif($filename);
                            $this->_file_info($filename);
                            return true;
                        } else {
                            throw new ZFImage_Exception("No se soportan imágenes GIF");
                        }
                        break;
                    case IMAGETYPE_PNG:
                        if ( $this->gd_support_png ) {
                            $this->image = imagecreatefrompng($filename);
                            $this->_file_info($filename);
                            return true;
                        } else {
                            throw new ZFImage_Exception("No se soportan imágenes PNG");
                        }
                        break;
                    case IMAGETYPE_JPEG:
                        if ( $this->gd_support_jpg ) {
                            $this->image = imagecreatefromjpeg($filename);
                            $this->_file_info($filename);
                            return true;
                        } else {
                            throw new ZFImage_Exception("No se soportan imágenes JPEG");
                        }
                        break;
                    default:
                        throw new ZFImage_Exception("Tipo de Imágen no soportado");
                }
            } else {
                throw new ZFImage_Exception("No se puede Obtener el tamaño de la imagen");
            }
        } else {
            throw new ZFImage_Exception("El archivo no existe");
        }
    }

    public function getImageType()
    {
        return $this->type;
    }
    
    //--------------------------------------------------------------------------

    /**
     * Enviar encabezados
     * @param int $image_format
     * @return boolean
     */
    public function sendHeader( $image_format = IMAGETYPE_PNG )
    {
        switch ( $image_format ) {
            case IMAGETYPE_GIF:
                header("Content-type: image/gif");
                return true;
                break;
            case IMAGETYPE_JPEG:
                header("Content-type: image/jpeg");
                return true;
                break;
            case IMAGETYPE_PNG:
                header("Content-type: image/png");
                return true;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Generar imagen GIF
     * @param string $filename
     * @return IMAGEN_GIF
     */
    public function imageGif( $filename = "" )
    {
        if ( !isset($this->image) ) { return false; }
        $this->evaluateFxStack();
        if ( $this->gd_support_gif ) {
            $e = error_get_last();

            if ( !is_array( $e ) ) {
                if ( !empty($filename) ) {
                    return imagegif($this->image, $filename);
                } else {
                    if ( $this->sendHeader(IMAGETYPE_GIF) ) {
                        return imagegif($this->image);
                    }
                }
            } else {
                throw new ZFImage_Exception("No se puede crear la imagen");
            }
        } else {
            throw new ZFImage_Exception("No se soportan imágenes GIF");
        }
    }

    /**
     * Generar imagen PNG
     * @param string $filename
     * @return IMAGEN_PNG
     */
    public function imagePng( $filename = "" )
    {
        if ( !isset($this->image) ) { return false; }
        $this->evaluateFxStack();
        if ( $this->gd_support_png ) {
            $e = error_get_last();
            $e = null;
            if ( !is_array( $e ) ) {
                if ( !empty($filename) ) {
                    return imagepng($this->image, $filename);
                } else {
                    if ( $this->sendHeader(IMAGETYPE_PNG) ) {
                        return imagepng($this->image);
                    }
                }
            } else {
                throw new ZFImage_Exception("No se puede crear la imagen");
            }
        } else {
            throw new ZFImage_Exception("No se soportan imágenes PNG");
        }
    }
    /**
     * Generar imagen JPEG
     * @param string $filename
     * @param int    $calidad
     * @return IMAGEN_JPEG
     */
    public function imageJpeg( $filename = "", $calidad=80 )
    {
        if ( !isset($this->image) ) { return false; }
        $this->evaluateFxStack();
        if ( $this->gd_support_jpg ) {
            $e = error_get_last();

            if ( !is_array( $e ) ) {
                if ( !empty($filename) ) {
                    return imagejpeg($this->image, $filename, $calidad);
                } else {
                    if ( $this->sendHeader(IMAGETYPE_JPEG) ) {
                        return imagejpeg($this->image, "", $calidad);;
                    }
                }
            } else {
                throw new ZFImage_Exception("No se puede crear la imagen");
            }
        } else {
            throw new ZFImage_Exception("No se soportan imágenes JPEG");
        }
    }

    /**
     * Destruir la imagen
     * @return boolean
     */
    public function destroyImage()
    {
        if ( !isset( $this->image ) ) {
            return false;
        }
        imagedestroy($this->image);
        unset($this->image);
    }

    //--------------------------------------------------------------------------

    /**
     * Devuelve el ancho de la imagen
     * @return int
     */
    public function getWidth()
    {
        if ( !isset( $this->image) ) {
            throw new ZFImage_Exception("No se ha cargado o creado una imagen");
        }
        return imagesx($this->image);
    }
    /**
     * Devuelve el alto de la imagen
     * @return int
     */
    public function getHeight()
    {
        if ( !isset( $this->image) ) {
            throw new ZFImage_Exception("No se ha cargado o creado una imagen");
        }
        return imagesy($this->image);
    }

    /**
     * Detremina si una imagen es de color verdadero
     * @return boolean
     */
    public function imageIsTrueColor()
    {
        if ( !isset($this->image) ) {
            return false;
        }
        return imageistruecolor($this->image);
    }
    /**
     * Identifica un color
     * @param int $x
     * @param int $y
     * @return int
     */
    public function imageColorAt( $x = 0, $y = 0 )
    {
        if ( !isset($this->image) ) {
            return false;
        }

        $color = imagecolorat($this->image, $x, $y);

        if ( $this->imageIsTrueColor() ) {
            $arrColor = imagecolorsforindex($this->image, $color);
            return $this->arrayColorToIntColor($arrColor);
        } else {
            return $color;
        }
    }

    //--------------------------------------------------------------------------

    /**
     * Rellenar imagen
     * @param string $color
     * @return boolean
     */
    public function imagefill( $color = "FFFFFF" )
    {
        if ( !isset($this->image) ) {
            return false;
        }
        $arrColor = ZFImage_Image::hexColorToArrayColor($color);
        $bgcolor  = imagecolorallocate($this->image, $arrColor["red"], $arrColor["green"], $arrColor["blue"]);
        imagefill($this->image, 0, 0, $bgcolor);
        return true;
    }

    /**
     * Color permitido
     * @param string $color
     * @return int
     */
    public function imageColorAllocate( $color = "FFFFFF" )
    {
        $arrColor = ZFImage_Image::hexColorToArrayColor($color);
        return imagecolorallocate($this->image, $arrColor["red"], $arrColor["green"], $arrColor["blue"]);
    }

    //--------------------------------------------------------------------------

    /**
     * Desplazar imagen
     * @param array $map
     * @return boolean
     */
    public function displace( $map )
    {
        if ( !isset($this->image) ) {
            return false;
        }

        $width = $this->getWidth();
        $height= $this->getHeight();

        $temp = new ZFImage_Image( $width, $height);

        for ( $y = 0; $y < $height; $y++ ) {
            for ( $x = 0; $x < $width; $x++ ) {
                $rgb = $this->imageColorAt( $map['x'][$x][$y], $map['y'][$x][$y]);
                $arrRgb = ZFImage_Image::intColorToArrayColor($rgb);
                $col = imagecolorallocatealpha($this->image, $arrRgb["red"], $arrRgb["green"], $arrRgb["blue"], $arrRgb["alpha"]);
                imagesetpixel($this->image, $x, $y, $col);
            }
        }

        $this->image = $temp->image;
        return true;
    }

    /**
     * Prueba del manejador
     * @return boolean
     */
    public function testImageHandle()
    {
        if ( isset($this->image) ) {
            if ( get_resource_type($this->image) == "gd") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //--------------------------------------------------------------------------
    // FUNCIONES ESTATICAS
    // -------------------------------------------------------------------------

    /**
     * Array de colores a color entero
     * @param array $arrColor
     * @return int
     */
    public static function arrayColorToIntColor( $arrColor = array(0,0,0) )
    {
        $intColor =    (($arrColor["alpha"] & 0xFF) << 24 )
                     | (($arrColor["red"] & 0xFF) << 16 )
                     | (($arrColor["green"] & 0xFF) << 8)
                     | (($arrColor["blue"] & 0xFF) << 0);

        return $intColor;
    }

    /**
     * Array de colores a color hexadecimal
     * @param array $arrColor
     * @return string
     */
    public static function arrayColorToHexColor( $arrColor = array(0,0,0) )
    {
        $intColor = ZFImage_Image::arrayColorToIntColor($arrColor);
        $hexColor = ZFImage_Image::intColorToHexColor($intColor);

        return $hexColor;
    }

    /**
     * Color entero a array de Colores
     * @param int $intColor
     * @return array
     */
    public static function intColorToArrayColor( $intColor = 0 )
    {
        $arrColor["alpha"]  = ($intColor >> 24) & 0xFF;
        $arrColor["red"]    = ($intColor >> 16) & 0xFF;
        $arrColor["green"]  = ($intColor >> 8)  & 0xFF;
        $arrColor["blue"]   = ($intColor)       & 0xFF;

        return $arrColor;
    }

    /**
     * Color entero a color Hexadecimal
     * @param int $intColor
     * @return string
     */
    public static function intColorToHexColor( $intColor = 0 )
    {
        $arrColor = ZFImage_Image::intColorToArrayColor($intColor);
        $hexColor = ZFImage_Image::arrayColorToHexColor($arrColor);

        return $hexColor;
    }

    /**
     * Color hexadecimal a array de colores
     * @param String $hexColor
     * @return Array
     */
    public static function hexColorToArrayColor( $hexColor = "000000" )
    {
        $arrColor["red"]    = hexdec(substr($hexColor, 0, 2));
        $arrColor["green"]  = hexdec(substr($hexColor, 2, 2));
        $arrColor["blue"]   = hexdec(substr($hexColor, 4, 2));

        return $arrColor;
    }

    /**
     * Color hexadecimal a color entero
     * @param string $hexColor
     * @return int
     */
    public static function hexColorToIntColor( $hexColor = "000000" )
    {
        $arrColor = ZFImage_Image::hexColorToArrayColor($hexColor);
        $intColor = ZFImage_Image::arrayColorToIntColor($arrColor);

        return $intColor;
    }

    //--------------------------------------------------------------------------
    /**
     * Función magica.
     * @param string $name
     * @return mixed
     */

    public function  __get($name)
    {
        if ( $name == "handle_x" ) {
            return ($this->mid_handle) ? floor($this->getWidth()/2): 0;
        }
        if ( $name == "handle_y" ) {
            return ($this->mid_handle) ? floor($this->getHeight()/2): 0;
        }

        if ( substr($name, 0, 2 ) == "a_" ) {
            return $this->attachments_stack[$name];
        } elseif ( array_key_exists($name, $this->settings ) ){
            return $this->settings[$name];
        } else {
            return false;
        }
    }
    /**
     * Función magica
     * @param string $name
     * @param mixed $value
     */
    public function  __set($name,  $value)
    {
        if ( substr($nambe, 0, 2) == "a_" ) {
            $this->attachments_stack[$name] = $value;
        } else {
            $this->settings[$name] = $value;
        }
    }

    //--------------------------------------------------------------------------
    // FUNCIONES PRIVADAS
    //--------------------------------------------------------------------------

    /**
     * Detectar si esta activa la extensión GD
     * para la manipulación de Imagenes
     */
    private function _detectGD()
    {
        if ( !extension_loaded('gd') ) {
            require_once 'ZF/Image/Exception.php';
            throw new ZFImage_Exception('Se requiere la extensión GD');
        }

        $this->gd_info = gd_info();

        preg_match('/\d+/', $this->gd_info['GD Version'], $match);
        $this->gd_version = $match[0];
        $this->gd_support_gif = $this->gd_info['GIF Create Support'];
        $this->gd_support_png = $this->gd_info['PNG Support'];
        // Dependiendo de la versión de GD que se use gd_info puede variar el nombre de la variable
        // que se esta buscando por lo que esta función quedará definida de la siguiente forma
        $this->gd_support_jpg = isset($this->gd_info['JPEG Support'])? $this->gd_info['JPEG Support']: $this->gd_info['JPG Support'];
        $this->gd_support_ttf = $this->gd_info['FreeType Support'];
    }

    private function _file_info( $filename )
    {
        $ext = array( 'b', 'Kb', 'Mb', 'Gb');
        $redondear = 2;

        $this->filepath = $filename;
        $this->filename = basename($filename);
        $this->filesize_bytes = filesize($filename);

        $size = $this->filesize_bytes;

        for ( $i = 0; $size > 1024 && $i < count($ext) - 1 ; $i++ ) {
            $size /= 1024;
        }
        $this->filesize_formatted = round($size,$redondear)." ".$ext[$i];

        $this->original_width   = $this->getWidth();
        $this->original_height  = $this->getHeight();
    }

    private function _blankpng()
    {
        $c  = "iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29m";
        $c .= "dHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAADqSURBVHjaYvz//z/DYAYAAcTEMMgBQAANegcCBNCg";
        $c .= "dyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAAN";
        $c .= "egcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQ";
        $c .= "oHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAADXoHAgTQoHcgQAANegcCBNCgdyBAAA16BwIE0KB3IEAA";
        $c .= "DXoHAgTQoHcgQAANegcCBNCgdyBAgAEAMpcDTTQWJVEAAAAASUVORK5CYII=";

        return $c;
    }
}
