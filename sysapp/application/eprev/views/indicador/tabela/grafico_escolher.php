<?php
$this->load->view('header.php');
?>
<style>
.grafico-linha-normal{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");background-position:-6px 0px;height:121px;width:166px;border-style:none;border-width:1; }
.grafico-linha-destaque{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");	background-position:-181px 0px;	height:121px;	width:166px;	border-style:none;	border-width:1; }
.grafico-linha-selecionado{	background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");	background-position:-355px 0px;	height:121px;	width:166px;	border-style:none;	border-width:1; }

.grafico-barra-normal{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");	background-position:-6px -121px;height:121px;width:166px;border-style:none;	border-width:1; }
.grafico-barra-destaque{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");	background-position:-181px -121px;height:121px;width:166px;	border-style:none;border-width:1; }
.grafico-barra-selecionado{	background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png");	background-position:-355px -121px;height:121px;width:166px;	border-style:none;border-width:1; }

.grafico-pizza-normal{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); background-position:-6px 242px;height:121px;width:166px;border-style:none;border-width:1; }
.grafico-pizza-destaque{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); background-position:-181px 242px; 	height:121px;	width:166px; 	border-style:none;	border-width:1; }
.grafico-pizza-selecionado{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); 	background-position:-355px 242px; 	height:121px;	width:166px; 	border-style:none;	border-width:1; }

.grafico-barmu-normal{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); background-position:-6px 121px;height:121px;width:166px;border-style:none;border-width:1; }
.grafico-barmu-destaque{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); background-position:-181px 121px; 	height:121px;	width:166px; 	border-style:none;	border-width:1; }
.grafico-barmu-selecionado{ background-image:url("<?php echo base_url(); ?>img/indicador_icone_graficos.png"); 	background-position:-355px 121px; 	height:121px;	width:166px; 	border-style:none;	border-width:1; }

</style>
<script>
	var selecionado='';
	var GRAFICO_LINHA=1;
	var GRAFICO_BARRA=2;
	var GRAFICO_BARMU=3;
	var GRAFICO_PIZZA=4;
	function trocar_icone(ob, tipo, opcao)
	{
		if(opcao=='selecionado')
		{ 
			limpar_grafico();
		}

		if( selecionado!=tipo )
		{
			$(ob).attr('class', 'grafico-'+tipo+'-'+opcao);
		}
	}
	
	function limpar_grafico()
	{
		$('#grafico-linha').attr('class', 'grafico-linha-normal');
		$('#grafico-barra').attr('class', 'grafico-barra-normal');
		$('#grafico-barmu').attr('class', 'grafico-barmu-normal');
		$('#grafico-pizza').attr('class', 'grafico-pizza-normal');
	}

	function grafico_click(ob)
	{
		tipo = ( ( $(ob).attr('id')=='grafico-linha' ) ? 'linha' :
				 ( $(ob).attr('id')=='grafico-barra' ) ? 'barra' : 
				 ( $(ob).attr('id')=='grafico-barmu' ) ? 'barmu' : 
				 ( $(ob).attr('id')=='grafico-pizza' ) ? 'pizza' : 
				 '' );

		if( selecionado!=tipo ){ trocar_icone( ob, tipo, 'selecionado' ); }

		selecionado=tipo;

		if( tipo=='linha' || tipo=='barra' || tipo=='barmu' )
		{
			//$('#ds_range_legenda').removeAttr( 'disabled', '' );
			//$('#ds_range_tick').removeAttr( 'disabled', '' );
		}
		if( tipo=='pizza' )
		{
			//$('#ds_range_legenda').removeAttr( 'disabled' );
			//$('#ds_range_tick').attr( 'disabled', true );
		}
	}

	function cd_indicador_event(ob)
	{
		$('#ds_range_tick').val('');
		$('#ds_range_legenda').val('');
		$('#ds_range_valor').val('');

		selecionado='';
		limpar_grafico();

		if( $('#cd_indicador_tabela').val()!='' )
		{
			url = "<?php echo site_url('indicador/grafico_config/carregar_grafico_config_ajax'); ?>";
			$.post( url, {cd:$('#cd_indicador_tabela').val()}, function(data)
			{ 
				if(data.erro=='')
				{
					var _selecionado='';
					if( data.cd_indicador_grafico_tipo==GRAFICO_LINHA ){ _selecionado='linha'; }
					if( data.cd_indicador_grafico_tipo==GRAFICO_BARRA ){ _selecionado='barra'; }
					if( data.cd_indicador_grafico_tipo==GRAFICO_BARMU ){ _selecionado='barmu'; }
					if( data.cd_indicador_grafico_tipo==GRAFICO_PIZZA ){ _selecionado='pizza'; }

					limpar_grafico();
					if( _selecionado!='' ){ grafico_click('#grafico-'+_selecionado); }

					$('#ds_range_tick').val(data.ds_range_tick);
					$('#ds_range_legenda').val(data.ds_range_legenda);
					$('#ds_range_valor').val(data.ds_range_valor);

					gerar_grafico();

					exibir_tabela();
				} 
				else
				{
					alert( data.erro );
				}
			}, 'json' );
		}
		else
		{
		}
	}

	function gerar_grafico()
	{
		url="<?php echo site_url('indicador/grafico_config/grafico_ajax/'); ?>";
		$.post( url, {cd:$('#cd_indicador_tabela').val()},function(data)
		{
			//$('#output-div').html(data);
			document.getElementById('visualizar_iframe').src=data;
		} );
	}

	function salvar()
	{
		if( $('#cd_indicador_tabela').val()=='' )
		{
			alert('Escolha um indicador antes de salvar.');
			$('#cd_indicador_tabela').focus();
			return false;
		}
		if( selecionado=='' || selecionado==0 )
		{
			alert('Escolha um tipo de gr�fico antes de salvar.');

			$('#grafico_box_content').fadeOut( 'fast', function(){ $('#grafico_box_content').fadeIn('fast', function(){$('#grafico_box_content').fadeOut( 'fast', function(){ $('#grafico_box_content').fadeIn('fast'); } );}); } );

			return false;
		}
		if( selecionado=='pizza' )
		{
			if( $('#ds_range_legenda').val()=='' )
			{
				alert('Informe a s�rie de c�lulas que correspondem as legendas que ser�o exibidas no gr�fico.');
				$('#ds_range_legenda').focus();
				return false;
			}
		}
		if( selecionado=='linha' )
		{
			if( $('#ds_range_tick').val()=='' )
			{
				alert('Informe a s�rie de c�lulas que correspondem aos r�tulos que ser�o exibidos no gr�fico.');
				$('#ds_range_tick').focus();
				return false;
			}
		}
		if( selecionado=='barra' || selecionado=='barmu' )
		{
			if( $('#ds_range_tick').val()=='' )
			{
				alert('Informe a s�rie de c�lulas que correspondem as legendas que ser�o exibidas no gr�fico.');
				$('#ds_range_tick').focus();
				return false;
			}
		}
		if( $('#ds_range_valor').val()=='' )
		{
			alert('Informe a s�rie de c�lulas que correspondem aos valores que ser�o exibidos no gr�fico.');
			$('#ds_range_valor').focus();
			return false;
		}

		if( confirm('Salvar?') )
		{
			if(selecionado=='pizza'){ _cd_indicador_grafico_tipo=GRAFICO_PIZZA; }
			if(selecionado=='linha'){ _cd_indicador_grafico_tipo=GRAFICO_LINHA; }
			if(selecionado=='barra'){ _cd_indicador_grafico_tipo=GRAFICO_BARRA; }
			if(selecionado=='barmu'){ _cd_indicador_grafico_tipo=GRAFICO_BARMU; }
			
			url="<?php echo site_url('indicador/grafico_config/salvar'); ?>";
			$.post(url,{cd_indicador_tabela:$('#cd_indicador_tabela').val(),cd_indicador_grafico_tipo:_cd_indicador_grafico_tipo,ds_range_valor:$('#ds_range_valor').val(),ds_range_legenda:$('#ds_range_legenda').val(),ds_range_tick:$('#ds_range_tick').val()},function(data){
				if(data=='true')
				{
					gerar_grafico();
					alert('Salvo com sucesso!');
				}
				else
				{
					alert(data);
				}
			})
		}
	}

	/* FACILITADORES PARA ESCOLHA DAS S�RIES DE C�LULAS */
	var textbox_sel='';
	function mudar_textbox(ob)
	{
		textbox_sel = $(ob).attr('id');
	}

	function exibir_tabela()
	{
		url="<?php echo site_url('indicador/grafico_config/tabela'); ?>";
		$.post( url, { cd_indicador_tabela:$('#cd_indicador_tabela').val() }, 
		function(data)
		{ 
			$('#visualizar_tabela_box_content').html( data );
		} );
	}
	
	function escolher_celula_na_tabela(v)
	{
		if( textbox_sel!='' )
		{
			$('#'+textbox_sel).val( v );
			$('#'+textbox_sel).focus();
		}
	}
	
	function adicionar_celula_legenda()
	{
		or = $('#ds_range_legenda_2').val();
		c1 = $('#cel_1_leg').val();
		c2 = $('#cel_2_leg').val();
		
		sep='';
		if(or!=''){sep=';';}
		
		$('#ds_range_legenda_2').val( or+sep+c1+','+c2 );
		
		$('#cel_1_leg').val('');
		$('#cel_2_leg').val('');
		$('#cel_1_leg').focus();
	}
	function adicionar_celula_tick()
	{
		or = $('#ds_range_tick_2').val();
		c1 = $('#cel_1_tick').val();
		c2 = $('#cel_2_tick').val();
		
		sep='';
		if(or!=''){sep=';';}
		
		$('#ds_range_tick_2').val( or+sep+c1+','+c2 );
		
		$('#cel_1_tick').val('');
		$('#cel_2_tick').val('');
		$('#cel_1_tick').focus();
	}
	function adicionar_celula_valor()
	{
		or = $('#ds_range_valor_2').val();
		c1 = $('#cel_1_val').val();
		c2 = $('#cel_2_val').val();
		
		sep='';
		if(or!=''){sep=';';}
		
		$('#ds_range_valor_2').val( or+sep+c1+','+c2 );
		
		$('#cel_1_val').val('');
		$('#cel_2_val').val('');
		$('#cel_1_val').focus();
	}
	/* FACILITADORES PARA ESCOLHA DAS S�RIES DE C�LULAS */
	
</script>

<br>
<center>
<?php echo form_open(); ?>
<table>
<tr><td>
	<?php
	echo 
		form_start_box( 'indicador_box', 'Escolha o Indicador' )
		.form_default_dropdown( 'cd_indicador_tabela', 'Indicador', $indicador_dd, array($cd_indicador_tabela), "onchange='cd_indicador_event(this);'" )
		.form_end_box( 'indicador_box' )
		;
	?>
</td></tr>
<tr><td>
	<table>
	<tr>
	<td valign='top'>
		<?php echo form_start_box('grafico_box', 'Escolha o tipo de gr�fico', false); ?>
		<div id='grafico-linha' class='grafico-linha-normal' onmousemove="trocar_icone(this, 'linha', 'destaque');" onmouseout="trocar_icone(this, 'linha','normal');" onclick="grafico_click(this);"></div>
		<div id='grafico-barra' class='grafico-barra-normal' onmousemove="trocar_icone(this, 'barra', 'destaque');" onmouseout="trocar_icone(this, 'barra','normal');" onclick="grafico_click(this);"></div>
		<div id='grafico-barmu' class='grafico-barmu-normal' onmousemove="trocar_icone(this, 'barmu', 'destaque');" onmouseout="trocar_icone(this, 'barmu','normal');" onclick="grafico_click(this);"></div>
		<div id='grafico-pizza' class='grafico-pizza-normal' onmousemove="trocar_icone(this, 'pizza', 'destaque');" onmouseout="trocar_icone(this, 'pizza','normal');" onclick="grafico_click(this);"></div>
		<?php echo form_end_box('grafico_box', false); ?>
	</td>
	<td valign='top'>
		<?
		echo 
			form_start_box('conf_box_2', 'Configure os Par�metros para o gr�fico',true,true,'style="display:none;"')

			.form_default_row( '', 'S�rie para Legendas', " <input id='cel_1_leg' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> <input id='cel_2_leg' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> ".comando("add_legenda_btn", "ADD", "adicionar_celula_legenda();") )
			.form_default_text( 'ds_range_legenda_2', '', '', "style='width:300px;'" )

			.form_default_row( '', 'S�rie para R�tulos', " <input id='cel_1_tick' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> <input id='cel_2_tick' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> ".comando("add_legenda_btn", "ADD", "adicionar_celula_tick();") )
			.form_default_text( 'ds_range_tick_2', '', '', "style='width:300px;'" )

			.form_default_row( '', 'S�rie para Valores', " <input id='cel_1_val' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> <input id='cel_2_val' type='text' style='width:30px;' onfocus='mudar_textbox(this)' /> ".comando("add_legenda_btn", "ADD", "adicionar_celula_valor();") )
			.form_default_textarea( 'ds_range_valor_2', '', '', "style='width:300px;'" )

			.form_default_row( '', '', br().comando("salvar_btn", "Salvar", "") )
			.form_end_box('conf_box_2')
			;

		echo 
			form_start_box('conf_box', 'Configure os Par�metros para o gr�fico',true,true)
			.form_default_text( 'ds_range_legenda', 'S�rie de C�lulas para Legenda (C,C,L,L;C,C,L,L)', '', "style='width:300px;'" )
			.form_default_text( 'ds_range_tick', 'S�rie de C�lulas para R�tulos (C,C,L,L)', '', "style='width:300px;'" )
			.form_default_textarea( 'ds_range_valor', 'S�ries de C�lulas para Valores (C,C,L,L;C,C,L,L)', '', "style='width:300px;'" )
			.form_default_row( '', '', br().comando("salvar_btn", "Salvar", "salvar();") )
			.form_end_box('conf_box')
			;
		?>
	</td>
	</tr>
	</table>
</td></tr>

<tr><td>

	<?php 
	echo 
		form_start_box('visualizar_tabela_box', 'Visualiza��o da Tabela', false,true)
		.form_end_box('visualizar_tabela_box', false)
		;
	echo 
		form_start_box('visualizar_box', 'Visualiza��o do Gr�fico', false)
		."<iframe id='visualizar_iframe' name='visualizar_iframe' style='width:100%;height:400px; border-style:none;'></iframe>"
		.form_end_box('visualizar_box', false)
		;
	?>

</td></tr>

</table>

<div id='output-div'></div>
<?php echo form_close(); ?>
</center>
<script>
<?php
if(intval($cd_indicador_tabela)>0){ echo "cd_indicador_event(null);"; }
?>
</script>
<?
$this->load->view('footer.php');
