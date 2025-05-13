<?php
class avaliacao extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
    
    function comite_media($cd_avaliacao_capa)
    {
    	$data = Array();
		
		CheckLogin();
    	
		$this->load->model( 'projetos/avaliacao_model', 'dbModel' );
		$data['capa']  = $this->dbModel->carregar_avaliacao_capa($cd_avaliacao_capa);
		
		if((count($data['capa']) > 0) and (intval($data['capa']['cd_avaliacao_capa']) > 0))
		{
			$cd_avaliacao_capa = intval($data['capa']['cd_avaliacao_capa']);
			
			$data['comite_componentes'] = $this->dbModel->listar_comite(intval($cd_avaliacao_capa));
			
			#### Grau do Avaliado ####
			$avaliado = $this->dbModel->listar_competencia_institucional_avaliado(intval($cd_avaliacao_capa));
			$data['avaliado'] = Array();
			foreach($avaliado as $item)
			{
				$data['avaliado'][$item['cd_comp_inst']] = $item['grau'];
			}
			
			#### Grau do Superior ####
			$superior = $this->dbModel->listar_competencia_institucional_superior(intval($cd_avaliacao_capa));
			$data['superior'] = Array();
			foreach($superior as $item)
			{
				$data['superior'][$item['cd_comp_inst']] = $item['grau'];
			}

			#### Grau do Comitê Individual ####
			$data['comite_avaliador'] = $this->dbModel->listar_competencia_institucional_comite_avaliacao(intval($cd_avaliacao_capa));
			
			$ar_seq = seq_caracter(count($data['comite_avaliador']));
			
			$data['comite'] = Array();
			$nr_conta = 0;
			foreach($data['comite_avaliador'] as $item)
			{
				$data['comite_avaliador'][$nr_conta]['seq'] = $ar_seq[$nr_conta];
				
				$comite = $this->dbModel->listar_competencia_institucional_comite(intval($item['cd_avaliacao']));
				foreach($comite as $subitem)
				{
					$data['comite'][intval($item['cd_avaliacao'])][$subitem['cd_comp_inst']] = $subitem['grau'];
				}			
				$nr_conta++;
			}		

			#### Média do Comitê ####
			$data['comite_media'] = $this->dbModel->listar_competencia_institucional_comite_media(intval($cd_avaliacao_capa));
			
			$this->load->view('servico/avaliacao/comite_media', $data);
		}
		else
		{
			exibir_mensagem("AVALIAÇÃO NÃO ENCONTRADA");
		}
    }

    function competencia_especifica($cd_avaliacao_capa)
    {
		$data = Array();
		
		CheckLogin();
    	
		$this->load->model( 'projetos/avaliacao_model', 'dbModel' );
		$data['capa']  = $this->dbModel->carregar_avaliacao_capa($cd_avaliacao_capa);
		
		if((count($data['capa']) > 0) and (intval($data['capa']['cd_avaliacao_capa']) > 0))
		{
			$cd_avaliacao_capa = intval($data['capa']['cd_avaliacao_capa']);
			
			#### Grau do Avaliado ####
			$data['avaliado'] = $this->dbModel->listar_competencia_especifica_avaliado(intval($cd_avaliacao_capa));
			
			#### Grau do Superior ####
			$superior = $this->dbModel->listar_competencia_especifica_superior(intval($cd_avaliacao_capa));
			$data['superior'] = Array();
			foreach($superior as $item)
			{
				$data['superior'][$item['cd_comp_espec']] = $item['grau'];
			}

			$this->load->view('servico/avaliacao/competencia_especifica', $data);
		}
		else
		{
			exibir_mensagem("AVALIAÇÃO NÃO ENCONTRADA");
		}    	
    }
    
    function responsabilidade($cd_avaliacao_capa)
    {
		$data = Array();
		
		CheckLogin();
    	
		$this->load->model( 'projetos/avaliacao_model', 'dbModel' );
		$data['capa']  = $this->dbModel->carregar_avaliacao_capa($cd_avaliacao_capa);
		
		if((count($data['capa']) > 0) and (intval($data['capa']['cd_avaliacao_capa']) > 0))
		{
			$cd_avaliacao_capa = intval($data['capa']['cd_avaliacao_capa']);
			
			#### Grau do Avaliado ####
			$data['avaliado'] = $this->dbModel->listar_responsabilidade_avaliado(intval($cd_avaliacao_capa));
			
			#### Grau do Superior ####
			$superior = $this->dbModel->listar_responsabilidade_superior(intval($cd_avaliacao_capa));
			$data['superior'] = Array();
			foreach($superior as $item)
			{
				$data['superior'][$item['cd_responsabilidade']] = $item['grau'];
			}

			$this->load->view('servico/avaliacao/responsabilidade', $data);
		}
		else
		{
			exibir_mensagem("AVALIAÇÃO NÃO ENCONTRADA");
		}     	
    }
}
