<?php
set_title('Cadastros, Contratos, Avaliação');
$this->load->view('header');
?>
<script>
function show_avaliador(c)
{
	$("#contrato_box").hide();
	$("#grupo_box").hide();
	$("#formulario_box").hide();
	$("#avaliacao_box").hide();
	$("#salvar_avaliacao_box").hide();
	$("#salvar_grupo_box").hide();
	$("#avaliador_box").show();
	$("#cd_contrato_formulario_grupo").val(c);
}

function hide_avaliador()
{
	$("#contrato_box").show();
	$("#grupo_box").show();
	$("#formulario_box").show();
	$("#avaliacao_box").show();
	$("#salvar_avaliacao_box").show();
	$("#salvar_grupo_box").show();
	$("#avaliador_box").hide();
	$("#cd_contrato_formulario_grupo").val('');
}

<?php if(isset($row["cd_contrato_avaliacao"])):?>
function abrir_resultado()
{
	location.href='<?php echo site_url("cadastro/contrato_avaliacao_resultado/index/") . "/" . $row["cd_contrato_avaliacao"]; ?>';
}
<?php endif; ?>

function abrir_lista()
{
	location.href='<?php echo site_url("cadastro/contrato_avaliacao"); ?>';
}
<?php 
echo form_default_js_submit(
    array( 		'contrato', 'formulario', array( 'dt_inicio_avaliacao', 'data' ), array('dt_fim_avaliacao', 'data'), array('dt_limite_avaliacao', 'data')	)
	, 'salvar_avaliacao(form)'
);
?>

function salvar_avaliacao(f)
{
	dis = document.getElementById('formulario').disabled;
	if(confirm('Salvar?'))
	{
		if(dis) document.getElementById('formulario').disabled=false;
		f.submit();
	}
}

function salvar_grupos( f )
{
	if(confirm('Salvar grupos?'))
	{
		f.action="<?php echo site_url('cadastro/contrato_avaliacao/salvar_grupo'); ?>";
		f.submit();
	}
}

function enviar_email(f)
{
	if(confirm('Enviar emails?'))
	{
		f.action="<?php echo site_url('cadastro/contrato_avaliacao/enviar_email/'); ?>";
		f.submit();
	}
}
<?php if(isset($row["cd_contrato_avaliacao"])):?>
function salvar_grupo()
{
	if( $('#cd_usuario_avaliador_gerencia').val()=='' )
	{
		alert("Informe a gerência!");
		return false;
	}
	if( $('#cd_usuario_avaliador').val()=='' )
	{
		alert("Informe o avaliador!");
		return false;
	}
	if( $('#cd_contrato_formulario_grupo').val()=='' )
	{
		alert("Grupo não identificado!");
		return false;
	}

	if( !confirm('Salvar avaliador?') ){ return false; }

	document.getElementById("loader_div").innerHTML = "<?php echo loader_html(); ?>";
	url = '<?php echo site_url("cadastro/contrato_avaliacao/salvar_grupo/"); ?>';
	$.post( url, 
		{
		cd_contrato_avaliacao: '<?php echo $row["cd_contrato_avaliacao"];?>'
		,cd_usuario_avaliador_gerencia: $('#cd_usuario_avaliador_gerencia').val()
		,cd_usuario_avaliador: $('#cd_usuario_avaliador').val()
		,cd_contrato_formulario_grupo: $('#cd_contrato_formulario_grupo').val()
		}
		, function(data){ 
			/*$('#loader_div').html(data);*/
			$("#cd_usuario_avaliador_gerencia").val('');
			location.reload(); 
		} 
	);
}
<?php endif;?>
function excluir_grupo(c)
{
	if( !confirm('Excluir avaliador?') ){ return false; }

	url = '<?php echo site_url("cadastro/contrato_avaliacao/excluir_grupo/"); ?>';
	$.post( url, 
	{
		cd_contrato_avaliacao_item: c
	}
	, function(data){ 
		location.reload(); 
	} 
	);
}

</script>

<?php
$abas[] = array( 'aba_lista', 'Lista', FALSE, 'abrir_lista()' );
$abas[] = array( 'aba_avaliacao', 'Avaliação', TRUE, '' );
if(isset($row["cd_contrato_avaliacao"]))
{
	$abas[] = array( 'aba_resultado', 'Resultado', FALSE, 'abrir_resultado()' );
}

echo aba_start( $abas );

	// conteudo da pagina
	echo form_open('cadastro/contrato_avaliacao/salvar');
	if(isset($row["cd_contrato_avaliacao"])) $cd_contrato_avaliacao=md5($row["cd_contrato_avaliacao"]);else $cd_contrato_avaliacao="";
	echo form_hidden( "cd_contrato_avaliacao", $cd_contrato_avaliacao );

	//
	// CONTRATO
	//
	echo form_start_box( "contrato_box", "Contrato" );
	$cd_contrato=(isset($row["cd_contrato"]))?$row["cd_contrato"]:"";
	echo form_default_dropdown_db("contrato", "Contrato *", array("projetos.contrato","cd_contrato","ds_empresa || ' - ' || ds_servico"), array($cd_contrato));
	echo form_end_box("contrato_box");

	//
	// FORMULÁRIO
	//
	echo form_start_box( "formulario_box", "Formulário" );
	$cd_contrato_formulario=(isset($row["cd_contrato_formulario"]))?$row["cd_contrato_formulario"]:"";
	$style='';
	if(!$permite_mudar_formulario) $style=" disabled ";

	echo form_default_dropdown_db(
		"formulario"
		, "Formulário *"
		, array("projetos.contrato_formulario","cd_contrato_formulario","ds_contrato_formulario")
		, array($cd_contrato_formulario)
		, $style
		);
	echo form_end_box("formulario_box");

	echo form_start_box( "avaliacao_box", "Avaliação" );
	echo form_default_date("dt_inicio_avaliacao", "Data de início da avaliação *", $row);
	echo form_default_date("dt_fim_avaliacao", "Data final da avaliação *", $row);
	echo form_default_date("dt_limite_avaliacao", "Data limite da avaliação *", $row);
	echo form_end_box("avaliacao_box");

	echo form_command_bar_detail_start("salvar_avaliacao_box");
	if(!$email_ja_enviado)
	{
		echo button_save("Salvar as configurações da avaliação");
	}
	if(isset($row["cd_contrato_avaliacao"]))
	{
		echo button_delete( "cadastro/contrato_avaliacao/excluir", $row["cd_contrato_avaliacao"] );
	}
	echo form_command_bar_detail_end();

	//
	// INCLUSÃO DE GERENCIAS, USUÁRIOS
	//
	echo form_start_box( "avaliador_box", "AVALIADOR" );
	echo form_default_row('','', form_input(  array( "id"=>"cd_contrato_formulario_grupo"), "", " style='display:none;' " )  );
	echo form_default_usuario_ajax("cd_usuario_avaliador");
	echo form_default_row("", "", " <input type='button' class='botao' value='adicionar' onclick='salvar_grupo();' /> <input type='button' class='botao' value='fechar' onclick='hide_avaliador();' /><div id='loader_div'></div> ");
	echo form_end_box("avaliador_box");

	echo form_start_box( "grupo_box", "Grupos", FALSE );
	if( ! isset($row["cd_contrato_avaliacao"]) )
	{
		echo "<span style='font-size:12;color:green;'>Antes de configurar os grupos você deve salvar as configurações da avaliação.</span>";
	}
	else
	{
		$body=array();
		$head = array( 
			'Código', 'Avaliador'
		);

		foreach( $grupos as $item )
		{
			$botao_excluir = "";
			$botao_adicionar = "";
			$exibir_avaliador = '';
			foreach( $item['avaliadores'] as $avaliador )
			{
				if(!$email_ja_enviado)
				{
					$botao_excluir = "<a href='javascript:void(0)' onclick='excluir_grupo( ".$avaliador['cd_contrato_avaliacao_item']." );'>excluir</a> - ";
				}
				$exibir_avaliador .= $botao_excluir . $avaliador['cd_divisao'] . ' / ' . $avaliador['nome_usuario_avaliador'] . '<br />';
			}
			if(trim($exibir_avaliador)!='') $exibir_avaliador.='<br>';

			if(!$email_ja_enviado)
			{
				$botao_adicionar="<input type='button' class='botao' onclick='show_avaliador(".$item["cd_contrato_formulario_grupo"].");' value='adicionar'>";
			}
			$body[] = array(
			array(form_hidden("cd_contrato_formulario_grupo_". $item["cd_contrato_formulario_grupo"], $item["cd_contrato_formulario_grupo"]).$item["ds_contrato_formulario_grupo"], 'text-align:left;')
				, array( "<div id='grupos_lista_div'>$exibir_avaliador</div>"
				.$botao_adicionar, 'text-align:left;')
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		$grid->view_count=FALSE;
		echo $grid->render();
	}
	echo form_end_box("grupo_box", FALSE);

	echo form_command_bar_detail_start("salvar_grupo_box");

	if( $permite_enviar_email )
	{
		echo button_save("Enviar email", "enviar_email(this.form);");
	}
	else
	{
		echo $mensagem_email;
	}
	echo form_command_bar_detail_end();

	echo "<br><br><br>";

echo aba_end(''); 
?>

<script type="text/javascript">
$('#avaliador_box').hide();
</script>

<?php
$this->load->view('footer');
