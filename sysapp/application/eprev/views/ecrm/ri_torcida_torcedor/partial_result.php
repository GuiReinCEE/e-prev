<?php
$body=array();
$head = array( 
	'Código','Nome','Email','RE','IP', 'Dt Inclusão', 'Brinde', 'Dt Libera', '', ''
);

foreach( $collection as $item )
{
	$brinde = '
					<select name="fl_brinde_'.$item["cd_torcedor"].'" onchange="setBrinde(this.value,'.$item["cd_torcedor"].');">
						<option value="S" '.($item["fl_brinde"] == "S" ? "selected" : "").'>Sim</option>
						<option value="N" '.($item["fl_brinde"] != "S" ? "selected" : "").'>Não</option>
					</select>
	          ';
	
	$bloquear=$liberar='';

	if($item["dt_libera"]!='')
	{
		$liberar = $item["dt_libera"];
		$bloquear = comando('bloquear_btn', 'Bloquear', 'bloquear( "'.md5($item["cd_torcedor"]).'" );');
	}
	else
	{
		$liberar = comando('liberar_btn', 'Liberar', 'liberar( "'.md5($item["cd_torcedor"]).'" );');
		$bloquear='';
	}
	
	$body[] = array(
		$item["cd_torcedor"]
		, array($item["nome"],"text-align:left;")
		, array($item["email"],"text-align:left;")
		, $item["re"]
		, $item["ip"]
		, $item["dt_inclusao"]
		, $brinde
		, $liberar
		, $bloquear
		, button_delete("ecrm/ri_torcida_torcedor/excluir", $item["cd_torcedor"])
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>