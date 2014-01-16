<?php
/**
 * ZFImage
 *
 * @link      http://github.com/elialejandro/zfimage2 for the canonical source repository
 * @copyright Copyright (c) 2010-2014 Eli Alejandro
 * @license   https://github.com/elialejandro/zfimage2/blob/master/license.md New BSD License
 */

namespace ZFImage\Plugin;

interface PluginInterface
{
    public function attachToOwner( $owner );
    public function getTypeId();
    public function getSubTypeId();
    public function getVersion();
    public function generate();
}