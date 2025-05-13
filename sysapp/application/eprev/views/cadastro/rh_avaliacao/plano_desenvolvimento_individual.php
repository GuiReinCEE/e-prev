<?php
	set_title('Sistema de Avaliação - PDI');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_pontos_fortes', 'ds_pontos_melhorias'), 'valida_form(form)'); ?>
	
	function valida_form(form)
	{
		if(<?= $row['tl_dpi'] ?> == 0)
		{
			alert('Preencha ao menos um item do PDI.');
		}
		else if(<?= $row['tl_dpi_preenchido'] ?> > 0)
		{
			alert('Preencha todas as informações do PDI.');
		}
		else
		{
			confirmacao = 'Deseja encerrar o PDI?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
		
			if(confirm(confirmacao))
			{ 
				form.submit();
			}
		}
	}

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao') ?>";
	}

	function ir_formulario()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao/formulario/'.$row['cd_avaliacao_usuario']) ?>";
	}

	function salvar_pontos_fortes()
	{
		if($("#ds_pontos_fortes").val() != '')
		{
			$.post("<?= site_url('cadastro/rh_avaliacao/salvar_pontos_fortes') ?>",
			{
				cd_avaliacao_usuario : <?= $row['cd_avaliacao_usuario'] ?>,
				ds_pontos_fortes     : $("#ds_pontos_fortes").val()
			},function(data){});
		}
	}

	function salvar_pontos_melhorias()
	{
		if($("#ds_pontos_melhorias").val() != '')
		{
			$.post("<?= site_url('cadastro/rh_avaliacao/salvar_pontos_melhorias') ?>",
			{
				cd_avaliacao_usuario : <?= $row['cd_avaliacao_usuario'] ?>,
				ds_pontos_melhorias  : $("#ds_pontos_melhorias").val()
			},function(data){});
		}
	}

	function salvar_observacao()
	{
		if($("#ds_observacao").val() != '')
		{
			$.post("<?= site_url('cadastro/rh_avaliacao/salvar_observacao') ?>",
			{
				cd_avaliacao_usuario : <?= $row['cd_avaliacao_usuario'] ?>,
				ds_observacao        : $("#ds_observacao").val()
			},function(data){});
		}
	}

	function adicionar(cd_avaliacao_usuario_plando_desenvolvimento)
	{
		$.post("<?= site_url('cadastro/rh_avaliacao/cadastro_plano_desenvolvimeto_individual/'.$row['cd_avaliacao'].'/'.$row['cd_avaliacao_usuario']) ?>/"+cd_avaliacao_usuario_plando_desenvolvimento,
		function(data)
		{
			$("#gridWindowTexto").html(data);

			$('#gridWindow').modal({
				focus:false,
				autoResize: true,
				containerCss:{
					width:650,
					height:650
					},
				onClose: function (dialog) {
					$.modal.close();
				}					
			});	
		});
	}

	function excluir(cd_avaliacao_usuario_plando_desenvolvimento)
	{
		confirmacao = 'Deseja excluir o item do PDI?\n\n'+
					'Clique [Ok] para Sim\n\n'+
					'Clique [Cancelar] para Não\n\n';
		

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('cadastro/rh_avaliacao/excluir_plano_desenvolvimeto_individual/'.$row['cd_avaliacao_usuario']) ?>/"+cd_avaliacao_usuario_plando_desenvolvimento;
		}
	}

	function listar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_avaliacao/listar_plano_desenvolvimeto_individual') ?>",
		{
			cd_avaliacao_usuario : $("#cd_avaliacao_usuario").val()
		},
		function(data)
		{
			$("#result_div").html(data);
		});
	}

	function mostrar()
	{
		$("#btn_mostrar").hide();
		$("#btn_ocultar").show();
		$("#treinamento_div").show();
	}

	function ocultar()
	{
		$("#btn_mostrar").show();
		$("#btn_ocultar").hide();
		$("#treinamento_div").hide();
	}

	$(function(){
		listar();
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

	#treinamento_div {
		overflow:auto; 
		width: 100%; 
		height: 300px; 
		display: none;
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_formulario', 'Formulário Avaliação', FALSE, 'ir_formulario()');
	$abas[] = array('aba_pdi', 'PDI', TRUE, 'location.reload();');

	 $head = array( 
        'Número',
        'Nome',
        'Promotor',
        'Dt. Início',
        'Dt. Final',
        'Tipo',
        'Carga<br/>Horária(h)'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            $item['numero'],
            array($item['nome'], 'text-align:left'),
            array($item['promotor'],'text-align:left'),
            $item['dt_inicio'],
            $item['dt_final'],
            array($item['ds_treinamento_colaborador_tipo'],'text-align:left'),
            $item['carga_horaria']
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
    $grid->view_count = FALSE;

	echo aba_start($abas); 
		echo '<center><div class="formulario">';
			echo form_open('cadastro/rh_avaliacao/encerrar_avaliacao_usuario');
				echo form_start_box('default_pdi_box', 'Cadastro PDI');
					echo form_default_hidden('cd_avaliacao', '', $row);	
	        		echo form_default_hidden('cd_avaliacao_usuario', '', $row);	
	        		echo form_default_hidden('cd_avaliacao_usuario_avaliacao', '', $row);	
	        		echo form_default_hidden('cd_usuario_avaliador', '', $row);	
	        		echo form_default_hidden('tp_avaliacao', '', $row);	
	        		echo form_default_hidden('fl_encerramento', '', 'N');	
	        		echo form_default_row('', 'Período:', $row['nr_ano_avaliacao']);
	        		echo form_default_row('', 'Avaliado:', $row['ds_avaliado']);
	        		echo form_default_row('', 'Admissão:', $row['dt_admissao']);
	        		echo form_default_row('', 'Cargo/Área de Atuação:', $row['ds_cargo_area_atuacao']);
	        		echo form_default_row('', 'Classe/Padrão:', $row['ds_classe'].' - '.$row['ds_padrao']);
	        		echo form_default_row('', 'Avaliador:', $row['ds_avaliador']);
				echo form_end_box('default_pdi_box');
				echo form_start_box('default_fortes_box', 'Pontos Fortes', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'   => 'ds_pontos_fortes', 
									'id'     => 'ds_pontos_fortes', 
									'class'  => 'resizable',
									'onblur' => 'salvar_pontos_fortes();',
									'rows'   => 6), $row['ds_pontos_fortes']).'
							</td>
						</tr>';

				echo form_end_box('default_fortes_box');
				echo form_start_box('default_melhorias_box', 'Pontos de Melhorias', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'   => 'ds_pontos_melhorias', 
									'id'     => 'ds_pontos_melhorias', 
									'class'  => 'resizable', 
									'onblur' => 'salvar_pontos_melhorias();',
									'rows'   => 6), $row['ds_pontos_melhorias']).'
							</td>
						</tr>';
				echo form_end_box('default_melhorias_box');
				echo '<center id="result_div"></center>';

				echo '<div id="treinamento_div">';
				echo br();
				echo '<h3>Treinamento(s) Realizado(s)</h3>';
				echo  $grid->render();
				echo '</div>';

				echo form_start_box('default_obs_box', 'Observações', FALSE);
					echo '
						<tr>
							<td colspan="2" valign="top" style="text-align:left; padding-top:5px; padding-right:5px; width:100%;">'.
								form_textarea(array(
									'name'   => 'ds_observacao', 
									'id'     => 'ds_observacao', 
									'class'  => 'resizable', 
									'onblur' => 'salvar_observacao();',
									'rows'   => 6), $row['ds_observacao']).'
							</td>
						</tr>';
				echo form_end_box('default_obs_box');
				echo form_command_bar_detail_start();

	            	echo button_save('Encerrar', 'salvar(form);', 'botao_vermelho');	
				
				echo form_command_bar_detail_end();
			echo form_close();
		echo '</div></center>';
        echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>
