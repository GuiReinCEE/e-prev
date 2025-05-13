<?php $this->load->view('header', array('topo_titulo'=>'Cadastro de perguntas de formulário')); ?>
<script>
	<?php echo form_default_js_submit( array( 'cd_contrato_formulario', 'cd_contrato_formulario_grupo', 'ds_contrato_formulario_pergunta', array('nr_ordem', 'int')) ); ?>

	function lista()
	{
		location.href='<?php echo site_url("cadastro/contrato_formulario_pergunta");?>';
	}

	function carregar_grupo()
	{
		document.getElementById("grupo_div").innerHTML = "<?php echo loader_html(); ?>";

		url = "<?php echo site_url('cadastro/contrato_formulario_pergunta/carregar_combo_grupo/').'/'?>"+$('#cd_contrato_formulario').val();
		$.post(url, {}, function(data){ $('#grupo_div').html(data) });
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, "lista()");
$abas[] = array('aba_detalhe', 'Pergunta', true, 'location.reload();');
echo aba_start( $abas );

echo form_open('cadastro/contrato_formulario_pergunta/salvar');
if(isset($row['cd_contrato_formulario_pergunta'])) echo form_hidden('cd_contrato_formulario_pergunta', $row['cd_contrato_formulario_pergunta']);

echo form_start_box( "default_box", "PERGUNTA" );
echo form_default_row("contrato_formulario", "Formulário: *",
		form_dropdown_db( "cd_contrato_formulario"
						, array("projetos.contrato_formulario", "cd_contrato_formulario", "ds_contrato_formulario")
						, $row['cd_contrato_formulario']
						, " onchange='carregar_grupo();' "
						, " dt_exclusao IS NULL "
						)
);

if(intval($row['cd_contrato_formulario_grupo'])>0)
{
	echo form_default_row( "contrato_formulario_grupo", "Grupo: *", "<div id='grupo_div'>".
			form_dropdown_db(
				"cd_contrato_formulario_grupo"
				, array("projetos.contrato_formulario_grupo", "cd_contrato_formulario_grupo", "ds_contrato_formulario_grupo")
				, $row['cd_contrato_formulario_grupo']
				, ''
				, 'cd_contrato_formulario='.intval($row['cd_contrato_formulario'])
				, " dt_exclusao IS NULL "
			)
	."</div>" );
}
else
{
	echo form_default_row( "contrato_formulario_grupo", "Grupo: *", "<div id='grupo_div' style='font-size:12px;'>escolha um formulário antes de escolher o grupo</div>" );
}

echo form_default_text( 'ds_contrato_formulario_pergunta', 'Pergunta: *', $row, " style='width:600px' " );
echo form_default_text( 'fl_multipla_resposta', 'Multipla:', $row);
echo form_default_integer( 'nr_ordem', 'Ordem: *', $row );
echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
echo button_save();
echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='" . site_url('cadastro/contrato_formulario_pergunta') . "'; }");
echo form_command_bar_detail_end();
?>
<script>
	$('#cd_contrato_formulario').focus();
</script>

<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
