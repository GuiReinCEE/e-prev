<?php
class contrato_notificacao extends Controller
{
    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model("projetos/contrato_model");
        $this->load->model('projetos/correspondencias_model');
    }


    function index()
    {

        $meses = array("janeiro","fevereiro","março","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");
        
        $result = null;
        $args = Array();
        $data = Array();

		
        #### GFC - CONTRATOS ####
        $ar_area[] = "GFC";
        $ar_contrato["GFC"][] = array(62); #SELTEC SISTEMAS DE SEGURANCA E SERVICOS LTDA
        $ar_contrato["GFC"][] = array(427); #METROFILE BRASIL GESTAO DA INFORMACAO LTDA.
        $ar_contrato["GFC"][] = array(389); #TIME TECNOLOGIA E DESENVOLVIMENTO DE SISTEMAS LTDA
        $ar_contrato["GFC"][] = array(162); #EMPRESA BRASILEIRA DE CORREIOS E TELEGRAFOS
        $ar_contrato["GFC"][] = array(54); #SERASA S/A
        $ar_contrato["GFC"][] = array(268); #SYNCHRO SISTEMAS DE INFORMAÇAO LTDA
        $ar_contrato["GFC"][] = array(94); #PORTO ALEGRE TERCEIRO TABELIONATO
        $ar_contrato["GFC"][] = array(163); #REDE & IMAGEM TECNOLOGIAS CONSULTORIA DE SISTEMAS LTDA - EPP
        $ar_contrato["GFC"][] = array(118); #ICATU SEGUROS S/A
		
        #### GCM - CONTRATOS ####
        $ar_area[] = "GCM";
        $ar_contrato["GCM"][] = array(110,111); #AMAURI BUENO CORRETORA DE SEGUROS LTDA
        $ar_contrato["GCM"][] = array(144); #TICKET SOLUCOES HDFGT S/A
        $ar_contrato["GCM"][] = array(507); #CURUPIRA S.A - TAKE
        $ar_contrato["GCM"][] = array(289); #PUBLICA COMUNICAÇAO LTDA - MOOVE
        $ar_contrato["GCM"][] = array(727); #JANNER LEAL SOCIEDADE INDIVIDUAL DE ADVOCACIA
        $ar_contrato["GCM"][] = array(629); #SERVICO FEDERAL DE PROCESSAMENTO DE DADOS (SERPRO)
        $ar_contrato["GCM"][] = array(447); #TLD TELEDATA TECNOLOGIA EM CONECTIVIDADE LTDA
        $ar_contrato["GCM"][] = array(506); #SEGMENTO PESQUISAS E ANÁLISE DE MERCADO LTDA

        #### GTI - CONTRATOS ####
        $ar_area[] = "GTI";
        $ar_contrato["GTI"][] = array(30); #IMMEDIATE CONSULTORIAS E SISTEMAS LTDA
        $ar_contrato["GTI"][] = array(58); #PROPUS INFORMÁTICA LTDA
        $ar_contrato["GTI"][] = array(418); #CLICKSIGN GESTAO DE DOCUMENTOS S/A
        $ar_contrato["GTI"][] = array(271); #FBT TECNOLOGIA LTDA

        #### GRC - CONTRATOS ####
        $ar_area[] = "GRC";
        $ar_contrato["GRC"][] = array(768,769); #SCALZILLI, ALTHAUS, CHIMELO & SPOHR ADVOGADOS
        $ar_contrato["GRC"][] = array(767); #DE BERNT ENTSCHEV HUMAN CAPITAL LTDA
        $ar_contrato["GRC"][] = array(449,450); #GI GROUP BRASIL RECURSOS HUMANOS LTDA
        $ar_contrato["GRC"][] = array(332); #INTEGRAR/RS - ASSOCIAÇAO DE INTEGRAÇAO EMPRESA ESCOLA
        $ar_contrato["GRC"][] = array(331); #AMERICAN TOUR AGENCIA DE VIAGENS E TURISMO LTDA
        $ar_contrato["GRC"][] = array(309); #ATIVA MEDICINA E SEGURANÇA DO TRABALHO LTDA
        $ar_contrato["GRC"][] = array(396); #PHF AUDITORES INDEPENDENTES S/S
        $ar_contrato["GRC"][] = array(729); #PLACE CONSULTORIA E RH LTDA
        $ar_contrato["GRC"][] = array(429); #BEECORP BEM ESTAR CORPORATIVO LTDA
        $ar_contrato["GRC"][] = array(36,37); #TICKET SERVIÇOS
		$ar_contrato["GRC"][] = array(431); #SENIOR SISTEMAS S/A
		$ar_contrato["GRC"][] = array(38,39,40,41,42); #SENIOR SISTEMAS S A
		$ar_contrato["GRC"][] = array(32,417); #UNIMED PORTO ALEGRE SOC COOP TRAB MÉDICO LTDA
		$ar_contrato["GRC"][] = array(454); #ASSOCIACAO BRASILEIRA DE RH
		$ar_contrato["GRC"][] = array(31,436); #UNIODONTO PORTO ALEGRE COOP ODONTOLOGICA
		$ar_contrato["GRC"][] = array(146,690); #CIEE- CENTRO DE INTEGRAÇÃO EMPRESA-ESCOLA RS

        #### GC - CONTRATOS ####
        $ar_area[] = "GC";
        $ar_contrato["GC"][] = array(89); #SINQIA TECNOLOGIA LTDA
        $ar_contrato["GC"][] = array(776); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(220); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(439); #INTERACT SOLUTIONS LTDA
	
        #### GAP - CONTRATOS ####
        $ar_area[] = "GAP";
        $ar_contrato["GAP"][] = array(448); #ANAPAR ASSOC. NACIONAL DE PARTICIPANTES DE FUNDOS DE PENSAO
        $ar_contrato["GAP"][] = array(84,329); #JESSE MONTELLO SERV TEC ATUARIA E ECON.
        $ar_contrato["GAP"][] = array(437); #ASSOC DOS TECN DAS EMPR ENERGIA ELETRICA DO RS
        $ar_contrato["GAP"][] = array(424); #ASSOC DOS APOSENTADOS E PENSIONISTAS ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(434); #UNIPROCEEE UNIAO DOS PROFISSIONAIS LIBERAIS DA CEEE
        $ar_contrato["GAP"][] = array(426); #SINDICATO DOS TECNICOS INDUSTRIAIS
        $ar_contrato["GAP"][] = array(442); #SENGE
        $ar_contrato["GAP"][] = array(416); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #SINDICATO TRABALHADORES NA IND ENERGIA ELETRICA NO EST RS
        $ar_contrato["GAP"][] = array(435); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
        $ar_contrato["GAP"][] = array(438,512); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
     
        #### GJ - CONTRATOS ####
        $ar_area[] = "GJ";
        $ar_contrato["GJ"][] = array(131); #ASTREA SOFTWARE LTDA
        $ar_contrato["GJ"][] = array(132); #KTREE PENSO TECNOLOGIA DA INFORMAÇAO LTDA
        $ar_contrato["GJ"][] = array(330); #CENCO E CENCO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(103,149,168,230,370); #FONSECA SALERNO, TRAVERSO E KVITKO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(127,128,129,130,135,221,222,456,668); #BOTHOME ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(133,508); #SOUZA, CESCON, BARRIEU & FLESCH SOCIEDADE DE ADVOGADOS
        $ar_contrato["GJ"][] = array(140,251); #BOCATER, CAMARGO, COSTA E SILVA, RODRIGUES ADV. ASSOCIADOS.
        $ar_contrato["GJ"][] = array(119); #RENE BERGMANN AVILA ADVOGADOS
        $ar_contrato["GJ"][] = array(430,504); #SPEROTTO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(126); #COLOMBO ADVOCACIA E CONSULTORIA JURIDICA S/S
        $ar_contrato["GJ"][] = array(121,452); #REIS, TORRES E FLORÊNCIO ASSOCIADOS
        $ar_contrato["GJ"][] = array(104,257,383); #BARCELLOS ADVOCACIA EMPRESARIAL
        $ar_contrato["GJ"][] = array(687); #REDE SOLUÇÕES DIGITAIS LTDA
        $ar_contrato["GJ"][] = array(138,148,171,256,382,505); #MILESKI ADVOGADOS
        $ar_contrato["GJ"][] = array(373,432); #SOUTO CORREA CESA LUMMERTZ & AMARAL ADVOGADOS
        $ar_contrato["GJ"][] = array(628); #RECURSAL PROCESSAMENTO DE DADOS LTDA
        $ar_contrato["GJ"][] = array(122,143); #ELIANE SCHIRMER ANTUNES
        $ar_contrato["GJ"][] = array(120); #ZAMARI E MARCONDES ADVOGADOS ASSOCIADOS S/C
        $ar_contrato["GJ"][] = array(123,124,125,139,166); #JUCHEM & ADVOGADOS ASSOCIADOS S/C
       
        
        $DEBUG = FALSE;
        
        #### DEBUG ####
		#$ar_area = array('GTI','GCM');
		#$ar_contrato['GTI'][] = array(627); #MACIEL
        #$ar_contrato['GTI'][] = array(271);
        #$ar_contrato['GCM'][] = array(103,149,168,230,370);
        ##$ar_contrato[] = array(122,143);
        #
		#echo "<PRE>"; print_r($ar_area); 
		#echo "<PRE>"; print_r($ar_contrato); 
		
        #echo "<PRE>";
        #echo "<HR>";
		$nr_conta = 1;
        foreach($ar_area as $area)
        {		
			#echo "<hr>".$area."<hr>";
			
			
			
			#$nr_conta = 1;
			foreach($ar_contrato[$area] as $ar_item)
			{
				$args['ar_seq_contrato'] = array($ar_item[0]);
				
				#echo "<HR>";
				#echo $nr_conta."<BR>";
				#echo "CT-".$ar_item[0]." | ".implode(",",$ar_item)."<BR>";				
				echo $nr_conta.",".$area.",".$ar_item[0].",".implode("|",$ar_item);
				
				$this->contrato_model->listarContratoNoticacaoLGPD($result, $args);
				$ar_data_contrato = $result->row_array();
							
				#print_r($ar_item); #exit;
				#print_r($ar_data_contrato); exit;
				
				$args['ar_seq_contrato'] = $ar_item;
				$this->contrato_model->getContatoNoticacaoLGPD($result, $args);
				$ar_contato = $result->result_array();        
				
				$ar_email = Array();
				foreach($ar_contato as $e)
				{
					if(trim($e["email"]) != "")
					{
						$ar_email[] = $e["email"];
					}
				}
				
				#print_r($ar_contato);
				echo ",".$ar_data_contrato["ds_empresa"].",".$ar_data_contrato["nm_fantasia"].",".implode(";",$ar_email); 
				echo "<BR>";
				#echo "<HR>";

				
				$nr_conta++;
			}
			
        }
        exit; ######################
            
    }


   function lista_empresas()
    {

        $meses = array("janeiro","fevereiro","março","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");
        
        $result = null;
        $args = Array();
        $data = Array();

		
        #### GFC - CONTRATOS ####
        $ar_area[] = "GFC";
        $ar_contrato["GFC"][] = array(62); #SELTEC SISTEMAS DE SEGURANCA E SERVICOS LTDA
        $ar_contrato["GFC"][] = array(427); #METROFILE BRASIL GESTAO DA INFORMACAO LTDA.
        $ar_contrato["GFC"][] = array(389); #TIME TECNOLOGIA E DESENVOLVIMENTO DE SISTEMAS LTDA
        $ar_contrato["GFC"][] = array(162); #EMPRESA BRASILEIRA DE CORREIOS E TELEGRAFOS
        $ar_contrato["GFC"][] = array(54); #SERASA S/A
        $ar_contrato["GFC"][] = array(268); #SYNCHRO SISTEMAS DE INFORMAÇAO LTDA
        $ar_contrato["GFC"][] = array(94); #PORTO ALEGRE TERCEIRO TABELIONATO
        $ar_contrato["GFC"][] = array(163); #REDE & IMAGEM TECNOLOGIAS CONSULTORIA DE SISTEMAS LTDA - EPP
        $ar_contrato["GFC"][] = array(118); #ICATU SEGUROS S/A
		
        #### GCM - CONTRATOS ####
        $ar_area[] = "GCM";
        $ar_contrato["GCM"][] = array(110,111); #AMAURI BUENO CORRETORA DE SEGUROS LTDA
        $ar_contrato["GCM"][] = array(144); #TICKET SOLUCOES HDFGT S/A
        $ar_contrato["GCM"][] = array(507); #CURUPIRA S.A - TAKE
        $ar_contrato["GCM"][] = array(289); #PUBLICA COMUNICAÇAO LTDA - MOOVE
        $ar_contrato["GCM"][] = array(727); #JANNER LEAL SOCIEDADE INDIVIDUAL DE ADVOCACIA
        $ar_contrato["GCM"][] = array(629); #SERVICO FEDERAL DE PROCESSAMENTO DE DADOS (SERPRO)
        $ar_contrato["GCM"][] = array(447); #TLD TELEDATA TECNOLOGIA EM CONECTIVIDADE LTDA
        $ar_contrato["GCM"][] = array(506); #SEGMENTO PESQUISAS E ANÁLISE DE MERCADO LTDA

        #### GTI - CONTRATOS ####
        $ar_area[] = "GTI";
        $ar_contrato["GTI"][] = array(30); #IMMEDIATE CONSULTORIAS E SISTEMAS LTDA
        $ar_contrato["GTI"][] = array(58); #PROPUS INFORMÁTICA LTDA
        $ar_contrato["GTI"][] = array(418); #CLICKSIGN GESTAO DE DOCUMENTOS S/A
        $ar_contrato["GTI"][] = array(271); #FBT TECNOLOGIA LTDA

        #### GRC - CONTRATOS ####
        $ar_area[] = "GRC";
        $ar_contrato["GRC"][] = array(768,769); #SCALZILLI, ALTHAUS, CHIMELO & SPOHR ADVOGADOS
        $ar_contrato["GRC"][] = array(767); #DE BERNT ENTSCHEV HUMAN CAPITAL LTDA
        $ar_contrato["GRC"][] = array(449,450); #GI GROUP BRASIL RECURSOS HUMANOS LTDA
        $ar_contrato["GRC"][] = array(332); #INTEGRAR/RS - ASSOCIAÇAO DE INTEGRAÇAO EMPRESA ESCOLA
        $ar_contrato["GRC"][] = array(331); #AMERICAN TOUR AGENCIA DE VIAGENS E TURISMO LTDA
        $ar_contrato["GRC"][] = array(309); #ATIVA MEDICINA E SEGURANÇA DO TRABALHO LTDA
        $ar_contrato["GRC"][] = array(396); #PHF AUDITORES INDEPENDENTES S/S
        $ar_contrato["GRC"][] = array(729); #PLACE CONSULTORIA E RH LTDA
        $ar_contrato["GRC"][] = array(429); #BEECORP BEM ESTAR CORPORATIVO LTDA
        $ar_contrato["GRC"][] = array(36,37); #TICKET SERVIÇOS
		$ar_contrato["GRC"][] = array(431); #SENIOR SISTEMAS S/A
		$ar_contrato["GRC"][] = array(38,39,40,41,42); #SENIOR SISTEMAS S A
		$ar_contrato["GRC"][] = array(32,417); #UNIMED PORTO ALEGRE SOC COOP TRAB MÉDICO LTDA
		$ar_contrato["GRC"][] = array(454); #ASSOCIACAO BRASILEIRA DE RH
		$ar_contrato["GRC"][] = array(31,436); #UNIODONTO PORTO ALEGRE COOP ODONTOLOGICA
		$ar_contrato["GRC"][] = array(146,690); #CIEE- CENTRO DE INTEGRAÇÃO EMPRESA-ESCOLA RS

        #### GC - CONTRATOS ####
        $ar_area[] = "GC";
        $ar_contrato["GC"][] = array(89); #SINQIA TECNOLOGIA LTDA
        $ar_contrato["GC"][] = array(776); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(220); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(439); #INTERACT SOLUTIONS LTDA
	
        #### GAP - CONTRATOS ####
        $ar_area[] = "GAP";
        $ar_contrato["GAP"][] = array(448); #ANAPAR ASSOC. NACIONAL DE PARTICIPANTES DE FUNDOS DE PENSAO
        $ar_contrato["GAP"][] = array(84,329); #JESSE MONTELLO SERV TEC ATUARIA E ECON.
        $ar_contrato["GAP"][] = array(437); #ASSOC DOS TECN DAS EMPR ENERGIA ELETRICA DO RS
        $ar_contrato["GAP"][] = array(424); #ASSOC DOS APOSENTADOS E PENSIONISTAS ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(434); #UNIPROCEEE UNIAO DOS PROFISSIONAIS LIBERAIS DA CEEE
        $ar_contrato["GAP"][] = array(426); #SINDICATO DOS TECNICOS INDUSTRIAIS
        $ar_contrato["GAP"][] = array(442); #SENGE
        $ar_contrato["GAP"][] = array(416); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #SINDICATO TRABALHADORES NA IND ENERGIA ELETRICA NO EST RS
        $ar_contrato["GAP"][] = array(435); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
        $ar_contrato["GAP"][] = array(438,512); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
     
        #### GJ - CONTRATOS ####
        $ar_area[] = "GJ";
        $ar_contrato["GJ"][] = array(131); #ASTREA SOFTWARE LTDA
        $ar_contrato["GJ"][] = array(132); #KTREE PENSO TECNOLOGIA DA INFORMAÇAO LTDA
        $ar_contrato["GJ"][] = array(330); #CENCO E CENCO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(103,149,168,230,370); #FONSECA SALERNO, TRAVERSO E KVITKO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(127,128,129,130,135,221,222,456,668); #BOTHOME ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(133,508); #SOUZA, CESCON, BARRIEU & FLESCH SOCIEDADE DE ADVOGADOS
        $ar_contrato["GJ"][] = array(140,251); #BOCATER, CAMARGO, COSTA E SILVA, RODRIGUES ADV. ASSOCIADOS.
        $ar_contrato["GJ"][] = array(119); #RENE BERGMANN AVILA ADVOGADOS
        $ar_contrato["GJ"][] = array(430,504); #SPEROTTO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(126); #COLOMBO ADVOCACIA E CONSULTORIA JURIDICA S/S
        $ar_contrato["GJ"][] = array(121,452); #REIS, TORRES E FLORÊNCIO ASSOCIADOS
        $ar_contrato["GJ"][] = array(104,257,383); #BARCELLOS ADVOCACIA EMPRESARIAL
        $ar_contrato["GJ"][] = array(687); #REDE SOLUÇÕES DIGITAIS LTDA
        $ar_contrato["GJ"][] = array(138,148,171,256,382,505); #MILESKI ADVOGADOS
        $ar_contrato["GJ"][] = array(373,432); #SOUTO CORREA CESA LUMMERTZ & AMARAL ADVOGADOS
        $ar_contrato["GJ"][] = array(628); #RECURSAL PROCESSAMENTO DE DADOS LTDA
        $ar_contrato["GJ"][] = array(122,143); #ELIANE SCHIRMER ANTUNES
        $ar_contrato["GJ"][] = array(120); #ZAMARI E MARCONDES ADVOGADOS ASSOCIADOS S/C
        $ar_contrato["GJ"][] = array(123,124,125,139,166); #JUCHEM & ADVOGADOS ASSOCIADOS S/C
       
        
        $DEBUG = FALSE;
        
        #### DEBUG ####
		#$ar_area = array('GTI','GCM');
		#$ar_contrato['GTI'][] = array(627); #MACIEL
        #$ar_contrato['GTI'][] = array(271);
        #$ar_contrato['GCM'][] = array(103,149,168,230,370);
        ##$ar_contrato[] = array(122,143);
        #
		#echo "<PRE>"; print_r($ar_area); 
		#echo "<PRE>"; print_r($ar_contrato); 
		
        #echo "<PRE>";
        #echo "<HR>";
		$nr_conta = 1;
        foreach($ar_area as $area)
        {		
			#echo "<hr>".$area."<hr>";
			
			
			
			#$nr_conta = 1;
			foreach($ar_contrato[$area] as $ar_item)
			{
				$args['ar_seq_contrato'] = array($ar_item[0]);
				
				#echo "<HR>";
				#echo $nr_conta."<BR>";
				#echo "CT-".$ar_item[0]." | ".implode(",",$ar_item)."<BR>";				
				echo $nr_conta.";".$area.";".$ar_item[0].";".implode("|",$ar_item);
				
				$this->contrato_model->listarContratoNoticacaoLGPD($result, $args);
				$ar_data_contrato = $result->row_array();
							
				#print_r($ar_item); #exit;
				#print_r($ar_data_contrato); exit;
				
				#print_r($ar_contato);
				echo ";".strtoupper(trim($ar_data_contrato['nr_registro'])).";".$ar_data_contrato["ds_empresa"].";".$ar_data_contrato["nm_fantasia"]; 
				echo "<BR>";
				#echo "<HR>";

				
				$nr_conta++;
			}
			
        }
        exit; ######################
            
    }

    function gera_notificacao()
    {
        echo "DOCUMENTOS GERADOS EM 08/12/2021"; EXIT;
		
		$this->load->plugin('fpdf');
        $meses = array("janeiro","fevereiro","março","abril","maio","junho","julho","agosto","setembro","outubro","novembro","dezembro");
        
        $result = null;
        $args = Array();
        $data = Array();

		
        #### GFC - CONTRATOS ####
        $ar_area[] = "GFC";
        $ar_contrato["GFC"][] = array(62); #SELTEC SISTEMAS DE SEGURANCA E SERVICOS LTDA
        $ar_contrato["GFC"][] = array(427); #METROFILE BRASIL GESTAO DA INFORMACAO LTDA.
        $ar_contrato["GFC"][] = array(389); #TIME TECNOLOGIA E DESENVOLVIMENTO DE SISTEMAS LTDA
        $ar_contrato["GFC"][] = array(162); #EMPRESA BRASILEIRA DE CORREIOS E TELEGRAFOS
        $ar_contrato["GFC"][] = array(54); #SERASA S/A
        $ar_contrato["GFC"][] = array(268); #SYNCHRO SISTEMAS DE INFORMAÇAO LTDA
        $ar_contrato["GFC"][] = array(94); #PORTO ALEGRE TERCEIRO TABELIONATO
        $ar_contrato["GFC"][] = array(163); #REDE & IMAGEM TECNOLOGIAS CONSULTORIA DE SISTEMAS LTDA - EPP
        $ar_contrato["GFC"][] = array(118); #ICATU SEGUROS S/A
		
        #### GCM - CONTRATOS ####
        $ar_area[] = "GCM";
        $ar_contrato["GCM"][] = array(110,111); #AMAURI BUENO CORRETORA DE SEGUROS LTDA
        $ar_contrato["GCM"][] = array(144); #TICKET SOLUCOES HDFGT S/A
        $ar_contrato["GCM"][] = array(507); #CURUPIRA S.A - TAKE
        $ar_contrato["GCM"][] = array(289); #PUBLICA COMUNICAÇAO LTDA - MOOVE
        $ar_contrato["GCM"][] = array(727); #JANNER LEAL SOCIEDADE INDIVIDUAL DE ADVOCACIA
        $ar_contrato["GCM"][] = array(629); #SERVICO FEDERAL DE PROCESSAMENTO DE DADOS (SERPRO)
        $ar_contrato["GCM"][] = array(447); #TLD TELEDATA TECNOLOGIA EM CONECTIVIDADE LTDA
        $ar_contrato["GCM"][] = array(506); #SEGMENTO PESQUISAS E ANÁLISE DE MERCADO LTDA

        #### GTI - CONTRATOS ####
        $ar_area[] = "GTI";
        $ar_contrato["GTI"][] = array(30); #IMMEDIATE CONSULTORIAS E SISTEMAS LTDA
        $ar_contrato["GTI"][] = array(58); #PROPUS INFORMÁTICA LTDA
        $ar_contrato["GTI"][] = array(418); #CLICKSIGN GESTAO DE DOCUMENTOS S/A
        $ar_contrato["GTI"][] = array(271); #FBT TECNOLOGIA LTDA

        #### GRC - CONTRATOS ####
        $ar_area[] = "GRC";
        $ar_contrato["GRC"][] = array(768,769); #SCALZILLI, ALTHAUS, CHIMELO & SPOHR ADVOGADOS
        $ar_contrato["GRC"][] = array(767); #DE BERNT ENTSCHEV HUMAN CAPITAL LTDA
        $ar_contrato["GRC"][] = array(449,450); #GI GROUP BRASIL RECURSOS HUMANOS LTDA
        $ar_contrato["GRC"][] = array(332); #INTEGRAR/RS - ASSOCIAÇAO DE INTEGRAÇAO EMPRESA ESCOLA
        $ar_contrato["GRC"][] = array(331); #AMERICAN TOUR AGENCIA DE VIAGENS E TURISMO LTDA
        $ar_contrato["GRC"][] = array(309); #ATIVA MEDICINA E SEGURANÇA DO TRABALHO LTDA
        $ar_contrato["GRC"][] = array(396); #PHF AUDITORES INDEPENDENTES S/S
        $ar_contrato["GRC"][] = array(729); #PLACE CONSULTORIA E RH LTDA
        $ar_contrato["GRC"][] = array(429); #BEECORP BEM ESTAR CORPORATIVO LTDA
        $ar_contrato["GRC"][] = array(36,37); #TICKET SERVIÇOS
		$ar_contrato["GRC"][] = array(431); #SENIOR SISTEMAS S/A
		$ar_contrato["GRC"][] = array(38,39,40,41,42); #SENIOR SISTEMAS S A
		$ar_contrato["GRC"][] = array(32,417); #UNIMED PORTO ALEGRE SOC COOP TRAB MÉDICO LTDA
		$ar_contrato["GRC"][] = array(454); #ASSOCIACAO BRASILEIRA DE RH
		$ar_contrato["GRC"][] = array(31,436); #UNIODONTO PORTO ALEGRE COOP ODONTOLOGICA
		$ar_contrato["GRC"][] = array(146,690); #CIEE- CENTRO DE INTEGRAÇÃO EMPRESA-ESCOLA RS

        #### GC - CONTRATOS ####
        $ar_area[] = "GC";
        $ar_contrato["GC"][] = array(89); #SINQIA TECNOLOGIA LTDA
        $ar_contrato["GC"][] = array(776); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(220); #BVQI DO BRASIL SOCIEDADE CERTIFICADORA LTDA
        $ar_contrato["GC"][] = array(439); #INTERACT SOLUTIONS LTDA
	
        #### GAP - CONTRATOS ####
        $ar_area[] = "GAP";
        $ar_contrato["GAP"][] = array(448); #ANAPAR ASSOC. NACIONAL DE PARTICIPANTES DE FUNDOS DE PENSAO
        $ar_contrato["GAP"][] = array(84,329); #JESSE MONTELLO SERV TEC ATUARIA E ECON.
        $ar_contrato["GAP"][] = array(437); #ASSOC DOS TECN DAS EMPR ENERGIA ELETRICA DO RS
        $ar_contrato["GAP"][] = array(424); #ASSOC DOS APOSENTADOS E PENSIONISTAS ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(434); #UNIPROCEEE UNIAO DOS PROFISSIONAIS LIBERAIS DA CEEE
        $ar_contrato["GAP"][] = array(426); #SINDICATO DOS TECNICOS INDUSTRIAIS
        $ar_contrato["GAP"][] = array(442); #SENGE
        $ar_contrato["GAP"][] = array(416); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #COOPERATIVA DE ECMEAP DA CEEE E ELETRICITARIOS DO RS
        $ar_contrato["GAP"][] = array(425); #SINDICATO TRABALHADORES NA IND ENERGIA ELETRICA NO EST RS
        $ar_contrato["GAP"][] = array(435); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
        $ar_contrato["GAP"][] = array(438,512); #ASSOCIACAO DOS ENGENHEIROS DA CEEE
     
        #### GJ - CONTRATOS ####
        $ar_area[] = "GJ";
        $ar_contrato["GJ"][] = array(131); #ASTREA SOFTWARE LTDA
        $ar_contrato["GJ"][] = array(132); #KTREE PENSO TECNOLOGIA DA INFORMAÇAO LTDA
        $ar_contrato["GJ"][] = array(330); #CENCO E CENCO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(103,149,168,230,370); #FONSECA SALERNO, TRAVERSO E KVITKO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(127,128,129,130,135,221,222,456,668); #BOTHOME ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(133,508); #SOUZA, CESCON, BARRIEU & FLESCH SOCIEDADE DE ADVOGADOS
        $ar_contrato["GJ"][] = array(140,251); #BOCATER, CAMARGO, COSTA E SILVA, RODRIGUES ADV. ASSOCIADOS.
        $ar_contrato["GJ"][] = array(119); #RENE BERGMANN AVILA ADVOGADOS
        $ar_contrato["GJ"][] = array(430,504); #SPEROTTO ADVOGADOS ASSOCIADOS
        $ar_contrato["GJ"][] = array(126); #COLOMBO ADVOCACIA E CONSULTORIA JURIDICA S/S
        $ar_contrato["GJ"][] = array(121,452); #REIS, TORRES E FLORÊNCIO ASSOCIADOS
        $ar_contrato["GJ"][] = array(104,257,383); #BARCELLOS ADVOCACIA EMPRESARIAL
        $ar_contrato["GJ"][] = array(687); #REDE SOLUÇÕES DIGITAIS LTDA
        $ar_contrato["GJ"][] = array(138,148,171,256,382,505); #MILESKI ADVOGADOS
        $ar_contrato["GJ"][] = array(373,432); #SOUTO CORREA CESA LUMMERTZ & AMARAL ADVOGADOS
        $ar_contrato["GJ"][] = array(628); #RECURSAL PROCESSAMENTO DE DADOS LTDA
        $ar_contrato["GJ"][] = array(122,143); #ELIANE SCHIRMER ANTUNES
        $ar_contrato["GJ"][] = array(120); #ZAMARI E MARCONDES ADVOGADOS ASSOCIADOS S/C
        $ar_contrato["GJ"][] = array(123,124,125,139,166); #JUCHEM & ADVOGADOS ASSOCIADOS S/C
       
        
        $DEBUG = FALSE;
        
        #### DEBUG ####
		#$ar_area = array('GTI','GCM');
		#$ar_contrato['GTI'][] = array(627); #MACIEL
        #$ar_contrato['GTI'][] = array(271);
        #$ar_contrato['GCM'][] = array(103,149,168,230,370);
        ##$ar_contrato[] = array(122,143);
        #
		#echo "<PRE>"; print_r($ar_area); 
		#echo "<PRE>"; print_r($ar_contrato); 
		
        #echo "<PRE>";
        #echo "<HR>";
        foreach($ar_area as $area)
        {		
			echo "<hr>".$area."<hr>";
			
			$nr_conta = 1;
			foreach($ar_contrato[$area] as $ar_item)
			{
				$args['ar_seq_contrato'] = array($ar_item[0]);
				
				$this->contrato_model->listarContratoNoticacaoLGPD($result, $args);
				$ar_data_contrato = $result->row_array();
							
				#print_r($ar_item); exit;
				#print_r($ar_data_contrato); exit;
				
				$ar_servico = array();
				foreach($ar_item as $ar_serv)
				{
					$args['ar_seq_contrato'] = $ar_serv;
					$this->contrato_model->getContratoNoticacaoLGPD($result, $args);
					$ar_data_servico = $result->row_array();        

					$ar_servico[] = trim($ar_data_servico['ds_servico']);
					#print_r($ar_data_servico);
				}

				$ar_gestor = array();
				foreach($ar_item as $ar_serv)
				{
					$args['ar_seq_contrato'] = $ar_serv;
					$this->contrato_model->getContratoNoticacaoLGPD($result, $args);
					$ar_data_servico = $result->row_array();        

					$ar_gestor[] = trim($ar_data_servico['gestor_contrato']);
					#print_r($ar_data_servico);
				}

				#echo "<HR>";
				
				#### GERAR NUMERO DE CORRESPONDENCIA ####
				## data, gerencia, solicitante, assinatura, destinatario, assunto | https://www.e-prev.com.br/cieprev/index.php/cadastro/sg_correspondencia/cadastro
				$args_carta = array(
					'divisao'               => "GTI",
					'solicitante_nome'      => "Cristiano Jacobsen",
					'assinatura_nome'       => "Rodrigo Sisnandes Pereira",
					'assunto'               => "Notificação LGPD",
					'destinatario_nome'     => strtoupper(trim($ar_data_contrato['nm_entidade'])),
					'cd_usuario'            => $this->session->userdata('codigo'),              
					'data'                  => "",
					'solicitante_emp'       => "",
					'solicitante_re'        => "",
					'solicitante_seq'       => "",
					'assinatura_emp'        => "",
					'assinatura_re'         => "",
					'assinatura_seq'        => "",
					'cd_empresa'            => "",
					'cd_registro_empregado' => "",
					'seq_dependencia'       => "",
					'destinatario_emp'      => "",
					'destinatario_re'       => "",
					'destinatario_seq'      => ""               
				);  
				$cd_correspondencia = $this->correspondencias_model->salvar($args_carta);
				$ar_correspondencia = $this->correspondencias_model->carrega(intval($cd_correspondencia));
				###########################################
				
				#$ar_correspondencia['ano_numero'] = "2021/xxxxx";
				
				$this->load->plugin('fpdf');
				$ob_pdf = new PDF('P', 'mm', 'A4');
				$ob_pdf->AddFont('segoeuil');
				$ob_pdf->AddFont('segoeuib');       
				#$ob_pdf->SetNrPag(true);
				$ob_pdf->SetNrPagDe(true);
				$ob_pdf->SetMargins(15, 14, 5);
				$ob_pdf->header_exibe = true;
				$ob_pdf->header_logo = true;
				$ob_pdf->header_logo_iso = true;
				$ob_pdf->header_titulo = false;
				
				$ob_pdf->AddPage();
				$largura = 180;
				
				$altura_linha = 5;

				$ob_pdf->SetY($ob_pdf->GetY() + 0);
			
				$ob_pdf->SetFont('segoeuil', '', 12);               
				$ob_pdf->MultiCell($largura, $altura_linha, "Porto Alegre, ".date("d")." de ".$meses[date("m")-1]." de ".date("Y"),0,"R");
				$ob_pdf->MultiCell($largura, $altura_linha, "FUNDAÇÃO FAMILIA/PRES/".$ar_correspondencia['ano_numero'],0,"R");      
				
				$ob_pdf->SetY($ob_pdf->GetY() + 4);
			
				$ob_pdf->SetFont('segoeuib', '', 12);               
				$ob_pdf->MultiCell($largura, $altura_linha, "Para: ".strtoupper(trim($ar_data_contrato['nm_fantasia'])));
				$ob_pdf->SetFont('segoeuil', '', 10);
				$ob_pdf->MultiCell($largura, $altura_linha, "Razão Social: ".strtoupper(trim($ar_data_contrato['nm_entidade'])));
				$ob_pdf->MultiCell($largura, $altura_linha, "Identificador: ".strtoupper(trim(implode(",",$ar_item)))." - ".strtoupper(trim($ar_data_contrato['nr_registro'])));
				$ob_pdf->MultiCell($largura, $altura_linha, "Gestor Contrato: ".strtoupper(trim(implode(",",$ar_gestor))));
				
				$ob_pdf->SetY($ob_pdf->GetY() + 4);
				
				$ob_pdf->SetFont('segoeuib', '', 12);               
				$ob_pdf->MultiCell($largura, $altura_linha, "Assunto: Notificação LGPD");           

				$altura_linha = 4.7;
				$ob_pdf->SetFont('segoeuil', '', 12);               
				$ob_pdf->MultiCell($largura, $altura_linha, "
Prezado(s) Senhor(es),
            
A legislação brasileira está passando por mudanças significativas referente à proteção dos dados pessoais tratados para fins comerciais, sendo que a Lei Geral de Proteção de Dados Pessoais - LGPD - nº 13.709, entrou em vigor em setembro/2020.
    
Referida lei dispõe sobre o tratamento de dados pessoais, nos meios digitais e físicos, por pessoas jurídicas, com objetivo de proteger os direitos fundamentais de liberdade e de livre desenvolvimento da personalidade da pessoa natural.
    
Como o alcance da referida lei prevê a responsabilidade solidária entre controladores e operadores dos dados pessoais coletados e tratados, e considerando que possuímos contrato(s) com o(s) seguinte(s) objeto(s):

- ".trim(implode(chr(10).chr(10)."- ",$ar_servico))."

Em razão do exposto consideramos necessário notificá-los para que no prazo de 15 dias corridos a contar do envio da presente notificação nos informem:

1.  Qual a atual conformidade da empresa com os requisitos impostos pela LGPD, quais políticas, medidas e boas práticas já estão implementadas na empresa?

2.  Se utilizam sistema rodando em nuvem?

3.  Se realizam testes sistemáticos de intrusão ou de acessos indevidos aos seus sistemas e ambientes?

4.  Possuem Relatório de Impacto à Proteção de Dados Pessoais, conforme previsto no inciso XVII, do art. 5º, da referida Lei?

5.  Possuem profissional com a função de Encarregado pelo Tratamento de Dados Pessoais para representá-los no que concerne a proteção de dados na empresa?
    
Ou que, caso ainda não possuam tais documentos e procedimentos, apresentem o plano de ação com escopo e previsão de datas para a implementação das medidas necessárias para a empresa entrar em compliance com a Lei 13.709/18.
");         
            if($ob_pdf->GetY() > 238)
            {
                $ob_pdf->AddPage();
            }
            
            $ob_pdf->MultiCell($largura, $altura_linha, "
Aproveitamos o ensejo para protestos de elevada estima e consideração.

Atenciosamente,

Rodrigo Sisnandes Pereira, 
Diretor Presidente.
"); 
    
				if($DEBUG)
				{
					$ob_pdf->Output();  
				}
				else
				{
					$arquivo = "up/contrato_notificacao/".$area."/CT-".$ar_item[0].".pdf";
					
					echo "<HR>".$nr_conta." - ".$arquivo."</HR>";
					$ob_pdf->Output($arquivo, 'F');
					$nr_conta++;
				}
				
			}
			
        }
        exit; ######################
            
    }


   
}
?>