<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
http://codeigniter.com/wiki/Listfiles/

List all files in given directory that match a given extension(s).
application/libraries/Listfiles.php 

in your controller:
$this->load->library('listfiles', array('jpg', 'jpeg', 'csv'));
$data['files'] = $this->listfiles->getFiles('dir/to/search');  
*/

class Listfiles{
    var $dir= '.';
    var $filter = false;
    var $filetype = array();
    var $files = array();

    function Listfiles($ext=false)
	{
        #log_message('debug', 'Listfiles Class Initialized.');
        $args = $ext; //func_get_args();
        $this->filter = (count($args))?true: false;
        if($this->filter)
		{
            foreach($args as $e)
			{
                array_push($this->filetype, $e);
            }
        }
        return($this->filetype);
    }
    
    function setDir($dir = false)
	{
        $this->dir = trim($dir);
		
		if(is_dir($this->dir))
		{
            return true;
        }
        return false;
    }
    
    function getFiles($dir = false)
	{
		if($this->setDir($dir))
		{
			$handle = @opendir($this->dir);
            if($handle)
			{
                while (false !== ($file = readdir($handle))) 
				{
                    if ($file != "." && $file != "..") 
					{
                        if(is_file($this->dir . "/" . $file))
						{
                            $fileinfo = pathinfo($this->dir . "/" . $file);
                            foreach($this->filetype as $type)
							{
                                if($type == $fileinfo['extension'])
								{
                                    $t['file']  = $this->dir . "/" . $file;
									$ar_tmp = explode('.', $file);
									$e = $ar_tmp[count($ar_tmp) -1];
									$nr_e = -1 * (strlen($e) + 1);
									$f = substr($file, 0, $nr_e);
                                    $t['file_name'] = $f.".".$e;										
                                    $t['name'] = $f;										
                                    $t['ext'] = $e;										
                                    $t['id_file'] = md5($f.".".$e);										
									$t['date_en']  = date("Y-m-d H:i:s", filectime($this->dir . "/" . $file));
									$t['date_br']  = date("d/m/Y H:i:s", filectime($this->dir . "/" . $file));
									$t['size']  = filesize($this->dir . "/" . $file);
								
                                    array_push($this->files,$t);
                                }
                            }
                        }
                    }
                }
                closedir($handle);
                return($this->files);
            }
        } 
		else 
		{
			log_message('error', 'Listfiles Class -> Not a valid directory resource: ' . $this->dir);
            return (array());
        }
    }
}
?>