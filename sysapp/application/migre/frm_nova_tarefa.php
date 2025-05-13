<?php 
$this->load->view('header'); 

$abas[] = array('aba_lista', 'Definição', TRUE, 'location.reload();');
echo aba_start( $abas );

?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="main.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="inc/menu_principal.js"></script>


<table border="0" cellpadding="0" cellspacing="0" width="{largura_tela}">
  <tr> 
    <td background="{c_menu}{img_fundo_sup}" bgcolor="{cor_sup_banner}" height="{altura_sup_banner}"><a href="http://www.e-prev.com.br/controle_projetos/index.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image01','','{c_menu}logo_swap.gif',1)"><img src="{c_menu}logo.gif" alt="Página inicial" name="Image01" border="0" hspace="{espaco_superior}" vspace="{espaco_vertical}" {largura_banner}{altura_banner}></a></td>
  </tr>
  <tr> 
    <td bgcolor="{cor_fundo_banner}" background="{c_menu}img_fundo_banner.jpg" height="{altura_banner}" class="{classe_banner}"><!-- InstanceBeginEditable name="imagem" --><img src="{c_banner}banner_def_tarefa.jpg" {titulo_tela}{largura_banner}{altura_banner}><!-- InstanceEndEditable --></td>
  </tr>
  <tr> 
    <td height="{altura_inf_banner}" colspan="2" bgcolor="{cor_inf_banner}" background="{c_menu}{img_fundo_menu}"><img src="{c_menu}img_inicial_menu.jpg" border="0" alt="" {largura_banner}{altura_banner}></td>
  </tr>
  <tr> 
    <td height="{alt_menu_principal}" valign="top" background="{c_menu}img_fundo_menu3.jpg" bgcolor="{cor_inf_banner}"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" {largura_banner}{altura_banner}>
        <tr> 
          <td {largura_banner}{altura_banner}><img src="{c_menu}img_paragrafo_skin.jpg" border="0" alt="" {largura_banner}{altura_banner}></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
		<td valign="top"> 

            <!-- START BLOCK : mensagem -->
            <font color="#FF0000">{msg}</font> 
            <!-- END BLOCK : mensagem -->
          </td>
		  <td width="90%">&nbsp;</td>
          <td></td>
        </tr>
        <tr valign="top"> 
    		<td colspan="2">
			<form name="form1" method="post" action="grava_tarefa.php">
              <table border="0" align="center" cellpadding="2" cellspacing="1">
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    Atividade Origem:</font></td>
                  <td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif">{origem} 
                    / {codigo} 
                    </font><font size="2" face="Arial, Helvetica, sans-serif"> 
                    <input name="origem" type="hidden" id="origem2" value="{origem}">
                    <input name="cd_tarefa" type="hidden" id="cd_tarefa" value="{cd_tarefa}">
                    <input name="dur_ant" type="hidden" id="dur_ant2" value="{dur_ant}">
                    <input name="insere" type="hidden" id="insere2" value="{insere}">
                    </font><font size="1" face="Verdana, Arial, Helvetica, sans-serif">&nbsp; 
                    </font></td>
                  <td align="right"> <input name="image" type="image" src="img/btn_salvar.jpg" border="0"><a href="javaScript:window.print()"><img src="img/btn_impressora.jpg" border="0"></a><a href="ajuda.php#solic_tarefa"><img src="img/btn_ajuda.jpg" border="0"></a></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Projeto:</font></td>
                  <td colspan="2"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">{projeto}</font></td>
                  <td align="right"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <input name="chk_encaminhar" type="checkbox" id="chk_encaminhar2" value="S">
                    Encaminhar para execu&ccedil;&atilde;o</font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Nome 
                    do programa:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="programa" id="select" >
                      <!-- START BLOCK : programa -->
                      <option value="{programa}" {sel_programa}>{programa}</option>
                      <!-- END BLOCK : programa -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Tipo 
                    da tarefa:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="cad_tarefa" id="select2" >
                      <!-- START BLOCK : tarefa -->
                      <option value="{cod_cad_tarefa}"{sel_tarefa}>{nome_cad_tarefa}</option>
                      <!-- END BLOCK : tarefa -->
                    </select>
                    {tipo_origem}</font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Analista:</font></td>
                  <td colspan="3"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="mandante" style="font-family: Verdana; font-size: 8 pt">
                      <!-- START BLOCK : mandante -->
                      <option value="{cod_analista}"{sel_analista}>{nome_analista}</option>
                      <!-- END BLOCK : mandante -->
                    </select>
                    {cod_analista} - {nome_analista} </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Programador:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="executor" id="select4" style="font-family: Verdana; font-size: 8 pt">
                      <!-- START BLOCK : atendente -->
                      <option value="{cod_atendente}"{sel_atendente}>{nome_atendente}</option>
                      <!-- END BLOCK : atendente -->
                    </select>
                    </font></td>
                </tr>

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
                <!--<tr bgcolor="#F4F4F4"> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Classifica&ccedil;&atilde;o 
                    da Tarefa:</font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <select name="tipo_tarefa" id="select4" >
					-->
                <!-- START BLOCK : tipo_tarefa -->
                <!-- <option value="{cd_tipo_tarefa}" {sel_tipo_tarefa}>{nome_tipo_tarefa}</option>
                      <!-- END BLOCK : tipo_tarefa -->
                <!-- </select>
                    </font></td>
                </tr>-->
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
                    <textarea  name="descricao" id="descricao" cols="60" rows="5" >{descricao}</textarea>
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Funcionalidades/restri&ccedil;&otilde;es 
                    <br>
                    da sele&ccedil;&atilde;o (regras): </font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <textarea  name="casos_testes" id="casos_testes" cols="60" rows="5">{casos_testes}</textarea>
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Funções ou procedimentos<br />
                    a serem utilizados: </font></td>
                  <td colspan="3"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                    <textarea  name="tabs_envolv" id="tabs_envolv" cols="60" rows="5"  >{tabs_envolv}</textarea>
                    </font></td>
                </tr>
                <tr> 
                  <td colspan="4"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><br>
                      </font></div></td>
                </tr>
                <!--bloco da acs -->
                <!-- START BLOCK : tarefa_acs -->
                <tr> 
                  <td colspan="4" align="center" bgcolor="#0046ad"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Caracter&iacute;sticas 
                    do Trabalho:</b></font></td>
                </tr>
                <tr> 
                  <td colspan="4" align="center"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_grafica" type="checkbox" value="S" {chk_grafica}>
                          Gr&aacute;fica</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_eletronica" type="checkbox" id="opt_eletronica" value="S" {chk_eletronica}>
                          Eletr&ocirc;nica</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_evento" type="checkbox" id="opt_evento" value="S" {chk_evento}>
                          Evento</font></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td colspan="4" align="center"> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr bgcolor="#CCCCCC"> 
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_anuncio" type="checkbox" id="opt_anuncio" value="S" {chk_anuncio}>
                          An&uacute;ncio</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_folder" type="checkbox" id="opt_folder" value="S" {chk_folder}>
                          Folder</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_mala" type="checkbox" id="opt_mala" value="S" {chk_mala}>
                          Mala-direta</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_cartaz" type="checkbox" id="opt_cartaz" value="S" {chk_cartaz}>
                          Cartaz</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_cartilha" type="checkbox" id="opt_cartilha" value="S" {chk_cartilha}>
                          Cartilha</font></td>
                        <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
                          <input name="opt_site" type="checkbox" id="opt_site" value="S" {chk_site}>
                          Site</font></td>
                      </tr>
                    </table></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cores:</font></td>
                  <td colspan="32"><font size="1" face="Verdana"> 
                    <input name="cores" type="text" id="cores2"   value="{cores}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Formato:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <input name="formato" type="text" id="formato2"   value="{formato}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Gramatura:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <input name="gramatura" type="text" id="gramatura"   value="{gramatura}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Quantia:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <input name="quantia" type="text" id="quantia"   value="{quantia}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Custo:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <input name="custo" type="text" id="custo"   value="{custo}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">CC:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <input name="cc" type="text" id="cc"   value="{cc}" size="50" maxlength="100" {ro_solic}>
                    </font></td>
                </tr>
                <tr> 
                  <td colspan="4" align="center" bgcolor="#0046ad"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>P&uacute;blico:</b></font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Participante:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_pacs"  {ro_solic}>
                      <!-- START BLOCK : cbo_pacs -->
                      <option value="{cd_pacs}" {chk_pacs}>{nome_pacs}</option>
                      <!-- END BLOCK : cbo_pacs -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Patrocinadora:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_patroc"  {ro_solic}>
                      <!-- START BLOCK : cbo_patroc -->
                      <option value="{cd_patr}" {chk_patr}>{nome_patr}</option>
                      <!-- END BLOCK : cbo_patroc -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Plano:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_nacs"  {ro_solic}>
                      <!-- START BLOCK : cbo_nacs -->
                      <option value="{cd_nacs}" {chk_nacs}>{nome_nacs}</option>
                      <!-- END BLOCK : cbo_nacs -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Comunidade:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_cacs"  {ro_solic}>
                      <!-- START BLOCK : cbo_cacs -->
                      <option value="{cd_cacs}" {chk_cacs}>{nome_cacs}</option>
                      <!-- END BLOCK : cbo_cacs -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Localiza&ccedil;&atilde;o:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_lacs"  {ro_solic}>
                      <!-- START BLOCK : cbo_lacs -->
                      <option value="{cd_lacs}" {chk_lacs}>{nome_lacs}</option>
                      <!-- END BLOCK : cbo_lacs -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Distribui&ccedil;&atilde;o:</font></td>
                  <td colspan="3"><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_dacs"  {ro_solic}>
                      <!-- START BLOCK : cbo_dacs -->
                      <option value="{cd_dacs}" {chk_dacs}>{nome_dacs}</option>
                      <!-- END BLOCK : cbo_dacs -->
                    </select>
                    </font></td>
                </tr>
                <tr> 
                  <td valign="top"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Observa&ccedil;&atilde;o:</font></td>
                  <td colspan="3"> <font size="1" face="Verdana"> 
                    <textarea  rows="10" name="txt_descricao" cols="60" >{descricao}</textarea>
                    </font> </td>
                </tr>
                <tr> 
                  <td valign="top"><font size="1" face="Verdana">Atendente da 
                    Atividade:</font></td>
                  <td><font size="1" face="Verdana"> 
                    <select size="1" name="cbo_analista"  {ro_solic}>
                      <!-- START BLOCK : cbo_analista_acs -->
                      <option value="{codana}" {chkana}>{nomeana}</option>
                      <!-- END BLOCK : cbo_analista_acs -->
                    </select>
                    </font></td>
                  <td colspan="2"><font size="1" face="Verdana">Indique para quem 
                    voc&ecirc; <br>
                    vai encaminhar esta solicita&ccedil;&atilde;o</font></td>
                </tr>
                <!-- END BLOCK : tarefa_acs -->
                <!-- fim do bloco da acs -->
                <tr> 
                  <td colspan="4"> <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
                      <input type="submit" name="Submit2" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;">
                      </font></div></td>
                </tr>
              </table>
            </form>
            <div align="center"> <img src="img/img_divisoria1.gif" width="80%" height="1"><br>
            </div></td>
			<td>&nbsp;</td>

        </tr>
		
      </table>
  </td></tr></table>	

<!-- Begin: Insert DYNTAR -->
<link rel="stylesheet" href="inc/dynamic_textarea_resizer/dyntar.css" type="text/css" />
<script type="text/javascript" src="inc/dynamic_textarea_resizer/dyntar.js"></script>
<!-- End: Insert DYNTAR -->	

<?php $this->load->view('footer');?>