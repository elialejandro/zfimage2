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
 * @version     $Id: Base.php 7 2011-04-15 22:24:23Z rurouni $
 *
 */

/**
 * @see ZFImage_Plugin_Interface
 */
require_once 'ZFImage/Plugin/Interface.php';

class ZFImage_Plugin_Base implements ZFImage_Plugin_Interface
{
    /**
     * Imagen
     * @var ZFImage_Image
     */
    public $_owner = null;

    public function attachToOwner( $owner )
    {
        $this->_owner = $owner;
    }

    public function getTypeId()
    {
        return $this->_type_id;
    }

    public function getSubTypeId()
    {
        return $this->_sub_type_id;
    }

    public function getVersion()
    {
        return $this->_version;
    }
    public function generate(){

    }
}
