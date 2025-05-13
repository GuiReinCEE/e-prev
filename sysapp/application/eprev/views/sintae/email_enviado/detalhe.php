<?php
$this->load->view('header_interna');

// SOLICITAÇÃO
echo form_start_box( "solicitacao_box", "SOLICITAÇÃO" );

echo form_default_info("numero", "Número", $atividade["numero"]);
echo form_default_info("dt_cad", "Data Solicitação", $atividade["dt_cad"]);

echo form_default_info("area", "Gerência de detino", $atividade["area"]);
echo form_default_info("cod_atendente", "Atendente", $atividade["cod_atendente"]);

echo form_default_info("divisao", "Gerência Solicitante", $atividade["divisao"]);
echo form_default_info("cod_solicitante", "Solicitante", $atividade["cod_solicitante"]);

echo form_default_info("tipo", "Tipo da atividade", $atividade["tipo"]);
echo form_default_info("tipo_solicitacao", "Tipo da manutenção", $atividade["tipo_solicitacao"]);

echo form_default_info("descricao", "Descrição", $atividade["descricao"]);

echo form_end_box("solicitacao_box");

// ATENDIMENTO
echo form_start_box("atendimento_box", "ATENDIMENTO");

echo form_default_info("projeto", "Projeto", "Cenário Legal");
echo form_default_info("situacao", "Situação", $atividade["status_atual"]);

echo form_end_box("atendimento_box");

// IMPLEMENTAÇÃO
echo form_start_box("implementacao_legal_box", "IMPLEMENTAÇÃO LEGAL");

echo form_default_text("pertinencia", "Pertinência", $atividade["pertinencia"]);
echo form_default_date("prazo_previsto", "Prazo Previsto", "");
echo form_default_date("data_implantacao", "Data da Implantação", "");
echo form_default_dropdown("reencaminhar", "Reencaminhar para", $gerencias);

echo form_end_box("implementacao_legal_box");
?>

	<div class="aba_conteudo">

		<!-- BARRA DE COMANDOS -->
		<div id="command_bar" class="command-bar">
			<input type="button" value="Voltar" class="botao_disabled" style="width:100px;" onclick="window.history.back();">
			<input type="button" value="Salvar" class="botao" style="width:100px;">
		</div>
		<br />

	</div>

<?php
$this->load->view('footer_interna');
?>