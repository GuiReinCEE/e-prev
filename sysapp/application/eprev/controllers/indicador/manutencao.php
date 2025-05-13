<?php
class manutencao extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    private function get_opcao()
    {
    	return array(
    		array('value' => 'S', 'text' => 'Sim'),
    		array('value' => 'N', 'text' => 'Não')
    	);
    }
	
    function index($cd_grupo = 0, $cd_tipo = '', $nr_ano = 0)
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

		$data['tipo'] = array(
			array('value' => 'G', 'text' => 'Gestão'),
			array('value' => 'A', 'text' => 'Auxiliar')
		);

		$this->Indicador_tabela_model->grupoCombo($result, $args);
		$data['ar_grupo'] = $result->result_array();	
		
		$this->Indicador_tabela_model->controlesCombo($result, $args);
		$data['ar_controle'] = $result->result_array();			
		
		$this->Indicador_tabela_model->processoCombo($result, $args);
		$data['ar_processo'] = $result->result_array();		

		$data['fl_filtro'] = $this->get_opcao();
		
        $data['cd_tipo'] = $cd_tipo;

		$this->load->view('indicador/manutencao/index.php', $data);
    }	
	
    function listar()
    {
		CheckLogin();
		$this->load->model('indicador/Indicador_tabela_model');

		$result = null;
		$data   = Array();
		$args   = Array();	

		$args['cd_indicador_grupo']    = intval($this->input->post('cd_indicador_grupo', true));
		$args['cd_indicador_periodo']  = intval($this->input->post('cd_indicador_periodo', true));
		$args['cd_indicador_controle'] = intval($this->input->post('cd_indicador_controle', true));
		$args['cd_processo']           = intval($this->input->post('cd_processo', true));
        $args['cd_tipo']               = $this->input->post('cd_tipo', true);
		$args['fl_igp'] 			   = $this->input->post('fl_igp', TRUE);
		$args['fl_poder'] 			   = $this->input->post('fl_poder', TRUE);

        manter_filtros($args);
		
		$this->Indicador_tabela_model->manutencaoListar( $result, $args );
		$data['collection'] = $result->result_array();
		$this->load->view('indicador/manutencao/index_result', $data);
    }	

	function detalhe($cd = 0, $cd_manutencao = 0)
	{
		CheckLogin();

		// apresentação
		$manutencao = array();
		if(intval($cd_manutencao) > 0)
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
						  FROM indicador.indicador_manutencao_item iai
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
						 WHERE cd_indicador_manutencao = ".intval($cd_manutencao)."
						 ORDER BY nr_ordem ASC
					   ";
			$query = $this->db->query($qr_sql);
			$manutencao = $query->result_array();
			
			#echo "<PRE>".$qr_sql."</PRE>"; exit;
		}

		$data['cd_manutencao'] = intval($cd_manutencao);
		$data['manutencao']    = $manutencao;

		if(intval($cd) == 0)
		{
			if(sizeof($manutencao) > 0)
			{
				$cd = $manutencao[0]['cd_indicador_tabela'];
			}
			else
			{
				echo "Indicador não informado";
				exit;
			}
		}

		$qr_sql = "	
					SELECT DISTINCT i.*, 
						   c.ds_indicador_controle, 
						   u.ds_indicador_unidade_medida, 
						   CASE WHEN i.fl_periodo = 'N' 
								THEN ''
								ELSE ip.ds_periodo
						   END AS ds_periodo,
						   ig.ds_indicador_grupo,
						   p.procedimento AS ds_processo
					  FROM indicador.indicador i 
					  JOIN indicador.indicador_grupo ig 
					    ON ig.cd_indicador_grupo=i.cd_indicador_grupo
					  JOIN indicador.indicador_controle c 
					    ON c.cd_indicador_controle=i.cd_indicador_controle 
					  JOIN indicador.indicador_unidade_medida u 
					    ON u.cd_indicador_unidade_medida=i.cd_indicador_unidade_medida
					  JOIN indicador.indicador_tabela it 
					    ON it.cd_indicador=i.cd_indicador
					  JOIN indicador.indicador_periodo ip 
					    ON it.cd_indicador_periodo=ip.cd_indicador_periodo
					  LEFT JOIN projetos.processos p
						ON p.cd_processo = i.cd_processo						
					 WHERE it.cd_indicador_tabela = ".intval($cd)."
		          ";
		$query = $this->db->query($qr_sql);
		$row = $query->row_array();

		$data['codigo'] = intval($cd);
		$data['row']    = $row;

		
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
		
		$this->load->view( 'indicador/manutencao/detalhe', $data );
	}
}
