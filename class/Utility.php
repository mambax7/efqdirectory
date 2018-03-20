<?php namespace XoopsModules\Efqdirectory;

use Xmf\Request;
use XoopsModules\Efqdirectory\Common;

require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility extends \XoopsObject
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait
}
