<?php $this->load->view('header', array('topo_titulo'=>'Cadastro, Contrato, formulário')); ?>
<script>
	<?php echo form_default_js_submit(array( array("ds_contrato_formulario", "str")	));	?>
	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/contrato_formulario"); ?>';
	}
	function ocultar_tudo()
	{
		$('#grupos_box').hide();
		$('#grupo_box').hide();
		$('#pergunta_box').hide();
		$('#default_box').hide();
		$('#command_bar').hide();
	}
	function abrir_padrao()
	{
		ocultar_tudo();
		$('#grupos_box').show();
		$('#default_box').show();
		$('#command_bar').show();
	}
	function abrir_grupo()
	{
		ocultar_tudo();
		$('#grupo_box').show();
	}
	function fechar_grupo()
	{
		abrir_padrao();
	}
	function fechar_pergunta()
	{
		abrir_padrao();
	}
	function editar_grupo(pk,ds,nr)
	{
		$('#pk_grupo').val(pk);
		$('#ds_contrato_formulario_grupo').val(ds);
		$('#nr_ordem').val(nr);
		abrir_grupo();
	}
	function editar_pergunta(pk,ds,nr,cd_g)
	{
		$('#pk_pergunta').val(pk);
		$('#ds_contrato_formulario_pergunta').val(ds);
		$('#nr_ordem_pergunta').val(nr);
		$('#cd_contrato_formulario_grupo').val(cd_g);
		abrir_pergunta();
	}
	function salvar_grupo()
	{
		if( $('#ds_contrato_formulario_grupo').val()=='' )
		{
			alert('Informe a descrição do grupo.');
			document.getElementById('ds_contrato_formulario_grupo').focus();
		}
		else if( $('#nr_ordem').val()=='' )
		{
			alert('Informe a ordem do grupo.');
			document.getElementById('nr_ordem').focus();
		}
		else
		{
			if(confirm('Salvar?'))
			{
				url = '<?php echo site_url("cadastro/contrato_formulario/salvar_grupo"); ?>';
				$.post( url, { cd_contrato_formulario_grupo:$('#pk_grupo').val(), cd_contrato_formulario:$('#cd_contrato_formulario').val(), ds_contrato_formulario_grupo:$('#ds_contrato_formulario_grupo').val(), nr_ordem:$('#nr_ordem').val() }, function(data){ if(data!=''){ $('#output').html(data); } else { limpar_grupo(); location.reload(); } } );
			}
		}
	}
	function limpar_grupo()
	{
		$('#pk_grupo').val('');
		$('#ds_contrato_formulario_grupo').val('');
		$('#nr_ordem').val('');
	}
	function excluir_grupo(grupo)
	{
		if(confirm('Excluir?'))
		{
			url = '<?php echo site_url("cadastro/contrato_formulario/excluir_grupo/")."/"; ?>'+grupo;
			$.post( url, {}, function(data){ if(data!=''){ $('#output').html(data); } else { location.reload(); } } );
		}
	}
	function excluir_pergunta(pergunta)
	{
		if(confirm('Excluir?'))
		{
			url = '<?php echo site_url("cadastro/contrato_formulario/excluir_pergunta/")."/"; ?>'+pergunta;
			$.post( url, {}, function(data){ if(data!=''){ $('#output').html(data); } else { location.reload(); } } );
		}
	}
	function abrir_pergunta(grupo)
	{
		ocultar_tudo();
		$('#pergunta_box').show();
		$('#cd_contrato_formulario_grupo').val(grupo);
	}
	function salvar_pergunta()
	{
		if( $('#ds_contrato_formulario_pergunta').val()=='' )
		{
			alert('Informe a descrição da pergunta.');
			document.getElementById('ds_contrato_formulario_grupo').focus();
		}
		else if( $('#nr_ordem_pergunta').val()=='' )
		{
			alert('Informe a ordem da pergunta.');
			document.getElementById('nr_ordem_pergunta').focus();
		}
		else
		{
			if(confirm('Salvar?'))
			{
				url = '<?php echo site_url("cadastro/contrato_formulario/salvar_pergunta"); ?>';
				$.post( url, { cd_contrato_formulario_pergunta:$('#pk_pergunta').val(), cd_contrato_formulario_grupo:$('#cd_contrato_formulario_grupo').val(), ds_contrato_formulario_pergunta:$('#ds_contrato_formulario_pergunta').val(), nr_ordem:$('#nr_ordem_pergunta').val() }, function(data){ if(data!=''){ $('#output').html(data); } else { limpar_pergunta(); location.reload(); } } );
			}
		}
	}
	function limpar_pergunta()
	{
		$('#pk_pergunta').val('');
		$('#cd_contrato_formulario_grupo').val('');
		$('#ds_contrato_formulario_pergunta').val('');
		$('#nr_ordem_pergunta').val('');
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start( $abas );
echo form_open('cadastro/contrato_formulario/salvar');
echo form_hidden( 'cd_contrato_formulario', intval($row['cd_contrato_formulario']) );

// Registros da tabela principal ...
echo form_start_box( "default_box", "Formulário" );
echo form_default_text("ds_contrato_formulario", "Descrição *", $row, "style='width:300px;'", "200");
echo form_end_box("default_box");

// Barra de comandos ...
echo form_command_bar_detail_start();
	echo button_save();
	if( intval($row['cd_contrato_formulario'])>0 )
	{
		echo button_delete("cadastro/contrato_formulario/excluir",$row["cd_contrato_formulario"]);
	}
	echo form_command_bar_detail_button("Voltar para lista", "if( confirm('Voltar?') ){ location.href='".site_url('cadastro/contrato_formulario')."'; }");
echo form_command_bar_detail_end();

// CAIXA - GRUPOS
echo form_start_box( "grupos_box", "Grupos e Perguntas", FALSE );
if( intval($row['cd_contrato_formulario'])>0 ):
?>
<style>.bot-bor{border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:black;padding:10px;}</style>
<table style="font-size:12;" align="center">

	<tr style='background:;'>
		<td><b>Ordem</b></td>
		<td valign="top"><b>Grupos</b></td>
		<td valign="top"><b>Perguntas</b></td>
	</tr>

	<?php foreach($row['grupos'] as $grupo): ?>
		<tr style='background:;'>
			<td valign='top' style='width:10px;' class='bot-bor'><span style='margin-right:10;'><?php echo $grupo['nr_ordem'] ?></span></td>
			<td valign="top" class='bot-bor'>
				<?php echo $grupo['ds_contrato_formulario_grupo']; ?>
				<br />
				<?php 
				$pk_grupo = $grupo["cd_contrato_formulario_grupo"]; 
				$ds_grupo = $grupo["ds_contrato_formulario_grupo"]; 
				$ordem_grupo = $grupo["nr_ordem"]; 
				?>
				(<a href='javascript:void(0);' onclick='editar_grupo( <?php echo $pk_grupo;?>, "<?php echo $ds_grupo;?>", <?php echo $ordem_grupo;?> );'>editar</a>|<a href='javascript:void(0);' onclick='excluir_grupo("<?php echo md5($grupo['cd_contrato_formulario_grupo']); ?>")'>excluir</a>)
			</td>
			<td valign="top" class='bot-bor'>
				<table style="font-size:12;">
				<?php foreach($grupo['perguntas'] as $pergunta): ?>
					<tr>
					<td valign='top'><span style='margin-right:10;'><?php echo $pergunta['nr_ordem'] ?></span></td>
					<td valign='top'>
						<div style='padding-bottom:10px;'>- <?php echo $pergunta['ds_contrato_formulario_pergunta']; ?><br>
						<?php 
						$pk_pergunta = $pergunta["cd_contrato_formulario_pergunta"]; 
						$cd_grupo = $pergunta["cd_contrato_formulario_grupo"]; 
						$ds_pergunta = $pergunta["ds_contrato_formulario_pergunta"]; 
						$ordem_pergunta = $pergunta["nr_ordem"]; 
						?>
						(<a href='javascript:void(0);' onclick='editar_pergunta( <?php echo $pk_pergunta; ?>, "<?php echo $ds_pergunta; ?>", <?php echo $ordem_pergunta?>, <?php echo $cd_grupo; ?> );'>editar</a>|<a href='javascript:void(0);' onclick='excluir_pergunta("<?php echo md5($pergunta['cd_contrato_formulario_pergunta']); ?>")'>excluir</a>)</div>
					</td>
					</tr>
				<?php endforeach; ?>
				</table>
				&nbsp
			</td>
			<td class='bot-bor'><input type='button' value='nova pergunta' class='botao' style='margin-bottom:10px;' onclick='limpar_pergunta();abrir_pergunta(<?php echo $grupo['cd_contrato_formulario_grupo']; ?>);' /></td>
		</tr>
	<?php endforeach; ?>

	<tr><td><input type='button' value='novo grupo' onclick='limpar_grupo();abrir_grupo();' class='botao' style='margin:10;' /></td></tr>

</table>
<?php
else:
	echo "<center><span>Antes de incluir os grupos e perguntas, salve o formulário!</span></center>";
endif;
echo form_end_box("grupos_box", FALSE);
// CAIXA - GRUPOS

// CADASTRO GRUPOS
echo form_start_box( "grupo_box", "Grupo" );
echo form_default_hidden("pk_grupo", "Código:", "");
echo form_default_text("ds_contrato_formulario_grupo", "Descrição:", "", "style='width:300px;'", "200");
echo form_default_integer("nr_ordem", "Ordem:", "", "", "3");
echo form_default_row("", "", "<input type='button' value='Salvar Grupo' onclick='salvar_grupo();' class='botao' style='margin-right:5;'><input type='button' value='Fechar' onclick='fechar_grupo();' class='botao'>");
echo form_end_box("grupo_box");
// CADASTRO GRUPOS

// CADASTRO PERGUNTAS
echo form_start_box( "pergunta_box", "Pergunta" );
echo form_default_hidden("pk_pergunta", "Código:"); 
echo form_default_hidden("cd_contrato_formulario_grupo", "Código do Grupo:"); 
echo form_default_textarea("ds_contrato_formulario_pergunta", "Descrição:", "", "style='width:500px;height:100px;'", "200"); 
echo form_default_integer("nr_ordem_pergunta", "Ordem:", "", "", "3"); 
echo form_default_row("", "", "<input type='button' value='Salvar Pergunta' onclick='salvar_pergunta();' class='botao' style='margin-right:5;'><input type='button' value='Fechar' onclick='fechar_pergunta();' class='botao'>"); 
echo form_end_box("pergunta_box");
// CADASTRO PERGUNTAS

/*echo form_start_box( "output_box", "Output" );
echo form_default_row("", "", "<div id='output' style='display:none;'></div>"); 
echo form_end_box("output_box");*/
?>
<div id='output' style='display:none;'></div>
<script>
	$('#grupo_box').hide();
	$('#pergunta_box').hide();
</script>
<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
