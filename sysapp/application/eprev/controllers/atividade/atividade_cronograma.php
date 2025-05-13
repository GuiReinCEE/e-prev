<?php
class atividade_cronograma extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		$this->load->model('projetos/atividade_cronograma_model');
    }
	
    function index($token_gerencia = "")
    {
		$result = null;
		$data = array();
		$args = array();	

		$data['token_gerencia'] = trim($token_gerencia);
		
		$this->atividade_cronograma_model->analistas($result, $args);
		$data['ar_analista'] = $result->result_array();		
		
		$this->load->view('atividade/atividade_cronograma/index.php',$data);
    }	
	
    function cronogramaListar()
    {
        $data = array();
        $result = null;
		$args = array();
		
		$args["dt_inclusao_ini"] = $this->input->post("dt_inclusao_ini", TRUE);
		$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);		
		$args["cd_analista"]     = $this->input->post("cd_analista", TRUE);		
		$args["token_gerencia"]  = $this->input->post("token_gerencia", TRUE);
		$args["cd_usuario"]      = $this->session->userdata('codigo');	
		
		manter_filtros($args);
				
        $this->atividade_cronograma_model->cronogramaListar( $result, $args );
		$data['collection'] = $result->result_array();
		
        $this->load->view('atividade/atividade_cronograma/index_partial_result', $data);
    }	
	
    function cadastro($cd_atividade_cronograma = 0)
    {
		$args = array();
		$data = array();
		$result = null;
		
		$data['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		
		if(intval($cd_atividade_cronograma) == 0)
		{
			$data['row'] = Array('cd_atividade_cronograma'=>0,
								 'descricao'=>'',  
								 'periodo'=>'',
								 'dt_inclusao'=>'',  
								 'dt_exclusao'=>'',
								 'cd_divisao'=>$this->session->userdata('divisao'),
								 'cd_responsavel'=>$this->session->userdata('codigo'),
								 'dt_encerra'=> ''
								);
		}
		else
		{
			
			$this->atividade_cronograma_model->cronograma($result, $args);
			$data['row'] = $result->row_array();	
		}
		$this->load->view('atividade/atividade_cronograma/cadastro.php',$data);
    }

    function cronograma_salvar()
    {
		$data = array();
		$result = null;
		$args = array();

		$args["cd_atividade_cronograma"] = $this->input->post("cd_atividade_cronograma", TRUE);
		$args["periodo"]                 = $this->input->post("periodo", TRUE);
		
		if($args["periodo"] == 1)
		{
			$args['dt_inicio'] = '01/04/'.date('Y');
			$args['dt_final']  = '31/07/'.date('Y');
		}
		else if($args["periodo"] == 2)
		{
			$args['dt_inicio'] = '01/08/'.date('Y');
			$args['dt_final']  = '30/11/'.date('Y');
		}
		else if($args["periodo"] == 3)
		{
			$args['dt_inicio'] = '01/12/'.date('Y');
			$args['dt_final']  = '31/03/'.date('Y')+1;
		}
		
		$args["cd_responsavel"]          = $this->input->post("cd_responsavel", TRUE);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
		
		$cd_cronograma_new = $this->atividade_cronograma_model->cronograma_salvar( $result, $args );
		
		redirect("atividade/atividade_cronograma/cronograma/".$cd_cronograma_new, "refresh");

    }

    function excluir_cronograma($cd_atividade_cronograma = 0)
    {
		$data = array();
		$result = null;
		$args = array();

		$args["cd_atividade_cronograma"] = intval($cd_atividade_cronograma);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->excluir_cronograma( $result, $args );
		
		redirect("atividade/atividade_cronograma/", "refresh");
    }	
	
    function cronograma($cd_atividade_cronograma = 0)
    {
		$data = array();
		$result = null;
		$args = array();
		
		$data['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		
		$data['gerencia_dd'] = $this->db->query("SELECT codigo AS value, codigo || ' - ' || nome AS text FROM projetos.divisoes WHERE tipo NOT IN ('OUT','COM') ORDER BY text")->result_array();

		$this->atividade_cronograma_model->cronograma_grupo($result, $args);
		$data['arr_atividade_cronograma_grupo'] = $result->result_array();	
		
		$this->atividade_cronograma_model->projetos( $result, $args );
		$data['arr_projetos'] = $result->result_array();
		
		$this->atividade_cronograma_model->complexidade( $result, $args );
		$data['arr_complexidades'] = $result->result_array();
		
		$this->atividade_cronograma_model->status( $result, $args );
		$data['arr_status'] = $result->result_array();
		
		$this->atividade_cronograma_model->solicitantes( $result, $args );
		$data['arr_solicitante'] = $result->result_array();

		$this->atividade_cronograma_model->cronogramaResponsavel($result, $args);
		$ar_responsavel = $result->row_array();		
		
		if ($ar_responsavel['cd_responsavel'] == $this->session->userdata('codigo'))
		{
			$data['fl_responsavel'] = true;
		}
		else
		{
			$data['fl_responsavel'] = false;
		}
		
		$this->atividade_cronograma_model->encerrado($result, $args);	
		$arr_dt_encerra = $result->row_array();	
		
		$data['dt_encerra'] = $arr_dt_encerra['dt_encerra'];
		
		$this->atividade_cronograma_model->cronograma($result, $args);
		$data['row'] = $result->row_array();	
		
		$this->load->view('atividade/atividade_cronograma/cronograma.php',$data);
    }  
	
    function listar_cronograma_item()
    {
        $data = array();
		$result = null;
		$args = array();
		
		$args["cd_atividade_cronograma"]       = $this->input->post("cd_atividade_cronograma", TRUE);
		$args["cd_divisao"]                    = $this->input->post("cd_divisao", TRUE);
		$args["cd_atividade_cronograma_grupo"] = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
		$args["ini_operacional"]               = $this->input->post("ini_operacional", TRUE);
		$args["fim_operacional"]               = $this->input->post("fim_operacional", TRUE);
		$args["ini_gerente"]                   = $this->input->post("ini_gerente", TRUE);
		$args["fim_gerente"]                   = $this->input->post("fim_gerente", TRUE);
		$args["sistema"]                       = $this->input->post("sistema", TRUE);
		$args["complexidade"]                  = $this->input->post("complexidade", TRUE);
		$args["status_atual"]                  = $this->input->post("status_atual", TRUE);
		$args["fl_prioridade_area"]            = $this->input->post("fl_prioridade_area", TRUE);
		$args["fl_prioridade_consenso"]        = $this->input->post("fl_prioridade_consenso", TRUE);
		$args["cd_solicitante"]                = $this->input->post("cd_solicitante", TRUE);
		
		manter_filtros($args);
		
		$this->atividade_cronograma_model->cronogramaResponsavel($result, $args);
		$ar_responsavel = $result->row_array();			
		
		if ($ar_responsavel['cd_responsavel'] == $this->session->userdata('codigo'))
		{
			$data['fl_responsavel'] = true;
		}
		else
		{
			$data['fl_responsavel'] = false;
		}		
		
		$this->atividade_cronograma_model->encerrado($result, $args);	
		$arr_dt_encerra = $result->row_array();	
		
		$data['dt_encerra'] = $arr_dt_encerra['dt_encerra'];
		
		$this->atividade_cronograma_model->listar_cronograma_item( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->atividade_cronograma_model->projetos( $result, $args );
		$arr_projetos = $result->result_array();
		
		$data['arr_projetos'][''] = 'Selecione';
		foreach( $arr_projetos as $item )
		{
			$data['arr_projetos'][$item["value"]] = $item["text"];
		}
		
        $this->atividade_cronograma_model->complexidade( $result, $args );
		$arr_complexidades= $result->result_array();
		
		$data['arr_complexidades'][''] = 'Selecione';
		foreach( $arr_complexidades as $item )
		{
			$data['arr_complexidades'][$item["value"]] = $item["text"];
		}
		
        $this->load->view('atividade/atividade_cronograma/cronograma_partial_result', $data);
    }	
	
    function item($cd_atividade_cronograma = 0, $cd_atividade_cronograma_item = 0)
    {
		$data = array();
		$result = null;
		$args = array();

		$data['cd_atividade_cronograma']      = intval($cd_atividade_cronograma);
		$data['cd_atividade_cronograma_item'] = intval($cd_atividade_cronograma_item);
		$args['cd_atividade_cronograma']      = intval($cd_atividade_cronograma);
		$args['cd_atividade_cronograma_item'] = intval($cd_atividade_cronograma_item);
		
		$this->atividade_cronograma_model->cronograma($result, $args);
		$data['row2'] = $result->row_array();	
		
		if(intval($cd_atividade_cronograma_item) == 0)
		{
			$data['row'] = Array('cd_atividade_cronograma_item'=>0,
								 'cd_atividade_cronograma'=>intval($cd_atividade_cronograma),
								 'cd_atividade'=>'',  
								 'nr_prioridade_operacional'=>'',  
								 'nr_prioridade_gerente'=>'',  
								 'dt_inclusao'=>'',  
								 'dt_exclusao'=>'',
								 'cd_atividade_cronograma_grupo'=>'',
								 'cd_divisao'=>$this->session->userdata('divisao'),
								 'cd_responsavel'=>$this->session->userdata('codigo')
								);
		}
		else
		{
			$this->atividade_cronograma_model->cronograma_item($result, $args);
			$data['row'] = $result->row_array();	
		}		
		
		
		
		$this->atividade_cronograma_model->encerrado($result, $args);	
		$arr_dt_encerra = $result->row_array();	
		
		$data['dt_encerra'] = $arr_dt_encerra['dt_encerra'];
		
		$this->load->view('atividade/atividade_cronograma/item.php',$data);
    }  
	
	function incluir_todas($cd_atividade_cronograma)
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
		
		$args["cd_atividade_cronograma_item"]  = 0;

		$args["nr_prioridade_operacional"]     = '';
		$args["nr_prioridade_gerente"]         = '';
		$args["cd_atividade_cronograma_grupo"] = '';

		$this->atividade_cronograma_model->atividade_nao_concluidas( $result, $args );
		$arr = $result->result_array();
		
		foreach($arr as $item)
		{
			$args['cd_atividade'] = $item['numero'];
			
			$this->atividade_cronograma_model->salvar_item( $result, $args );
		}
		
		redirect("atividade/atividade_cronograma/cronograma/".$args["cd_atividade_cronograma"], "refresh");
	}
	
    function salvar_item()
    {
		$data = array();
		$result = null;
		$args = array();

		$args["cd_atividade_cronograma_item"]  = $this->input->post("cd_atividade_cronograma_item", TRUE);
		$args["cd_atividade_cronograma"]       = $this->input->post("cd_atividade_cronograma", TRUE);
		$args["cd_atividade"]                  = $this->input->post("cd_atividade", TRUE);
		$args["cd_atividade_cronograma_grupo"] = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
		$args["cd_usuario"]                    = $this->session->userdata('codigo');
		
		$cd_atividade_cronograma_new = $this->atividade_cronograma_model->salvar_item( $result, $args );
		
		redirect("atividade/atividade_cronograma/cronograma/".$args["cd_atividade_cronograma"], "refresh");

    }	
	
    function excluir_item($cd_atividade_cronograma =0, $cd_atividade_cronograma_item = 0)
    {
		$data = array();
		$result = null;
		$args = array();

		$args["cd_atividade_cronograma"]      = intval($cd_atividade_cronograma);
		$args["cd_atividade_cronograma_item"] = intval($cd_atividade_cronograma_item);
		$args["cd_usuario"]                   = $this->session->userdata('codigo');
		$this->atividade_cronograma_model->excluir_item( $result, $args );
		
		redirect("atividade/atividade_cronograma/cronograma/".$args["cd_atividade_cronograma"], "refresh");
    }		
	
	function acompanhamento($cd_atividade_cronograma = 0, $cd_atividade_cronograma_item = 0)
	{
		$data = array();
		$result = null;
		$args = array();
		
		$data['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		
		$this->atividade_cronograma_model->cronogramaResponsavel($result, $args);
		$ar_responsavel = $result->row_array();	
		
		$this->atividade_cronograma_model->lista_acompanhamento($result, $args);
		$data['collection'] = $result->result_array();	
		
		$this->atividade_cronograma_model->cronograma($result, $args);
		$data['row'] = $result->row_array();	
		
		if ($ar_responsavel['cd_responsavel'] == $this->session->userdata('codigo'))
		{
			$data['fl_responsavel'] = true;
		}
		else
		{
			$data['fl_responsavel'] = false;
		}
		
		$this->load->view('atividade/atividade_cronograma/acompanhamento',$data);
	}
	
	function salvar_acompanhamento()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$data['cd_atividade_cronograma'] = $this->input->post("cd_atividade_cronograma", TRUE);
		$args['cd_atividade_cronograma'] = $this->input->post("cd_atividade_cronograma", TRUE);
		$args['descricao']               = $this->input->post("descricao", TRUE);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->salvar_acompanhamento($result, $args);
		
		redirect("atividade/atividade_cronograma/acompanhamento/".$args["cd_atividade_cronograma"], "refresh");
	}
	
	function encerrar_cronograma($cd_atividade_cronograma = 0)
	{
		$data = array();
		$result = null;
		$args = array();
		
		$data['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args['cd_atividade_cronograma'] = intval($cd_atividade_cronograma);
		$args["cd_usuario"]              = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->encerrar_cronograma($result, $args);
		
		redirect("atividade/atividade_cronograma/cronograma/".$args["cd_atividade_cronograma"], "refresh");
	}
	
	function salva_operacional()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma_item'] = $this->input->post("cd_atividade_cronograma_item", TRUE);
		$args['nr_prioridade_operacional']    = $this->input->post("nr_prioridade_operacional", TRUE);
		$args["cd_usuario"]                   = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->salva_operacional($result, $args);
	}
	
	function salva_gerente()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma_item'] = $this->input->post("cd_atividade_cronograma_item", TRUE);
		$args['nr_prioridade_gerente']        = $this->input->post("nr_prioridade_gerente", TRUE);
		$args["cd_usuario"]                   = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->salva_gerente($result, $args);
	}
	
	function salva_projeto()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade'] = $this->input->post("cd_atividade", TRUE);
		$args['sistema']      = $this->input->post("sistema", TRUE);
		$args["cd_usuario"]   = $this->session->userdata('codigo');
		
		$this->atividade_cronograma_model->salva_projeto($result, $args);
	}
	
	function salva_complexidade()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade']    = $this->input->post("cd_atividade", TRUE);
		$args['cd_complexidade'] = $this->input->post("cd_complexidade", TRUE);
		$args["cd_usuario"]      = $this->session->userdata('codigo');

		$this->atividade_cronograma_model->salva_complexidade($result, $args);
	}
	
	function salva_grupo()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma_item'] = $this->input->post("cd_atividade_cronograma_item", TRUE);
		$args['cd_grupo']                     = $this->input->post("cd_grupo", TRUE);
		$args["cd_usuario"]                   = $this->session->userdata('codigo');

		$this->atividade_cronograma_model->salva_grupo($result, $args);
	}
	
	function carrega_grupo()
	{
		$cd_atividade_cronograma_item  = $this->input->post("cd_atividade_cronograma_item", TRUE);
		$cd_atividade_cronograma_grupo = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
		$fl = $this->input->post("fl", TRUE);
	
		$salvar_grupo = '<a href="javascript: void(0)" id="grupo_salvar_'.$cd_atividade_cronograma_item.'" onclick="salvar_grupo('.$cd_atividade_cronograma_item.', this, '.$fl.');" title="Salvar">[salvar]</a>';
	
		echo form_default_dropdown_db('cd_grupo_'.$cd_atividade_cronograma_item, '', array('projetos.atividade_cronograma_grupo', 'cd_atividade_cronograma_grupo', 'ds_atividade_cronograma_grupo'), array($cd_atividade_cronograma_grupo), '', '', array(true, false)).' '.($fl == 1 ? $salvar_grupo : '');
	}
	
	function verifica_atividade()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma'] = $this->input->post("cd_atividade_cronograma", TRUE);
		$args['cd_atividade']            = $this->input->post("cd_atividade", TRUE);
		
		$this->atividade_cronograma_model->verifica_atividade($result, $args);
		$row = $result->row_array();

		echo $row['tl'];		
	}
	
	function quadro_resumo($cd_atividade_cronograma)
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma'] = $cd_atividade_cronograma;
		$data['cd_atividade_cronograma'] = $cd_atividade_cronograma;
		
		$this->atividade_cronograma_model->quadro_resumo( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/atividade_cronograma/quadro_resumo',$data);
	}
	
	function concluidas_fora($cd_atividade_cronograma)
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma'] = $cd_atividade_cronograma;
		$data['cd_atividade_cronograma'] = $cd_atividade_cronograma;
		
		$this->atividade_cronograma_model->gerencias( $result, $args );
		$data['arr_gerencias'] = $result->result_array();
		
		$this->load->view('atividade/atividade_cronograma/concluidas_fora',$data);
	}
	
	function lista_concluidas_fora()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args['cd_atividade_cronograma'] = $this->input->post("cd_atividade_cronograma", TRUE);
		$args['cd_gerencia']             = $this->input->post("cd_gerencia", TRUE);
		
		$this->atividade_cronograma_model->lista_concluidas_fora( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('atividade/atividade_cronograma/concluidas_fora_result',$data);
	}
	
	function imprimir()
	{
		$data = array();
		$result = null;
		$args = array();
		
		$args["cd_atividade_cronograma"]       = $this->input->post("cd_atividade_cronograma", TRUE);
		$args["cd_divisao"]                    = $this->input->post("cd_divisao", TRUE);
		$args["cd_solicitante"]                = $this->input->post("cd_solicitante", TRUE);
		$args["cd_atividade_cronograma_grupo"] = $this->input->post("cd_atividade_cronograma_grupo", TRUE);
		$args["ini_operacional"]               = $this->input->post("ini_operacional", TRUE);
		$args["fim_operacional"]               = $this->input->post("fim_operacional", TRUE);
		$args["ini_gerente"]                   = $this->input->post("ini_gerente", TRUE);
		$args["fim_gerente"]                   = $this->input->post("fim_gerente", TRUE);
		$args["sistema"]                       = $this->input->post("sistema", TRUE);
		$args["complexidade"]                  = $this->input->post("complexidade", TRUE);
		$args["status_atual"]                  = $this->input->post("status_atual", TRUE);
		$args["fl_prioridade_area"]            = $this->input->post("fl_prioridade_area", TRUE);
		$args["fl_prioridade_consenso"]        = $this->input->post("fl_prioridade_consenso", TRUE);
		
        $this->atividade_cronograma_model->listar_cronograma_item( $result, $args );
		$collection = $result->result_array();
		
		#### EXCEL ####
		$this->load->plugin('phpexcel');
		$nr_col_ini  = 0;
		$nr_col_fim  = 0;
		$nr_row_ini  = 5;			
		
		#### Create new PHPExcel object ####
		$objPHPExcel = new PHPExcel();

		#### CRIA PLANILHA ####
		$objPHPExcel->setActiveSheetIndex(0);	
		$objPHPExcel->getActiveSheet()->setTitle('Cronograma - Atividades');
		
		#### LOGO ####
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Terms and conditions');
		$objDrawing->setDescription('Terms and conditions');
		list($width, $height) = getimagesize('./img/logofundacao_carta.jpg'); 
		$objDrawing->setPath('./img/logofundacao_carta.jpg');
		$objDrawing->setCoordinates("A1");
		$objDrawing->setHeight(38);
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());	
		
		#### TITULO ###
		$objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', utf8_encode("CRONOGRAMA - ATIVIDADES"));
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);	
		
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode(date('d/m/Y H:i:s')));
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);		
		
		#### CABEÇALHO ####
		$nr_row = $nr_row_ini;
		$nr_col = $nr_col_ini;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, utf8_encode("Cód."));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode("Grupo"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, utf8_encode("Dt. Atividade"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode("Solic/Atend"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode("Solic/Gerência"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, utf8_encode("Atividade"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode("Descrição"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, utf8_encode("Operacional"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 8, $nr_row, utf8_encode("Gerente"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 9, $nr_row, utf8_encode("Status"));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 10, $nr_row, utf8_encode("Complexidade"));
		
		$nr_col_fim+=10;		
			
		$nr_row = $nr_row_ini + 1;	
		
		foreach($collection as $item)
		{
			$nr_col = $nr_col_ini;
			
			$objPHPExcel->getActiveSheet()->getRowDimension($nr_row)->setRowHeight(70);
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col, $nr_row, $item['cd_atividade_cronograma_item']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 1, $nr_row, utf8_encode($item['ds_atividade_cronograma_grupo']));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 2, $nr_row, $item['dt_atividade']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 3, $nr_row, utf8_encode($item['solicitante'])."\n".utf8_encode($item["atendente"]));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 4, $nr_row, utf8_encode($item['divisao']));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 5, $nr_row, $item['cd_atividade']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 6, $nr_row, utf8_encode($item['descricao']));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 7, $nr_row, $item['nr_prioridade_operacional']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 8, $nr_row, $item['nr_prioridade_gerente']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 9, $nr_row, utf8_encode($item['status_atividade']));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($nr_col + 10, $nr_row, utf8_encode($item['ds_complexidade']));
			
			$nr_row++;				
		}	
		
		#### FORMATA CABEÇALHO DA TABELA ####
		$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini)->getCoordinate();
		$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row_ini)->getCoordinate();
		$sharedStyle = new PHPExcel_Style();
		$sharedStyle->applyFromArray(
			array(
						'font' => array(
							'bold' => true
						),
						'alignment' => array(
							'wrap' => true, 
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
						),
						'borders' => array(
							'top'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'bottom'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'left'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							),
							'right'     => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)								
						),							
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
							'rotation' => 90,
							'startcolor' => array(
								'argb' => 'FFA0A0A0'
							),
							'endcolor' => array(
								'argb' => 'FFFFFFFF'
							)
						)
				));		
				
		$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);		

		#### FORMATA TABELA ####
		$I = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_ini, $nr_row_ini + 1)->getCoordinate();
		$F = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($nr_col_fim, $nr_row)->getCoordinate();			

		$sharedStyle = new PHPExcel_Style();
		$sharedStyle->applyFromArray(
			array('borders' => array(
									'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
									'right'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
									),
					'alignment' => array(
								'wrap' => true, 
								'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
								)										
				 ));			
		
		$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle, $I.':'.$F);

		#### GERA EXCEL ####
		$ds_xls = random_string().'.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$ds_xls.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');	
		exit;
	}
}
