<?php

namespace Core\Utils;


class Image
{
	
	protected $image		= false;
	
	/**
	 * Accepted image extensions
	 * @var array
	 */
	protected static $imgExts = ['image/jpeg', 'image/png'];
	
	
	
	/**
     * @param $format string
     *
     * Check if extentions is accepted
     *
     * @return bool
     */
	public static function isAccepted($format)
	{
		return in_array($format, self::$imgExts);
	}
	
	
	public function __set()
	{
		if ()
	}
	
    /**
     * @param $oldFilename string
     * @param $file \Phalcon\Http\Request\FileInterface
     * @param $path string
     *
     * Upload Image of type jpeg, png
     *
     * @return string
     */
    public function uploadImageFile($oldFilename, $file, $path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $imgExts = array('image/jpeg', 'image/png');

        $filename = '';
        if (in_array($file->getType(), $imgExts)) {
            $parts = pathinfo($file->getName());

            $filename = $parts['filename'] . '_' . md5($file->getName() . date('YmdHis')) . '.' . $parts['extension'];
            $file->moveTo($path . '/' . $filename);
            chmod($path . '/' . $filename, 0777);

            if (!is_dir($path . '/' . $oldFilename) && file_exists($path . '/' . $oldFilename)) {
                unlink($path . '/' . $oldFilename);
            }
        }

        return $filename;
    }
	
}