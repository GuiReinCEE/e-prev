<?php
set_title('Reunião SG - Cadastro');
$this->load->view('header');
?>
<script>
<?php
echo form_default_js_submit(array(
	'dt_sugerida', 
	'cd_reuniao_sg_instituicao',
	'participantes',
	'contato',
	'pauta',
	'cd_usuario_validacao',
	'cd_usuario_inclusao'
));
?>
    function reuniaoSGConfirma(form)
    {
        if($('#dt_reuniao').val() == "")
        {
            alert("Informe a data da reunião.");
            $('#dt_reuniao').focus();
        }
        else if($('#hr_reuniao').val() == "")
        {
            alert("Informe a hora da reunião.");
            $('#hr_reuniao').focus();
        }		
        else if(confirm('Confirma a Reunião?'))
        {
            $('#fl_confirma').val(1);
            form.submit();
        }
    }	

    function ir_lista()
    {
        location.href='<?=site_url("atividade/reuniao_sg")?>';
    }

    function ir_parecer()
    {
        location.href='<?=site_url("atividade/reuniao_sg/parecer/".$row['cd_reuniao_sg'])?>';
    }
	
	function ir_anexo()
    {
        location.href='<?=site_url("atividade/reuniao_sg/anexo/".$row['cd_reuniao_sg'])?>';
    }
    /*
    function ir_participante()
    {
        location.href='<?=site_url("atividade/reuniao_sg/participante/" . $row['cd_reuniao_sg'])?>';
    }
	*/
    function reuniaoSGExcluir(cd_reuniao_sg)
    {
        if(confirm("ATENÇÃO\n\nDeseja Excluir a reunião?\n\n"))
        {
            location.href='<?=site_url("atividade/reuniao_sg/excluir")?>' + "/" + cd_reuniao_sg;
        }
    }	

    function reuniaoSGNaoConfirma(cd_reuniao_sg)
    {
        if(confirm("ATENÇÃO\n\nDeseja NÃO Confirmar a reunião?\n\n"))
        {
            location.href='<?=site_url("atividade/reuniao_sg/naoConfirmar")?>' + "/" + cd_reuniao_sg;
        }
    }		
	
	function imprimir()
	{
		window.open('<?=site_url("atividade/reuniao_sg/imprimir_detalhe/".$row['cd_reuniao_sg'])?>');
	}
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Agendamento', TRUE, 'location.reload();');

if(intval($row['cd_reuniao_sg']) > 0)
{
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
}

if ($row['dt_reuniao'] != '')
{
    $abas[] = array('aba_lista', 'Parecer', FALSE, 'ir_parecer();');
}

foreach($arr_diretoria as $item)
{
	$arr[] = $item['value'];
}

echo aba_start($abas);
	echo form_open('atividade/reuniao_sg/salvar');
		echo form_start_box("default_box", "Reunião");
			echo form_default_hidden("fl_confirma", "", 0);
			echo form_default_text('cd_reuniao_sg', "Nº da Reunião: ", $row['cd_reuniao_sg'], "style='width:100%;border: 0px;' readonly");
			if(intval($row['cd_reuniao_sg']) > 0)
			{
				 echo form_default_text('dt_inclusao', "Dt. Solicitação: ", $row, "style='width:100%;border: 0px;' readonly");
			}

			if ((intval($row['cd_reuniao_sg']) > 0) AND (($this->session->userdata('divisao') != 'SG') OR (!array_search($row['cd_usuario_inclusao'], $arr) AND $this->session->userdata('divisao') == 'SG')))
			{
				echo form_default_text('usuario_cadastro', "Solicitante: ", $row['usuario_cadastro'], "style='width:100%;border: 0px;' readonly");
				echo form_default_hidden("cd_usuario_inclusao", "", (intval($row['cd_reuniao_sg']) > 0 ? $row['cd_usuario_inclusao'] : $this->session->userdata('codigo')));
			}
			else if($this->session->userdata('divisao') == 'GC')
			{
				echo form_default_dropdown('cd_usuario_inclusao', 'Solicitante: *', $arr_diretoria, array($row['cd_usuario_inclusao']));
			}
			else
			{
				echo form_default_hidden("cd_usuario_inclusao", "", (intval($row['cd_reuniao_sg']) > 0 ? $row['cd_usuario_inclusao'] : $this->session->userdata('codigo')));
			}

			if ($row['dt_exclusao'] != "")
			{
				echo form_default_text('dt_exclusao', "Dt. Exclusão: ", $row, "style='width:100%;border: 0px;' readonly");
			}

			if ($row['dt_cancela'] != "")
			{
				echo form_default_text('dt_cancela', "Dt. Não Confirmado: ", $row, "style='width:100%;border: 0px;' readonly");
			}

			if ((gerencia_in(array('GC'))) or ($row['dt_reuniao'] != ""))
			{
				if (intval($row['cd_reuniao_sg']) > 0)
				{
					echo form_default_date('dt_reuniao', "Data Reunião: ", $row);
					echo form_default_time('hr_reuniao', "Hora Reunião: ", $row);
				}
			}

			if($this->session->userdata('divisao') == 'GC')
			{
				echo form_default_upload_iframe('arquivo', 'reuniao_sg', 'Arquivo :', array($row['arquivo'], $row['arquivo_nome']), 'reuniao_sg');
			}
			else
			{
				if(trim($row['arquivo']) != '')
				{
					echo form_default_row('', 'Arquivo :',  '<a href="' . base_url() . 'up/reuniao_sg/' . $row['arquivo'] . '" target="_blank">[ver arquivo]</a>');
				}
			}

			echo form_default_date('dt_sugerida', "Sugestão de data:* ", $row);
			echo form_default_time('hr_sugerida', "Sugestão de horário: ", $row);

			if (($row['dt_exclusao'] == "") and ($row['dt_reuniao'] == "") and ($row['dt_cancela'] == "")  or (($row['dt_encerrado'] == "") and (($this->session->userdata('codigo') ==  $row['cd_usuario_validacao']) or ($this->session->userdata('tipo') == 'G')) ))
			{
				echo form_default_dropdown('cd_usuario_validacao', 'Responsável:*', $ar_res, array($row['cd_usuario_validacao']));
			}
			else
			{
			  echo form_default_dropdown('cd_usuario_validacao_d', 'Responsável:*', $ar_res, array($row['cd_usuario_validacao']), 'disabled'); 
			  echo form_default_hidden('cd_usuario_validacao', '', $row['cd_usuario_validacao']); 
			}
			echo form_default_checkbox_group('arr_participante', 'Convidados Internos :', $arr_participante, $arr_participante_checked, 120);
			/*
			if(intval($row['cd_reuniao_sg']) > 0)
			{
				$btn = '';
				
				if($fl_encerrado['fl_encerrado'] == 'S')
				{
					$btn .=  button_save("Adicionar", "ir_participante()").br(1);
				}
				
				echo
				
				$this->load->helper('grid');
				$grid = new grid();

				$body = array();

				$head = array(
				  'Participante',
				  'Gerência',
				  ''
				);
				
				foreach ($collection as $item)
				{
					$body[] = array(
					  array($item['nome'], "text-align:left;"),
					  $item['divisao'],
					  ($fl_encerrado['fl_encerrado'] == 'S' ? '<a onclick="excluir_participante(' . $item["cd_reuniao_sg_participante"] . ')" href="javascript:void(0);">[Excluir]</a>' : '')
					);
				}

				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;

				echo form_default_row('', 'Participantes:', $btn. $grid->render());
			}
			*/
			echo form_default_dropdown_db("cd_reuniao_sg_instituicao", "Nome da Instituição:* ", Array('projetos.reuniao_sg_instituicao', 'cd_reuniao_sg_instituicao', 'ds_reuniao_sg_instituicao'), Array($row['cd_reuniao_sg_instituicao']), "", "", TRUE);

			echo form_default_textarea('participantes', "Convidados Externos:* ", $row);

			echo form_default_textarea('contato', "Contato:* ", $row);
			echo form_default_textarea('pauta', "Pauta (Cfe IT 7.4.01.103):* ", $row);

		echo form_end_box("default_box");

		echo form_command_bar_detail_start();
		
			if ($row['dt_encerrado'] == '')
			{
				if (($row['dt_exclusao'] == "") and ($row['dt_reuniao'] == "") and ($row['dt_cancela'] == "")  or (($row['dt_encerrado'] == "") and (($this->session->userdata('codigo') ==  $row['cd_usuario_validacao']) or ($this->session->userdata('tipo') == 'G')) ))
				{
					echo button_save("Salvar");
					if (intval($row['cd_reuniao_sg']) > 0)
					{
						if (gerencia_in(array('GC')))
						{
							echo button_save("Confirmar", "reuniaoSGConfirma(this.form)", "botao_disabled");
							echo button_save("Não Confirmar", "reuniaoSGNaoConfirma(" . $row['cd_reuniao_sg'] . ")", "botao_disabled");
						}
						echo button_save("Excluir", "reuniaoSGExcluir(" . $row['cd_reuniao_sg'] . ")", "botao_vermelho");
					}
				}
				else if (gerencia_in(array('GC')))
				{
					echo button_save("Salvar");
					echo button_save("Confirmar", "reuniaoSGConfirma(this.form)", "botao_disabled");
					echo button_save("Não Confirmar", "reuniaoSGNaoConfirma(" . $row['cd_reuniao_sg'] . ")", "botao_disabled");
					
				}
			}
			
			echo button_save("Imprimir", "imprimir();", "botao_disabled");
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>