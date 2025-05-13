<?php
class Pauta_sg extends Controller
{
	var $dir_ini_de;
	var $dir_ini_cd;
	var $dir_ini_cf;
	
	function __construct()
    {
        parent::Controller();
		
		CheckLogin();
		
		/*
		CRIAR LINK NO LINUX
		ln -s [Specific file/directory] [symlink name]

		DELIBERATIVO
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/DE /u/www/eletroceee/pydio/data/DELIBERATIVO/DE
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CF /u/www/eletroceee/pydio/data/DELIBERATIVO/CF
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/OUTROS /u/www/eletroceee/pydio/data/DELIBERATIVO/OUTROS

		FISCAL
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/DE /u/www/eletroceee/pydio/data/FISCAL/DE
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CD /u/www/eletroceee/pydio/data/FISCAL/CD
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/OUTROS /u/www/eletroceee/pydio/data/FISCAL/OUTROS

		DIRETORIA
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CD /u/www/eletroceee/pydio/data/DIRETORIA/CD
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/CF /u/www/eletroceee/pydio/data/DIRETORIA/CF
		ln -s /u/www/eletroceee/pydio/data/DOCUMENTOS_APROVADOS/OUTROS /u/www/eletroceee/pydio/data/DIRETORIA/OUTROS
		
		
		CCI
		ln -s /u/www/eletroceee/pydio/data/CCI /u/www/eletroceee/pydio/data/DELIBERATIVO/CCI
		ln -s /u/www/eletroceee/pydio/data/CCI /u/www/eletroceee/pydio/data/FISCAL/CCI
		ln -s /u/www/eletroceee/pydio/data/CCI /u/www/eletroceee/pydio/data/DIRETORIA/CCI
		
		
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DESENQUADRAMENTO /u/pydio/data/DELIBERATIVO/DESENQUADRAMENTO
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DESENQUADRAMENTO /u/pydio/data/FISCAL/DESENQUADRAMENTO
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DESENQUADRAMENTO /u/pydio/data/DIRETORIA/DESENQUADRAMENTO


		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/REGISTRO-SOLICITACOES /u/pydio/data/DELIBERATIVO/REGISTRO-SOLICITACOES
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/REGISTRO-SOLICITACOES /u/pydio/data/FISCAL/REGISTRO-SOLICITACOES
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/REGISTRO-SOLICITACOES /u/pydio/data/DIRETORIA/REGISTRO-SOLICITACOES


		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DOC-RECEBIDO /u/pydio/data/DELIBERATIVO/DOC-RECEBIDO
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DOC-RECEBIDO /u/pydio/data/FISCAL/DOC-RECEBIDO
		ln -s /u/pydio/data/DOCUMENTOS_APROVADOS/DOC-RECEBIDO /u/pydio/data/DIRETORIA/DOC-RECEBIDO		

		*/		
		
		$this->dir_ini_aprovado = '../eletroceee/pydio/data/DOCUMENTOS_APROVADOS/';
		$this->dir_ini_de       = '../eletroceee/pydio/data/DIRETORIA/';
		$this->dir_ini_cd       = '../eletroceee/pydio/data/DELIBERATIVO/';
		$this->dir_ini_cf       = '../eletroceee/pydio/data/FISCAL/';

        $this->load->plugin('encoding_pi');
    }

    private function get_permissao()
    {
    	if(gerencia_in(array('GRC', 'DE')))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    private function get_permissao_repostas()
    {
    	if(gerencia_in(array('GRC')))
    	{
    		return TRUE;
    	}
    	else if($this->session->userdata('tipo') == 'G')
    	{
    		return TRUE;
    	}
    	else if($this->session->userdata('indic_01') == 'S')
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }

    private function get_sumula()
    {
    	return array(
    		array('value' => 'DE', 'text' => 'Diretoria Executiva'),
    		array('value' => 'CF', 'text' => 'Conselho Fiscal'),
    		array('value' => 'CD', 'text' => 'Conselho Deliberativo'),
    		array('value' => 'IN', 'text' => 'Interventor')
    	);
    }
    
    private function get_tipo_reuniao()
    {
    	return array(
			array('value' => 'E', 'text' => 'Extraordinária'), 
			array('value' => 'O', 'text' => 'Ordinária')
		);
    }

    private function integracao_link($dir, $dt_pauta, $nr_ata, $fl_sumula)
	{
		$dir_aprovado = $this->dir_ini_aprovado;
		
		$dt_data = str_replace('/', '-', $dt_pauta);
		$ar_data = explode('-', $dt_data);

		#### CRIA DIRETORIO ANO ####
		$dir_tmp = $dir.$fl_sumula."/".$ar_data[2];
		if(!is_dir($dir_tmp))
		{
			mkdir($dir_tmp, 0777);
		}
		
		$dir_aprovado = $dir_aprovado.$fl_sumula."/".$ar_data[2];
		if(!is_dir($dir_aprovado))
		{
			mkdir($dir_aprovado, 0777);
		}		

		#### CRIA DIRETORIO DA REUNIAO ####
		$dir_tmp .= '/Reuniao '.$nr_ata.' de '.$dt_data;
		if(!is_dir($dir_tmp))
		{
			mkdir($dir_tmp, 0777);
		}
		
		$dir_aprovado .= '/Reuniao '.$nr_ata.' de '.$dt_data;
		if(!is_dir($dir_aprovado))
		{
			mkdir($dir_aprovado, 0777);
		}		
		
		#### CRIA DIRETORIO DOCUMENTOS DA REUNIAO ####
		$dir_doc = $dir_tmp.'/documentos';
		if(!is_dir($dir_doc))
		{
			mkdir($dir_doc, 0777);
		}

		$dir_aprovado .= '/documentos';
		if(!is_dir($dir_aprovado))
		{
			mkdir($dir_aprovado, 0777);
		}		
		
		return $dir_tmp;
	}

	private function altera_integracao_link($integracao_arq, $dt_pauta, $nr_ata, $fl_sumula)
	{
		$dir_tmp = substr($integracao_arq, 0, strripos($integracao_arq, '/'));
		$data = str_replace('/', '-', $dt_pauta);
		$dir_tmp .= '/Reuniao '.$nr_ata." de ".$data;
		rename($integracao_arq, $dir_tmp);
		
		$dir_aprovado = "";
		if($fl_sumula == "CD")
		{
			$dir_aprovado = str_replace("DELIBERATIVO","DOCUMENTOS_APROVADOS",$integracao_arq);
		}
		elseif($fl_sumula == "CF")
		{
			$dir_aprovado = str_replace("FISCAL","DOCUMENTOS_APROVADOS",$integracao_arq);
		}				
		elseif($fl_sumula == "DE")
		{
			$dir_aprovado = str_replace("DIRETORIA","DOCUMENTOS_APROVADOS",$integracao_arq);
		}
		else
		{
			echo "ERRO"; EXIT;
		}		
		
		$dir_aprov = substr($dir_aprovado, 0, strripos($dir_aprovado, '/'));
		$data = str_replace('/', '-', $dt_pauta);
		$dir_aprov .= '/Reuniao '.$nr_ata." de ".$data;
		rename($dir_aprovado, $dir_aprov);		

		return $dir_tmp;
	}

	public function get_usuarios()
	{
	    $this->load->model('gestao/pauta_sg_model');

	    $cd_gerencia = $this->input->post('cd_gerencia', TRUE);

		foreach($this->pauta_sg_model->get_usuarios($cd_gerencia) as $item)
		{
			$data[] = array(
				'value' => $item['value'],
				'text'  => utf8_encode($item['text'])
			);
		}
		
	    echo json_encode($data);
	}

	public function index()
    {
		if($this->get_permissao())
		{
			$data = array(
				'sumula'       => $this->get_sumula(),
				'tipo_reuniao' => $this->get_tipo_reuniao()
			);
								
			$this->load->view('gestao/pauta_sg/index', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function listar()
    {
    	$this->load->model('gestao/pauta_sg_model');

    	$args = array(
    		'nr_ata'              => $this->input->post('nr_ata', TRUE),
    		'fl_sumula'           => $this->input->post('fl_sumula', TRUE),
    		'dt_pauta_sg_ini'     => $this->input->post('dt_pauta_sg_ini', TRUE),
    		'dt_pauta_sg_fim'     => $this->input->post('dt_pauta_sg_fim', TRUE),
    		'dt_pauta_sg_fim_ini' => $this->input->post('dt_pauta_sg_fim_ini', TRUE),
    		'dt_pauta_sg_fim_fim' => $this->input->post('dt_pauta_sg_fim_fim', TRUE),
    		'fl_aprovado'         => $this->input->post('fl_aprovado', TRUE),
    		'fl_tipo_reuniao'     => $this->input->post('fl_tipo_reuniao', TRUE)
    	);
				
		manter_filtros($args);
		
		$data['collection'] = $this->pauta_sg_model->listar($args);

		$this->load->view('gestao/pauta_sg/index_result', $data);
    }

    public function cadastro($cd_pauta_sg = 0)
	{
		if($this->get_permissao())
		{
			$data = array(
				'sumula'       => $this->get_sumula(),
				'tipo_reuniao' => $this->get_tipo_reuniao()
			);

			if(intval($cd_pauta_sg) == 0)
			{
				$data['row'] = array(
					'cd_pauta_sg'     => intval($cd_pauta_sg),
					'nr_ata'          => '',
					'fl_sumula'       => '',
					'local'           => '',
					'dt_pauta'        => '',
					'hr_pauta'        => '',
					'dt_pauta_sg_fim' => '',
					'hr_pauta_sg_fim' => '',
					'dt_aprovacao'    => '',
					'fl_tipo_reuniao' => ''
				);
			}
			else
			{
				$this->load->model('gestao/pauta_sg_model');

				$data['row'] = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
			}

			$dir = '';

			if(trim($data['row']['fl_sumula']) == 'DE')
			{
				$dir = $this->dir_ini_de;
			}
			else if(trim($data['row']['fl_sumula']) == 'CF')
			{
				$dir = $this->dir_ini_cf;
			}
			else if(trim($data['row']['fl_sumula']) == 'CD')
			{
				$dir = $this->dir_ini_cd;
			}

			echo $this->integracao_link(
				$dir,
				$data['row']['dt_pauta_sg'], 
				$data['row']['nr_ata'],
				trim($data['row']['fl_sumula'])
			);

			$this->load->view('gestao/pauta_sg/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function valida_numero_ata()
    {
    	$this->load->model('gestao/pauta_sg_model');

        $data = $this->pauta_sg_model->valida_numero_ata(
        	$this->input->post('cd_pauta_sg', TRUE), 
        	$this->input->post('nr_ata', TRUE), 
        	$this->input->post('fl_sumula', TRUE)
       	);

        echo json_encode($data);
    }

    private function salvar_integrante_presente($cd_pauta_sg, $fl_colegiado)
    {
		$this->load->model('gestao/pauta_sg_model');

		$this->pauta_sg_model->salvar_integrante_presente($cd_pauta_sg, $fl_colegiado, $this->session->userdata('codigo'));

		$this->pauta_sg_model->atualizar_titulares_presentes($cd_pauta_sg);
    }

    public function atualizar_integrante($cd_pauta_sg, $fl_colegiado)
    {
    	$this->load->model('gestao/pauta_sg_model');

    	$this->pauta_sg_model->delete_integrante_presente($cd_pauta_sg, $this->session->userdata('codigo'));

    	$this->pauta_sg_model->salvar_integrante_presente($cd_pauta_sg, $fl_colegiado, $this->session->userdata('codigo'));

    	$this->pauta_sg_model->atualizar_titulares_presentes($cd_pauta_sg);

    	redirect('gestao/pauta_sg/presentes/'.$cd_pauta_sg, 'refresh');
    }

	public function salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$cd_pauta_sg = $this->input->post('cd_pauta_sg', TRUE);

			$args = array(
				'nr_ata'          => $this->input->post('nr_ata', TRUE),
				'fl_sumula'       => $this->input->post('fl_sumula', TRUE),
				'local'           => $this->input->post('local', TRUE),
				'dt_pauta_sg'     => $this->input->post('dt_pauta', TRUE).' '.$this->input->post('hr_pauta', TRUE),
				'dt_pauta_sg_fim' => $this->input->post('dt_pauta_sg_fim', TRUE).' '.$this->input->post('hr_pauta_sg_fim', TRUE),
				'fl_tipo_reuniao' => $this->input->post('fl_tipo_reuniao', TRUE),
				'integracao_arq'  => '',
				'cd_usuario'      => $this->session->userdata('codigo')
			); 
			
			$fl_sumula = $args['fl_sumula'];

			if(intval($cd_pauta_sg) == 0)
			{
				#### INTEGRAÇÃO LINK ####

				$dir = '';

				if(trim($args['fl_sumula']) == 'DE')
				{
					$dir = $this->dir_ini_de;
				}
				else if(trim($args['fl_sumula']) == 'CF')
				{
					$dir = $this->dir_ini_cf;
				}
				else if(trim($args['fl_sumula']) == 'CD')
				{
					$dir = $this->dir_ini_cd;
				}

				$args['integracao_arq'] = $this->integracao_link(
					$dir,
					$this->input->post('dt_pauta', TRUE), 
					$args['nr_ata'],
					trim($args['fl_sumula'])
				);
				
				$integracao_arq = $args['integracao_arq'];
			
				$fl_tipo_reuniao = trim($args['fl_tipo_reuniao']);

                $cd_pauta_sg = $this->pauta_sg_model->salvar($args);

                $this->salvar_integrante_presente($cd_pauta_sg, $args['fl_sumula']);

				$anexos = $this->pauta_sg_model->anexos_assuntos_removidos($cd_pauta_sg);

				if(count($anexos) > 0)
				{
					if(trim($integracao_arq) != '')
					{
						foreach ($anexos as $key => $item) 
						{
							#copy('../cieprev/up/pauta/'.$item['arquivo'], $integracao_arq.'/documentos/'.$item['nr_item_sumula'].' - '.fixUTF8($item['arquivo_nome']));
							copy('../cieprev/up/pauta/'.$item['arquivo'], $integracao_arq.'/documentos/'.str_pad($item['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($item['arquivo_nome']));
						}
					}
                }

                $pauta_sg = $this->pauta_sg_model->get_pauta($cd_pauta_sg);

				if(trim($args['fl_tipo_reuniao']) == 'O')
				{
					/*
                    $assunto = $this->pauta_sg_model->get_numero_assunto_pauta($cd_pauta_sg);

					$gerente_secretaria    = $this->pauta_sg_model->get_gerente_secretaria();
					$substituto_secretaria = $this->pauta_sg_model->get_substituto_secretaria();

					$args = array(
						'cd_pauta_sg'               => intval($cd_pauta_sg),
						'nr_item_sumula'            => (count($assunto) > 0 ? intval($assunto['nr_item_sumula']) + 1 : 1),
						'ds_pauta_sg_assunto'       => 'Assinatura das Atas Anteriores',
						'nr_tempo'                  => '',
						'cd_gerencia_responsavel'   => 'GRC',
						'cd_usuario_responsavel'    => intval($gerente_secretaria['cd_usuario']),
						'cd_gerencia_substituto'    => 'GRC',
						'cd_usuario_substituto'     => intval($substituto_secretaria['cd_usuario']),
						'cd_diretoria'              => 'PRE',
						'nr_rds'                    => '',
						'nr_ano_rds'                => '',
						'fl_aplica_rds'             => '',
						'ds_decisao'                => '',
						'instancia_aprovacao'       => '',
						'cd_pauta_sg_objetivo'      => '',
                    	'cd_pauta_sg_justificativa' => '',
						'cd_usuario'                => $this->session->userdata('codigo')
					);

					$this->pauta_sg_model->assunto_salvar($args);
					*/

					$pauta_anterior_sg = $this->pauta_sg_model->get_pauta_anterior(
						$cd_pauta_sg, 
						trim($pauta_sg['fl_sumula']), 
						trim($pauta_sg['ds_mes_ano'])
					);

					if(intval($pauta_anterior_sg['fl_pauta_anterior']) == 0)
					{
                        $assunto = $this->pauta_sg_model->get_numero_assunto_pauta($cd_pauta_sg);

						$pauta_anual = $this->pauta_sg_model->get_pauta_sg_anual(
							trim($pauta_sg['fl_sumula']), 
							trim($pauta_sg['ds_mes_ano'])
						);

						foreach ($pauta_anual as $key => $item) 
						{
							$args = array(
								'cd_pauta_sg'               => $cd_pauta_sg,
								'nr_item_sumula'            => (count($assunto) > 0 ? intval($assunto['nr_item_sumula']) + 1 : 1),
								'ds_pauta_sg_assunto'       => $item['ds_assunto'],
								'nr_tempo'                  => $item['nr_tempo'],
								'cd_gerencia_responsavel'   => $item['cd_gerencia_responsavel'],
								'cd_usuario_responsavel'    => $item['cd_responsavel'],
								'cd_gerencia_substituto'    => $item['cd_gerencia_responsavel'],
								'cd_usuario_substituto'     => $item['cd_substituto'],
								'cd_diretoria'              => $item['cd_diretoria'],
								'nr_rds'                    => '',
								'nr_ano_rds'                => '',
								'fl_aplica_rds'             => '',
								'ds_decisao'                => '',
								'instancia_aprovacao'       => '',
								'nr_ordem_fornecimento'     => '',
								'fl_ordem_fornecimento'     => '',
								'cd_pauta_sg_objetivo'      => $item['cd_pauta_sg_objetivo'],
                    			'cd_pauta_sg_justificativa' => $item['cd_pauta_sg_justificativa'],
								'cd_usuario'                => $this->session->userdata('codigo')
							);

							$cd_pauta_sg_assunto = $this->pauta_sg_model->assunto_salvar($args);

							$this->pauta_sg_model->atualiza_pauta_anual(
								$cd_pauta_sg_assunto, 
								$item['cd_pauta_sg_anual_assunto'],
								$this->session->userdata('codigo')
							);
						}
					}
				}

                $pautar_proxima_reuniao = $this->pauta_sg_model->get_assuntos_pautar_proxima_reuniao($pauta_sg['fl_sumula']);
                
                foreach ($pautar_proxima_reuniao as $key => $item) 
                {
                    $assunto = $this->pauta_sg_model->get_numero_assunto_pauta($cd_pauta_sg);

                    $args = array(
                        'cd_pauta_sg'               => $cd_pauta_sg,
                        'nr_item_sumula'            => (count($assunto) > 0 ? intval($assunto['nr_item_sumula']) + 1 : 1),
                        'ds_pauta_sg_assunto'       => $item['ds_pauta_sg_assunto'],
                        'nr_tempo'                  => $item['nr_tempo'],
                        'cd_gerencia_responsavel'   => $item['cd_gerencia_responsavel'],
                        'cd_usuario_responsavel'    => $item['cd_usuario_responsavel'],
                        'cd_gerencia_substituto'    => $item['cd_gerencia_responsavel'],
                        'cd_usuario_substituto'     => $item['cd_usuario_substituto'],
                        'cd_diretoria'              => $item['cd_diretoria'],
                        'nr_rds'                    => $item['nr_rds'],
                        'nr_ano_rds'                => $item['nr_ano_rds'],
                        'fl_aplica_rds'             => $item['fl_aplica_rds'],
                        'ds_decisao'                => '',
                        'instancia_aprovacao'       => '',
                        'nr_ordem_fornecimento'     => $item['nr_ordem_fornecimento'],
						'fl_ordem_fornecimento'     => $item['fl_ordem_fornecimento'],
                        'cd_pauta_sg_objetivo'      => $item['cd_pauta_sg_objetivo'],
                        'cd_pauta_sg_justificativa' => $item['cd_pauta_sg_justificativa'],
                        'cd_usuario'                => $this->session->userdata("codigo")
                    );

                    $cd_pauta_sg_assunto = $this->pauta_sg_model->assunto_salvar($args);

                    $anexo = $this->pauta_sg_model->anexo_listar($item['cd_pauta_sg_assunto']);

                    if(count($anexo) > 0)
                    {
                        foreach ($anexo as $key2 => $item2) 
                        {    
                            $anexo = array(
                                'cd_pauta_sg_assunto'   => $cd_pauta_sg_assunto,
                                'arquivo'               => $item2['arquivo'],
                                'arquivo_nome'          => $item2['arquivo_nome'],
                                'fl_rds'                => $item2['fl_rds'],
                                'nr_ano_rds'            => $item2['nr_ano_rds'],
                                'nr_rds'                => $item2['nr_rds'],
                                'fl_quadro_comparativo' => '',
                                'fl_ordem_fornecimento' => '',
                                'cd_usuario'            => $this->session->userdata('codigo')
                            );
    
                            $this->pauta_sg_model->anexo_salvar($anexo);

                            if(trim($integracao_arq) != '')
	                        {
	                            #copy('../cieprev/up/pauta/'.$item2['arquivo'], $integracao_arq.'/documentos/'.$args['nr_item_sumula'].' - '.fixUTF8($item2['arquivo_nome']));
	                            copy('../cieprev/up/pauta/'.$item2['arquivo'], $integracao_arq.'/documentos/'.str_pad($args['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($item2['arquivo_nome']));
	                        }
                        }
                    }

                    $this->pauta_sg_model->salvar_assunto_pautar_incluido($item['cd_pauta_sg_assunto'], $this->session->userdata('codigo'));
                }

				$dt_pauta = $this->input->post('dt_pauta', TRUE);

				list($dia, $mes, $ano) = explode('/', $dt_pauta);

				$pautar_proxima_reuniao_mes = $this->pauta_sg_model->get_assuntos_pautar_proxima_reuniao_mes(
					intval($mes), 
					intval($ano), 
					$fl_sumula
                );
                
                foreach ($pautar_proxima_reuniao_mes as $key => $item) 
                {
                    $assunto = $this->pauta_sg_model->get_numero_assunto_pauta($cd_pauta_sg);
                    
                    $args = array(
                        'cd_pauta_sg'               => $cd_pauta_sg,
                        'nr_item_sumula'            => (count($assunto) > 0 ? intval($assunto['nr_item_sumula']) + 1 : 1),
                        'ds_pauta_sg_assunto'       => $item['ds_pauta_sg_assunto'],
                        'nr_tempo'                  => $item['nr_tempo'],
                        'cd_gerencia_responsavel'   => $item['cd_gerencia_responsavel'],
                        'cd_usuario_responsavel'    => $item['cd_usuario_responsavel'],
                        'cd_gerencia_substituto'    => $item['cd_gerencia_responsavel'],
                        'cd_usuario_substituto'     => $item['cd_usuario_substituto'],
                        'cd_diretoria'              => $item['cd_diretoria'],
                        'nr_rds'                    => $item['nr_rds'],
                        'nr_ano_rds'                => $item['nr_ano_rds'],
                        'fl_aplica_rds'             => $item['fl_aplica_rds'],
                        'ds_decisao'                => '',
                        'instancia_aprovacao'       => '',
                        'nr_ordem_fornecimento'     => $item['nr_ordem_fornecimento'],
						'fl_ordem_fornecimento'     => $item['fl_ordem_fornecimento'],
                        'cd_pauta_sg_objetivo'      => $item['cd_pauta_sg_objetivo'],
                        'cd_pauta_sg_justificativa' => $item['cd_pauta_sg_justificativa'],
                        'cd_usuario'                => $this->session->userdata("codigo")
                    );
                    
                    $cd_pauta_sg_assunto = $this->pauta_sg_model->assunto_salvar($args);

                    $anexo = $this->pauta_sg_model->anexo_listar($item['cd_pauta_sg_assunto']);

                   if(count($anexo) > 0)
                    {
                        foreach ($anexo as $key2 => $item2) 
                        {    
                            $anexo = array(
                                'cd_pauta_sg_assunto'   => $cd_pauta_sg_assunto,
                                'arquivo'               => $item2['arquivo'],
                                'arquivo_nome'          => $item2['arquivo_nome'],
                                'fl_rds'                => $item2['fl_rds'],
                                'nr_ano_rds'            => $item2['nr_ano_rds'],
                                'nr_rds'                => $item2['nr_rds'],
                                'fl_quadro_comparativo' => $item2['fl_quadro_comparativo'],
                                'fl_ordem_fornecimento' => $item2['fl_ordem_fornecimento'],
                                'cd_usuario'            => $this->session->userdata('codigo')
                            );
    
                            $this->pauta_sg_model->anexo_salvar($anexo);

                            if(trim($integracao_arq) != '')
	                        {
	                            #copy('../cieprev/up/pauta/'.$item2['arquivo'], $integracao_arq.'/documentos/'.$args['nr_item_sumula'].' - '.fixUTF8($item2['arquivo_nome']));
	                            copy('../cieprev/up/pauta/'.$item2['arquivo'], $integracao_arq.'/documentos/'.str_pad($args['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($item2['arquivo_nome']));
	                        }
                        }
                    }

                    $this->pauta_sg_model->salvar_assunto_pautar_incluido($item['cd_pauta_sg_assunto'], $this->session->userdata('codigo'));
                }
			}
			else
			{
				$fl_gerar_pauta = false;

				$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

				if(intval($args['nr_ata']) != intval($row['nr_ata']) OR trim($this->input->post('dt_pauta', TRUE)) != trim($row['dt_pauta']))
				{
					if(file_exists(trim($row['integracao_arq']).'/Pauta_'.intval($row['nr_ata']).'.pdf'))
					{
						@unlink(trim($row['integracao_arq']).'/Pauta_'.intval($row['nr_ata']).'.pdf');
						
						$dir_aprovado = "";
						if($row['fl_sumula'] == "CD")
						{
							$dir_aprovado = str_replace("DELIBERATIVO","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
						}
						elseif($row['fl_sumula'] == "CF")
						{
							$dir_aprovado = str_replace("FISCAL","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
						}				
						elseif($row['fl_sumula'] == "DE")
						{
							$dir_aprovado = str_replace("DIRETORIA","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
						}
						else
						{
							echo "ERRO"; EXIT;
						}
						@unlink(trim($dir_aprovado).'/Pauta_'.intval($row['nr_ata']).'.pdf');

						$fl_gerar_pauta = true;
					}

					$args['integracao_arq'] = $this->altera_integracao_link(
						trim($row['integracao_arq']), 
						trim($this->input->post('dt_pauta', TRUE)), 
						intval($args['nr_ata']),
						trim($row['fl_sumula'])
					);
				}
				else
				{
					$args['integracao_arq'] = $row['integracao_arq'];
				}

				$this->pauta_sg_model->atualizar($cd_pauta_sg, $args);

				if($fl_gerar_pauta)
				{
					$this->pauta_gerar($cd_pauta_sg, TRUE);
				}
			}

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function publicar()
    {
        if($this->get_permissao())
        {
        	$this->load->model('gestao/pauta_sg_model');

        	$cd_pauta_sg = $this->input->post('cd_pauta_sg', TRUE);

            $this->pauta_sg_model->publicar(
            	$cd_pauta_sg,
            	$this->input->post('dt_publicacao_libera', TRUE),
            	$this->session->userdata('codigo')
            );

            redirect('gestao/pauta_sg/cadastro/'.$cd_pauta_sg, 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function assunto($cd_pauta_sg, $cd_pauta_sg_assunto = 0)
	{
		if($this->get_permissao())
		{
            $this->load->model('gestao/pauta_sg_model');

			$data = array(
				'row'        => $this->pauta_sg_model->carrega(intval($cd_pauta_sg)),
				'diretoria'  => $this->pauta_sg_model->get_diretoria(),
				'collection' => $this->pauta_sg_model->assunto_listar(intval($cd_pauta_sg), 'S')
            );

			if(intval($cd_pauta_sg_assunto) == 0)
			{
				$assunto = $this->pauta_sg_model->get_numero_assunto_pauta($cd_pauta_sg);

				$data['drop'] = array(
            		array('value' => 'S', 'text' => 'Sim'),
            		array('value' => 'N', 'text' => 'Não')
            	);

				$data['assunto'] = array(
					'cd_pauta_sg_assunto'     => 0,
					'nr_item_sumula'          => (count($assunto) > 0 ? intval($assunto['nr_item_sumula']) + 1 : 1),
					'ds_pauta_sg_assunto'     => '',
					'nr_tempo'                => '',
					'cd_gerencia_responsavel' => '',
					'cd_usuario_responsavel'  => '',
					'cd_gerencia_substituto'  => '',
					'cd_usuario_substituto'   => '',
					'cd_diretoria'            => '',
                    'instancia_aprovacao'     => '', 
                    'nr_rds'                  => '',
                    'nr_ano_rds'              => '',
                    'fl_ordem_fornecimento'   => 'N',
                    'fl_pendencia_gestao'     => '',
                    'fl_pautar_reuniao'       => '',
                    'fl_proxima_reuniao'      => '',
                    'nr_mes_pautar'           => '',
                    'nr_ano_pautar'           => '',
                    'tp_colegiado_pautar'     => ''
                );

				$data['responsavel'] = array();
                $data['substituto']  = array();
			}
			else
			{
                $data['assunto'] = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

                $cd_gerencia_pendencia = $data['assunto']['cd_gerencia_responsavel'];

                if($data['assunto']['cd_gerencia_pendencia'] != '')
                {
                    $cd_gerencia_pendencia = $data['assunto']['cd_gerencia_pendencia'];
                }

            	$data['responsavel']        = $this->pauta_sg_model->get_usuarios($data['assunto']['cd_gerencia_responsavel']);
                $data['substituto']         = $this->pauta_sg_model->get_usuarios($data['assunto']['cd_gerencia_substituto']);
                $data['usuario_pendencia']  = $this->pauta_sg_model->get_usuarios($cd_gerencia_pendencia);
                $data['drop']        = array(
                    array('value' => 'S', 'text' => 'Sim'),
                    array('value' => 'N', 'text' => 'Não')
                );
                $data['colegiado']   = array(
                    array('value' => 'DE', 'text' => 'DE'),
                    array('value' => 'CD', 'text' => 'CD'),
                    array('value' => 'CF', 'text' => 'CF')
                );
            }

			$this->load->view('gestao/pauta_sg/assunto', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function assunto_salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$cd_pauta_sg         = $this->input->post('cd_pauta_sg', TRUE);
            $cd_pauta_sg_assunto = $this->input->post('cd_pauta_sg_assunto', TRUE);            
			$nr_rds              = $this->input->post('nr_rds', TRUE);
            $nr_ano_rds          = $this->input->post('nr_ano_rds', TRUE);

            $fl_aplica_rds = '';

            if(intval($nr_rds) != '' && intval($nr_ano_rds) != '')
            {
                $fl_aplica_rds = 'S';
            }

            $fl_pendencia_gestao   = $this->input->post('fl_pendencia_gestao', TRUE);
            $fl_pautar_reuniao     = '';
            $tp_colegiado_pautar   = '';
            $fl_proxima_reuniao    = '';
            $nr_mes_pautar         = 0;
            $nr_ano_pautar         = 0;
            $cd_gerencia_pendencia = '';
            $cd_usuario_pendencia  = 0;

            if(trim($fl_pendencia_gestao) == "S")
            {
                $fl_pautar_reuniao     = $this->input->post('fl_pautar_reuniao', TRUE);
                $cd_gerencia_pendencia = $this->input->post('cd_gerencia_pendencia', TRUE);
                $cd_usuario_pendencia  = $this->input->post('cd_usuario_pendencia', TRUE);

                if(trim($fl_pendencia_gestao) == "S" AND trim($fl_pautar_reuniao) == "S")
                {
                    $tp_colegiado_pautar = $this->input->post('tp_colegiado_pautar', TRUE);
                    $fl_proxima_reuniao  = $this->input->post('fl_proxima_reuniao', TRUE);

                    if(trim($fl_pautar_reuniao) == "S" AND trim($fl_proxima_reuniao) == "N")
                    {
                       $nr_mes_pautar = $this->input->post('nr_mes_pautar', TRUE);
                       $nr_ano_pautar = $this->input->post('nr_ano_pautar', TRUE);
                    }
                }
            }

			$args = array(
				'cd_pauta_sg'               => intval($cd_pauta_sg),
				'nr_item_sumula'            => $this->input->post('nr_item_sumula', TRUE),
				'ds_pauta_sg_assunto'       => $this->input->post('ds_pauta_sg_assunto', TRUE),
				'nr_tempo'                  => '',
				'nr_rds'                    => $nr_rds,
                'nr_ano_rds'                => $nr_ano_rds,
                'fl_aplica_rds'             => $fl_aplica_rds,
                'fl_ordem_fornecimento'     => $this->input->post('fl_ordem_fornecimento', TRUE),
				'cd_gerencia_responsavel'   => $this->input->post('cd_gerencia_responsavel', TRUE),
				'cd_usuario_responsavel'    => $this->input->post('cd_usuario_responsavel', TRUE),
				'cd_gerencia_substituto'    => $this->input->post('cd_gerencia_substituto', TRUE),
				'cd_usuario_substituto'     => $this->input->post('cd_usuario_substituto', TRUE),
				'cd_diretoria'              => $this->input->post('cd_diretoria', TRUE),
				'ds_decisao'                => $this->input->post('ds_decisao', TRUE),
				'instancia_aprovacao'       => $this->input->post('instancia_aprovacao', TRUE),
				'cd_pauta_sg_objetivo'      => '',
            	'cd_pauta_sg_justificativa' => '',
            	'nr_ordem_fornecimento'     => '',
            	'fl_pendencia_gestao'       => $fl_pendencia_gestao,
                'fl_pautar_reuniao'         => $fl_pautar_reuniao,
                'tp_colegiado_pautar'       => $tp_colegiado_pautar,
            	'fl_proxima_reuniao'        => $fl_proxima_reuniao,
            	'nr_mes_pautar'             => $nr_mes_pautar,
                'nr_ano_pautar'             => $nr_ano_pautar,
                'cd_gerencia_pendencia'     => $cd_gerencia_pendencia,
                'cd_usuario_pendencia'      => $cd_usuario_pendencia,
				'cd_usuario'                => $this->session->userdata('codigo')
            );

			if(intval($cd_pauta_sg_assunto) == 0)
			{
				$this->pauta_sg_model->assunto_salvar($args);
			}
			else
			{
				$this->pauta_sg_model->assunto_atualizar($cd_pauta_sg_assunto, $args);

				if(trim($args['ds_decisao']) != '')
				{
					$this->apresentacao_gerar($cd_pauta_sg, $cd_pauta_sg_assunto);				
				}
			}

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function set_ordem()
    {
    	$this->load->model('gestao/pauta_sg_model');

		$cd_pauta_sg         = $this->input->post('cd_pauta_sg', TRUE);
		$cd_pauta_sg_assunto = $this->input->post('cd_pauta_sg_assunto', TRUE);

		$args = array(
			'nr_item_sumula' => $this->input->post('nr_item_sumula', TRUE),
			'cd_usuario'     => $this->session->userdata('codigo')
		);

		$anexos = $this->pauta_sg_model->anexo_listar($cd_pauta_sg_assunto);

		if(count($anexos))
		{
			$pauta   = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
			$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

			if(trim($pauta['integracao_arq']) != '')
			{
				foreach ($anexos as $key => $item) 
				{
					#if(file_exists(fixUTF8($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.$item['arquivo_nome'])))
					if(file_exists(fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.$item['arquivo_nome'])))
					{
						$item['arquivo_nome'] = $item['arquivo_nome'];
						$item['cd_usuario']   = $this->session->userdata('codigo');

						$this->pauta_sg_model->anexo_atualizar($item['cd_pauta_sg_assunto_anexo'], $item);
						
						/*
						rename(
							fixUTF8($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.$item['arquivo_nome']), 
							fixUTF8($pauta['integracao_arq'].'/documentos/'.$args['nr_item_sumula'].' - '.$item['arquivo_nome'])
						);
						*/
						rename(
							fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.$item['arquivo_nome']), 
							fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($args['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.$item['arquivo_nome'])
						);						
					}
				}

				#if(file_exists(fixUTF8($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - Apresentação.pdf')))
				if(file_exists(fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - Apresentação.pdf')))
				{
					/*
					rename(
						fixUTF8($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - Apresentação.pdf'), 
						fixUTF8($pauta['integracao_arq'].'/documentos/'.$args['nr_item_sumula'].' - Apresentação.pdf')
					);
					*/
					rename(
						fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - Apresentação.pdf'), 
						fixUTF8($pauta['integracao_arq'].'/documentos/'.str_pad($args['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - Apresentação.pdf')
					);					
				}
			}
		}

		$this->pauta_sg_model->set_ordem($cd_pauta_sg_assunto, $args);
    }

    public function set_resolucao_diretoria()
    {
    	$this->load->model('gestao/pauta_sg_model');

		$this->pauta_sg_model->set_resolucao_diretoria(
			$this->input->post('cd_pauta_sg_assunto', TRUE),
			$this->session->userdata('codigo'),
			$this->input->post('fl_resolucao_diretoria', TRUE)
		);
    }

    public function aprovar_assunto()
    {
    	$this->load->model('gestao/pauta_sg_model');

    	$this->pauta_sg_model->set_aprovar_assunto(
			$this->input->post('cd_pauta_sg_assunto', TRUE),
			$this->session->userdata('codigo'),
			$this->input->post('fl_aprovado', TRUE)
		);
    }

    private function excluir_anexos_assunto($cd_pauta_sg, $cd_pauta_sg_assunto)
    {
    	$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		if(trim($pauta['integracao_arq']) != '')
		{
			$anexos = $this->pauta_sg_model->anexo_listar($cd_pauta_sg_assunto);

			foreach ($anexos as $key => $item) 
			{
				#@unlink($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($item['arquivo_nome']));
				@unlink($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($item['arquivo_nome']));
			}
		}
    }

    public function assunto_excluir($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$this->pauta_sg_model->assunto_excluir(
				$cd_pauta_sg_assunto, 
				$this->session->userdata('codigo')
			);

			$this->excluir_anexos_assunto($cd_pauta_sg, $cd_pauta_sg_assunto);

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

    public function assunto_remover($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$this->pauta_sg_model->assunto_remover(
				$cd_pauta_sg_assunto, 
				$this->session->userdata('codigo')
			);

			$this->excluir_anexos_assunto($cd_pauta_sg, $cd_pauta_sg_assunto);

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function reabrir_assunto($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$this->pauta_sg_model->reabrir_assunto(
				$cd_pauta_sg_assunto, 
				$this->session->userdata('codigo')
			);

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

   	public function presentes($cd_pauta_sg)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$data = array(
				'row'           => $this->pauta_sg_model->carrega(intval($cd_pauta_sg)),
				'collection'    => $this->pauta_sg_model->listar_presentes(intval($cd_pauta_sg))
            );

            $this->load->view('gestao/pauta_sg/presentes', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	private function get_integrante($cd_pauta_sg)
	{
		$this->load->model('gestao/pauta_sg_model');

		$titular    = array();
        $suplente   = array();
        $secretaria = array();

        foreach ($this->pauta_sg_model->get_presentes(intval($cd_pauta_sg)) as $key => $item)
        {
        	if(trim($item['fl_secretaria']) == 'N')
        	{
        		$cd_titular = $item['cd_titular'];
            	$ds_titular = $item['ds_titular'];
            	$ds_cargo   = $item['cargo'];
            	$ds_email   = $item['email'];

            	if(trim($item['fl_titular_presente']) == 'N' AND trim($item['fl_suplente_presente']) == 'S')
            	{
            		$cd_titular = $item['cd_suplente'];
            		$ds_titular = $item['ds_suplente'];
            	}
            	else if(trim($item['fl_titular_presente']) == 'N' AND trim($item['fl_suplente_presente']) == 'N')
            	{
            		$cd_titular = '';
            		$ds_titular = '';
            	}
            	else if(trim($item['fl_titular_presente']) == 'N' AND trim($item['fl_suplente_presente']) == '')
            	{
            		$cd_titular = '';
            		$ds_titular = '';
            	}
            	else if(trim($item['fl_titular_presente']) == 'S' AND intval($item['nr_presidente']) == 1)
            	{
            		$cd_titular = $item['cd_titular'];
            		$ds_titular = $item['ds_titular'].($item['fl_sumula'] == "DE" ? '' : ' - Presidente');
            	}

            	if(intval($cd_titular) > 0)
            	{
            		$titular[] = array(
	        			'cd_titular' => $cd_titular,
	        			'ds_titular' => $ds_titular,
	        			'ds_cargo' => $ds_cargo,
	        			'ds_email' => $ds_email
        			);
            	}

        		$cd_suplente = '';
            	$ds_suplente = '';

            	if(trim($item['fl_titular_presente']) == 'S' AND trim($item['fl_suplente_presente']) == 'S')
            	{
					$cd_suplente = $item['cd_suplente'];
	            	$ds_suplente = $item['ds_suplente'];
            	}

            	if(intval($cd_titular) > 0)
            	{
					$suplente[] = array(
						'cd_suplente' => $cd_suplente,
	            		'ds_suplente' => $ds_suplente
	        		);
            	}
        	}
        	else if(trim($item['fl_titular_presente']) == 'S')
        	{
            	$secretaria[] = array(
            		'cd_secretaria' => $item['cd_titular'],
            		'ds_secretaria' => $item['ds_titular']
            	);
        	}	
        }

        $result = array(
        	'titular'    => $titular,
        	'suplente'   => $suplente,
        	'secretaria' => $secretaria
        );

        #echo "<pre>";        print_r($result);        exit;

    	return $result;
	}

	public function salvar_presente()
	{
		$this->load->model('gestao/pauta_sg_model');

		$cd_pauta_sg_integrante_presente = $this->input->post('cd_pauta_sg_integrante_presente', TRUE);
		$fl_salvar 						 = $this->input->post('fl_salvar', TRUE);

		$this->pauta_sg_model->salvar_presente($cd_pauta_sg_integrante_presente, $fl_salvar, $this->session->userdata('codigo'));
	}

	public function salvar_presidente()
	{
		$this->load->model('gestao/pauta_sg_model');

		$cd_pauta_sg_integrante_presente = $this->input->post('cd_pauta_sg_integrante_presente', TRUE);
		$fl_presidente					 = $this->input->post('fl_presidente', TRUE);

		$this->pauta_sg_model->salvar_presidente($cd_pauta_sg_integrante_presente, $fl_presidente, $this->session->userdata('codigo'));
	}

	public function enviar($cd_pauta_sg)
	{
		if($this->get_permissao())
		{
			$this->load->model(array(
				'gestao/pauta_sg_model',
				'projetos/eventos_email_model'
			));

			$cd_evento = 286;

			$email = $this->eventos_email_model->carrega($cd_evento);

			$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

			$collection = $this->pauta_sg_model->assunto_listar(intval($cd_pauta_sg), 'S', 'N');

			$tags = array('[FL_COLEGIADO]', '[NR_ATA]');
			$subs = array($row['fl_sumula'], $row['nr_ata']);
			
			$assunto = str_replace($tags, $subs, $email['assunto']);

			$cd_usuario = $this->session->userdata('codigo');

			$args = array(
				'de'      => 'Pauta SG',
				'assunto' => $assunto,
				'para'    => '',
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => ''
			);

			foreach ($collection as $key => $item) 
			{
				$tags = array('[NR_PAUTA]', '[DESCRICAO]', '[DT_LIMITE]', '[LINK]');
				$subs = array(
					$item['nr_item_sumula'], 
					$item['ds_pauta_sg_assunto'], 
					$row['dt_limite'],
					site_url('gestao/pauta_sg/responder/'.$cd_pauta_sg.'/'.$item['cd_pauta_sg_assunto'])
				);
				
				$texto = str_replace($tags, $subs, $email['email']);

				$args['para']  = trim($item['ds_email_responsavel']).';'.trim($item['ds_email_substituto']);
				$args['texto'] = trim($texto);

				$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);
			}

			$this->pauta_sg_model->enviar(
				$cd_pauta_sg,
				$this->session->userdata('codigo')
			);

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function encerrar($cd_pauta_sg)
	{
		if($this->get_permissao())
		{
            $this->load->model('gestao/pauta_sg_model');
            
            $assuntos_pautar = $this->pauta_sg_model->listar_assuntos_pautar($cd_pauta_sg);

            foreach ($assuntos_pautar as $key => $item)
            {
                $args = array(
                   'cd_pauta_sg_assunto'        => $item['cd_pauta_sg_assunto'],
                   'cd_pauta_sg_assunto_pautar' => $item['cd_pauta_sg_assunto_pautar'],
                   'cd_usuario'                 => $this->session->userdata('codigo')
                );

                $this->pauta_sg_model->salvar_assuntos_pautar($args);
            }

            $assuntos_pendencia_gestao = $this->pauta_sg_model->listar_assuntos_pendencia_gestao($cd_pauta_sg);

            foreach ($assuntos_pendencia_gestao as $key => $item)
            {
                $args = array(
                   'cd_pauta_sg_assunto'                => $item['cd_pauta_sg_assunto'],
                   'cd_pauta_sg_assunto_pendencia'      => $item['cd_pauta_sg_assunto_pendencia'],
                   'cd_reuniao_sistema_gestao_tipo'     => $item['cd_reuniao_sistema_gestao_tipo'],
                   'cd_superior'                        => $item['cd_diretoria'],
                   'dt_reuniao'                         => $item['dt_pauta_sg'],
                   'ds_item'                            => 'Pauta '.$item['nr_pauta_sg'].' item '.$item['nr_item_sumula'].' - '.$item['ds_pauta_sg_assunto'],
                   'ds_pendencia_gestao_acompanhamento' => 'Pendência gerada pela Pauta '.$item['nr_pauta_sg'].' item '.$item['nr_item_sumula'],
                   'cd_gerencia_pendencia'              => $item['cd_gerencia_pendencia'],
                   'cd_usuario_pendencia'               => $item['cd_usuario_pendencia'],
                   'cd_usuario'                         => $this->session->userdata('codigo')
                );

                $cd_pendencia_gestao = $this->pauta_sg_model->salvar_assuntos_pendencia_gestao($args);

                $this->pauta_sg_model->salvar_assuntos_pendencia_gestao_acompanhamento($cd_pendencia_gestao, $args);
            }

			$this->pauta_sg_model->encerrar(
				$cd_pauta_sg,
				$this->session->userdata('codigo')
			);

			$this->pauta_gera_rds($cd_pauta_sg);

			$this->pauta_gerar($cd_pauta_sg, TRUE);

			redirect('gestao/pauta_sg/cadastro/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	private function pauta_gera_rds($cd_pauta_sg)
	{
        $this->load->model('gestao/pauta_sg_model');

		$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

		$assunto = $this->pauta_sg_model->assunto_listar(intval($cd_pauta_sg), 'S');

		foreach ($assunto as $key => $item) 
		{
			if(trim($item['fl_aplica_rds']) == 'S')
			{
				$arquivo = $this->pauta_sg_model->anexo_listar(intval($item['cd_pauta_sg_assunto']), 'S');

				if(count($arquivo) > 0)
				{
					$controle_rds = $this->pauta_sg_model->get_controle_rds(intval($item['cd_pauta_sg_assunto']));

					if(count($controle_rds) == 0)
					{
						$args = array(
							'cd_pauta_sg_assunto' => intval($item['cd_pauta_sg_assunto']),
							'ds_controle_rds'     => trim($item['ds_pauta_sg_assunto']),
							'arquivo'             => trim($arquivo[0]['arquivo']),
							'arquivo_nome'        => trim($arquivo[0]['arquivo_nome']),
							'nr_ano_rds'          => trim($arquivo[0]['nr_ano_rds']),
							'nr_rds'              => trim($arquivo[0]['nr_rds']),
							'nr_ata'              => intval($pauta['nr_ata']), 
							'dt_reuniao'          => $pauta['dt_pauta_sg'],
							'fl_restrito'         => trim($item['fl_rds_restrita']),
							'cd_usuario'          => intval($item['cd_usuario_encerramento'])
						);

						$cd_controle_rds = $this->pauta_sg_model->controle_rds_salvar($args);

						$args = array(
							'cd_controle_rds' => $cd_controle_rds,
							'cd_gerencia'     => trim($item['cd_gerencia_responsavel']),
							'cd_usuario'      => intval($item['cd_usuario_encerramento'])
						); 

						$this->pauta_sg_model->controle_rds_gerencia_salvar($args);

						if(trim($item['cd_gerencia_responsavel']) != trim($item['cd_gerencia_substituto']))
						{
							$args['cd_gerencia'] = trim($item['cd_gerencia_substituto']);

							$this->pauta_sg_model->controle_rds_gerencia_salvar($args);
						}

						copy('../cieprev/up/pauta/'.$arquivo[0]['arquivo'], '../cieprev/up/controle_rds/'.$arquivo[0]['arquivo']);
					}
				}
			}
		}
	}

	public function reabrir($cd_pauta_sg)
	{
		if($this->get_permissao())
		{
            $this->load->model('gestao/pauta_sg_model');
            
            $assuntos_pautar = $this->pauta_sg_model->listar_assuntos_pautar($cd_pauta_sg);

            foreach ($assuntos_pautar as $key => $item)
            {
                $args = array(
                   'cd_pauta_sg_assunto' => $item['cd_pauta_sg_assunto'],
                   'cd_usuario'          => $this->session->userdata('codigo')
                );
    
                $this->pauta_sg_model->atualizar_assuntos_pautar($args);
            }

			$this->pauta_sg_model->reabrir(
				$cd_pauta_sg,
				$this->session->userdata('codigo')
			);

			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function enviar_colegiado($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		$this->load->model(array(
			'gestao/pauta_sg_model',
			'projetos/eventos_email_model'
		));

		$cd_evento = 290;

		$email = $this->eventos_email_model->carrega($cd_evento);

		$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
		$assunto = $this->pauta_sg_model->assunto_carrega($cd_pauta_sg_assunto);

		$tags = array('[DS_LINK_DIRETO]', '[LINK_DIRETO]', '[NR_ATA]', '[DS_TIPO_REUNIAO]');
		
		$acao = 'adicionado';

		if(trim($assunto['dt_reaberto']) != '')
		{
			$acao = 'atualizado';
		}

		$ds_link_direto = 'Foi '.$acao.' o material no item '.$assunto['nr_item_sumula'].' - '.$assunto['ds_pauta_sg_assunto'].' da pauta '.$row['nr_ata'].'. Clique neste link para acesso aos documentos desta pauta: ';
		
		$link_direto    = 'https://www.fundacaofamiliaprevidencia.com.br/link/?p='.$row['cd_pauta_sg_md5'];
		$subs = array($ds_link_direto, $link_direto, $row['nr_ata'], $row['ds_tipo_reuniao']);
		
		$assunto = str_replace($tags, $subs, $email['assunto']);
			
		$texto = str_replace($tags, $subs, $email['email']);

		$cd_usuario = $this->session->userdata('codigo');

		$para = '';

		if(trim($row['fl_sumula']) == 'DE')
		{
			$para = 'de@familiaprevidencia.com.br';
		}
		else if(trim($row['fl_sumula']) == 'CF')
		{
			$para = 'cf@familiaprevidencia.com.br';
		}
		else if(trim($row['fl_sumula']) == 'CD')
		{
			$para = 'cd@familiaprevidencia.com.br';
		}

		$args = array(
			'de'      => 'Pauta SG',
			'assunto' => $assunto,
			'para'    => $para,
			'cc'      => $email['cc'],
			'cco'     => $email['cco'],
			'texto'   => $texto
		);

		$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

		$this->pauta_sg_model->enviar_colegiado(intval($cd_pauta_sg), intval($cd_usuario));

		//redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
	}

	public function anexo($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$data = array(
				'row'        => $this->pauta_sg_model->carrega(intval($cd_pauta_sg)),
				'assunto'    => $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto)),
				'collection' => $this->pauta_sg_model->anexo_listar($cd_pauta_sg_assunto)
			);

			$this->load->view('gestao/pauta_sg/anexo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function anexo_salvar()
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$cd_pauta_sg         = $this->input->post('cd_pauta_sg', TRUE);
			$cd_pauta_sg_assunto = $this->input->post('cd_pauta_sg_assunto', TRUE);

			$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
			
			$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));		
			
			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

			$args = array(
				'cd_pauta_sg_assunto'   => intval($cd_pauta_sg_assunto),
				'arquivo_nome'          => '',
				'arquivo'               => '',
				'fl_rds'                => 'N',
				'nr_ano_rds'            => '',
                'nr_rds'                => '',
                'fl_ordem_fornecimento' => '',
                'fl_quadro_comparativo' => '',
				'cd_usuario'            => $this->session->userdata('codigo')
			);
		
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;

				while($nr_conta < $qt_arquivo)
				{
					$args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
					$args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
					
					$this->pauta_sg_model->anexo_salvar($args);
					
					$nr_conta++;
					
					#### INTEGRAÇÃO LINK ####
					if(is_dir($pauta['integracao_arq']))
					{
						#copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($args['arquivo_nome']));
						copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($args['arquivo_nome']));
					}
				}
			}		
			
			redirect('gestao/pauta_sg/assunto/'.$cd_pauta_sg, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function anexo_excluir($cd_pauta_sg, $cd_pauta_sg_assunto, $cd_pauta_sg_assunto_anexo)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
			
			$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

			$anexo = $this->pauta_sg_model->anexo_carrega(intval($cd_pauta_sg_assunto_anexo));	

			$this->pauta_sg_model->anexo_excluir($cd_pauta_sg_assunto_anexo, $this->session->userdata('codigo'));	

			if(trim($pauta['integracao_arq']) != '')
			{
				#@unlink($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($anexo['arquivo_nome']));
				@unlink($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($anexo['arquivo_nome']));
			}
		
			redirect('gestao/pauta_sg/anexo/'.$cd_pauta_sg.'/'.$cd_pauta_sg_assunto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function pauta($cd_pauta_sg)
	{
		$this->pauta_gerar($cd_pauta_sg, TRUE);

		$arq = $this->pauta_gerar($cd_pauta_sg, FALSE);
		
		header('Location: '.base_url().'up/pauta/'.$arq);
	}

	private function pauta_gerar($cd_pauta_sg, $fl_autoatendimento = FALSE)
	{
		if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

			$collection = $this->pauta_sg_model->assunto_listar(intval($cd_pauta_sg), 'S');

			$this->load->plugin('fpdf');
				
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');				
			$ob_pdf->SetNrPag(true);
	        $ob_pdf->SetMargins(10, 14, 5);
	        $ob_pdf->header_exibe = true;
	        $ob_pdf->header_logo = true;
	        $ob_pdf->header_titulo = true;
	        $ob_pdf->header_titulo_texto = '';

	        $ob_pdf->SetLineWidth(0);
	        $ob_pdf->SetDrawColor(0, 0, 0);

	        $ob_pdf->AddPage();
	        $ob_pdf->SetY($ob_pdf->GetY() + 1);
	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(190, 4.5, 'PAUTA PARA REUNIÃO', 0, 'C');

			if(trim($row['fl_sumula']) == 'DE')
	        {
	        	$ob_pdf->MultiCell(190, 4.5, 'DIRETORIA EXECUTIVA', 0, 'C');
	        }
	        else if(trim($row['fl_sumula']) == 'CF')
	        {
	        	$ob_pdf->MultiCell(190, 4.5, 'CONSELHO FISCAL', 0, 'C');
	        }
	        else if(trim($row['fl_sumula']) == 'IN')
	        {
	        	$ob_pdf->MultiCell(190, 4.5, 'INTERVENTOR', 0, 'C');
	        }			
	        else 
	        {
	        	$ob_pdf->MultiCell(190, 4.5, 'CONSELHO DELIBERATIVO', 0, 'C');
	        }

	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	        $ob_pdf->SetFont('segoeuib', '', 12);
	        $ob_pdf->MultiCell(190, 5.5, 'ATA nº: '.$row['nr_ata'], 0, 'L');
			$ob_pdf->SetFont('segoeuil', '', 12);

			if(trim($row['dt_pauta_sg_fim']) == '')
			{
				$ob_pdf->MultiCell(190, 5.5, 'Data: '.$row['dt_pauta'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Horário: '.$row['hr_pauta'], 0, 'L');
	        }
			else
			{
				$ob_pdf->MultiCell(190, 5.5, 'Data: '.$row['dt_pauta'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Horário: '.$row['hr_pauta'], 0, 'L');
				$ob_pdf->MultiCell(190, 5.5, 'Data Encerramento: '.$row['dt_pauta_sg_fim'], 0, 'L');
				
				if(trim($row['hr_pauta_sg_fim']) != '')
				{
					$ob_pdf->MultiCell(190, 5.5, 'Horário Encerramento: '.$row['hr_pauta_sg_fim'], 0, 'L');
				}
			}

			$ob_pdf->MultiCell(190, 5.5, 'Local: '.$row['local'], 0, 'L');
	        
	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	        if(trim($row['fl_sumula']) == 'DE')
	        {
				if($fl_autoatendimento)
				{
					$ob_pdf->SetWidths(array(10, 55, 125));
					$ob_pdf->SetAligns(array('C', 'C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 12);
					$ob_pdf->Row(array('Nº', 'RELATOR', 'ASSUNTOS'));
					$ob_pdf->SetAligns(array('C', 'L', 'J'));
				}
				else
				{
					$ob_pdf->SetWidths(array(10, 55, 85, 20, 20));
					$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 12);
					$ob_pdf->Row(array('Nº', 'RELATOR', 'ASSUNTOS', 'TEMPO', 'RD'));
					$ob_pdf->SetAligns(array('C', 'L', 'J', 'J', 'C', 'C'));
				}				
	        }
	        elseif(trim($row['fl_sumula']) == 'IN')
	        {
				if($fl_autoatendimento)
				{
					$ob_pdf->SetWidths(array(10, 55, 55, 70));
					$ob_pdf->SetAligns(array('C', 'C', 'C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 10);
					$ob_pdf->Row(array('Nº', 'INSTÂNCIA APROVAÇÃO', 'ÁREA DE ATUAÇÃO', 'ASSUNTOS'));
					$ob_pdf->SetAligns(array('C', 'L', 'L', 'J'));
				}
				else
				{    
					$ob_pdf->SetWidths(array(10, 25, 30, 20, 65, 20, 20));
					$ob_pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 10);
					$ob_pdf->Row(array('Nº', 'INSTÂNCIA APROVAÇÃO', 'ÁREA DE ATUAÇÃO', 'GERÊNCIA', 'ASSUNTOS', 'TEMPO', 'RI'));
					$ob_pdf->SetAligns(array('C', 'L', 'L', 'C', 'J', 'C', 'C'));
				}				
	        }			
	        else
	        {
				if($fl_autoatendimento)
				{
					$ob_pdf->SetWidths(array(10, 180));
					$ob_pdf->SetAligns(array('C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 12);
					$ob_pdf->Row(array('Nº', 'ASSUNTOS'));
					$ob_pdf->SetAligns(array('C', 'J'));					
				}
				else
				{
					$ob_pdf->SetWidths(array(10, 160, 20));
					$ob_pdf->SetAligns(array('C', 'C', 'C'));
					$ob_pdf->SetFont('segoeuib', '', 12);
					$ob_pdf->Row(array('Nº', 'ASSUNTOS', 'TEMPO'));
					$ob_pdf->SetAligns(array('C', 'J', 'C'));
				}
	        }

	        $ob_pdf->SetFont('segoeuil', '', 12);

	        foreach($collection as $key => $item)
	        {
	        	if(trim($row['fl_sumula']) == 'DE')
	        	{
	        		if($fl_autoatendimento)
					{
						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['ds_diretoria'],
							$item['ds_pauta_sg_assunto']
						));				
					}
					else
					{
						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['ds_diretoria'],
							$item['ds_pauta_sg_assunto'],
							(trim($item['nr_tempo']) != '' ? $item['nr_tempo'].'min' : ''),
							$item['ds_resolucao_diretoria']
						));
					}					
					
	        	}
				elseif(trim($row['fl_sumula']) == 'IN')
	        	{
	        		if($fl_autoatendimento)
					{
						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['instancia_aprovacao'],
							$item['ds_diretoria'],
							$item['ds_pauta_sg_assunto']
						));				
					}
					else
					{
						$ob_pdf->SetFont('segoeuil', '', 10);

						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['instancia_aprovacao'],
							$item['ds_diretoria'],
							$item['cd_gerencia_responsavel'],
							$item['ds_pauta_sg_assunto'],
							(trim($item['nr_tempo']) != '' ? $item['nr_tempo'].'min' : ''),
							$item['ds_resolucao_diretoria']
						));
					}					
					
	        	}
	        	else
	        	{
	        		if($fl_autoatendimento)
					{
						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['ds_pauta_sg_assunto']
						));						
					}
					else
					{
						$ob_pdf->Row(array(
							$item['nr_item_sumula'],
							$item['ds_pauta_sg_assunto'],
							(trim($item['nr_tempo']) != '' ? $item['nr_tempo'].'min' : '')
						));
					}
	        	}
	        }
			
			$arq_pauta = 'Pauta_'.$row['nr_ata'].($fl_autoatendimento ? '_autoatendimento' : '').'.pdf';

			#### INTEGRAÇÃO LINK ####
			if(is_dir($row['integracao_arq']))
			{
				$ob_pdf->Output('up/pauta/'.$arq_pauta, 'F');
				
				copy('../cieprev/up/pauta/'.$arq_pauta, $row['integracao_arq'].'/Pauta_'.$row['nr_ata'].'.pdf');
				
				$dir_aprovado = "";
				if($row['fl_sumula'] == "CD")
				{
					$dir_aprovado = str_replace("DELIBERATIVO","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
				}
				elseif($row['fl_sumula'] == "CF")
				{
					$dir_aprovado = str_replace("FISCAL","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
				}				
				elseif($row['fl_sumula'] == "DE")
				{
					$dir_aprovado = str_replace("DIRETORIA","DOCUMENTOS_APROVADOS",$row['integracao_arq']);
				}
				else
				{
					echo "ERRO"; EXIT;
				}
				copy('../cieprev/up/pauta/'.$arq_pauta, $dir_aprovado.'/Pauta_'.$row['nr_ata'].'.pdf');
				
			}
			else
			{
				$ob_pdf->Output('up/pauta/'.$arq_pauta,'F');
			}
			
			return $arq_pauta;
			#$ob_pdf->Output();
        }
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function sumula($cd_pauta_sg)
	{
		if(gerencia_in(array("SG", "DE")))
		{
			$this->load->model('gestao/pauta_sg_model');

			$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

			$collection = $this->pauta_sg_model->assunto_listar(intval($cd_pauta_sg), 'S');

			$this->load->plugin('fpdf');
				
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
            $ob_pdf->AddFont('segoeuib');	
			$ob_pdf->SetNrPag(true);
	        $ob_pdf->SetMargins(10, 14, 5);
	        $ob_pdf->header_exibe = true;
	        $ob_pdf->header_logo = true;
	        $ob_pdf->header_titulo = true;
	        $ob_pdf->header_titulo_texto = "";

	        $ob_pdf->SetLineWidth(0);
	        $ob_pdf->SetDrawColor(0, 0, 0);

	        $d = 'O';

	        if(trim($row['fl_sumula']) == 'DE')
	        {
	        	$d = 'A';
	        }

	        $ob_pdf->AddPage();
	        $ob_pdf->SetY($ob_pdf->GetY() + 1);
	        $ob_pdf->SetFont('Courier', 'b', 12);
	        $ob_pdf->MultiCell(190, 4.5, 'SÚMULA DA REUNIÃO D'.$d, 0, 'C');

	        if(trim($row['fl_sumula']) == 'DE')
	        {
	        	$sumula = 'DIRETORIA EXECUTIVA';
	        }
	        else if(trim($row['fl_sumula']) == 'CF')
	        {
	        	$sumula = 'CONSELHO FISCAL';
	        }
	        else if(trim($row['fl_sumula']) == 'IN')
	        {
	        	$sumula = 'INTERVENTOR';
	        }			
	        else 
	        {
	        	$sumula = 'CONSELHO DELIBERATIVO';
	        }

	        $ob_pdf->MultiCell(190, 4.5, $sumula, 0, 'C');
			
			if((trim($row['dt_pauta_sg_fim']) == '') OR (trim($row['dt_pauta']) == trim($row['dt_pauta_sg_fim'])))
			{
				$ob_pdf->MultiCell(190, 4.5, 'ATA nº '.$row['nr_ata'].' DE '.$row['dt_pauta'], 0, 'C');
	        }
			else
			{
				$ob_pdf->MultiCell(190, 4.5, 'ATA nº '.$row['nr_ata'].' DE '.$row['dt_pauta'].' E '.$row['dt_pauta_sg_fim'], 0, 'C');
			}
			
	        $ob_pdf->SetY($ob_pdf->GetY() + 5);

	        $order = 0;

	        foreach($collection as $key => $item)
	        {
				if(($ob_pdf->GetY() + 5) > 255)
				{
					$ob_pdf->AddPage();
				}
				
				if(trim($row['fl_sumula']) == 'DE')
	        	{
	        		$ob_pdf->SetFont('Courier', '', 12);
	        		$ob_pdf->SetY($ob_pdf->GetY() + 5);

	        		$ob_pdf->MultiCell(190, 4.5, $item['ds_diretoria'].':', 0, 'L');

	        		$ob_pdf->SetY($ob_pdf->GetY() + 5);
	        	}
				elseif(trim($row['fl_sumula']) == 'IN')
	        	{
	        		$ob_pdf->SetFont('Courier', 'B', 12);
	        		$ob_pdf->SetY($ob_pdf->GetY() + 5);

					$ob_pdf->MultiCell(190, 4.5, 'INSTÂNCIA DE APROVAÇÃO: '.$item['instancia_aprovacao'], 0, 'L');
	        		$ob_pdf->SetY($ob_pdf->GetY() + 2);
					$ob_pdf->MultiCell(190, 4.5, 'Área de atuação - '.$item['ds_diretoria'].':', 0, 'L');
	        		$ob_pdf->SetY($ob_pdf->GetY() + 3);
	        	}				

	        	$ob_pdf->SetFont('Courier', 'b', 12);
	    		$ob_pdf->MultiCell(190, 4.5, $item['nr_item_sumula'].') '.$item['ds_pauta_sg_assunto'].':', 0, 'L');
	    		$ob_pdf->SetFont('Courier', '', 10);
	    		$ob_pdf->MultiCell(190, 4.5, $item['ds_decisao'], 0, 'J');
	    		$ob_pdf->SetY($ob_pdf->GetY() + 5);
	        }

	        $collection = $this->get_integrante(intval($cd_pauta_sg));

	        if(count($collection['titular']) > 0)
	        {
	        	if(($ob_pdf->GetY()+20) >= 270)
	            {
	            	$ob_pdf->AddPage();

	            	$ob_pdf->SetFont('segoeuib', '', 10);
	                $ob_pdf->Text(20, $ob_pdf->GetY()+5, 'Cont. da Súmula da reunião do(a) '.$sumula.' ATA nº '.$row['nr_ata']);
	                $ob_pdf->SetFont('segoeuil', '', 12);

	                $ob_pdf->SetY($ob_pdf->GetY()+10);
	            }

				$ob_pdf->SetFont('segoeuib', 'U', 10);

				if(trim($row['fl_sumula']) == 'DE')
				{
					$ob_pdf->Text(20,$ob_pdf->GetY()+5, trim('Diretoria:'));
				}
				else
				{
					$ob_pdf->Text(20,$ob_pdf->GetY()+5, trim('Titulares:'));
					$ob_pdf->Text(120,$ob_pdf->GetY()+5, trim('Suplentes:'));
				}
	            $ob_pdf->SetFont('segoeuil', '', 12);

		        $collection = $this->get_integrante(intval($cd_pauta_sg));

		        $titular    = $collection['titular'];
		        $suplente   = $collection['suplente'];
		        $secretaria = $collection['secretaria'];

		        $linha = $ob_pdf->GetY();

				if(trim($row['fl_sumula']) == 'DE')
				{
					$i = 1;
					$l = 0;
					foreach ($titular as $key => $item)
					{
//echo $i.' - '.$linha.br();

						if(($linha+30) >= 270 AND ($i % 2))
						{
							$ob_pdf->AddPage();

							$ob_pdf->SetFont('segoeuib', '', 10);
							$ob_pdf->Text(20, $ob_pdf->GetY()+5, 'Cont. da Súmula da reunião do(a) '.$sumula.' ATA nº '.$row['nr_ata']);
							$ob_pdf->SetFont('segoeuil', '', 12);

							$linha = $ob_pdf->GetY();
							$l = $ob_pdf->GetY();
						}
						
						$l = (!($i % 2) ? $l : $linha);
						$linha = (($i % 2) ? $linha : $l);
					
						$ob_pdf->Text((20 + (!($i % 2) ? 100 : 0)), $linha+30, trim($titular[$key]['ds_titular'])); 
						$ob_pdf->Text((20 + (!($i % 2) ? 100 : 0)), $linha+34, trim($titular[$key]['ds_cargo'])); 						

						$linha += 25; 
						$i++;
					}					
				}
				else
				{
					foreach ($titular as $key => $item)
					{
						if(($linha+20) >= 270)
						{
							$ob_pdf->AddPage();

							$ob_pdf->SetFont('segoeuib', '', 10);
							$ob_pdf->Text(20, $ob_pdf->GetY()+5, 'Cont. da Súmula da reunião do(a) '.$sumula.' ATA nº '.$row['nr_ata']);
							$ob_pdf->SetFont('segoeuil', '', 12);

							$linha = $ob_pdf->GetY();
						}

						$ob_pdf->Text(20,$linha+20, trim($titular[$key]['ds_titular']));	 
						$ob_pdf->Text(120,$linha+20, trim($suplente[$key]['ds_suplente']));	 

						$linha += 15; 
					}
		        }

		        $ob_pdf->SetY($linha);
	            
	            if(($linha+20) >= 270)
	            {
	                $ob_pdf->AddPage();
	                $linha = $ob_pdf->GetY();
	            }

	            $ob_pdf->SetFont('segoeuib', 'U', 10);
	            $ob_pdf->Text(20,$linha+20, trim('Secretária:'));
	            $ob_pdf->SetFont('segoeuil', '', 12);

	            $linha = $ob_pdf->GetY();

		        foreach ($secretaria as $key => $item)
		        {
		        	$ob_pdf->Text(20,$linha+40, trim($item['ds_secretaria']));	 

		        	$linha += 15;       	
		        }

	        }

	        $ob_pdf->Output();
	        exit;
        }
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function pesquisa()
    {
    	if($this->get_permissao())
		{
			$data = array(
				'sumula'       => $this->get_sumula(),
				'tipo_reuniao' => $this->get_tipo_reuniao()
			);
								
			$this->load->view('gestao/pauta_sg/pesquisa', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function pesquisa_listar()
    {
    	$this->load->model('gestao/pauta_sg_model');

    	$args = array(
    		'nr_ata'              => $this->input->post('nr_ata', TRUE),
    		'fl_sumula'           => $this->input->post('fl_sumula', TRUE),
    		'fl_tipo_reuniao'     => $this->input->post('fl_tipo_reuniao', TRUE),
    		'dt_pauta_sg_ini'     => $this->input->post('dt_pauta_sg_ini', TRUE),
    		'dt_pauta_sg_fim'     => $this->input->post('dt_pauta_sg_fim', TRUE),
    		'dt_pauta_sg_fim_ini' => $this->input->post('dt_pauta_sg_fim_ini', TRUE),
    		'dt_pauta_sg_fim_fim' => $this->input->post('dt_pauta_sg_fim_fim', TRUE),
    		'ds_pauta_sg_assunto' => $this->input->post('ds_pauta_sg_assunto', TRUE)
    	);

		manter_filtros($args);
		
		$data['collection'] = $this->pauta_sg_model->pesquisa_listar($args);
		
		$this->load->view('gestao/pauta_sg/pesquisa_result', $data);
    }

    public function zip_docs($cd_pauta_sg, $cd_pauta_sg_assunto)
    {
    	if($this->get_permissao())
		{
			$this->load->model('gestao/pauta_sg_model');

			$this->load->library('zip');

			#### CRIA DIRETORIO TEMP PARA USAR O NOME ORIGINAL ####
			$dir_tmp = "../cieprev/up/pauta_".intval($cd_pauta_sg)."_".intval($cd_pauta_sg_assunto);
			if(!is_dir($dir_tmp))
			{
				mkdir($dir_tmp);
			}

			$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
								
			$arquivo = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto));

			$assunto = $this->pauta_sg_model->assunto_carrega($cd_pauta_sg_assunto);

			$dir = "../cieprev/up/pauta";
			foreach ($arquivo as $ar_item)
			{
				$ar_nome  = explode(".",$ar_item['arquivo_nome']);
				$ext      = $ar_nome[ (count($ar_nome) - 1) ];
				$n_ext    = strlen($ar_item['arquivo_nome']) - (strlen($ext) + 1);
				$nome_ori = substr($ar_item['arquivo_nome'], 0, $n_ext)."_".$ar_item['cd_pauta_sg_assunto_anexo'].".".$ext;
				
				copy($dir."/".$ar_item['arquivo'], $dir_tmp."/".$nome_ori);
				
				$this->zip->read_file($dir_tmp."/".$nome_ori);
				//@unlink($dir_tmp."/".$nome_ori);
			}
			
			if(is_dir($dir_tmp))
			{
				//@rmdir($dir_tmp);
			}

			$this->zip->download($row['fl_sumula'].'_'.$row['nr_ata'].'_'.$assunto['nr_item_sumula'].".zip");
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function minhas()
    {
    	$data = array(
			'sumula'       => $this->get_sumula(),
			'tipo_reuniao' => $this->get_tipo_reuniao()
		);
							
		$this->load->view('gestao/pauta_sg/minhas', $data);
    }

    public function minhas_listar()
    {
		$this->load->model('gestao/pauta_sg_model');

    	$args = array(
    		'nr_ata'              => $this->input->post('nr_ata', TRUE),
    		'fl_sumula'           => $this->input->post('fl_sumula', TRUE),
    		'fl_tipo_reuniao'     => $this->input->post('fl_tipo_reuniao', TRUE),
    		'dt_pauta_sg_ini'     => $this->input->post('dt_pauta_sg_ini', TRUE),
    		'dt_pauta_sg_fim'     => $this->input->post('dt_pauta_sg_fim', TRUE)
    	);

		manter_filtros($args);
		
		$data['collection'] = $this->pauta_sg_model->minhas_listar(
			$this->session->userdata('codigo'),
			$args
		);
		
		$this->load->view('gestao/pauta_sg/minhas_result', $data);	
    }

    public function responder($cd_pauta_sg, $cd_pauta_sg_assunto)
    {
		$this->load->model('gestao/pauta_sg_model');

        $assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$data = array(
				'row'           => $this->pauta_sg_model->carrega(intval($cd_pauta_sg)),
				'assunto'       => $assunto,
				'objetivo'      => $this->pauta_sg_model->get_objetivo(),
				'justificativa' => $this->pauta_sg_model->get_justificativa(),
			);

			$arquivo = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto), 'S');

			$data['assunto']['cd_pauta_sg_assunto_anexo'] = (isset($arquivo[0]['cd_pauta_sg_assunto_anexo']) ? $arquivo[0]['cd_pauta_sg_assunto_anexo'] : 0);
			$data['assunto']['arquivo']                   = (isset($arquivo[0]['arquivo']) ? $arquivo[0]['arquivo'] : '');
			$data['assunto']['arquivo_nome']              = (isset($arquivo[0]['arquivo_nome']) ? $arquivo[0]['arquivo_nome'] : '');

			$arquivo = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto), '', 'S', '');

			$data['assunto']['cd_pauta_sg_assunto_ordem_anexo'] = (isset($arquivo[0]['cd_pauta_sg_assunto_anexo']) ? $arquivo[0]['cd_pauta_sg_assunto_anexo'] : 0);
			$data['assunto']['arquivo_ordem']                   = (isset($arquivo[0]['arquivo']) ? $arquivo[0]['arquivo'] : '');
			$data['assunto']['arquivo_ordem_nome']              = (isset($arquivo[0]['arquivo_nome']) ? $arquivo[0]['arquivo_nome'] : '');

			$arquivo = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto), '', '', 'S');

			$data['assunto']['cd_pauta_sg_assunto_quadro_anexo'] = (isset($arquivo[0]['cd_pauta_sg_assunto_anexo']) ? $arquivo[0]['cd_pauta_sg_assunto_anexo'] : 0);
			$data['assunto']['arquivo_quadro']                   = (isset($arquivo[0]['arquivo']) ? $arquivo[0]['arquivo'] : '');
			$data['assunto']['arquivo_quadro_nome']              = (isset($arquivo[0]['arquivo_nome']) ? $arquivo[0]['arquivo_nome'] : '');

			$this->load->view('gestao/pauta_sg/responder', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function responder_salvar()
    {
    	$this->load->model('gestao/pauta_sg_model');

		$cd_pauta_sg         = $this->input->post('cd_pauta_sg', TRUE);
		$cd_pauta_sg_assunto = $this->input->post('cd_pauta_sg_assunto', TRUE);

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$fl_aplica_rds 		    = $this->input->post('fl_aplica_rds', TRUE);
			$fl_ordem_fornecimento  = $this->input->post('fl_ordem_fornecimento', TRUE);

			$args = array(
				'nr_tempo'                  => $this->input->post('nr_tempo', TRUE),
				'cd_pauta_sg_objetivo'      => $this->input->post('cd_pauta_sg_objetivo', TRUE),
				'cd_pauta_sg_justificativa' => $this->input->post('cd_pauta_sg_justificativa', TRUE),
				'fl_aplica_detalhamento'    => $this->input->post('fl_aplica_detalhamento', TRUE),
				'ds_detalhamento'           => $this->input->post('ds_detalhamento', TRUE),
				'fl_aplica_historico'       => $this->input->post('fl_aplica_historico', TRUE),
				'ds_historico'              => $this->input->post('ds_historico', TRUE),
				'fl_aplica_recomendacao'    => $this->input->post('fl_aplica_recomendacao', TRUE),
				'ds_recomendacao'           => $this->input->post('ds_recomendacao', TRUE),
				'fl_aplica_situacao'        => $this->input->post('fl_aplica_situacao', TRUE),
				'ds_situacao'               => $this->input->post('ds_situacao', TRUE),
				'fl_aplica_rds'             => $fl_aplica_rds,
				'fl_ordem_fornecimento'     => $fl_ordem_fornecimento,
				'fl_quadro_comparativo'		=> '',
				'fl_rds_restrita'           => $this->input->post('fl_rds_restrita', TRUE),
				'nr_ordem_fornecimento'     => $this->input->post('nr_ordem_fornecimento', TRUE),
				'cd_usuario'                => $cd_usuario
            );

			$this->pauta_sg_model->responder_salvar($cd_pauta_sg_assunto, $args);

			if(trim($fl_aplica_rds) == 'S')
			{
				$args = array(
					'cd_pauta_sg_assunto' 	=> intval($cd_pauta_sg_assunto),
					'arquivo_nome'        	=> $this->input->post('arquivo_nome', TRUE),
					'arquivo'             	=> $this->input->post('arquivo', TRUE),
					'fl_rds'              	=> 'S',
					'fl_ordem_fornecimento' => '',
					'fl_quadro_comparativo' => '',
					'nr_ano_rds'          	=> $this->input->post('nr_ano_rds', TRUE),
					'nr_rds'              	=> $this->input->post('nr_rds', TRUE),
					'cd_usuario'          	=> $this->session->userdata('codigo')
				);

				$cd_pauta_sg_assunto_anexo = $this->input->post('cd_pauta_sg_assunto_anexo', TRUE);

				if(intval($cd_pauta_sg_assunto_anexo) == 0)
				{
					$this->pauta_sg_model->anexo_salvar($args);
				}
				else
				{
					$this->pauta_sg_model->anexo_atualizar(intval($cd_pauta_sg_assunto_anexo), $args);
				}

				$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

				#### INTEGRAÇÃO LINK ####
				if(is_dir($pauta['integracao_arq']))
				{
					#copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($args['arquivo_nome']));
					copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($args['arquivo_nome']));
				}
			}
			else
			{
				$arquivo = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto), 'S');

				if(count($arquivo) > 0)
				{
					$this->pauta_sg_model->anexo_excluir($arquivo[0]['cd_pauta_sg_assunto_anexo'], $this->session->userdata('codigo'));

					$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

					if(trim($pauta['integracao_arq']) != '')
					{
						#@unlink($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($anexo['arquivo_nome']));
						@unlink($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($anexo['arquivo_nome']));
					}
				}
			}	

			if(trim($fl_ordem_fornecimento) == 'S')
			{
				$args = array(
					'cd_pauta_sg_assunto' 	=> intval($cd_pauta_sg_assunto),
					'arquivo_nome'        	=> $this->input->post('arquivo_ordem_nome', TRUE),
					'arquivo'             	=> $this->input->post('arquivo_ordem', TRUE),
					'fl_rds'				=> '',
					'fl_ordem_fornecimento' => 'S',
					'fl_quadro_comparativo' => '',
					'nr_ano_rds'          	=> $this->input->post('nr_ano_rds', TRUE),
					'nr_rds'              	=> $this->input->post('nr_rds', TRUE),
					'cd_usuario'          	=> $this->session->userdata('codigo')
				);

				$cd_pauta_sg_assunto_anexo = $this->input->post('cd_pauta_sg_assunto_ordem_anexo', TRUE);

				if(intval($cd_pauta_sg_assunto_anexo) == 0)
				{
					$this->pauta_sg_model->anexo_salvar($args);
				}
				else
				{
					$this->pauta_sg_model->anexo_atualizar(intval($cd_pauta_sg_assunto_anexo), $args);
				}

				$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

				#### INTEGRAÇÃO LINK ####
				if(is_dir($pauta['integracao_arq']))
				{
					#copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($args['arquivo_nome']));
					copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($args['arquivo_nome']));
				}
			}

			if(trim($fl_ordem_fornecimento) == 'S')
			{
				$args = array(
					'cd_pauta_sg_assunto' 	=> intval($cd_pauta_sg_assunto),
					'arquivo_nome'        	=> $this->input->post('arquivo_quadro_nome', TRUE),
					'arquivo'             	=> $this->input->post('arquivo_quadro', TRUE),
					'fl_rds'				=> '',
					'fl_ordem_fornecimento' => '',
					'fl_quadro_comparativo' => 'S',
					'nr_ano_rds'          	=> $this->input->post('nr_ano_rds', TRUE),
					'nr_rds'              	=> $this->input->post('nr_rds', TRUE),
					'cd_usuario'          	=> $this->session->userdata('codigo')
				);

				$cd_pauta_sg_assunto_anexo = $this->input->post('cd_pauta_sg_assunto_quadro_anexo', TRUE);

				if(intval($cd_pauta_sg_assunto_anexo) == 0)
				{
					$this->pauta_sg_model->anexo_salvar($args);
				}
				else
				{
					$this->pauta_sg_model->anexo_atualizar(intval($cd_pauta_sg_assunto_anexo), $args);
				}

				$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

				#### INTEGRAÇÃO LINK ####
				if(is_dir($pauta['integracao_arq']))
				{
					#copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($args['arquivo_nome']));
					copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($args['arquivo_nome']));
				}
			}

			redirect('gestao/pauta_sg/responder/'.$cd_pauta_sg.'/'.$cd_pauta_sg_assunto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function responder_anexo($cd_pauta_sg, $cd_pauta_sg_assunto)
    {
		$this->load->model('gestao/pauta_sg_model');

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$data = array(
				'row'        => $this->pauta_sg_model->carrega(intval($cd_pauta_sg)),
				'assunto'    => $assunto,
				'collection' => $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto), 'N', 'N', 'N')
			);

			$this->load->view('gestao/pauta_sg/responder_anexo', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function responder_anexo_salvar()
	{
		$this->load->model('gestao/pauta_sg_model');

		$cd_pauta_sg         = intval($this->input->post('cd_pauta_sg', TRUE));
		$cd_pauta_sg_assunto = intval($this->input->post('cd_pauta_sg_assunto', TRUE));

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));
			
			$qt_arquivo = intval($this->input->post('arquivo_m_count', TRUE));

			$args = array(
				'cd_pauta_sg_assunto' => intval($cd_pauta_sg_assunto),
				'arquivo_nome'        => '',
				'arquivo'             => '',
				'fl_rds'              => 'N',
				'nr_ano_rds'          => '',
				'nr_rds'              => '',
				'cd_usuario'          => $this->session->userdata('codigo')
			);
		
			if($qt_arquivo > 0)
			{
				$nr_conta = 0;

				while($nr_conta < $qt_arquivo)
				{					
					$args['arquivo_nome']  = $this->input->post('arquivo_m_'.$nr_conta.'_name', TRUE);
					$args['arquivo']       = $this->input->post('arquivo_m_'.$nr_conta.'_tmpname', TRUE);
					
					$this->pauta_sg_model->anexo_salvar($args);
					
					$nr_conta++;
					
					#### INTEGRAÇÃO LINK ####
					if(is_dir($pauta['integracao_arq']))
					{
						#copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($args['arquivo_nome']));
						copy('../cieprev/up/pauta/'.$args['arquivo'], $pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($args['arquivo_nome']));
					}
				}
			}

			redirect('gestao/pauta_sg/responder_anexo/'.$cd_pauta_sg.'/'.$cd_pauta_sg_assunto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function responder_anexo_excluir($cd_pauta_sg, $cd_pauta_sg_assunto, $cd_pauta_sg_assunto_anexo)
	{
		$this->load->model('gestao/pauta_sg_model');

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

			$anexo = $this->pauta_sg_model->anexo_carrega(intval($cd_pauta_sg_assunto_anexo));	

			$this->pauta_sg_model->anexo_excluir($cd_pauta_sg_assunto_anexo, $this->session->userdata('codigo'));	

			if(trim($pauta['integracao_arq']) != '')
			{
				#@unlink($pauta['integracao_arq'].'/documentos/'.$assunto['nr_item_sumula'].' - '.fixUTF8($anexo['arquivo_nome']));
				@unlink($pauta['integracao_arq'].'/documentos/'.str_pad($assunto['nr_item_sumula'] ,3,'0', STR_PAD_LEFT).' - '.fixUTF8($anexo['arquivo_nome']));
			}
		
			redirect('gestao/pauta_sg/responder_anexo/'.$cd_pauta_sg.'/'.$cd_pauta_sg_assunto, 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}

	public function encerrar_assunto($cd_pauta_sg, $cd_pauta_sg_assunto)
    {
		$this->load->model('gestao/pauta_sg_model');

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$cd_usuario = $this->session->userdata('codigo');

		if(intval($cd_usuario) == intval($assunto['cd_usuario_responsavel']) OR intval($cd_usuario) == intval($assunto['cd_usuario_substituto']))
		{
			$this->load->model('projetos/eventos_email_model');

			$cd_evento = 287;

			$email = $this->eventos_email_model->carrega($cd_evento);

			$row = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

			$ds_anexo_info = '';

			if(intval($assunto['tl_arquivo']) == 0)
			{
				$ds_anexo_info = ' <b/>SEM DOCUMENTO(S) SUPORTE</b>';
			}

			$tags = array('[FL_COLEGIADO]', '[NR_ATA]');
			$subs = array($row['fl_sumula'], $row['nr_ata']);
			
			$assunto_email = str_replace($tags, $subs, $email['assunto']);

			$tags = array('[NR_PAUTA]', '[DESCRICAO]', '[ANEXO]', '[LINK]');
			$subs = array(
				$assunto['nr_item_sumula'], 
				$assunto['ds_pauta_sg_assunto'], 
				$ds_anexo_info,
				site_url('gestao/pauta_sg/assunto/'.$cd_pauta_sg.'/'.$cd_pauta_sg_assunto)
			);
			
			$texto = str_replace($tags, $subs, $email['email']);

			$args = array(
				'de'      => 'Pauta SG',
				'assunto' => $assunto_email,
				'para'    => $email['para'],
				'cc'      => $email['cc'],
				'cco'     => $email['cco'],
				'texto'   => $texto
			);

			$this->eventos_email_model->envia_email($cd_evento, $cd_usuario, $args);

			$this->pauta_sg_model->encerrar_assunto(
				$cd_pauta_sg_assunto,
				$this->session->userdata('codigo')
			);

			$this->apresentacao_gerar($cd_pauta_sg, $cd_pauta_sg_assunto);

			$this->enviar_colegiado($cd_pauta_sg, $cd_pauta_sg_assunto);

			redirect('gestao/pauta_sg/minhas', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    private function monta_pagina($ob_pdf, $texto)
	{
		$ob_pdf->AddPage();
        $ob_pdf->Image('./img/apresentacao/fundo.png', 0, 0, $ob_pdf->ConvertSize(1120), $ob_pdf->ConvertSize(792), '', '', false);

        $ob_pdf->Image('./img/apresentacao/titulo.png', 10, 8, $ob_pdf->ConvertSize(1050), $ob_pdf->ConvertSize(80), '', '', false);

        $ob_pdf->SetTextColor(255, 255, 255);

        $ob_pdf->SetFont('Arial', 'B', 36);
        $ob_pdf->MultiCell(285, 10, trim($texto), 0, 'C');

        $ob_pdf->SetY($ob_pdf->GetY()+10);
	}

	private function set_fonte_texto($ob_pdf, $texto)
	{
		if(strlen(trim($texto)) > 900)
		{
			$ob_pdf->SetFont('Arial', 'B', 22);
		}
		else if(strlen(trim($texto)) > 600)
		{
			$ob_pdf->SetFont('Arial', 'B', 24);
		}
		else
		{
			$ob_pdf->SetFont('Arial', 'B', 26);
		}
	}

	public function apresentacao($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		$arq = $this->apresentacao_gerar($cd_pauta_sg, $cd_pauta_sg_assunto);
		
		header('Location: '.base_url().'up/pauta/'.$arq);
	}

	public function apresentacao_gerar($cd_pauta_sg, $cd_pauta_sg_assunto)
	{
		$this->load->model('gestao/pauta_sg_model');

		$pauta = $this->pauta_sg_model->carrega(intval($cd_pauta_sg));

		$assunto = $this->pauta_sg_model->assunto_carrega(intval($cd_pauta_sg_assunto));

		$anexos = $this->pauta_sg_model->anexo_listar(intval($cd_pauta_sg_assunto));

		$this->load->plugin('fpdf');
			
		$ob_pdf = new PDF('L', 'mm', 'A4');
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');				
		$ob_pdf->SetNrPag(false);
        $ob_pdf->SetMargins(8, 14, 5);
        $ob_pdf->header_exibe = false;
        $ob_pdf->header_logo = false;
        $ob_pdf->header_titulo = false;
        $ob_pdf->header_titulo_texto = '';

        $ob_pdf->SetLineWidth(0);
        $ob_pdf->SetDrawColor(0, 0, 0);

        $this->monta_pagina($ob_pdf, 'Estrutura');

        $ob_pdf->SetTextColor(0, 60, 85);

        $ob_pdf->SetFont('Arial', 'B', 32);

        $ob_pdf->MultiCell(285, 20, '1.   Assunto, Objetivo e Justificativa', 5, 'L');
        
        $i = 2;

        if(trim($assunto['ds_detalhamento']) != '' OR trim($assunto['ds_historico']) != '' OR trim($assunto['ds_situacao']) != '')
        {
        	$ob_pdf->MultiCell(285, 20, $i.'.   Detalhamento', 0, 'L');

        	$i++;
    	}
        
        if(trim($assunto['ds_recomendacao']) != '')
        {
        	$ob_pdf->MultiCell(285, 20, $i.'.   Recomendações da Gerência', 0, 'L');

        	$i++;
        }

        if(count($anexos) > 0)
        {
        	$ob_pdf->MultiCell(285, 20, $i.'.   Anexos', 5, 'L');
    	}

        $this->monta_pagina($ob_pdf, 'Assunto, Objetivo e Justificativa');

        $ob_pdf->SetTextColor(0, 60, 85);

        $ob_pdf->SetFont('Arial', 'B', 28);

        $this->set_fonte_texto($ob_pdf, $assunto['ds_pauta_sg_assunto']);

        $ob_pdf->MultiCell(280, 9, trim($assunto['ds_pauta_sg_assunto']), '0', 'J');

        if(trim($assunto['nr_tempo']) != '')
        {
        	$ob_pdf->SetY($ob_pdf->GetY()+5);
        	$ob_pdf->MultiCell(280, 9, 'Tempo: '.$assunto['nr_tempo'].' min', '0', 'J');
        }

        $ob_pdf->SetY($ob_pdf->GetY()+5);

        $ob_pdf->MultiCell(280, 9, 'Objetivo: '.trim($assunto['ds_pauta_sg_objetivo']), '0', 'J');

        $ob_pdf->SetY($ob_pdf->GetY()+5);

        $ob_pdf->MultiCell(280, 9, 'Justificativa: '.trim($assunto['ds_pauta_sg_justificativa']), '0', 'J');

        if(trim($assunto['ds_detalhamento']) != '')
        {
        	$this->monta_pagina($ob_pdf, 'Detalhamento');

        	$ob_pdf->SetTextColor(0, 60, 85);

	        $this->set_fonte_texto($ob_pdf, $assunto['ds_detalhamento']);

	        $ob_pdf->MultiCell(280, 9, trim($assunto['ds_detalhamento']), '0', 'J');
        }

        if(trim($assunto['ds_historico']) != '')
        {
        	$this->monta_pagina($ob_pdf, 'Detalhamento');

        	$ob_pdf->SetTextColor(0, 60, 85);

	        $this->set_fonte_texto($ob_pdf, $assunto['ds_historico']);

	        $ob_pdf->MultiCell(280, 9, 'Histórico : '.trim($assunto['ds_historico']), '0', 'J');
        }

        if(trim($assunto['ds_situacao']) != '')
        {
        	$this->monta_pagina($ob_pdf, 'Detalhamento');

        	$ob_pdf->SetTextColor(0, 60, 85);

	        $this->set_fonte_texto($ob_pdf, $assunto['ds_situacao']);

	        $ob_pdf->MultiCell(280, 9, 'Situação Atual : '.trim($assunto['ds_situacao']), '0', 'J');
        }

        if(trim($assunto['ds_recomendacao']) != '')
        {
	        if(trim($pauta['fl_sumula']) =