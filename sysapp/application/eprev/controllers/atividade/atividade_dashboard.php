<?php
class atividade_dashboard extends Controller
{
    function __construct()
    {
        parent::Controller();
		$this->load->model('projetos/atividade_dashboard_model');
		$this->load->library('charts');
    }

    function index()
    {
		CheckLogin();
        if(gerencia_in(array('GTI')))
        {
			$result = null;
			$args = Array();
			$data = Array();
			
			### TMO - TMA ###
			$args['cd_tipo'] = 1; #ultimos 5 anos
			$this->atividade_dashboard_model->tempoMedio($result, $args);
			$data['ar_tm_5ano'] = $result->row_array();	

			$args['cd_tipo'] = 2; #ultimos 3 anos
			$this->atividade_dashboard_model->tempoMedio($result, $args);
			$data['ar_tm_3ano'] = $result->row_array();				
			
			$args['cd_tipo'] = 3; #ultimo ano
			$this->atividade_dashboard_model->tempoMedio($result, $args);
			$data['ar_tm_1ano'] = $result->row_array();				

			### ABERTAS X ENCERRADAS ###
			$args['cd_tipo'] = 10; #ultimos aberta 1 mes
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_01meses_aberta'] = $result->row_array();		

			$args['cd_tipo'] = 11; #ultimos aberta 3 meses
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_03meses_aberta'] = $result->row_array();				
			
			$args['cd_tipo'] = 12; #ultimos aberta 12 meses
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_12meses_aberta'] = $result->row_array();	

			$args['cd_tipo'] = 20; #ultimos encerrada 1 mes
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_01meses_encerrada'] = $result->row_array();		

			$args['cd_tipo'] = 21; #ultimos encerrada 3 meses
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_03meses_encerrada'] = $result->row_array();				
			
			$args['cd_tipo'] = 22; #ultimos encerrada 12 meses
			$this->atividade_dashboard_model->resumoMes($result, $args);
			$data['ar_12meses_encerrada'] = $result->row_array();		

			### RESUMO ANO ###
			$this->atividade_dashboard_model->resumoAno($result, $args);
			$data['ar_ano'] = $result->result_array();	

			### RESUMO AREA ###
			$this->atividade_dashboard_model->resumoArea($result, $args);
			$data['ar_area'] = $result->result_array();		

			### RESUMO EM TESTE ###
			$this->atividade_dashboard_model->resumoEmTesteMes($result, $args);
			$data['ar_teste_ano_mes'] = $result->result_array();	

			### RESUMO CATEGORIA GERAL ###
			$args['cd_gerencia'] = "";
			$args['nr_ano']      = "";
			$args['fl_desenv']   = "";
			$this->atividade_dashboard_model->resumoCategoria($result, $args);
			$data['ar_categoria'] = $result->result_array();	
			
			### RESUMO CATEGORIA ANO ###
			$args['cd_gerencia'] = "";
			$args['nr_ano']      = date("Y");
			$args['fl_desenv']   = "";
			$this->atividade_dashboard_model->resumoCategoria($result, $args);
			$data['ar_categoria_ano'] = $result->result_array();					
			
			### RESUMO CATEGORIA ANO - DESENVOLVIMENTO ###
			$args['cd_gerencia'] = "";
			$args['nr_ano']      = date("Y");
			$args['fl_desenv']   = "S";
			$this->atividade_dashboard_model->resumoCategoria($result, $args);
			$data['ar_categoria_ano_desenv'] = $result->result_array();			
			
			
			### CARDS ###
            $this->atividade_dashboard_model->listarBacklog($result, $args);
			$data['ar_backlog'] = $result->result_array();	
			
			$this->atividade_dashboard_model->resumoBacklogArea($result, $args);
			$data['ar_backlog_area'] = $result->result_array();	
			
            $this->atividade_dashboard_model->listarAndamento($result, $args);
			$data['ar_andamento'] = $result->result_array();	

			$this->atividade_dashboard_model->resumoAndamento($result, $args);
			$data['ar_andamento_area'] = $result->result_array();	
			
            $this->atividade_dashboard_model->listarEmTeste($result, $args);
			$data['ar_teste'] = $result->result_array();	
			
			$this->atividade_dashboard_model->resumoEmTeste($result, $args);
			$data['ar_teste_area'] = $result->result_array();			
			
            $this->atividade_dashboard_model->listarAguardaUsuario($result, $args);
			$data['ar_usuario'] = $result->result_array();	
			
			$this->atividade_dashboard_model->resumoAguardaUsuario($result, $args);
			$data['ar_usuario_area'] = $result->result_array();			
			
			###
			$this->load->view('atividade/atividade_dashboard/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	function monitor()
	{
		$result = null;
		$args = Array();
		$data = Array();		
		
		#### RESUMO ####
		$this->atividade_dashboard_model->listarBacklog($result, $args);
		$ar_backlog = $result->result_array();	

		$this->atividade_dashboard_model->listarAndamento($result, $args);
		$ar_andamento = $result->result_array();		
		
		$this->atividade_dashboard_model->listarEmTeste($result, $args);
		$ar_teste = $result->result_array();	

		$this->atividade_dashboard_model->listarAguardaUsuario($result, $args);
		$ar_usuario = $result->result_array();		

		$this->atividade_dashboard_model->listarAbertas($result, $args);
		$ar_aberta = $result->result_array();		
		
		$this->atividade_dashboard_model->listarEncerradas($result, $args);
		$ar_encerrada = $result->result_array();		
		
		
		### ABERTAS X ENCERRADAS ###
		$args['cd_tipo'] = 10; #ultimos aberta 1 mes
		$this->atividade_dashboard_model->resumoMes($result, $args);
		$ar_01meses_aberta = $result->row_array();		
		
		$args['cd_tipo'] = 12; #ultimos aberta 12 meses
		$this->atividade_dashboard_model->resumoMes($result, $args);
		$ar_12meses_aberta = $result->row_array();	

		$args['cd_tipo'] = 20; #ultimos encerrada 1 mes
		$this->atividade_dashboard_model->resumoMes($result, $args);
		$ar_01meses_encerrada = $result->row_array();		
		
		$args['cd_tipo'] = 22; #ultimos encerrada 12 meses
		$this->atividade_dashboard_model->resumoMes($result, $args);
		$ar_12meses_encerrada = $result->row_array();		
		
		           
		$ar_retorno['qt_backlog']   = count($ar_backlog);
		$ar_retorno['qt_andamento'] = count($ar_andamento);
		$ar_retorno['qt_teste']     = count($ar_teste);
		$ar_retorno['qt_usuario']   = count($ar_usuario);
		$ar_retorno['qt_total']     = (count($ar_backlog) + count($ar_andamento) + count($ar_teste) + count($ar_usuario));
		
		$ar_retorno['resultado_mes']   = number_format((($ar_01meses_encerrada['qt_atividade']/(intval($ar_01meses_aberta['qt_atividade']) == 0 ? 1 : $ar_01meses_aberta['qt_atividade'])) * 100), 0, ',', '.');
		$ar_retorno['resultado_12mes'] = number_format((($ar_12meses_encerrada['qt_atividade']/$ar_12meses_aberta['qt_atividade']) * 100), 0, ',', '.');
		
		$ar_retorno['qt_aberta_hoje']    = count($ar_aberta);
		$ar_retorno['qt_encerrada_hoje'] = count($ar_encerrada);
		
		echo json_encode($ar_retorno);
	}
	
	function atendente($usuario = "")
	{
		$result = null;
		$args = Array();
		$data = Array();	
		
		$args['usuario'] = strtoupper(trim($usuario));
		$this->atividade_dashboard_model->monitorAtendente($result, $args);
		$ar_data = $result->result_array();	
		
		$ar_monitor['BACKLOG'] = 0;
		$ar_monitor['ANDAMENTO'] = 0;
		$ar_monitor['TESTE'] = 0;
		$ar_monitor['AGDUSER'] = 0;
		$ar_monitor['ABERTA_ANO'] = 0;
		$ar_monitor['CONCLUIDA_ANO'] = 0;
		
		foreach($ar_data as $item)
		{
			if($item['tipo'] == "BACKLOG")
			{
				$ar_monitor['BACKLOG'] = $item['quantidade'];
			}
			elseif($item['tipo'] == "ANDAMENTO")
			{
				$ar_monitor['ANDAMENTO'] = $item['quantidade'];
			}
			elseif($item['tipo'] == "TESTE")
			{
				$ar_monitor['TESTE'] = $item['quantidade'];
			}
			elseif($item['tipo'] == "AGDUSER")
			{
				$ar_monitor['AGDUSER'] = $item['quantidade'];
			}
			elseif($item['tipo'] == "ABERTA_ANO")
			{
				$ar_monitor['ABERTA_ANO'] = $item['quantidade'];
			}
			elseif($item['tipo'] == "CONCLUIDA_ANO")
			{
				$ar_monitor['CONCLUIDA_ANO'] = $item['quantidade'];
			}			
		}
		
		$ar_retorno['usuario']          = strtoupper(trim($usuario));
		$ar_retorno['qt_total']         = intval($ar_monitor['BACKLOG']) + intval($ar_monitor['ANDAMENTO']) + intval($ar_monitor['TESTE']) + intval($ar_monitor['AGDUSER']);
		$ar_retorno['qt_backlog']       = intval($ar_monitor['BACKLOG']) + intval($ar_monitor['ANDAMENTO']);
		$ar_retorno['qt_teste']         = intval($ar_monitor['TESTE']);
		$ar_retorno['qt_agd_usuario']   = intval($ar_monitor['AGDUSER']);		
		$ar_retorno['qt_aberta_ano']    = intval($ar_monitor['ABERTA_ANO']);	
		$ar_retorno['qt_concluida_ano'] = intval($ar_monitor['CONCLUIDA_ANO']);	
		$ar_retorno['pr_concluida_ano'] = number_format(((intval($ar_monitor['CONCLUIDA_ANO'])/intval($ar_monitor['ABERTA_ANO'])) * 100),0,"","");
	
		echo json_encode($ar_retorno);
	}		
}
?>