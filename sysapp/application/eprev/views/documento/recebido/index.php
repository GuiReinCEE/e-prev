<?php
$this->load->view('header_sem_menu');
?>
<script>
function filtrar()
{
	document.getElementById("current_page").value = 0;
	load();
}

function load()
{
	$.post( '<?php echo base_url(); ?>index.php/documento/recebido/listar'
		,{
			current_page: document.getElementById("current_page").value
		}
		,
	function(data)
		{
			document.getElementById("result_div").innerHTML = data;
			configure_result_table();
		}
	);
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[null]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	// ob_resul.sort(1, false);
}

function abrir(v)
{
	document.getElementById('linha_'+v).style.display = '';
}
function fechar(v)
{
	document.getElementById('linha_'+v).style.display = 'none';
}

function enviar_form(v)
{
	document.getElementById("data_envio_td_"+v).innerHTML = "<?php echo date('d/m/Y'); ?>";
	carregar_gerencias(v);
}

function carregar_gerencias(v)
{
	document.getElementById("destino_envio_td_"+v).innerHTML = 
		"<SELECT onchange='carregar_usuarios(\""+v+"\")'><OPTION value=''>.:Gerências:.</OPTION><OPTION value='GI'>GI</OPTION><OPTION value='GAP'>GAP</OPTION></SELECT><span id='usuarios_span_"+v+"'></span>";
}

function carregar_usuarios(v)
{
	document.getElementById("usuarios_span_"+v).innerHTML = 
		"<SELECT id='usuarios_select_"+v+"'><OPTION VALUE=''>.:Usuários:.</OPTION><OPTION VALUE='191'>Carlos</OPTION><OPTION VALUE='136'>Cristiano</OPTION></SELECT><input type='button' value='Salvar' class='botao' onclick='salvar_destino(\""+v+"\");' />";
}

function salvar_destino(v)
{
	$.post( '<?php echo base_url().index_page(); ?>/documento/recebido/salvar_envio'
		,{
			cd_documento_recebido_item: v
			, cd_usuario_destino: document.getElementById('usuarios_select_'+v).value
		}
		,function(data)
		{
			// alert(data);
		}
	);

	document.getElementById("destino_envio_td_"+v).innerHTML = document.getElementById('usuarios_select_'+v).options[ document.getElementById('usuarios_select_'+v).selectedIndex ].text;
	alert( 'Alterações realizadas com sucesso!');
}

function receber_form(v)
{
	$.post( '<?php echo base_url().index_page(); ?>/documento/recebido/salvar_envio'
		,{
			cd_documento_recebido_item: v
			, cd_usuario_destino: document.getElementById('usuarios_select_'+v).value
		}
		,function(data)
		{
			// alert(data);
		}
	);

	document.getElementById("destino_envio_td_"+v).innerHTML = document.getElementById('usuarios_select_'+v).options[ document.getElementById('usuarios_select_'+v).selectedIndex ].text;
	alert( 'Alterações realizadas com sucesso!');
}

function reencaminhar_form(v)
{
	$.post( '<?php echo base_url().index_page(); ?>/documento/recebido/salvar_reencaminhar'
		,{
			cd_documento_recebido_item: v
			, cd_usuario_destino: document.getElementById('usuarios_select_'+v).value
		}
		,function(data)
		{
			// alert(data);
		}
	);

	document.getElementById("destino_envio_td_"+v).innerHTML = document.getElementById('usuarios_select_'+v).options[ document.getElementById('usuarios_select_'+v).selectedIndex ].text;
	alert( 'Alterações realizadas com sucesso!');
}

function incluir_documento()
{
	window.location = '<?php echo base_url(); ?>index.php/documento/recebido/documento';
}
</script>

	<div class="aba_conteudo">

		<!-- BARRA DE COMANDOS -->
		<div id="command_bar" class="command-bar">
			<input type="button" value="Exibir Filtro" class="botao" onclick="$('#filter_bar').show();" />
			<input type="button" value="Incluir documento" class="botao" onclick="incluir_documento();" />
		</div>
		<br />

		<!-- BARRA DE FILTRO -->
		<div id="filter_bar" class="filter-bar" style="display:none;">
			<h3>Filtros</h3>
			<table cellpadding="0" cellspacing="0" align="center">

			<tr>

				<td valign="top" style="padding-left:30px">Assunto:</td>
				<td valign="top" style="padding-left:30px">
				<?php
					echo form_input( array('name'=>'assunto', 'id'=>'assunto', 'style'=>'width:300px;') );
				?>
				</td>

			</tr>

			</table>

			<br>
			<input type="button" 
				class="botao" 
				value="Filtrar" 
				onclick="filtrar();" 
				/><input type="button" class="botao" value="Esconder filtros" onclick="$('#filter_bar').hide();" />
			<br>
			<br>
		</div>
		<div id="result_div"></div>
		<br />
		<!-- BARRA DE COMANDOS -->

	</div>

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer_sem_menu');
?>