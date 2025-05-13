<?php
$this->load->view('header_interna');
?>
<script>
function filtrar()
{
	document.getElementById('current_page').value = 0;
	load();
}

function load()
{
	new Ajax.Updater( 'result_div', document.getElementById('base_url').value + 'rh/pessoa_result', 
	{ 
		parameters: 
		{ 
			current_page: $F('current_page')
			,nome: $F('nome_text')
			,divisao: $F('divisao_select')
			,nome_usual: $F('nome_usual__text')
			,usuario: $F('usuario_text')
		},
		onComplete:function()
		{
			configure_result_table();
		}
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "CaseInsensitiveString", "CaseInsensitiveString"]);
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
	ob_resul.sort(1, false);
}
</script>
<!-- <div class="aba_definicao"> -->

	<!-- div id="aba">
	<ul>
		<li class="abaSelecionada"><span>LISTA DE USUÁRIOS</span></li>
		<li><span>PERFIL DO USUÁRIO</span></li>
		<li><span>PREFERÊNCIAS</span></li>
		<li><span>MEU WORKSPACE</span></li>
	</ul>
	</div -->

	<div class="aba_conteudo">
				
		<!-- BARRA DE COMANDOS -->
		<div id="command_bar" class="command-bar">
			<input type="button" value="Exibir Filtro" class="botao" onclick="document.getElementById('filter_bar').show();" />
			<input type="button" value="PDF" class="botao" />
			<input type="button" value="Imprimir" class="botao" />
			<input type="button" value="Email" class="botao" />
			<input type="button" value="Novo registro" class="botao" />
		</div>
		<br />

		<!-- BARRA DE FILTRO -->
		<div id="filter_bar" class="filter-bar" style="display:none;">
			<br />
			<table align="center">
			<tr>
				<td><label for="nome_text">Nome:</label></td>
				<td>
					<?
					$attr = array('name'=>'nome_text', 'id'=>'nome_text');
					echo form_input($attr);
					?>
				</td>
				<td style="width:10px"></td>
				<td><label for="nome_usual__text">Nome Usual:</label></td>
				<td>
					<?
					$attr = array('name'=>'nome_usual__text', 'id'=>'nome_usual__text');
					echo form_input($attr);
					?>
				</td>
			</tr>
			<tr>
				<td><label for="divisao_select">Divisão:</label></td>
				<td>
					<select name="divisao_select" id="divisao_select">
					<option value="">Todos</option>
					<option value="GI">GI</option>
					<option value="GAD">GAD</option>
					<option value="GF">GF</option>
					</select>
				</td>
				<td style="width:10px"></td>
				<td><label for="usuario_text">Usuário:</label></td>
				<td>
					<?
					$attr = array('name'=>'usuario_text', 'id'=>'usuario_text');
					echo form_input($attr);
					?>
				</td>
			</tr>
			</table>
			<br>
			<input type="button" 
				class="botao" 
				value="Filtrar" 
				onclick="filtrar();" 
				/><input type="button" class="botao" value="Limpar" /><input type="button" class="botao" value="Esconder filtros" onclick="document.getElementById('filter_bar').hide();" />
			<br>
			<br>
		</div>
		<div id="result_div"></div>
		<br />
		<!-- <div id="command_bar" class="command-bar">
			<input type="button" value="PDF" class="botao" />
			<input type="button" value="Imprimir" class="botao" />
			<input type="button" value="Email" class="botao" />
			<input type="button" value="Novo registro" class="botao" />
		</div> -->
		<!-- BARRA DE COMANDOS -->
		
	</div>

<!-- </div> -->

<script type="text/javascript">
	filtrar();
</script>

<?php
$this->load->view('footer_interna');
?>