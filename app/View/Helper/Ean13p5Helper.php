<?php  
/** 
* Ean13p5 Helper class file. 
* 
* Simplifies creating charts with the google charts api. 
* 
* Copyright (c) 2013 Ivo Filot
* 
* Licensed under The MIT License 
* Redistributions of files must retain the above copyright notice. 
* 
* @filesource 
* @copyright     Copyright (c) 2013 Ivo Filot
* @link             http://net.productions.free.fr 
* @license       http://www.opensource.org/licenses/mit-license.php The MIT License 
*/ 

App::uses('AppHelper', 'View/Helper');

class Ean13p5Helper extends AppHelper {

    var $codestring;
    var $startchar;
    var $addoncheck;

    public function Ean13p5() { //default constructor
        $this->codestring[0]['L']['O'] = '0001101';
        $this->codestring[1]['L']['O'] = '0011001';
        $this->codestring[2]['L']['O'] = '0010011';
        $this->codestring[3]['L']['O'] = '0111101';
        $this->codestring[4]['L']['O'] = '0100011';
        $this->codestring[5]['L']['O'] = '0110001';
        $this->codestring[6]['L']['O'] = '0101111';
        $this->codestring[7]['L']['O'] = '0111011';
        $this->codestring[8]['L']['O'] = '0110111';
        $this->codestring[9]['L']['O'] = '0001011';
        $this->codestring[0]['R']['O'] = '1110010';
        $this->codestring[1]['R']['O'] = '1100110';
        $this->codestring[2]['R']['O'] = '1101100';
        $this->codestring[3]['R']['O'] = '1000010';
        $this->codestring[4]['R']['O'] = '1011100';
        $this->codestring[5]['R']['O'] = '1001110';
        $this->codestring[6]['R']['O'] = '1010000';
        $this->codestring[7]['R']['O'] = '1000100';
        $this->codestring[8]['R']['O'] = '1001000';
        $this->codestring[9]['R']['O'] = '1110100';
        $this->codestring[0]['L']['E'] = '0100111';
        $this->codestring[1]['L']['E'] = '0110011';
        $this->codestring[2]['L']['E'] = '0011011';
        $this->codestring[3]['L']['E'] = '0100001';
        $this->codestring[4]['L']['E'] = '0011101';
        $this->codestring[5]['L']['E'] = '0111001';
        $this->codestring[6]['L']['E'] = '0000101';
        $this->codestring[7]['L']['E'] = '0010001';
        $this->codestring[8]['L']['E'] = '0001001';
        $this->codestring[9]['L']['E'] = '0010111';

        $this->startchar[0] = 'OOOOOO';
        $this->startchar[1] = 'OOEOEE';
        $this->startchar[2] = 'OOEEOE';
        $this->startchar[3] = 'OOEEEO';
        $this->startchar[4] = 'OEOOEE';
        $this->startchar[5] = 'OEEOOE';
        $this->startchar[6] = 'OEEEOO';
        $this->startchar[7] = 'OEOEOE';
        $this->startchar[8] = 'OEOEEO';
        $this->startchar[9] = 'OEEOEO';

        $this->addoncheck[0] = 'EEOOO';
        $this->addoncheck[1] = 'EOEOO';
        $this->addoncheck[2] = 'EOOEO';
        $this->addoncheck[3] = 'EOOOE';
        $this->addoncheck[4] = 'OEEOO';
        $this->addoncheck[5] = 'OOEEO';
        $this->addoncheck[6] = 'OOOEE';
        $this->addoncheck[7] = 'OEOEO';
        $this->addoncheck[8] = 'OEOOE';
        $this->addoncheck[9] = 'OOEOE';
    }

    /**
     * [string_to_binary description]
     * @param  String $string Barcode
     * @return Array         Binary representation of barcode
     */
    private function string_to_binary($string) {
        $left = '';         // left hand side of the barcode
        $right = '';        // right hand side of the barcode
        $checksum = '';     // checksum
        $addonstring = '';  // possible addition to barcode

        for($n=1;$n<=13;$n++) {
            $digit[$n]=substr($string,$n-1,1);
        }

        // generate oddevenstring
        $oddevenstring=$this->startchar[$digit[1]];
        for($n=1;$n<=6;$n++) {
            $oddeven[$n]=substr($oddevenstring,$n-1,1);
        }
        // left hand side
        for($n=2;$n<=7;$n++) {
            $o=$n-1;
            $val = $this->codestring[$digit[$n]]['L'][$oddeven[$o]];
            $left.=$val;
        }
        // right hand side
        for($n=8;$n<=13;$n++) {
            $val = $this->codestring[$digit[$n]]['R']['O'];
            $right.=$val;
        }

        //checksum berekening voor de addon

        for($n=14;$n<=18;$n++) {       
            if($n%2==0) {
                $checksum+=3*substr($string,$n-1,1);
            } else {
                $checksum+=9*substr($string,$n-1,1);
            }
            
            $addon[$n-13]=substr($string,$n-1,1);
        }

        $checksum=$checksum%10;
        $oddevenstringaddon=$this->addoncheck[$checksum];

        for($n=1;$n<=5;$n++) {
            $oddevenaddon[$n]=substr($oddevenstringaddon,$n-1,1);
        }


        $addonstring.="1011";

        for($n=1;$n<=5;$n++) {
            $val = $this->codestring[$addon[$n]]['L'][$oddevenaddon[$n]];
            $addonstring.=$val;

            if($n!=5) {
            $addonstring.="01";
            }   
        }   

        $vals = array(
            'left' => $left,
            'right' => $right,
            'addon' => $addonstring
        );

        return $vals;
    }

    /**
     * [makeImage description]
     * @param  String $code Binary representation of barcode
     * @param  String $link Path to image file
     * @return void
     */
    public function makeImage($code, $link) {

        // stop this function if an image file containing this barcode already exists
        if(file_exists($link)) {
            return;
        }

        $vals = $this->string_to_binary($code);
        $L = $vals['left'];
        $R = $vals['right'];
        $addon = $vals['addon'];

        $lijnbreedte=8;
        $lijnhoogte=$lijnbreedte*30;
        $links=$lijnhoogte/4;
        $boven=$lijnhoogte/8;
        $rechtsboven=$links/2;
        $verhouding=1.1;
        $seperatie=$links/2;
        $hoogteverschil=0.15*$lijnhoogte;
        $breedte=$links+95*($lijnbreedte-1)+(95)-4+$rechtsboven+$seperatie+47*($lijnbreedte-1)+120;
        $hoogte=$boven+$lijnhoogte*$verhouding+$rechtsboven;

        $tx=500;

        $image=imagecreatetruecolor($breedte,$hoogte);

        $zwart=imagecolorallocate($image,0,0,0);
        $wit=imagecolorallocate($image,255,255,255);

        imagefilledrectangle($image,0,0,$breedte-1,$hoogte-1,$wit);

        imagefilledrectangle($image,$links,$boven,$links+$lijnbreedte-1,$boven+$lijnhoogte*$verhouding,$zwart);
        imagefilledrectangle($image,$links+2*$lijnbreedte-1,$boven,$links+3*$lijnbreedte-2,$boven+$lijnhoogte*$verhouding,$zwart);

        for($n=1;$n<=49;$n++) {
            if(substr($L,$n-1,1)=="1") {
                $x1=$links+($n+3)*($lijnbreedte-1)+($n-1)-1;
                $x2=$links+($n+4)*($lijnbreedte-1)+($n-1)-1;
                imagefilledrectangle($image,$x1,$boven,$x2,$boven+$lijnhoogte,$zwart);
            }
        }

        $n=44;
        imagefilledrectangle($image,$links+($n+3)*($lijnbreedte-1)+($n-1)-1,$boven,$links+($n+4)*($lijnbreedte-1)+($n-1)-1,$boven+$lijnhoogte*$verhouding,$zwart);
        $n=46;
        imagefilledrectangle($image,$links+($n+3)*($lijnbreedte-1)+($n-1)-1,$boven,$links+($n+4)*($lijnbreedte-1)+($n-1)-1,$boven+$lijnhoogte*$verhouding,$zwart);

        for($n=48;$n<=97;$n++) {
            $o=$n-47;
            if(substr($R,$o-1,1)=="1") {
                $x1=$links+($n+3)*($lijnbreedte-1)+($n-1)-1;
                $x2=$links+($n+4)*($lijnbreedte-1)+($n-1)-1;
                imagefilledrectangle($image,$x1,$boven,$x2,$boven+$lijnhoogte,$zwart);
            }
        }

        $n=90;
        imagefilledrectangle($image,$links+($n+3)*($lijnbreedte-1)+($n-1)-1,$boven,$links+($n+4)*($lijnbreedte-1)+($n-1)-1,$boven+$lijnhoogte*$verhouding,$zwart);
        $n=92;
        imagefilledrectangle($image,$links+($n+3)*($lijnbreedte-1)+($n-1)-1,$boven,$links+($n+4)*($lijnbreedte-1)+($n-1)-1,$boven+$lijnhoogte*$verhouding,$zwart);

        $eindx=$links+($n+4)*($lijnbreedte-1)+($n-1)-1;
        $beginx=$eindx+$seperatie;

        for($n=1;$n<=47;$n++) {
            if(substr($addon,$n-1,1)=="1") {
                $x1=$beginx+($n+3)*($lijnbreedte-1)+($n-1)-1;
                $x2=$beginx+($n+4)*($lijnbreedte-1)+($n-1)-1;
                imagefilledrectangle($image,$x1,$boven+$hoogteverschil,$x2,$boven+$lijnhoogte,$zwart);
            }
        }

        //getallen
        $fontgrootte=$lijnbreedte*4;

        //leading digit
            $o=1;
            $n=($o-2)*7-6;
            $x1=$links+($n+3)*($lijnbreedte-1)+($n-1)+40;
            imagettftext($image,$fontgrootte,0,$x1,$boven+$lijnhoogte+$fontgrootte*1.2,$zwart,WWW_ROOT.DS."font".DS."verdana.ttf",substr($code,$o-1,1));

        for($o=2;$o<=7;$o++) {
            $n=($o-2)*7+2;
            $x1=$links+($n+3)*($lijnbreedte-1)+($n-1)-1;
            imagettftext($image,$fontgrootte,0,$x1,$boven+$lijnhoogte+$fontgrootte*1.2,$zwart,WWW_ROOT.DS."font".DS."verdana.ttf",substr($code,$o-1,1));
        }

        for($o=8;$o<=13;$o++) {
            $n=($o-2)*7+6;
            $x1=$links+($n+3)*($lijnbreedte-1)+($n-1)-1;
            imagettftext($image,$fontgrootte,0,$x1,$boven+$lijnhoogte+$fontgrootte*1.2,$zwart,WWW_ROOT.DS."font".DS."verdana.ttf",substr($code,$o-1,1));
        }

        for($o=14;$o<=18;$o++) {
            $n=($o-13)*9-7;
            $x1=$beginx+($n+3)*($lijnbreedte-1)+($n-1)-1;
            imagettftext($image,$fontgrootte,0,$x1,$boven+$fontgrootte*1.2-10,$zwart,WWW_ROOT.DS."font".DS."verdana.ttf",substr($code,$o-1,1));
        }

        $ty=$hoogte/$breedte*$tx;
        $target=imagecreatetruecolor($tx,$ty);
        imagecopyresampled($target,$image,0,0,0,0,$tx,$ty,$breedte,$hoogte);
        imagejpeg($target,$link,100);
    }

}
?>