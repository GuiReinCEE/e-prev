<?php
if( !function_exists('menu_mais_usados') )
{
	function menu_mais_usados()
	{
		$CI =& get_instance();

		$sql = "
			SELECT count(*) as nr, l.prog, t.desc_programa
			FROM projetos.log_acessos l, projetos.telas_eprev t
			WHERE cd_usuario = ?
			AND l.prog = t.nome_programa
			GROUP BY prog, t.desc_programa
			ORDER BY nr DESC
			LIMIT 10
		";

		$query = $CI->db->query( $sql, array((int)$CI->session->userdata("codigo")) );
		$collection = $query->result_array();

		$output = "<ul>";
		foreach( $collection as $item )
		{
			$output .= "<li><a href='http://".$_SERVER['SERVER_NAME']."/controle_projetos/".$item['prog']."'>".$item['desc_programa']."</a></li>";
		}
		$output .= "</ul>";

		return $output;
	}
}

if( !function_exists('menu_classic_start') )
{
	function menu_classic_start($cd_menu=0)
	{
		$output = "<ul>";
		$output .= menu_classic_render($cd_menu);
		$output .= "</ul>";

		return $output;
	}
}

if ( ! function_exists('menu_classic_render'))
{
	function menu_classic_render($cd_menu=0)
	{
		$CI =& get_instance();

		if((int)$cd_menu==0)
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai IS NULL ORDER BY nr_ordem, ds_menu";
		}
		else
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai=? ORDER BY nr_ordem, ds_menu";
		}

		$query = $CI->db->query( $sql, array((int)$cd_menu) );
		$collection = $query->result_array();

		$output = "";
		foreach( $collection as $item )
		{
			$sql_icon = "SELECT count(*) as quantos FROM projetos.menu WHERE cd_menu_pai=? AND dt_desativado is null;";
			$query = $CI->db->query($sql_icon, array( (int)$item['cd_menu'] ));
			$row = $query->row_array();

			// tratamento do link
			$onclick = "";
			$href = "href='javascript:void(null)'";
			$target = "";
			$link="";

			if($item['ds_href']=="#")
			{
				$href = "<a href='#'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}
			else if( substr($item['ds_href'], 0, 7)=='http://' OR substr($item['ds_href'], 0, 8)=='https://' )
			{
				$href = "<a href='".$item['ds_href']."' target='_blank'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}
			else if( strpos( $item['ds_href'],'.php') )
			{
				$protocolo = (isset($_SERVER['HTTPS']))?"https":"http";
				$link = $protocolo . "://" . $_SERVER['SERVER_NAME'] . "/controle_projetos/" . $item['ds_href'];
				$href = "<a href='$link'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}
			else
			{
				$link = base_url() . 'index.php/' . $item['ds_href'];
				$href = "<a href='$link'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}

			$output .= "<li>" . $href . "\n";

			if((int)$row['quantos']>0)
			{
				$output .= "<ul>\n";
				$output .= menu_classic_render( $item['cd_menu'] );
				$output .= "</ul>\n";
			}

			$output .= "</li>";
		}

		return $output;
	}
}

if( !function_exists('menu_extjs_start') )
{
	function menu_extjs_start($cd_menu=0)
	{
		$output = "\n[\n";
		$output .= menu_extjs_render($cd_menu);
		$output .= "\n]\n";

		return $output;
	}
}

if( ! function_exists('menu_extjs_render') )
{
	function menu_extjs_render($cd_menu=0)
	{
		$CI =& get_instance();

		if((int)$cd_menu==0)
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai IS NULL ORDER BY nr_ordem, ds_menu";
		}
		else
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai=? ORDER BY nr_ordem, ds_menu";
		}

		$query = $CI->db->query( $sql, array((int)$cd_menu) );
		$collection = $query->result_array();

		$output = "";
		$atual=0;
		foreach( $collection as $item )
		{
			$output .= "{";
			$sql_icon = " SELECT COUNT(*) as quantos FROM projetos.menu WHERE cd_menu_pai=? AND dt_desativado is null; ";
			$query = $CI->db->query($sql_icon, array( (int)$item['cd_menu'] ));
			$row = $query->row_array();

			// tratamento do link
			$href = "";
			$link="";

			if((trim($item['ds_href']) == "#") or (trim($item['ds_href']) == ""))
			{
				$href = "";
			}
			else if( substr($item['ds_href'], 0, 7)=='http://' OR substr($item['ds_href'], 0, 8)=='https://' )
			{
				$href = "window.open('".$item['ds_href']."');";
				// $href = "<a href='".$item['ds_href']."' target='_blank'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}
			else if( strpos( $item['ds_href'],'.php') )
			{
				$protocolo = (isset($_SERVER['HTTPS']))?"https":"http";
				$link = base_url_eprev() . $item['ds_href'];
				$href = "location.href='$link'";
			}
			else
			{
				$link = base_url() . index_page() . '/' . $item['ds_href'];
				$href = "location.href='$link'";
				//$href = "<a href='$link'>".$item['ds_menu']." (".$item['cd_menu'].")</a>";
			}

			// ESSA LINHA ABAIXO EXIBE O CÓDIGO DO ITEM NO PRÓPRIO MENU
			// $output .= " text: '".$item['ds_menu']."( ".$item['cd_menu']." )' \n";
			$output .= " text: '".$item['ds_menu']."' \n";

			if($item['ds_icone']!='') $output .= " ,iconCls: '".$item['ds_icone']."' \n";

			if(trim($href)!="")
			{
				$output .= " ,handler: function(){ $href } \n";
			}
			else
			{
				$output .= " ,handler: function(){ return false; } \n";
			}

			if((int)$row['quantos']>0)
			{
				$output .= ", menu:{items:[\n";
				$output .= menu_extjs_render( $item['cd_menu'] );
				$output .= "\n]}";
			}

			$atual++;
			$virgula=(sizeof($collection)==$atual)?"":",";

			$output .= "}$virgula \n";
		}

		return $output;
	}
}

if( ! function_exists('test_number') )
{
	function test_number($cd_menu=2, $level)
	{
		$CI =& get_instance();

		$sql = "
		SELECT * 
		FROM projetos.menu 
		WHERE dt_desativado is null AND cd_menu_pai=? 
		ORDER BY nr_ordem, ds_menu
		";

		$query = $CI->db->query( $sql, array((int)$cd_menu) );
		$collection = $query->result_array();

		$output = "";
		$contador=0;
		foreach( $collection as $item )
		{
			$contador++;
			echo $level . '.' . $contador . "-" . $item['ds_menu'] . "<br />";

			test_number( $item['cd_menu'], $level . "." . $contador );
		}
	}
}
?>