<?php 
if($tabela)
{
	$ds_tabela_periodo = "<big>".$tabela[0]['ds_indicador'] . " - " . $tabela[0]['ds_periodo']."</big>";
}
else
{
	$ds_tabela_periodo = "";
}

set_title($tabela[0]['ds_indicador']);
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array(

		array("mes_referencia", "int")
		,array("ano_referencia", "int") 
		,array("cd_indicador_tabela", "int")
		,'nr_valor_1'
		,'nr_valor_2'
		,'nr_valor_3'
		,'nr_valor_4'
		,'nr_valor_5'
		,'nr_valor_6'
		,'nr_valor_7'
		,'nr_valor_8'
		,'nr_valor_9'
		,'nr_valor_10'
		,'nr_valor_11'

	),'_salvar(form)');	?>

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
		location.href='<?php echo site_url("indicador_atendimento/desligamento_inadi"); ?>';
	}

    function manutencao()
    {
        location.href='<?php echo site_url("indicador/manutencao/index/11/A/"); ?>';
    }
</script>
<?php
$abas[] = array( 'aba_lista', 'Lista', false, 'manutencao();' );
$abas[] = array('aba_lista', 'Lançamento', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );

echo form_open( 'indicador_atendimento/desligamento_inadi/salvar' );
echo form_hidden( 'cd_desligamento_inadi', intval($row['cd_desligamento_inadi']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", $tabela[0]['ds_indicador'] );

if( sizeof($tabela)==1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e período aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'].br(2) );
}
elseif( sizeof($tabela)>1 )
{
	echo form_default_hidden( 'cd_indicador_tabela', 'Código da tabela', $tabela[0]['cd_indicador_tabela'] ); 
	echo form_default_row( "", "Indicador e período aberto", $tabela[0]['ds_indicador'] . ' - ' . $tabela[0]['ds_periodo'] );
	echo form_default_row( "", "", "<span style='font-size:12;'>Existe mais de um período aberto, no entanto só será possível incluir valores para o novo período depois de fechar o mais antigo.</span>".br(2) );
}
else
{
	// nenhum período aberto para esse indicador
	echo form_default_row(  "", "Indicador e período aberto", "Nenhum período aberto para criar a tabela do indicador." );
}

echo form_default_mes_ano( 'mes_referencia', 'ano_referencia', $label_0.': (*)', $row['dt_referencia'] ); 
echo form_default_hidden('dt_referencia', 'Mês', $row);

echo form_default_integer("nr_valor_1", $label_1.': (*)', ($row['nr_valor_1']), "class='indicador_text'"); 
echo form_default_integer("nr_valor_2", $label_2.': (*)', ($row['nr_valor_2']), "class='indicador_text'");
echo form_default_integer("nr_valor_3", $label_3.': (*)', ($row['nr_valor_3']), "class='indicador_text'");
echo form_default_integer("nr_valor_4", $label_4.': (*)', ($row['nr_valor_4']), "class='indicador_text'");
echo form_default_integer("nr_valor_5", $label_5.': (*)', ($row['nr_valor_5']), "class='indicador_text'");
echo form_default_integer("nr_valor_6", $label_6.': (*)', ($row['nr_valor_6']), "class='indicador_text'");
echo form_default_integer("nr_valor_7", $label_7.': (*)', ($row['nr_valor_7']), "class='indicador_text'");
echo form_default_integer("nr_valor_8", $label_8.': (*)', ($row['nr_valor_8']), "class='indicador_text'");
echo form_default_integer("nr_valor_9", $label_9.': (*)', ($row['nr_valor_9']), "class='indicador_text'");
echo form_default_integer("nr_valor_10", $label_10.': (*)', ($row['nr_valor_10']), "class='indicador_text'");
echo form_default_integer("nr_valor_11", $label_11.': (*)', ($row['nr_valor_11']), "class='indicador_text'");

if( $row['fl_media']=='S' )
{
	//echo form_default_row("", "Média", "<input id='fl_media' name='fl_media' type='checkbox' checked value='S' />");
}
else
{
	//echo form_default_row("", "Média", "<input id='fl_media' name='fl_media' type='checkbox' value='S' />");
}

echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();

if( intval($row['cd_desligamento_inadi'])>0  )
{
	echo button_delete("indicador_atendimento/desligamento_inadi/excluir",$row["cd_desligamento_inadi"]);
}

echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('indicador_atendimento/desligamento_inadi')."'; }");
echo form_command_bar_detail_end();
?>
<script>
	$('#nr_valor_1').focus();
</script>
<?php
echo aba_end();
echo form_close();

$this->load->view('footer_interna');
?>