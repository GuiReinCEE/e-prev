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
	
	function jogoGrafico(cd_jogo)
	{
		location.href='<?php echo site_url("ecrm/jogo/grafico"); ?>' + "/" + cd_jogo;
	}

	function jogoFixaPergunta(cd_jogo)
	{
		var cd_fixa_pri = ($("#cd_jogo_pergunta_fixa_inicio").val() == "" ? 0 : $("#cd_jogo_pergunta_fixa_inicio").val());
		var cd_fixa_ult = ($("#cd_jogo_pergunta_fixa_ultima").val() == "" ? 0 : $("#cd_jogo_pergunta_fixa_ultima").val());
		
		if((cd_fixa_pri == cd_fixa_ult) && (cd_fixa_pri > 0) && (cd_fixa_ult > 0))
		{
			alert("A primeira e última pergunta não podem ser a mesma.");
		}
		else
		{
			location.href='<?php echo site_url("ecrm/jogo/jogoFixaPergunta"); ?>' + "/" + cd_jogo + "/" + cd_fixa_pri + "/" + cd_fixa_ult;
		}
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
	$abas[] = array('aba_estrutura', 'Estrutura', TRUE, 'location.reload();');
	$abas[] = array('aba_imagem', 'Imagens',  FALSE, "jogoImagem('".$cd_jogo."');");
	$abas[] = array('aba_resultado', 'Resultado',  FALSE, "jogoResultado('".$cd_jogo."');");
	$abas[] = array('aba_grafico', 'Gráfico', FALSE, "jogoGrafico('".$cd_jogo."');");

	echo aba_start( $abas );

	echo form_start_box("default_acerto", "Mensagem Resultado" );
		echo button_save("Novo Resultado","jogoAcerto(".$cd_jogo.")");
		echo br(2);
		$body=array();
		$head = array( 
			'Código',
			'Mensagem',
			'Resultado Início',
			'Resultado Final'
		);

		foreach($acerto as $item )
		{
			$body[] = array(
			anchor("ecrm/jogo/acerto/".$item["cd_jogo"]."/".$item["cd_jogo_acerto"], $item["cd_jogo_acerto"]),
			array($item["ds_mensagem"],"text-align:left;"),
			$item["qt_inicio"],
			$item["qt_final"]
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();		
		
	echo form_end_box("default_acerto");	
	
	echo form_start_box("default_fixa", "Fixar pergunta");
		$ar_fixa_pergunta[] = Array("value" => "", "text" => "Nenhuma");
		foreach($pergunta as $ar_pergunta)
		{
			$ar_fixa_pergunta[] = Array("value" => $ar_pergunta['cd_jogo_pergunta'], "text" => ($ar_pergunta["nr_ordem"]." - ".$ar_pergunta["ds_pergunta"]));
		}
		echo form_default_dropdown('cd_jogo_pergunta_fixa_inicio', 'Primeira:', $ar_fixa_pergunta, Array($ar_jogo['cd_jogo_pergunta_fixa_inicio']));		
		echo form_default_dropdown('cd_jogo_pergunta_fixa_ultima', 'Última:', $ar_fixa_pergunta, Array($ar_jogo['cd_jogo_pergunta_fixa_ultima']));		
		
		echo form_default_row('','', button_save("Salvar","jogoFixaPergunta(".$cd_jogo.")"));
		
	echo form_end_box("default_fixa");	
	
	echo form_start_box( "default_box", "Perguntas" );
		echo button_save("Nova Pergunta","jogoPergunta(".$cd_jogo.")");
		echo "<BR><BR>";
		echo "<UL class='jogo_pergunta'>";
		foreach( $pergunta as $ar_pergunta )
		{
			echo "<LI>";
			echo "<div class='jogo_pergunta_titulo'>";
			echo anchor("ecrm/jogo/pergunta/".$cd_jogo."/".$ar_pergunta['cd_jogo_pergunta'],($ar_pergunta["nr_ordem"]." - ".$ar_pergunta["ds_pergunta"]));
			echo "</div>";
			echo "<div>";
			echo ($ar_jogo['tp_jogo'] == "A" ? "Exibir resposta correta: ".($ar_pergunta['fl_exibe_resposta'] == "S" ? "Sim" : "Não") : "");
			echo "</div>";

			echo "<BR>";
			if(trim($ar_pergunta["ds_complemento"]) != "")
			{
				echo "<div class='jogo_pergunta_complemento'>".nl2br($ar_pergunta["ds_complemento"])."</div>";
				echo "<BR>";
			}
				echo "<UL>";
				$cor = "#F2F8FC";
				$cor_atual = "";
				foreach($pergunta_item[$ar_pergunta['cd_jogo_pergunta']] as $ar_item )
				{
					$cor_atual = ($cor == $cor_atual ? "#FFFFFF" : $cor);
					
					echo "<DIV style='background-color:".$cor_atual."; padding: 1px;'>";
					echo anchor("ecrm/jogo/pergunta_item/".$cd_jogo."/".$ar_pergunta['cd_jogo_pergunta']."/".$ar_item['cd_jogo_pergunta_item'], ("(".($ar_jogo['tp_jogo'] == "A" ? ($ar_item["fl_certo"] == "S" ?  "<b>X</b>" : "&nbsp;&nbsp;") : "<b>".$ar_item["vl_resposta"]."</b>").") ".$ar_item["nr_ordem"]." - ".$ar_item["ds_item"]));
					echo "</DIV>";
					
					echo "</LI>";
				}	
				
				echo "</UL>";
			echo "<div class='jogo_pergunta_complemento'><BR>";
			echo button_save("Nova Resposta","jogoPerguntaItem(".$cd_jogo.",".$ar_pergunta['cd_jogo_pergunta'].")");				
			echo "</div>";
			
			echo "</LI><BR>";
		}		
		echo "</UL>";
	echo form_end_box("default_box");
	echo br(5);
?>
<BR><BR>
<?php
	echo aba_end();
	$this->load->view('footer_interna');
?>