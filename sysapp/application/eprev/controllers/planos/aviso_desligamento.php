<?php

class aviso_desligamento extends Controller
{

    function __construct()
    {
        parent::Controller();
    }

    function index($cd_plano = "", $cd_plano_empresa = "", $nr_mes = "", $nr_ano = "")
    {
        CheckLogin();

        if (gerencia_in(array('GFC', 'GAP.','GS')))
        {
            $result = null;
            $args = Array();
            $data = Array();

            $data['cd_plano'] = $cd_plano;
            $data['cd_plano_empresa'] = $cd_plano_empresa;
            $data['nr_mes'] = $nr_mes;
            $data['nr_ano'] = $nr_ano;

            $this->load->view('planos/aviso_desligamento/index.php', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function listar()
    {
        CheckLogin();

        if (gerencia_in(array('GFC', 'GAP.','GS')))
        {
            $this->load->model('projetos/Aviso_desligamento_model');

            $result = null;
            $data = array();
            $args = array();

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);

            $this->Aviso_desligamento_model->verificaGeracao($result, $args);
            $data['fl_gerado_email'] = $result->num_rows();			
			
            $this->Aviso_desligamento_model->verificaEnvio($result, $args);
            $data['fl_envia_email'] = $result->num_rows();

            $this->Aviso_desligamento_model->listar($result, $args);

            $data['collection'] = $result->result_array();
            $data['qt_registro'] = $result->num_rows();
            $data['collection'] = $result->result_array();
            $this->load->view('planos/aviso_desligamento/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function gerar_email()
    {
        CheckLogin();

        if (gerencia_in(array('GFC')))
        {
            $this->load->model('projetos/Aviso_desligamento_model');

            $result = null;
            $data = array();
            $args = array();

            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);
            $args["cd_usuario"] = usuario_id();

            $this->Aviso_desligamento_model->gerar($result, $args);

            $this->listar();
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }	

    function envia_email()
    {
        CheckLogin();

        if (gerencia_in(array('GFC')))
        {
            $this->load->model('projetos/Aviso_desligamento_model');

            $result = null;
            $data = array();
            $args = array();

            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["nr_mes"] = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);
            $args["cd_usuario"] = usuario_id();

            $this->Aviso_desligamento_model->envia_email($result, $args);

            $this->listar();
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
    function excluir_aviso()
    {
        CheckLogin();

        if (gerencia_in(array('GFC')))
        {
            $this->load->model('projetos/Aviso_desligamento_model');

            $result = null;
            $data = array();
            $args = array();

            $args["cd_empresa"]            = $this->input->post("cd_empresa", TRUE);
            $args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado", TRUE);
            $args["seq_dependencia"]       = $this->input->post("seq_dependencia", TRUE);
            $args["nr_mes"]                = $this->input->post("nr_mes", TRUE);
            $args["nr_ano"]                = $this->input->post("nr_ano", TRUE);
            $args["cd_usuario"]            = usuario_id();

            $this->Aviso_desligamento_model->excluir_aviso($result, $args);
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
            $this->load->model('projetos/Aviso_desligamento_model');
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
            
            #### BUSCA LISTA GERADO E ENVIADO ####
            $args['fl_email_enviado'] = "S";
            $this->Aviso_desligamento_model->aviso_desligamento_controler($result, $args);
            $ar_contribuicao_controle_enviado = $result->result_array();
            
            $ar_proc['cd_usuario_cadastro'] = intval(usuario_id());
			$ar_proc['cd_gerencia']         = $this->session->userdata('divisao');
            $ar_proc['ano'] = date('Y');
            
            $ar_proc['tipo_protocolo'] = "D";

            $ar_proc['fl_contrato'] = "";

            $this->Documento_protocolo_model->criar_protocolo($ar_proc, $msg, $ar_protocolo);

            foreach ($ar_contribuicao_controle_enviado as $ar_reg)
            {
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

                $prot['cd_documento'] = 12;

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
        $this->load->model('projetos/Aviso_desligamento_model');
        
        $args = $ar_certificado[0];
     
        $this->Aviso_desligamento_model->relatorio_listar($result, $args);
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

}
