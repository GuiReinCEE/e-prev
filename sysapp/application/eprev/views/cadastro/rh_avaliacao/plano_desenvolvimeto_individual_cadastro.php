<script>
	<?= form_default_js_submit(array('ds_avaliacao_usuario_plando_desenvolvimento', 'ds_plano_melhoria', 'ds_resultado', 'ds_responsavel', 'ds_quando')); ?>

	$(function(){
		if("<?= $row['fl_formulario'] ?>" == "S")
		{
			$("#ds_avaliacao_usuario_plando_desenvolvimento").attr("disabled", "disabled");
		}
	});
</script>
<style>
	#gridWindowTexto {
		overflow-y:hidden;
		overflow-x:hidden;
		font-size: 0pt; 
	}

	.txtCampo{
		font-size: 10pt;
	}
</style>
<?php
	echo form_open('cadastro/rh_avaliacao/salvar_plano_desenvolvimeto_individual');
		echo '
			<input type="hidden" id="cd_avaliacao" name="cd_avaliacao" value="'.$row['cd_avaliacao'].'" />
			<input type="hidden" id="cd_avaliacao_usuario" name="cd_avaliacao_usuario" value="'.$row['cd_avaliacao_usuario'].'" />
			<input type="hidden" id="cd_avaliacao_usuario_plando_desenvolvimento" name="cd_avaliacao_usuario_plando_desenvolvimento" value="'.$row['cd_avaliacao_usuario_plando_desenvolvimento'].'" />

			<h2 style="font-size: 26pt;">Cadastro</h2>
			<table style=" border-spacing: 0 15px;">
				<tr>
					<td style="text-align:left;">
						<label>Competência/Fator de Desempenho: (*)</label> '.
							form_textarea(array(
								'name'  => 'ds_avaliacao_usuario_plando_desenvolvimento', 
								'id'    => 'ds_avaliacao_usuario_plando_desenvolvimento',  
								'style' => 'width:98%',

								'rows'  => 6), $row['ds_avaliacao_usuario_plando_desenvolvimento']).'
					</td>
				</tr>
				<tr style="margin-top:5px;">
					<td style="text-align:left;">
						<label>Plano para Melhoria do Desempenho: (*)</label> '.
							form_textarea(array(
								'name'  => 'ds_plano_melhoria', 
								'id'    => 'ds_plano_melhoria',  
								'style' => 'width:98%',
								'rows'  => 6), $row['ds_plano_melhoria']).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;">
						<label>Resultado Esperado: (*)</label> '.
							form_textarea(array(
								'name'  => 'ds_resultado', 
								'id'    => 'ds_resultado',  
								'style' => 'width:98%',
								'rows'  => 6), $row['ds_resultado']).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;">
						<label class="txtCampo">Responsável (Quem): (*)</label> '.
							form_input(array(
								'type'  => 'text',
								'name'  => 'ds_responsavel', 
								'id'    => 'ds_responsavel',  
								'style' => 'width:98%'), $row['ds_responsavel']).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;">
						<label class="txtCampo">Quando (Prazo): (*)</label> '.
							form_input(array(
								'type'  => 'text',
								'name'  => 'ds_quando', 
								'id'    => 'ds_quando',  
								'style' => 'width:98%'), $row['ds_quando']).'
					</td>
				</tr>
			</table>';
		echo form_command_bar_detail_start();
            echo button_save('Salvar');	
		echo form_command_bar_detail_end();
    echo form_close()
?>