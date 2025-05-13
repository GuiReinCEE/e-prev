<?php
set_title('Eleições - Apuração');
$this->load->view('header');
?>
<style>
	.coluna-padrao-form {
		width: 300px;
	}
	
	.label-padrao-form {
		font-size: 12pt;
	}
</style>
<script>
	<?php 
		if ($row["situacao"] == "G")
		{
			#### ABRE APURAÇÃO ####	
			echo form_default_js_submit(
				array(
						"id_eleicao",
						"qt_kit_recebido"
					  ),
					'apuracao_abrir(form)');	
		}
		elseif ($row["situacao"] == "A") 
		{
			#### LANCAR VOTOS ####		
			echo form_default_js_submit(
				array(
						"id_eleicao",
						"qt_kit_recebido"
					  ),
					'apuracao_lanca(form)');			
		}
	?>	
	
	function apuracao_abrir(form)
	{
		var confirmacao = 'ATENÇÃO\n\nTOTAL DE KITS RECEBIDOS: ' + $("#qt_kit_recebido").val() + '\n\n\nConfirma a quantidade de Kits recebidos?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{		
			form.submit();
		}	
	}
	
	function apuracao_lanca(form)
	{
		if(apuracao_validar())
		{		
			form.submit();
		}	
	}	

	var ar_campo_deliberativo = Array();
	var ar_campo_fiscal       = Array();
	var ar_campo_diretor      = Array();
	
	function apuracao_validar()
	{
		var qt_total_voto = 0;
		var qt_total_invalido = 0;
		var qt_total_invalido_total = 0;
		
		if(parseInt($('#qt_total_invalido').val()) > 0)
		{
			qt_total_invalido = parseInt($('#qt_total_invalido').val());
		}		
			
		if(parseInt($('#qt_total_invalido-total').val()) > 0)
		{
			qt_total_invalido_total+= parseInt($('#qt_total_invalido-total').val());
		}
		
		//CONSELHO DELIBERATIVO
		var qt_total_voto_deliberativo = 0;
		for(i=0; i < ar_campo_deliberativo.length; i++)
		{
			if(parseInt($("#"+ar_campo_deliberativo[i]).val()) > 0)
			{
				qt_total_voto_deliberativo += parseInt($("#"+ar_campo_deliberativo[i]).val());
			}
		}

		//TESTAR AQUI TIPO ELEICAO
		if(qt_total_voto_deliberativo > 0)
		{
			qt_total_voto_deliberativo = qt_total_voto_deliberativo / parseInt($('#cd_tipo').val());
		}
		
		//CONSELHO FISCAL
		var qt_total_voto_fiscal = 0;
		for(i=0; i < ar_campo_fiscal.length; i++)
		{
			if(parseInt($("#"+ar_campo_fiscal[i]).val()) > 0)
			{
				qt_total_voto_fiscal += parseInt($("#"+ar_campo_fiscal[i]).val());
			}			
		}	

		//DIRETOR
		var qt_total_voto_diretor = qt_total_voto_fiscal; //QUANDO NAO TEM ELEICAO PARA DIRETOR
		//var qt_total_voto_diretor = 0;
		for(i=0; i < ar_campo_diretor.length; i++)
		{
			if(parseInt($("#"+ar_campo_diretor[i]).val()) > 0)
			{
				qt_total_voto_diretor += parseInt($("#"+ar_campo_diretor[i]).val());
			}			
		}				
		
		qt_total_voto = qt_total_voto_deliberativo + qt_total_voto_fiscal + qt_total_voto_diretor;
		
		console.log("qt_total_voto_deliberativo => " + qt_total_voto_deliberativo);
		console.log("qt_total_voto_fiscal => " + qt_total_voto_fiscal);
		console.log("qt_total_voto_diretor => " + qt_total_voto_diretor);
		console.log("qt_total_voto => " + qt_total_voto);
		
		if	(
			(qt_total_voto_deliberativo != qt_total_voto_fiscal)
			||
			(qt_total_voto_deliberativo != qt_total_voto_diretor)
			||
			(qt_total_voto_diretor != qt_total_voto_fiscal)
			)
		{
			alert('Número total de votos por cargo não é igual.');
			return false;					
		}
		else if(parseInt($('#qt_total_recebido').val()) < (parseInt($('#qt_total_apurado').val()) + parseInt(qt_total_voto_deliberativo) + qt_total_invalido + qt_total_invalido_total)) 
		{
			alert('Número Total de Votos (Kits inválidos + Votos Válidos + Voto do Lote) é maior que o total de Kits Recebidos.');
			return false;
		}
		else
		{
			if(confirm("Número de votos do lote é:\n- Votos Válidos => " + qt_total_voto_deliberativo + "\n- Kits Inválidos => " + qt_total_invalido + "\n\nPara confirmar clique no botão [Ok]."))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}	
	
	
	function apuracao_encerrar(id_eleicao)
	{
		var qt_total_invalido = 0;

		if(parseInt($('#qt_total_invalido-total').val()) > 0)
		{
			qt_total_invalido = parseInt($('#qt_total_invalido-total').val());
		}			
	
		if(parseInt($("#qt_total_recebido").val()) != (parseInt($("#qt_total_apurado").val()) + qt_total_invalido))
		{
			alert("Não foi possível encerrar a Eleição.\n\nExiste uma diferença entre Total de Kits Recebidos ("+parseInt($("#qt_total_recebido").val())+") e o Total de Votos Válidos + Kits inválidos ("+(parseInt($("#qt_total_apurado").val()) + qt_total_invalido)+").");
		}
		else
		{
			var confirmacao = $("#ds_eleicao").val() + '\n\n'+
			                  'Confirma o ENCERRAMENTO?\n\n'+
							  'Clique [Ok] para Sim\n\n'+
							  'Clique [Cancelar] para Não\n\n';	

			if(confirm(confirmacao))
			{		
				location.href = '<?php echo site_url("gestao/eleicoes_apuracao/apuracao_encerrar"); ?>' + "/" + id_eleicao;
			}			
		}
	}	
	
    function lista()
    {
        location.href='<?php echo site_url("gestao/eleicoes_apuracao"); ?>';
    }
	
	function ir_lote()
    {
        location.href='<?php echo site_url("gestao/eleicoes_apuracao/lote/".$row['id_eleicao']); ?>';
    }
</script>
<?php
$scr_campo = "";

if ($row["situacao"] == "G")
{
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Apuração', TRUE, 'location.reload();');
echo aba_start($abas);

	#### ABRE APURAÇÃO ####
	echo form_open('gestao/eleicoes_apuracao/apuracao_abrir');
		echo form_start_box("default_box", $row["nome"]);
			echo form_default_hidden('id_eleicao', '', $row['id_eleicao']);
			echo form_default_integer("qt_kit_recebido", "Informe a quantidade de Kits recebidos:");

		echo form_end_box("default_box");
		
		echo form_command_bar_detail_start();
			echo button_save("Abrir apuração da Eleição");
		echo form_command_bar_detail_end();
		echo br(2);
	echo form_close();		
}
else
{
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Apuração', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Lotes', FALSE, 'ir_lote();');
echo aba_start($abas);

	$fl_lancar_votos = true;
	if(intval($row["num_votos"]) == (intval($row["votos_apurados"]) + intval($row["invalidados"])))
	{
		$fl_lancar_votos = false;
	}
	
	#### LANCAR VOTOS ####
	echo form_open('gestao/eleicoes_apuracao/apuracao_salvar');
		echo form_start_box("default_box", $row["nome"]);
			echo form_default_hidden('ds_eleicao', '', $row['nome']);
			echo form_default_hidden('id_eleicao', '', intval($row['id_eleicao']));
			echo form_default_hidden('cd_tipo', '', $row['cd_tipo']);
			echo form_default_text("dt_hr_abertura", "Dt Abertura:", $row, 'style="width:100px; border:0" readonly');
			echo (trim($row["dt_hr_fechamento"]) != "" ? form_default_text("dt_hr_fechamento", "Dt Encerramento:", $row, 'style="width:100px; border:0" readonly') : "");
			echo form_default_text("qt_cadastro", "Cadastro Eleitoral:", number_format($row["qt_cadastro"],0,",",""), 'style="width:100px; text-align:right; border:0; font-weight: bold;" readonly');
			echo form_default_text("qt_total_recebido", "Kits recebidos:", number_format($row["num_votos"],0,",",""), 'style="width:100px; text-align:right; border:0; color: blue; font-weight: bold;" readonly');
			
			echo form_default_row("", "Kits inválidos:", 
			form_input(array("name"=>"qt_total_invalido-total", "id"=>"qt_total_invalido-total", "style"=>"width:100px; text-align:right; border:0; color: red; font-weight: bold;"), number_format($row["invalidados"],0,",",""), "readonly")
			.
			form_input(array("name"=>"qt_total_invalido", "id"=>"qt_total_invalido", "style"=>"width:100px; text-align:right;".(!$fl_lancar_votos ? "display:none;" : "")))
			);			
			
			echo form_default_text("qt_total_apurado", "Votos válidos:", number_format($row["votos_apurados"],0,",",""), 'style="width:100px; text-align:right; border:0; color: green; font-weight: bold;" readonly');
			echo form_default_text("qt_kit_apurado", "Kits apurados:", number_format($row["votos_apurados"] + $row["invalidados"],0,",",""), 'style="width:100px; text-align:right; border:0; color: brown; font-weight: bold;" readonly');
		echo form_end_box("default_box");

		echo form_start_box("default_tipo_lote_box", "LOTE");
			$ar_tp_voto[] = array('value' => 'C', 'text' => 'Correios');
			$ar_tp_voto[] = array('value' => 'E', 'text' => 'Internet/Telefone');
			echo form_default_dropdown('tp_voto', 'Tipo do lote:(*)', $ar_tp_voto, array("C"));
		echo form_end_box("default_tipo_lote_box");	
		
		echo form_start_box("default_deliberativo_box", "CONSELHO DELIBERATIVO");
			foreach($ar_deliberativo as $ar_item)
			{
				$id   = "cd_candidato-".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"];
				$nome = "ar_candidato[".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"]."]";
				
				$ar_atrib = array("name"=>$nome, "id"=>$id, "style"=>"width:100px; text-align:right;".(!$fl_lancar_votos ? "display:none;" : ""), "onkeypress"=>"handleEnter(this, event);", "onkeydown"=>"$(this).numeric();");
				$ar_atrib_total = array("name"=>$id."-total", "id"=>$id."-total");
				
				$scr_campo.= "ar_campo_deliberativo.push('".$id."');\n";
				
				echo form_default_row("", $ar_item["ds_candidato"], form_input($ar_atrib).form_input($ar_atrib_total, number_format($ar_item["qt_total_candidato"],0,",","."), 'style="width: 100px; border:0; text-align: right; font-weight:bold;" readonly'));
			}
		echo form_end_box("default_deliberativo_box");		
		
		
		echo form_start_box("default_fiscal_box", "CONSELHO FISCAL");
			foreach($ar_fiscal as $ar_item)
			{
				$id   = "cd_candidato-".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"];
				$nome = "ar_candidato[".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"]."]";
				
				$ar_atrib = array("name"=>$nome, "id"=>$id, "style"=>"width:100px; text-align:right;".(!$fl_lancar_votos ? "display:none;" : ""), "onkeypress"=>"handleEnter(this, event);", "onkeydown"=>"$(this).numeric();");
				$ar_atrib_total = array("name"=>$id."-total", "id"=>$id."-total");
				
				$scr_campo.= "ar_campo_fiscal.push('".$id."');\n";				
				
				echo form_default_row("", $ar_item["ds_candidato"], form_input($ar_atrib).form_input($ar_atrib_total, number_format($ar_item["qt_total_candidato"],0,",","."), 'style="width: 100px; border:0; text-align: right; font-weight:bold;" readonly'));
			}
		echo form_end_box("default_fiscal_box");	
		

		echo form_start_box("default_diretor_box", "DIRETOR");
			foreach($ar_diretor as $ar_item)
			{
				$id   = "cd_candidato-".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"];
				$nome = "ar_candidato[".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"]."]";
				
				$ar_atrib = array("name"=>$nome, "id"=>$id, "style"=>"width:100px; text-align:right;".(!$fl_lancar_votos ? "display:none;" : ""), "onkeypress"=>"handleEnter(this, event);", "onkeydown"=>"$(this).numeric();");
				$ar_atrib_total = array("name"=>$id."-total", "id"=>$id."-total");
				
				$scr_campo.= "ar_campo_diretor.push('".$id."');\n";					
				
				echo form_default_row("", $ar_item["ds_candidato"], form_input($ar_atrib).form_input($ar_atrib_total, number_format($ar_item["qt_total_candidato"],0,",","."), 'style="width: 100px; border:0; text-align: right; font-weight:bold;" readonly'));
			}
		echo form_end_box("default_diretor_box");		
		
		echo form_start_box("default_diretor_box", "CAP");
			foreach($ar_cap_aessul as $ar_item)
			{
				$id   = "cd_candidato-".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"];
				$nome = "ar_candidato[".$ar_item["cd_empresa"]."-".$ar_item["cd_registro_empregado"]."-".$ar_item["seq_dependencia"]."]";
				
				$ar_atrib = array("name"=>$nome, "id"=>$id, "style"=>"width:100px; text-align:right;".(!$fl_lancar_votos ? "display:none;" : ""), "onkeypress"=>"handleEnter(this, event);", "onkeydown"=>"$(this).numeric();");
				$ar_atrib_total = array("name"=>$id."-total", "id"=>$id."-total");
				
				//$scr_campo.= "ar_campo_diretor.push('".$id."');\n";					
				
				echo form_default_row("", $ar_item["ds_candidato"], form_input($ar_atrib).form_input($ar_atrib_total, number_format($ar_item["qt_total_candidato"],0,",","."), 'style="width: 100px; border:0; text-align: right; font-weight:bold;" readonly'));
			}
		echo form_end_box("default_diretor_box");		
		
		
		echo form_command_bar_detail_start();
			if ($row["situacao"] == "A") 
			{
				if(intval($row["num_votos"]) > (intval($row["votos_apurados"]) + intval($row["invalidados"])))
				{
					echo button_save("Lançar Lote de Votos");
				}
				elseif(intval($row["num_votos"]) == (intval($row["votos_apurados"]) + intval($row["invalidados"])))
				{
					echo button_save("Encerrar Apuração","apuracao_encerrar(".intval($row['id_eleicao']).")","botao_vermelho");
				}
			}
		echo form_command_bar_detail_end();
		echo br(4);
	echo form_close();	
}	
?>
<script>
	$(document).ready(function() {
		<?php
			echo $scr_campo;
		?>
	});	
</script>
<?php
echo aba_end();
$this->load->view('footer_interna');
?>