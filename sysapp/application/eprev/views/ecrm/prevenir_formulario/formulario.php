<?php
set_title('Formulários Prevenir');
$this->load->view('header');
?>
<script>
function ir_lista()
{
	location.href="<?php echo site_url('ecrm/prevenir_formulario'); ?>";
}	

function ir_relatorio()
{
	location.href="<?php echo site_url('ecrm/prevenir_formulario/relatorio'); ?>";
}

function muda_exibicao(cd_prevenir_formulario_item, fl_exibir)
{
	$.post('<?php echo site_url('ecrm/prevenir_formulario/muda_exibicao');?>',
	{
		cd_prevenir_formulario_item : cd_prevenir_formulario_item,
		fl_exibir                   : fl_exibir
	},
	function(data)
	{
	});
}


function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		null
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
	
	var ob_resul = new SortableTable(document.getElementById("table-2"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		null
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
	
	var ob_resul = new SortableTable(document.getElementById("table-3"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		null
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
	
	var ob_resul = new SortableTable(document.getElementById("table-4"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		null
	]);
	ob_resul.onsort = function ()
	{
		var rows = ob_resul.tBody.rows;
		var l = rows.length;
		for (var i = 0; i < l; i++)
		{
			removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
			addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
		}
	};
	ob_resul.sort(0, true);
	
	
}

$(function(){
	configure_result_table();
});

</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Relatório', FALSE, 'ir_relatorio();');
$abas[] = array('aba_nc', 'Formulário', TRUE, 'location.reload();');


$head = array( 
	'O Que?',
	'Por Quê?',
	'Quem?',
	'Quando?',
	'Onde?',
	'Como?',
	'Exibir'
);

$body[1] = array();
$body[2] = array();
$body[3] = array();
$body[4] = array();

foreach( $collection as $item )
{
	$body[$item['cd_pergunta']][] = array(
		array($item["o_que"], 'text-align:justify;'),
		array($item["porque"], 'text-align:justify;'),
		array($item["quem"], 'text-align:justify;'),
		array($item["quando"], 'text-align:justify;'),
		array($item["onde"], 'text-align:justify;'),
		array($item["como"], 'text-align:justify;'),
		($fl_editar ? form_dropdown('fl_exibir', array('S'=> 'Sim', 'N' => 'Não'), array($item['fl_exibir']), 'onchange="muda_exibicao('.intval($item['cd_prevenir_formulario_item']).',$(this).val());"') : '')
	);
}
$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;


echo aba_start( $abas );
	echo '<center>';
	echo img(base_url().'img/previnir_formulario.png');
	echo '</center>';
	echo form_start_box("default_box", "Formulário");
		echo form_default_row('', 'Nome da Instituição : ', ($fl_editar ? $row['ds_instituicao'] : md5($row['ds_instituicao'])));
		echo form_default_row('', 'Nome : ', ($fl_editar ? $row['ds_nome'] : md5($row['ds_nome'])));
		echo form_default_row('', 'Telefone : ', ($fl_editar ? $row['nr_telefone'] : md5($row['nr_telefone'])));
		echo form_default_row('', 'E-mail : ', ($fl_editar ? $row['ds_email'] : md5($row['ds_email'])));
		echo form_default_row('', 'Dt de Entrega : ', $row['dt_envio']);
	echo form_end_box("default_box");
	
	echo form_start_box("default_acoes_box", "Ações Judiciais");
		echo form_default_row('', 'Ameaça/Problema : ', '<b>Elevada taxa de crescimento das ações judiciais.</b>');
		echo form_default_row('', 'Principais causas : ', 'Pulverização de escritórios demandantes.'.br().'Regras do Plano.');
	echo form_end_box("default_acoes_box");
	$grid->id_tabela = 'table-1';
	$grid->body      = $body[1];
	echo $grid->render();
	
	echo form_start_box("default_rent_box", "Diminuição da Rentabilidade do Patrimônio");
		echo form_default_row('', 'Ameaça/Problema : ', '<b>Diminuição da rentabilidade do patrimônio.</b>');
		echo form_default_row('', 'Principais causas : ', 'Perfil de investimento com baixa tolerância a riscos de mercado e crédito e investimentos prioritariamente em produtos convencionais.'.br().'redução da taxa de juros real da economia brasileira.');
	echo form_end_box("default_rent_box");
	$grid->id_tabela = 'table-2';
	$grid->body      = $body[2];
	echo $grid->render();
	
	echo form_start_box("default_adesao_box", "Baixa Adesão de Participantes em Alguns Planos");
		echo form_default_row('', 'Ameaça/Problema : ', '<b>Baixa adesão de participantes em alguns planos.</b>');
		echo form_default_row('', 'Principais causas : ', 'Dificuldade de aprovação de novos planos nas patrocinadoras (AES, CGTEE).'.br().'dificuldade de canais de distribuição de planos de instituidores.');
	echo form_end_box("default_adesao_box");
	$grid->id_tabela = 'table-3';
	$grid->body      = $body[3];
	echo $grid->render();
	
	echo form_start_box("default_renovacao_box", "Possibilidade da não Renovação de Concessão dos Serviços Públicos de Energia Elétrica do Grupo CEEE");
		echo form_default_row('', 'Ameaça/Problema : ', '<b>Possibilidade da não renovação de concessão dos serviços públicos de energia elétrica do Grupo CEEE.</b>');
	echo form_end_box("default_renovacao_box");
	$grid->id_tabela = 'table-4';
	$grid->body      = $body[4];
	echo $grid->render();
	
	echo br(2);
echo aba_end(); 

$this->load->view('footer');
?>