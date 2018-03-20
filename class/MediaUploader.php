<?php namespace XoopsModules\Efqdirectory;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

/*!
Example

require_once __DIR__ . '/uploader.php';
$allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
$maxfilesize = 50000;
$maxfilewidth = 120;
$maxfileheight = 120;
$uploader = new \XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
if (!$uploader->upload()) {
echo $uploader->getErrors();
} else {
echo '<h4>File uploaded successfully!</h4>'
echo 'Saved as: ' . $uploader->getSavedFileName() . '<br>';
echo 'Full path: ' . $uploader->getSavedDestination();
}
} else {
echo $uploader->getErrors();
}

*/

/**
 * Upload Media files
 *
 * Example of usage:
 * <code>
 * require_once __DIR__ . '/uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new \XoopsMediaUploader('/home/xoops/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
 *   if (!$uploader->upload()) {
 *      echo $uploader->getErrors();
 *   } else {
 *      echo '<h4>File uploaded successfully!</h4>'
 *      echo 'Saved as: ' . $uploader->getSavedFileName() . '<br>';
 *      echo 'Full path: ' . $uploader->getSavedDestination();
 *   }
 * } else {
 *   echo $uploader->getErrors();
 * }
 * </code>
 *
 * @package          kernel
 * @subpackage       core
 *
 * @author           Kazumi Ono     <onokazu@xoops.org>
 * @copyright    (c) 2000-2003 The Xoops Project - www.xoops.org
 */

//require_once XOOPS_ROOT_PATH . '/class/uploader.php';
xoops_load('XoopsMediaUploader');

/**
 * Class MediaUploader
 */
class MediaUploader extends \XoopsMediaUploader
{

    /**
     * Constructor
     *
     * @param   string $uploadDir
     * @param   array  $allowedMimeTypes
     * @param   int    $maxFileSize
     * @param   int    $maxWidth
     * @param   int    $maxHeight
     * @internal param int $cmodvalue
     */
    public function __construct($uploadDir, $allowedMimeTypes, $maxFileSize = 0, $maxWidth = null, $maxHeight = null)
    {
        parent::__construct($uploadDir, $allowedMimeTypes, $maxFileSize, $maxWidth, $maxHeight);
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        //$filename = $imgloc."/";
        $filename = $this->uploadDir . '/';
        $filename .= $this->savedFileName;
        if (false !== $dimension = getimagesize($filename)) {
            if ($dimension[0] > 0) {
                $result = $dimension[0];
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, image width unknown..', $this->mediaTmpName), E_USER_WARNING);
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        //$filename = $imgloc."/";
        $filename = $this->uploadDir . '/';
        $filename .= $this->savedFileName;
        if (false !== $dimension = getimagesize($filename)) {
            if ($dimension[1] > 0) {
                $result = $dimension[1];
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, image height unknown..', $this->mediaTmpName), E_USER_WARNING);
        }

        return $result;
    }
}
