<?php
/**
 * Método para criação dinamica de includes
 *
 * @param string $names
 */
function using( $names )
{
	$location = 'oo/db/';
	
	$foldersys = array( '_super', '_interfaces', '_drivers' );
	
	//list files this folder
	// LIST SYSTEM FOLDER PRIORITY 0
	$sys = opendir( $location.$foldersys[0] );
	while ($file = readdir($sys))
	{
		if( preg_match("/\.php/", $file) )
		{
			// echo '<br>include ' . $path . $folder . '/' . $file;
			include $location.$foldersys[0] . '/' . $file;
		}
	}
	// LIST SYSTEM FOLDER PRIORITY 1
	$sys = opendir( $location.$foldersys[1] );
	while ($file = readdir($sys))
	{
		if( preg_match("/\.php/", $file) )
		{
			// echo '<br>include ' . $path . $folder . '/' . $file;
			include $location.$foldersys[1] . '/' . $file;
		}
	}
	// LIST SYSTEM FOLDER PRIORITY 2
	$sys = opendir( $location.$foldersys[2] );
	while ($file = readdir($sys))
	{
		if( preg_match("/\.php/", $file) )
		{
			// echo '<br>include ' . $path . $folder . '/' . $file;
			include $location.$dir. '/' .$foldersys[2] . '/' . $file;
		}
	}

	$all = opendir($location);
	while($dir = readdir($all))
	{
		$folder = $location.$dir;
		if (is_dir($folder))
		{
			// includes for folder in the project
			if( ! preg_match("/_/", $folder) )
			{
				foreach($names as $name)
				{
					$parts = preg_split("/\./", $name);

					if( preg_match("/".$parts[0]."/", $folder ) )
					{
						// list files this folder
						if(file_exists($folder . '/' . $parts[1]))
						{
							$files2 = opendir($folder . '/' . $parts[1]);
							while ($file2 = readdir($files2))
							{
								if (is_dir($folder))
								{
									//$files3 = opendir($path . $folder . '/' . $file2);
									//while ($file3 = readdir($files3))
									//{
										if( preg_match("/\.php/", $file2) )
										{
											//echo '<br>include ' . $path . $folder . '/' . $file2;
											include $path . $folder . '/' . $parts[1] . '/' . $file2;
										}
									//}
								}
							}
						}
						else
						{
							echo '<br />esquema oo <b>"' . $name . '"</b> não existe<br />';
						}
					}
				}
			}
		}
	}
	if(isset($all))
	{
		closedir($all);
		unset($all);	
	}
	if(isset($files))
	{
		closedir($files);
		unset($files);
	}
}
?>