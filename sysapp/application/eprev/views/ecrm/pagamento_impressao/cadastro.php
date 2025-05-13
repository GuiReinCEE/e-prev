<?php
	set_title('Pagamento Impressão - Cadastro');
	$this->load->view('header');
?>
<script>

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/pagamento_impressao') ?>";
    }

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Registro', TRUE, 'location.reload();');

    echo aba_start($abas);

            echo form_start_box('default_box', 'Dados da impressão');	
				echo form_default_text('cd_auto_atendimento_pagamento_impressao', 'Código:', $row['cd_auto_atendimento_pagamento_impressao'], "style='width:800px; border: 0px;' readonly" );
				echo form_default_text('participante', 'Tipo Documento:', $row['cd_empresa']."/".$row['cd_registro_empregado']."/".$row['seq_dependencia'], "style='width:100%;' readonly" );
				echo form_default_text('nome', "Nome: ", $row, "style='width:100%;' readonly" );
				echo form_default_text('tp_documento', 'Tipo Documento:', $row['tp_documento'], "style='width:100%; font-weight:bold; color:green;' readonly" );
				echo form_default_text('num_bloqueto', 'Identificador:', $row['num_bloqueto'], "style='width:100%;' readonly" );
				echo form_default_text('vl_valor', 'Valor:', number_format($row["vl_valor"],2,',','.'), "style='width:100%; font-weight:bold; color:blue;' readonly" );
				echo form_default_text('ano_competencia', 'Ano/Mês Competência:', $row["ano_competencia"]."/".$row["mes_competencia"], "style='width:100%;' readonly" );
				echo form_default_text('dt_impressao', 'Dt. Impressão:', $row['dt_impressao'], "style='width:100%;' readonly" );
				echo form_default_text('dt_vencimento', 'Dt. Vencimento:', $row['dt_vencimento'], "style='width:100%;' readonly" );
				echo form_default_text('ip', 'IP:', $row['ip'], "style='width:100%;' readonly" );
				echo form_default_row('origem', 'Origem:', ($row["fl_origem"] == "I" ? '<span class="label label-info">Interno</span>' : '<span class="label">Externo</span>'));
				echo form_default_text('codigo_barra', 'Linha Digitável:', $row['codigo_barra'], "style='width:100%;' readonly" );
				echo form_default_text('codigo_barra_interno', 'Cód Barra Interno:', $row['codigo_barra_interno'], "style='width:100%;' readonly" );
				echo form_default_textarea('log', 'Log:', $row["dados_post"], "style='width:100%; height: 100px;' readonly" );
			
			
				#echo form_default_row('dt_impressao', 'Dt. Impressão:', '<span class="label label-success">'.$row['dt_impressao'].'</span>');
				#echo form_default_row('dt_vencimento', 'Dt. Vencimento:', '<span class="label label-warning">'.$row['dt_vencimento'].'</span>');			
     
				
				
            echo form_end_box('default_box');
			
			if ($row['tp_documento'] == "BDL")
			{
			echo form_start_box('default_banrisul_box', 'Registro Banrisul');	
				echo form_default_text('nr_registro', 'Registro:', $row['nr_registro'], "style='width:100%;' readonly" );
				
				if(gerencia_in(array('GTI')))
				{
					echo form_default_row('tp_registro_ambiente', 'PDF:', anchor("https://www.fundacaofamiliaprevidencia.com.br/bdl.php?b=".$row['cd_auto_atendimento_pagamento_impressao_md5'], "[BDL]", array("target" => "_blank")));
				}
				
				echo form_default_row('fl_tipo_registro', 'Tipo:', $row["fl_tipo_registro"]);
				echo form_default_row('tp_registro_ambiente', 'Ambiente:', ($row["tp_registro_ambiente"] == "P" ? '<span class="label label-warning">'.$row["tp_registro_ambiente"].'</span>' : '<span class="label">'.$row["tp_registro_ambiente"].'</span>'));
				echo form_default_row('fl_registro', 'Status Registro:', ($row["fl_erro_registro"] == "S" ? '<span class="label label-important">ERRO</span>' : '<span class="label label-success">OK</span>'));
				echo form_default_textarea('xml_check', 'Retorno:', utf8_decode($row["xml_check"]), "style='width:100%; height: 100px;' readonly" );
				echo form_default_editor_code('xml_envio', 'XML Envio:', $row["xml_envio"], "style='width:900px; height: 300px;' readonly", strtolower($row["fl_tipo_registro"]));
				echo form_default_row('', '', '');
				echo form_default_editor_code('xml_retorno', 'XML Retorno:', $row["xml_retorno"], "style='width:900px; height: 300px;' readonly" , strtolower($row["fl_tipo_registro"]));
			echo form_end_box('default_banrisul_box');
			}
        echo br(10);
	echo aba_end();

    $this->load->view('footer_interna');
?>