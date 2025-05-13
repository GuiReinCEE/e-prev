<!DOCTYPE html>
<html lang="pt">
	<head>
	    <meta charset="utf-8">
	    <title>Campanha - Sinpro/RS Previdência</title>
	</head>
	<body>
		<div style="text-align:center;">
			<br/>
			<center>
				<table style="border:none; text-align:center; width:700px; background-color:#F5F4F5;">
					<tr>
						<td style="text-align:left; width:450px;">
							<div style="margin:5px;">
								<span  style="font-family:'Century Gothic','Gill Sans MT', Arial, Helvetica; font-weight: bold; color:#606060; font-size:22px;">
								Olá, <?= $row['PARTICIPANTE_NOME']['ds_linha'] ?>
								</span>
							</div>
						</td>
			            <td style="text-align:right; width:250px;">
			            	<img src="https://www.fcprev.com.br/temp/resources/images/campanha_aumento_contrib_inst/LOGO-SINPRO.png" alt="Logo SINPRO Previdência"/>
			            </td>
			        </tr>
			        <tr>
			            <td style="text-align:center;" colspan="2">
			            	<span style="font-family:'Century Gothic','Gill Sans MT', Arial, Helvetica; font-weight: bold; color:#606060; font-size: 38px; font-weight: bold;">
							ACUMULE
							</span>
						</td>
					</tr>
			        <tr>
			            <td style="text-align:center;" colspan="2">
			                <img src="https://www.fcprev.com.br/temp/index.php/campanha_aumento_contrib_inst/imagem_valor_final/<?= $row['SIMULA_SALDO_ACUMULADO_NOVO_C3']['vl_valor'] ?>" style="border:none;" alt="Resultado Simulado"/>
			            </td>
			        </tr>
			        <tr>
			            <td style="text-align:center;" colspan="2">
			            	<span style="font-family:'Century Gothic','Gill Sans MT', Arial, Helvetica; font-weight: bold; color:#606060; font-size: 38px; font-weight: bold;">
							EM SUA POUPANÇA<br/>PREVIDENCIÁRIA
							</span>
						</td>
					</tr>
			        <tr>
			            <td style="text-align:center;" colspan="2">
			            	<img src="https://www.fcprev.com.br/temp/index.php/campanha_aumento_contrib_inst/imagem_valor_sugerido/<?= $row['SIMULA_CONTRIB_NOVO_C3']['vl_valor'].'/'.$row['SIMULA_RENTABILIDADE_C3']['vl_valor'].'/'.$row['BEN_DATA_SIMULACAO']['ds_linha'].'/'.intval($row['SIMULA_TEMPO_C3']['vl_valor']) ?>" style="border:none; width:700px;" alt="Valor Sugerido para Contribuição"/>
			            </td>
			        </tr>
					<tr>
			            <td style="text-align:center; background-color:#EBE816" colspan="2">
			            	<span style="font-family:'Century Gothic','Gill Sans MT', Arial, Helvetica; font-weight: bold; color:#606060; font-size: 20px;">
		                        Aproveite! <b><a href="https://www.fundacaoceee.com.br/auto_atendimento_contrib_proga.php?_p=<?=trim($cripto_re)?>&_valor=<?=$row['SIMULA_CONTRIB_NOVO_C3']['vl_valor']?>" style="font-weight: bold;">CLIQUE AQUI</a></b> para aumentar sua contribuição<br/>e investir mais no seu futuro.<br/>
		                        Ele depende das decisões que você tomar hoje.
			                </span>
			            </td>
			        </tr>
			        <tr>
			            <td style="text-align:center;" colspan="2">
			            	<br/>
			            	<span style="font-family:'Century Gothic','Gill Sans MT', Arial, Helvetica; font-weight: bold; color:#606060; font-size: 20px;">
		                        Outras Informações:
		                        <br/>
		                        <span style="font-weight: bold;">
		                        	Envie mensagem pelo site. <a href="https://www.fundacaofamiliaprevidencia.com.br/index.php/contact/">Clique aqui</a>.
		                        	<br/>
		                        	Ou ligue: 0800 510 2596 (de fixo) | (51) 3027 1221 (de celular)
		                        </span>
			                </span>
			                <br/><br/>
			                <img src="https://www.fcprev.com.br/temp/resources/images/campanha_aumento_contrib_inst/LOGO-FCEEE.png" style="border:none; width:250px;" alt="Logo Fundação CEEE - Previdência Privada"/>
			                <br/><br/>
			            </td>
			        </tr>
			        <tr>
			       		<td style="text-align:center;" colspan="2">
			       			<br/><br/>
			       			<img src="https://www.fundacaofamiliaprevidencia.com.br/meu_retrato/img/bannerapp.png" border="0" usemap="#bannerapp">
							<map name = "bannerapp">
								<area shape = "rect" coords = "107, 107, 214, 139" alt="Download Android" title="Download Android" target="_blank" href="https://play.google.com/store/apps/details?id=com.graycompany.fundacaoceee"/>
								<area shape = "rect" coords = "243, 107, 350, 139" alt="Download iOS" title="Download iOS" target="_blank" href="https://itunes.apple.com/us/app/meu-plano/id1279019114"/>
							</map>

							<br/><br/>
			       		</td>
			       </tr>
				</table>
			</center>
		</div>
	</body>
</html>