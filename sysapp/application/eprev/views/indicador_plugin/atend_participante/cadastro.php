<?php
set_title($tabela[0]['ds_indicador']);
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(array("mes_referencia","ano_referencia", "cd_indicador_tabela"),'_salvar(form)');	?>

	function _salvar(form)
	{
		$('#dt_referencia').val(  '01/'+$('#mes_referencia').val()+'/'+$('#ano_referencia').val() );

		if(confirm('Salvar?'))
		{
			form.submit();
		}
	}

	function ir_lista()
	{
		location.href='<?php echo site_url("indicador_plugin/atend_participante"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/"); ?>';
    }

    function excluir()
	{
		location.href = '<?= site_url("indicador_plugin/atend_participante/excluir/".$row["cd_atend_participante"]) ?>';
	}

    $('#nr_ceee').focus();
</script>
<?php
if(count($tabela) == 0)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Nenhum período aberto para criar a tabela do indicador.</span>';
	exit;
}
else if(count($tabela) > 1)
{
	echo '<span style="font-size: 12pt; color:red; font-weight:bold;">Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>';
	exit;			
}

$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open( 'indicador_plugin/atend_participante/salvar' );
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_indicador_tabela', 'Código indicador tabela', $tabela[0]['cd_indicador_tabela']);

			echo form_default_hidden('cd_atend_participante', 'Código da tabela', intval($row['cd_atend_participante']));

			echo form_default_row("", "Indicador:", '<span class="label label-inverse">'.$tabela[0]['ds_indicador'].'</span>');

			echo form_default_row("", "Período aberto:", '<span class="label label-important">'.$tabela[0]['ds_periodo'].'</span>');

			echo form_default_row("","","");
			echo form_default_hidden('dt_referencia', $label_0.": (*)", $row); 
			echo form_default_mes_ano( 'mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia'] );
			echo form_default_hidden("nr_cgtee", $label_3.': ', app_decimal_para_php($row['nr_cgtee']), "class='indicador_text'");
			echo form_default_hidden("nr_sinpro", $label_7.': ', app_decimal_para_php($row['nr_sinpro']), "class='indicador_text'");

	        echo form_default_float("nr_ceee", $label_1.': ', app_decimal_para_php($row['nr_ceee']), "class='indicador_text'");
	        echo form_default_float("nr_rge", $label_4.': ', app_decimal_para_php($row['nr_rge']), "class='indicador_text'");
			echo form_default_float("nr_aes", $label_2.': ', app_decimal_para_php($row['nr_aes']), "class='indicador_text'");
			echo form_default_float("nr_crm", $label_5.': ', app_decimal_para_php($row['nr_crm']), "class='indicador_text'");
			echo form_default_float("nr_inpel", $label_9.': ', app_decimal_para_php($row['nr_inpel']), "class='indicador_text'");
			echo form_default_float("nr_ceran", $label_10.': ', app_decimal_para_php($row['nr_ceran']), "class='indicador_text'");
			echo form_default_float("nr_foz", $label_11.': ', app_decimal_para_php($row['nr_foz']), "class='indicador_text'");
			echo form_default_float("nr_familia_municipio", $label_15.': ', app_decimal_para_php($row['nr_familia_municipio']), "class='indicador_text'");
			echo form_default_float("nr_ieabprev", $label_16.': ', app_decimal_para_php($row['nr_ieabprev']), "class='indicador_text'");
			echo form_default_float("nr_senge", $label_6.': ', app_decimal_para_php($row['nr_senge']), "class='indicador_text'");
			echo form_default_float("nr_familia", $label_8.': ', app_decimal_para_php($row['nr_familia']), "class='indicador_text'");
			echo form_default_float("nr_meta", $label_13.': ', app_decimal_para_php($row['nr_meta']), "class='indicador_text'");
			echo form_default_textarea("observacao", $label_14.': ', $row['observacao']);
		echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		echo button_save();
		if( intval($row['cd_atend_participante'])>0  )
		{
			echo button_save('Excluir', 'excluir();', 'botao_vermelho');
		}
	echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>