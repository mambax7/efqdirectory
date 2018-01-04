<?php namespace XoopsModules\Efqdirectory;

use Xmf\Request;
use XoopsModules\Efqdirectory\Common;


require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility extends \XoopsObject
{
    use common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use common\ServerStats; // getServerStats Trait

    use common\FilesManagement; // Files Management Trait
}
