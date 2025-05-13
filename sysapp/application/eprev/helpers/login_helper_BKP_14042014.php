<?php
if ( ! function_exists('CheckLogin'))
{
	function CheckLogin()
	{
		$ret=false;
		$CI =& get_instance();

		log_acesso_salvar();

		// Acesso a página interna do CI ePrev
		$return_page = $_SERVER['PHP_SELF'];

		$return_page = str_replace("/cieprev/index.php/", "", $return_page);
		$CI->session->set_userdata( array('return_page'=>$return_page) );

		if( $CI->session->userdata('logged_in') && ( isset($_SESSION['Z']) && intval($_SESSION['Z'])>0 ) )
		{
			//echo "1.";
			$ret = true;
		}
		else
		{
			//echo "2.";
			$b = $CI->simplelogin->login('', '', $_SERVER['REMOTE_ADDR']);

			if( $b==true )
			{
				//echo "3.";
				$ret=true;
			}
			else
			{
				//echo "4.";
				$ret=false;
			}
		}

		// SE NÃO ESTÁ LOGADO NO EPREV NOVO
		if( $ret==false )
		{
			// VERIFICA SE ESTÁ LOGADO PELO EPREV ANTIGO
			session_start();
			if( isset($_SESSION['Z']) && intval($_SESSION['Z'])>0 )
			{
				$b = $CI->simplelogin->login('', '', '', md5( trim($_SESSION['Z']) . trim($_SESSION['U']) ) );

				if($b)
				{
					if( $CI->session->userdata('logged_in') )
					{
						$ret = true;
					}
					else
					{
						$ret=false;
					}
				}
				else
				{
					$ret=false;
				}
			}
		}

		// SE REALMENTE NÃO ESTÁ LOGADO NO EPREV NOVO REDIRECIONA PARA LOGIN
		if($ret==false)
		{
			redirect('/login/' , 'refresh');
		}

		return $ret;
	}
}

if( ! function_exists('usuario_id') )
{
	function usuario_id()
	{
		$ci = &get_instance();
		return intval( $ci->session->userdata('codigo') );
	}
}

if( ! function_exists('gerencia_in') )
{
	/**
	 * Verificar se a gerência do usuário logado está inserida no grupo de gerências passado por parametro
	 *
	 * @param array $gerencias	grupo de gerencias para comparação com gerencia do usuário logado
	 * @return boolean
	 */
	function gerencia_in( $gerencias )
	{
		$ci = &get_instance();
		foreach( $gerencias as $gerencia )
		{
			if($gerencia==$ci->session->userdata('divisao'))
			{
				return true;
			}
		}

		// TODO: COMENTAR A LINHA ABAIXO EM PRODUÇÃO!
		if( $ci->session->userdata('codigo')==170 or $ci->session->userdata('codigo')==251 ){return true;} //LIBERA PARA CRISTIANO JACOBSEN

		return false;
	}
}

if( ! function_exists('tipo_usuario_in') )
{
	/**
	 * Verificar se a gerência do usuário logado está inserida no grupo de gerências passado por parametro
	 *
	 * @param array $gerencias	grupo de gerencias para comparação com gerencia do usuário logado
	 * @return boolean
	 */
	function tipo_usuario_in( $tipos )
	{
		$ci = &get_instance();
		foreach( $tipos as $tipo )
		{
			if($tipo==$ci->session->userdata('tipo'))
			{
				return true;
			}
		}

		// TODO: COMENTAR A LINHA ABAIXO EM PRODUÇÃO!
		//if( $ci->session->userdata('divisao')=='GI' ){return true;}
		if( $ci->session->userdata('codigo')==170 ){return true;} //LIBERA PARA CRISTIANO JACOBSEN

		return false;
	}
}

function usuario_administrador_indicador($cd)
{
	$ci=&get_instance();
	$sql="SELECT COUNT(*) AS quantos FROM indicador.indicador_administrador WHERE cd_usuario={cd_usuario} AND dt_exclusao IS NULL AND ds_tipo='ADMINISTRADOR'";
	esc( "{cd_usuario}", intval($cd), $sql );
	$q=$ci->db->query($sql);
	$r=$q->row_array();

	return ( intval($r['quantos'])>0 );
}

function usuario_responsavel_indicador($cd)
{
	$ci=&get_instance();
	$sql="SELECT COUNT(*) AS quantos FROM indicador.indicador_administrador WHERE cd_usuario={cd_usuario} AND dt_exclusao IS NULL AND ds_tipo='RESPONSAVEL'";
	esc( "{cd_usuario}", intval($cd), $sql );
	$q=$ci->db->query($sql);
	$r=$q->row_array();

	return ( intval($r['quantos'])>0 );
}

function usuario_administrador_avaliacao()
{
	return ( usuario_id()==191 || usuario_id()==47 );
}