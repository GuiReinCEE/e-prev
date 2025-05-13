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
			redirect('/login/index/'.base64_encode($return_page) , 'refresh');
		}
		else
		{
			$arr     = explode('/', $CI->uri->uri_string());
			$ds_menu = trim((isset($arr[1]) ? $arr[1] : '').(isset($arr[2]) ? '/'.$arr[2] : ''));

			$nao_registrar_menu = array(
				'geral/upload',
				'geral/upload_multiplo'
			);

			$nao_registrar_uri = array(
				'/atividade/minhas/notificacao',
				'/atividade/pendencia_minha/checar',
				'/home'
			);

			if((!in_array($ds_menu, $nao_registrar_menu)) AND (!in_array($CI->uri->uri_string(), $nao_registrar_uri)) AND (trim($ds_menu) != ''))
			{
				$qr_sql = "
					INSERT INTO projetos.log_acesso_menu
					     (
					     	cd_usuario,
					     	ds_menu,
					     	ds_uri
					     )
					VALUES
					     (
					        ".intval( $CI->session->userdata('codigo')).",
					        '".trim($ds_menu)."',
					        '".trim($CI->uri->uri_string())."'
					     )";

				 $CI->db->query($qr_sql);
			 }
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
			if($gerencia == $ci->session->userdata('divisao'))
			{
				return true;
			}
			elseif($gerencia == $ci->session->userdata('divisao_ant')) #Devido a reestruturação de 04/2014
			{
				return true;
			}
			elseif($gerencia == $ci->session->userdata('cd_gerencia_unidade')) #Unidade
			{
				return true;
			}				
			else
			{
				switch ($gerencia) 
				{
					case 'GTI':
						if($ci->session->userdata('divisao') == 'GS')
				        {
				        	return true;
				        }
					break;
					
					case 'GN':
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }
					break;
					
					case 'GRSC':
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }
					break;
					
				    case 'AC':
				        if($ci->session->userdata('divisao') == 'GCM')
				        {
				        	return true;
				        }

				        if($ci->session->userdata('divisao') == 'GN')
				        {
				        	return true;
				        }
						
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }
				        break;
				    case 'AJ':
				        if($ci->session->userdata('divisao') == 'GJ')
				        {
				        	return true;
				        }
				        break;
				    case 'GE':
				        if($ci->session->userdata('divisao') == 'GCM')
				        {
				        	return true;
				        }

				        if($ci->session->userdata('divisao') == 'GRSC')
				        {
				        	return true;
				        }
						
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }
				        break;
				    case 'GGS':
				        if(($ci->session->userdata('divisao_ant') == 'GI') AND ($ci->session->userdata('divisao') == 'GTI'))
				        {
				        	return true;
				        }
				        else if(($ci->session->userdata('divisao_ant') == 'GAD') AND ($ci->session->userdata('divisao') == 'GGPA'))
				        {
				        	return true;
				        }
				        break;
				    case 'GP':
				    	if($ci->session->userdata('divisao') == 'GJ')
				        {
				        	return true;
				        }

				        if($ci->session->userdata('divisao') == 'GRSC')
				        {
				        	return true;
				        }
						
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }

				        if(($ci->session->userdata('divisao_ant') == 'GAP') AND ($ci->session->userdata('divisao') == 'GCM'))
				        {
				        	return true;
				        }

				        if(($ci->session->userdata('divisao_ant') == 'GAP') AND ($ci->session->userdata('divisao') == 'GCM'))
				        {
				        	return true;
				        }

				        if(($ci->session->userdata('divisao_ant') == 'GB') AND ($ci->session->userdata('divisao') == 'GAP.'))
				        {
				        	return true;
				        }

				        if(($ci->session->userdata('divisao_ant') == 'GA') OR ($ci->session->userdata('divisao') == 'GAP.'))
				        {
				        	return true;
				        }
				        break;
				    case 'SG':
				        if($ci->session->userdata('divisao') == 'GRC')
				        {
				        	return true;
				        }
				        break;  
				    case 'GCM':
				        if($ci->session->userdata('divisao') == 'GN')
				        {
				        	return true;
				        }

				        if($ci->session->userdata('divisao') == 'GRSC')
				        {
				        	return true;
				        }
						
						if($ci->session->userdata('divisao') == 'GNR')
				        {
				        	return true;
				        }
				        break; 
				}
			}
		}

		// TODO: COMENTAR A LINHA ABAIXO EM PRODUÇÃO!
		if( $ci->session->userdata('codigo')==170 or $ci->session->userdata('codigo')==251 or $ci->session->userdata('codigo')==516){return true;} //LIBERA PARA CRISTIANO JACOBSEN

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