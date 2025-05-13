<?php
set_title('Formul�rios Prevenir');
$this->load->view('header');
?>
<script>


function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString',
		'CaseInsensitiveString'
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

function ir_lista()
{
	location.href="<?php echo site_url('ecrm/prevenir_formulario'); ?>";
}

function ir_relatorio(cd_pergunta)
{
	location.href="<?php echo site_url('ecrm/prevenir_formulario/relatorio'); ?>/"+cd_pergunta;
}

$(function(){
	configure_result_table();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'A��es Judiciais', (intval($cd_pergunta) == 1 ? true : false), 'ir_relatorio(1);');
$abas[] = array('aba_lista', 'Diminui��o da Rentabilidade do Patrim�nio', (intval($cd_pergunta) == 2 ? true : false), 'ir_relatorio(2);');
$abas[] = array('aba_lista', 'Baixa Ades�o de Participantes ...', (intval($cd_pergunta) == 3 ? true : false), 'ir_relatorio(3);');
$abas[] = array('aba_lista', 'Possib. da n�o Renova��o ...', (intval($cd_pergunta) == 4 ? true : false), 'ir_relatorio(4);');

$title = '';

switch ($cd_pergunta) 
{
    case 1:
        $title = 'A��es Judiciais';
        break;
    case 2:
        $title = 'Diminui��o da Rentabilidade do Patrim�nio';
        break;
    case 3:
        $title = 'Baixa Ades�o de Participantes em Alguns Planos';
        break;
	case 4:
        $title = 'Possibilidade da n�o Renova��o de Concess�o dos Servi�os P�blicos de Energia El�trica do Grupo CEEE';
        break;
}

$body = array();
$head = array( 
	'O Que?',
	'Por Qu�?',
	'Quem?',
	'Quando?',
	'Onde?',
	'Como?'
);

foreach( $collection as $item )
{
	$body[] = array(
		array($item["o_que"], 'text-align:justify;'),
		array($item["porque"], 'text-align:justify;'),
		array($item["quem"], 'text-align:justify;'),
		array($item["quando"], 'text-align:justify;'),
		array($item["onde"], 'text-align:justify;'),
		array($item["como"], 'text-align:justify;')
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo '<center>';
	echo img(base_url().'img/rel_prevenir_formulario.png');
	echo '<h1 style="font-size:18px;">Formul�rio 5W1H - Di�logo Institucional'.br().'Plano de a��o 5W1H</h1>';
	echo '</center>';
	
	echo form_start_box( "default_box", $title);
		switch ($cd_pergunta) 
		{
			case 1:
				echo form_default_row('', 'Amea�a/Problema : ', '<b>Elevada taxa de crescimento das a��es judiciais.</b>');
				echo form_default_row('', 'Principais causas : ', 'Pulveriza��o de escrit�rios demandantes.'.br().'Regras do Plano.');
				break;
			case 2:
				echo form_default_row('', 'Amea�a/Problema : ', '<b>Diminui��o da rentabilidade do patrim�nio.</b>');
				echo form_default_row('', 'Principais causas : ', 'Perfil de investimento com baixa toler�ncia a riscos de mercado e cr�dito e investimentos prioritariamente em produtos convencionais.'.br().'redu��o da taxa de juros real da economia brasileira.');
				break;
			case 3:
				echo form_default_row('', 'Amea�a/Problema : ', '<b>Baixa ades�o de participantes em alguns planos.</b>');
				echo form_default_row('', 'Principais causas : ', 'Dificuldade de aprova��o de novos planos nas patrocinadoras (AES, CGTEE).'.br().'dificuldade de canais de distribui��o de planos de instituidores.');
				break;
			case 4:
				echo form_default_row('', 'Amea�a/Problema : ', '<b>Possibilidade da n�o renova��o de concess�o dos servi�os p�blicos de energia el�trica do Grupo CEEE.</b>');
				break;
		}
	
	echo form_end_box("default_box");
	echo $grid->render();
	echo br(2);
echo aba_end(); 

$this->load->view('footer');
?>