<?php
require('fpdf/fpdf.php');
class PDF extends FPDF
{
    var $widths;
    var $aligns;
    var $nr_pag    = false;
    var $nr_pag_de = false;
    var $B=0;
    var $I=0;
    var $U=0;
    var $HREF="";
    var $borda_exibe  = false;
    var $header_exibe  = false;
    var $header_logo   = false;
    var $header_logo_iso   = false;
    var $header_titulo = false;
    var $header_titulo_texto = "Título";
    var $header_subtitulo = false;
    var $header_subtitulo_texto = "Subtítulo";
    var $header_titulo_font = 0;
    
    var $AR_THEMA = Array();
    var $AR_TABELA_COR = Array();
    
    
    ##### WRITETAG ####
    var $wLine; // Maximum width of the line
    var $hLine; // Height of the line
    var $Text; // Text to display
    var $border;
    var $align; // Justification of the text
    var $fill;
    var $Padding;
    var $lPadding;
    var $tPadding;
    var $bPadding;
    var $rPadding;
    var $TagStyle; // Style for each tag
    var $Indent;
    var $Space; // Minimum space between words
    var $PileStyle; 
    var $Line2Print; // Line to display
    var $NextLineBegin; // Buffer between lines 
    var $TagName;
    var $Delta; // Maximum width minus width
    var $StringLength; 
    var $LineLength;
    var $wTextLine; // Width minus paddings
    var $nbSpace; // Number of spaces in the line
    var $Xini; // Initial position
    var $href; // Current URL
    var $TagHref; // URL for a cell
    ##### WRITETAG ####

    function Header()
    {
        $this->AddFont('segoeuil');
        $this->AddFont('segoeuib');
        $this->AliasNbPages('{qt_pagina}');
        
        if($this->borda_exibe)
        {
            $this->Borda();
        }
        
        if($this->header_exibe)
        {
            if($this->header_logo)
            {
                $this->Image('./img/logofundacao_carta.jpg', 10, 10, $this->ConvertSize(150), $this->ConvertSize(33),'','',false);
            }
			
            if($this->header_logo_iso)
            {
                $this->Image('./img/logo_iso_p_ffp_preto_branco.jpg', 187, 10, $this->ConvertSize(44), $this->ConvertSize(31),'','',false);
            }			
            
            if($this->header_titulo)
            {           
                switch ($this->header_titulo_font) 
                {
                    case 0:
                        $this->SetFont('segoeuib','',13);
                        break;
                    case 1:
                        $this->SetFont('times','B', 16);
                        break;
                }
                
                $this->SetTextColor(0,0,0);
                //Move to the right
                $this->SetXY(50,($this->GetY() - 4.5));
                //Title
                $this->MultiCell(($this->CurOrientation == "L" ? 200 : 130),6,$this->header_titulo_texto,0,"C");
            }
            
            if($this->header_subtitulo)
            {           
                $this->SetFont('segoeuil','',10);
                $this->SetTextColor(0,0,0);
                $this->SetXY(50,$this->GetY()+1);
                //Subtitle
                $this->MultiCell(($this->CurOrientation == "L" ? 200 : 130),6,$this->header_subtitulo_texto,0,"C");
            }
            
            $this->Ln(8);
        }
    }
    
    function Borda()
    {
        if($this->CurOrientation == "P")
        {
            #### PAISAGEM ####
            $ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            $this->RoundedRect(7, 7, 196, 278, 3.50, '1111', 'DF', $ar_estilo, array(255, 255, 255));   
        }
        
        if($this->CurOrientation == "L")
        {
            #### RETRATO ####
            $ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            $this->RoundedRect(7, 7, 283, 191, 3.50, '1111', 'DF', $ar_estilo, array(255, 255, 255));   
        }       
    }

    function SetNrPag($s)
    {
        $this->nr_pag=$s;
    }

    function SetNrPagDe($s)
    {
        $this->nr_pag_de=$s;
    }   
    
    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function RowCollor($data, $color_back, $color_text, $altura_linha = 0, $key_seleciona = -1)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
		
		$h = ($altura_linha > 0 ? $altura_linha : $h);
		
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            #$this->Rect($x,$y,$w,$h,'DF',null,$color_back[$i]);
            
			if($key_seleciona == $i)
            {
                $seleciona = array('width' => 2, 'cap' => 'square', 'join' => 'round', 'dash' => '2,10', 'color' => array(18, 140, 55));
                $this->Rect($x,$y,$w,$h,'DF', array('all' => $seleciona), $color_back[$i]);

                $normal = array('width' => 0, 'cap' => 'round', 'join' => 'round', 'dash' => '0', 'color' => array(0, 0, 0));
                $this->SetLineStyle($normal);
            }
            else
            {
                $this->Rect($x,$y,$w,$h,'DF', null, $color_back[$i]);
            }
            
			//Print the text
			$this->SetTextColor($color_text[$i][0], $color_text[$i][1], $color_text[$i][2]);
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowTag($data, $head = array(), $head_align = array())
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=(5*$nb) +5;
        //Issue a page break first if needed
        $this->CheckPageBreakRowTag($h, $head, $head_align);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            //$this->MultiCell($w,5,$data[$i],0,$a);

            $t = explode('<br />', $data[$i]);
            
            if(count($t) <= 1)
            {
                $this->WriteTag($w, 5,$data[$i], 0, $a, false, '3,0,0,3');
            }
            else
            {
                foreach ($t as $k => $item) 
                {
                    $item = str_replace(array('<p>', '</p>'), '', $item);
  
                    $this->SetX($x);
                    $this->WriteTag($w, 5,'<p>'.$item.'</p>', 0, $a, false, '3,0,0,3');
                }
            }

            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreakRowTag($h, $head = array(), $head_align = array())
    {
        $x = $this->GetX();
        $line_width = $this->GetLineWidth();
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
        {
            $this->AddPage($this->CurOrientation);

            if(count($head) > 0)
            {
                $aligns = $this->aligns;

                if(count($head_align) > 0)
                {
                    $this->SetAligns($head_align);
                }
                
                $this->RowTag($head);

                $this->SetAligns($aligns);
            }
        }
        $this->SetX($x);
        $this->SetLineWidth($line_width);
    }

    function CheckPageBreak($h)
    {
        $x = $this->GetX();
        $line_width = $this->GetLineWidth();
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
        {
            $this->AddPage($this->CurOrientation);
        }
        $this->SetX($x);
        $this->SetLineWidth($line_width);
    }
    
    function GetLineWidth() 
    {
        return $this->LineWidth;
    }   
    
    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }       
    
    /**
     * Sets line style
     *
     * @param string $style Line style. Array with keys among the following:
     *         . width: Width of the line in user units
     *         . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
     *         . join: miter, round or bevel
     *         . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
     *                 For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
     *                              (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
     *         . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
     *         . color: Draw color. Array with components (red, green, blue)
     */
    function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                if(preg_match('/^.+,/', $dash))
                    $tab = explode(',', $dash);
                else
                    $tab = array($dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2f', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }

    /**
     * Draws a line
     *
     * @param unknown_type $x1 Start point
     * @param unknown_type $y1 Start point
     * @param unknown_type $x2 End point
     * @param unknown_type $y2 End point
     * @param unknown_type $style Line style. Array like for SetLineStyle
     */
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    /**
     * Draws a rectangle
     *
     * @param unknown_type $x Top left corner
     * @param unknown_type $y Top left corner
     * @param unknown_type $w Width and height
     * @param unknown_type $h Width and height
     * @param unknown_type $style Style of rectangle (draw and/or fill: D, F, DF, FD)
     * @param unknown_type $border_style Border style of rectangle. Array with some of this index
     *    . all: Line style of all borders. Array like for SetLineStyle
     *    . L: Line style of left border. null (no border) or array like for SetLineStyle
     *    . T: Line style of top border. null (no border) or array like for SetLineStyle
     *    . R: Line style of right border. null (no border) or array like for SetLineStyl
     *    . B: Line style of bottom border. null (no border) or array like for SetLineStylee
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     */
    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $border_style = null;
                parent::Rect($x, $y, $w, $h, $style);
                break;
            case 'DF': case 'FD':
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all'])) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                } else
                    $style = 'F';
                parent::Rect($x, $y, $w, $h, $style);
                break;
            default:
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all']) && $border_style['all']) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                    parent::Rect($x, $y, $w, $h, $style);
                }
                break;
        }
        if ($border_style) {
            if (isset($border_style['L']) && $border_style['L'])
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            if (isset($border_style['T']) && $border_style['T'])
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            if (isset($border_style['R']) && $border_style['R'])
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            if (isset($border_style['B']) && $border_style['B'])
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
        }
    }

    /**
     * Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
     *
     * @param unknown_type $x0 Start point
     * @param unknown_type $y0 Start point
     * @param unknown_type $x1 Control point 1
     * @param unknown_type $y1 Control point 1
     * @param unknown_type $x2 Control point 2
     * @param unknown_type $y2 Control point 2
     * @param unknown_type $x3 End point
     * @param unknown_type $y3 End point
     * @param unknown_type $style Style of rectangule (draw and/or fill: D, F, DF, FD)
     * @param unknown_type $line_style Line style for curve. Array like for SetLineStyle
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     */
    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $op = 'f';
                $line_style = null;
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        if ($line_style)
            $this->SetLineStyle($line_style);

        $this->_Point($x0, $y0);
        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
        $this->_out($op);
    }

    /**
     * Draws an ellipse
     *
     * @param unknown_type $x0 Center point
     * @param unknown_type $y0 Center point
     * @param unknown_type $rx Horizontal and vertical radius (if ry = 0, draws a circle)
     * @param unknown_type $ry Horizontal and vertical radius (if ry = 0, draws a circle)
     * @param unknown_type $angle Orientation angle (anti-clockwise)
     * @param unknown_type $astart Start angle
     * @param unknown_type $afinish Finish angle
     * @param unknown_type $style Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
     * @param unknown_type $line_style Line style for ellipse. Array like for SetLineStyle
     * @param unknown_type $fill_color Fill color. Array with components (red, green, blue)
     * @param unknown_type $nSeg Ellipse is made up of nSeg Bézier curves
     */
    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        if ($rx) {
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($r, $g, $b) = $fill_color;
                $this->SetFillColor($r, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $op = 'f';
                    $line_style = null;
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                case 'C':
                    $op = 's'; // small 's' means closing the path as well
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($line_style)
                $this->SetLineStyle($line_style);
            if (!$ry)
                $ry = $rx;
            $rx *= $this->k;
            $ry *= $this->k;
            if ($nSeg < 2)
                $nSeg = 2;

            $astart = deg2rad((float) $astart);
            $afinish = deg2rad((float) $afinish);
            $totalAngle = $afinish - $astart;

            $dt = $totalAngle/$nSeg;
            $dtm = $dt/3;

            $x0 *= $this->k;
            $y0 = ($this->h - $y0) * $this->k;
            if ($angle != 0) {
                $a = -deg2rad((float) $angle);
                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
                $x0 = 0;
                $y0 = 0;
            }

            $t1 = $astart;
            $a0 = $x0 + ($rx * cos($t1));
            $b0 = $y0 + ($ry * sin($t1));
            $c0 = -$rx * sin($t1);
            $d0 = $ry * cos($t1);
            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
            for ($i = 1; $i <= $nSeg; $i++) {
                // Draw this bit of the total curve
                $t1 = ($i * $dt) + $astart;
                $a1 = $x0 + ($rx * cos($t1));
                $b1 = $y0 + ($ry * sin($t1));
                $c1 = -$rx * sin($t1);
                $d1 = $ry * cos($t1);
                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
                            ($a1 - ($c1 * $dtm)) / $this->k,
                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
                            $a1 / $this->k,
                            $this->h - ($b1 / $this->k));
                $a0 = $a1;
                $b0 = $b1;
                $c0 = $c1;
                $d0 = $d1;
            }
            $this->_out($op);
            if ($angle !=0)
                $this->_out('Q');
        }
    }

    // Draws a circle
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
    // - line_style: Line style for circle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
    }

    // Draws a polygon
    // Parameters:
    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
        $np = count($p) / 2;
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $line_style = null;
                $op = 'f';
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        $draw = true;
        if ($line_style)
            if (isset($line_style['all']))
                $this->SetLineStyle($line_style['all']);
            else { // 0 .. (np - 1), op = {B, S}
                $draw = false;
                if ('B' == $op) {
                    $op = 'f';
                    $this->_Point($p[0], $p[1]);
                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
                        $this->_Line($p[$i], $p[$i + 1]);
                    $this->_Line($p[0], $p[1]);
                    $this->_out($op);
                }
                $p[$np * 2] = $p[0];
                $p[($np * 2) + 1] = $p[1];
                for ($i = 0; $i < $np; $i++)
                    if (!empty($line_style[$i]))
                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
            }

        if ($draw) {
            $this->_Point($p[0], $p[1]);
            for ($i = 2; $i < ($np * 2); $i = $i + 2)
                $this->_Line($p[$i], $p[$i + 1]);
            $this->_Line($p[0], $p[1]);
            $this->_out($op);
        }
    }

    // Draws a regular polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - ns: Number of sides
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($ns < 3)
            $ns = 3;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p = null;
        for ($i = 0; $i < $ns; $i++) {
            $a = $angle + ($i * 360 / $ns);
            $a_rad = deg2rad((float) $a);
            $p[] = $x0 + ($r * sin($a_rad));
            $p[] = $y0 + ($r * cos($a_rad));
        }
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a star polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - nv: Number of vertices
    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($nv < 2)
            $nv = 2;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p2 = null;
        $visited = null;
        for ($i = 0; $i < $nv; $i++) {
            $a = $angle + ($i * 360 / $nv);
            $a_rad = deg2rad((float) $a);
            $p2[] = $x0 + ($r * sin($a_rad));
            $p2[] = $y0 + ($r * cos($a_rad));
            $visited[] = false;
        }
        $p = null;
        $i = 0;
        do {
            $p[] = $p2[$i * 2];
            $p[] = $p2[($i * 2) + 1];
            $visited[$i] = true;
            $i += $ng;
            $i %= $nv;
        } while (!$visited[$i]);
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a rounded rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - r: Radius of the rounded corners
    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
    // - border_style: Border style of rectangle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
        if ('0000' == $round_corner) // Not rounded
            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
        else { // Rounded
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($red, $g, $b) = $fill_color;
                $this->SetFillColor($red, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $border_style = null;
                    $op = 'f';
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($border_style)
                $this->SetLineStyle($border_style);

            $MyArc = 4 / 3 * (sqrt(2) - 1);

            $this->_Point($x + $r, $y);
            $xc = $x + $w - $r;
            $yc = $y + $r;
            $this->_Line($xc, $y);
            if ($round_corner[0])
                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
            else
                $this->_Line($x + $w, $y);

            $xc = $x + $w - $r ;
            $yc = $y + $h - $r;
            $this->_Line($x + $w, $yc);

            if ($round_corner[1])
                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
            else
                $this->_Line($x + $w, $y + $h);

            $xc = $x + $r;
            $yc = $y + $h - $r;
            $this->_Line($xc, $y + $h);
            if ($round_corner[2])
                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
            else
                $this->_Line($x, $y + $h);

            $xc = $x + $r;
            $yc = $y + $r;
            $this->_Line($x, $yc);
            if ($round_corner[3])
                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
            else {
                $this->_Line($x, $y);
                $this->_Line($x + $r, $y);
            }
            $this->_out($op);
        }
    }

    /* PRIVATE METHODS */

    /**
     * Sets a draw point
     *
     * @param unknown_type $x Point
     * @param unknown_type $y Point
     */
    function _Point($x, $y){
        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
    }

    /**
     * Draws a line from last draw point
     *
     * @param unknown_type $x End point
     * @param unknown_type $y End point
     */
    function _Line($x, $y) {
        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    /**
     * Draws a Bézier curve from last draw point
     *
     * @param unknown_type $x1 Control point 1
     * @param unknown_type $y1 Control point 1
     * @param unknown_type $x2 Control point 2
     * @param unknown_type $y2 Control point 2
     * @param unknown_type $x3 End point
     * @param unknown_type $y3 End point
     */
    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }       
    
    function Footer()
    {
        if($this->nr_pag_de)
        {
            $this->SetTextColor(0,0,0);
            //Go to 1.5 cm from bottom
            $this->SetXY(7,-15);
            //Select Arial italic 8
            $this->SetFont('Courier','I',6);
            //Print current and total page numbers
            $this->Cell(0,10,date('d/m/Y G:i:s'),0,0,'L');
            
            $this->SetFont('Courier','I',8);
            
            $nr_pagina = $this->PageNo().'/{qt_pagina}';
            $x = (($this->GetStringWidth($nr_pagina)) * -1) + 6;
            $this->SetX($x);
            
            #$this->SetX(-10);
            
            
            
            $this->Cell(0,10,$nr_pagina,0,0,'L');
        }   
        elseif($this->nr_pag)
        {
            $this->SetTextColor(0,0,0);
            //Go to 1.5 cm from bottom
            $this->SetXY(7,-15);
            //Select Arial italic 8
            $this->SetFont('Courier','I',6);
            //Print current and total page numbers
            $this->Cell(0,10,date('d/m/Y G:i:s'),0,0,'L');
            
            $this->SetFont('Courier','I',8);
            $this->SetX(-10);
            $this->Cell(0,10,$this->PageNo(),0,0,'L');
        }
    }
    
    function i25($xpos, $ypos, $code, $basewidth=1, $height=10)
    {

        $wide = $basewidth;
        $narrow = $basewidth / 3 ;

        // wide/narrow codes for the digits
        $barChar['0'] = 'nnwwn';
        $barChar['1'] = 'wnnnw';
        $barChar['2'] = 'nwnnw';
        $barChar['3'] = 'wwnnn';
        $barChar['4'] = 'nnwnw';
        $barChar['5'] = 'wnwnn';
        $barChar['6'] = 'nwwnn';
        $barChar['7'] = 'nnnww';
        $barChar['8'] = 'wnnwn';
        $barChar['9'] = 'nwnwn';
        $barChar['A'] = 'nn';
        $barChar['Z'] = 'wn';

        // add leading zero if code-length is odd
        if(strlen($code) % 2 != 0){
            $code = '0' . $code;
        }
    /*
        $this->SetFont('Arial','',5);
        $this->Text($xpos, $ypos + $height + 4, $code);
        $this->SetFillColor(0);
    */
        // add start and stop codes
        $code = 'AA'.strtolower($code).'ZA';

        for($i=0; $i<strlen($code); $i=$i+2){
            // choose next pair of digits
            $charBar = $code[$i];
            $charSpace = $code[$i+1];
            // check whether it is a valid digit
            if(!isset($barChar[$charBar])){
                $this->Error('Invalid character in barcode: '.$charBar);
            }
            if(!isset($barChar[$charSpace])){
                $this->Error('Invalid character in barcode: '.$charSpace);
            }
            // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
            $seq = '';
            for($s=0; $s<strlen($barChar[$charBar]); $s++){
                $seq .= $barChar[$charBar][$s] . $barChar[$charSpace][$s];
            }
            for($bar=0; $bar<strlen($seq); $bar++){
                // set lineWidth depending on value
                if($seq[$bar] == 'n'){
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                // draw every second value, because the second digit of the pair is represented by the spaces
                if($bar % 2 == 0){
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
        }
    }   

    function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
    {
        if($cw){
            $d = $b;
            $b = $o - $a;
            $a = $o - $d;
        }else{
            $b += $o;
            $a += $o;
        }
        $a = ($a%360)+360;
        $b = ($b%360)+360;
        if ($a > $b)
            $b +=360;
        $b = $b/360*2*M_PI;
        $a = $a/360*2*M_PI;
        $d = $b-$a;
        if ($d == 0 )
            $d =2*M_PI;
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='b';
        else
            $op='s';
        if (sin($d/2))
            $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
        //first put the center
        $this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
        //put the first point
        $this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
        //draw the arc
        if ($d < M_PI/2){
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        }else{
            $b = $a + $d/4;
            $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
            $a = $b;
            $b = $a + $d/4;
            $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                        $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                        $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                        $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                        $xc+$r*cos($b),
                        $yc-$r*sin($b)
                        );
        }
        //terminate drawing
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
    {
        $h = $this->h;
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
            $x1*$this->k,
            ($h-$y1)*$this->k,
            $x2*$this->k,
            ($h-$y2)*$this->k,
            $x3*$this->k,
            ($h-$y3)*$this->k));
    }           
    
    function PieChart($w, $h, $data, $format, $colors=null)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);

        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $hLegend = 5;
        $radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
        $radius = floor($radius / 2);
        $XDiag = $XPage + $margin + $radius;
        $YDiag = $YPage + $margin + $radius;
        if($colors == null) {
            for($i = 0;$i < $this->NbVal; $i++) {
                $gray = $i * intval(255 / $this->NbVal);
                $colors[$i] = array($gray,$gray,$gray);
            }
        }

        //Sectors
        $this->SetLineWidth(0.2);
        $angleStart = 0;
        $angleEnd = 0;
        $i = 0;
        foreach($data as $val) {
            $angle = floor(($val * 360) / doubleval($this->sum));
            if ($angle != 0) {
                $angleEnd = $angleStart + $angle;
                $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
                $this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
                $angleStart += $angle;
            }
            $i++;
        }
        if ($angleEnd != 360) {
            $this->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
        }

        //Legends
        $this->SetFont('Courier', '', 10);
        $x1 = $XPage + 2 * $radius + 4 * $margin;
        $x2 = $x1 + $hLegend + $margin;
        $y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
        for($i=0; $i<$this->NbVal; $i++) {
            $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
            $this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
            $this->SetXY($x2,$y1);
            $this->Cell(0,$hLegend,$this->legends[$i]);
            $y1+=$hLegend + $margin;
        }
    }

    function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
    {
        $this->SetFont('Courier', '', 10);
        $this->SetLegends($data,$format);

        $XPage = $this->GetX();
        $YPage = $this->GetY();
        $margin = 2;
        $YDiag = $YPage + $margin;
        $hDiag = floor($h - $margin * 2);
        $XDiag = $XPage + $margin * 2 + $this->wLegend;
        $lDiag = floor($w - $margin * 3 - $this->wLegend);
        if($color == null)
            $color=array(155,155,155);
        if ($maxVal == 0) {
            $maxVal = max($data);
        }
        $valIndRepere = ceil($maxVal / $nbDiv);
        $maxVal = $valIndRepere * $nbDiv;
        $lRepere = floor($lDiag / $nbDiv);
        $lDiag = $lRepere * $nbDiv;
        $unit = $lDiag / $maxVal;
        $hBar = floor($hDiag / ($this->NbVal + 1));
        $hDiag = $hBar * ($this->NbVal + 1);
        $eBaton = floor($hBar * 80 / 100);

        $this->SetLineWidth(0.2);
        $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

        $this->SetFont('Courier', '', 10);
        $this->SetFillColor($color[0],$color[1],$color[2]);
        $i=0;
        foreach($data as $val) {
            //Bar
            $xval = $XDiag;
            $lval = (int)($val * $unit);
            $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
            $hval = $eBaton;
            $this->Rect($xval, $yval, $lval, $hval, 'DF');
            //Legend
            $this->SetXY(0, $yval);
            $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
            $i++;
        }

        //Scales
        for ($i = 0; $i <= $nbDiv; $i++) {
            $xpos = $XDiag + $lRepere * $i;
            $this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
            $val = $i * $valIndRepere;
            $xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
            $ypos = $YDiag + $hDiag - $margin;
            $this->Text($xpos, $ypos, $val);
        }
    }

    function SetLegends($data, $format)
    {
        $this->legends=array();
        $this->wLegend=0;
        $this->sum=array_sum($data);
        $this->NbVal=count($data);
        foreach($data as $l=>$val)
        {
            $p=sprintf('%.2f',$val/$this->sum*100).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
            $this->legends[]=$legend;
            $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
        }
    }

    
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

    function getTema()
    {
    $this->AR_TABELA_COR[] = array(0,255,255);      
    $this->AR_TABELA_COR[] = array(0,255,0);        
    $this->AR_TABELA_COR[] = array(0,128,128);
    $this->AR_TABELA_COR[] = array(245,245,245);
    $this->AR_TABELA_COR[] = array(220,220,220);
    $this->AR_TABELA_COR[] = array(253,245,230);
    $this->AR_TABELA_COR[] = array(250,240,230);
    $this->AR_TABELA_COR[] = array(250,235,215);
    $this->AR_TABELA_COR[] = array(255,239,213);
    $this->AR_TABELA_COR[] = array(255,235,205);
    $this->AR_TABELA_COR[] = array(255,228,196);
    $this->AR_TABELA_COR[] = array(255,218,185);
    $this->AR_TABELA_COR[] = array(255,222,173);
    $this->AR_TABELA_COR[] = array(255,228,181);
    $this->AR_TABELA_COR[] = array(255,248,220);
    $this->AR_TABELA_COR[] = array(255,255,240);
    $this->AR_TABELA_COR[] = array(255,250,205);
    $this->AR_TABELA_COR[] = array(255,245,238);
    $this->AR_TABELA_COR[] = array(245,255,250);
    $this->AR_TABELA_COR[] = array(240,255,255);
    $this->AR_TABELA_COR[] = array(240,248,255);
    $this->AR_TABELA_COR[] = array(230,230,250);
    $this->AR_TABELA_COR[] = array(255,240,245);
    $this->AR_TABELA_COR[] = array(255,228,225);
    $this->AR_TABELA_COR[] = array(255,255,255);
    $this->AR_TABELA_COR[] = array(0,0,0);
    $this->AR_TABELA_COR[] = array(47,79,79);
    $this->AR_TABELA_COR[] = array(105,105,105);
    $this->AR_TABELA_COR[] = array(112,128,144);
    $this->AR_TABELA_COR[] = array(119,136,153);
    $this->AR_TABELA_COR[] = array(190,190,190);
    $this->AR_TABELA_COR[] = array(211,211,211);
    $this->AR_TABELA_COR[] = array(25,25,112);
    $this->AR_TABELA_COR[] = array(0,0,128);
    $this->AR_TABELA_COR[] = array(100,149,237);
    $this->AR_TABELA_COR[] = array(72,61,139);
    $this->AR_TABELA_COR[] = array(106,90,205);
    $this->AR_TABELA_COR[] = array(123,104,238);
    $this->AR_TABELA_COR[] = array(132,112,255);
    $this->AR_TABELA_COR[] = array(0,0,205);
    $this->AR_TABELA_COR[] = array(65,105,225);
    $this->AR_TABELA_COR[] = array(0,0,255);
    $this->AR_TABELA_COR[] = array(30,144,255);
    $this->AR_TABELA_COR[] = array(0,191,255);
    $this->AR_TABELA_COR[] = array(135,206,235);
    $this->AR_TABELA_COR[] = array(135,206,250);
    $this->AR_TABELA_COR[] = array(70,130,180);
    $this->AR_TABELA_COR[] = array(211,167,168);
    $this->AR_TABELA_COR[] = array(176,196,222);
    $this->AR_TABELA_COR[] = array(173,216,230);
    $this->AR_TABELA_COR[] = array(176,224,230);
    $this->AR_TABELA_COR[] = array(175,238,238);
    $this->AR_TABELA_COR[] = array(0,206,209);
    $this->AR_TABELA_COR[] = array(72,209,204);
    $this->AR_TABELA_COR[] = array(64,224,208);
    $this->AR_TABELA_COR[] = array(0,255,255);
    $this->AR_TABELA_COR[] = array(224,255,255);
    $this->AR_TABELA_COR[] = array(95,158,160);
    $this->AR_TABELA_COR[] = array(102,205,170);
    $this->AR_TABELA_COR[] = array(127,255,212);
    $this->AR_TABELA_COR[] = array(0,100,0);
    $this->AR_TABELA_COR[] = array(85,107,47);
    $this->AR_TABELA_COR[] = array(143,188,143);
    $this->AR_TABELA_COR[] = array(46,139,87);
    $this->AR_TABELA_COR[] = array(60,179,113);
    $this->AR_TABELA_COR[] = array(32,178,170);
    $this->AR_TABELA_COR[] = array(152,251,152);
    $this->AR_TABELA_COR[] = array(0,255,127);
    $this->AR_TABELA_COR[] = array(124,252,0);
    $this->AR_TABELA_COR[] = array(0,255,0);
    $this->AR_TABELA_COR[] = array(127,255,0);
    $this->AR_TABELA_COR[] = array(0,250,154);
    $this->AR_TABELA_COR[] = array(173,255,47);
    $this->AR_TABELA_COR[] = array(50,205,50);
    $this->AR_TABELA_COR[] = array(154,205,50);
    $this->AR_TABELA_COR[] = array(34,139,34);
    $this->AR_TABELA_COR[] = array(107,142,35);
    $this->AR_TABELA_COR[] = array(189,183,107);
    $this->AR_TABELA_COR[] = array(240,230,140);
    $this->AR_TABELA_COR[] = array(238,232,170);
    $this->AR_TABELA_COR[] = array(250,250,210);
    $this->AR_TABELA_COR[] = array(255,255,200);
    $this->AR_TABELA_COR[] = array(255,255,0);
    $this->AR_TABELA_COR[] = array(255,215,0);
    $this->AR_TABELA_COR[] = array(238,221,130);
    $this->AR_TABELA_COR[] = array(218,165,32);
    $this->AR_TABELA_COR[] = array(184,134,11);
    $this->AR_TABELA_COR[] = array(188,143,143);
    $this->AR_TABELA_COR[] = array(205,92,92);
    $this->AR_TABELA_COR[] = array(139,69,19);
    $this->AR_TABELA_COR[] = array(160,82,45);
    $this->AR_TABELA_COR[] = array(205,133,63);
    $this->AR_TABELA_COR[] = array(222,184,135);
    $this->AR_TABELA_COR[] = array(245,245,220);
    $this->AR_TABELA_COR[] = array(245,222,179);
    $this->AR_TABELA_COR[] = array(244,164,96);
    $this->AR_TABELA_COR[] = array(210,180,140);
    $this->AR_TABELA_COR[] = array(210,105,30);
    $this->AR_TABELA_COR[] = array(178,34,34);
    $this->AR_TABELA_COR[] = array(165,42,42);
    $this->AR_TABELA_COR[] = array(233,150,122);
    $this->AR_TABELA_COR[] = array(250,128,114);
    $this->AR_TABELA_COR[] = array(255,160,122);
    $this->AR_TABELA_COR[] = array(255,165,0);
    $this->AR_TABELA_COR[] = array(255,140,0);
    $this->AR_TABELA_COR[] = array(255,127,80);
    $this->AR_TABELA_COR[] = array(240,128,128);
    $this->AR_TABELA_COR[] = array(255,99,71);
    $this->AR_TABELA_COR[] = array(255,69,0);
    $this->AR_TABELA_COR[] = array(255,0,0);
    $this->AR_TABELA_COR[] = array(255,105,180);
    $this->AR_TABELA_COR[] = array(255,20,147);
    $this->AR_TABELA_COR[] = array(255,192,203);
    $this->AR_TABELA_COR[] = array(255,182,193);
    $this->AR_TABELA_COR[] = array(219,112,147);
    $this->AR_TABELA_COR[] = array(176,48,96);
    $this->AR_TABELA_COR[] = array(199,21,133);
    $this->AR_TABELA_COR[] = array(208,32,144);
    $this->AR_TABELA_COR[] = array(255,0,255);
    $this->AR_TABELA_COR[] = array(238,130,238);
    $this->AR_TABELA_COR[] = array(221,160,221);
    $this->AR_TABELA_COR[] = array(218,112,214);
    $this->AR_TABELA_COR[] = array(186,85,211);
    $this->AR_TABELA_COR[] = array(153,50,204);
    $this->AR_TABELA_COR[] = array(148,0,211);
    $this->AR_TABELA_COR[] = array(138,43,226);
    $this->AR_TABELA_COR[] = array(160,32,240);
    $this->AR_TABELA_COR[] = array(147,112,219);
    $this->AR_TABELA_COR[] = array(216,191,216);
    $this->AR_TABELA_COR[] = array(255,250,250);
    $this->AR_TABELA_COR[] = array(238,233,233);
    $this->AR_TABELA_COR[] = array(205,201,201);
    $this->AR_TABELA_COR[] = array(139,137,137);
    $this->AR_TABELA_COR[] = array(255,245,238);
    $this->AR_TABELA_COR[] = array(238,229,222);
    $this->AR_TABELA_COR[] = array(205,197,191);
    $this->AR_TABELA_COR[] = array(139,134,130);
    $this->AR_TABELA_COR[] = array(255,239,219);
    $this->AR_TABELA_COR[] = array(238,223,204);
    $this->AR_TABELA_COR[] = array(205,192,176);
    $this->AR_TABELA_COR[] = array(139,131,120);
    $this->AR_TABELA_COR[] = array(255,228,196);
    $this->AR_TABELA_COR[] = array(238,213,183);
    $this->AR_TABELA_COR[] = array(205,183,158);
    $this->AR_TABELA_COR[] = array(139,125,107);
    $this->AR_TABELA_COR[] = array(255,218,185);
    $this->AR_TABELA_COR[] = array(238,203,173);
    $this->AR_TABELA_COR[] = array(205,175,149);
    $this->AR_TABELA_COR[] = array(139,119,101);
    $this->AR_TABELA_COR[] = array(255,222,173);
    $this->AR_TABELA_COR[] = array(238,207,161);
    $this->AR_TABELA_COR[] = array(205,179,139);
    $this->AR_TABELA_COR[] = array(139,121,94);
    $this->AR_TABELA_COR[] = array(255,250,205);
    $this->AR_TABELA_COR[] = array(238,233,191);
    $this->AR_TABELA_COR[] = array(205,201,165);
    $this->AR_TABELA_COR[] = array(139,137,112);
    $this->AR_TABELA_COR[] = array(255,255,240);
    $this->AR_TABELA_COR[] = array(238,238,224);
    $this->AR_TABELA_COR[] = array(205,205,193);
    $this->AR_TABELA_COR[] = array(139,139,131);
    $this->AR_TABELA_COR[] = array(193,205,193);
    $this->AR_TABELA_COR[] = array(255,240,245);
    $this->AR_TABELA_COR[] = array(238,224,229);
    $this->AR_TABELA_COR[] = array(205,193,197);
    $this->AR_TABELA_COR[] = array(139,131,134);
    $this->AR_TABELA_COR[] = array(255,228,225);
    $this->AR_TABELA_COR[] = array(238,213,210);
    $this->AR_TABELA_COR[] = array(205,183,181);
    $this->AR_TABELA_COR[] = array(139,125,123);
    $this->AR_TABELA_COR[] = array(240,255,255);
    $this->AR_TABELA_COR[] = array(224,238,238);
    $this->AR_TABELA_COR[] = array(193,205,205);
    $this->AR_TABELA_COR[] = array(131,139,139);
    $this->AR_TABELA_COR[] = array(131,111,255);
    $this->AR_TABELA_COR[] = array(122,103,238);
    $this->AR_TABELA_COR[] = array(105,89,205);
    $this->AR_TABELA_COR[] = array(71,60,139);
    $this->AR_TABELA_COR[] = array(72,118,255);
    $this->AR_TABELA_COR[] = array(67,110,238);
    $this->AR_TABELA_COR[] = array(58,95,205);
    $this->AR_TABELA_COR[] = array(39,64,139);
    $this->AR_TABELA_COR[] = array(30,144,255);
    $this->AR_TABELA_COR[] = array(28,134,238);
    $this->AR_TABELA_COR[] = array(24,116,205);
    $this->AR_TABELA_COR[] = array(16,78,139);
    $this->AR_TABELA_COR[] = array(99,184,255);
    $this->AR_TABELA_COR[] = array(92,172,238);
    $this->AR_TABELA_COR[] = array(79,148,205);
    $this->AR_TABELA_COR[] = array(54,100,139);
    $this->AR_TABELA_COR[] = array(0,191,255);
    $this->AR_TABELA_COR[] = array(0,178,238);
    $this->AR_TABELA_COR[] = array(0,154,205);
    $this->AR_TABELA_COR[] = array(0,104,139);
    $this->AR_TABELA_COR[] = array(135,206,255);
    $this->AR_TABELA_COR[] = array(126,192,238);
    $this->AR_TABELA_COR[] = array(108,166,205);
    $this->AR_TABELA_COR[] = array(74,112,139);
    $this->AR_TABELA_COR[] = array(176,226,255);
    $this->AR_TABELA_COR[] = array(164,211,238);
    $this->AR_TABELA_COR[] = array(141,182,205);
    $this->AR_TABELA_COR[] = array(96,123,139);
    $this->AR_TABELA_COR[] = array(198,226,255);
    $this->AR_TABELA_COR[] = array(185,211,238);
    $this->AR_TABELA_COR[] = array(159,182,205);
    $this->AR_TABELA_COR[] = array(108,123,139);
    $this->AR_TABELA_COR[] = array(202,225,255);
    $this->AR_TABELA_COR[] = array(188,210,238);
    $this->AR_TABELA_COR[] = array(162,181,205);
    $this->AR_TABELA_COR[] = array(110,123,139);
    $this->AR_TABELA_COR[] = array(191,239,255);
    $this->AR_TABELA_COR[] = array(178,223,238);
    $this->AR_TABELA_COR[] = array(154,192,205);
    $this->AR_TABELA_COR[] = array(104,131,139);
    $this->AR_TABELA_COR[] = array(224,255,255);
    $this->AR_TABELA_COR[] = array(209,238,238);
    $this->AR_TABELA_COR[] = array(180,205,205);
    $this->AR_TABELA_COR[] = array(122,139,139);
    $this->AR_TABELA_COR[] = array(187,255,255);
    $this->AR_TABELA_COR[] = array(174,238,238);
    $this->AR_TABELA_COR[] = array(150,205,205);
    $this->AR_TABELA_COR[] = array(102,139,139);
    $this->AR_TABELA_COR[] = array(152,245,255);
    $this->AR_TABELA_COR[] = array(142,229,238);
    $this->AR_TABELA_COR[] = array(122,197,205);
    $this->AR_TABELA_COR[] = array(83,134,139);
    $this->AR_TABELA_COR[] = array(0,245,255);
    $this->AR_TABELA_COR[] = array(0,229,238);
    $this->AR_TABELA_COR[] = array(0,197,205);
    $this->AR_TABELA_COR[] = array(0,134,139);
    $this->AR_TABELA_COR[] = array(0,255,255);
    $this->AR_TABELA_COR[] = array(0,238,238);
    $this->AR_TABELA_COR[] = array(0,205,205);
    $this->AR_TABELA_COR[] = array(0,139,139);
    $this->AR_TABELA_COR[] = array(151,255,255);
    $this->AR_TABELA_COR[] = array(141,238,238);
    $this->AR_TABELA_COR[] = array(121,205,205);
    $this->AR_TABELA_COR[] = array(82,139,139);
    $this->AR_TABELA_COR[] = array(127,255,212);
    $this->AR_TABELA_COR[] = array(118,238,198);
    $this->AR_TABELA_COR[] = array(102,205,170);
    $this->AR_TABELA_COR[] = array(69,139,116);
    $this->AR_TABELA_COR[] = array(193,255,193);
    $this->AR_TABELA_COR[] = array(180,238,180);
    $this->AR_TABELA_COR[] = array(155,205,155);
    $this->AR_TABELA_COR[] = array(105,139,105);
    $this->AR_TABELA_COR[] = array(84,255,159);
    $this->AR_TABELA_COR[] = array(78,238,148);
    $this->AR_TABELA_COR[] = array(67,205,128);
    $this->AR_TABELA_COR[] = array(46,139,87);
    $this->AR_TABELA_COR[] = array(154,255,154);
    $this->AR_TABELA_COR[] = array(144,238,144);
    $this->AR_TABELA_COR[] = array(124,205,124);
    $this->AR_TABELA_COR[] = array(84,139,84);
    $this->AR_TABELA_COR[] = array(0,255,127);
    $this->AR_TABELA_COR[] = array(0,238,118);
    $this->AR_TABELA_COR[] = array(0,205,102);
    $this->AR_TABELA_COR[] = array(0,139,69);
    $this->AR_TABELA_COR[] = array(127,255,0);
    $this->AR_TABELA_COR[] = array(118,238,0);
    $this->AR_TABELA_COR[] = array(102,205,0);
    $this->AR_TABELA_COR[] = array(69,139,0);
    $this->AR_TABELA_COR[] = array(192,255,62);
    $this->AR_TABELA_COR[] = array(179,238,58);
    $this->AR_TABELA_COR[] = array(154,205,50);
    $this->AR_TABELA_COR[] = array(105,139,34);
    $this->AR_TABELA_COR[] = array(202,255,112);
    $this->AR_TABELA_COR[] = array(188,238,104);
    $this->AR_TABELA_COR[] = array(162,205,90);
    $this->AR_TABELA_COR[] = array(110,139,61);
    $this->AR_TABELA_COR[] = array(255,246,143);
    $this->AR_TABELA_COR[] = array(238,230,133);
    $this->AR_TABELA_COR[] = array(205,198,115);
    $this->AR_TABELA_COR[] = array(139,134,78);
    $this->AR_TABELA_COR[] = array(255,236,139);
    $this->AR_TABELA_COR[] = array(238,220,130);
    $this->AR_TABELA_COR[] = array(205,190,112);
    $this->AR_TABELA_COR[] = array(139,129,76);
    $this->AR_TABELA_COR[] = array(255,255,0);
    $this->AR_TABELA_COR[] = array(238,238,0);
    $this->AR_TABELA_COR[] = array(205,205,0);
    $this->AR_TABELA_COR[] = array(139,139,0);
    $this->AR_TABELA_COR[] = array(255,215,0);
    $this->AR_TABELA_COR[] = array(238,201,0);
    $this->AR_TABELA_COR[] = array(205,173,0);
    $this->AR_TABELA_COR[] = array(139,117,0);
    $this->AR_TABELA_COR[] = array(255,193,37);
    $this->AR_TABELA_COR[] = array(238,180,34);
    $this->AR_TABELA_COR[] = array(205,155,29);
    $this->AR_TABELA_COR[] = array(139,105,20);
    $this->AR_TABELA_COR[] = array(255,185,15);
    $this->AR_TABELA_COR[] = array(238,173,14);
    $this->AR_TABELA_COR[] = array(205,149,12);
    $this->AR_TABELA_COR[] = array(139,101,8);
    $this->AR_TABELA_COR[] = array(255,193,193);
    $this->AR_TABELA_COR[] = array(238,180,180);
    $this->AR_TABELA_COR[] = array(205,155,155);
    $this->AR_TABELA_COR[] = array(139,105,105);
    $this->AR_TABELA_COR[] = array(255,106,106);
    $this->AR_TABELA_COR[] = array(238,99,99);
    $this->AR_TABELA_COR[] = array(205,85,85);
    $this->AR_TABELA_COR[] = array(139,58,58);
    $this->AR_TABELA_COR[] = array(255,130,71);
    $this->AR_TABELA_COR[] = array(238,121,66);
    $this->AR_TABELA_COR[] = array(205,104,57);
    $this->AR_TABELA_COR[] = array(139,71,38);
    $this->AR_TABELA_COR[] = array(255,211,155);
    $this->AR_TABELA_COR[] = array(238,197,145);
    $this->AR_TABELA_COR[] = array(205,170,125);
    $this->AR_TABELA_COR[] = array(139,115,85);
    $this->AR_TABELA_COR[] = array(255,231,186);
    $this->AR_TABELA_COR[] = array(238,216,174);
    $this->AR_TABELA_COR[] = array(205,186,150);
    $this->AR_TABELA_COR[] = array(139,126,102);
    $this->AR_TABELA_COR[] = array(255,165,79);
    $this->AR_TABELA_COR[] = array(238,154,73);
    $this->AR_TABELA_COR[] = array(205,133,63);
    $this->AR_TABELA_COR[] = array(139,90,43);
    $this->AR_TABELA_COR[] = array(255,127,36);
    $this->AR_TABELA_COR[] = array(238,118,33);
    $this->AR_TABELA_COR[] = array(205,102,29);
    $this->AR_TABELA_COR[] = array(139,69,19);
    $this->AR_TABELA_COR[] = array(255,48,48);
    $this->AR_TABELA_COR[] = array(238,44,44);
    $this->AR_TABELA_COR[] = array(205,38,38);
    $this->AR_TABELA_COR[] = array(139,26,26);
    $this->AR_TABELA_COR[] = array(255,64,64);
    $this->AR_TABELA_COR[] = array(238,59,59);
    $this->AR_TABELA_COR[] = array(205,51,51);
    $this->AR_TABELA_COR[] = array(139,35,35);
    $this->AR_TABELA_COR[] = array(255,140,105);
    $this->AR_TABELA_COR[] = array(238,130,98);
    $this->AR_TABELA_COR[] = array(205,112,84);
    $this->AR_TABELA_COR[] = array(139,76,57);
    $this->AR_TABELA_COR[] = array(255,160,122);
    $this->AR_TABELA_COR[] = array(238,149,114);
    $this->AR_TABELA_COR[] = array(205,129,98);
    $this->AR_TABELA_COR[] = array(139,87,66);
    $this->AR_TABELA_COR[] = array(255,165,0);
    $this->AR_TABELA_COR[] = array(238,154,0);
    $this->AR_TABELA_COR[] = array(205,133,0);
    $this->AR_TABELA_COR[] = array(139,90,0);
    $this->AR_TABELA_COR[] = array(255,127,0);
    $this->AR_TABELA_COR[] = array(238,118,0);
    $this->AR_TABELA_COR[] = array(205,102,0);
    $this->AR_TABELA_COR[] = array(139,69,0);
    $this->AR_TABELA_COR[] = array(255,114,86);
    $this->AR_TABELA_COR[] = array(238,106,80);
    $this->AR_TABELA_COR[] = array(205,91,69);
    $this->AR_TABELA_COR[] = array(139,62,47);
    $this->AR_TABELA_COR[] = array(255,99,71);
    $this->AR_TABELA_COR[] = array(238,92,66);
    $this->AR_TABELA_COR[] = array(205,79,57);
    $this->AR_TABELA_COR[] = array(139,54,38);
    $this->AR_TABELA_COR[] = array(255,69,0);
    $this->AR_TABELA_COR[] = array(238,64,0);
    $this->AR_TABELA_COR[] = array(205,55,0);
    $this->AR_TABELA_COR[] = array(139,37,0);
    $this->AR_TABELA_COR[] = array(255,20,147);
    $this->AR_TABELA_COR[] = array(238,18,137);
    $this->AR_TABELA_COR[] = array(205,16,118);
    $this->AR_TABELA_COR[] = array(139,10,80);
    $this->AR_TABELA_COR[] = array(255,110,180);
    $this->AR_TABELA_COR[] = array(238,106,167);
    $this->AR_TABELA_COR[] = array(205,96,144);
    $this->AR_TABELA_COR[] = array(139,58,98);
    $this->AR_TABELA_COR[] = array(255,181,197);
    $this->AR_TABELA_COR[] = array(238,169,184);
    $this->AR_TABELA_COR[] = array(205,145,158);
    $this->AR_TABELA_COR[] = array(139,99,108);
    $this->AR_TABELA_COR[] = array(255,174,185);
    $this->AR_TABELA_COR[] = array(238,162,173);
    $this->AR_TABELA_COR[] = array(205,140,149);
    $this->AR_TABELA_COR[] = array(139,95,101);
    $this->AR_TABELA_COR[] = array(255,130,171);
    $this->AR_TABELA_COR[] = array(238,121,159);
    $this->AR_TABELA_COR[] = array(205,104,137);
    $this->AR_TABELA_COR[] = array(139,71,93);
    $this->AR_TABELA_COR[] = array(255,52,179);
    $this->AR_TABELA_COR[] = array(238,48,167);
    $this->AR_TABELA_COR[] = array(205,41,144);
    $this->AR_TABELA_COR[] = array(139,28,98);
    $this->AR_TABELA_COR[] = array(255,62,150);
    $this->AR_TABELA_COR[] = array(238,58,140);
    $this->AR_TABELA_COR[] = array(205,50,120);
    $this->AR_TABELA_COR[] = array(139,34,82);
    $this->AR_TABELA_COR[] = array(255,0,255);
    $this->AR_TABELA_COR[] = array(238,0,238);
    $this->AR_TABELA_COR[] = array(205,0,205);
    $this->AR_TABELA_COR[] = array(139,0,139);
    $this->AR_TABELA_COR[] = array(140,34,34);
    $this->AR_TABELA_COR[] = array(255,131,250);
    $this->AR_TABELA_COR[] = array(238,122,233);
    $this->AR_TABELA_COR[] = array(205,105,201);
    $this->AR_TABELA_COR[] = array(139,71,137);
    $this->AR_TABELA_COR[] = array(255,187,255);
    $this->AR_TABELA_COR[] = array(238,174,238);
    $this->AR_TABELA_COR[] = array(205,150,205);
    $this->AR_TABELA_COR[] = array(139,102,139);
    $this->AR_TABELA_COR[] = array(224,102,255);
    $this->AR_TABELA_COR[] = array(209,95,238);
    $this->AR_TABELA_COR[] = array(180,82,205);
    $this->AR_TABELA_COR[] = array(122,55,139);
    $this->AR_TABELA_COR[] = array(191,62,255);
    $this->AR_TABELA_COR[] = array(178,58,238);
    $this->AR_TABELA_COR[] = array(154,50,205);
    $this->AR_TABELA_COR[] = array(104,34,139);
    $this->AR_TABELA_COR[] = array(155,48,255);
    $this->AR_TABELA_COR[] = array(145,44,238);
    $this->AR_TABELA_COR[] = array(125,38,205);
    $this->AR_TABELA_COR[] = array(85,26,139);
    $this->AR_TABELA_COR[] = array(171,130,255);
    $this->AR_TABELA_COR[] = array(159,121,238);
    $this->AR_TABELA_COR[] = array(137,104,205);
    $this->AR_TABELA_COR[] = array(93,71,139);
    $this->AR_TABELA_COR[] = array(255,225,255);
    $this->AR_TABELA_COR[] = array(238,210,238);
    $this->AR_TABELA_COR[] = array(205,181,205);
    $this->AR_TABELA_COR[] = array(139,123,139);
    $this->AR_TABELA_COR[] = array(10,10,10);
    $this->AR_TABELA_COR[] = array(40,40,30);
    $this->AR_TABELA_COR[] = array(70,70,70);
    $this->AR_TABELA_COR[] = array(100,100,100);
    $this->AR_TABELA_COR[] = array(130,130,130);
    $this->AR_TABELA_COR[] = array(160,160,160);
    $this->AR_TABELA_COR[] = array(190,190,190);
    $this->AR_TABELA_COR[] = array(210,210,210);
    $this->AR_TABELA_COR[] = array(240,240,240);
    $this->AR_TABELA_COR[] = array(100,100,100);
    $this->AR_TABELA_COR[] = array(0,0,139);
    $this->AR_TABELA_COR[] = array(0,139,139);
    $this->AR_TABELA_COR[] = array(139,0,139);
    $this->AR_TABELA_COR[] = array(139,0,0);
    $this->AR_TABELA_COR[] = array(192, 192, 192);
    $this->AR_TABELA_COR[] = array(144,176,168);
    $this->AR_TABELA_COR[] = array(144,238,144);    
 
 $this->AR_THEMA = array(
    "earth"     => array($this->AR_TABELA_COR[136],$this->AR_TABELA_COR[34],$this->AR_TABELA_COR[40],$this->AR_TABELA_COR[45],$this->AR_TABELA_COR[46],$this->AR_TABELA_COR[62],$this->AR_TABELA_COR[63],$this->AR_TABELA_COR[134],$this->AR_TABELA_COR[74],$this->AR_TABELA_COR[10],$this->AR_TABELA_COR[120],$this->AR_TABELA_COR[136],$this->AR_TABELA_COR[141],$this->AR_TABELA_COR[168],$this->AR_TABELA_COR[180],$this->AR_TABELA_COR[77],$this->AR_TABELA_COR[209],$this->AR_TABELA_COR[218],$this->AR_TABELA_COR[346],$this->AR_TABELA_COR[395],$this->AR_TABELA_COR[89],$this->AR_TABELA_COR[430]),
    "pastel" => array(
                        $this->AR_TABELA_COR[415],
                        $this->AR_TABELA_COR[228],
                        $this->AR_TABELA_COR[79],
                        $this->AR_TABELA_COR[105],
                        $this->AR_TABELA_COR[59],
                        $this->AR_TABELA_COR[42],
                        $this->AR_TABELA_COR[147],
                        $this->AR_TABELA_COR[405],
                        $this->AR_TABELA_COR[301],
                        $this->AR_TABELA_COR[428],
                        $this->AR_TABELA_COR[152],
                        $this->AR_TABELA_COR[110],
                        $this->AR_TABELA_COR[337],
                        $this->AR_TABELA_COR[431],
                        $this->AR_TABELA_COR[38],
                        $this->AR_TABELA_COR[177],
                        $this->AR_TABELA_COR[201],
                        $this->AR_TABELA_COR[401],
                        $this->AR_TABELA_COR[402],
                        $this->AR_TABELA_COR[403],
                        $this->AR_TABELA_COR[404],
                        $this->AR_TABELA_COR[405],
                        $this->AR_TABELA_COR[406],
                        $this->AR_TABELA_COR[407],
                        $this->AR_TABELA_COR[50],
                        $this->AR_TABELA_COR[230],
                        $this->AR_TABELA_COR[310],
                        $this->AR_TABELA_COR[409],
						$this->AR_TABELA_COR[1],
						$this->AR_TABELA_COR[10],
						$this->AR_TABELA_COR[20],
						$this->AR_TABELA_COR[30],
						$this->AR_TABELA_COR[40],
						$this->AR_TABELA_COR[50],
						$this->AR_TABELA_COR[60],
						$this->AR_TABELA_COR[70],
						$this->AR_TABELA_COR[80],
						$this->AR_TABELA_COR[90],
						$this->AR_TABELA_COR[100],
						$this->AR_TABELA_COR[110],
						$this->AR_TABELA_COR[100],

    ),
    "water"  => array($this->AR_TABELA_COR[8],$this->AR_TABELA_COR[370],$this->AR_TABELA_COR[24],$this->AR_TABELA_COR[40],$this->AR_TABELA_COR[335],$this->AR_TABELA_COR[56],$this->AR_TABELA_COR[213],$this->AR_TABELA_COR[237],$this->AR_TABELA_COR[268],$this->AR_TABELA_COR[14],$this->AR_TABELA_COR[326],$this->AR_TABELA_COR[387],$this->AR_TABELA_COR[10],$this->AR_TABELA_COR[388]),
    "sand"=> array($this->AR_TABELA_COR[27],$this->AR_TABELA_COR[168],$this->AR_TABELA_COR[34],$this->AR_TABELA_COR[170],$this->AR_TABELA_COR[19],$this->AR_TABELA_COR[50],$this->AR_TABELA_COR[65],$this->AR_TABELA_COR[72],$this->AR_TABELA_COR[131],$this->AR_TABELA_COR[209],$this->AR_TABELA_COR[46],$this->AR_TABELA_COR[393])
    );  
        return $this->AR_THEMA;
    }
    

    #### ANEXAR ARQUIVOS ####
    var $files = array();
    var $n_files;
    var $open_attachment_pane = false;

    function Attach($file, $name='', $desc='')
    {
        if($name=='')
        {
            $p = strrpos($file,'/');
            if($p===false)
                $p = strrpos($file,'\\');
            if($p!==false)
                $name = substr($file,$p+1);
            else
                $name = $file;
        }
        $this->files[] = array('file'=>$file, 'name'=>$name, 'desc'=>$desc);
    }

    function OpenAttachmentPane()
    {
        $this->open_attachment_pane = true;
    }

    function _putfiles()
    {
        $s = '';
        foreach($this->files as $i=>$info)
        {
            $file = $info['file'];
            $name = $info['name'];
            $desc = $info['desc'];

            $fc = file_get_contents($file);
            if($fc===false)
                $this->Error('Cannot open file: '.$file);

            $this->_newobj();
            $s .= $this->_textstring(sprintf('%03d',$i)).' '.$this->n.' 0 R ';
            $this->_out('<<');
            $this->_out('/Type /Filespec');
            $this->_out('/F '.$this->_textstring($name));
            $this->_out('/EF <</F '.($this->n+1).' 0 R>>');
            if($desc)
                $this->_out('/Desc '.$this->_textstring($desc));
            $this->_out('>>');
            $this->_out('endobj');

            $this->_newobj();
            $this->_out('<<');
            $this->_out('/Type /EmbeddedFile');
            $this->_out('/Length '.strlen($fc));
            $this->_out('>>');
            $this->_putstream($fc);
            $this->_out('endobj');
        }
        $this->_newobj();
        $this->n_files = $this->n;
        $this->_out('<<');
        $this->_out('/Names ['.$s.']');
        $this->_out('>>');
        $this->_out('endobj');
    }

    function _putresources()
    {
        parent::_putresources();
        if(!empty($this->files))
            $this->_putfiles();
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        if(!empty($this->files))
            $this->_out('/Names <</EmbeddedFiles '.$this->n_files.' 0 R>>');
        if($this->open_attachment_pane)
            $this->_out('/PageMode /UseAttachments');
    }   
    
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

    ##### WRITEHTML #####
    function WriteHTML($html)
    {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        
        #echo "<PRE>";print_r($a);exit;
        
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,$e);
                    #$this->Cell(140,5,$e,0,1,'J');
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract attributes
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $attr=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    function OpenTag($tag,$attr)
    {
        //Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyleWriteHTML($tag,true);
        if($tag=='A')
            $this->HREF=$attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyleWriteHTML($tag,false);
        if($tag=='A')
            $this->HREF='';
    }

    function SetStyleWriteHTML($tag,$enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
        {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyleWriteHTML('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyleWriteHTML('U',false);
        $this->SetTextColor(0);
    }
    ##### WRITEHTML #####   
    

    ##### WRITETAG ####
    function WriteTagSetStyle()
    {
        //PARAGRÁFO
        $this->SetStyle('p',   'times', 'N', 12, '0,0,0');
        //NEGRITO
        $this->SetStyle('b',   'times', 'B',  0, '0,0,0');
        //NEGRITO ITALICO
        $this->SetStyle('bi',  'times', 'BI',  0, '0,0,0');
        //NEGRITO SUBLINHADO
        $this->SetStyle('bu',   'times', 'BU', 0, '0,0,0');
        //NEGRITO ITALICO SUBLINHADO
        $this->SetStyle('biu', 'times', 'BIU',  0, '0,0,0');
        //ITALICO
        $this->SetStyle('i',   'times', 'I',  0, '0,0,0');
        //ITALICO SUBLINHADO
        $this->SetStyle('iu',   'times', 'IU',  0, '0,0,0');
        //SUBLINHADO
        $this->SetStyle('u',   'times', 'U',  0, '0,0,0');
        //LINK
        $this->SetStyle('a',   'times', 'BU', 0, '0,0,0');
    }

    function WriteTag($w, $h, $txt, $border=0, $align="J", $fill=false, $padding=0)
    {
        $this->wLine=$w;
        $this->hLine=$h;
        $this->Text=trim($txt);
        $this->Text=preg_replace("/\n|\r|\t/","",$this->Text);
        $this->border=$border;
        $this->align=$align;
        $this->fill=$fill;
        $this->Padding=$padding;

        $this->Xini=$this->GetX();
        $this->href="";
        $this->PileStyle=array();        
        $this->TagHref=array();
        $this->LastLine=false;

        $this->SetSpace();
        $this->Padding();
        $this->LineLength();
        $this->BorderTop();

        while($this->Text!="")
        {
            $this->MakeLine();
            $this->PrintLine();
        }

        $this->BorderBottom();
    }


    function SetStyle($tag, $family, $style, $size, $color, $indent=-1)
    {
         $tag=trim($tag);
         $this->TagStyle[$tag]['family']=trim($family);
         $this->TagStyle[$tag]['style']=trim($style);
         $this->TagStyle[$tag]['size']=trim($size);
         $this->TagStyle[$tag]['color']=trim($color);
         $this->TagStyle[$tag]['indent']=$indent;
    }


    // Private Functions

    function SetSpace() // Minimal space between words
    {
        $tag=$this->Parser($this->Text);
        $this->FindStyle($tag[2],0);
        $this->DoStyle(0);
        $this->Space=$this->GetStringWidth(" ");
    }


    function Padding()
    {
        if(preg_match("/^.+,/",$this->Padding)) {
            $tab=explode(",",$this->Padding);
            $this->lPadding=$tab[0];
            $this->tPadding=$tab[1];
            if(isset($tab[2]))
                $this->bPadding=$tab[2];
            else
                $this->bPadding=$this->tPadding;
            if(isset($tab[3]))
                $this->rPadding=$tab[3];
            else
                $this->rPadding=$this->lPadding;
        }
        else
        {
            $this->lPadding=$this->Padding;
            $this->tPadding=$this->Padding;
            $this->bPadding=$this->Padding;
            $this->rPadding=$this->Padding;
        }
        if($this->tPadding<$this->LineWidth)
            $this->tPadding=$this->LineWidth;
    }


    function LineLength()
    {
        if($this->wLine==0)
            $this->wLine=$this->w - $this->Xini - $this->rMargin;

        $this->wTextLine = $this->wLine - $this->lPadding - $this->rPadding;
    }


    function BorderTop()
    {
        $border=0;
        if($this->border==1)
            $border="TLR";
        $this->Cell($this->wLine,$this->tPadding,"",$border,0,'C',$this->fill);
        $y=$this->GetY()+$this->tPadding;
        $this->SetXY($this->Xini,$y);
    }


    function BorderBottom()
    {
        $border=0;
        if($this->border==1)
            $border="BLR";
        $this->Cell($this->wLine,$this->bPadding,"",$border,0,'C',$this->fill);
    }


    function DoStyle($tag) // Applies a style
    {
        $tag=trim($tag);
        $this->SetFont($this->TagStyle[$tag]['family'],
            $this->TagStyle[$tag]['style'],
            $this->TagStyle[$tag]['size']);

        $tab=explode(",",$this->TagStyle[$tag]['color']);
        if(count($tab)==1)
            $this->SetTextColor($tab[0]);
        else
            $this->SetTextColor($tab[0],$tab[1],$tab[2]);
    }


    function FindStyle($tag, $ind) // Inheritance from parent elements
    {
        $tag=trim($tag);

        // Family
        if($this->TagStyle[$tag]['family']!="")
            $family=$this->TagStyle[$tag]['family'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['family']!="") {
                    $family=$this->TagStyle[$val]['family'];
                    break;
                }
            }
        }

        // Style
        $style="";
        $style1=strtoupper($this->TagStyle[$tag]['style']);
        if($style1!="N")
        {
            $bold=false;
            $italic=false;
            $underline=false;
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                $style1=strtoupper($this->TagStyle[$val]['style']);
                if($style1=="N")
                    break;
                else
                {
                    if(strpos($style1,"B")!==false)
                        $bold=true;
                    if(strpos($style1,"I")!==false)
                        $italic=true;
                    if(strpos($style1,"U")!==false)
                        $underline=true;
                } 
            }
            if($bold)
                $style.="B";
            if($italic)
                $style.="I";
            if($underline)
                $style.="U";
        }

        // Size
        if($this->TagStyle[$tag]['size']!=0)
            $size=$this->TagStyle[$tag]['size'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['size']!=0) {
                    $size=$this->TagStyle[$val]['size'];
                    break;
                }
            }
        }

        // Color
        if($this->TagStyle[$tag]['color']!="")
            $color=$this->TagStyle[$tag]['color'];
        else
        {
            reset($this->PileStyle);
            while(list($k,$val)=each($this->PileStyle))
            {
                $val=trim($val);
                if($this->TagStyle[$val]['color']!="") {
                    $color=$this->TagStyle[$val]['color'];
                    break;
                }
            }
        }
         
        // Result
        $this->TagStyle[$ind]['family']=$family;
        $this->TagStyle[$ind]['style']=$style;
        $this->TagStyle[$ind]['size']=$size;
        $this->TagStyle[$ind]['color']=$color;
        $this->TagStyle[$ind]['indent']=$this->TagStyle[$tag]['indent'];
    }


    function Parser($text)
    {
        $tab=array();
        // Closing tag
        if(preg_match("|^(</([^>]+)>)|",$text,$regs)) {
            $tab[1]="c";
            $tab[2]=trim($regs[2]);
        }


        // Opening tag
        else if(preg_match("|^(<([^>]+)>)|",$text,$regs)) {
            $regs[2]=preg_replace("/^a/","a ",$regs[2]);
            $tab[1]="o";
            $tab[2]=trim($regs[2]);

            // Presence of attributes
            if(preg_match("/(.+) (.+)='(.+)'/",$regs[2])) {
                $tab1=preg_split("/ +/",$regs[2]);
                $tab[2]=trim($tab1[0]);
                while(list($i,$couple)=each($tab1))
                {
                    if($i>0) {
                        $tab2=explode("=",$couple);
                        $tab2[0]=trim($tab2[0]);
                        $tab2[1]=trim($tab2[1]);
                        $end=strlen($tab2[1])-2;
                        $tab[$tab2[0]]=substr($tab2[1],1,$end);
                    }
                }
            }
        }
         // Space
         else if(preg_match("/^( )/",$text,$regs)) {
            $tab[1]="s";
            $tab[2]=' ';
        }
        // Text
        else if(preg_match("/^([^< ]+)/",$text,$regs)) {
            $tab[1]="t";
            $tab[2]=trim($regs[1]);
        }

        

        $begin=(isset($regs[1]) ? strlen($regs[1]) : 0);
        $end=strlen($text);
        $text=substr($text, $begin, $end);
        $tab[0]=$text;

        return $tab;
    }


    function MakeLine()
    {
        $this->Text.=" ";
        $this->LineLength=array();
        $this->TagHref=array();
        $Length=0;
        $this->nbSpace=0;

        $i=$this->BeginLine();
        $this->TagName=array();

        if($i==0) {
            $Length=$this->StringLength[0];
            $this->TagName[0]=1;
            $this->TagHref[0]=$this->href;
        }

        while($Length<$this->wTextLine)
        {
            $tab=$this->Parser($this->Text);
            $this->Text=$tab[0];
            if($this->Text=="") {
                $this->LastLine=true;
                break;
            }

            if($tab[1]=="o") {
                array_unshift($this->PileStyle,$tab[2]);
                $this->FindStyle($this->PileStyle[0],$i+1);

                $this->DoStyle($i+1);
                $this->TagName[$i+1]=1;
                if($this->TagStyle[$tab[2]]['indent']!=-1) {
                    $Length+=$this->TagStyle[$tab[2]]['indent'];
                    $this->Indent=$this->TagStyle[$tab[2]]['indent'];
                }
                if($tab[2]=="a")
                    $this->href=$tab['href'];
            }

            if($tab[1]=="c") {
                array_shift($this->PileStyle);
                if(isset($this->PileStyle[0]))
                {
                    $this->FindStyle($this->PileStyle[0],$i+1);
                    $this->DoStyle($i+1);
                }
                $this->TagName[$i+1]=1;
                if($this->TagStyle[$tab[2]]['indent']!=-1) {
                    $this->LastLine=true;
                    $this->Text=trim($this->Text);
                    break;
                }
                if($tab[2]=="a")
                    $this->href="";
            }

            if($tab[1]=="s") {
                $i++;
                $Length+=$this->Space;
                $this->Line2Print[$i]="";
                if($this->href!="")
                    $this->TagHref[$i]=$this->href;
            }

            if($tab[1]=="t") {
                $i++;
                $this->StringLength[$i]=$this->GetStringWidth($tab[2]);
                $Length+=$this->StringLength[$i];
                $this->LineLength[$i]=$Length;
                $this->Line2Print[$i]=$tab[2];
                if($this->href!="")
                    $this->TagHref[$i]=$this->href;
             }

        }

        trim($this->Text);
        if($Length>$this->wTextLine || $this->LastLine==true)
            $this->EndLine();
    }


    function BeginLine()
    {
        $this->Line2Print=array();
        $this->StringLength=array();

        if(isset($this->PileStyle[0]))
        {
            $this->FindStyle($this->PileStyle[0],0);
            $this->DoStyle(0);
        }

        if(count($this->NextLineBegin)>0) {
            $this->Line2Print[0]=$this->NextLineBegin['text'];
            $this->StringLength[0]=$this->NextLineBegin['length'];
            $this->NextLineBegin=array();
            $i=0;
        }
        else {
            preg_match("/^(( *(<([^>]+)>)* *)*)(.*)/",$this->Text,$regs);
            $regs[1]=str_replace(" ", "", $regs[1]);
            $this->Text=$regs[1].$regs[5];
            $i=-1;
        }

        return $i;
    }


    function EndLine()
    {
        if(end($this->Line2Print)!="" && $this->LastLine==false) {
            $this->NextLineBegin['text']=array_pop($this->Line2Print);
            $this->NextLineBegin['length']=end($this->StringLength);
            array_pop($this->LineLength);
        }

        while(end($this->Line2Print)==="")
            array_pop($this->Line2Print);

        $this->Delta=$this->wTextLine-end($this->LineLength);

        $this->nbSpace=0;
        for($i=0; $i<count($this->Line2Print); $i++) {
            if($this->Line2Print[$i]=="")
                $this->nbSpace++;
        }
    }


    function PrintLine()
    {
        $border=0;
        if($this->border==1)
            $border="LR";
        $this->Cell($this->wLine,$this->hLine,"",$border,0,'C',$this->fill);
        $y=$this->GetY();
        $this->SetXY($this->Xini+$this->lPadding,$y);

        if($this->Indent!=-1) {
            if($this->Indent!=0)
                $this->Cell($this->Indent,$this->hLine);
            $this->Indent=-1;
        }

        $space=$this->LineAlign();
        $this->DoStyle(0);
        for($i=0; $i<count($this->Line2Print); $i++)
        {
            if(isset($this->TagName[$i]))
                $this->DoStyle($i);
            if(isset($this->TagHref[$i]))
                $href=$this->TagHref[$i];
            else
                $href='';
            if($this->Line2Print[$i]=="")
                $this->Cell($space,$this->hLine,"         ",0,0,'C',false,$href);
            else
                $this->Cell($this->StringLength[$i],$this->hLine,$this->Line2Print[$i],0,0,'C',false,$href);
        }

        $this->LineBreak();
        if($this->LastLine && $this->Text!="")
            $this->EndParagraph();
        $this->LastLine=false;
    }


    function LineAlign()
    {
        $space=$this->Space;
        if($this->align=="J") {
            if($this->nbSpace!=0)
                $space=$this->Space + ($this->Delta/$this->nbSpace);
            if($this->LastLine)
                $space=$this->Space;
        }

        if($this->align=="R")
            $this->Cell($this->Delta,$this->hLine);

        if($this->align=="C")
            $this->Cell($this->Delta/2,$this->hLine);

        return $space;
    }


    function LineBreak()
    {
        $x=$this->Xini;
        $y=$this->GetY()+$this->hLine;
        $this->SetXY($x,$y);
    }


    function EndParagraph()
    {
        $border=0;
        if($this->border==1)
            $border="LR";
        $this->Cell($this->wLine,$this->hLine/2,"",$border,0,'C',$this->fill);
        $x=$this->Xini;
        $y=$this->GetY()+$this->hLine/2;
        $this->SetXY($x,$y);
    }   
    ##### WRITETAG ####
}