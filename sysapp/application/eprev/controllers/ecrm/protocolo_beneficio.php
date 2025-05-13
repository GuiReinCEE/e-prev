<?php
class protocolo_beneficio extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		$data['usuarios_dd'] = $this->db->query( "SELECT a.codigo as value, a.nome as text FROM projetos.usuarios_controledi a JOIN projetos.protocolo_beneficio b ON a.codigo=b.cd_usuario_inclusao WHERE b.dt_exclusao IS NULL AND a.tipo <> 'X' ORDER BY a.nome" )->result_array();

        $this->load->view('ecrm/protocolo_beneficio/index.php', $data);
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Protocolo_beneficio_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["nr_protocolo"] = intval($this->input->post("nr_protocolo", TRUE));
		$args["nr_ano"] = intval($this->input->post("nr_ano", TRUE));
		$args["dt_inclusao_inicio"] = $this->input->post("dt_inclusao_inicio", TRUE);
		$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);

		$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = intval($this->input->post("cd_registro_empregado", TRUE));
		$args["seq_dependencia"] = $this->input->post("seq_dependencia", TRUE);

		if(trim($args["cd_empresa"])=="") $args["cd_empresa"]=-1;
		if(trim($args["seq_dependencia"])=="") $args["seq_dependencia"]=-1;
		$args["nome"] = $this->input->post("nome", TRUE);
		$args["cd_protocolo_beneficio_assunto"] = intval($this->input->post("cd_protocolo_beneficio_assunto", TRUE));
		$args["cd_protocolo_beneficio_forma_envio"] = intval($this->input->post("cd_protocolo_beneficio_forma_envio", TRUE));
		$args["cd_usuario_inclusao"] = intval($this->input->post("cd_usuario_inclusao", TRUE));

		// --------------------------
		// listar ...

        $this->Protocolo_beneficio_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/protocolo_beneficio/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		$sql = " select * from projetos.protocolo_beneficio ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_protocolo_beneficio={cd_protocolo_beneficio} ";
			esc( "{cd_protocolo_beneficio}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$data['uf_dd']=$this->db->query("SELECT a.cd_uf as value, a.cd_uf as text FROM geografico.uf a ORDER BY a.ds_uf")->result_array();
		$this->load->view('ecrm/protocolo_beneficio/detalhe', $data);
	}

	function listar_cidade_para_detalhe_ajax()
	{
		$uf = $this->input->post('cd_uf');
		$result=$this->db->query("
		SELECT a.cidade as value, a.cidade as text 
		FROM geografico.cidade a 
		WHERE a.uf=? 
		ORDER BY a.cidade
		", array($uf))->result_array();

		$options=array(""=>"::todas::");
		if($result)
		{
			foreach( $result as $item )
			{
				$options[$item['value']] = $item['text'];
			}
		}

		echo form_dropdown("cidade", $options);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_protocolo_beneficio', TRUE);

		$args["cd_protocolo_beneficio"] = $this->input->post("cd_protocolo_beneficio",TRUE);
		$args["nr_protocolo"] = $this->input->post("nr_protocolo",TRUE);
		$args["nr_ano"] = $this->input->post("nr_ano",TRUE);
		$args["cd_empresa"] = $this->input->post("cd_empresa",TRUE);
		$args["cd_registro_empregado"] = $this->input->post("cd_registro_empregado",TRUE);
		$args["seq_dependencia"] = $this->input->post("seq_dependencia",TRUE);
		$args["nome"] = $this->input->post("nome",TRUE);
		$args["observacao"] = $this->input->post("observacao",TRUE);
		$args["cep"] = $this->input->post("cep",TRUE);
		$args["uf"] = $this->input->post("uf",TRUE);
		$args["cidade"] = $this->input->post("cidade",TRUE);
		$args["endereco"] = $this->input->post("endereco",TRUE);
		$args["bairro"] = $this->input->post("bairro",TRUE);
		$args["cd_protocolo_beneficio_assunto"] = $this->input->post("cd_protocolo_beneficio_assunto",TRUE);
		$args["cd_protocolo_beneficio_forma_envio"] = $this->input->post("cd_protocolo_beneficio_forma_envio",TRUE);
		$args["cd_usuario_inclusao"] = usuario_id();

		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.protocolo_beneficio
			(
				cd_empresa
				, cd_registro_empregado
				, seq_dependencia
				, nome
				, observacao
				, cep
				, uf
				, cidade
				, endereco
				, bairro
				, cd_protocolo_beneficio_assunto
				, cd_protocolo_beneficio_forma_envio
				, dt_inclusao
				, cd_usuario_inclusao
			)
			VALUES
			(
				{cd_empresa}
				, {cd_registro_empregado}
				, {seq_dependencia}
				, '{nome}'
				, ".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'")."
				, '{cep}'
				, '{uf}'
				, '{cidade}'
				, '{endereco}'
				, '{bairro}'
				, {cd_protocolo_beneficio_assunto}
				, {cd_protocolo_beneficio_forma_envio}
				, CURRENT_TIMESTAMP
				, {cd_usuario_inclusao}
			)
			";
		}
		else
		{
			$sql="
			UPDATE

				projetos.protocolo_beneficio

			SET

				cd_empresa = {cd_empresa}
				, cd_registro_empregado = {cd_registro_empregado}
				, seq_dependencia = {seq_dependencia}
				, nome = '{nome}'
				, observacao = ".(trim($args["observacao"]) == "" ? "NULL" :"'{observacao}'")."
				, cep = '{cep}'
				, uf = '{uf}'
				, cidade = '{cidade}'
				, endereco = '{endereco}'
				, bairro = '{bairro}'
				, cd_protocolo_beneficio_assunto = {cd_protocolo_beneficio_assunto}
				, cd_protocolo_beneficio_forma_envio = {cd_protocolo_beneficio_forma_envio}

			WHERE 

				cd_protocolo_beneficio = {cd_protocolo_beneficio}
			";
		}

		// Campos opcionais
		if( trim($args['cd_empresa'])=='' ) { $args['cd_empresa']='DEFAULT'; }
		if( trim($args['cd_registro_empregado'])=='' ) { $args['cd_registro_empregado']='DEFAULT'; }
		if( trim($args['seq_dependencia'])=='' ) { $args['seq_dependencia']='DEFAULT'; }
		if( intval($args['cd_protocolo_beneficio_assunto'])==0 ) { $args['cd_protocolo_beneficio_assunto']='DEFAULT'; }
		if( intval($args['cd_protocolo_beneficio_forma_envio'])==0 ) { $args['cd_protocolo_beneficio_forma_envio']='DEFAULT'; }

		esc("{nr_protocolo}", $args["nr_protocolo"], $sql, "int", FALSE);
		esc("{nr_ano}", $args["nr_ano"], $sql, "int", FALSE);
		esc("{cd_empresa}", $args["cd_empresa"], $sql, "str", FALSE);
		esc("{cd_registro_empregado}", $args["cd_registro_empregado"], $sql, "str", FALSE);
		esc("{seq_dependencia}", $args["seq_dependencia"], $sql, "str", FALSE);
		esc("{nome}", $args["nome"], $sql, "str", FALSE);
		esc("{observacao}", $args["observacao"], $sql, "str", FALSE);
		esc("{cep}", $args["cep"], $sql, "str", FALSE);
		esc("{uf}", $args["uf"], $sql, "str", FALSE);
		esc("{cidade}", $args["cidade"], $sql, "str", FALSE);
		esc("{endereco}", $args["endereco"], $sql, "str", FALSE);
		esc("{bairro}", $args["bairro"], $sql, "str", FALSE);
		esc("{cd_protocolo_beneficio_assunto}", $args["cd_protocolo_beneficio_assunto"], $sql, "str", FALSE);
		esc("{cd_protocolo_beneficio_forma_envio}", $args["cd_protocolo_beneficio_forma_envio"], $sql, "str", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_protocolo_beneficio}", $args["cd_protocolo_beneficio"], $sql, "int", FALSE);

		$query = $this->db->query($sql);

		if( intval($args['cd_protocolo_beneficio'])==0 )
		{
			$new_id = $this->db->insert_id("projetos.protocolo_beneficio", "cd_protocolo_beneficio");
		}
		else
		{
			$new_id = intval($args['cd_protocolo_beneficio']);
		}

		redirect( "ecrm/protocolo_beneficio/detalhe/".$new_id, "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE projetos.protocolo_beneficio
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(cd_protocolo_beneficio::varchar)='{cd_protocolo_beneficio}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_protocolo_beneficio}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'ecrm/protocolo_beneficio', 'refresh' );
	}

	function mala_direta_salvar()
	{
		CheckLogin();
        $this->load->model('projetos/Protocolo_beneficio_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		$args["nr_protocolo"] = intval($this->input->post("nr_protocolo", TRUE));
		$args["nr_ano"] = intval($this->input->post("nr_ano", TRUE));
		$args["dt_inclusao_inicio"] = $this->input->post("dt_inclusao_inicio", TRUE);
		$args["dt_inclusao_fim"] = $this->input->post("dt_inclusao_fim", TRUE);

		$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
		$args["cd_registro_empregado"] = intval($this->input->post("cd_registro_empregado", TRUE));
		$args["seq_dependencia"] = $this->input->post("seq_dependencia", TRUE);

		if(trim($args["cd_empresa"])=="") $args["cd_empresa"]=-1;
		if(trim($args["seq_dependencia"])=="") $args["seq_dependencia"]=-1;
		$args["nome"] = $this->input->post("nome", TRUE);
		$args["cd_protocolo_beneficio_assunto"] = intval($this->input->post("cd_protocolo_beneficio_assunto", TRUE));
		$args["cd_protocolo_beneficio_forma_envio"] = intval($this->input->post("cd_protocolo_beneficio_forma_envio", TRUE));
		$args["cd_usuario_inclusao"] = intval($this->input->post("cd_usuario_inclusao", TRUE));

		// --------------------------
		// listar ...

        $this->Protocolo_beneficio_model->listar( $result, $args );

        if( $result )
        {
            $collection = $result->result_array();

			$this->db->trans_start();
			foreach( $collection as $item )
			{
				$sql = "
						INSERT INTO projetos.mala_direta_empresas_integracao(
								usuario, nome, logradouro, 
								bairro, cep, complemento_cep, cidade, unidade_federativa)
						VALUES ('{usuario}', '{nome}', '{logradouro}', 
								'{bairro}', {cep}, {complemento_cep}, '{cidade}', '{unidade_federativa}'
						);
				";

				$a_cep = explode( "-", $item['cep'] );
				esc( "{usuario}", $this->session->userdata('usuario'), $sql, 'str', FALSE );
				esc( "{nome}", $item['nome'], $sql, 'str', FALSE );
				esc( "{cep}", $a_cep[0], $sql, 'int' );
				esc( "{complemento_cep}", $a_cep[1], $sql, 'int' );
				esc( "{logradouro}", $item['endereco'], $sql, 'str', FALSE );
				esc( "{bairro}", $item['bairro'], $sql, 'str', FALSE );
				esc( "{cidade}", $item['cidade'], $sql, 'str', FALSE );
				esc( "{unidade_federativa}", $item['uf'], $sql, 'str', FALSE );

				$this->db->query($sql);
			}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE)
			{
				echo 'false';
			}
			else
			{
				echo 'true';
			}
        }
		else
		{
			echo 'false';
		}
	}
}
