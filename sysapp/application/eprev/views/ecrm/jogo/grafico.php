<?php
set_title('Jogo - Estrutura');
$this->load->view('header');
?>
<script>

	<?php
		echo form_default_js_submit(Array('ds_jogo',array('dt_inicio','data'),array('hr_inicio','hora'),array('dt_final','data'),array('hr_final','hora')));
	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/jogo"); ?>';
	}
	
	function jogo(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/detalhe"); ?>' + "/" + cd_jogo;
	}	

	function jogoAcerto(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/acerto"); ?>' + "/" + cd_jogo;
	}	

	function jogoPergunta(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/pergunta"); ?>' + "/" + cd_jogo;
	}

	function jogoEstrutura(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/estrutura"); ?>' + "/" + cd_jogo;
	}	
	
	function jogoPerguntaItem(cd_jogo,cd_jogo_pergunta)
	{
		location.href='<?php echo site_url("ecrm/jogo/pergunta_item"); ?>' + "/" + cd_jogo + "/" + cd_jogo_pergunta;
	}

	function jogoImagem(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/imagem"); ?>' + "/" + cd_jogo;
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
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', TRUE, 'location.reload();');
	
	echo aba_start( $abas );

	echo form_start_box( "default_box", "Perguntas" );
		echo "<UL class='jogo_pergunta'>";
		foreach( $pergunta as $ar_pergunta )
		{
			echo "<LI>";
				echo "<b>".($ar_pergunta["nr_ordem"]." - ".$ar_pergunta["ds_pergunta"])."</b><BR>";
				echo '<img src="'.site_url("ecrm/jogo/graficoItem")."/".$ar_pergunta['cd_jogo_pergunta'].'" border="0">';
				
				if($ar_jogo['tp_jogo'] == "A")
				{
					echo '<img src="'.site_url("ecrm/jogo/graficoItemAcerto")."/".$ar_pergunta['cd_jogo_pergunta'].'" border="0">';
					echo '<BR><span style="font-size: 8pt;">(*) Resposta correta</span>';
				}
				
			echo "</LI><BR><BR>";
		}		
		echo "</UL>";
	echo form_end_box("default_box");
?>
<BR><BR>
<?php
	echo aba_end();
	$this->load->view('footer_interna');
?>