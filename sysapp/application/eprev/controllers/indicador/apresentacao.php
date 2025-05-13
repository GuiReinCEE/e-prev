<?php
class apresentacao extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index($cd_tipo = '', $cd_grupo = 0, $nr_ano = 0)
    {
		CheckLogin();
		$this->load->model('indicador/Indicador_tabela_model');
		
		$result = null;
		$data   = Array();
		$args   = Array();		
		
		$args['nr_ano'] = (intval($nr_ano) == 0 ? date('Y') : intval($nr_ano));
		$this->Indicador_tabela_model->buscaPeriodo($result, $args);
		$row_periodo = $result->row_array();	
		$cd_indicador_periodo = 0;

        if(count($row_periodo) > 0)
		{
			$cd_indicador_periodo = intval($row_periodo['cd_indicador_periodo']);
		}

		$data['cd_grupo']   = intval($cd_grupo);
		$data['cd_periodo'] = intval($cd_indicador_periodo);
        $args['cd_tipo'] = trim($cd_tipo);
        $data['cd_tipo'] = $args['cd_tipo'];

        $data['tipo'] = array(
			array('value' => 'G', 'text' => 'Gestão'),
			array('value' => 'A', 'text' => 'Auxiliar')
		);

		$this->Indicador_tabela_model->grupoComboTipo($result, $args);
		$data['ar_grupo'] = $result->result_array();	
		
		$this->Indicador_tabela_model->periodoCombo($result, $args);
		$data['ar_periodo'] = $result->result_array();		
		
		$this->Indicador_tabela_model->controlesCombo($result, $args);
		$data['ar_controle'] = $result->result_array();			
		
		$this->load->view('indicador/apresentacao/index.php', $data);
    }	
	
    function listar()
    {
		CheckLogin();
		$this->load->model('indicador/Indicador_tabela_model');
		$this->load->helper(array('indicador'));

		$result = null;
		$data   = Array();
		$args   = Array();
        $data['collection'] = Array();

		$args['cd_indicador_grupo']    = intval($this->input->post('cd_indicador_grupo', true));
		$args['cd_indicador_periodo']  = intval($this->input->post('cd_indicador_periodo', true));
		$args['cd_processo']           = intval($this->input->post('cd_processo', true));
		$args['cd_indicador_controle'] = intval($this->input->post('cd_indicador_controle', true));
		$args['cd_tipo']               = trim($this->input->post('cd_tipo', true));
		$args['fl_igp'] 			   = $this->input->post('fl_igp', TRUE);
		$args['fl_poder'] 			   = $this->input->post('fl_poder', TRUE);
		$args['fl_encerrado'] 		   = $this->input->post('fl_encerrado', TRUE);

		$this->Indicador_tabela_model->listar( $result, $args );
		$data['collection'] = $result->result_array();
		
		$this->load->view('indicador/apresentacao/index_result', $data);
    }	

	function detalhe($cd = 0, $cd_apresentacao = 0)
	{
		CheckLogin();
		$this->load->model('indicador/Indicador_tabela_model');
		$args   = Array();
		$data   = Array();
		$result = null;		
		$data['apresentacao'] = Array();
/*
		// apresentação
		$apresentacao = array();
		if(intval($cd_apresentacao) > 0)
		{
			$qr_sql = " --- 1
						SELECT iai.*, 
							   it.ds_indicador_tabela, 
							   i.ds_indicador, 
							   ig.ds_indicador_grupo, 
							   CASE WHEN i.fl_periodo = 'N' 
									THEN ''
									ELSE ip.ds_periodo
							   END AS ds_periodo,
							   p.procedimento AS ds_processo
						  FROM indicador.indicador_apresentacao_item iai
						  JOIN indicador.indicador_tabela it 
							ON it.cd_indicador_tabela=iai.cd_indicador_tabela
						  JOIN indicador.indicador i 
							ON i.cd_indicador=it.cd_indicador
						  JOIN indicador.indicador_grupo ig 
							ON ig.cd_indicador_grupo=i.cd_indicador_grupo
						  JOIN indicador.indicador_periodo ip 
							ON ip.cd_indicador_periodo=it.cd_indicador_periodo
						  LEFT JOIN projetos.processos p
							ON p.cd_processo = i.cd_processo
						 WHERE cd_indicador_apresentacao = ".intval($cd_apresentacao)." 
						 ORDER BY nr_ordem ASC
					   ";
			$query = $this->db->query($qr_sql);
			$apresentacao = $query->result_array();
			
			#echo "<PRE>".$qr_sql."</PRE>"; exit;
		}

		$data['cd_apresentacao'] = intval($cd_apresentacao);
		$data['apresentacao']    = $apresentacao;

		if(intval($cd) == 0)
		{
			if(sizeof($apresentacao) > 0)
			{
				$cd = $apresentacao[0]['cd_indicador_tabela'];
			}
			else
			{
				echo "Indicador não informado";
				exit;
			}
		}
*/
		
		$args["cd_indicador_tabela"] = intval($cd);
		
		$this->Indicador_tabela_model->apresentacaoListarIndicador($result, $args);
		$data['ar_indicador'] = $result->result_array();		
		
		$this->Indicador_tabela_model->info_indicador_tabela($result, $args);
		$data['row'] = $result->row_array();

		$data['codigo'] = intval($cd);
		
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
		
		$this->load->view( 'indicador/apresentacao/detalhe', $data );
	}
}
