<?php
set_title('Acompanhamento de Projetos - Escopo');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('cd_acomp'));
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
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_escopo/".intval($row_escopo['cd_acompanhamento_escopos'])); ?>');
    }
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuni�es', FALSE, 'ir_lista_reunicao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_reuniao', 'Escopo', TRUE, 'location.reload();');
$abas[] = array('aba_previsao', 'Previs�o', FALSE, 'ir_previsao();');	
	
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
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_escopo');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_hidden('cd_acompanhamento_escopos', '', $row_escopo);
			echo form_default_text('cd_acomp_h', "C�digo :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_start_box( "default_escopo_box", "Escopo" );
			echo form_default_textarea('ds_objetivos', '1) Objetivo : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', 'Descrever o que se deseja alcan�ar com a implementa��o do projeto.<br/>A identifica��o do objetivo do projeto acontece na fase de an�lise de requisitos, durante a avalia��o do que deve ser feito e o porque.<br/><br/>');
			echo form_default_textarea('ds_regras', '2) Regras de Neg�cio/Funcionalidas : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', '
Descrever as regras de neg�cio necess�rias para o desenvolvimento do projeto.<br/>Estas regras s�o identificadas durante a defini��o/ revis�o do processo de neg�cio. Defini��o das funcionalidades do projeto, conforme as reuni�es com os respons�veis pelo projeto.<br/><br/>');
			echo form_default_textarea('ds_impacto', '3) Impacto : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', '
Descrever a avalia��o realizada sobre o impacto que este projeto causa nos demais processos e sistemas, tais como integra��es e mudan�as.<br/><br/>');
			echo form_default_textarea('ds_responsaveis', '4) Respons�veis : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', '
Apontar os respons�veis pelos processos envolvidos no projeto.<br/>Estas pessoas dever�o estar envolvidas na defini��o, execu��o e testes do projeto.<br/>Dever� existir tamb�m um ou mais respons�veis pela aprova��o do pr�-escopo (conforme acordo entre analista e envolvidos).<br/><br/>');
			echo form_default_textarea('ds_solucao', '5) Solu��o Imediata (opcional) : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', 'Descrever a solu��o imediata e tempor�ria de como ser� realizado o processo at� que o projeto seja implementado.<br/>Esta solu��o imediata � utilizada quando n�o � poss�vel esperar a conclus�o do projeto para iniciar o processo, sendo definido um fluxo alternativo e imediatamente vi�vel em comum acordo entre o analista de neg�cios/sistemas e o respons�vel pelo processo.<br/><br/>');
			echo form_default_textarea('ds_recurso', '6) Recurso/Custo : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', 'Descri��o do levantamento de recursos e/ou custos a serem utilizados no projeto.<br/>A avalia��o s� ser� realizada quando for necess�ria a utiliza��o recursos externos para a realiza��o do projeto.<br/><br/>');
			echo form_default_textarea('ds_viabilidade', '7) Viabilidade/Sugest�o (opcional) : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', '
A avalia��o de viabilidade sempre � realizada durante a an�lise de requisitos, mas este item somente ser� descrito neste documento se for identificado pelo analista de neg�cios/ sistemas que � invi�vel a implementa��o do projeto solicitado.<br/>Neste item pode ser descrita uma sugest�o alternativa de como esta solicita��o pode ser atendida.<br/><br/>');
			echo form_default_textarea('ds_modelagem', '8) Modelagem de Dados : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', 'Refer�ncia ao ER no caso de tabelas novas e no caso de manuten��es nas tabelas deve ser descrito as altera��es realizadas.<br/><br/>');
			echo form_default_textarea('ds_produtos', '9) Produtos : ', $row_escopo, 'style="height:100px;"');
			echo form_default_row('', '', 'Referenciar o nome da WBS e nome dos formul�rios dos produtos. ');
		echo form_end_box("default_escopo_box");
		echo form_command_bar_detail_start();
			
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar");
			}

			if(intval($row_escopo['cd_acompanhamento_escopos']) > 0)
			{
				echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
			}

		echo form_command_bar_detail_end();	
	echo form_close();		
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>