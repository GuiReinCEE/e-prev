<?PHP
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	$sql = " 
	        SELECT nome_sem_acento,
                   cd_barra,
				   TRIM(SUBSTRING(UPPER(funcoes.remove_acento(TRIM(empresa))),0,55)) AS empresa				   
	          FROM acs.seminario 
	         WHERE cd_seminario_edicao = ".$_REQUEST['cd_seminario']."
			   AND dt_exclusao IS NULL
			 ORDER BY nome_sem_acento
		   ";
	$ob_resul = pg_query($db, $sql);
	

	
	require('inc/fpdf153/fpdf.php');
	
	
	class PDF extends FPDF
	{
		function EAN13($x,$y,$barcode,$h=11,$w=.35)
		{
			$this->Barcode($x,$y,$barcode,$h,$w,13);
		}

		function UPC_A($x,$y,$barcode,$h=16,$w=.35)
		{
			$this->Barcode($x,$y,$barcode,$h,$w,12);
		}

		function GetCheckDigit($barcode)
		{
			//Compute the check digit
			$sum=0;
			for($i=1;$i<=11;$i+=2)
				$sum+=3*$barcode{$i};
			for($i=0;$i<=10;$i+=2)
				$sum+=$barcode{$i};
			$r=$sum%10;
			if($r>0)
				$r=10-$r;
			return $r;
		}

		function TestCheckDigit($barcode)
		{
			//Test validity of check digit
			$sum=0;
			for($i=1;$i<=11;$i+=2)
				$sum+=3*$barcode{$i};
			for($i=0;$i<=10;$i+=2)
				$sum+=$barcode{$i};
			return ($sum+$barcode{12})%10==0;
		}

		function Barcode($x,$y,$barcode,$h,$w,$len)
		{
			//Padding
			$barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
			if($len==12)
				$barcode='0'.$barcode;
			//Add or control the check digit
			if(strlen($barcode)==12)
				$barcode.=$this->GetCheckDigit($barcode);
			elseif(!$this->TestCheckDigit($barcode))
				$this->Error('Incorrect check digit');
			//Convert digits to bars
			$codes=array(
				'A'=>array(
					'0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
					'5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
				'B'=>array(
					'0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
					'5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
				'C'=>array(
					'0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
					'5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
				);
			$parities=array(
				'0'=>array('A','A','A','A','A','A'),
				'1'=>array('A','A','B','A','B','B'),
				'2'=>array('A','A','B','B','A','B'),
				'3'=>array('A','A','B','B','B','A'),
				'4'=>array('A','B','A','A','B','B'),
				'5'=>array('A','B','B','A','A','B'),
				'6'=>array('A','B','B','B','A','A'),
				'7'=>array('A','B','A','B','A','B'),
				'8'=>array('A','B','A','B','B','A'),
				'9'=>array('A','B','B','A','B','A')
				);
			$code='101';
			$p=$parities[$barcode{0}];
			for($i=1;$i<=6;$i++)
				$code.=$codes[$p[$i-1]][$barcode{$i}];
			$code.='01010';
			for($i=7;$i<=12;$i++)
				$code.=$codes['C'][$barcode{$i}];
			$code.='101';
			//Draw bars
			for($i=0;$i<strlen($code);$i++)
			{
				if($code{$i}=='1')
					$this->Rect($x+$i*$w,$y,$w,$h,'F');
			}
			//Print text uder barcode
			$this->SetFont('Courier','',5);
			$this->Text($x,$y+$h+7/$this->k,substr($barcode,-$len));
		}
	}	
	
	
	
	$ob_pdf = new PDF('P','mm','Letter'); 
	$ob_pdf->SetMargins(5,14,5);
	$ob_pdf->AddPage();
	
	#$ob_pdf->SetTextColor(210, 210, 210);
	
	$nr_x = 0;
 	$nr_y = 0;
	$nr_conta = 0;
	$nr_conta_x = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$ar_nome = explode(" ",trim($ar_reg['nome_sem_acento']));
		$EMPRESA = $ar_reg['empresa'];
		
		if(count($ar_nome) > 1)
		{
			$NOME = $ar_nome[0]." ".$ar_nome[(count($ar_nome) - 1)];
			$NOME = substr($NOME,0,25);
		}
		else
		{
			$NOME = $ar_nome[0];
			$NOME = substr($NOME,0,25);
		}
		
		
		$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
		//$ob_pdf->SetTextColor(64,64,64);
		$ob_pdf->SetFont('Courier','B',12);
		$ob_pdf->Text($ob_pdf->GetX() + (33.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 5.7, $NOME);	
		$ob_pdf->SetFont('Courier','',5);
		$ob_pdf->Text($ob_pdf->GetX() + (33.5 - ($ob_pdf->GetStringWidth($EMPRESA)/2)),$ob_pdf->GetY() + 7.7, $EMPRESA);	
		$ob_pdf->EAN13($ob_pdf->GetX() + 17,$ob_pdf->GetY() + 9,$ar_reg['cd_barra']);
		
		
		//file('http://'.$_SERVER['SERVER_NAME'].'/controle_projetos/inc/cod_barra/html/image.php?code=ean13&o=1&t=30&r=2&text='.$ar_reg['cd_barra'].'&f=1&a1=&a2=&fl_write=1');
		//$ob_pdf->Image('../upload/cd_barra_'.$ar_reg['cd_barra'].'.png', $ob_pdf->GetX() + 7, $ob_pdf->GetY() + 7, ConvertSize(200,$ob_pdf->pgwidth), ConvertSize(38,$ob_pdf->pgwidth),'','',false);		
		
		$nr_conta++;
		$nr_conta_x++;
		
		if($nr_conta_x == 3)
		{
			$ob_pdf->SetX(5);
			$nr_x = 0;
			$nr_y = 25.5;
			$nr_conta_x = 0;
		}
		else
		{
			$nr_x = 68.5;
			$nr_y = 0;
		}

		if($nr_conta == 30)
		{
			$ob_pdf->AddPage();
			$ob_pdf->SetMargins(5,14,5);
			$nr_conta = 0;
			$nr_x = 0;
			$nr_y = 0;
		}
		
	}

	$ob_pdf->Output();

	
	function ConvertSize($size=5,$maxsize=0)
	{
	// Depends of maxsize value to make % work properly. Usually maxsize == pagewidth
	  //Identify size (remember: we are using 'mm' units here)
	  if ( stristr($size,'px') ) $size *= 0.2645; //pixels
	  elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
	  elseif ( stristr($size,'mm') ) $size += 0; //millimeters
	  elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
	  elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
	  elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
	  elseif ( stristr($size,'%') )
	  {
	  	$size += 0; //make "90%" become simply "90" 
	  	$size *= $maxsize/100;
	  }
	  else $size *= 0.2645; //nothing == px
	  
	  return $size;
	}	
?>



