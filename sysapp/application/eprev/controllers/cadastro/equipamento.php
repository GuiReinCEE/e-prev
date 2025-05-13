<?php
class equipamento extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index($tipo="1", $cod_divisao="", $situacao="SIT1")
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		$args=array();
		
		if(gerencia_in(array('GTI')) OR ($this->session->userdata('usuario') == "mkerecki"))
		{ 
			
			$data['tipo']        = (resgatar_filtro('tipo_equipamento') ? resgatar_filtro('tipo_equipamento') : $tipo);
			$data['cod_divisao'] = (resgatar_filtro('cod_divisao') ? resgatar_filtro('cod_divisao') : $cod_divisao);
			$data['situacao']    = (resgatar_filtro('situacao') ? resgatar_filtro('situacao') : $situacao);

			$this->Equipamentos_model->tipoEquipamento( $result, $args );
			$data['tipo_equipamento_dd'] = $result->result_array();		

			$this->Equipamentos_model->gerenciaEquipamento( $result, $args );
			$data['divisao_dd'] = $result->result_array();		
			
			$this->Equipamentos_model->situacaoEquipamento( $result, $args );
			$data['situacao_dd'] = $result->result_array();	

			$this->Equipamentos_model->sisOperacionalEquipamento( $result, $args );
			$data['sistema_operacional_categoria_dd'] = $result->result_array();	

			$this->Equipamentos_model->processadorEquipamento( $result, $args );
			$data['processador_categoria_dd'] = $result->result_array();			
			
			$this->Equipamentos_model->salaEquipamento( $result, $args );
			$data['sala_dd'] = $result->result_array();
			
			$this->Equipamentos_model->memoriaEquipamento( $result, $args );
			$data['memoria_dd'] = $result->result_array();


			$data['login_dd'][]=array( 'value'=>'', 'text'=>'Todos' );
			$data['login_dd'][]=array( 'value'=>'S', 'text'=>'Sim' );
			$data['login_dd'][]=array( 'value'=>'N', 'text'=>'Não' );

			$data['cpuscanner_dd'][]=array( 'value'=>'', 'text'=>'Todos' );
			$data['cpuscanner_dd'][]=array( 'value'=>'S', 'text'=>'Sim' );
			$data['cpuscanner_dd'][]=array( 'value'=>'N', 'text'=>'Não' );

			$this->load->view('cadastro/equipamento/index.php', $data);		
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}

    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Equipamentos_model');
		if(gerencia_in(array('GTI')) OR ($this->session->userdata('usuario') == "mkerecki"))
		{ 
			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();

			$args["tipo_equipamento"]              = intval($this->input->post("tipo_equipamento", TRUE));
			$args["cod_divisao"]                   = $this->input->post( "cod_divisao", TRUE );
			$args["cd_sala"]                       = $this->input->post( "cd_sala", TRUE );
			$args["situacao"]                      = $this->input->post( "situacao", TRUE );
			$args["qt_memoria"]                    = $this->input->post( "qt_memoria", TRUE );
			$args["sistema_operacional_categoria"] = $this->input->post( "sistema_operacional_categoria", TRUE );
			$args["login_rede"]                    = $this->input->post( "login_rede", TRUE );
			$args["processador_categoria"]         = $this->input->post( "processador_categoria", TRUE );
			$args["cpuscanner"]                    = $this->input->post( "cpuscanner", TRUE );

			manter_filtros($args);
			
			// --------------------------
			// listar ...

			$this->Equipamentos_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('cadastro/equipamento/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function detalhe($codigo_patrimonio = 0)
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		
		if(gerencia_in(array('GTI')) OR ($this->session->userdata('usuario') == "mkerecki"))
		{
			$data['codigo_patrimonio'] = $codigo_patrimonio;			
			$args['codigo_patrimonio'] = $codigo_patrimonio;
			
			if(!$this->Equipamentos_model->existeEquipamento($codigo_patrimonio))
			{
				$qr_sql = "
							SELECT *, 
							       0 AS qt_memoria, 
							       0 AS qt_espaco_total, 
							       0 AS qt_espaco_livre, 
							       0 AS qt_espaco_usado 
							  FROM projetos.equipamentos
							 LIMIT 1
		                  ";
				
				$query = $this->db->query($qr_sql);
				$fields = $query->field_data();
				
				$row = Array();
				foreach($fields as $field)
				{
					$row[$field->name] = '';
				}	
				$data['row'] = $row;							
			}
			else
			{
				$this->Equipamentos_model->detalhe($result, $args);
				$data['row'] = $result->row_array();
			}
			
			$this->Equipamentos_model->tipoEquipamento($result, $args);
			$data['ar_tipo_equipamento'] = $result->result_array();	
			
			$this->Equipamentos_model->gerenciaEquipamento($result, $args);
			$data['ar_divisao'] = $result->result_array();					
			
			$this->Equipamentos_model->usuarioEquipamento($result, $args);
			$data['ar_usuario'] = $result->result_array();				
	
			$this->Equipamentos_model->situacaoEquipamento($result, $args);
			$data['ar_situacao'] = $result->result_array();	
			
			$this->Equipamentos_model->sisOperacionalEquipamento($result, $args);
			$data['ar_sistema_operacional_categoria'] = $result->result_array();			
	
			$this->Equipamentos_model->processadorEquipamento($result, $args);
			$data['ar_processador_categoria'] = $result->result_array();	

			$this->Equipamentos_model->memoriaEquipamento( $result, $args );
			$data['ar_memoria_ram_categoria'] = $result->result_array();
			
			
			$this->load->view('cadastro/equipamento/detalhe',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
	
    function meu_computador()
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		$args['cd_usuario'] = usuario_id();
		$this->Equipamentos_model->meu_computador($result, $args);
		$data['row'] = $result->row_array();
		$this->load->view('cadastro/equipamento/meu_computador',$data);
    }

    function quadro_resumo($cd_divisao = "")
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		
		$data = Array();
		$args = Array();
		
		
		$args['cd_divisao'] = $cd_divisao;
		$data['cd_divisao'] = $cd_divisao;
		
		$this->Equipamentos_model->gerenciaEquipamento($result, $args);
		$data['ar_divisao'] = $result->result_array();			
		
		$this->Equipamentos_model->resumoTipoEquipamento($result, $args);
		$data['ar_tipo_equipamento'] = $result->result_array();
		
		$this->Equipamentos_model->resumoSituacaoEquipamento($result, $args);
		$data['ar_situacao_equipamento'] = $result->result_array();	

		$this->Equipamentos_model->resumoDiscoEquipamento($result, $args);
		$data['ar_disco_equipamento'] = $result->result_array();		
		
		$this->Equipamentos_model->resumoMemoriaEquipamento($result, $args);
		$data['ar_memoria_equipamento'] = $result->result_array();		

		$this->Equipamentos_model->resumoProcessadorEquipamento($result, $args);
		$data['ar_processador_equipamento'] = $result->result_array();

		$this->Equipamentos_model->resumoSistemaOperacionalEquipamento($result, $args);
		$data['ar_sistema_operacional_equipamento'] = $result->result_array();		
		
		
		
		$this->load->view('cadastro/equipamento/quadro_resumo',$data);
    }	

    function salvar()
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$args['codigo_patrimonio']             = intval($this->input->post("codigo_patrimonio", TRUE));
			$args['dt_cadastro']                   = $this->input->post("dt_cadastro", TRUE);
			$args['cod_divisao']                   = $this->input->post("cod_divisao", TRUE);
			$args['usuario']                       = intval($this->input->post("usuario", TRUE));
			$args['nome_computador']               = $this->input->post("nome_computador", TRUE);
			$args['ip']                            = $this->input->post("ip", TRUE);
			$args['tipo_equipamento']              = $this->input->post("tipo_equipamento", TRUE);
			$args['situacao']                      = $this->input->post("situacao", TRUE);
			$args['sistema_operacional_categoria'] = $this->input->post("sistema_operacional_categoria", TRUE);
			$args['processador_categoria']         = $this->input->post("processador_categoria", TRUE);
			$args['memoria_ram']                   = intval($this->input->post("memoria_ram", TRUE));
			$args['espaco_disco_total']                  = intval($this->input->post("espaco_disco_total", TRUE));
			$args['espaco_disco_livre']                  = intval($this->input->post("espaco_disco_livre", TRUE));
			$args['espaco_disco_usado']                  = intval($this->input->post("espaco_disco_usado", TRUE));
	
			$this->Equipamentos_model->salvar($result, $args);
			
			$this->detalhe($args['codigo_patrimonio']);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }		
	
    function setCPUScannerManual()
    {
		CheckLogin();
		$this->load->model('projetos/Equipamentos_model');
		
		if(gerencia_in(array('GI')))
		{
			$args = Array();
			$args['codigo_patrimonio'] = intval( $this->input->post("codigo_patrimonio", TRUE) );
			$args['cd_usuario'] = usuario_id();
	
			$this->Equipamentos_model->setCPUScannerManual($result, $args);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
}
?>