<?php
$body = array();
$head = array( 
	"Ordem",
	"",
	"Nome do Gráfico",
	"Tipo de Gráfico",
	""
);

foreach($collection as $item)
{
	$config = array(
		"name"   => "nr_ordem_".$item['cd_caderno_cci_grafico'], 
		"id"     => "nr_ordem_".$item['cd_caderno_cci_grafico'],
		"onblur" => "set_ordem(".$item['cd_caderno_cci_grafico'].");",
		"style"  => "display:none; width:50px;"
	);

	$body[] = array(
		'<span id="ajax_ordem_valor_'.$item['cd_caderno_cci_grafico'].'"></span> '.'<span id="valor_ordem_'.$item['cd_caderno_cci_grafico'].'">'.$item['nr_ordem'].'</span>'.
		form_input($config, $item['nr_ordem'])."<script> jQuery(function($){ $('#cd_caderno_cci_grafico_".$item['cd_caderno_cci_grafico']."').numeric(); }); </script>",
		'<a id="editar_ordem_'.$item['cd_caderno_cci_grafico'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_caderno_cci_grafico'].');" title="Editar a ordem">[editar]</a>'.
		'<a id="salvar_ordem_'.$item['cd_caderno_cci_grafico'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>',
		array(anchor("gestao/caderno_cci/grafico/".$item["cd_caderno_cci"]."/".$item["cd_caderno_cci_grafico"], $item["ds_caderno_cci_grafico"]), "text-align:left;"),
		$item["tipo_grafico"],
		($item["tp_grafico"] != 'R' ? '<a href="javascript:void(0);" onclick="configurar('.$item["cd_caderno_cci_grafico"].')">[configurar]</a>' : '').
		'<a href="javascript:void(0);" onclick="excluir('.$item["cd_caderno_cci_grafico"].')">[excluir]</a>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>