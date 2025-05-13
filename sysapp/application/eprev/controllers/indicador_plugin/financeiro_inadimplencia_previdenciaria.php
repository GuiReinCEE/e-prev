<?php
class Financeiro_inadimplencia_previdenciaria extends Controller
{	
	function __construct()
    {
        parent::Controller();

        CheckLogin();

		$this->enum_indicador = intval(enum_indicador::FINANCEIRO_INADIMPLENCIA_PREVIDENCIARIA);
		
		$this->load->helper(array('indicador'));
		
		$ar_label = indicador_db::indicador_get_label($this->enum_indicador);

		foreach($ar_label as $ar_item)
		{
			$this->{'label_'.$ar_item['id_label']} = $ar_item['ds_label'];
		}

		$this->cd_usuario = $this->session->userdata('codigo');		

		$this->load->model('indicador_plugin/financeiro_inadimplencia_previdenciaria_model');
    }

    public function index()
    {
		if(indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			if(indicador_db::abrir_periodo_para_indicador($this->enum_indicador, $this->cd_usuario))
			{
				$this->criar_indicador();
			}	

			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

	        $this->load->view('indicador_plugin/financeiro_inadimplencia_previdenciaria/index', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	if(indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
        {
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
	        $data['label_4'] = $this->label_4;
	        $data['label_5'] = $this->label_5;
	        
			$data['tabela'] = indicador_tabela_aberta($this->enum_indicador);

			$data['collection'] = $this->financeiro_inadimplencia_previdenciaria_model->listar($data['tabela'][0]['cd_indicador_tabela']);
			
			$this->load->view('indicador_plugin/financeiro_inadimplencia_previdenciaria/index_result', $data);
        }
        else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function cadastro($cd_financeiro_inadimplencia_previdenciaria = 0)
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$data['tabela'] = indicador_tabela_aberta(intval($this->enum_indicador));
						
			if(intval($cd_financeiro_inadimplencia_previdenciaria) == 0)
			{
				$row = $this->financeiro_inadimplencia_previdenciaria_model->carrega_referencia();
				
				$data['row'] = array(
                    'cd_financeiro_inadimplencia_previdenciaria' => $cd_financeiro_inadimplencia_previdenciaria,
                    'dt_referencia'         		             => (isset($row['dt_referencia_n']) ? $row['dt_referencia_n'] : ''),
                    'ds_observacao'                              => '',

                    'nr_carga_ceee'                              => 0,
                    'nr_inadimplencia_ceee'                      => 0,
                    'nr_meta_ceee'                               => (isset($row['nr_meta_ceee']) ? $row['nr_meta_ceee'] : 0),

                    'nr_carga_cgtee'                             => 0,
                    'nr_inadimplencia_cgtee'                     => 0,
                    'nr_meta_cgtee'                              => (isset($row['nr_meta_cgtee']) ? $row['nr_meta_cgtee'] : 0),

                    'nr_carga_rge'                               => 0,
                    'nr_inadimplencia_rge'                       => 0,
                    'nr_meta_rge'                                => (isset($row['nr_meta_rge']) ? $row['nr_meta_rge'] : 0),

                    'nr_carga_rgesul'                            => 0,
                    'nr_inadimplencia_rgesul'                    => 0,
                    'nr_meta_rgesul'                             => (isset($row['nr_meta_rgesul']) ? $row['nr_meta_rgesul'] : 0),

                    'nr_carga_ceeemigrado'                       => 0,
                    'nr_inadimplencia_ceeemigrado'               => 0,
                    'nr_meta_ceeemigrado'                        => (isset($row['nr_meta_ceeemigrado']) ? $row['nr_meta_ceeemigrado'] : 0),

                    'nr_carga_fundacaomigrado'                   => 0,
                    'nr_inadimplencia_fundacaomigrado'           => 0,
                    'nr_meta_fundacaomigrado'                    => (isset($row['nr_meta_fundacaomigrado']) ? $row['nr_meta_fundacaomigrado'] : 0),

                    'nr_carga_ceeenovos'                         => 0,
                    'nr_inadimplencia_ceeenovos'                 => 0,
                    'nr_meta_ceeenovos'                          => (isset($row['nr_meta_ceeenovos']) ? $row['nr_meta_ceeenovos'] : 0),

                    'nr_carga_fundacaonovos'                     => 0,
                    'nr_inadimplencia_fundacaonovos'             => 0,
                    'nr_meta_fundacaonovos'                      => (isset($row['nr_meta_fundacaonovos']) ? $row['nr_meta_fundacaonovos'] : 0),

                    'nr_carga_crm'                               => 0,
                    'nr_inadimplencia_crm'                       => 0,
                    'nr_meta_crm'                                => (isset($row['nr_meta_crm']) ? $row['nr_meta_crm'] : 0),

                    'nr_carga_inpel'                             => 0,
                    'nr_inadimplencia_inpel'                     => 0,
                    'nr_meta_inpel'                              => (isset($row['nr_meta_inpel']) ? $row['nr_meta_inpel'] : 0),

                    'nr_carga_foz'                               => 0,
                    'nr_inadimplencia_foz'                       => 0,
                    'nr_meta_foz'                                => (isset($row['nr_meta_foz']) ? $row['nr_meta_foz'] : 0),

                    'nr_carga_ceran'                             => 0,
                    'nr_inadimplencia_ceran'                     => 0,
                    'nr_meta_ceran'                              => (isset($row['nr_meta_ceran']) ? $row['nr_meta_ceran'] : 0)
				);
			}			
			else
			{
				$data['row'] = $this->financeiro_inadimplencia_previdenciaria_model->carrega($cd_financeiro_inadimplencia_previdenciaria);
			}

			$this->load->view('indicador_plugin/financeiro_inadimplencia_previdenciaria/cadastro', $data);
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
    
    public function salvar()
    {
        $cd_financeiro_inadimplencia_previdenciaria = $this->input->post('cd_financeiro_inadimplencia_previdenciaria', true);

        $nr_carga_ceee                              = app_decimal_para_db($this->input->post('nr_carga_ceee', true));
        $nr_inadimplencia_ceee                      = app_decimal_para_db($this->input->post('nr_inadimplencia_ceee', true));
        $nr_meta_ceee                               = app_decimal_para_db($this->input->post('nr_meta_ceee', true));

        $nr_carga_cgtee                             = app_decimal_para_db($this->input->post('nr_carga_cgtee', true));
        $nr_inadimplencia_cgtee                     = app_decimal_para_db($this->input->post('nr_inadimplencia_cgtee', true));
        $nr_meta_cgtee                              = app_decimal_para_db($this->input->post('nr_meta_cgtee', true));

        $nr_carga_rge                               = app_decimal_para_db($this->input->post('nr_carga_rge', true));
        $nr_inadimplencia_rge                       = app_decimal_para_db($this->input->post('nr_inadimplencia_rge', true));
        $nr_meta_rge                                = app_decimal_para_db($this->input->post('nr_meta_rge', true));

        $nr_carga_rgesul                            = app_decimal_para_db($this->input->post('nr_carga_rgesul', true));
        $nr_inadimplencia_rgesul                    = app_decimal_para_db($this->input->post('nr_inadimplencia_rgesul', true));
        $nr_meta_rgesul                             = app_decimal_para_db($this->input->post('nr_meta_rgesul', true));

        $nr_carga_ceeemigrado                       = app_decimal_para_db($this->input->post('nr_carga_ceeemigrado', true));
        $nr_inadimplencia_ceeemigrado               = app_decimal_para_db($this->input->post('nr_inadimplencia_ceeemigrado', true));
        $nr_meta_ceeemigrado                        = app_decimal_para_db($this->input->post('nr_meta_ceeemigrado', true));

        $nr_carga_fundacaomigrado                   = app_decimal_para_db($this->input->post('nr_carga_fundacaomigrado', true));
        $nr_inadimplencia_fundacaomigrado           = app_decimal_para_db($this->input->post('nr_inadimplencia_fundacaomigrado', true));
        $nr_meta_fundacaomigrado                    = app_decimal_para_db($this->input->post('nr_meta_fundacaomigrado', true));

        $nr_carga_ceeenovos                         = app_decimal_para_db($this->input->post('nr_carga_ceeenovos', true));
        $nr_inadimplencia_ceeenovos                 = app_decimal_para_db($this->input->post('nr_inadimplencia_ceeenovos', true));
        $nr_meta_ceeenovos                          = app_decimal_para_db($this->input->post('nr_meta_ceeenovos', true));

        $nr_carga_fundacaonovos                     = app_decimal_para_db($this->input->post('nr_carga_fundacaonovos', true));
        $nr_inadimplencia_fundacaonovos             = app_decimal_para_db($this->input->post('nr_inadimplencia_fundacaonovos', true));
        $nr_meta_fundacaonovos                      = app_decimal_para_db($this->input->post('nr_meta_fundacaonovos', true));

        $nr_carga_crm                               = app_decimal_para_db($this->input->post('nr_carga_crm', true));
        $nr_inadimplencia_crm                       = app_decimal_para_db($this->input->post('nr_inadimplencia_crm', true));
        $nr_meta_crm                                = app_decimal_para_db($this->input->post('nr_meta_crm', true));

        $nr_carga_inpel                             = app_decimal_para_db($this->input->post('nr_carga_inpel', true));
        $nr_inadimplencia_inpel                     = app_decimal_para_db($this->input->post('nr_inadimplencia_inpel', true));
        $nr_meta_inpel                              = app_decimal_para_db($this->input->post('nr_meta_inpel', true));

        $nr_carga_foz                               = app_decimal_para_db($this->input->post('nr_carga_foz', true));
        $nr_inadimplencia_foz                       = app_decimal_para_db($this->input->post('nr_inadimplencia_foz', true));
        $nr_meta_foz                                = app_decimal_para_db($this->input->post('nr_meta_foz', true));

        $nr_carga_ceran                             = app_decimal_para_db($this->input->post('nr_carga_ceran', true));
        $nr_inadimplencia_ceran                     = app_decimal_para_db($this->input->post('nr_inadimplencia_ceran', true));
        $nr_meta_ceran                              = app_decimal_para_db($this->input->post('nr_meta_ceran', true));


        $nr_percentual_ceee            = ($nr_inadimplencia_ceee / ($nr_carga_ceee > 0 ? $nr_carga_ceee : 1)) * 100;
        $nr_percentual_cgtee           = ($nr_inadimplencia_cgtee / ($nr_carga_cgtee > 0 ? $nr_carga_cgtee : 1)) * 100;
        $nr_percentual_rge             = ($nr_inadimplencia_rge / ($nr_carga_rge > 0 ? $nr_carga_rge : 1)) * 100;
        $nr_percentual_rgesul          = ($nr_inadimplencia_rgesul / ($nr_carga_rgesul > 0 ? $nr_carga_rgesul : 1)) * 100;
        $nr_percentual_ceeemigrado     = ($nr_inadimplencia_ceeemigrado / ($nr_carga_ceeemigrado > 0 ? $nr_carga_ceeemigrado : 1)) * 100;
        $nr_percentual_fundacaomigrado = ($nr_inadimplencia_fundacaomigrado / ($nr_carga_fundacaomigrado > 0 ? $nr_carga_fundacaomigrado : 1)) * 100;
        $nr_percentual_ceeenovos       = ($nr_inadimplencia_ceeenovos / ($nr_carga_ceeenovos > 0 ? $nr_carga_ceeenovos : 1)) * 100;
        $nr_percentual_fundacaonovos   = ($nr_inadimplencia_fundacaonovos / ($nr_carga_fundacaonovos > 0 ? $nr_carga_fundacaonovos : 1)) * 100;
        $nr_percentual_crm             = ($nr_inadimplencia_crm / ($nr_carga_crm > 0 ? $nr_carga_crm : 1)) * 100;
        $nr_percentual_inpel           = ($nr_inadimplencia_inpel / ($nr_carga_inpel > 0 ? $nr_carga_inpel : 1)) * 100;
        $nr_percentual_foz             = ($nr_inadimplencia_foz / ($nr_carga_foz > 0 ? $nr_carga_foz : 1)) * 100;
        $nr_percentual_ceran           = ($nr_inadimplencia_ceran / ($nr_carga_ceran > 0 ? $nr_carga_ceran : 1)) * 100;

        $nr_inadimplencia_resultado = $nr_inadimplencia_ceee + $nr_inadimplencia_cgtee + $nr_inadimplencia_rge + $nr_inadimplencia_rgesul +                                         $nr_inadimplencia_ceeemigrado + $nr_inadimplencia_fundacaomigrado + $nr_inadimplencia_ceeenovos +                                             $nr_inadimplencia_fundacaonovos + $nr_inadimplencia_crm + $nr_inadimplencia_inpel + $nr_inadimplencia_foz +                                   $nr_inadimplencia_ceran;
        $nr_carga_resultado         = $nr_carga_ceee + $nr_carga_cgtee + $nr_carga_rge + $nr_carga_rgesul + $nr_carga_ceeemigrado +                                                 $nr_carga_fundacaomigrado + $nr_carga_ceeenovos + $nr_carga_fundacaonovos + $nr_carga_crm + $nr_carga_inpel +                                 $nr_carga_foz + $nr_carga_ceran;
        $nr_meta_total              = $nr_meta_ceee + $nr_meta_cgtee + $nr_meta_rge + $nr_meta_rgesul + $nr_meta_ceeemigrado +                                                      $nr_meta_fundacaomigrado + $nr_meta_ceeenovos + $nr_meta_fundacaonovos + $nr_meta_crm + $nr_meta_inpel +                                      $nr_meta_foz + $nr_meta_ceran;
        $nr_meta_resultado          = $nr_meta_total / 12;
        $nr_percentual_resultado    = ($nr_inadimplencia_resultado / $nr_carga_resultado) * 100;

        $nr_ponderada_ceee            = $nr_inadimplencia_ceee / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_cgtee           = $nr_inadimplencia_cgtee / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_rge             = $nr_inadimplencia_rge / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_rgesul          = $nr_inadimplencia_rgesul / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderado_ceeemigrado     = $nr_inadimplencia_ceeemigrado / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_fundacaomigrado = $nr_inadimplencia_fundacaomigrado / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_ceeenovos       = $nr_inadimplencia_ceeenovos / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_fundacaonovos   = $nr_inadimplencia_fundacaonovos / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_crm             = $nr_inadimplencia_crm / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_inpel           = $nr_inadimplencia_inpel / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_foz             = $nr_inadimplencia_foz / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);
        $nr_ponderada_ceran           = $nr_inadimplencia_ceran / ($nr_inadimplencia_resultado > 0 ? $nr_inadimplencia_resultado : 1);

        $nr_resultado_ceee            = $nr_ponderada_ceee * $nr_percentual_ceee;
        $nr_resultado_cgtee           = $nr_ponderada_cgtee * $nr_percentual_cgtee;
        $nr_resultado_rge             = $nr_ponderada_rge * $nr_percentual_rge;
        $nr_resultado_rgesul          = $nr_ponderada_rgesul * $nr_percentual_rgesul;
        $nr_resultado_ceeemigrado     = $nr_ponderado_ceeemigrado * $nr_percentual_ceeemigrado;
        $nr_resultado_fundacaomigrado = $nr_ponderada_fundacaomigrado * $nr_percentual_fundacaomigrado;
        $nr_resultado_ceeenovos       = $nr_ponderada_ceeenovos * $nr_percentual_ceeenovos;
        $nr_resultado_fundacaonovos   = $nr_ponderada_fundacaonovos * $nr_percentual_fundacaonovos;
        $nr_resultado_crm             = $nr_ponderada_crm * $nr_percentual_crm;
        $nr_resultado_inpel           = $nr_ponderada_inpel * $nr_percentual_inpel;
        $nr_resultado_foz             = $nr_ponderada_foz * $nr_percentual_foz;
        $nr_resultado_ceran           = $nr_ponderada_ceran * $nr_percentual_ceran;

        $nr_ponderada_resultado = $nr_ponderada_ceee + $nr_ponderada_cgtee + $nr_ponderada_rge + $nr_ponderada_rgesul + $nr_ponderado_ceeemigrado +                             $nr_ponderada_fundacaomigrado + $nr_ponderada_ceeenovos + $nr_ponderada_fundacaonovos + $nr_ponderada_crm +                                   $nr_ponderada_inpel + $nr_ponderada_foz + $nr_ponderada_ceran;
        $nr_resultado_resultado = $nr_resultado_ceee + $nr_resultado_cgtee + $nr_resultado_rge + $nr_resultado_rgesul + $nr_resultado_ceeemigrado +                             $nr_resultado_fundacaomigrado + $nr_resultado_ceeenovos + $nr_resultado_fundacaonovos + $nr_resultado_crm +                                   $nr_resultado_inpel + $nr_resultado_foz + $nr_resultado_ceran;

        $args = array(
            'cd_indicador_tabela'              => $this->input->post('cd_indicador_tabela', true),
            'dt_referencia'                    => $this->input->post('dt_referencia', true),
            'ds_observacao'                    => $this->input->post('ds_observacao', true),
            'ds_tabela'                        => '',
            'fl_media'                         => 'N',

            'nr_carga_ceee'                    => $nr_carga_ceee,
            'nr_inadimplencia_ceee'            => $nr_inadimplencia_ceee ,
            'nr_meta_ceee'                     => $nr_meta_ceee ,
            'nr_percentual_ceee'               => $nr_percentual_ceee ,
            'nr_ponderada_ceee'                => $nr_ponderada_ceee ,
            'nr_resultado_ceee'                => $nr_resultado_ceee ,

            'nr_carga_cgtee'                   => $nr_carga_cgtee ,
            'nr_inadimplencia_cgtee'           => $nr_inadimplencia_cgtee ,
            'nr_meta_cgtee'                    => $nr_meta_cgtee ,
            'nr_percentual_cgtee'              => $nr_percentual_cgtee ,
            'nr_ponderada_cgtee'               => $nr_ponderada_cgtee ,
            'nr_resultado_cgtee'               => $nr_resultado_cgtee ,

            'nr_carga_rge'                     => $nr_carga_rge ,
            'nr_inadimplencia_rge'             => $nr_inadimplencia_rge ,
            'nr_meta_rge'                      => $nr_meta_rge ,
            'nr_percentual_rge'                => $nr_percentual_rge ,
            'nr_ponderada_rge'                 => $nr_ponderada_rge ,
            'nr_resultado_rge'                 => $nr_resultado_rge ,

            'nr_carga_rgesul'                  => $nr_carga_rgesul ,
            'nr_inadimplencia_rgesul'          => $nr_inadimplencia_rgesul ,
            'nr_meta_rgesul'                   => $nr_meta_rgesul ,
            'nr_percentual_rgesul'             => $nr_percentual_rgesul ,
            'nr_ponderada_rgesul'              => $nr_ponderada_rgesul ,
            'nr_resultado_rgesul'              => $nr_resultado_rgesul ,

            'nr_carga_ceeemigrado'             => $nr_carga_ceeemigrado ,
            'nr_inadimplencia_ceeemigrado'     => $nr_inadimplencia_ceeemigrado ,
            'nr_meta_ceeemigrado'              => $nr_meta_ceeemigrado ,
            'nr_percentual_ceeemigrado'        => $nr_percentual_ceeemigrado ,
            'nr_ponderado_ceeemigrado'         => $nr_ponderado_ceeemigrado,
            'nr_resultado_ceeemigrado'         => $nr_resultado_ceeemigrado,

            'nr_carga_fundacaomigrado'         => $nr_carga_fundacaomigrado,
            'nr_inadimplencia_fundacaomigrado' => $nr_inadimplencia_fundacaomigrado,
            'nr_meta_fundacaomigrado'          => $nr_meta_fundacaomigrado,
            'nr_percentual_fundacaomigrado'    => $nr_percentual_fundacaomigrado,
            'nr_ponderada_fundacaomigrado'     => $nr_ponderada_fundacaomigrado,
            'nr_resultado_fundacaomigrado'     => $nr_resultado_fundacaomigrado,

            'nr_carga_ceeenovos'               => $nr_carga_ceeenovos,
            'nr_inadimplencia_ceeenovos'       => $nr_inadimplencia_ceeenovos,
            'nr_meta_ceeenovos'                => $nr_meta_ceeenovos,
            'nr_percentual_ceeenovos'          => $nr_percentual_ceeenovos,
            'nr_ponderada_ceeenovos'           => $nr_ponderada_ceeenovos,
            'nr_resultado_ceeenovos'           => $nr_resultado_ceeenovos,

            'nr_carga_fundacaonovos'           => $nr_carga_fundacaonovos,
            'nr_inadimplencia_fundacaonovos'   => $nr_inadimplencia_fundacaonovos,
            'nr_meta_fundacaonovos'            => $nr_meta_fundacaonovos,
            'nr_percentual_fundacaonovos'      => $nr_percentual_fundacaonovos,
            'nr_ponderada_fundacaonovos'       => $nr_ponderada_fundacaonovos,
            'nr_resultado_fundacaonovos'       => $nr_resultado_fundacaonovos,

            'nr_carga_crm'                     => $nr_carga_crm,
            'nr_inadimplencia_crm'             => $nr_inadimplencia_crm,
            'nr_meta_crm'                      => $nr_meta_crm,
            'nr_percentual_crm'                => $nr_percentual_crm,
            'nr_ponderada_crm'                 => $nr_ponderada_crm,
            'nr_resultado_crm'                 => $nr_resultado_crm,

            'nr_carga_inpel'                   => $nr_carga_inpel,
            'nr_inadimplencia_inpel'           => $nr_inadimplencia_inpel,
            'nr_meta_inpel'                    => $nr_meta_inpel,
            'nr_percentual_inpel'              => $nr_percentual_inpel,
            'nr_ponderada_inpel'               => $nr_ponderada_inpel,
            'nr_resultado_inpel'               => $nr_resultado_inpel,

            'nr_carga_foz'                     => $nr_carga_foz,
            'nr_inadimplencia_foz'             => $nr_inadimplencia_foz,
            'nr_meta_foz'                      => $nr_meta_foz,
            'nr_percentual_foz'                => $nr_percentual_foz,
            'nr_ponderada_foz'                 => $nr_ponderada_foz,
            'nr_resultado_foz'                 => $nr_resultado_foz,

            'nr_carga_ceran'                   => $nr_carga_ceran,
            'nr_inadimplencia_ceran'           => $nr_inadimplencia_ceran,
            'nr_meta_ceran'                    => $nr_meta_ceran,
            'nr_percentual_ceran'              => $nr_percentual_ceran,
            'nr_ponderada_ceran'               => $nr_ponderada_ceran,
            'nr_resultado_ceran'               => $nr_resultado_ceran,

            'nr_carga_resultado'               => $nr_carga_resultado,
            'nr_inadimplencia_resultado'       => $nr_inadimplencia_resultado,
            'nr_meta_resultado'                => $nr_meta_resultado,
            'nr_percentual_resultado'          => $nr_percentual_resultado,
            'nr_ponderada_resultado'           => $nr_ponderada_resultado,
            'nr_resultado_resultado'           => $nr_resultado_resultado,
            
            'cd_usuario'                       => $this->session->userdata('codigo') 
        );

        $args['ds_tabela'] = $this->monta_tabela($args);

        if(intval($cd_financeiro_inadimplencia_previdenciaria) == 0)
        {
            $this->financeiro_inadimplencia_previdenciaria_model->salvar($args);
        }
        else
        {
            $this->financeiro_inadimplencia_previdenciaria_model->atualizar($cd_financeiro_inadimplencia_previdenciaria, $args);
        }

        $this->criar_indicador();

        redirect('indicador_plugin/financeiro_inadimplencia_previdenciaria/index', 'refresh');
    }

    private function monta_tabela($args)
    {
        $tabela = '<table class="sort-table sub-table" width="100%" cellspacing="2" cellpadding="2" align="center">';
       
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th>Plano</th>';
        $tabela .= '<th>Carga</th>';
        $tabela .= '<th>Inadimplência</th>';
        $tabela .= '<th>Meta</th>';
        $tabela .= '<th>Resultado</th>';
        $tabela .= '<th></th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';

        $tabela .= '<tbody>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CEEE</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_ceee'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_ceee'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_ceee'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_ceee'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_ceee']) <= floatval($args['nr_meta_ceee']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CGTEE</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_cgtee'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_cgtee'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_cgtee'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_cgtee'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_cgtee']) <= floatval($args['nr_meta_cgtee']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>RGE</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_rge'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_rge'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_rge'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_rge'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_rge']) <= floatval($args['nr_meta_rge']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>RGE SUL</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_rgesul'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_rgesul'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_rgesul'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_rgesul'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_meta_rgesul']) <= floatval($args['nr_meta_rgesul']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CEEE MIGRADO</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_ceeemigrado'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_ceeemigrado'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_ceeemigrado'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_ceeemigrado'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_ceeemigrado']) <= floatval($args['nr_meta_ceeemigrado']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>FUND. MIGRADO</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_fundacaomigrado'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_fundacaomigrado'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_fundacaomigrado'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_fundacaomigrado'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_fundacaomigrado']) <= floatval($args['nr_meta_fundacaomigrado']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CEEE NOVOS</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_ceeenovos'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_ceeenovos'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_ceeenovos'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_ceeenovos'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_ceeenovos']) <= floatval($args['nr_meta_ceeenovos']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>FUND. NOVOS</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_fundacaonovos'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_fundacaonovos'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_fundacaonovos'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_fundacaonovos'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_fundacaonovos']) <= floatval($args['nr_meta_fundacaonovos']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CRM</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_crm'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_crm'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_crm'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_crm'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_crm']) <= floatval($args['nr_meta_crm']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>INPEL</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_inpel'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_inpel'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_inpel'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_inpel'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_inpel']) <= floatval($args['nr_meta_inpel']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>FOZ</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_foz'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_foz'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_foz'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_foz'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_foz']) <= floatval($args['nr_meta_foz']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';

        $tabela .= '</tr>';

        $tabela .= '<tr>';
		$tabela .= '<td style="text-align:left;"><b>CERAN</b></td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_carga_ceran'], 2, ',', '.').'</td>';
		$tabela .= '<td style="text-align:center;">'.number_format($args['nr_inadimplencia_ceran'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_meta_ceran'], 2, ',', '.').'</td>';
        $tabela .= '<td style="text-align:center;">'.number_format($args['nr_resultado_ceran'], 2, ',', '.').'</td>';

        $tabela .= '<td style="text-align:center;">';
        $tabela .= '<img src="';

        if(floatval($args['nr_resultado_ceran']) <= floatval($args['nr_meta_ceran']))
        {
            $tabela .= base_url().'img/indicador_status/atendeu.png';
        }
        else
        {
            $tabela .= base_url().'img/indicador_status/nao_atendeu.png';
        }

        $tabela .= '" style="width:16px;">';

        $tabela .= '</td>';
        
        $tabela .= '</tr>';
        
        $tabela .= '</tbody>';

        $tabela .= '</table>';

        return $tabela;
    }

    public function excluir($cd_financeiro_inadimplencia_previdenciaria)
    {
        if(indicador_db::verificar_permissao($this->cd_usuario, 'GFC'))
        {
            $this->financeiro_inadimplencia_previdenciaria_model->excluir(
                $cd_financeiro_inadimplencia_previdenciaria, 
                $this->session->userdata('codigo')
            );

            $this->criar_indicador();

            redirect('indicador_plugin/financeiro_inadimplencia_previdenciaria/index', 'refresh');
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function criar_indicador()
    {
        if(indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$data['label_0'] = $this->label_0;
			$data['label_1'] = $this->label_1;
			$data['label_2'] = $this->label_2;
			$data['label_3'] = $this->label_3;
			$data['label_4'] = $this->label_4;
			$data['label_5'] = $this->label_5;
			
			$tabela = indicador_tabela_aberta( intval($this->enum_indicador) );

			$sql = " DELETE FROM indicador.indicador_parametro WHERE cd_indicador_tabela=".intval($tabela[0]['cd_indicador_tabela'])."; ";
			
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0,0, utf8_encode($data['label_0']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1,0, utf8_encode($data['label_1']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2,0, utf8_encode($data['label_2']), 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3,0, utf8_encode($data['label_3']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4,0, utf8_encode($data['label_4']), 'background,center');
            $sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5,0, '', 'background,center');
			$sql.=indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6,0, utf8_encode($data['label_5']), 'background,center');
			
			$collection = $this->financeiro_inadimplencia_previdenciaria_model->listar($tabela[0]['cd_indicador_tabela']);
			
			$indicador                  = array();
			$linha                      = 0;			
			$contador_ano_atual         = 0;
			$nr_carga_resultado         = 0;
			$nr_inadimplencia_resultado = 0;
			$nr_meta_resultado          = 0;
			$nr_resultado_resultado     = 0;
			
			foreach($collection as $item)
			{
				if(intval($item['ano_referencia']) >= intval($tabela[0]['nr_ano_referencia']) -5)
				{
					if(trim($item['fl_media']) == 'S')
					{
						$referencia = ' Resultado de ' . $item['ano_referencia'];
					}
					else
					{
						$referencia = $item['mes_ano_referencia'];
					}
					
					if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
					{
						$contador_ano_atual++;

						$nr_carga_resultado         += $item['nr_carga_resultado'];
						$nr_inadimplencia_resultado += $item['nr_inadimplencia_resultado'];
						$nr_meta_resultado          = $item['nr_meta_resultado'];
					}

					$indicador[$linha][0] = $referencia;
					$indicador[$linha][1] = $item['nr_carga_resultado'];
					$indicador[$linha][2] = $item['nr_inadimplencia_resultado'];
					$indicador[$linha][3] = $item['nr_meta_resultado'];
					$indicador[$linha][4] = $item['nr_resultado_resultado'];
					$indicador[$linha][5] = $item['ds_tabela'];
					$indicador[$linha][6] = $item['ds_observacao'];

					$linha++;
				}
			}	
				
			$linha_sem_media = $linha;

			if(intval($contador_ano_atual) > 0)
			{	
                if(intval($nr_carga_resultado) > 0)
                {
                    $nr_resultado_resultado = ($nr_inadimplencia_resultado / $nr_carga_resultado) * 100;
                }

				$indicador[$linha][0] = '';
				$indicador[$linha][1] = '';
				$indicador[$linha][2] = '';
				$indicador[$linha][3] = '';
				$indicador[$linha][4] = '';
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
				
				$linha++;

				$indicador[$linha][0] = '<b>Resultado de '.intval($tabela[0]['nr_ano_referencia']).'</b>';
				$indicador[$linha][1] = $nr_carga_resultado;
				$indicador[$linha][2] = $nr_inadimplencia_resultado;
				$indicador[$linha][3] = $nr_meta_resultado;
				$indicador[$linha][4] = $nr_resultado_resultado;
				$indicador[$linha][5] = '';
				$indicador[$linha][6] = '';
			}
			
			$linha = 1;
	
			for($i=0; $i<sizeof($indicador); $i++)
			{
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 0, $linha, utf8_encode($indicador[$i][0]), 'background,center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 1, $linha, app_decimal_para_php($indicador[$i][1]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 2, $linha, app_decimal_para_php($indicador[$i][2]), 'center', 'S', 2);
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 3, $linha, app_decimal_para_php($indicador[$i][3]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 4, $linha, app_decimal_para_php($indicador[$i][4]), 'center', 'S', 2, 'S');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 5, $linha, utf8_encode($indicador[$i][5]), 'center');
				$sql .= indicador_db::sql_inserir_celula($tabela[0]['cd_indicador_tabela'], 6, $linha, utf8_encode(nl2br($indicador[$i][6])),'justify');
				
				$linha++;
			}

			$coluna_para_ocultar='';
			$sql.=indicador_db::sql_inserir_grafico(
				$tabela[0]['cd_indicador_tabela'],
				enum_indicador_grafico_tipo::LINHA,
				'3,3,0,0;4,4,0,0',
				"0,0,1,$linha_sem_media",
				"3,3,1,$linha_sem_media;4,4,1,$linha_sem_media",
				$this->cd_usuario,
				$coluna_para_ocultar,
				1,
				2
			);

			$this->db->query($sql);
		}
		else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function fechar_periodo()
	{
		if(indicador_db::verificar_permissao($this->cd_usuario,'GFC'))
		{
			$tabela = indicador_tabela_aberta($this->enum_indicador);
			
			$collection = $this->financeiro_inadimplencia_previdenciaria_model->listar($tabela[0]['cd_indicador_tabela']);

            $contador_ano_atual         = 0;
            $nr_carga_resultado         = 0;
			$nr_inadimplencia_resultado = 0;
			$nr_meta_resultado          = 0;
			$nr_resultado_resultado     = 0;
			
			foreach($collection as $item)
			{			 
                if(intval($item['ano_referencia']) == intval($tabela[0]['nr_ano_referencia']))
                {
                    $contador_ano_atual++;

                    $nr_carga_resultado         += $item['nr_carga_resultado'];
                    $nr_inadimplencia_resultado += $item['nr_inadimplencia_resultado'];
                    $nr_meta_resultado          = $item['nr_meta_resultado'];
                    $nr_resultado_resultado     += $item['nr_resultado_resultado'];
                }
			}

			if(intval($contador_ano_atual) > 0)
			{			
				$args = array(
					'cd_financeiro_inadimplencia_previdenciaria' => 0,
					'cd_indicador_tabela' 						=> $tabela[0]['cd_indicador_tabela'],
                    'dt_referencia'       						=> '01/01/'.intval($tabela[0]['nr_ano_referencia']),
                    'ds_tabela'                                 => '',
					'fl_media'			  						=> 'S',
                    'ds_observacao'		  						=> '',
                    
                    'nr_carga_ceee'                             => '',
                    'nr_inadimplencia_ceee'                     => '',
                    'nr_meta_ceee'                              => '',
                    'nr_percentual_ceee'                        => '',
                    'nr_ponderada_ceee'                         => '',
                    'nr_resultado_ceee'                         => '',
                
                    'nr_carga_cgtee'                            => '',
                    'nr_inadimplencia_cgtee'                    => '',
                    'nr_meta_cgtee'                             => '',
                    'nr_percentual_cgtee'                       => '',
                    'nr_ponderada_cgtee'                        => '',
                    'nr_resultado_cgtee'                        => '',
                
                    'nr_carga_rge'                              => '',
                    'nr_inadimplencia_rge'                      => '',
                    'nr_meta_rge'                               => '',
                    'nr_percentual_rge'                         => '',
                    'nr_ponderada_rge'                          => '',
                    'nr_resultado_rge'                          => '',
                
                    'nr_carga_rgesul'                           => '',
                    'nr_inadimplencia_rgesul'                   => '',
                    'nr_meta_rgesul'                            => '',
                    'nr_percentual_rgesul'                      => '',
                    'nr_ponderada_rgesul'                       => '',
                    'nr_resultado_rgesul'                       => '',
                
                    'nr_carga_ceeemigrado'                      => '',
                    'nr_inadimplencia_ceeemigrado'              => '',
                    'nr_meta_ceeemigrado'                       => '',
                    'nr_percentual_ceeemigrado'                 => '',
                    'nr_ponderado_ceeemigrado'                  => '',
                    'nr_resultado_ceeemigrado'                  => '',
                
                    'nr_carga_fundacaomigrado'                  => '',
                    'nr_inadimplencia_fundacaomigrado'          => '',
                    'nr_meta_fundacaomigrado'                   => '',
                    'nr_percentual_fundacaomigrado'             => '',
                    'nr_ponderada_fundacaomigrado'              => '',
                    'nr_resultado_fundacaomigrado'              => '',
                
                    'nr_carga_ceeenovos'                        => '',
                    'nr_inadimplencia_ceeenovos'                => '',
                    'nr_meta_ceeenovos'                         => '',
                    'nr_percentual_ceeenovos'                   => '',
                    'nr_ponderada_ceeenovos'                    => '',
                    'nr_resultado_ceeenovos'                    => '',
                
                    'nr_carga_fundacaonovos'                    => '',
                    'nr_inadimplencia_fundacaonovos'            => '',
                    'nr_meta_fundacaonovos'                     => '',
                    'nr_percentual_fundacaonovos'               => '',
                    'nr_ponderada_fundacaonovos'                => '',
                    'nr_resultado_fundacaonovos'                => '',
                
                    'nr_carga_crm'                              => '',
                    'nr_inadimplencia_crm'                      => '',
                    'nr_meta_crm'                               => '',
                    'nr_percentual_crm'                         => '',
                    'nr_ponderada_crm'                          => '',
                    'nr_resultado_crm'                          => '',
                
                    'nr_carga_inpel'                            => '',
                    'nr_inadimplencia_inpel'                    => '',
                    'nr_meta_inpel'                             => '',
                    'nr_percentual_inpel'                       => '',
                    'nr_ponderada_inpel'                        => '',
                    'nr_resultado_inpel'                        => '',
                
                    'nr_carga_foz'                              => '',
                    'nr_inadimplencia_foz'                      => '',
                    'nr_meta_foz'                               => '',
                    'nr_percentual_foz'                         => '',
                    'nr_ponderada_foz'                          => '',
                    'nr_resultado_foz'                          => '',
                
                    'nr_carga_ceran'                            => '',
                    'nr_inadimplencia_ceran'                    => '',
                    'nr_meta_ceran'                             => '',
                    'nr_percentual_ceran'                       => '',
                    'nr_ponderada_ceran'                        => '',
                    'nr_resultado_ceran'                        => '',
                    
                    'nr_carga_resultado'                        => $nr_carga_resultado,
                    'nr_inadimplencia_resultado'                => $nr_inadimplencia_resultado,
                    'nr_meta_resultado'                         => $nr_meta_resultado,
                    'nr_percentual_resultado'                   => '',
                    'nr_ponderada_resultado'                    => '',
                    'nr_resultado_resultado'                    => $nr_resultado_resultado,

					'cd_usuario'          						=> $this->cd_usuario
                );

				$this->financeiro_inadimplencia_previdenciaria_model->salvar($args);
			}

			$this->financeiro_inadimplencia_previdenciaria_model->fechar_periodo($tabela[0]['cd_indicador_tabela'], $this->cd_usuario);

			redirect('indicador_plugin/financeiro_inadimplencia_previdenciaria/index', 'refresh');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
	}
}