<?php
set_title('Área de Trabalho');
$this->load->view('header');
?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>skins/skin002/css/workspace.css" />

<img id="IR2017" src="<?php echo base_url(); ?>skins/skin002/img/workspace/IRPF-PROGRAMA.png" border="0" usemap="#map" style="display:none; position: absolute; top: 20%; left: 15%; ">
<map name="map">
    <area shape="rect" coords="50,80,360,126" href="https://www.e-prev.com.br/cieprev/up/irpf/IRPF2022Win32v1.0.exe" title="Download do programa">
    <area shape="rect" coords="384,4,414,33" href="javascript: void(0);" onclick="document.getElementById('IR2017').style.display='none';" title="Clique para fechar">
</map>

<!--
<img id="banner_evento" src="<?php echo base_url(); ?>skins/skin002/img/workspace/banner_evento.png" border="0" usemap="#map" style="position: absolute; top: 20%; left: 40%;">
<map name="map">
    <area shape="rect" coords="357,0,379,22" href="javascript: void(0);" onclick="document.getElementById('banner_evento').style.display='none';" title="Clique para fechar">
    <area shape="rect" coords="0,22,379,400" href="http://www.fundacaoceee.com.br/inscricao_evento.php?id=126" title="Clique para se inscrever">
</map>
-->

<?php
if ($fl_exibe_banner_pesquisa_ti == "S")
{
?>
<img id="banner_pesquisa_ti" src="<?php echo base_url(); ?>skins/skin002/img/workspace/psti.png" border="0" usemap="#map" style="position: absolute; top: 20%; left: 40%;">
<map name="map">
    <area shape="rect" coords="475,0,502,22" href="javascript: void(0);" onclick="document.getElementById('banner_pesquisa_ti').style.display='none';" title="Clique para fechar">
    <area shape="rect" coords="0,53,502,281" href="<?php echo base_url_eprev(); ?>resp_enquetes_capa.php?c=507" title="Clique para responder">
</map>
<?php
}
?>




<table align="center" border="0" bgcolor="white">
    <tr>
        <!-- GESTÃO -->
        <td valign="top" class="work_gestao">
            <ul>
                <li>
                    <a href="<?= site_url("ecrm/intranet/pagina/GQ") ?>">Gestão da Qualidade</a>
                </li>
                <li>
                    <a href="<?= site_url("ecrm/intranet/pagina/PE") ?>">Planejamento Estratégico</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/controle_igp/apresentacao') ?>" target="_blank">Indicador IGP</a>
            
                </li>
                <li>
                    <a href="<?= site_url('ecrm/intranet/pagina/PODER/10532') ?>">PODER</a>
                </li>
                <li>
                    <a href="<?= site_url('ecrm/intranet/pagina/PUB/10295') ?>">Orçamento</a>
                </li>
                <!--
                <li>
                    <a href="<?= site_url('gestao/sumula/consulta') ?>">Súmulas DE</a>
                </li>
                -->
                <li>
                    <a href="<?= site_url("ecrm/intranet/pagina/SUM") ?>">Súmulas</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/circular') ?>">Circulares</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/reuniao_sistema_gestao/reuniao_gestao') ?>">Reunião de Gestão</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/pendencia_gestao') ?>">Pendências de Gestão</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/formulario/consulta') ?>">Formulários</a>
                </li>
                <li>
                    <a href="<?= base_url('up/par_20230320.pdf') ?>" target="_blank">Norma e Manual do Processo de Apuração de Responsabilidades</a>
                </li>	
                <!--			
                <li><?= anchor_file('srvseguranca\Certificado_ISO\Certificado_ISO.pdf', 'Certificado do Sistema de Gestão') ?></li>
                
                <li>
                    <a href="<?= site_url('gestao/codigo_etica/get') ?>" target="_blank">Código de Ética</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/estatuto/get') ?>" target="_blank">Estatuto</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/regimento_interno') ?>">Regimentos Internos</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/regulamento') ?>">Regulamentos</a>
                </li>
                <li>
                    <a href="<?= site_url('gestao/politica') ?>">Políticas</a>
                </li>
                -->
                <li>
                    <a href="<?= site_url("ecrm/intranet/pagina/INST") ?>">Instrumentos Normativos</a>
                </li>
                
                <!--
                <li>
                    <a href="https://unio.pfmconsultoria.com.br/" target="_blank">Unio - Gestão de Riscos e Controles Internos</a>
                </li>
                -->
                <li>
                    <a href="https://fprev.com.br/sa" target="_blank">S.A. (Interact) - PRODUÇÃO</a>
                </li>
                <!--
                <li>
                    <a href="https://fprev.com.br/sa?i=hml" target="_blank">S.A. (Interact) - HOMOLOGAÇÃO</a>
                </li>
                -->
                <li>
                    <a href="<?= site_url('ecrm/intranet/pagina/CIPA/10593') ?>">CIPA</a>
                </li>
            </ul>

            <label>Comitês</label>
            <ul>
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/CRQC"); ?>">Com. Integrado de Riscos, Controles e Qualidade</a></li>
                <li style="white-space:nowrap;"><a href="<?php echo site_url("ecrm/intranet/pagina/CCI"); ?>">Com. Consultivo de Investimentos</a></li>
                <!--
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/CEA"); ?>">Comitê de Educação Ambiental</a></li>
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/CP"); ?>">Comitê Previdenciário</a></li>
                -->
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/CAP"); ?>">Com. de Acompanhamento do Plano</a></li>
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/CE"); ?>">Com. de Expansão</a></li>
            </ul>   
            
        </td>
        <!-- INTRANET -->
        <td valign="top" class="work_intranet">
            <label>Recursos</label>
            <ul>
                <li><a href="<?php echo site_url("ecrm/intranet/pagina/GCM/10560"); ?>">Regulamento Viagem dos Sonhos</a></li>
                <li><a href="<?php echo site_url("ecrm/informativo_cenario_legal/consulta_normativo"); ?>">Cenário Legal</a></li>
                <li><a href="<?php echo site_url("clicksign/clicksign_documento"); ?>">Assinatura de Documentos</a></li>
				<!--<li><a href="#" onclick="openSisAssinatura();">Assinatura de Documentos</a></li>-->
                <!--<li><a href="https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/" target="_blank">Assinatura de Documentos</a></li>-->
				
				<li><a href="https://www.ffprh.com.br" target="_blank">Meu Portal RH</a></li>
				
                <li><a href="https://fprev.com.br/mb/" target="_blank">Simuladores Comercial dos Planos</a></li>
                <!--
                <li><a href="https://www.e-prev.com.br/clipping/publicar.html" target="_blank">Clipping Diário</a></li>
                -->

                <li><a href="<?php echo site_url("intranet/biblioteca_multimidia"); ?>">Biblioteca Multimídia</a></li>				
                

                <!--<li><a href="http://www.receita.fazenda.gov.br/Aplicacoes/ATRJO/Simulador/simulador.asp?tipoSimulador=M_Prox" target="_blank">Simulador IRPF</a></li>-->
                <!--<li><a href="https://www27.receita.fazenda.gov.br/simulador-irpf/" target="_blank">Simulador IRPF</a></li>-->
                <li><a href="#" onclick="document.getElementById('IR2017').style.display='';">Declaração do IRPF 2022</a> <!--<img src="<?php echo base_url(); ?>skins/skin002/img/workspace/pending.gif" border="0"></li>-->
				<!--<li><a href="<?php echo site_url("copa/copa/"); ?>">Bolão Copa do Mundo 2018</a></li>-->

                <li><a href="https://portalamericantur.com.br/connect/login/default.aspx" target="_blank">AmericanTur</a></li>  
            </ul>

            <label>Assessorias e Gerências</label>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">
                        <ul>
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/AI"); ?>">AI</a></li>
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/GC"); ?>">GC</a></li>
                            <!--<li><a href="<?php echo site_url("ecrm/intranet/pagina/GCM"); ?>">GCM</a></li>-->
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/GN"); ?>">GN</a></li>
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/GRSC"); ?>">GRSC</a></li>
                        </ul>
                    </td>
                    <td valign="top">
                        <ul>
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/GFC"); ?>">GFC</a></li>
							<li><a href="<?php echo site_url("ecrm/intranet/pagina/GIN"); ?>">GIN</a></li>	
							<li><a href="<?php echo site_url("ecrm/intranet/pagina/GJ"); ?>">GJ</a></li>
							<li><a href="<?php echo site_url("ecrm/intranet/pagina/GAP."); ?>">GAP</a></li>
                            <!--<li><a href="<?php echo site_url("ecrm/intranet/pagina/GRC"); ?>">GRC</a></li>-->
                        </ul>
                    </td>
                    <td valign="top">
                        <ul>
							<li><a href="<?php echo site_url("ecrm/intranet/pagina/GTI"); ?>">GTI</a></li>	
							<li><a href="<?php echo site_url("ecrm/intranet/pagina/AE"); ?>">Aeletro</a></li>	
                            <li><a href="<?php echo site_url("ecrm/intranet/pagina/PUB"); ?>">Gestão</a></li>   
                        </ul>
                    </td>
                </tr>
            </table>

            <label>Equipes</label>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">
                        <ul>
							<li><a href="<?php echo site_url("cadastro/equipe/index/AI"); ?>">AI</a></li>
                            <li><a href="<?php echo site_url("cadastro/equipe/index/GC"); ?>">GC</a></li>
                            <!--<li><a href="<?php echo site_url("cadastro/equipe/index/GCM"); ?>">GCM</a></li>	-->
                            <li><a href="<?php echo site_url("cadastro/equipe/index/GN"); ?>">GN</a></li>
                            <li><a href="<?php echo site_url("cadastro/equipe/index/GRSC"); ?>">GRSC</a></li>
                        </ul>
                    </td>
                    <td valign="top">
                        <ul>	
                            <li><a href="<?php echo site_url("cadastro/equipe/index/GFC"); ?>">GFC</a></li>
							<li><a href="<?php echo site_url("cadastro/equipe/index/GIN"); ?>">GIN</a></li>	
							<li><a href="<?php echo site_url("cadastro/equipe/index/GJ"); ?>">GJ</a></li>
							<li><a href="<?php echo site_url("cadastro/equipe/index/GAP."); ?>">GAP</a></li>	
                            <!--<li><a href="<?php echo site_url("cadastro/equipe/index/GRC"); ?>">GRC</a></li> -->
                        </ul>
                    </td>
                    <td valign="top">
                        <ul>
							<li><a href="<?php echo site_url("cadastro/equipe/index/GTI"); ?>">GTI</a></li>	
							<li><a href="<?php echo site_url("cadastro/equipe/index/DE"); ?>">Diretoria</a></li>	
                        </ul>
                    </td>
                </tr>
            </table>			
        </td>

        <!-- E-PREV -->
        <td valign="top" class="work_eprev">
            <label>Calendário</label>
            <ul>
                <li><a href="<?php echo site_url("calendario"); ?>">Feriados e Folgas</a></li>
                <li><a href="<?php echo site_url("calendario/index/0/P"); ?>">Pagamentos Colaboradores</a></li>
                <li><a href="<?php echo site_url("calendario/index/0/E"); ?>">Eventos</a></li>
                <li><a href="<?php echo site_url("calendario/index/0/R"); ?>">Reuniões dos Colegiados</a></li>
            </ul>		
            <label>Meu Trabalho</label>
            <ul>
				<li><a href="<?php echo site_url("atividade/pendencia_minha"); ?>">Minhas Pendências</a></li>
                <li><a href="<?php echo site_url("atividade/minhas"); ?>">Minhas Atividades</a></li>
				<?php if($this->session->userdata('divisao_ant') == 'GI') {?>
					<li><a href="<?php echo site_url("atividade/tarefa"); ?>">Minhas Tarefas</a></li>
				<?php } ?>	
                <li><a href="<?php echo eprev_url(); ?>avaliacao.php">Minhas Avaliações</a></li>
                <li><a href="<?php echo site_url("servico/contracheque"); ?>">Meu Contracheque</a></li>
                <li><a href="<?php echo site_url("servico/meu_extrato"); ?>">Meu Extrato</a></li>
                <li><a href="https://webmail.eletroceee.com.br" target="_blank">Meu Webmail</a></li>
            </ul>

            <label>Minha Área</label>
            <ul>
                <li><a href="<?php echo site_url("indicador/apresentacao/index/G"); ?>">Indicadores</a></li>
                <li><a href="<?php echo site_url("gestao/nc"); ?>">Não Conformidades</a></li>
                <li><a href="<?php echo site_url("gestao/pendencia_gestao/index/1"); ?>">Pendência - Qualidade</a></li>
                <li><a href="<?php echo site_url("gestao/pendencia_gestao/index/2"); ?>">Pendência - Súmula</a></li>
                <?php if($this->session->userdata('divisao_ant') == 'GI') {?>
                    <li><a href="<?php echo site_url("atividade/atividade_cronograma/index/0"); ?>">Cronograma - Prioridades</a></li>
                    <li><a href="<?php echo site_url("atividade/info_cronograma"); ?>">Cronograma - Analistas</a></li>
                <?php } ?>
				
                <?php if($this->session->userdata('divisao') == 'GIN') {?>
                    <li><a href="<?php echo site_url("atividade/cronograma_investimento"); ?>">Cronograma</a></li>
                    <li><a href="<?php echo site_url("gestao/caderno_cci"); ?>">Caderno CCI</a></li>
                <?php } ?>				
            </ul>
        </td>

        <!-- INTERNET -->
        <td valign="top" class="work_internet">
            <label>Nossos sites</label>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">
                        <ul>
                            <li><a href="http://www.fundacaoceee.com.br" target="_blank">Fundação Família Previdência</a></li>
                            <li><a href="http://www.ceeeprev.com.br/" target="_blank">CeeePrev</a></li>
                            <li><a href="http://www.sengeprevidencia.com.br" target="_blank">SENGE Previdência</a></li>
							<li><a href="http://www.familiaprevidencia.com.br" target="_blank">FAMÍLIA PREVIDÊNCIA Associativo</a></li>
                            <li><a href="http://www.inpelprev.com.br/" target="_blank">FAMÍLIA PREVIDÊNCIA Corporativo</a></li>
                            <li><a href="https://www.fundacaofamiliaprevidencia.com.br/index.php/prefeitura-nova-previdencia/" target="_blank">FAMÍLIA PREVIDÊNCIA Municípios</a></li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li><a href="https://www.fundacaoceee.com.br/unico_rge/" target="_blank">Plano I RGE</a></li>
							<li><a href="https://www.fundacaoceee.com.br/unico_aessul/" target="_blank">Plano II RGE</a></li>
                            <li><a href="https://www.fundacaoceee.com.br/unico_ceee/" target="_blank">Plano Único CEEE</a></li>
                            <li><a href="http://www.ceranprev.com.br/" target="_blank">Ceran Prev</a></li>
                            <li><a href="http://www.fozdochapecoprev.com.br/" target="_blank">Foz do Chapecó Prev</a></li>
							<li><a href="http://www.crmprev.com.br/" target="_blank">CRM Prev</a></li>
                        </ul>
                    </td>
                </tr>
            </table>


            <label>Links corporativos</label>
            <ul>
                <li><a href="http://www.abrapp.org.br" target="_blank">ABRAPP</a></li>
                <li><a href="http://www.anapar.com.br" target="_blank">ANAPAR</a></li>
                <li><a href="http://www.banrisul.com.br" target="_blank">Banrisul</a></li>
                <li><a href="http://www.previc.gov.br" target="_blank">PREVIC</a></li>
            </ul>

            <label>Patrocinadores/Instituidores</label>
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="top">
                        <ul>
                            <li><a href="http://www.ceee.com.br/grupo" target="_blank"> Grupo CEEE</a></li>
                            <li><a href="http://www.crm.rs.gov.br" target="_blank">CRM</a></li>
                            <li><a href="http://www.rgesul.com.br" target="_blank">RGE SUL</a></li>									
                            <li><a href="http://www.rge-rs.com.br" target="_blank">RGE</a></li>
                            <li><a href="http://www.inpel.com.br" target="_blank">INPEL</a></li>

                            <li><a href="http://www.ceran.com.br/" target="_blank">Ceran</a></li>
                            <li><a href="http://www.fozdochapeco.com.br/" target="_blank">Foz do Chapecó</a></li>
                        </ul>
                    </td>
                    <td>
                        <ul>
                            <li><a href="http://www.senge.org.br" target="_blank">SENGE</a></li>
                            <li><a href="http://www.sinpro-rs.org.br" target="_blank">SINPRO</a></li>
                            <li><a href="http://www.sintaers.com.br" target="_blank">SINTAE</a></li>
                            <li><a href="http://www.afceee.com.br" target="_blank">AFCEEE</a></li>
                            <li><a href="http://www.sintee.com.br" target="_blank">SINTEE</a></li>
                            <li><a href="http://www.sintepvales.org.br" target="_blank">SINTEP VALES</a></li>
                            <li><a href="http://sintec-rs.com.br" target="_blank">SINTEC</a></li>
                            <li><a href="http://www.tcheprevidencia.com.br" target="_blank">TCHÊ PREVIDÊNCIA</a></li>
                            <li><a href="http://www.seprorgs.org.br" target="_blank">SEPRORGS</a></li>

                            <li><a href="https://www.abrhrs.org.br/" target="_blank">ABRHRS</a></li>
                            <li><a href="#" target="_blank">ADJORI</a></li>
                            <li><a href="http://www.sindha.org.br/" target="_blank">SINDHA</a></li>
                            <li><a href="http://www.fundacaoceee.com.br" target="_blank">FFP</a></li>




                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<script>
	function fecharDialogoPopup()
	{
		$("#dialogoPopup").hide();
	}							
</script>
<div style="position: absolute; top: 100px; left: 250px; display:none;" id="dialogoPopup">
	<img src="<?php echo base_url(); ?>img/popup_dialogo_2012.png" border="0" id="dialogoPopupImg" usemap="#dialogoPopupImgMap">
	<map name="dialogoPopupImgMap">
		<area shape="rect" coords="49,294,230,335" href="http://www.fundacaoceee.com.br/dialogo_4/inscricao.php" title="Clique aqui para fazer sua inscrição">
		<area shape="rect" coords="254,10,270,26" href="javascript: fecharDialogoPopup();" title="Clique aqui para fechar">
	</map>						
</div>
<form id="formSistemaAssinatura" action="https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/" method="post" target="_blank" style="display:none;">
  <input type="text" id="user_token" name="user_token" value="<?php echo md5($this->session->userdata('usuario').date("Ymd")); ?>" style="width: 100%;">
</form>
<script>
	function openSisAssinatura()
	{
		$('#formSistemaAssinatura').submit();
	}
</script>

<script type="text/javascript">
	$(function(){
		//$("#dialogoPopup").show();
		if ($.browser.msie) 
		{
			//$(document).pngFix();
		}
	});
	
	$(window).scroll(function() {
		if($(this).scrollTop() > 100)
		{
			$("#dialogoPopup").stop().animate({"top":($(this).scrollTop() + 40) + "px"}, 1000);
		}
		else
		{
			$("#dialogoPopup").stop().animate({"top":"100px"}, 1000);
		}
	});
</script>	

<?php
/*
  <!--
  <div style="position: absolute; top: 10px; left: 65px;" id="popup_comite_etica">
  <a href="javascript: void(0);" onclick="$('#popup_comite_etica').hide();" title="Clique para fechar"><img src="<?php echo base_url();?>img/popup_comite_etica.png" border="0"></a>
  </div>
  -->
 */

$this->load->view('footer_interna');
?>