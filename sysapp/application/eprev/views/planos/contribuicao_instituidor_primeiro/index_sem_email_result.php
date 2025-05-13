<?php
	#echo "<PRE>".print_r($ar_lista,true)."</PRE>";exit;
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
	Envio de Contribuição (Primeiro Pagamento) referente à <? echo $NR_MES."/".$NR_ANO;?><BR>
	Plano: <? echo $CD_PLANO; ?><BR>
	Empresa: <? echo $CD_EMPRESA; ?><BR>
</h1>
<h1 style='font-family: Calibri, Arial; font-size: 17pt; color:red;'>
	Inscritos sem Email
</h1>
<table border="0" align="center" cellspacing="20">
	<tr style="height: 30px;">
		<td>
			<input type="button" value="Enviar lista para Cadastro (GAP)" onclick="enviarEmailCadastro();" class="botao_disabled" style="width: 200px;">
		</td>		
	</tr>
</table>
<?php
$body=array();
$head = array( 
	'EMP/RE/SEQ',  
	'Nome',
	'Forma'
);

foreach($ar_lista as $item)
{
	$body[] = array(
	    $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		array($item["forma_pagamento"],"text-align:left;")
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>