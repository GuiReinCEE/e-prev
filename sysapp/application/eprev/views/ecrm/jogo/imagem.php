<?php
set_title('Jogo - Estrutura');
$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('cd_jogo'));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}
	
	function jogo(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/detalhe"); ?>' + "/" + cd_jogo;
	}	

	function jogoEstrutura(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/estrutura"); ?>' + "/" + cd_jogo;
	}

	function jogoResultado(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/resultado"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoGrafico(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/grafico"); ?>' + "/" + cd_jogo;
	}	
</script>

<style type="text/css">
.jogo_pergunta ul
{
	margin-left: 40px;
}

.jogo_pergunta_titulo
{
	width: 100%;
	padding: 2px;
	background-color: #DAE9F7;
}

.jogo_pergunta_titulo a
{
	font-weight: bold;
}

.jogo_pergunta_complemento
{
	margin-left: 40px;
}

</style>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_jogo', 'Cadastro', FALSE, "jogo('".$cd_jogo."');");
	$abas[] = array('aba_estrutura', 'Estrutura', FALSE, "jogoEstrutura('".$cd_jogo."');");
	$abas[] = array('aba_imagem', 'Imagens', TRUE, "location.reload();");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");

	echo form_open('ecrm/jogo/salvarImagem');
	echo aba_start( $abas );
	
	
	echo form_start_box("default_acerto", "Cadastro" );
		echo form_default_text('cd_jogo', "Código: ", $cd_jogo, "style='width:100%;border: 0px;' readonly" );
		
		echo form_default_upload_iframe('img_botao', 'jogo', 'Botão: (L = 450px, A = 180px, .jpg)');
		echo form_default_row('', 'Botão Atual:', '<img src="../../../../../eletroceee/img/jogo/botao_'.$cd_jogo.'.jpg" border="0">');
		
		echo form_default_upload_iframe('img_inicio', 'jogo', 'Início: (L = 700px, A = 450px, .jpg)');
		echo form_default_row('', 'Início Atual:', '<img src="../../../../../eletroceee/img/jogo/inicio_'.$cd_jogo.'.jpg" border="0">');
		
		echo form_default_upload_iframe('img_instrucao', 'jogo', 'Instrução: (L = 700px, A = 450px, .jpg)');
		echo form_default_row('', 'Instrução Atual:', '<img src="../../../../../eletroceee/img/jogo/instrucao_'.$cd_jogo.'.jpg" border="0">');
		
		echo form_default_upload_iframe('img_pergunta', 'jogo', 'Pergunta: (L = 700px, A = 450px, .jpg)');
		echo form_default_row('', 'Pergunta Atual:', '<img src="../../../../../eletroceee/img/jogo/pergunta_'.$cd_jogo.'.jpg" border="0">');
		
		echo form_default_upload_iframe('img_proxima', 'jogo', 'Próxima: (L = 150px, A = até 60px, .png)');
		echo form_default_row('', 'Próxima Atual:', '<img src="../../../../../eletroceee/img/jogo/proxima_'.$cd_jogo.'.png" border="0">');
		
		echo form_default_upload_iframe('img_resultado', 'jogo', 'Resultado: (L = 700px, A = 450px, .jpg)');
		echo form_default_row('', 'Resultado Atual:', '<img src="../../../../../eletroceee/img/jogo/resultado_'.$cd_jogo.'.jpg" border="0">');
		
	echo form_end_box("default_acerto");	
	
	echo form_command_bar_detail_start();
		echo button_save();
	echo form_command_bar_detail_end();	

?>
<br><br>
<?php
	echo aba_end();
	echo form_close();
	$this->load->view('footer_interna');
?>