<?php
/**
 * ZFImage
 *
 * @link      http://github.com/elialejandro/zfimage2 for the canonical source repository
 * @copyright Copyright (c) 2010-2014 Eli Alejandro
 * @license   https://github.com/elialejandro/zfimage2/blob/master/license.md New BSD License
 */

namespace ZFImage\Plugin;

class Base implements PluginInterface
{
    /**
     * Imagen
     * @var ZFImage\Image
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
