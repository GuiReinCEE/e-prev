<?php
if ( ! function_exists('menu_start_render'))
{
	function menu_start_render($cd_conteudo=0)
	{
		$CI =& get_instance();
		
		$db1 = $CI->load->database('cjunior', true);
		
		$sql = "SELECT * FROM cjunior.conteudo WHERE fl_ativo='S' AND cd_conteudo=? ORDER BY nr_ordem, ds_titulo";

		$query = $db1->query( $sql, array((int)$cd_conteudo) );
		$collection = $query->result_array();

		$output = "";
		foreach( $collection as $item )
		{
			$sql_icon = "SELECT count(*) as quantos FROM cjunior.conteudo WHERE cd_conteudo_root=? AND fl_ativo='S';";
			$query = $db1->query($sql_icon, array( (int)$item['cd_conteudo'] ));
			$row = $query->row_array();
			$icon = ((int)$row['quantos']>0) ? 'folder' : 'file';

			// ----

			$link = anchor('content/content/detail/'.$item['cd_conteudo'], $item['ds_titulo'] . " (". $item['cd_conteudo'] .")", array('target'=>'detail', 'title'=>$item['ds_titulo'] . " (" . $item['cd_conteudo'] . ")"   ));
			$output .= "<li class='closed'><span class='$icon'>" . $link . "</span>\n";

			if((int)$row['quantos']>0)
			{
				$output .= "<ul>\n";
				$output .= menu_render( $item['cd_conteudo'] );
				$output .= "</ul>\n";
			}

			$output .= "</li>";
		}

		return $output;
	}
}

if ( ! function_exists('menu_render'))
{
	function menu_render($cd_conteudo=0)
	{
		$CI =& get_instance();
		$db=$CI->load->database('cjunior',true);

		if((int)$cd_conteudo==0)
		{
			$sql = "SELECT * FROM cjunior.conteudo WHERE fl_ativo='S' AND cd_conteudo_root IS NULL ORDER BY nr_ordem, ds_titulo";
		}
		else
		{
			$sql = "SELECT * FROM cjunior.conteudo WHERE fl_ativo='S' AND cd_conteudo_root=? ORDER BY nr_ordem, ds_titulo";
		}

		$query = $db->query( $sql, array((int)$cd_conteudo) );
		$collection = $query->result_array();

		$output = "";
		foreach( $collection as $item )
		{
			$sql_icon = "SELECT count(*) as quantos FROM cjunior.conteudo WHERE cd_conteudo_root=? AND fl_ativo='S';";
			$query = $db->query($sql_icon, array( (int)$item['cd_conteudo'] ));
			$row = $query->row_array();
			$icon = ((int)$row['quantos']>0) ? 'folder' : 'file';

			// ----

			//$link = "<a href='#' onclick='load_content();'>load content</a>";
			$link = anchor('content/content/detail/'.$item['cd_conteudo'], $item['ds_titulo'] . " (". $item['cd_conteudo'] .")", array('target'=>'detail', 'title'=>$item['ds_titulo'] . " (" . $item['cd_conteudo'] . ")"   ));
			$output .= "<li class='closed'><span class='$icon'>" . $link . "</span>\n";

			if((int)$row['quantos']>0)
			{
				$output .= "<ul>\n";
				$output .= menu_render( $item['cd_conteudo'] );
				$output .= "</ul>\n";
			}

			$output .= "</li>";
		}

		return $output;
	}
}

if ( ! function_exists('path_content'))
{
	function path_content($cd_conteudo)
	{
		$lista = _path_content((int)$cd_conteudo);
		
		echo $lista;
	}
}

if ( ! function_exists('_path_content'))
{
	function _path_content($cd_conteudo)
	{
		$CI =& get_instance();
		$db=$CI->load->database('cjunior',true);
		
		$output = "";
		
		$sql = "SELECT * FROM cjunior.conteudo WHERE cd_conteudo = ? ORDER BY nr_ordem, ds_titulo";
		
		$query = $db->query($sql, array((int)$cd_conteudo));
		
		if($query)
		{
			$row = $query->row_array();
			if($row)
			{
				$output .= $row['ds_titulo'] . " <b>&gt;</b> ";
				if((int)$row['cd_conteudo_root']>0)
				{
					path_content($row['cd_conteudo_root']);
				}
			}
		}
		
		return $output;
	}
}
?>