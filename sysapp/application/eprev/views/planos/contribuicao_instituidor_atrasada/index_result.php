<?php
/*
  #### FORMAS DE PAGAMENTO ####
  "BCO";"DÉBITO EM CONTA CORRENTE"
  "BDL";"BLOQUETO BANCARIO"
  "CHQ";"CHEQUE"
  "DEP";"DEPÓSITO BANCÁRIO"
  "FLT";"FOLHA PATROCINADORA"
  "FOL";"FOLHA DE PAGAMENTO"

  #### CODIGOS_COBRANCAS #####
  2450;"CONTRIBUIÇÃO SINPRO-RS PREV"

 */

#echo "<PRE>".print_r($ar_contribuicao_atrasada,true)."</PRE>";
#echo "<PRE>".print_r($ar_contribuicao_atrasada_anterior,true)."</PRE>";
#exit;
?>
<BR>
<style>
    .contribuicao_instituidor * {
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: normal;
    }

    .contribuicao_instituidor hr {
        border-width: 0;
        height: 1px;
        border-top-width: 1px;
        border-top-color: gray;
        border-top-style: dashed;

    }	

    .ci_cadastro * {
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: normal;
    }

    .ci_cadastro {
        border: 1px solid #64992C;
    }	

    .ci_cadastro input{
        border: 1px solid gray;
        padding-right: 3px;
    }	

    .ci_cadastro caption {
        white-space:nowrap;
        border: 1px solid #64992C;
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: bold;
        text-align: center;
        line-height: 25px;
        background-color: #64992C;
        color: #FFFFFF;
    }	

    .ci_geracao * {
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: normal;
    }

    .ci_geracao {
        border: 1px solid #B36D00;
    }	

    .ci_geracao input{
        border: 1px solid gray;
        padding-right: 3px;
    }	

    .ci_geracao caption {
        white-space:nowrap;
        border: 1px solid #B36D00;
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: bold;
        text-align: center;
        line-height: 25px;
        background-color: #B36D00;
        color: #FFFFFF;
    }	


    .ci_financeiro * {
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: normal;
    }

    .ci_financeiro {
        border: 1px solid #0B5394;
    }	

    .ci_financeiro input{
        border: 1px solid gray;
        padding-right: 3px;
    }	

    .ci_financeiro caption {
        white-space:nowrap;
        border: 1px solid #0B5394;
        font-family: Verdana, Tahoma, Arial;
        font-size: 10pt;
        font-weight: bold;
        text-align: center;
        line-height: 25px;
        background-color: #0B5394;
        color: #FFFFFF;
    }

    .destaca * {
        font-weight: bold;
    }
</style>
<h1 style="text-align:left;">
    Envio de Contribuição (Atrasada) referente à <? echo $NR_MES . "/" . $NR_ANO; ?><BR>
    Plano: <? echo $CD_PLANO; ?><BR>
    Empresa: <? echo $CD_EMPRESA; ?><BR>
</h1>

<table align="center" border="0" cellspacing="10" class="contribuicao_instituidor">
    <tr>
        <td valign="top">
<?php
$qt_total_anterior = $ar_contribuicao_atrasada_anterior['COBDL']['TOTAL'] +
    $ar_contribuicao_atrasada_anterior['COB1P']['TOTAL'] +
    $ar_contribuicao_atrasada_anterior['CODCC']['TOTAL'] +
    $ar_contribuicao_atrasada_anterior['COFOL']['TOTAL'] +
    $ar_contribuicao_atrasada_anterior['COFLT']['TOTAL'];
?>
            <table border="0" cellspacing="5" class="ci_geracao">
                <caption>Contribuições em Atraso Anterior</caption>
                <tr>
                    <td style="width: 180px;"></td>
                    <td align="center">Qtd</td>
                </tr>				
                <tr>
                    <td>Primeiro Pagamento</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada_anterior['COB1P']['TOTAL']; ?>" name="qt_1pg_ant" id="qt_1pg_ant" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>
                <tr>
                    <td>BDL</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada_anterior['COBDL']['TOTAL']; ?>" name="qt_bdl_ant" id="qt_bdl_ant" readonly style="text-align:right; width: 60px;">
                    </td>				
                </tr>				
                <tr>
                    <td>BCO</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada_anterior['CODCC']['TOTAL']; ?>" name="qt_bco_ant" id="qt_bco_ant" readonly style="text-align:right; width: 60px;">
                    </td>				
                </tr>
                <tr>
                    <td>Folha</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada_anterior['COFOL']['TOTAL']; ?>" name="qt_fol_ant" id="qt_fol_ant" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>					
                <tr>
                    <td>Folha Patroc</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada_anterior['COFLT']['TOTAL']; ?>" name="qt_flt_ant" id="qt_flt_ant" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>					
                <tr>
                    <td colspan="3"><hr></td>
                </tr>				
                <tr>
                    <td style="white-space:nowrap;">Total</td>
                    <td class="destaca">
                        <input type="text" value="<? echo $qt_total_anterior; ?>" name="qt_total_ant" id="qt_total_ant" readonly style="text-align:right; width: 60px;">
                    </td>					
                    <td style="font-size: 65%;"></td>					
                </tr>				
            </table>
        </td>

        <td valign="top">
<?php
$qt_total_email = $ar_contribuicao_atrasada['COBDL']['EMAIL'] +
    $ar_contribuicao_atrasada['COB1P']['EMAIL'] +
    $ar_contribuicao_atrasada['CODCC']['EMAIL'] +
    $ar_contribuicao_atrasada['COFOL']['EMAIL'] +
    $ar_contribuicao_atrasada['COFLT']['EMAIL'];

$qt_total = $ar_contribuicao_atrasada['COBDL']['TOTAL'] +
    $ar_contribuicao_atrasada['COB1P']['TOTAL'] +
    $ar_contribuicao_atrasada['CODCC']['TOTAL'] +
    $ar_contribuicao_atrasada['COFOL']['TOTAL'] +
    $ar_contribuicao_atrasada['COFLT']['TOTAL'];
?>
            <table border="0" cellspacing="5" class="ci_financeiro">
                <caption>Contribuições em Atraso</caption>
                <tr>
                    <td style="width: 180px;"></td>
                    <td align="center">Email</td>
                    <td align="center">Qtd</td>
                </tr>				
                <tr>
                    <td>Primeiro Pagamento</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COB1P']['EMAIL']; ?>" name="qt_1pg_email" id="qt_1pg_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COB1P']['TOTAL']; ?>" name="qt_1pg" id="qt_1pg" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>
                <tr>
                    <td>BDL</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COBDL']['EMAIL']; ?>" name="qt_bdl_email" id="qt_bdl_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COBDL']['TOTAL']; ?>" name="qt_bdl" id="qt_bdl" readonly style="text-align:right; width: 60px;">
                    </td>				
                </tr>				
                <tr>
                    <td>BCO</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['CODCC']['EMAIL']; ?>" name="qt_bco_email" id="qt_bco_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['CODCC']['TOTAL']; ?>" name="qt_bco" id="qt_bco" readonly style="text-align:right; width: 60px;">
                    </td>				
                </tr>
                <tr>
                    <td>Folha</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COFOL']['EMAIL']; ?>" name="qt_fol_email" id="qt_fol_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COFOL']['TOTAL']; ?>" name="qt_fol" id="qt_fol" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>					
                <tr>
                    <td>Folha Patroc</td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COFLT']['EMAIL']; ?>" name="qt_flt_email" id="qt_flt_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td>
                        <input type="text" value="<? echo $ar_contribuicao_atrasada['COFLT']['TOTAL']; ?>" name="qt_flt" id="qt_flt" readonly style="text-align:right; width: 60px;">
                    </td>					
                </tr>					
                <tr>
                    <td colspan="3"><hr></td>
                </tr>				
                <tr>
                    <td style="white-space:nowrap;">Total</td>
                    <td class="destaca">
                        <input type="text" value="<? echo $qt_total_email; ?>" name="qt_total_email" id="qt_total_email" readonly style="text-align:right; width: 60px;">
                    </td>
                    <td class="destaca">
                        <input type="text" value="<? echo $qt_total; ?>" name="qt_total" id="qt_total" readonly style="text-align:right; width: 60px;">
                    </td>					
                    <td style="font-size: 65%;"></td>					
                </tr>				
            </table>
        </td>
    </tr>
</table>
<?php
#### INCONSISTENCIAS ####
if (!$fl_enviado)
{
    if (intval($qt_total) != intval($qt_total_email))
    {
        $erro = "- Total com email (" . intval($qt_total_email) . ") é diferente do Total (" . intval($qt_total) . ") ";

        echo "
            <script>
                alert('" . $erro . "');
            </script>   
        ";

    }
    else
    {
        $erro = '';
    }
        echo "
					<center>
						<span class='label label-warning'>".$erro."</span>
						<BR>
						<center>
							<input type=\"button\" value=\"Listar sem email\" onclick=\"semEmail();\" class=\"btn btn-warning btn-mini\" style=\"width: 120px;\">
						</center>							
												
					</center>
				 ";
    
    

    if (intval($qt_total_email) == 0)
    {
        $erro = "- Não foi encontrado contribuições para enviar";
        echo "
					<center>
						<span class='label label-warning'>
							" . $erro . "  
						</span>
						<script>
							alert('INCONSISTÊNCIA');
						</script>							
						<br><br><br>
						<br><br><br>
					</center>
				 ";
        exit;
    }
	
	if ((!($fl_enviado AND $fl_gerado)) and ($fl_check_periodo == "N"))
	{
        $erro = "- Não foi encontrado contribuições para enviar";
        echo "
					<center>
						<span class='label label-warning'>
						- Envio somente a partir do dia 13/".str_pad(intval($NR_MES),2, "0", STR_PAD_LEFT)."/".$NR_ANO."
						</span>
						<script>
							alert('ATENÇÃO\\n\\nEnvio somente a partir do dia 13/".str_pad(intval($NR_MES),2, "0", STR_PAD_LEFT)."/".$NR_ANO."');
						</script>							
						<br><br><br>
						<br><br><br>
					</center>
				 ";
        exit;
	}	
}



#### BOTOES ####
$bt_gerar = false;
$bt_envia_email = false;
$bt_protocolo = false;

#echo "fl_enviado => $fl_enviado | fl_gerado => $fl_gerado <BR>";

if ($fl_enviado)
{
    $bt_gerar = false;
    $bt_envia_email = false;
}
elseif ($fl_gerado)
{
    $bt_gerar = false;
    $bt_envia_email = true;
}
else
{
    $bt_gerar = true;
    $bt_envia_email = false;
}

if($fl_enviado AND $fl_gerado)
{
    $bt_protocolo = true;
}

?>
<table border="0" align="center" cellspacing="20">
    <tr style="height: 30px;">
        <td style="<? echo ($bt_gerar == true ? "" : "display:none;"); ?>">
			<span class="label label-inverse" style="font-size: 14pt; line-height: 32px;">ATENÇÃO: Os e-mails só podem ser enviados 1 dia após a data de emissão no eletro.</span>
			<BR><BR>			
			<table border="0" align="center">
				<tr>
					<td>Dt Emissão Eletro:</td>
					<td><?php echo form_date("dt_emissao_eletro"); ?></td>
					<td><input type="button" value="Gerar" onclick="gerar();" class="btn btn-primary btn-small"></td>
				</tr>
			</table>
			<BR><BR>
			<BR><BR>
			<BR><BR>				
        </td>
        <td style="<? echo ($bt_envia_email == true ? "" : "display:none;"); ?>">
			<span class="label label-inverse" style="font-size: 14pt; line-height: 35px;">ATENÇÃO: Os e-mails só devem ser enviados 1 dia após a data de emissão no eletro.</span>
			<BR><BR>			
            <input type="button" value="Enviar Emails" onclick="enviarEmail();" class="botao_vermelho" style="width: 120px;">
        </td>	
        <td style="<? echo ($bt_protocolo == true ? "" : "display:none;"); ?>">
            <input type="button" value="Gerar Protocolo" onclick="gerar_protocolo();" class="botao" style="width: 120px;">
        </td>
    </tr>
</table>
<div style="text-align:center; width: 100%; <? echo ($bt_gerar == true ? "display:none;" : ""); ?>">
<?php
$body = array();
$head = array(
  'EMP/RE/SEQ',
  'Nome',
  'Forma',
  'Situação',
  'Dt Geração',
  ''
);

foreach ($ar_contribuicao_controle as $item)
{
    $body[] = array(
      $item["cd_empresa"] . "/" . $item["cd_registro_empregado"] . "/" . $item["seq_dependencia"],
      array($item["nome"], "text-align:left;"),
      array($item["ds_contribuicao_controle_tipo"], "text-align:left;"),
      ($item["fl_email_enviado"] == "S" ? '<span style="font-weight: bold; color: green;">Enviado</span>' : '<span style="font-weight: bold; color: blue;">Aguardando envio</span>'),
      $item["dt_geracao"],
	  ($item["fl_email_enviado"] == "N" ? '<a href="javascript:void(0)" onclick="excluir('.$item["cd_empresa"].', '.$item["cd_registro_empregado"].', '.$item["seq_dependencia"].', '.$item['nr_ano_competencia'].', '.$item['nr_mes_competencia'].', \''.$item['cd_contribuicao_controle_tipo'].'\')">[excluir]</a>' : '')
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
</div>
<BR><BR><BR>