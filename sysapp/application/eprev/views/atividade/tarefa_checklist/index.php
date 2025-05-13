<?php
set_title('Tarefa - Checklist');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(), 'verifica_radio()');
?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/tarefa"); ?>';
    }
	
	function ir_atividade()
	{
		location.href='<?php echo base_url(). "sysapp/application/migre/cad_atividade_atend.php?n=".$row['cd_atividade']."&aa="; ?>';
	}
	
	function ir_definicao()
	{
		location.href='<?php echo site_url("atividade/tarefa/cadastro/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("atividade/tarefa_anexo/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_execucao()
	{
		location.href='<?php echo site_url("atividade/tarefa_execucao/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function ir_historico()
	{
		location.href='<?php echo site_url("atividade/tarefa_historico/index/".$row['cd_atividade']."/".$row['cd_tarefa']); ?>';
	}
	
	function verifica_radio()
	{
		var bol = true;
	
		$('#form_salve_checklist :input').each(function(){
			if($(this).attr('id') == "fl_resposta")
			{
				if($(this).val() == '')
				{
					bol = false ;
				}
			}
		});

		if(bol)
		{
			if(confirm('Deseja salvar?'))
			{
				$('#form_salve_checklist').submit();
			}
		}
		else
		{
			alert('Responda todas as perguntas.');
		}
	}


</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Atividade', FALSE, 'ir_atividade()');
$abas[] = array('aba_lista', 'Definição', FALSE, 'ir_definicao();');
$abas[] = array('aba_lista', 'Execução', FALSE, 'ir_execucao();');
$abas[] = array('aba_lista', 'Checklist', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
$abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

$fl_save = false;
$fl_checklist = false;
$fl_verificado = false;

if(trim($row['fl_status']) != "LIBE" AND trim($row['fl_status']) != "CONC" AND (intval($row['cd_recurso']) == $this->session->userdata('codigo')))
{
	$fl_checklist = true;
}

if(trim($row['fl_status']) == "LIBE" AND $this->session->userdata('codigo') == intval($row['cd_mandante']))
{
	$fl_verificado = true;
}

if($fl_checklist OR $fl_verificado)
{
	$fl_save = true;
}

$body=array();
 	 	 	
$head = array( 
	'Pergunta',
	'Resposta',
	'Verificado pelo analista'
);

$options[""] = "Selecione";
$options["S"] = "Sim";
$options["N"] = "Não";
$hidden = '';
foreach( $collection as $item )
{
    $body[] = array(
		array($item['ds_grupo'],'text-align:left; font-weight:bold;'),
		'',
		''
	);

	foreach( $item['perguntas'] as $item2 )
	{
		$selected = array($item2['fl_resposta']);
	
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		$fl_dropdown = ($fl_checklist ? false : true);
		
		if(trim($item2['fl_resposta']) != '')
		{
			$fl_dropdown = true;
		}
		
		$dropdown = '<select id="fl_resposta" name="fl_resposta['.$item2['cd_tarefa_checklist_pergunta'].']"'.($fl_dropdown ? 'style="display:none"' : '')." onkeypress='handleEnter(this, event);'>\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;
			$val = (string) $val;

			$sel = (in_array($key, $selected))?' selected="selected"':'';

			$dropdown .= '<option value="'.$key.'"'.$sel.'>'.$val."</option>\n";
		}

		$dropdown .= "</select>";
		$body[] = array(
	
			array($item2 ['ds_pergunta'],'text-align:left'),
			$dropdown.
			(trim($item2['fl_resposta']) != '' ? (trim($item2['fl_resposta']) == 'S' ? '<span style="color:green; font-weight:bold">Sim</span>' : '<span style="color:red; font-weight:bold">Não</span>' ) : ''),
			($fl_verificado ? form_checkbox(array('name' => 'fl_especialista['.$item2['cd_tarefa_checklist_pergunta'].']'), 'S', (trim($item2['fl_especialista']) == 'S' ? true : false)) : (trim($item2['fl_especialista']) == 'S' ? '<span style="font-weight:bold">Sim</bold>' : ''))
		);
	}
}

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('atividade/tarefa_checklist/salvar', 'name="filter_bar_form" id="form_salve_checklist"');
		echo form_start_box( "default_box", "Tarefa" );
			echo form_default_text("atividade_os", "Atividade/Tarefa:", $row['cd_atividade'].' / '.$row['cd_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("status", "Status:", $row['status_atual'], 'style="width: 500px; border: 0px; font-weight:bold; color:'.trim($row['status_cor']).'" readonly');
			echo form_default_text("nome_tarefa", "Tipo da tarefa:", $row['nome_tarefa'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("analista", "Analista:", $row['analista'], 'style="width: 500px; border: 0px;" readonly');
			echo form_default_text("programador", "Programador:", $row['programador'], 'style="width: 500px; border: 0px;" readonly');
		echo form_end_box("default_box");
		echo form_start_box( "default_box", "Perguntas" );
			echo form_default_hidden('cd_atividade', '', $row['cd_atividade']);
			echo form_default_hidden('cd_tarefa', '', $row['cd_tarefa']);
			echo form_default_hidden('codigo_tarefa', '', $row['codigo_tarefa']);
			echo $grid->render();
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo ($fl_save ? button_save("Salvar") : '');
        echo form_command_bar_detail_end();
	echo form_close();

echo br(2);

echo aba_end();
$this->load->view('footer_interna');
?>