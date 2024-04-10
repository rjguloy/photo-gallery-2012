<?php
//Page Filename: test-image.php
/*
*
*This page will simply upload an image file
*and resize it in a specified maximum length
*with a thumbnail to be created after it
*and save the image information to the
*database table.
*
*@author Robert John Guloy <bobguloy_is@yahoo.com>
*@copyright 2012 Robert John Guloy
*@license http://www.php.net/license/3_0.txt
*/

//Requires initialize.inc.php
if (file_exists('includes/initialize.inc.php')) include_once('includes/initialize.inc.php');

//Include the commonly used functions
if (file_exists('includes/functions_common.php')) include_once('includes/functions_common.php');

//Include the DB Connection
if (file_exists('includes/class_database.php')) include_once('includes/class_database.php');

//Include the class that will resize the uploaded image
if (file_exists('includes/class_image_resizer.php')) include_once('includes/class_image_resizer.php');


$db = new Database;
$db->connect();

$refresh = FALSE;

//If the form was submitted
if (isset($_POST['submit']))
{
    if ( ! empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0)
    {
        //create an instance of the class and define the properties
        $img = new Image_resizer;
        $img->file_name = $_FILES['image']['name'];
        $img->tmp_file = $_FILES['image']['tmp_name'];
        $img->file_type = $_FILES['image']['type'];
        $img->file_error = $_FILES['image']['error'];
        $img->new_height = 500;
        $img->new_width = 500;
        $img->size_as_max = TRUE;
        $img->save_path = ABSOLUTE_PATH . IMAGE_PATH;

        $result = $img->set_image_size();

        //create a thumbnail if the first resize was successful
        if ($result)
        {
            $tmb = new Image_resizer;
            $tmb->file_name = $img->file_name;
            $tmb->tmp_file = $img->tmp_file;
            $tmb->file_type = $img->file_type;
            $tmb->file_error = $img->file_error;
            $tmb->new_height = 75;
            $tmb->new_width = 75;
            $tmb->size_as_max = FALSE;
            $tmb->save_path = ABSOLUTE_PATH . THUMB_PATH;

            $result = $tmb->set_image_size();

            if ( ! $result)
            {
                unlink($img->save_path . $img->file_name);
                $_SESSION['sys_message'] = 'Failed to write thumbnail. File not saved.';
            }
            else
            {
                if ( ! $db->link)
                {
                    unlink($img->save_path . $img->file_name);
                    $_SESSION['sys_message'] = 'DB connection is lost. File not saved.';
                }
                else
                {
                    $file = escape_string($img->file_name);
                    $type = escape_string($img->file_type);
                    $title = escape_string($_POST['title']);
                    $desc = escape_string($_POST['desc']);

                    $sql = "INSERT INTO `tbl_images` (`image_filename`, `image_type`, `image_title`, `image_description`)
                    VALUES ('{$file}', '{$type}', '{$title}', '{$desc}')";

                    if (mysqli_query($db->link, $sql))
                    {
                        $_SESSION['sys_message'] = 'The Image was successfully uploaded.';
                    }
                    else
                    {
                        unlink($img->save_path . $img->file_name);
                        unlink($tmb->save_path . $tmb->file_name);
                        $_SESSION['sys_message'] = mysql_error();
                    }
                }
            }
            $tmb= NULL;
        }
        $img = NULL;
    }
    else
    {
        $_SESSION['sys_message'] = 'No File uploaded.';
    }
    //reload the page to clear form resubmission
    header('Location:'.PAGE_FILE);
    //set refresh to TRUE so $_SESSION['sys_message']
    //wont be unset for display after refresh
    $refresh = TRUE;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Sample Image Gallery Upload and Resize</title>
        <style>
            body { margin:0px; font-family:Tahoma, Geneva, sans-serif; background-color:#CCC; }
            label { display:inline-block; width:100px; }
            fieldset { background-color:#555; color:#FFF; border-radius:10px; padding:15px;}
            input[type=text], input[type=file] { width:250px; border-radius:5px; border:1px solid #FF6; }
            input[type=submit] { padding:5px 15px; font-weight:bold; border:1px solid #88F; border-radius:5px; }
            #header { padding:30px 0px; margin-bottom:10px;}
            #wrapper { width:970px;	margin:0px auto; padding:20px; background-color:#FFF; border-radius:10px; }
            #footer	{ position:fixed; bottom:0px; width:100%; padding:15px 0px; z-index:10;	}
            #error { background-color:#900; color:#FFF; padding:10px; margin:20px; }
            .section { background-color:#66F; color:#FFF; text-align:center; }
            .hr { border-bottom:1px solid #999; margin:10px 0px; }
            .sub-header { text-align:left; padding:5px; margin:10px 0px; border-radius:10px; }
            .img { float:left; margin:5px; width:75px; height:75px; border:1px solid #333; border-radius:5px; }
            .cl { clear:both; }
        </style>
    </head>

    <body>
        <div id="header" class="section">
            <h1>Sample Image Uploader By Robert John Guloy</h1>
        </div>
        <div id="wrapper">
            <!--Display system message if there is any-->
            <?php echo ( ! empty($_SESSION['sys_message']) ? '<div id="error">Message : '.$_SESSION['sys_message'].'</div>' : ''); ?>
            <!--End of system message-->
            <form method= "post"  action= " <?php echo PAGE_FILE; ?>" enctype="multipart/form-data">
                <fieldset>
                <div><label for="image">Image File</label><input type="file" name="image" /></div>
                <div><label for="title">Title</label><input type="text" name="title" /></div>
                <div><label for="desc">Description</label><input type="text" name="desc" /></div>
                <div class="hr"></div>
                <input type="submit" class="section" name="submit" value=" Upload Image " />
                </fieldset>
            </form>
            <h2 class="section sub-header">Uploaded Images</h2>
            <div class="cl"></div>
            <?php
            if ($db ->link )
            {
                $sql = "SELECT `image_filename` AS `file` FROM `tbl_images`";
                $result = mysqli_query ($db ->link, $sql);
                $count = mysqli_num_rows  ($result );

                if ($count )
                {
                    while ($image = mysqli_fetch_array($result ))
                    {
                        ?>
                        <a href= " <?php echo IMAGE_PATH . $image['file']?>" target="_blank">
                        <div class="img" style="background:url(<?php echo THUMB_PATH . $image['file']; ?>) center"></div>
                        </a>
                        <?php
                    }
                }
                else { echo '<p>There are no Images uploaded at this time.</p>'; }
            }
            ?>
            <div class= "cl" ></div>
        </div> <!--End of #wrapper-->
        <div id= "footer"  class= "section"  >
            Copyright  &copy; 2012 &bull; Robert John Guloy
        </div>
        <?php
        //unset $_SESSION['sys_message'] after the page reloads and displays the message.
        if (! $refresh ) unset ($_SESSION['sys_message' ]);

        //Close the database connection
        mysqli_close ($db ->link );
        ?>
    </body>
</html>


