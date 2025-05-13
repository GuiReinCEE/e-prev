<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

/**
 * log_save()
 *
 * Criar um log no sistema ePrev
 *
 * @access	public
 * @param	string	tipo de log
 * @param	string	mensagem a ser gravada
 */	
if ( ! function_exists('log_save'))
{
	function log_save($tipo, $mensagem)
	{
		$CI =& get_instance();

		// $sql_log = "INSERT INTO projetos.log (tipo, local, descricao, dt_cadastro) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";

		$CI->load->model("projetos/Log_model");
		return $CI->Log_model->insert( $tipo, $mensagem );
	}
}

function log_acesso_salvar()
{
	$d=array('','');
	if(isset($_SERVER['REQUEST_URI'])){$d[0]=$_SERVER['REQUEST_URI'];}
	if(isset($_SERVER['REMOTE_ADDR'])){$d[1]=$_SERVER['REMOTE_ADDR'];}
	$ci=&get_instance();
	// Gravar Log de Acesso
	$sql="INSERT INTO projetos.log_acessos (cd_usuario,prog,dt_acesso,ip) values ({cd_usuario},'{prog}',current_timestamp,'{ip}')";
	esc('{cd_usuario}',intval(usuario_id()),$sql);
	esc('{prog}',$d[0],$sql);
	esc('{ip}',$d[1],$sql);
	$ci->db->query($sql);
}
