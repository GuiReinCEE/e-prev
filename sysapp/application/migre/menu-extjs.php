<?php
include_once('inc/conexao.php');

if( !function_exists('menu_extjs_start') )
{
	function menu_extjs_start($cd_menu=0, $db)
	{
		$output = "\n[\n";
		$output .= menu_extjs_render($cd_menu, $db);
		$output .= "\n]\n";
	
		return $output;
	}
}

if( !function_exists('menu_extjs_render') )
{
	function menu_extjs_render($cd_menu=0, $db)
	{
		if((int)$cd_menu==0)
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai IS NULL ORDER BY nr_ordem, ds_menu";
		}
		else
		{
			$sql = "SELECT * FROM projetos.menu WHERE dt_desativado is null AND cd_menu_pai=".intval($cd_menu)." ORDER BY nr_ordem, ds_menu";
		}
	
		$query = pg_query($db, $sql);
		$collection = pg_fetch_all($query);
	
		$output = "";
		$atual=0;
		foreach( $collection as  $item )
		{
			$output .= "{";
			$sql_icon = " SELECT COUNT(*) as quantos FROM projetos.menu WHERE cd_menu_pai=".intval($item['cd_menu'])." AND dt_desativado is null; ";
			$query = pg_query($db, $sql_icon);
			$row = pg_fetch_all($query);
	
			// tratamento do link
			$href = "";
			$link="";
	
			if($item['ds_href']=="#")
			{
				$href = "";
			}
			else if( substr($item['ds_href'], 0, 7)=='http://' OR substr($item['ds_href'], 0, 8)=='https://' )
			{
				$href = "window.open('".$item['ds_href']."');";
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
				$href = "cieprev_acesso( '".$item['ds_href']."' );";
			}
	
			// ESSA LINHA ABAIXO EXIBE O CÓDIGO DO ITEM NO PRÓPRIO MENU
			// $output .= " text: '".$item['ds_menu']."( ".$item['cd_menu']." )' \n";
			$output .= " text: '".$item['ds_menu']."' \n";
	
			if($item['ds_icone']!='') $output .= " ,iconCls: '".$item['ds_icone']."' \n";
	
			if(trim($href)!="")
			{
				$output .= " ,handler: function(){ $href } \n";
			}
	
			if((int)$row[0]['quantos']>0)
			{
				$output .= ", menu:{items:[\n";
				$output .= menu_extjs_render( $item['cd_menu'], $db );
				$output .= "\n]}";
			}
	
			$atual++;
			$virgula=(sizeof($collection)==$atual)?"":",";
	
			$output .= "}$virgula \n";
		}
	
		return $output;
	}
}
//echo menu_extjs_start(0, $db);
?>