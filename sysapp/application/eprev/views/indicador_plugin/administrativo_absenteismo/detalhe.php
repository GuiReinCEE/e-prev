<?php 
	set_title($tabela[0]['ds_indicador']);
	$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(
		"mes_referencia",
		"ano_referencia",
		"cd_indicador_tabela"
	),'_salvar(form)');	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/administrativo_absenteismo"); ?>';
	}
    
    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }

	function _salvar(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function excluir(cd_administrativo_absenteismo)
	{
		var text = "Excluir?\n\n"+
		"[OK] para Sim\n\n"+
		"[Cancelar] para Não";

		if(confirm(text))
		{
			location.href = "<?= site_url('indicador_plugin/administrativo_absenteismo/excluir') ?>/" + cd_administrativo_absenteismo;
		}
	}

	$(function (){
		$('#nr_valor_1').focus();
	});
</script>
<?php
	$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
	$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start( $abas );
?>
	    <? if(count($tabela) == 0) : ?>
		    <div style="width:100%; text-align:center;">
		        <span style="font-size: 12pt; color:red; font-weight:bold;">
		            Nenhum período aberto para criar a tabela do indicador.
		        </span>
		    </div>
	    <? elseif(count($tabela) > 1) : ?>
		    <div style="width:100%; text-align:center;">
		        <span style="font-size: 12pt; color:red; font-weight:bold;">
		            Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.
		        </span>
		    </div>
<?php
		else:
			echo form_open('indicador_plugin/administrativo_absenteismo/salvar');
				echo form_start_box("cadastro_box", 'Cadastro');
					echo form_default_hidden('cd_administrativo_absenteismo', '', $row['cd_administrativo_absenteismo']);
					echo form_default_hidden('cd_indicador_tabela', '', $tabela[0]['cd_indicador_tabela'] );
					echo form_default_hidden('dt_referencia', '', $row);
					echo form_default_row('', 'Indicador:', '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');
            		echo form_default_row('', 'Período aberto:', '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');
					echo form_default_mes_ano('mes_referencia', 'ano_referencia', $label_0.' *', $row['dt_referencia']);
					echo form_default_numeric("nr_valor_1", $label_1.' :', number_format($row['nr_valor_1'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
					echo form_default_numeric("nr_valor_2", $label_2.' :', number_format($row['nr_valor_2'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
					echo form_default_numeric("nr_meta", $label_4.' :', number_format($row['nr_meta'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
					echo form_default_numeric("nr_referencial", $label_6.' :', number_format($row['nr_referencial'],2,",","."), "class='indicador_text'", array("centsLimit" => 2));
					echo form_default_textarea("observacao", $label_7, $row['observacao']);
				echo form_end_box("default_box");
				echo form_command_bar_detail_start();
					echo button_save();

					if(intval($row['cd_administrativo_absenteismo']) > 0)
					{
						echo button_save('Excluir', 'excluir('.$row["cd_administrativo_absenteismo"].')', 'botao_vermelho');
					}
				echo form_command_bar_detail_end();
			echo form_close();
		endif; 
	echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>