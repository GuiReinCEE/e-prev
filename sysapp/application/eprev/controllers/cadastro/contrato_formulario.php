<?php
class contrato_formulario extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('cadastro/contrato_formulario/index.php');
    }

    function listar()
    {
        CheckLogin();

        $this->load->model('projetos/Contrato_formulario_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		// --------------------------
		// listar ...

        $this->Contrato_formulario_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('cadastro/contrato_formulario/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		CheckLogin();

		$this->load->model('projetos/Contrato_formulario_model');
		$row['cd_contrato_formulario'] = intval($cd);

		$this->Contrato_formulario_model->carregar( $row );

		if($row) $data['row']=$row;
		$this->load->view('cadastro/contrato_formulario/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_contrato_formulario', TRUE);
		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.contrato_formulario
			(
				ds_contrato_formulario
				, dt_inclusao
				, cd_usuario_inclusao
			) 
			VALUES 
			( 
				'{ds_contrato_formulario}'
				, CURRENT_TIMESTAMP
				, {cd_usuario_inclusao}
			)
			";
		}
		else
		{
			$sql="
			UPDATE projetos.contrato_formulario
			SET ds_contrato_formulario = '{ds_contrato_formulario}'
			WHERE cd_contrato_formulario = {cd_contrato_formulario}
			";
		}

		esc("{ds_contrato_formulario}", $this->input->post("ds_contrato_formulario",TRUE), $sql, "str", FALSE); esc("{cd_usuario_inclusao}", usuario_id(), $sql, "int", FALSE); esc("{cd_contrato_formulario}", $this->input->post("cd_contrato_formulario",TRUE), $sql, "int", FALSE);

		$query = $this->db->query($sql);
		if(intval($codigo)==0)
		{
			$new_id = intval($this->db->insert_id("projetos.contrato_formulario", "cd_contrato_formulario"));
		}
		else
		{
			$new_id=intval($codigo);
		}

		redirect( "cadastro/contrato_formulario/detalhe/".$new_id, "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.contrato_formulario
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_contrato_formulario::varchar)='{cd_contrato_formulario}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_contrato_formulario}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		//echo $sql;

		redirect( 'cadastro/contrato_formulario', 'refresh' );
	}

	function salvar_grupo()
	{
		$dados['cd_contrato_formulario_grupo'] = intval($this->input->post( "cd_contrato_formulario_grupo", TRUE ));
		$dados['cd_contrato_formulario'] = $this->input->post( "cd_contrato_formulario", TRUE );
		$dados['ds_contrato_formulario_grupo'] = $this->input->post( "ds_contrato_formulario_grupo", TRUE );
		$dados['nr_ordem'] = $this->input->post( "nr_ordem", TRUE );

		if($dados['cd_contrato_formulario_grupo']==0)
		{
			$sql = "INSERT INTO projetos.contrato_formulario_grupo ( ds_contrato_formulario_grupo, cd_contrato_formulario, dt_inclusao, cd_usuario_inclusao, nr_ordem ) VALUES ( '{ds_contrato_formulario_grupo}', {cd_contrato_formulario}, CURRENT_TIMESTAMP, {cd_usuario_inclusao}, {nr_ordem} )";
		}
		else
		{
			$sql = "UPDATE projetos.contrato_formulario_grupo SET ds_contrato_formulario_grupo='{ds_contrato_formulario_grupo}', nr_ordem={nr_ordem} WHERE cd_contrato_formulario_grupo={cd_contrato_formulario_grupo} ";
		}
		esc( "{ds_contrato_formulario_grupo}", $dados['ds_contrato_formulario_grupo'], $sql );
		esc( "{cd_contrato_formulario}", intval( $dados['cd_contrato_formulario'] ), $sql );
		esc( "{cd_contrato_formulario_grupo}", intval( $dados['cd_contrato_formulario_grupo'] ), $sql );
		esc( "{cd_usuario_inclusao}", usuario_id(), $sql );
		esc( "{nr_ordem}", intval( $dados['nr_ordem'] ), $sql );

		$query=$this->db->query($sql);
	}

	function salvar_pergunta()
	{
		$dados['cd_contrato_formulario_grupo'] = $this->input->post( "cd_contrato_formulario_grupo", TRUE );
		$dados['ds_contrato_formulario_pergunta'] = $this->input->post( "ds_contrato_formulario_pergunta", TRUE );
		$dados['nr_ordem'] = $this->input->post( "nr_ordem", TRUE );
		$dados['cd_contrato_formulario_pergunta'] = intval($this->input->post( "cd_contrato_formulario_pergunta", TRUE ));

		if($dados['cd_contrato_formulario_pergunta']==0)
		{
			$sql = "INSERT INTO projetos.contrato_formulario_pergunta ( ds_contrato_formulario_pergunta, cd_contrato_formulario_grupo, dt_inclusao, cd_usuario_inclusao, nr_ordem ) VALUES ( '{ds_contrato_formulario_pergunta}', {cd_contrato_formulario_grupo}, CURRENT_TIMESTAMP, {cd_usuario_inclusao}, {nr_ordem} )";
		}
		else
		{
			$sql = "UPDATE projetos.contrato_formulario_pergunta SET ds_contrato_formulario_pergunta='{ds_contrato_formulario_pergunta}', nr_ordem={nr_ordem} WHERE cd_contrato_formulario_pergunta={cd_contrato_formulario_pergunta}";
		}
		
		esc( "{cd_contrato_formulario_pergunta}", intval($dados['cd_contrato_formulario_pergunta']), $sql );
		esc( "{ds_contrato_formulario_pergunta}", $dados['ds_contrato_formulario_pergunta'], $sql );
		esc( "{cd_contrato_formulario_grupo}", intval( $dados['cd_contrato_formulario_grupo'] ), $sql );
		esc( "{cd_usuario_inclusao}", usuario_id(), $sql );
		esc( "{nr_ordem}", intval( $dados['nr_ordem'] ), $sql );

		$query=$this->db->query($sql);
	}

	function excluir_grupo($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.contrato_formulario_grupo
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_contrato_formulario_grupo::varchar)='{cd_contrato_formulario_grupo}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_contrato_formulario_grupo}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		//echo $sql;
		// redirect( 'cadastro/contrato_formulario', 'refresh' );
	}

	function excluir_pergunta($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.contrato_formulario_pergunta
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_contrato_formulario_pergunta::varchar)='{cd_contrato_formulario_pergunta}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_contrato_formulario_pergunta}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		//echo $sql;
		// redirect( 'cadastro/contrato_formulario', 'refresh' );
	}
	
	function duplicar($id)
	{
		CheckLogin();

		$sql = "
		SELECT rotinas.contrato_formulario_duplica({cd_contrato_formulario})
		";
		esc("{cd_contrato_formulario}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		//echo $sql;
		redirect( 'cadastro/contrato_formulario', 'refresh' );

	}
}
