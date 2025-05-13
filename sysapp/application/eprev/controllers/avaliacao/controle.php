<?php
class controle extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function abertura($ano=0)
	{
		CheckLogin();

		if( ! gerencia_in( array("GAD", "GI") ) ) {echo "Apenas usuários da GAD"; return false; exit;}

		if(intval($ano)!=0)
		{
			$data['dt_periodo'] = intval($ano);

			$q = $this->db->query( "
			SELECT to_char(dt_abertura, 'DD/MM/YYYY') as dt_abertura, to_char(dt_fechamento, 'DD/MM/YYYY') as dt_fechamento
			FROM projetos.avaliacao_controle 
			WHERE dt_periodo=?"
			, array(intval($ano)) );

			$data['record'] = $q->row_array();
			if(sizeof($data['record'])==0)
			{
				$data['record'] = array(  'dt_abertura'=>'', 'dt_fechamento'=>''  );
			}
			
			$this->load->view('avaliacao/controle/abertura', $data);
		}
		else
		{
			redirect( 'avaliacao/controle/abertura/'.date('Y'), 'refresh' );
		}
	}

	function salvar()
	{
		CheckLogin();
		
		if( ! gerencia_in( array("GAD") ) ) {echo "Apenas usuários da GAD"; return false; exit;}
				
		$dt_periodo = $this->input->post('dt_periodo');
		$dt_inicio = $this->input->post('dt_abertura');
		$dt_fim = $this->input->post('dt_fechamento');
		
		$q = $this->db->query( "SELECT COUNT(*) AS c FROM projetos.avaliacao_controle WHERE dt_periodo=?", array(intval($dt_periodo)) );
		$r = $q->row_array();
		
		if(intval($r['c'])>0)
		{
			$q = $this->db->query( " 
			UPDATE projetos.avaliacao_controle 
			SET dt_abertura=TO_DATE(?, 'DD/MM/YYYY')
			, dt_fechamento=TO_DATE(?, 'DD/MM/YYYY') 
			WHERE dt_periodo=? ", 
			array($dt_inicio, $dt_fim, intval($dt_periodo)) );
		}
		else
		{
			$q = $this->db->query( "
			INSERT INTO projetos.avaliacao_controle 
			(dt_periodo, dt_abertura, dt_fechamento, cd_usuario_abertura, cd_usuario_fechamento) 
			VALUES 
			(?,TO_DATE(?, 'DD/MM/YYYY'),TO_DATE(?, 'DD/MM/YYYY'),?,?)", 
			array(
				intval($dt_periodo)
				, $dt_inicio, $dt_fim, usuario_id(), usuario_id()
				)
			);
		}
		
		redirect( 'avaliacao/controle/abertura/'.date('Y'), 'refresh' );
	}
}
?>