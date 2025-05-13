<?php 
$this->load->view('header'); 

$abas[] = array('aba_lista', 'Definição', TRUE, 'location.reload();');
echo aba_start( $abas );

?>

	<script type="text/javascript">
    // JACOBSEN - 18/01/2007

	function getComboTabela( cd_db )
	{
		var ds_url   = "ajax_frm_tarefa_tabelas.php";
		if(cd_db != "")
		{
			var lt_param = "ds_funcao=TABELA";
			    lt_param += "&cd_db=" + cd_db;
			            
			ajaxExecute(ds_url, lt_param, "document.getElementById('ob_tabela')", '.innerHTML=', 'POST');
			ajaxExecute(ds_url, 'ds_funcao=CAMPO&cd_db=' + cd_db, "document.getElementById('ob_campo')", '.innerHTML=', 'POST');
			
		}
		else 
		{
			ajaxExecute(ds_url, 'ds_funcao=TABELA', "document.getElementById('ob_tabela')", '.innerHTML=', 'POST');
			ajaxExecute(ds_url, 'ds_funcao=CAMPO', "document.getElementById('ob_campo')", '.innerHTML=', 'POST');
		}
	}

	function getComboCampo( cd_tabela )
	{
		var ds_url    = "ajax_frm_tarefa_tabelas.php";
		if(cd_tabela != "")
		{
			var lt_param  = "ds_funcao=CAMPO";
			    lt_param += "&cd_tabela=" + cd_tabela;
				lt_param += "&cd_db=" + document.getElementById('cd_db').value;
			            
			ajaxExecute(ds_url, lt_param, "document.getElementById('ob_campo')", '.innerHTML=', 'POST');
		} 
		else 
		{
			ajaxExecute(ds_url, 'ds_funcao=CAMPO', "document.getElementById('ob_campo')", '.innerHTML=', 'POST');
		}
	}
	
	function delLineTable(ob_linha)
	{
		ob_linha.parentNode.removeChild(ob_linha);
	}
	
	function addDetalhe()
	{
		if( validDetalhe() )
		{
			var ob_detalhe    = document.getElementById('ob_detalhe');
			var ob_linha      = document.createElement('tr');
				ob_linha.style.cssText = "background-color: #F4F4F4;";
			var ob_tabela     = document.createElement('td');
			var ob_campo      = document.createElement('td');
			var ob_fl_campo   = document.createElement('td');
			var ob_vl_dominio = document.createElement('td');
			var ob_es         = document.createElement('td');
			var ob_prompt     = document.createElement('td');
			var ob_visivel    = document.createElement('td');
			var ob_del     = document.createElement('td');
				ob_del.style.cssText = "text-align:center;";
			var ob_bt_del  = document.createElement('input');
				ob_bt_del.type    = "button";
				ob_bt_del.value   = "Remover";
				ob_bt_del.onclick = function(){ delLineTable(ob_linha); }

			ob_del.appendChild(ob_bt_del);
				
			ob_tabela.innerHTML = "<input type='text' name='ar_tabela[]' value='" + document.getElementById('cd_db').value +"."+ document.getElementById('cd_tabela').value + "' readonly style='color:#999999;' >";
			ob_campo.innerHTML  = "<input type='text' name='ar_campo[]' value='" + document.getElementById('cd_campo').value + "' />";
			ds_select = "<select name='ar_fl_campo[]'>";
			ds_select+= "<option value='T'>Text Item</option>";
			ds_select+= "<option value='L'>List Item</option>";
			ds_select+= "<option value='C'>Check Box</option>";
			ds_select+= "<option value='R'>Radio Group</option>";
			ds_select+= "<option value='P'>Push Bottom</option>";
			ds_select+= "<option value='D'>Display Item</option>";
			ds_select+= "</select>";
			ob_fl_campo.innerHTML  = ds_select;
			
			ob_vl_dominio.innerHTML  = "<input type='text' name='ar_vl_dominio[]' value='' >";
			ob_es.innerHTML          = "<select name='ar_fl_campo_de[]'><option value='E'>Enable</option><option value='D'>Disable</option></select>";
			ob_prompt.innerHTML      = "<input type='text' name='ar_prompt[]' value='' >";
			ob_visivel.innerHTML     = "<select name='ar_fl_visivel[]'><option value='S'>Sim</option><option value='N'>Não</option></select>";

			ob_linha.appendChild(ob_tabela);
			ob_linha.appendChild(ob_campo);
			ob_linha.appendChild(ob_fl_campo);
			ob_linha.appendChild(ob_vl_dominio);
			ob_linha.appendChild(ob_es);
			ob_linha.appendChild(ob_prompt);
			ob_linha.appendChild(ob_visivel);
			ob_linha.appendChild(ob_del);
			
			ob_detalhe.appendChild(ob_linha);
		}
	}
	
	var indice_ordem = 0;
	function addOrdem()
	{
		if(validDetalhe())
		{
			indice_ordem++;
			var ob_detalhe    = document.getElementById('ob_detalhe_ordem');
			var ob_linha      = document.createElement('tr');
				ob_linha.style.cssText = "background-color: #F4F4F4;";
			var ob_db         = document.createElement('td');
			var ob_tabela     = document.createElement('td');
			var ob_campo      = document.createElement('td');
			var ob_ordem	  = document.createElement('td');
			var ob_del     = document.createElement('td');
				ob_del.style.cssText = "text-align:center;";
			var ob_bt_del  = document.createElement('input');
				ob_bt_del.type    = "button";
				ob_bt_del.value   = "Remover";
				ob_bt_del.onclick = 
				function()
				{
					delLineTable(ob_linha);
				}

			ob_del.appendChild(ob_bt_del);
				
			ob_db.innerHTML     = "<input type='text' name='ar_ordem_db[]' value='" + document.getElementById('cd_db').value +"' readonly style='color:#999999;' >";
			ob_tabela.innerHTML = "<input type='text' name='ar_ordem_tabela[]' value='" + document.getElementById('cd_tabela').value + "' readonly style='color:#999999;' >";
			ob_campo.innerHTML  = "<input type='text' name='ar_ordem_campo[]' value='" + document.getElementById('cd_campo').value + "' readonly style='color:#999999;' >";
			ob_ordem.innerHTML  = "<input type='text' name='ar_ordem[]' id='ar_ordem_"+indice_ordem+"' value='" + document.getElementById('nr_ordem').value + "'>";

			ob_linha.appendChild(ob_db);
			ob_linha.appendChild(ob_tabela);
			ob_linha.appendChild(ob_campo);
			ob_linha.appendChild(ob_ordem);
			ob_linha.appendChild(ob_del);
			
			ob_detalhe.appendChild(ob_linha);
			MaskInput( document.getElementById("ar_ordem_"+indice_ordem), "999999" );
			
		}
	}	
	
	function addLovs()
	{
		var ob_detalhe    = document.getElementById('ob_lovs');
		var ob_linha      = document.createElement('tr');
			ob_linha.style.cssText = "background-color: #F4F4F4;";
		var ob_seq     = document.createElement('td');
		var ob_tabela      = document.createElement('td');
		var ob_campo_ori   = document.createElement('td');
		var ob_campo_des = document.createElement('td');
		var ob_del     = document.createElement('td');
			ob_del.style.cssText = "text-align:center;";
		var ob_bt_del  = document.createElement('input');
			ob_bt_del.type    = "button";
			ob_bt_del.value   = "Remover";
			ob_bt_del.onclick = function(){ delLineTable(ob_linha); }

		ob_del.appendChild(ob_bt_del);
			
		ob_seq.innerHTML       = "<input type='text' name='ar_lovs_seq[]' value=''>";
		ob_tabela.innerHTML    = "<input type='text' name='ar_lovs_tabela[]' value=''>";
		ob_campo_ori.innerHTML = "<input type='text' name='ar_lovs_campo_ori[]' value=''>";
		ob_campo_des.innerHTML = "<input type='text' name='ar_lovs_campo_des[]' value=''>";

		ob_linha.appendChild(ob_seq);
		ob_linha.appendChild(ob_tabela);
		ob_linha.appendChild(ob_campo_ori);
		ob_linha.appendChild(ob_campo_des);
		ob_linha.appendChild(ob_del);
		
		ob_detalhe.appendChild(ob_linha);
	}		
	
	function validDetalhe()
	{
		
		if(document.getElementById('cd_db').value == "")
		{
			return false;
		}
		
		if(document.getElementById('cd_tabela').value == "")
		{
			return false;
		}

		if(document.getElementById('cd_campo').value == "")
		{
			return false;
		}		
		
		return true;
	}


	
	var nr_conta = 0;
	function addFile()
    {
        nr_conta++;
		var ds_obj_grupo     = 'arquivo_add';
		var ds_obj_item      = 'arquivo_add_';
		var ob_grupo_arq     = document.getElementById(ds_obj_grupo);
        var ob_div_arq       = document.createElement('div');
		var ds_div_arq       = "'" + ds_obj_item + nr_conta + "'";
		
        ob_div_arq.setAttribute('id', ds_obj_item + nr_conta);
        ob_div_arq.innerHTML = '	<table>';
		ob_div_arq.innerHTML+= '		<tr>';
		ob_div_arq.innerHTML+= '        	<td>';	
		ob_div_arq.innerHTML+= '				<font size="2" face="Verdana, Arial, Helvetica, sans-serif">';
		ob_div_arq.innerHTML+= '	 				Selecione o arquivo:';
		ob_div_arq.innerHTML+= '				</font>';
		ob_div_arq.innerHTML+= '        	</td>';
		ob_div_arq.innerHTML+= '        	<td>';
        ob_div_arq.innerHTML+= '				<input name="ar_arquivo[]" type="file"> ';
		ob_div_arq.innerHTML+= '        	</td>';
		ob_div_arq.innerHTML+= '        	<td>';
		ob_div_arq.innerHTML+= '				<input type="button" value="Remover"  onclick="removeFile(' + ds_div_arq + ');">';
		ob_div_arq.innerHTML+= '        	</td>';
		ob_div_arq.innerHTML+= '		</tr>';
		ob_div_arq.innerHTML+= '	</table>';
							 
        ob_grupo_arq.appendChild(ob_div_arq);
    }

    function removeFile(id_obj)
    {
		document.getElementById(id_obj).innerHTML = "";
    }
	
	function formataNumero(e)
	{
		if (e.keyCode) charCode = e.keyCode;  	
		else if (e.which) charCode = e.which;
		else if (e.charCode) charCode = e.charCode;

		
		if (charCode == 13) //ENTER
		{
			return true;
		}
		
		if (charCode == 8) //BACKSPACE
		{
			return true;
		}

		if (charCode == 46) //DEL
		{
			return true;
		}
		
		
		var var_caracter = String.fromCharCode(charCode);
		if ((var_caracter>="0") && (var_caracter<="9"))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
    </script>


	<table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr valign="top"> 
          <td> 
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <!-- START BLOCK : cadastro -->
        <tr valign="top"> 
    		<td colspan="2">
			<form name="form1" method="post" action="grava_tarefa.php?fl_tipo_grava={fl_tipo_grava}" enctype="multipart/form-data">
              <table border="0" align="center" cellpadding="2" cellspacing="1">
                <tr> 

                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Atividade Origem:</font></td>
					<td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><?php echo $origem; ?> / <?php echo $codigo; ?></font><font size="2" face="Arial, Helvetica, sans-serif"> 
                    <input name="origem" type="hidden" id="origem2" value="{origem}">
                    <input name="cd_tarefa" type="hidden" id="cd_tarefa" value="{cd_tarefa}">
                    <input name="dur_ant" type="hidden" id="dur_ant2" value="{dur_ant}">
                    <input name="insere" type="hidden" id="insere2" value="{insere}">
                    </font><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                    </font></td>
                  <td align="right"> <input name="image" type="image" src="https://www.e-prev.com.br/controle_projetos/img/btn_salvar.jpg" border="0"><a href="javaScript:window.print()"><img src="https://www.e-prev.com.br/controle_projetos/img/btn_impressora.jpg" border="0"></a><a href="ajuda.php#solic_tarefa"><img src="https://www.e-prev.com.br/controle_projetos/img/btn_ajuda.jpg" border="0"></a></td>
                </tr>

                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Projeto:</font></td>
                  <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">{projeto}</font></td>
                  <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input name="chk_encaminhar" type="checkbox" id="chk_encaminhar2" value="S">
                    Encaminhar para execu&ccedil;&atilde;o</font></td>
                </tr>

				<?php echo form_default_dropdown("programa", "Nome do programa", $programa_dd); ?>
				<?php echo form_default_dropdown("cad_tarefa", "Tipo da tarefa", $tipo_da_tarefa_dd); ?>
				<?php echo form_default_dropdown("mandante", "Analista", $mandante_dd); ?>

                <!-- <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Analista:</font></td>
                  <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="mandante" style="font-family: Verdana; font-size: 8 pt">

                      <option value="{cod_analista}"{sel_analista}>{nome_analista}</option>

                    </select>
                    {cod_analista} - {nome_analista} </font></td>
                </tr> -->

				<?php echo form_default_dropdown("executor", "Programador", $executor_dd); ?>
                <!-- <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Programador:</font></td>
                  <td colspan="3">
					<font size="1" face="Verdana, Arial, Helvetica, sans-serif">
					<select name="executor" id="select4" style="font-family: Verdana; font-size: 8 pt">

						<option value="{cod_atendente}"{sel_atendente}>{nome_atendente}</option>

					</select></font></td>
                </tr> -->

				<tr>
				  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Prioridade:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">
				    <input id="prioridade_sim" onclick="prioridade_click('');" type="radio" name="prioridade" value="S" {chkPrioridadeSim}> Sim 
					<input id="prioridade_nao" onclick="prioridade_click('none');" type="radio" name="prioridade" value="N" {chkPrioridadeNao}> Não
                    </font></td>				
				</tr>

				<tr id="trPrioridade" style="display:none;">
					<td>
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nível de prioridade:</font>
					</td>
	                <td colspan="3" style='font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12;'>
	                	<select id="nr_nivel_prioridade" name="nr_nivel_prioridade">
	                		<option {nr_nivel_prioridade_0} value="0">0</option>
	                		<option {nr_nivel_prioridade_1} value="1">1</option>
	                		<option {nr_nivel_prioridade_2} value="2">2</option>
	                		<option {nr_nivel_prioridade_3} value="3">3</option>
	                		<option {nr_nivel_prioridade_4} value="4">4</option>
	                		<option {nr_nivel_prioridade_5} value="5">5</option>
	                		<option {nr_nivel_prioridade_6} value="6">6</option>
	                		<option {nr_nivel_prioridade_7} value="7">7</option>
	                		<option {nr_nivel_prioridade_8} value="8">8</option>
	                		<option {nr_nivel_prioridade_9} value="9">9</option>
	                		<option {nr_nivel_prioridade_10} value="10">10</option>
	                	</select>
	                	onde 0 é o menor nível e 10 é o maior nível de prioridade
	            		<script>
		                  	function prioridade_click(display)
		                  	{
		                  		document.getElementById('trPrioridade').style.display=display;
		                  	}
							if( document.getElementById('prioridade_sim').checked ) document.getElementById('trPrioridade').style.display='';
							if( document.getElementById('prioridade_nao').checked ) document.getElementById('trPrioridade').style.display='none';
						</script>
					</td>
				</tr>
				<tr> 
                  <td valign="top">
                  	<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><label for="">Checklist de teste</label></font>
                  </td>
                  <td colspan="3">
                  	<font size="1" face="Verdana, Arial, Helvetica, sans-serif">
                  	<input type="radio" name="chk_checklist" value="S" {checklistSim}> Sim 
					<input type="radio" name="chk_checklist" value="N" {checklistNao}> Não
					</font>
                    <!-- <input name="chk_checklist" type="checkbox" id="chk_checklist" value="S" /> -->
                  </td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data 
                    de In&iacute;cio Prevista:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input name="dt_inicio" type="text" id="dt_inicio2" value="{dt_inicio}" size="12" maxlength="10"  onBlur="verifica_data(this)" onKeyUp="mascara_data(this)" >
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data 
                    de T&eacute;rmino Prevista:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input name="dt_fim" type="text" id="dt_fim2" value="{dt_fim}" size="12" maxlength="10" onBlur="verifica_data(this)" onKeyUp="mascara_data(this)"  >
                    </font></td>
                </tr>
                <tr bgcolor="#F4F4F4"> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data 
                    de in&iacute;cio da tarefa:</font></td>
                  <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">{dt_inicio_prog}</font></td>
                </tr>
                <tr bgcolor="#F4F4F4"> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data 
                    de fim da tarefa:</font></td>
                  <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">{dt_fim_prog}</font></td>
                </tr>
                <tr bgcolor="#F4F4F4"> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Data 
                    de Acordo:</font></td>
                  <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    {dt_ok_anal} </font></td>
                </tr>
				<tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Resumo:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input type="text" name="resumo" size="80" maxlength="95" value="{resumo}" >
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Objetivo:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <textarea   name="descricao" id="descricao" cols="60" rows="5" >{descricao}</textarea>
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Funcionalidades/restri&ccedil;&otilde;es 
                    <br>
                    da sele&ccedil;&atilde;o (regras): </font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <textarea  name="casos_testes" id="casos_testes" cols="60" rows="5" >{casos_testes}</textarea>
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fun&ccedil;&otilde;es 
                    ou procedimentos<br>
                    a serem utilizados: </font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <textarea  name="tabs_envolv" id="tabs_envolv" cols="60" rows="5" >{tabs_envolv}</textarea>
                    </font></td>
                </tr>
                
				<!--
					18/01/2007 - Cristiano Jacobsen
				-->
				<tr> 
					<td valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							Nome da Tela (Forms):
						</font>
					</td>
					<td colspan="3">
						<input type="text" name="ds_nome_tela" size="80" maxlength="95" value="{ds_nome_tela}" >
					</td>	
				<tr> 
					<td valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							Menu:
						</font>
					</td>
					<td colspan="3">
						<font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
							<textarea  name="ds_menu" cols="60" rows="5" id="ds_menu" >{ds_menu}</textarea>
						</font>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							LOVS:
						</font>
					</td>
					<td colspan="3" align="center">
						<table width='100%'>
							<tbody id='ob_lovs'>
								<tr bgcolor="#dae9f7">
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<b>Seq
									</td>
								    <td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<b>Tabela
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<b>Campo Origem
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<b>Campo Destino
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<BR>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="button" value="Adicionar" onclick="addLovs();">
						<script>
							addLovs();
						</script>
					</td>
				</tr>
				
                <tr> 
					<td valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							Detalhes da tela:
						</font>
					</td>
					<td colspan="3">
						<table>
							<tr>
								<td>
									<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										Banco:
									</font>
								</td>
								<td>
									<select name='cd_db' id='cd_db' onchange="getComboTabela(this.value);" style='font-family: Verdana; font-size: 8 pt'>
										<option value=''>Selecione</option>
										<option value='POSTGRESQL'>POSTGRESQL</option>
										<option value='ORACLE'>ORACLE</option>
										<option value='Novo'>Novo</option>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										Tabela:
									</font>
								</td>
								<td id="ob_tabela">
									<select name='cd_tabela' id='cd_tabela' style='width:440px; font-family: Verdana; font-size: 8 pt'>
										<option value=''>Selecione</option>
									</select>								
								</td>
							</tr>
							<tr>
								<td>
									<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										Campo:
									</font>
								</td>
								<td id="ob_campo">
									<select name='cd_campo' id='cd_campo' style='width:440px; font-family: Verdana; font-size: 8 pt'>
										<option value=''>Selecione</option>
									</select>																
								</td>
							</tr>
							<tr>
								<td>
									<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										Ordem:
									</font>
								</td>
								<td>
									<input type="text" id="nr_ordem" name="nr_ordem" value="0" />																
								</td>
							</tr>

							<tr>
								<td colspan="2" align="center"> 
									<input type="button" value="Adicionar Campo" onclick="addDetalhe();addOrdem();">
									<!-- <input type="button" value="Adicionar Ordenação" onclick="addOrdem();"> -->
									
								</td>
							</tr>								
						</table>
					</td>
                </tr>	
			
                <tr> 
					<td valign="top" colspan='4'>
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
						<table width='100%'>
							<tbody id='ob_detalhe'>
								<tr bgcolor="#dae9f7">
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Banco.Tabela</b></font>
									</td>
								    <td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Campo</b></font>
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Tipo Campo</b></font>
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Val. de Domínio</b></font>
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Ds/En</b></font>
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Prompt</b></font>
									</td>									
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Visível</b></font>
									</td>
									<td>
										<font  color="#0046ad" face="Arial, Helvetica, sans-serif">
										<BR>
									</td>
								</tr>
							</tbody>
						</table>
				<br>
				</tr>
			    <tr> 
					<td valign="top">
						<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
							Ordenado por:
						</font>
					</td>
					<td colspan="3">
						<table width='100%'>
							<tbody id='ob_detalhe_ordem'>
								<tr bgcolor="#dae9f7">
									<td><font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Banco</b></font></td>
								    <td><font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Tabela</b></font></td>
									<td><font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Campo</b></font></td>
									<td><font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Ordem</b></font></td>
									<td><font  color="#0046ad" face="Arial, Helvetica, sans-serif"><BR></td>
								</tr>
							</tbody>
						</table>					
					</td>
                </tr>
              </table>
            <div align="center"><br>
				<table width="90%" align="center" cellpadding="4" cellspacing="1">
					<tr bgcolor="#dae9f7"> 
						<td colspan="2">
							<font  color="#0046ad" face="Arial, Helvetica, sans-serif"><b>Arquivos, layouts ou documentos anexos:</b></font>
							</font>
						</td>
					</tr>
					<tr> 
						<td colspan="2" id="arquivo_add" align="center">
							<script>
								addFile();
							</script>
						</td>
					</tr>
					<tr> 
						<td colspan="2" align="center"> 
							<input type="button" value="Adicionar" onclick="addFile();">
						</td>
					</tr>
				</table>
            </div>			
			</td>

			<td>&nbsp;</td>

        </tr>
		<tr> 
			  <td colspan="2"  align="center">
			  <img src="img/img_divisoria1.gif" width="80%" height="1">
			  <br>
				<div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <input type="submit" name="Submit2" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;">
                      </font></div>			  
			  </td>
			  <td>&nbsp;</td>
		</tr>		

      </table>
  </td></tr></table>

<!-- Begin: Insert DYNTAR -->
<link rel="stylesheet" href="<?php echo base_url()."js/dyn/" ?>dyntar.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url()."js/dyn/" ?>dyntar.js"></script>
<!-- End: Insert DYNTAR -->	

<?php $this->load->view('footer');?>