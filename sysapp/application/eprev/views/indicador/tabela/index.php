<?php
set_title('Indicador - Desenho da tabela de par�metros'); 
$this->load->view('header.php'); 
?>
<script src="<?php echo base_url();?>js/jquery-plugins/iutil.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>js/jquery-plugins/idrag.js" type="text/javascript"></script>
<script type='text/javascript'>
<!--
/*function mudou_celula_event(ob)
{
	$('#celula_style').val($('#style_'+ob.id).val());
	$('#celula_id').val(ob.id);
}*/

/*function celula_style_event(ob)
{
	$('#style_'+$('#celula_id').val()).val($('#celula_style').val());

	$('#'+$('#celula_id').val()).attr( 'style', $('#style_'+$('#celula_id').val()).val() );

	$('#'+$('#celula_id').val()).focus();	
}*/

function aplicar_estilo( linha, coluna, ob )
{
	$( '#tabela_cel_'+linha+'-'+coluna+'' ).attr( 'style', 'display:none;'+$(ob).val() );
	$( ob ).attr( 'style', 'display:;'+$(ob).val() );
}

function exibir()
{
	url="<?php echo site_url('indicador/tabela/exibir_ajax'); ?>";
	$.post( url, { linhas:$('#linhas').val(),colunas:$('#colunas').val(),cd_indicador_tabela:$('#cd_indicador_tabela').val() }, 
	function(data){ 
		$('#result_div').html( data );

		exibir_resultado_formula();

		if($('#linhas').val()=='' || $('#colunas').val()=='' || $('#linhas').val()=='0' || $('#colunas').val()=='0')
		{
			url="<?php echo site_url('indicador/tabela/linhas_e_colunas_ajax'); ?>";
			$.post(url,{cd_indicador_tabela:$('#cd_indicador_tabela').val()},function(data){
				$('#linhas').val(data.linhas); $('#colunas').val(data.colunas);
			},'json');
		}
		else
		{
			// $('#linhas').val(13); $('#colunas').val(3);
		} 
	} );
}

function salvar(f)
{
	f.target='output_iframe';
	f.submit();
}

function cd_indicador_tabela_event(ob)
{
	if( $('#cd_indicador_tabela').val()!='' )
	{
		url = "<?php echo site_url('indicador/tabela/carregar_indicador_ajax'); ?>";
		$.post( url, {cd:$('#cd_indicador_tabela').val()}, function(data)
		{ 
			if(data.erro=='')
			{
				if( data.ds_indicador_tabela!='' )
				{
					$('#ds_indicador_tabela').val( data.ds_indicador_tabela );
				}
				else
				{
					$('#ds_indicador_tabela').val( '[Informe o T�tulo]' );
				}
				$('#linhas').val('');
				$('#colunas').val('');
				$('#configuracao_box').show();
				$('#exibir_box').show();
				//$('#output_box').show();

				exibir();
			} 
			else
			{
				alert( data.erro );
			}
		}, 'json' );
	}
	else
	{
		$('#configuracao_box').hide();
		$('#exibir_box').hide();
		//$('#output_box').hide();
	}
}

function exibir_estilo()
{ 
	$('#estilo_box').show();
	$('.estilo').show();
	$('.celula').hide();
	$('.formula').hide();
}

function exibir_valor()
{ 
	$('.estilo').hide();
	$('#estilo_box').hide();
	$('#estilo_wizard_box').hide();
	$('.celula').show();
	$('.formula').hide();
}

function exibir_resultado_formula()
{
	$('.estilo').hide();
	$('#estilo_box').hide();
	$('#estilo_wizard_box').hide();
	$('.com_formula').hide();
	$('.formula').show();
}

function executar_formula(c,l)
{
	url="<?php echo site_url('indicador/tabela/executar_formula_ajax')?>";
	$.post( url, {cd_indicador_tabela:$('#cd_indicador_tabela').val(),coluna:c,linha:l},function(data){alert(data);} );
}

function aplicar_estilo_padrao()
{
	if(confirm('Aplicar o estilo padr�o a tabela?'))
	{
		ls = $('#linhas').val();
		cs = $('#colunas').val();

		for( i=0;i<ls;i++ )
		{
			for(j=0;j<cs;j++)
			{
				if( i==0 )
				{
					st="background:url(http://www.e-prev.com.br/cieprev/skins/skin002/img/form/form-box-title-background.png); font-weight: bold;font-size:10;";
				}
				else 
				{
					if( j==0 && i!=0 )
					{
						st="background:url(http://www.e-prev.com.br/cieprev/skins/skin002/img/form/form-box-title-background.png); font-weight: bold;";
					}
					else
					{
						st="text-align:right;";
					}
				}

				$( '#style_tabela_cel_'+i+'-'+j+'' ).val( st );

				$( '#style_tabela_cel_'+i+'-'+j+'' ).attr( 'style', 'display:none;' + st );
				$( '#formula_tabela_cel_'+i+'-'+j+'' ).attr( 'style', 'display:none;' + st );
				$( '#tabela_cel_'+i+'-'+j+'' ).attr( 'style', 'display:none;' + st );
			}
		}

		exibir_valor();
	}
}

function letra_indice(l)
{
	return ( ( l=='A' )?0 : ( l=='B' )?1: ( l=='C' )?2: ( l=='D' )?3: ( l=='E' )?4: ( l=='F' )?5: ( l=='G' )?6: ( l=='H' )?8: ( l=='I' )?9: ( l=='J' )?10: ( l=='K' )?11: ( l=='L' )?12: ( l=='M' )?13: ( l=='N' )?14: ( l=='O' )?15: ( l=='P' )?16: ( l=='Q' )?17: ( l=='R' )?18: ( l=='S' )?19: ( l=='T' )?20: ( l=='U' )?21: ( l=='V' )?22: ( l=='W' )?23: ( l=='X' )?24: ( l=='Y' )?25: ( l=='Z' )?26: 0 )
}
function resetar_inputs_de_estilo()
{
	ci=0; cf=$('#colunas').val()-1; li=0; lf=$('#linhas').val()-1;
	for( j=ci;j<=cf;j++ )
	{
		for( i=li;i<=lf;i++ )
		{
			$( "#style_tabela_cel_"+i+"-"+j+"" ).css( 'border-style', '' );
			$( "#style_tabela_cel_"+i+"-"+j+"" ).css( 'border-color', '' );
		}
	}

}
function ver_alteracao_estilo()
{
	resetar_inputs_de_estilo();
	
	ci=cf=li=lf=1;
	
	ci=(  ($('#coluna_inicio_estilo').val()!='')?letra_indice($('#coluna_inicio_estilo').val()):0  );
	cf=(  ($('#coluna_fim_estilo').val()!='')?letra_indice($('#coluna_fim_estilo').val()): ci  );
	li=(  ($('#linha_inicio_estilo').val()!='')?$('#linha_inicio_estilo').val():0  );
	lf=(  ($('#linha_fim_estilo').val()!='')?$('#linha_fim_estilo').val():li  );
	
	for( j=ci;j<=cf;j++ )
	{
		for( i=li;i<=lf;i++ )
		{
			$( "#style_tabela_cel_"+i+"-"+j+"" ).css( 'border-style', 'dashed' );
			$( "#style_tabela_cel_"+i+"-"+j+"" ).css( 'border-color', 'red' );
		}
	}
}
function aplicar_alteracao_estilo()
{
	if( $('#coluna_inicio_estilo').val()=='' ){alert( 'A coluna inicial � obrigat�ria' );$('#coluna_inicio_estilo').focus();return false;}
	if( $('#linha_inicio_estilo').val()=='' ){alert( 'A linha inicial � obrigat�ria' );$('#linha_inicio_estilo').focus();return false;}

	// ci=cf=li=lf=0;

	ci=(  ($('#coluna_inicio_estilo').val()!='')?letra_indice($('#coluna_inicio_estilo').val()):0  );
	cf=(  ($('#coluna_fim_estilo').val()!='')?letra_indice($('#coluna_fim_estilo').val()):ci  );
	li=(  ($('#linha_inicio_estilo').val()!='')?$('#linha_inicio_estilo').val():0  );
	lf=(  ($('#linha_fim_estilo').val()!='')?$('#linha_fim_estilo').val():li  );

	for( j=ci;j<=cf;j++ )
	{
		for( i=li;i<=lf;i++ )
		{
			$( "#style_tabela_cel_"+i+"-"+j+"" ).val( $('#definir_estilo').val() );
			aplicar_estilo( i, j, document.getElementById('style_tabela_cel_'+i+'-'+j) );
		}
	}

	resetar_inputs_de_estilo();
}

function editar_estilo(s)
{
	$('#estilo_wizard_box').show();
	url="<?php echo site_url('indicador/tabela/criar_box_propriedade_estilo_ajax'); ?>";
	$.post( url, {estilo:s}, function(data){$('#estilo_wizard_box_content').html(data);} );
}

function adicionar_coluna(i,c,final)
{
	if(confirm('Adicionar coluna?'))
	{
		url='<?php echo site_url('indicador/tabela/test_inserir_coluna/'); ?>/'+i+'/'+c+'/'+final;
		$.post(url,{},function(data){ $('#colunas').val( parseInt($('#colunas').val())+1); exibir(); });
	}
}
function excluir_coluna(i,c)
{
	if(confirm('Excluir coluna?'))
	{
		url='<?php echo site_url('indicador/tabela/test_excluir_coluna/'); ?>/'+i+'/'+c;
		$.post(url,{},function(data){ $('#colunas').val( parseInt($('#colunas').val())-1); exibir(); });
	}
}
function adicionar_linha(i,l,final)
{
	if(confirm('Adicionar linha?'))
	{
		url='<?php echo site_url('indicador/tabela/test_inserir_linha/'); ?>/'+i+'/'+l+'/'+final;
		$.post(url,{},function(data){ $('#linhas').val( parseInt($('#linhas').val())+1); exibir(); });
	}
}
function excluir_linha(i,l)
{
	if(confirm('Excluir linha?'))
	{
		url='<?php echo site_url('indicador/tabela/test_excluir_linha/'); ?>/'+i+'/'+l;
		$.post(url,{},function(data){ $('#linhas').val( parseInt($('#linhas').val())-1); exibir(); });
	}
}

$(document).ready( rodar_ao_iniciar );
function rodar_ao_iniciar()
{
	$('#configuracao_box').hide();
	$('#exibir_box').hide();
	//$('#output_box').hide();
	$('#estilo_box').hide();
	$('#estilo_wizard_box').hide();
	
	$('#definir_estilo').after( "<?php echo comando('editar_estilo_btn', 'Editar', "editar_estilo(this.form.definir_estilo.value);"); ?>" );
	
	$('#estilo_wizard_box').Draggable(
		{
			handle:	'#estilo_wizard_box_title'
		}
	);

	$('#indicador_box').hide();
	
	//$('#output_box').hide();
	
	<?php if($cd_indicador_tabela_sel!=0){ echo "cd_indicador_tabela_event()"; } ?>
}
-->
</script>
<style>
.tabela_cel{ border-style:solid;border-width:1px;background-color:white;width:150px;height:20; }
.alteracao_estilo{ border-style:dashed; }
</style>
<?php 
echo form_open( 'indicador/tabela/salvar' );

/*echo
	form_start_box('indicador_box', 'Indicador')
	.form_default_dropdown_db('cd_indicador_tabela', 'Indicador'
		, array('
			indicador.indicador i
			join indicador.indicador_tabela it on i.cd_indicador=it.cd_indicador
			join indicador.indicador_periodo ip on it.cd_indicador_periodo=ip.cd_indicador_periodo
			join indicador.indicador_grupo ig on ig.cd_indicador_grupo=i.cd_indicador_grupo
			'
			, "it.cd_indicador_tabela"
			, " i.cd_indicador || ' - ' || it.cd_indicador_tabela || ' - ' || ig.ds_indicador_grupo || ' - ' || i.ds_indicador || ' - ' || ip.ds_periodo "
			)
		, array( $cd_indicador_tabela_sel )
		, " onchange='cd_indicador_tabela_event(this);' "
		, ""
		, false
		, ' ig.dt_exclusao is null 
			AND ip.dt_exclusao is null 
			AND i.dt_exclusao IS NULL 
			AND it.dt_fechamento_periodo IS NULL 
			AND current_timestamp BETWEEN ip.dt_inicio AND ip.dt_fim 
			AND i.cd_usuario_responsavel='.intval(usuario_id()).' '
		, ' i.nr_ordem '
	)
	.form_end_box('indicador_box');*/

echo
	form_start_box('indicador_view_box', 'Indicador')
	.form_default_hidden('cd_indicador_tabela', 'Indicador', $cd_indicador_tabela_sel)
	.form_default_row('', '', $ds_nome_indicador)
	.form_end_box('indicador_box');

echo 
	form_start_box('configuracao_box', 'Configura��o')
		.form_default_text('colunas', 'Colunas','',"style='width:50px;'")
		.form_default_text('linhas', 'Linhas','',"style='width:50px;'")
		.form_default_row('','','')
		.form_default_row('','',
			comando('exibir_button', 'Exibir', 'exibir();')
			.nbsp().comando('salvar_button', 'Salvar Tabela', 'salvar(this.form);')
		)
	.form_end_box('configuracao_box')
	;

echo 
	form_start_box('estilo_wizard_box', 'Estilo da c�lula', false, true, " style='width:600px;position:absolute;' ")
	.form_end_box('estilo_wizard_box', false)
	;

echo
	form_start_box('estilo_box', 'Estilo da c�lula')
	.form_default_text( 'coluna_inicio_estilo', 'Come�ar na Coluna', '', "onblur='ver_alteracao_estilo()'" )
	.form_default_text( 'coluna_fim_estilo', 'Terminar na Coluna', '', "onblur='ver_alteracao_estilo()'" )
	.form_default_integer( 'linha_inicio_estilo', 'Come�ar na Linha', '', "onblur='ver_alteracao_estilo()'" )
	.form_default_integer( 'linha_fim_estilo', 'Terminar na Linha', '', "onblur='ver_alteracao_estilo()'" )
	.form_default_text( 'definir_estilo', 'Defini��o do estilo', '', "style='width:300px;'" )
	.form_default_row('','',comando('aplicar_estilo_btn','Aplicar estilo!', 'aplicar_alteracao_estilo();'))
	.form_end_box('estilo_box')
	;

echo 
	form_start_box('exibir_box', 'Exibir', false)
		."
			<a href='javascript:void(0)' onclick='exibir_valor();'>exibir estrutura</a> 
			| <a href='javascript:void(0)' onclick='exibir_estilo();'>exibir estilos</a> 
			| <a href='javascript:void(0)' onclick='exibir_resultado_formula();'>exibir resultado das f�rmulas</a>
			| <a href='javascript:void(0)' onclick='aplicar_estilo_padrao();'>aplicar estilo padr�o</a>
			"
		.br(2)
		."<table border='0'>

				<tr><td style='text-align:center;'><input id='ds_indicador_tabela' name='ds_indicador_tabela' style='width:100%;text-align:center;border-style:solid;border-width:1px;background-color:white;' type='text' value='[Informe o T�tulo]' /></td></tr>

				<tr><td><div id='result_div'></div></td></tr>

			</table>"
	.form_end_box('exibir_box', false)
	;

echo 
	form_start_box('output_box', 'Output', false)
	."<iframe name='output_iframe' style='width:100%;height:300px;' frameborder='0'></iframe>"
	.form_end_box('output_box', false)
	;

echo form_close();
$this->load->view('footer.php');