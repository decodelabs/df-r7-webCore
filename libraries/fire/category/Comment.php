<?php 
/**
 * This file is part of the Decode Framework
 * @license http://opensource.org/licenses/MIT
 */
namespace df\fire\category;

use df;
use df\core;
use df\fire;

if(!df\Launchpad::$loader || !df\Launchpad::$loader->hasPackage('nightfireCore')) {
    return;
}
    
class Comment extends Base {

    protected static $_requiredOutputTypes = ['Html'];
    protected static $_defaultBlocks = ['SimpleTags', 'Markdown', 'BBCode'];
}