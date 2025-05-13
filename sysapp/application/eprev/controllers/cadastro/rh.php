<?php
class Rh extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    private function get_permissao()
    {
    	CheckLogin();

    	if($this->session->userdata('indic_09') == '*')
        {
            return TRUE;
        }
        else if($this->session->userdata('indic_05') == 'S')
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function index()
    {
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/rh_model');

			$data['gerencia'] = $this->rh_model->get_gerencia_usuario();

			$this->load->view('cadastro/rh/index', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    public function listar()
    {
    	$this->load->model('rh_avaliacao/rh_model');

		$args = array(
			'cd_gerencia' => $this->input->post('cd_gerencia', TRUE),
			'fl_ativo'    => $this->input->post('fl_ativo', TRUE)
		);

		manter_filtros($args);

		$data['collection'] = $this->rh_model->listar($args);

		foreach ($data['collection'] as $key => $item) 
		{
			$row = $this->rh_model->get_progresso_promocao($item['codigo']);

			$data['collection'][$key]['dt_progressao_promocao'] = (isset($row['dt_progressao_promocao']) ? $row['dt_progressao_promocao'] : '');
			$data['collection'][$key]['ds_cargo_area_atuacao']  = (isset($row['ds_cargo_area_atuacao']) ? $row['ds_cargo_area_atuacao'] : '');
			$data['collection'][$key]['ds_classe']              = (isset($row['ds_classe']) ? $row['ds_classe'] : '');
		}

		$this->load->view('cadastro/rh/index_result', $data);     		
    }

    public function cadastro($cd_usuario)
    {
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/rh_model');

			$data = array(
				'cargo'        => $this->rh_model->get_cargo(),
				'diretoria'    => $this->rh_model->get_diretoria(),
				'escolaridade' => $this->rh_model->get_escolaridade(),
				'row'          => $this->rh_model->carrega($cd_usuario),
			);

			$data['gerencia'] = $this->rh_model->get_gerencia_vigente($data['row']['cd_gerencia']);

			$data['gerencia_unidade'] = array();

			if(trim($data['row']['cd_gerencia']) != '')
			{
				$data['gerencia_unidade'] = $this->rh_model->get_gerencia_unidade($data['row']['cd_gerencia']);
			}

			$row = $this->rh_model->get_progresso_promocao($data['row']['cd_usuario']);

			$data['row']['dt_progressao_promocao'] = (isset($row['dt_progressao_promocao']) ? $row['dt_progressao_promocao'] : '');
			$data['row']['ds_cargo_area_atuacao']  = (isset($row['ds_cargo_area_atuacao']) ? $row['ds_cargo_area_atuacao'] : '');
			$data['row']['ds_classe']              = (isset($row['ds_classe']) ? $row['ds_classe'] : '');
			$data['row']['arquivo']                = (isset($row['arquivo']) ? $row['arquivo'] : '');
			$data['row']['arquivo_nome']           = (isset($row['arquivo_nome']) ? $row['arquivo_nome'] : '');

			$this->load->view('cadastro/rh/cadastro', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	public function salvar()
    {
		if($this->get_permissao())
		{
			$this->load->model('rh_avaliacao/rh_model');

			$cd_usuario = $this->input->post('cd_usuario', TRUE);

			$args = array(
				'usuario'               => $this->input->post('usuario', TRUE),
				'nome'                  => $this->input->post('nome', TRUE),
				'guerra'                => $this->input->post('guerra', TRUE),
				'celular'               => $this->input->post('celular', TRUE),
				'tipo'                  => $this->input->post('tipo', TRUE),
				'dt_nascimento'         => $this->input->post('dt_nascimento', TRUE),
				'dt_admissao'           => $this->input->post('dt_admissao', TRUE),
				'cd_gerencia'           => $this->input->post('cd_gerencia', TRUE),
				'cd_gerencia_unidade'   => $this->input->post('cd_gerencia_unidade', TRUE),
				'cd_registro_empregado' => $this->input->post('cd_registro_empregado', TRUE),
				'cd_escolaridade'       => $this->input->post('cd_escolaridade', TRUE),
				'cd_cargo_area_atuacao' => $this->input->post('cd_cargo_area_atuacao', TRUE),
				'cd_diretoria'          => $this->input->post('cd_diretoria', TRUE),
				'cd_cargo'              => $this->input->post('cd_cargo', TRUE),
				'fl_exibe_cpuscanner'   => $this->input->post('fl_exibe_cpuscanner', TRUE),
				'fl_login_auto'         => $this->input->post('fl_login_auto', TRUE),
				'observacao'            => $this->input->post('observacao', TRUE),
				'fl_ldap_autenticar'    => $this->input->post('fl_ldap_autenticar', TRUE),
				'senha_md5'             => $this->input->post('senha_md5', TRUE),
				'senha_md5_old'         => $this->input->post('senha_md5_old', TRUE),
				'assinatura'            => $this->input->post('assinatura', TRUE),
				'nr_ramal'              => $this->input->post('nr_ramal', TRUE),
				'nr_ramal_callcenter'   => $this->input->post('nr_ramal_callcenter', TRUE),
				'nr_ip_callcenter'      => $this->input->post('nr_ip_callcenter', TRUE),
				'fl_intervalo'          => $this->input->post('fl_intervalo', TRUE),
				'indic_01'              => $this->input->post('indic_01', TRUE),
				'indic_02'              => $this->input->post('indic_02', TRUE),
				'indic_03'              => $this->input->post('indic_03', TRUE),
				'indic_04'              => $this->input->post('indic_04', TRUE),
				'indic_06'              => $this->input->post('indic_06', TRUE),
				'indic_07'              => $this->input->post('indic_07', TRUE),
				'indic_09'              => $this->input->post('indic_09', TRUE),
				'indic_10'              => $this->input->post('indic_10', TRUE),
				'indic_12'              => $this->input->post('indic_12', TRUE),
				'indic_13'              => $this->input->post('indic_13', TRUE)
			);

			$this->rh_model->salvar($cd_usuario, $args);

			redirect('cadastro/rh/cadastro/'.$cd_usuario, 'refresh');
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }	
	
    public function get_usuario_foto($usuario)
    {
    	$this->load->model('rh_avaliacao/rh_model');

		$row = $this->rh_model->get_usuario_foto($usuario);
		
		$avatar = $row['avatar'];
		
		if(trim($avatar) == '')
		{
			$avatar = $row['usuario'].'.png';
		}
		
		if(!file_exists('./up/avatar/'.$avatar))
		{
			$avatar = 'user.png';
		}

		$file = base_url().'up/avatar/'.$avatar;

		$image = @imagecreatefrompng($file);

		if(!$image)
		{
			$image = @imagecreatefromjpeg($file);
		}


		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));

		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);

		header('Content-Type: image/jpeg');

		$quality = 50;
		imagejpeg($bg);
		imagedestroy($bg);
	}
}
?>