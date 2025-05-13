<?php
class Rh extends Controller
{
	function __construct()
	{
		parent::Controller();
		if( ! CheckLogin() ) exit;
	}
	
	function pessoa()
	{
		$this->load->view('rh/pessoa');
	}
	function pessoa_result()
	{
		$this->load->library('pagination');

		// filtros
		$page = $this->input->post('current_page');
		$nome = $this->input->post('nome');
		$divisao = $this->input->post('divisao');
		$guerra = $this->input->post('nome_usual');
		$usuario = $this->input->post('usuario');

		// count results
		$this->db->where_not_in('tipo', array('X', 'T'));
		$this->db->where_not_in('divisao', array('SNG', 'LM2'));
		$this->db->like('upper(nome)',strtoupper($nome));
		$this->db->like('upper(guerra)',strtoupper($guerra));
		$this->db->like('upper(usuario)',strtoupper($usuario));
		if($divisao!="") $this->db->where('divisao', $divisao);

		$count = $this->db->count_all_results('projetos.usuarios_controledi');

		// Setup pagination
		$this->setup_pagination($count);

		// load results
		$this->db->where_not_in('tipo', array('X', 'T'));
		$this->db->where_not_in('divisao', array('SNG', 'LM2'));
		$this->db->like('upper(nome)',strtoupper($nome));
		$this->db->like('upper(guerra)',strtoupper($guerra));
		$this->db->like('upper(usuario)',strtoupper($usuario));
		if($divisao!="") $this->db->where('divisao', $divisao);
		
		$this->db->order_by('nome');
		$result = $this->db->get_where(
			'projetos.usuarios_controledi', 
			null, 
			$this->pagination->per_page, 
			$page
		);
		$data['usuarios'] = $result->result();

		$this->load->view('rh/pessoa_partial_result', $data);
	}

	private function setup_pagination($count)
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $this->config->item('base_url') . 'index.php/rh/pessoa';
		$config['per_page'] = 10;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
}
?>