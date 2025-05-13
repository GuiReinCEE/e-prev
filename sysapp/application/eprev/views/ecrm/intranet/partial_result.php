<?php
	if(count($collection) == 0)
	{
		$cd_intranet_voltar = 0;
	}
	else
	{
		$cd_intranet_voltar = $collection[0]['cd_intranet_voltar'];
	}
?>
<input type="hidden" name="cd_intranet_voltar" id="cd_intranet_voltar" value="<?php echo $cd_intranet_voltar ?>">
<?php
$body=array();
$head = array( 
	'Cód',
	'Item',
	'',
	'Subitens',
	'Item Superior',
	'Ordem',
	'',
	'Dt Cadastro',
	'Dt Alteração',
	'Usuário Alteração'
);

$menu_raiz = array(
	10421,
	10405,
	10462,
	10351,
	10335,
	10339,
	10292,
	10290,
	10261,
	10148,
	10200,
	10073,
	10146,
	10399,
	10144,
	10079,
	10082,
	10083,
	10081,
	10078,
	10075,
	10088,
	10089,
	10198
);

foreach( $collection as $item )
{
	$subitem = '';
	
	$itemsuperior = Array();
	foreach($item['itemsuperior'] as $item1 )
	{
		$itemsuperior[$item1["value"]] = $item1["text"];
	}	

	foreach($item['subitem'] as $item2)
	{
		$subitem .= anchor("ecrm/intranet/cadastro/".$item2["cd_gerencia"]."/".$item2["cd_intranet"], $item2['titulo']).br() ;
	}
	
	$input_ordem = array(
		"name"=>"nr_ordem_".$item['cd_intranet'], 
		"id"=>"nr_ordem_".$item['cd_intranet'],
		"onblur" => "salvar_ordem(".$item['cd_intranet'].");",
		"style"=>"display:none;width:50px;"
	);
	/*				
	$editar_ordem = '<a href="javascript: void(0);" id="nr_ordem_editar_'.$item['cd_intranet'].'" onclick="editar_ordem('.$item['cd_intranet'].', $(this));" title="Editar">[editar]</a>';

	$salvar_ordem = '<a href="javascript: void(0);" id="nr_ordem_salvar_'.$item['cd_intranet'].'" onclick="salvar_ordem('.$item['cd_intranet'].', $(this));" title="Salvar">[salvar]</a>';	
	*/


	if(in_array($item["cd_intranet_pai"], $menu_raiz))
	{
		$drop_item_superior = 'Menu Raiz';
	}
	else
	{
		$drop_item_superior = form_dropdown('cd_intranet_'.$item["cd_intranet"], $itemsuperior, array($item["cd_intranet_pai"]), 'onchange="setItemPai('.$item["cd_intranet"].', $(this).val());" style="width: 200px;"');	
	}

	$body[] = array(
		$item["cd_intranet"],
		array( trim($item["titulo"]), "text-align:left;"),
		
		anchor(site_url('ecrm/intranet/cadastro/'.$item["cd_gerencia"].'/'.$item["cd_intranet"]), "[conteúdo]"),

		(trim($subitem) != "" ?'<a href="javascript: verSubitem('.$item['cd_intranet'].'); void(0);"  title="Listar subitens">[listar]</a>' : ""),
		$drop_item_superior,
		'<span id="ajax_ordem_valor_'.$item['cd_intranet'].'"></span> '.'<span id="valor_ordem_'.$item['cd_intranet'].'">'.$item['nr_ordem'].'</span>'.
		form_input($input_ordem, $item['nr_ordem'])."<script> jQuery(function($){ $('#intranet_".$item['cd_intranet']."').numeric(); }); </script>",
		'<a id="editar_ordem_'.$item['cd_intranet'].'" href="javascript: editar_ordem('.$item['cd_intranet'].'); void(0);" title="Editar a ordem">[editar]</a>'.
		'<a id="salvar_ordem_'.$item['cd_intranet'].'" href="javascript: salvar_ordem('.$item['cd_intranet'].'); void(0);" style="display:none" title="Salvar a ordem">[salvar]</a>',

		//array('<span id="nr_ordem_ajax_'.$item['cd_intranet'].'"></span> '.form_input($input_ordem,$item['nr_ordem'])."<script> jQuery(function($){ $('#nr_ordem_".$item['cd_intranet']."').numeric(); }); </script> ".$salvar_ordem, 'text-align:center;'),
		$item["dt_inclusao"],
		$item["dt_alteracao"],
		array($item["usuario_alteracao"], "text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();

?>
