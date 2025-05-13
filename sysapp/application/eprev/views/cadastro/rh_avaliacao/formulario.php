<?php
	set_title('Sistema de Avaliação - Autoavaliação');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array()); ?>
	
	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao') ?>";
	}

	function ir_pdi()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao/plano_desenvolvimento_individual/'.$row['cd_avaliacao_usuario']) ?>";
	}

	function salvar_encerrar(form)
	{
		var bloco       = []
		var id_pergunta = 0;
		var id_bloco    = 0;

		$("#btn_salvar").hide();

		<? foreach ($bloco_pergunta as $key => $item): ?>
			if($(".pergunta_<?= $item ?>").val() == "")
			{
				if(id_pergunta == 0)
				{
					id_pergunta = <?= $item ?>;
				}

				$(".pergunta_<?= $item ?>").css("border-color", "red");
				$("#pergunta_<?= $item ?>_label").css("color", "red");
			}
			else
			{
				$(".pergunta_<?= $item ?>").css("border-color", "#a9a9a9");
				$("#pergunta_<?= $item ?>_label").css("color", "black");
			}
		<? endforeach; ?>	

		if(id_pergunta > 0)
		{			
			$("html, body").stop().animate({
				scrollTop: $("#pergunta_"+id_pergunta+"_row").offset().top
			}, 1000, "easeInOutExpo");
			
			alert("Responda todas as perguntas para poder encerrar!");
			 
			$("#btn_salvar").show();
		}
		else
		{
			<? foreach ($bloco as $key => $item): ?>
				<? foreach ($item as $key2 => $item2): ?>
				var bloco_<?= $key2 ?> = 0;
				
				$(".bloco_<?= $key2 ?>").each(function(){
					bloco_<?= $key2 ?> += get_ponto_performance("<?= $key?>", $(this).val());
				});

				if(((bloco_<?= $key2 ?> / <?= $item2 ?>) <= 50))
				{
					if(($(".justificativa_<?= $key2 ?>").val() == ""))
					{
						if(id_bloco == 0)
						{
							id_bloco = <?= $key2 ?>;
							$(".justificativa_<?= $key2 ?>").focus();
						}

						$("#default_bloco_box_<?= $key2 ?>").css("border-color", "red");
						$(".justificativa_<?= $key2 ?>").css("border-color", "red");
						$("#justificativa_<?= $key2 ?>_label").css("color", "red");
					}
					else
					{
						bloco.push(<?= $key2 ?>);
					}
				}
				else
				{	
					$("#default_bloco_box_<?= $key2 ?>").css("border-color", "#bdcad8");
					$(".justificativa_<?= $key2 ?>").css("border-color", "#a9a9a9");
					$("#justificativa_<?= $key2 ?>_label").css("color", "black");
				}
				<? endforeach; ?>

			<? endforeach; ?>

			if(id_bloco > 0)
			{
				$("html, body").stop().animate({
					scrollTop: $("#default_bloco_box_"+id_bloco).offset().top
				}, 1000, "easeInOutExpo");
				
				alert("Justifique os blocos em vermelho.");
				 
				$("#btn_salvar").show();
			}
			else
			{
				$("#bloco").val(bloco);

				confirmacao = 'Deseja encerrar a avaliação?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
		
				if(confirm(confirmacao))
				{ 
					$("#fl_encerramento").val("S")
					$(form).submit();
				}
			}
		}
	}

	function get_ponto_performance(tp_grupo, tp_performance)
	{
		switch (tp_grupo) 
		{
		<? foreach ($performance as $key => $item): ?>
			case "<?= $key ?>":
				switch (tp_performance) 
				{
				<? foreach ($item as $key2 => $item2): ?>
					case "<?= $item2['tp_performance'] ?>":
						return <?= $item2['nr_ponto'] ?>;
					break;
				<? endforeach; ?>
				}
		        break;
		<? endforeach; ?>
		}
	}

	function exibir_conhecimentos()
	{
		$("#gridWindowTexto").html('<?= $row['ds_conhecimento'] ?>');

		$('#gridWindow').modal({
			focus:false,
			autoResize: true,
			containerCss:{
				width:600,
				height:650
				},
			onClose: function (dialog) {
				$.modal.close();
			}					
		});	
	}

	function gerar_pdf()
	{
		window.open("<?= site_url('cadastro/rh_avaliacao/formulario_pdf/'.$row['cd_avaliacao_usuario']) ?>");
	}

	$(function(){
		<? if(trim($row['dt_encerramento']) != ''): ?>
		$("textarea").attr("disabled", "disabled");
		<? endif; ?>
	});
</script>
<style>
	div.formulario {
		width:1200px; 
		text-align:left;
	}

	.resizable-textarea textarea {
		width: 100%;
	}

	div.quadrado_matriz {
		width:110px;
		height:110px;
		border: 1px solid #000;
		float:left;
		line-height:15px;
		text-align: center;
		margin: 4px 4px 4px 4px;
	}

	div.quadrado_matriz.span {
		padding: 1px;
	}

	div.quadro_selecionado {
        -webkit-box-shadow: 9px 7px 5px rgba(50, 50, 50, 0.77);
        -moz-box-shadow:    9px 7px 5px rgba(50, 50, 50, 0.77);
        box-shadow:         9px 7px 5px rgba(50, 50, 50, 0.77);
        border: 3px dashed #000;
        -moz-border-radius:7px;
		-webkit-border-radius:7px;
		border-radius:7px;
	}

	h1.titutlo_h1 {
		text-align:center; 
		font-weight:bold; 
		font-size:18px; 
		color:blue;
	}

	label.instrucao {
		font-weight:bold; 
		font-size:15px;
	}

	table.bloco {
		table-layout:inherit; 
		margin-bottom: 6px; 
		width:100%; 
		border-collapse: collapse;
	}

	label.descricao_bloco {
		font-weight:bold; 
		font-size:15px;
	}

</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_formulario', $row['ds_avaliacao'], TRUE, 'location.reload();');

	if(trim($row['dt_encerramento']) == '' AND trim($row['dt_encerramento_reuniao_avaliacao']) != '')
	{
		$abas[] = array('aba_pdi', 'PDI', FALSE, 'ir_pdi();');
	}

	$head = array( 
        'Conceito',
        'Descrição'
    );

    $this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = FALSE;

	echo aba_start($abas);
		echo '<center><div class="formulario">';
        echo form_open('cadastro/rh_avaliacao/salvar');
        	echo form_start_box('default_formulario_box', 'Formulário Autoavaliação');
        		echo form_default_hidden('cd_avaliacao', '', $row);	
        		echo form_default_hidden('cd_avaliacao_usuario', '', $row);	
        		echo form_default_hidden('cd_avaliacao_usuario_avaliacao', '', $row);	
        		echo form_default_hidden('cd_usuario_avaliador', '', $row);	
        		echo form_default_hidden('tp_avaliacao', '', $row);	
        		echo form_default_hidden('fl_encerramento', '', 'N');
        		echo form_default_hidden('bloco');
        		echo form_default_row('', 'Período:', $row['nr_ano_avaliacao']);
        		echo form_default_row('', 'Avaliado:', $row['ds_avaliado']);
        		echo form_default_row('', 'Admissão:', $row['dt_admissao']);
        		echo form_default_row('', 'Escolaridade:', $row['ds_escolaridade_avaliado']);
        		echo form_default_row('', 'Cargo/Área de Atuação:', $row['ds_cargo_area_atuacao']);
        		echo form_default_row('', 'Classe/Padrão:', $row['ds_classe'].(trim($row['ds_padrao']) != '' ? ' - '.$row['ds_padrao']: ''));
        		echo form_default_row('', 'Escolaridade Exigida do Cargo:', $row['ds_escolaridade_cargo']);
        		echo form_default_row('', 'Avaliador:', $row['ds_avaliador']);
        		echo form_default_row('', 'Avaliação:', '<label class="'.$row['ds_class_avaliacao'].'">'.$row['ds_avaliacao'].'</label>');
        		if(trim($cd_matriz) != '')
        		{
        			echo form_default_row('', 'Resultado:', $cd_matriz);
        			
        			if(trim($ds_promocao_progressao) != '')
        			{
        				echo form_default_row('', 'Tipo:', $ds_promocao_progressao);
        			}
        		}
        		
        	echo form_end_box('default_formulario_box');
        	/*
        	echo form_start_box('default_ocorrencia_ponto_box', 'Ocorrências do Ponto');
        		$head_ocorrencia_ponto = array(
        			'Mês',
					'Tipo de Ocorrência',
					'Quantidade'
        		);

        		$body = array();

                foreach($ocorrencia_ponto as $item)
				{
			        $body[] = array(
			            $item['dt_referencia'],
						$item['ds_ocorrencia_ponto_tipo'],
						array($item['nr_quantidade'], 'text-align : center', TRUE),
			        );
			    }

			    $grid->head = $head_ocorrencia_ponto;
				$grid->body = $body;

				echo $grid->render();

        	echo form_end_box('default_ocorrencia_ponto_box');
			*/
        	if(trim($row['dt_encerramento_reuniao_avaliacao']) == '')
        	{
        		echo form_start_box('default_instrucoes_box', 'Instruções de Preenchimento');
	        		echo '
		        		<tr>
							<td colspan="2">
								<label class="instrucao">'.nl2br($row['ds_instrucao_preenchimento']).'</label>
							</td>
						</tr>
								';
	        	echo form_end_box('default_instrucoes_box');
        	}
        	else if(count($matriz) > 0)
        	{
        		echo br();
        		echo '<center>';
        		echo button_save('Formulário', 'gerar_pdf();', 'botao_vermelho');	
        		echo '</center>';
        		echo 

        		br().'
        		<hr/>
        		<h1 class="titutlo_h1">RESULTADO - '.trim($cd_matriz).'</h1>
        		<BR/>
	        	<center>
	        		<table>';

		        foreach ($matriz as $key => $item) 
		        {
		        	echo '<tr>';

		        		foreach ($item as $key2 => $quadro) 
		        		{
		        			echo '
		        			<td>
			        			<div class="quadrado_matriz'.(trim($quadro['cd_matriz']) == $cd_matriz ? ' quadro_selecionado' : '').'" style="background-color: '.$quadro['cor_fundo'].';">
					            	<span style="color:'.$quadro['cor_texto'].';"><b>'.$quadro['cd_matriz'].'</b>'.br().nl2br($quadro['ds_matriz']).'</span>
					            </div>
		        			</td>';
		        		}

		        	echo '</tr>';
		        }

		        echo '
		        		</table>
		        	</center>';

		        echo form_start_box('default_fortes_box', 'Pontos Fortes', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'  => 'ds_pontos_fortes', 
									'id'    => 'ds_pontos_fortes', 
									'class' => 'resizable',
									'rows'  => 6), $row['ds_pontos_fortes']).'
							</td>
						</tr>';

				echo form_end_box('default_fortes_box');

				echo form_start_box('default_melhorias_box', 'Pontos de Melhorias', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'  => 'ds_pontos_melhorias', 
									'id'    => 'ds_pontos_melhorias', 
									'class' => 'resizable', 
									'rows'  => 6), $row['ds_pontos_melhorias']).'
							</td>
						</tr>';
				echo form_end_box('default_melhorias_box');
				echo $view_pdi;
				echo form_start_box('default_obs_box', 'Observações', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'  => 'ds_observacao', 
									'id'    => 'ds_observacao', 
									'class' => 'resizable', 
									'rows'  => 6), $row['ds_observacao']).'
							</td>
						</tr>';
				echo form_end_box('default_obs_box');
        	}

        	foreach ($collection as $key => $item) 
            {
            	echo br();
            	echo '<hr/>';
                echo '<h1 class="titutlo_h1">'.$item['ds_grupo'].'</h1>';
                echo br();

                $body = array();
                $options = array();

                $options[''] = 'Selecione';

                foreach($item['peformance'] as $peformance)
				{
					$options[$peformance['tp_performance']] = $peformance['ds_performance'];

			        $body[] = array(
			            array($peformance['ds_performance'], 'text-align:left; width:150px;'),
			            array($peformance['ds_performance_descricao'], 'text-align:left')
			        );
			    }

			    $grid->head = $head;
				$grid->body = $body;

				echo $grid->render();

				foreach($item['bloco'] as $bloco)
				{
			        echo form_start_box('default_bloco_box_'.$bloco['cd_avaliacao_bloco'], $bloco['ds_bloco'], FALSE);
			        echo '<table border="0" class="bloco">';
			        	if(trim($bloco['ds_bloco_descricao']) != '')
			        	{
			        		echo '
				        		<tr>
									<td colspan="2">
										<label class="descricao_bloco">'.nl2br($bloco['ds_bloco_descricao']).'</label>
									</td>
								</tr>
							';

							if(trim($bloco['fl_conhecimento']) == 'S')
							{
								echo '
					        		<tr>
										<td colspan="2">
										<br/>
											<a href="javascript:void(0)" onclick="exibir_conhecimentos();">
											<img id="info" src="'.base_url().'img/info.gif" border="0"> Ver Conhecimentos</a>
										</td>
									</tr>
								';
							}
							
							if(count($bloco['pergunta']) > 0)
							{
								echo form_default_row('', '', '');
							}
			        	}
	
			        	foreach($bloco['pergunta'] as $pergunta)
						{
							echo '
								<tr id="pergunta_'.$pergunta['cd_avaliacao_bloco_pergunta'].'_row" class="sort-selecionado" style="border-color: inherit;">
									<td valign="middle" style="text-align:left; vertical-align:middle; width:800px; margin-bottom: 6px; padding:5px 4px 4px 4px;">
										<label style="font-size:14px;" id="pergunta_'.$pergunta['cd_avaliacao_bloco_pergunta'].'_label">
										'.nl2br(trim($pergunta['ds_pergunta'])).'
										</label>
									</td>
									<td align="middle" style="text-align:center; vertical-align:middle; width:100px; padding:4px 0px 4px 0px;">';

										if(trim($pergunta['ds_pergunta']) != '' AND $this->session->userdata('codigo') == intval($row['cd_usuario_avaliador']))
										{
											echo '
											<label style="font-size:11px; color:blue;">
											'.nl2br(trim($pergunta['ds_resposta_avaliado'])).'
											</label>';
										}

										echo form_dropdown('pergunta['.$pergunta['cd_avaliacao_bloco_pergunta'].']', $options, $pergunta['tp_resposta'], 'class="pergunta_'.$pergunta['cd_avaliacao_bloco_pergunta'].' bloco_'.$bloco['cd_avaliacao_bloco'].'"');
								echo '
									</td>
								</tr>
							';
						}

						if(trim($bloco['ds_justificativa_avaliado']) != '' AND $this->session->userdata('codigo') == intval($row['cd_usuario_avaliador']))
						{
							echo '
							<tr>
								<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">
									Justificativa do Avaliado: <label style="font-size:11px; color:blue;"></i>'.nl2br($bloco['ds_justificativa_avaliado']).' </i></label>
								</td>
							</tr>';
						}

						echo '
							<tr>
								<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">
									<label id="justificativa_'.$bloco['cd_avaliacao_bloco'].'_label"> Justificativa:</label> '.form_textarea(array('name' => 'justificativa['.$bloco['cd_avaliacao_bloco'].']', 'id' => 'justificativa['.$bloco['cd_avaliacao_bloco'].']', 'class' => 'resizable justificativa_'.$bloco['cd_avaliacao_bloco'], 'rows' => 4), $bloco['ds_justificativa']).'
								</td>
							</tr>';

			        echo form_end_box('default_bloco_box_'.$bloco['cd_avaliacao_bloco']);
			    }
            }
            echo form_command_bar_detail_start();

	            if(
	            	in_array($this->session->userdata('codigo'), array($row['cd_usuario'], $row['cd_usuario_avaliador'])) 
	            	AND 
	            	$fl_permissao 
	            	AND 
	            	(
		            	(trim($row['dt_encerramento_reuniao_avaliacao']) == '' AND $row['cd_usuario_avaliador'] == $this->session->userdata('codigo'))
		            	OR 
		            	(trim($row['dt_encerramento_autoavaliacao']) == '' AND  $row['cd_usuario'] == $this->session->userdata('codigo'))
	            	)
	            	)
	            {
	            	if($fl_permissao_reuniao_avaliacao)
	            	{
	            		echo '<label class="label label-important" style="font-size:150%;">Avaliado ainda não encerrou a autoavaliação.</label>';
	            	}
	            	else
	            	{
	            		echo br();
	            		echo '<h1 style="color:red; font-size:120%;">Ao encerrar você estará finalizando a avaliação '.trim($row['ds_avaliacao']).'</h1>';
	            		echo br(2);
	            		echo '<div id="btn_salvar">';
	            		echo button_save('Salvar');	
						echo button_save('Salvar e Encerrar', 'salvar_encerrar(form);', 'botao_vermelho');
						echo '</div>';
	            	}
	            }
				
			echo form_command_bar_detail_end();
        echo form_close();
        echo '</div></center>';
        echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>