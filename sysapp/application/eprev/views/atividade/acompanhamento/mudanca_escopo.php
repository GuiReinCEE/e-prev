<?php
set_title('Acompanhamento de Projetos - Mudança Escopo');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('numero'));
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	

	function ir_lista_reunicao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_mudanca_escopo/".intval($row_mudanca['cd_acompanhamento_mudanca_escopo'])); ?>');
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_lista_reunicao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_reuniao', 'Mudança Escopo', TRUE, 'location.reload();');
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

$arr_etapa[] = array('value' => 'AR', 'text' => 'Análise de Requisitos');
$arr_etapa[] = array('value' => 'ES', 'text' => 'Escopo');
$arr_etapa[] = array('value' => 'AU', 'text' => 'Aprovação do Usuário');
$arr_etapa[] = array('value' => 'DE', 'text' => 'Desenvolvimento');
$arr_etapa[] = array('value' => 'ME', 'text' => 'Mudança do Escopo');
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_mudanca_escopo');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_hidden('cd_acompanhamento_mudanca_escopo', '', $row);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_start_box( "default_mudanca_escopo_box", "Mudança Escopo" );
			echo form_default_integer('nr_numero', 'Número :*', $row_mudanca);
			echo form_default_dropdown('cd_solicitante', 'Solicitante :*', $arr_solicitante, array($row_mudanca['cd_solicitante']));
			echo form_default_dropdown('cd_analista', 'Analista :*', $arr_solicitante, array($row_mudanca['cd_analista']));
			echo form_default_dropdown('cd_etapa', 'Etapa :*', $arr_etapa, array($row_mudanca['cd_etapa']));
			echo form_default_date('dt_mudanca', 'Dt. Mudança :*', $row_mudanca);
			echo form_default_date('dt_aprovacao', 'Dt. Aprovação :*', $row_mudanca);
			echo form_default_integer('nr_dias', 'Tempo em dias :*', $row_mudanca);
			echo form_default_textarea('ds_descricao', '1) Descrição da Mudança de Escopo : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', 'Definição da alteração necessário no escopo do projeto.<br/><br/>');
			echo form_default_textarea('ds_regras', '2) Regras de Negócio/Funcionalidas : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', '
Descrever as regras de negócio necessárias para o desenvolvimento do projeto.<br/>Estas regras são identificadas durante a definição/ revisão do processo de negócio. Definição das funcionalidades do projeto, conforme as reuniões com os responsáveis pelo projeto.<br/><br/>');
			echo form_default_textarea('ds_impacto', '3) Impacto : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', '
Descrever a avaliação realizada sobre o impacto que este projeto causa nos demais processos e sistemas, tais como integrações e mudanças.<br/><br/>');
			echo form_default_textarea('ds_responsaveis', '4) Responsáveis : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', '
Apontar os responsáveis pelos processos envolvidos no projeto.<br/>Estas pessoas deverão estar envolvidas na definição, execução e testes do projeto.<br/>Deverá existir também um ou mais responsáveis pela aprovação do pré-escopo (conforme acordo entre analista e envolvidos).<br/><br/>');
			echo form_default_textarea('ds_solucao', '5) Solução Imediata (opcional) : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', 'Descrever a solução imediata e temporária de como será realizado o processo até que o projeto seja implementado.<br/>Esta solução imediata é utilizada quando não é possível esperar a conclusão do projeto para iniciar o processo, sendo definido um fluxo alternativo e imediatamente viável em comum acordo entre o analista de negócios/sistemas e o responsável pelo processo.<br/><br/>');
			echo form_default_textarea('ds_recurso', '6) Recurso/Custo : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', 'Descrição do levantamento de recursos e/ou custos a serem utilizados no projeto.<br/>A avaliação só será realizada quando for necessária a utilização recursos externos para a realização do projeto.<br/><br/>');
			echo form_default_textarea('ds_viabilidade', '7) Viabilidade/Sugestão (opcional) : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', '
A avaliação de viabilidade sempre é realizada durante a análise de requisitos, mas este item somente será descrito neste documento se for identificado pelo analista de negócios/ sistemas que é inviável a implementação do projeto solicitado.<br/>Neste item pode ser descrita uma sugestão alternativa de como esta solicitação pode ser atendida.<br/><br/>');
			echo form_default_textarea('ds_modelagem', '8) Modelagem de Dados : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', 'Referência ao ER no caso de tabelas novas e no caso de manutenções nas tabelas deve ser descrito as alterações realizadas.<br/><br/>');
			echo form_default_textarea('ds_produtos', '9) Produtos : ', $row_mudanca, 'style="height:100px;"');
			echo form_default_row('', '', 'Referenciar o nome da WBS e nome dos formulários dos produtos. ');
		echo form_end_box("default_mudanca_escopo_box");
		echo form_command_bar_detail_start();
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar");
			}
			
			if(intval($row_mudanca['cd_acompanhamento_mudanca_escopo']) > 0)
			{
				echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
			}
		echo form_command_bar_detail_end();	
	echo form_close();		
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>