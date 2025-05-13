<?php
#echo "<PRE style='text-align:left;'>".print_r($ar_lista,true)."</PRE>"; #exit;
	if(gerencia_in(array('GP','GCM')))
	{
		echo form_start_box("botoes_box", "Opções",true);
			echo form_default_row('', '', '
				<input type="button" onclick="protocolo();" value="Novo Protocolo Interno" class="btn btn-mini btn-info" style="width: 200px;"> 
				<input type="button" onclick="protocoloDigitalizacao();" value="Novo Protocolo Digitalização (DIGITAL)" class="btn btn-mini btn-success" style="width: 200px;">
				<input type="button" onclick="excluirDocumentos();" value="Excluir" class="btn btn-mini btn-danger" style="width: 200px;">
			');
		echo form_end_box("botoes_box");
	}
	else
	{
		echo form_start_box("botoes_box", "Opções",true);
			echo form_default_row('', '', '
				<input type="button" onclick="excluirDocumentos();" value="Excluir" class="btn btn-mini btn-danger" style="width: 200px;">
			');
		echo form_end_box("botoes_box");		
	}

$body=array();
$head = array( 
	'<input type="checkbox"  id="checkboxCheckAll" onclick="checkAll();" title="Clique para Marcar ou Desmarcar Todos">',
	'',
	'Dt Digitalizado',
	'Usuário',
	'Arquivo',
	'Prot. Interno',
	'Documento',
	'Nome',
	'Processo',
	''
);

$_js_preenche = "";

foreach($ar_lista as $ar_item)
{
	$id = $ar_item['id_file']."_";
	$id_part = array($id.'cd_empresa',$id.'cd_registro_empregado',$id.'seq_dependencia', $id.'nome_participante');
	$id_cod_doc = $id.'id_codigo';

	#### PREENCHE CAMPOS - GB ###
	$_RE = $id_part;
	$_cd_documento = "";
	if (gerencia_in(Array('GI','GP','GCM')))
	{
		//rborges_image_2012-12-28-103749_0_246328_0_364_30-12-2012_20_21.pdf
		$ar_tmp = explode("_",$ar_item["file_name"]);
		#Array ( [0] => rborges [1] => image [2] => 2012-12-28-103918 [3] => 0 [4] => 197050 [5] => 0 [6] => 364 [7] => 30-12-2012 [8] => 40 [9] => 21.pdf ) 
		#[3] => 0 [4] => 197050 [5] => 0 [6] => 364
		
		#echo intval($ar_tmp[4]).br(1);

		if((count($ar_tmp) > 6) and (intval($ar_tmp[4]) > 0) and (intval($ar_tmp[6]) > 0))
		{
			$_RE[$id.'cd_empresa']            = intval($ar_tmp[3]);
			$_RE[$id.'cd_registro_empregado'] = intval($ar_tmp[4]);
			$_RE[$id.'seq_dependencia']       = intval($ar_tmp[5]);
			$_cd_documento                    = intval($ar_tmp[6]);	
			
			#consultar_tipo_documentos__556651cb812883d0c01ef833a2b9e246_id_codigo();
		}
		else
		{
			$_RE = false;
		}
		#print_r($ar_tmp); echo br(2); #exit;

		if(intval($ar_item['cd_documento']) > 0)
		{
			$_cd_documento = intval($ar_item['cd_documento']);	

			$_js_preenche.= "
								consultar_tipo_documentos__".$id_cod_doc."();
							";
		}

		if(intval($ar_item['cd_empresa']) > 0)
		{
			$_RE[$id.'cd_empresa'] = intval($ar_item['cd_empresa']);	
		}

		if(intval($ar_item['cd_registro_empregado']) > 0)
		{
			$_RE[$id.'cd_registro_empregado'] = intval($ar_item['cd_registro_empregado']);	

			$_js_preenche.= "
								consultar_participante__".$id."cd_empresa();
							";
		}

		if(trim($ar_item['seq_dependencia']) != '')
		{
			$_RE[$id.'seq_dependencia'] = intval($ar_item['seq_dependencia']);	
		}
	}
	else
	{
		$_RE = false;
	}
	
	
	$campo_check = array(
		'name'        => $id.'chk',
		'id'          => $id.'chk',
		'value'       => $ar_item['id_file'],
		'checked'     => FALSE
		);
		
	$campo_doc = array(
			'id_codigo'  => $id_cod_doc,
			'id_nome'    => $id.'nome_documento',
			'formulario' => FALSE,
			'value'      => $_cd_documento 
			);
	
	$protocolo = "";
	foreach($ar_item["ar_protocolo_interno"] as $ar_prot)
	{
		if($protocolo == "")
		{
			$protocolo = anchor("ecrm/cadastro_protocolo_interno/detalhe/".$ar_prot['cd_documento_recebido'],$ar_prot['nr_documento_recebido'],array('title' => 'Ir para o protocolo interno'));
		}
		else
		{
			$protocolo.= br(1).anchor("ecrm/cadastro_protocolo_interno/detalhe/".$ar_prot['cd_documento_recebido'],$ar_prot['nr_documento_recebido'],array('title' => 'Ir para o protocolo interno'));
		}
	}

	$body[] = array(
			form_checkbox($campo_check),
			'',
			$ar_item['date_br'],
			$ar_item['usuario'],
			anchor("../".$ar_item["file"],$ar_item['file_name'],array('title' => 'Visualizar arquivo','target' => '_blank')),
			$protocolo,
			form_default_tipo_documento($campo_doc),
			form_default_participante(
							$id_part,
							'',
							$_RE,
							true,
							true,
							'',
							false
							)
			.br(1)
			.form_input(array('name' => $id.'nome_participante', 'id' => $id.'nome_participante'), '', 'style="width:300px;"'),
			form_input(array('name' => $id.'processo', 'id' => $id.'processo'), '', 'style="width:150px;"').
			"<script> jQuery(function($){  $('#".$id."processo').numeric();  }); </script>",
			'<a href="javascript:void(0)" onclick="salvar_digitalizado(\''.$ar_item['id_file'].'\', '.$ar_item['cd_digitalizado'].')">[salvar]</a>'
		);
}

$ar_oculta = Array();
if($tp_digitalizacao != "JUR")
{
    $ar_oculta = Array(8);
}

$this->load->helper('grid');
$grid = new grid();
$grid->id_tabela  = 'tabela_digitalizado';
$grid->head       = $head;
$grid->body       = $body;
$grid->col_oculta = $ar_oculta;
echo $grid->render();

echo "<BR><BR><BR>";

if(trim($_js_preenche) != "")
{
	echo "
			<script>
				".$_js_preenche."
			</script>
	     ";
}
?>