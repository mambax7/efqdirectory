<?php
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
class MediaUploader
{
    /**
     * Flag indicating if unrecognized mimetypes should be allowed (use with precaution ! may lead to security issues )
     **/
    public $allowUnknownTypes = false;

    public $mediaName;
    public $mediaType;
    public $mediaSize;
    public $mediaTmpName;
    public $mediaError;
    public $mediaRealType = '';

    public $uploadDir = '';

    public $allowedMimeTypes = [];

    public $maxFileSize = 0;
    public $maxWidth;
    public $maxHeight;

    public $targetFileName;

    public $prefix;

    public $errors = [];

    public $savedDestination;

    public $savedFileName;

    public $extensionToMime = [];

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
        @$this->extensionToMime = include XOOPS_ROOT_PATH . '/class/mimetypes.inc.php';
        if (!is_array($this->extensionToMime)) {
            $this->extensionToMime = [];

            return false;
        }
        if (is_array($allowedMimeTypes)) {
            $this->allowedMimeTypes =& $allowedMimeTypes;
        }
        $this->uploadDir   = $uploadDir;
        $this->maxFileSize = (int)$maxFileSize;
        if (isset($maxWidth)) {
            $this->maxWidth = (int)$maxWidth;
        }
        if (isset($maxHeight)) {
            $this->maxHeight = (int)$maxHeight;
        }
    }

    /**
     * Fetch the uploaded file
     *
     * @param   string $media_name Name of the file field
     * @param   int    $index      Index of the file (if more than one uploaded under that name)
     * @return  bool
     **/
    public function fetchMedia($media_name, $index = null)
    {
        if (empty($this->extensionToMime)) {
            $this->setErrors('Error loading mimetypes definition');

            return false;
        }
        if (!isset($_FILES[$media_name])) {
            $this->setErrors('File not found');

            //echo " - no such file ";
            return false;
        } elseif (is_array($_FILES[$media_name]['name']) && isset($index)) {
            $index              = (int)$index;
            $this->mediaName    = get_magic_quotes_gpc() ? stripslashes($_FILES[$media_name]['name'][$index]) : $_FILES[$media_name]['name'][$index];
            $this->mediaType    = $_FILES[$media_name]['type'][$index];
            $this->mediaSize    = $_FILES[$media_name]['size'][$index];
            $this->mediaTmpName = $_FILES[$media_name]['tmp_name'][$index];
            $this->mediaError   = !empty($_FILES[$media_name]['error'][$index]) ? $_FILES[$media_name]['errir'][$index] : 0;
        } else {
            $media_name         =& $_FILES[$media_name];
            $this->mediaName    = get_magic_quotes_gpc() ? stripslashes($media_name['name']) : $media_name['name'];
            $this->mediaName    = $media_name['name'];
            $this->mediaType    = $media_name['type'];
            $this->mediaSize    = $media_name['size'];
            $this->mediaTmpName = $media_name['tmp_name'];
            $this->mediaError   = !empty($media_name['error']) ? $media_name['error'] : 0;
        }
        if (false !== ($ext = strrpos($this->mediaName, '.'))) {
            $ext = substr($this->mediaName, $ext + 1);
            if (isset($this->extensionToMime[$ext])) {
                $this->mediaRealType = $this->extensionToMime[$ext];
                //trigger_error( "XoopsMediaUploader: Set mediaRealType to {$this->mediaRealType} (file extension is $ext)", E_USER_NOTICE );
            }
        }
        $this->errors = [];
        if ($ext && in_array($ext, ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'xbm'])) {
            // Prevent sending of invalid images that would crash IE
            if (!($info = getimagesize($this->mediaTmpName))) {
                $this->setErrors('Invalid file content');

                return false;
            }
        }
        if ((int)$this->mediaSize < 0) {
            $this->setErrors('Invalid File Size');

            return false;
        }
        if ('' === $this->mediaName) {
            $this->setErrors('Filename Is Empty');

            return false;
        }
        if ('none' === $this->mediaTmpName || !is_uploaded_file($this->mediaTmpName)) {
            $this->setErrors('No file uploaded');

            return false;
        }
        if ($this->mediaError > 0) {
            $this->setErrors('Error occurred: Error #' . $this->mediaError);

            return false;
        }

        return true;
    }

    /**
     * Set the target filename
     *
     * @param   string $value
     **/
    public function setTargetFileName($value)
    {
        $this->targetFileName = trim($value);
    }

    /**
     * Set the prefix
     *
     * @param   string $value
     **/
    public function setPrefix($value)
    {
        $this->prefix = trim($value);
    }

    /**
     * Get the uploaded filename
     *
     * @return  string
     **/
    public function getMediaName()
    {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return  string
     **/
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return  int
     **/
    public function getMediaSize()
    {
        return $this->mediaSize;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return  string
     **/
    public function getMediaTmpName()
    {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return  string
     **/
    public function getSavedFileName()
    {
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return  string
     **/
    public function getSavedDestination()
    {
        return $this->savedDestination;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @param int $chmod
     * @return bool
     */
    public function upload($chmod = 0644)
    {
        if ('' === $this->uploadDir) {
            $this->setErrors('Upload directory not set');

            return false;
        }
        if (!is_dir($this->uploadDir)) {
            $this->setErrors('Failed opening directory: ' . $this->uploadDir);
        }
        if (!is_writable($this->uploadDir)) {
            $this->setErrors('Failed opening directory with write permission: ' . $this->uploadDir);
        }
        if (!$this->checkMaxFileSize()) {
            $this->setErrors('File size too large: ' . $this->mediaSize);
        }
        if (!$this->checkMaxWidth()) {
            $this->setErrors(sprintf('File width must be smaller than %u', $this->maxWidth));
        }
        if (!$this->checkMaxHeight()) {
            $this->setErrors(sprintf('File height must be smaller than %u', $this->maxHeight));
        }
        if (!$this->checkMimeType()) {
            $this->setErrors('MIME type not allowed: ' . $this->mediaType);
        }
        if (count($this->errors) > 0) {
            return false;
        }
        if (!$this->_copyFile($chmod)) {
            $this->setErrors('Failed uploading file: ' . $this->mediaName);

            return false;
        }

        return true;
    }

    /**
     * Copy the file to its destination
     *
     * @param $chmod
     * @return bool
     */
    public function _copyFile($chmod)
    {
        $matched = [];
        if (!preg_match("/\.([a-zA-Z0-9]+)$/", $this->mediaName, $matched)) {
            return false;
        }
        if (isset($this->targetFileName)) {
            $this->savedFileName = $this->targetFileName;
        } elseif (isset($this->prefix)) {
            $this->savedFileName = uniqid($this->prefix, true) . '.' . strtolower($matched[1]);
        } else {
            $this->savedFileName = strtolower($this->mediaName);
        }
        $this->savedDestination = $this->uploadDir . '/' . $this->savedFileName;
        if (!move_uploaded_file($this->mediaTmpName, $this->savedDestination)) {
            return false;
        }
        @chmod($this->savedDestination, $chmod);

        return true;
    }

    /**
     * Is the file the right size?
     *
     * @return  bool
     **/
    public function checkMaxFileSize()
    {
        if ($this->mediaSize > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return  bool
     **/
    public function checkMaxWidth()
    {
        if (!isset($this->maxWidth)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[0] > $this->maxWidth) {
                return false;
            }
            //$result = $dimension[0];
            //$this->width = $result;
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max width check..', $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
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

    /**
     * Is the picture the right height?
     *
     * @return  bool
     **/
    public function checkMaxHeight()
    {
        if (!isset($this->maxHeight)) {
            return true;
        }
        if (false !== $dimension = getimagesize($this->mediaTmpName)) {
            if ($dimension[1] > $this->maxHeight) {
                return false;
            }
        } else {
            trigger_error(sprintf('Failed fetching image size of %s, skipping max height check..', $this->mediaTmpName), E_USER_WARNING);
        }

        return true;
    }

    /**
     * Check whether or not the uploaded file type is allowed
     *
     * @return  bool
     **/
    public function checkMimeType()
    {
        if (empty($this->mediaRealType) && !$this->allowUnknownTypes) {
            $this->setErrors('Unknown filetype rejected');

            return false;
        }

        return (empty($this->allowedMimeTypes) || in_array($this->mediaRealType, $this->allowedMimeTypes));
    }

    /**
     * Add an error
     *
     * @param   string $error
     **/
    public function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

    /**
     * Get generated errors
     *
     * @param    bool $ashtml Format using HTML?
     *
     * @return    array|string    Array of array messages OR HTML string
     */
    public function &getErrors($ashtml = true)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
            $ret = '';
            if (count($this->errors) > 0) {
                $ret = '<h4>Errors Returned While Uploading</h4>';
                foreach ($this->errors as $error) {
                    $ret .= $error . '<br>';
                }
            }

            return $ret;
        }
    }
}
