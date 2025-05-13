<?php
$body = array();
$head = array( 
	"Cód.",
	"Título",
	"Autor",
	"Status",
	""
);

foreach( $collection as $item )
{
	if(gerencia_in(array("SG")))
	{
		$body[] = array(
			anchor("cadastro/biblioteca_sg/cadastro/".$item["cd_biblioteca_livro"], $item["nr_biblioteca_livro"]),
			array(anchor("cadastro/biblioteca_sg/cadastro/".$item["cd_biblioteca_livro"], $item["ds_biblioteca_livro"]), "text-align:left;"),
			array($item["autor"], "text-align:left;"),
			'<span class="'.$item["class_status"].'">'.$item["status"].'</span>',
			(trim($item["fl_locar"]) == "S" ? '<a href="javascript:void(0);" onclick="alugar('.$item["cd_biblioteca_livro"].')">[emprestar]</a> ' : "").
			(trim($item["fl_devolver"]) == "S" ? '<a href="javascript:void(0);" onclick="devolver('.$item["cd_biblioteca_livro_movimento"].')">[devolver]</a> ' : "").
			(((trim($item["fl_locar"]) == "N") AND (trim($item["fl_devolver"]) == "N") AND (intval($item["cd_biblioteca_livro_movimento"]) == 0)) ? '<a href="javascript:void(0);" onclick="excluir('.$item["cd_biblioteca_livro"].')">[excluir]</a> ' : "")
		);
	}
	else
	{
		$body[] = array(
			$item["nr_biblioteca_livro"],
			array( $item["ds_biblioteca_livro"], "text-align:left;"),
			array($item["autor"], "text-align:left;"),
			'<span class="'.$item["class_status"].'">'.$item["status"].'</span>',
			(trim($item["fl_devolver"]) == "S" ? '<a href="javascript:void(0);" onclick="devolver('.$item["cd_biblioteca_livro_movimento"].')">[devolver]</a> ' : "")
		);		
	}
	
}

$this->load->helper("grid");
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>