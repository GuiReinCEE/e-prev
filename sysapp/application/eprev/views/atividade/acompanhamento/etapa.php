<?php
set_title('Acompanhamento de Projetos - Etapa');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_acomp'));
	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}

	function ir_reuniao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function novo_registro_operacional()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/registro_operacional/".intval($row['cd_acomp'])); ?>';
	}
	
	function novo_escopo()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/escopo/".intval($row['cd_acomp'])); ?>';
	}
	
	function novo_wbs()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/wbs/".intval($row['cd_acomp'])); ?>';
	}
	
	function novo_mudanca_escopo()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/mudanca_escopo/".intval($row['cd_acomp'])); ?>';
	}
	
	function imprimir_registro_operacional(cd_acompanhamento_registro_operacional)
    {
        window.open('<?php echo site_url("atividade/registro_operacional/imprimir/"); ?>/'+cd_acompanhamento_registro_operacional);
    }
	
	function imprimir_escopo(cd_acompanhamento_escopos)
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_escopo/"); ?>/'+cd_acompanhamento_escopos);
    }
	
	function imprimir_mudanca_escopo(cd_acompanhamento_mudanca_escopo)
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_mudanca_escopo"); ?>/'+cd_acompanhamento_mudanca_escopo);
    }
	
	function excluir_wbs(cd_acompanhamento_wbs)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o anexo?\n\n"))
		{
			$.post( '<?php echo site_url('atividade/acompanhamento/excluir_wbs'); ?>',
			{
				cd_acompanhamento_wbs : cd_acompanhamento_wbs
			},
			function(data)
			{
				location.reload();
			});
		}
	}
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_reuniao();');
$abas[] = array('aba_etapas', 'Etapas', TRUE, 'location.reload();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	

$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}

#### REGISTRO OPERACIONAL ####
$body = array();
$head = array('Autor', 'Processo', 'Finalizado', '');

foreach( $arr_reg_operacional as $item )
{
	$body[] = array(					 
		array($item["nome"],'text-align:left;'),
		array(anchor(site_url("atividade/acompanhamento/registro_operacional/".intval($row['cd_acomp'])."/".intval($item['cd_acompanhamento_registro_operacional'])), $item['ds_nome']),'text-align:left;'),
		$item['dt_finalizado'],
		'<a href="javascript:void(0)" onclick="imprimir_registro_operacional('.$item['cd_acompanhamento_registro_operacional'].')">[imprimir]</a>'
	);
}
$this->load->helper('grid');
$grid_reg_operacional = new grid();
$grid_reg_operacional->head = $head;
$grid_reg_operacional->body = $body;
$grid_reg_operacional->view_count = false;
$grid_reg_operacional->view_data = false;

#### ESCOPO ####
$body = array();
$head = array('Objetivo do Escopo', 'Usuário', '');
foreach( $arr_escopo as $item )
{
	$body[] = array(					 
		array(anchor(site_url("atividade/acompanhamento/escopo/".intval($row['cd_acomp'])."/".intval($item['cd_acompanhamento_escopos'])), nl2br($item['ds_objetivos'])),'text-align:left;'),
		array($item['nome'],'text-align:left;'),
		'<a href="javascript:void(0)" onclick="imprimir_escopo('.$item['cd_acompanhamento_escopos'].')">[imprimir]</a>'
	);
}
$this->load->helper('grid');
$grid_escopo = new grid();
$grid_escopo->head = $head;
$grid_escopo->body = $body;
$grid_escopo->view_count = false;
$grid_escopo->view_data = false;

#### WBS ####
$body = array();
$head = array('Dt Inclusão', 'Arquivo', 'Usuário', '');

foreach( $arr_wbs as $item )
{
	$body[] = array(					
		$item['dt_cadastro'],
		array(anchor(base_url().'up/acompanhamento_wbs/'.$item['ds_arquivo_fisico'], $item['ds_arquivo'] , array('target' => "_blank")), "text-align:left;"),
		$item['nome'],
		'<a href="javascript:void(0);" onclick="excluir_wbs('.$item['cd_acompanhamento_wbs'].')">[excluir]</a>'
	);
}
$this->load->helper('grid');
$grid_wbs = new grid();
$grid_wbs->head = $head;
$grid_wbs->body = $body;
$grid_wbs->view_count = false;
$grid_wbs->view_data = false;

#### MUDANÇA DE ESCOPO ####
$body = array();
$head = array('Solicitante', 'Data', '');
foreach( $arr_mudanca_escopo as $item )
{
	$body[] = array(	
		array(anchor(site_url("atividade/acompanhamento/mudanca_escopo/".intval($row['cd_acomp'])."/".intval($item['cd_acompanhamento_mudanca_escopo'])), $item['ds_nome_solicitante']), "text-align:left;"),
		$item['dt_mudanca'],
		'<a href="javascript:void(0)" onclick="imprimir_mudanca_escopo('.$item['cd_acompanhamento_mudanca_escopo'].')">[imprimir]</a>'
	);
}
$this->load->helper('grid');
$grid_mudanca_escopo = new grid();
$grid_mudanca_escopo->head = $head;
$grid_mudanca_escopo->body = $body;
$grid_mudanca_escopo->view_count = false;
$grid_mudanca_escopo->view_data = false;

echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_etapa');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );		
		echo form_end_box("default_box");	
		echo form_start_box("default_analise_requisitos", "Análise de Requisitos" );
			echo form_default_dropdown('status_ar', 'Status:', $arr_status, Array($row['status_ar']));
			echo form_default_textarea('desc_ar', "Descrição: ", $row, "style='height: 80px;'");
			echo $grid_reg_operacional->render();
			echo form_default_row('', '', button_save("Novo Registro Operacional", 'novo_registro_operacional();'));		
		echo form_end_box("default_analise_requisitos");	
		echo form_start_box("default_escopo", "Escopo" );
			echo form_default_dropdown('status_es', 'Status:', $arr_status, Array($row['status_es']));
			echo form_default_textarea('desc_es', "Descrição: ", $row, "style='height: 80px;'");
			echo $grid_escopo->render();
			echo form_default_row('', '', button_save("Novo Escopo", 'novo_escopo();'));
			echo br(2);		
			echo $grid_wbs->render();
			echo form_default_row('', '', button_save("Novo WBS", 'novo_wbs();'));	
		echo form_end_box("default_escopo");	
		echo form_start_box("default_aprovacao_usuario", "Aprovação do Usuário" );
			echo form_default_dropdown('status_au', 'Status:', $arr_status, Array($row['status_au']));
			echo form_default_textarea('desc_au', "Descrição: ", $row, "style='height: 80px;'");
		echo form_end_box("default_aprovacao_usuario");		
		echo form_start_box("default_desenvolvimento", "Desenvolvimento" );
			echo form_default_dropdown('status_de', 'Status:', $arr_status, Array($row['status_de']));
			echo form_default_textarea('desc_de', "Descrição: ", $row, "style='height: 80px;'");
		echo form_end_box("default_desenvolvimento");	
	
		echo form_start_box("default_mudanca_escopo", "Mudança de Escopo" );
			echo form_default_dropdown('status_me', 'Status:', $arr_status, Array($row['status_me']));
			echo form_default_textarea('desc_me', "Descrição: ", $row, "style='height: 80px;'");
			echo $grid_mudanca_escopo->render();
			echo form_default_row('', '', button_save("Nova Mudança de Escopo", 'novo_mudanca_escopo();'));		
		echo form_end_box("default_mudanca_escopo");		
		echo form_command_bar_detail_start();
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar Etapas");
			}
		echo form_command_bar_detail_end();	
	echo form_close();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>