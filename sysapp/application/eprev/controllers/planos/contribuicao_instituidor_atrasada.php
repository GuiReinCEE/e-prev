<?php

class contribuicao_instituidor_atrasada extends Controller
{

    function __construct()
    {
        parent::Controller();
    }

    function index($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "")
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $args = Array();
            $data = Array();

            $data['cd_plano'] = $cd_plano;
            $data['cd_plano_empresa'] = $cd_plano_empresa;
            $data['nr_mes'] = $nr_mes;
            $data['nr_ano'] = $nr_ano;

            $this->load->view('planos/contribuicao_instituidor_atrasada/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

	public function get_tempo_descarte($cd_documento = 0)
	{
        $args = array();
        $url  = 'http://10.63.255.217:8080/ords/ordsws/tabela_temporalidade_doc/tempo_descarte/index';

        $args = array(
            'id'          => '8dcfac716cf69a12255d63a4abf8b485',
            'cd_tipo_doc' => $cd_documento
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

    	$vl_arquivo_central = (trim($json['result'][0]['vl_arquivo_central']) != '' ? trim($json['result'][0]['vl_arquivo_central']).' - ' : '');
		$ds_tempo_descarte  = $vl_arquivo_central . $json['result'][0]['id_arquivo_central'];
		$id_classificacao_info_doc  = $json['result'][0]['id_classificacao_info_doc'];

        return array('ds_tempo_descarte' => $ds_tempo_descarte, 'id_classificacao_info_doc' => $id_classificacao_info_doc);
	}
    
    function gerar_protocolo()
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP')))
        {
            $this->load->model('projetos/contribuicao_instituidor_atrasada_model');
            $this->load->model('projetos/Documento_protocolo_model');
            $result = null;
            $data = Array();
            $args = Array();
            $ar_protocolo = Array();

            $args["cd_empresa"] = $this->input->post("cd_plano_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            manter_filtros($args);
            
            $data['ar_contribuicao_atrasada']['COBDL'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COB1P'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['CODCC'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COFOL'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COFLT'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $args['cd_contribuicao_controle_tipo'] = array("'COBDL'", "'COB1P'", "'CODCC'", "'COFOL'", "'COFLT'");
            $args['codigo_lancamento'] = array(
              7 => array('COBDL' => 2400, 'CODCC' => 2410), #SENGE
              8 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINPRO
              10 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTAE
              12 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTEP
              19 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #AFCEEE
              20 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINTEC
              24 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #TCHE
              25 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SEPRORGS
              26 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ABRH-RS
              27 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #CEAPE
              28 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINDHA
              29 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #FUNDAÇÃO FAMÍLIA PREVIDÊNCIA
              30 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ADJORI
              31 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509')  #ARCOSUL
            );

            #### BUSCA LISTA GERADO E ENVIADO ####
            $args['fl_email_enviado'] = "S";
            $this->contribuicao_instituidor_atrasada_model->contribuicao_controle($result, $args);
            $ar_contribuicao_controle_enviado = $result->result_array();
            
            $ar_proc['cd_usuario_cadastro'] = intval(usuario_id());
			$ar_proc['cd_gerencia']         = $this->session->userdata('divisao');
            $ar_proc['ano'] = date('Y');
            
            $ar_proc['tipo_protocolo'] = "D";

            $ar_proc['fl_contrato'] = "";
            
            $this->Documento_protocolo_model->criar_protocolo($ar_proc, $msg, $ar_protocolo);

            foreach ($ar_contribuicao_controle_enviado as $ar_reg)
            {	
				$prot['cd_documento'] = 12;
				
				$tempo_descarte = $this->get_tempo_descarte($ar_reg['cd_documento']);
				
                $prot = Array();
                $prot['cd_documento_protocolo_item'] = 0;
                $prot['cd_empresa'] = $ar_reg['cd_empresa'];
                $prot['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
                $prot['seq_dependencia'] = $ar_reg['seq_dependencia'];
                $prot['nr_folha'] = 1;
                $prot['cd_usuario_cadastro'] = intval(usuario_id());
                $prot['arquivo'] = "";
                $prot['arquivo_nome'] = "";
                $prot['observacao'] = "";
                $prot['ds_processo'] = "";
            
                $ar_reg['fl_retornou'] = "";

                

                $prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
				$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);
                
                $arq = $ar_reg['cd_empresa'] . "_" . $ar_reg['cd_registro_empregado'] . "_" . $ar_reg['seq_dependencia'] . "_12_" . uniqid(time()) . ".pdf";
                $dir = "up/protocolo_digitalizacao_" . intval($ar_protocolo['cd_documento_protocolo']) . "/";
                $prot['arquivo'] = $arq;
                $prot['arquivo_nome'] = $arq;
                $prot['cd_documento_protocolo'] = intval($ar_protocolo['cd_documento_protocolo']);
                                
                $ar_reg['nr_mes'] = $ar_reg["nr_mes_competencia"];
                $ar_reg['nr_ano'] = $ar_reg["nr_ano_competencia"];
                
                $this->email_imprimir(array($ar_reg), false, $dir, $arq);
                $this->Documento_protocolo_model->adicionaDocumento($result, $prot);

            }
            
            if(intval($ar_protocolo['cd_documento_protocolo']) > 0)
            {
                redirect("ecrm/protocolo_digitalizacao/detalhe_financeiro/" . intval($ar_protocolo['cd_documento_protocolo']), "refresh");
            }
            else
            {
                exibir_mensagem("ERRO AO GERAR PROTOCOLO");
            }
             
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function email_imprimir($ar_certificado, $fl_tela=true,$dir="up/",$arq="certificado.pdf")
    {
        $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
        
        $args = $ar_certificado[0];
     
        $this->Contribuicao_instituidor_atrasada_model->relatorioListar($result, $args);
        $row = $result->row_array();
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF('P', 'mm', 'A4');
        $ob_pdf->SetNrPag(true);
        $ob_pdf->SetMargins(10, 14, 5);
        $ob_pdf->header_exibe = false;
        $ob_pdf->header_logo = false;
        $ob_pdf->header_titulo = false;
        $ob_pdf->AddPage();
        
        
        $ob_pdf->SetFont('Arial', 'B', 14);
        $ob_pdf->Text(11, 10, $row['nome']);
        
        $ob_pdf->SetLineWidth(0.7);
        $ob_pdf->Line(10,$ob_pdf->GetY(),200,$ob_pdf->GetY());
        $ob_pdf->SetY($ob_pdf->GetY() + 6);
        $ob_pdf->SetFont('Arial', 'B', 10);
        $ob_pdf->Text(11, $ob_pdf->GetY(), 'De:');
        $ob_pdf->Text(11, $ob_pdf->GetY()+4, 'Enviado em:');
        $ob_pdf->Text(11, $ob_pdf->GetY()+8, 'Para:');
        $ob_pdf->Text(11, $ob_pdf->GetY()+12, 'Cc:');
        $ob_pdf->Text(11, $ob_pdf->GetY()+16, 'Assunto:');
        
        $ob_pdf->SetFont('Arial', '', 10);
        
        $ob_pdf->Text(35, $ob_pdf->GetY(), $row['de']);
        $ob_pdf->Text(35, $ob_pdf->GetY()+4, $row['dt_email_enviado']);
        $ob_pdf->Text(35, $ob_pdf->GetY()+8, $row['para']);
        $ob_pdf->Text(35, $ob_pdf->GetY()+12, $row['cc']);
        $ob_pdf->Text(35, $ob_pdf->GetY()+16, $row['assunto']);
        
        $ob_pdf->SetY($ob_pdf->GetY() + 20);
        
        $ob_pdf->SetFont('Courier', '', 10);
        
        $ob_pdf->MultiCell(190, 6, $row['texto']);

        if($fl_tela)
        {
            $ob_pdf->Output();
        }
        else
        {
            $ob_pdf->Output($dir.$arq,"F");
        }
    }

    function atrasada()
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $data = Array();
            $args = Array();

            $data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
            $data["CD_PLANO"] = $this->input->post("cd_plano", TRUE);
            $data["NR_MES"] = $this->input->post("nr_mes", TRUE);
            $data["NR_ANO"] = $this->input->post("nr_ano", TRUE);

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            manter_filtros($args);

			#### VERIFICADO SE ESTÁ NO PERÍODO ENVIO, apartir do dia 13 ####
            $this->Contribuicao_instituidor_atrasada_model->checkPeriodo($result, $args);
            $ar_tmp = $result->row_array();			
			$data['fl_check_periodo'] = $ar_tmp['fl_periodo'];
			
            $data['ar_contribuicao_atrasada']['COBDL'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COB1P'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['CODCC'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COFOL'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $data['ar_contribuicao_atrasada']['COFLT'] = array('TOTAL' => 0, 'EMAIL' => 0);
            $args['cd_contribuicao_controle_tipo'] = array("'COBDL'", "'COB1P'", "'CODCC'", "'COFOL'", "'COFLT'");
            $args['codigo_lancamento'] = array(
              7 => array('COBDL' => 2400, 'CODCC' => 2410), #SENGE
              8 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINPRO
              10 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTAE
              12 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTEP
              19 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #AFCEEE
              20 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINTEC
              24 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #TCHE
              25 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SEPRORGS
              26 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ABRH-RS
              27 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #CEAPE
              28 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINDHA
              29 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #FUNDAÇÃO FAMÍLIA PREVIDÊNCIA
              30 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ADJORI
              31 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509')  #ARCOSUL
            );

            #### BUSCA LISTA GERADO ####
            $args['fl_email_enviado'] = "";
            $this->Contribuicao_instituidor_atrasada_model->contribuicao_controle($result, $args);
            $data['ar_contribuicao_controle'] = $result->result_array();
            $data['fl_gerado'] = (count($data['ar_contribuicao_controle']) > 0 ? true : false);

            #### BUSCA LISTA GERADO E ENVIADO ####
            $args['fl_email_enviado'] = "S";
            $this->Contribuicao_instituidor_atrasada_model->contribuicao_controle($result, $args);
            $data['ar_contribuicao_controle_enviado'] = $result->result_array();
            $data['fl_enviado'] = (count($data['ar_contribuicao_controle_enviado']) > 0 ? true : false);

            #### BUSCA TOTAIS BCO, FOL, FLT ####
            $args['fl_email'] = "";
            $this->Contribuicao_instituidor_atrasada_model->atrasada($result, $args);
            $ar_atrasada = $result->result_array();
            foreach ($ar_atrasada as $ar_item)
            {
                $data['ar_contribuicao_atrasada'][$ar_item['tp_pagamento']]['TOTAL'] = $ar_item['qt_total'];
            }

            #### BUSCA TOTAIS BCO, FOL, FLT COM EMAIL ####
            $args['fl_email'] = "S";
            $this->Contribuicao_instituidor_atrasada_model->atrasada($result, $args);
            $ar_atrasada_email = $result->result_array();
            foreach ($ar_atrasada_email as $ar_item)
            {
                $data['ar_contribuicao_atrasada'][$ar_item['tp_pagamento']]['EMAIL'] = $ar_item['qt_total'];
            }

            #### BUSCA TOTAIS BCO, FOL, FLT COMPETENCIA ANTERIOR ####
            $this->Contribuicao_instituidor_atrasada_model->atrasada_anterior($result, $args);
            $ar_atrasada_anterior = $result->result_array();
            $data['ar_contribuicao_atrasada_anterior']['COBDL'] = array('TOTAL' => 0);
            $data['ar_contribuicao_atrasada_anterior']['COB1P'] = array('TOTAL' => 0);
            $data['ar_contribuicao_atrasada_anterior']['CODCC'] = array('TOTAL' => 0);
            $data['ar_contribuicao_atrasada_anterior']['COFOL'] = array('TOTAL' => 0);
            $data['ar_contribuicao_atrasada_anterior']['COFLT'] = array('TOTAL' => 0);
            foreach ($ar_atrasada_anterior as $ar_item)
            {
                $data['ar_contribuicao_atrasada_anterior'][$ar_item['tp_pagamento']]['TOTAL'] = $ar_item['qt_total'];
            }

            $this->load->view('planos/contribuicao_instituidor_atrasada/index_result.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function sem_email()
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $data = Array();
            $args = Array();

            $data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
            $data["CD_PLANO"] = $this->input->post("cd_plano", TRUE);
            $data["NR_MES"] = $this->input->post("nr_mes", TRUE);
            $data["NR_ANO"] = $this->input->post("nr_ano", TRUE);

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            manter_filtros($args);

            $args['codigo_lancamento'] = array(
              7 => array('COBDL' => 2400, 'CODCC' => 2410), #SENGE
              8 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINPRO
              10 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTAE
              12 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTEP
              19 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #AFCEEE
              20 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINTEC
              24 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #TCHE
              25 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SEPRORGS
              26 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ABRH-RS
              27 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #CEAPE
              28 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINDHA
              29 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #FUNDAÇÃO FAMÍLIA PREVIDÊNCIA
              30 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ADJORI
              31 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509')  #ARCOSUL
            );

            $this->Contribuicao_instituidor_atrasada_model->sem_email($result, $args);
            $data['ar_lista'] = $result->result_array();

            $this->load->view('planos/contribuicao_instituidor_atrasada/index_sem_email_result.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function gerar()
    {
        CheckLogin();
        if (gerencia_in(array('GFC')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            manter_filtros($args);

            $args['codigo_lancamento'] = array(
              7 => array('COBDL' => 2400, 'CODCC' => 2410), #SENGE
              8 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINPRO
              10 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTAE
              12 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTEP
              19 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #AFCEEE
              20 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINTEC
              24 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #TCHE
              25 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SEPRORGS
              26 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ABRH-RS
              27 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #CEAPE
              28 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINDHA
              29 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #FUNDAÇÃO FAMÍLIA PREVIDÊNCIA
              30 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ADJORI
              31 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509')  #ARCOSUL
            );
            $args["cd_usuario"] = $this->session->userdata('codigo');

            echo $this->Contribuicao_instituidor_atrasada_model->gerar($result, $args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function enviarEmail()
    {
        CheckLogin();
        if (gerencia_in(array('GFC')))
        {
            $this->load->model(array(
                'projetos/Contribuicao_instituidor_atrasada_model',
                'projetos/contribuicao_relatorio_model'
            ));
            
            $result = null;
            $data = Array();
            $args = Array();

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"]       = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"]         = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"]           = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]           = $this->input->post("nr_ano", TRUE);
            $args["cd_usuario"]       = $this->session->userdata('codigo');

            manter_filtros($args);

            $args["cd_usuario"] = $this->session->userdata('codigo');

            $this->Contribuicao_instituidor_atrasada_model->enviarEmail($result, $args);

            $args['cd_contribuicao_relatorio_origem'] = 2;

            $args['link'] = 'https://www.fundacaoceee.com.br/';

            switch ($args["cd_empresa"]) 
            {
                case 7:
                    $args['link'] .= 'senge_pagamento.php?';
                    break;
                case 8:
                case 10:
                case 12:
                    $args['link'] .= 'sinprors_pagamento.php?';
                    break;
                case 19:
                case 20:
                case 24:
                case 25:
                case 26:
                case 27:
                case 28:
                case 29:
                case 30:
                case 31:
                    $args['link'] .= 'familia_pagamento.php?';
                    break;
            }

            $args['controle_tipo'] = array('COBDL', 'CODCC', 'COFOL', 'COFLT', 'COB1P');
            $args['nr_mes_comp']   = 0;
            $args['nr_ano_comp']   = 0;
            $args['fl_enviar_sms'] = 'S';

            $this->contribuicao_relatorio_model->salvar_contribuicao_controle($args);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function enviarEmailCadastro()
    {
        CheckLogin();
        if (gerencia_in(array('GFC')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $data = Array();
            $args = Array();

            $data["CD_EMPRESA"] = $this->input->post("cd_empresa", TRUE);
            $data["CD_PLANO"] = $this->input->post("cd_plano", TRUE);
            $data["NR_MES"] = $this->input->post("nr_mes", TRUE);
            $data["NR_ANO"] = $this->input->post("nr_ano", TRUE);

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            manter_filtros($args);
			
			$args["cd_usuario"] = $this->session->userdata('codigo');	

            $args['codigo_lancamento'] = array(
              7 => array('COBDL' => 2400, 'CODCC' => 2410), #SENGE
              8 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINPRO
              10 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTAE
              12 => array('COBDL' => 2450, 'CODCC' => 2460, 'COFOL' => 2480), #SINTEP
              19 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #AFCEEE
              20 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINTEC
              24 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #TCHE
              25 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SEPRORGS
              26 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ABRH-RS
              27 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #CEAPE
              28 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #SINDHA
              29 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #FUNDAÇÃO FAMÍLIA PREVIDÊNCIA
              30 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509'), #ADJORI
              31 => array('COBDL' => 2502, 'CODCC' => 2501, 'COFOL' => 2500, 'COFLT' => '2503,2509')  #ARCOSUL
              
            );

            $this->Contribuicao_instituidor_atrasada_model->sem_email($result, $args);
            $ar_lista = $result->result_array();
            $lista = "";
            foreach ($ar_lista as $ar_item)
            {
                $part = "[" . $ar_item['forma_pagamento'] . "] " . $ar_item['cd_empresa'] . "/" . $ar_item['cd_registro_empregado'] . "/" . $ar_item['seq_dependencia'] . " - " . $ar_item['nome'];
                $lista = ($lista == "" ? $part . chr(10) : $lista . $part . chr(10));
            }

            if (trim($lista) != "")
            {
                $result = null;
                $data = Array();
                $args = Array();
                $args['lista'] = $lista;
                echo $this->Contribuicao_instituidor_atrasada_model->enviarEmailCadastro($result, $args);
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function relatorio($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "", $fl_retornou = "")
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP', 'GE')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $args = Array();
            $data = Array();

            $data['cd_plano'] = $cd_plano;
            $data['cd_plano_empresa'] = $cd_plano_empresa;
            $data['nr_mes'] = $nr_mes;
            $data['nr_ano'] = $nr_ano;
            $data['fl_retornou'] = $fl_retornou;

            $this->load->view('planos/contribuicao_instituidor_atrasada/relatorio.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function relatorioListar()
    {
        CheckLogin();
        if (gerencia_in(array('GFC', 'GP', 'GE')))
        {
            $this->load->model('projetos/Contribuicao_instituidor_atrasada_model');
            $result = null;
            $args = Array();
            $data = Array();

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"] = $this->input->post("seq_dependencia", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);
            $args["fl_retornou"] = $this->input->post("fl_retornou", TRUE);

            manter_filtros($args);

            $this->Contribuicao_instituidor_atrasada_model->relatorioListar($result, $args);
            $data['collection'] = $result->result_array();
            $this->load->view('planos/contribuicao_instituidor_atrasada/relatorio_result', $data);
        }
        else
        {
            
        }
    }
	
	function excluir()
	{
		CheckLogin();
        if (gerencia_in(array('GFC', 'GP')))
        {
			$this->load->model('projetos/contribuicao_instituidor_atrasada_model');
		
			$result = null;
            $args = Array();
            $data = Array();
			
            $args["cd_empresa"]                    = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"]         = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]               = $this->input->post("seq_dependencia", TRUE);
			$args["nr_ano_competencia"]            = $this->input->post("nr_ano_competencia", TRUE);
			$args["nr_mes_competencia"]            = $this->input->post("nr_mes_competencia", TRUE);
			$args["cd_contribuicao_controle_tipo"] = $this->input->post("cd_contribuicao_controle_tipo", TRUE);
			
			$this->contribuicao_instituidor_atrasada_model->excluir($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
	}

    public function envia_email_retorno($cd_plano, $cd_empresa, $nr_ano, $nr_mes)
    {
        CheckLogin();
        
        if(gerencia_in(array('GFC', 'GCM')))
        {
            $this->load->model('projetos/contribuicao_instituidor_atrasada_model');

            $this->contribuicao_instituidor_atrasada_model->envia_email_retorno(
                $cd_plano, 
                $cd_empresa, 
                $nr_ano, 
                $nr_mes, 
                $this->session->userdata('codigo')
            );

            redirect('planos/contribuicao_instituidor_atrasada/relatorio/'.$cd_plano.'/'.$cd_empresa.'/'.$nr_mes.'/'.$nr_ano.'/S');
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

}
