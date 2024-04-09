<?php
//<------start contents of class_image_resizer.php

/*
*
*This class will function as an image resizer of an uploaded image
*
*
*
*
*@author Robert John Guloy <bobguloy_is@yahoo.com>
*@copyright 2012 Robert John Guloy
*@license http://www.php.net/license/3_0.txt
*/

class Image_resizer
{
    //Set the $_FILES to these variables
    var $file_name;
    var $tmp_file;
    var $file_type;
    var $file_error;
    //Set the new image dimensions
    var $new_width;
    var $new_height;
    //Set $size_as_max as TRUE if larger image dimansion should not go larger the new dimensions
    //Else set as FALSE if shorter image dimension should not go smaller than the new dimensions
    var $size_as_max = TRUE;
    //Set the destination path of the image
    var $save_path;


    public function is_valid_image_type($type)
    {
        if ($type == 'image/pjpeg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/gif')
        {
            return TRUE;
        }
        else
        {
            $_SESSION['sys_message'] = 'The file uploaded is not a valid image file.';
            return FALSE;
        }
    }


private function _is_initialized()
{
    if ($this->file_error != 0) { $_SESSION['sys_message'] = 'An error occurred uploading the file.'; return FALSE; }
    if (empty($this->file_name)) { $_SESSION['sys_message'] = 'There is no filename specified.'; return FALSE; }
    if (empty($this->tmp_file)) { $_SESSION['sys_message'] = 'There is no temporary file specified.'; return FALSE; }
    if (empty($this->file_type)) { $_SESSION['sys_message'] = 'There is no file type specified.'; return FALSE; }
    if (empty($this->save_path)) { $_SESSION['sys_message'] = 'There is no directory specified to save the file.'; return FALSE; }

    if (empty($this->new_width) || empty($this->new_height))
    {
        $_SESSION['sys_message'] = 'There are no new dimensions specified to the file.';
        return FALSE;
    }

    if ( ! $this->is_valid_image_type($this->file_type))
    {
        return FALSE;
    }

    return TRUE;
}


    private function _make_image($source, $file, $quality = 100)
    {
        switch ($this->file_type)
        {
            case 'image/pjpeg':

            case 'image/jpeg':
                $result = imagejpeg($source, $file, $quality);
                break;
            case 'image/png':
                $result = imagepng($source, $file, $quality);
                break;
            case 'image/gif':
                $result = imagegif($source, $file);
                break;
        }

        if ( ! $result)
        {
            $_SESSION['sys_message'] = 'Failed to write the uploaded image to the destination folder.';
            return FALSE;
        }

        return TRUE;
    }


    public function set_image_size()
    {
        if ( ! $this->_is_initialized())
        {
            //If the variables are not initialized
            return FALSE;
        }

        // Create an Image from it so we can do the resize
        switch ($this->file_type)
        {
            case 'image/pjpeg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($this->tmp_file);
                break;
            case 'image/png':
                $image = imagecreatefrompng($this->tmp_file);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($this->tmp_file);
                break;
        }

        if ( ! $image)
        {
            $_SESSION['sys_message'] = 'The uploaded file is not a valid &quot;' . $this->file_type . '&quot; file.';
            return FALSE;
        }

        // Capture the original size of the uploaded image
        list($old_width, $old_height) = getimagesize($this->tmp_file);

        if ($this->size_as_max === TRUE)
        {
            //This condition is to set the greater length in width or height as the MAX length
            if ($old_width >= $old_height)
            {
                //The image is a landscape or equal square
                //Set the width as the largest
                $height = ($old_height / $old_width) * $this->new_width;
                $width = $this->new_width;
            }
            else
            {
                //The image is a portrait
                //Set the height as the largest
                $width = ($old_width / $old_height) * $this->new_height;
                $height = $this->new_height;
            }
        }
        else
        {
            //This condition is to set the lesser length in width or height as the MIN length
            if ($old_width >= $old_height)
            {
                //The image is a landscape or equal square
                //Set the height as the smallest
                $width = ($old_width / $old_height) * $this->new_height;
                $height = $this->new_height;
            }
            else
            {
                //The image is a portrait
                //Set the width as the smallest
                $height = ($old_height/$old_width)*$this->new_width;
                $width = $this->new_width;
            }
        }

        $source = imagecreatetruecolor($width, $height);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if(($this->file_type == 'image/png') OR ($this->file_type == 'image/gif'))
        {
            imagealphablending($source, FALSE);
            imagesavealpha($source, TRUE);
            $transparent = imagecolorallocatealpha($source, 255, 255, 255, 127);
            imagefilledrectangle($source, 0, 0, $width, $height, $transparent);
        }

        // this line actually does the image resizing, copying from the original
        // image into the $tmp image
        imagecopyresampled($source, $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
        // now write the resized image to disk. I have assumed that you want the
        // resized, uploaded image file to reside in the ./images subdirectory.
        $this->file_name = format_filename($this->file_name);
        $filename = $this->save_path . $this->file_name;

        $success = $this->_make_image($source, $filename, 100);

        imagedestroy($image);
        imagedestroy($source);

        if ( ! $success)
        {
            return FALSE;
        }

        $_SESSION['sys_message'] = 'Image has been uploaded and resized.';
        return $success;

    }
}

//<------end contents of class_image_resizer.php