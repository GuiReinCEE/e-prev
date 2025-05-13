<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menulib
{
	private $CI;
	function Menulib()
	{
		$this->CI =& get_instance();
		$b=$this->get_id_by_path();
	}
	
	function get_id_by_path( $path = "" )
	{
		if($path=="")
		{
			$qs = "";
			if( isset($_SERVER['QUERY_STRING']) )
			{
				if( $_SERVER['QUERY_STRING']!="" )
				{
					$qs = "?" . $_SERVER['QUERY_STRING'];
				}
			}
			$path = $_SERVER['PHP_SELF'] . $qs;
			// echo $this->menu->get_id_by_path();
				
		}
		
		$path = str_replace( "/controle_projetos/", "", $path );
		$path = str_replace( "/cieprev/index.php/", "", $path );
		
		$id = 3;
		
		$q = $this->CI->db->query("
			SELECT cd_menu
			FROM projetos.menu 
			WHERE ds_href = " . $this->CI->db->escape($path) . "
		");
		
		if( sizeof($q->result())>0 )
		{
			$r = $q->row_array();
			$id = $r['cd_menu'];
		}
		else
		{
			$apath = explode("?", $path);
			
			if(sizeof($apath)==2)
			{
				$page = $apath[0];
				$qs = $apath[1];
			}
			else
			{
				$page = $apath[0];
				$qs = "";
			}
			
			$q = $this->CI->db->query("
				SELECT cd_menu, ds_href
				FROM projetos.menu 
				WHERE ds_href LIKE " . $this->CI->db->escape($page) . " || '%'
			");
			
			if( sizeof($q->result())==1 )
			{
				$r = $q->row_array();
				$id = $r['cd_menu'];
			}
			else
			{
				if($qs!="")
				{
					$aqs = explode("&", $qs);
					$first = "";
					$default = "";
					$find = "";
					foreach( $q->result_array() as $r )
					{
						$ahref = explode( "?", $r['ds_href'] );

						if(sizeof($ahref)==2)
						{
							$menu_page = $ahref[0];
							$menu_qs = $ahref[1];
							
							$amenu_qs = explode("&", $menu_qs);
							$aintersect = array_intersect( $amenu_qs, $aqs );
							if(sizeof($aintersect)==sizeof($amenu_qs))
							{
								$find=$r['cd_menu'];
							}
						}
						else
						{
							$menu_page = $ahref[0];
							$menu_qs = "";
							$default = $r['cd_menu'];
						}
					}

					if($find=="") $find=$default;
					if($find=="") $find=$first;

					$id = $find;
				}
			}
		}
		if($id)
		{
			$_SESSION['cd_menu'] = $id;
			$this->CI->session->set_userdata(array('cd_menu'=>$id));
		}
		return $id;
	}
}
?>