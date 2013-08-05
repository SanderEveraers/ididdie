<?php
$data_to_encode = '075960605047509113'; 
                         
// Generate Barcode data 
//$barcode = new BarcodeHelper();
$this->Ean13p5->Ean13p5(); 
$this->Ean13p5->makeImage($data_to_encode, "img/barcode/$data_to_encode.jpeg");

// Display image 
echo $this->Html->Image("/img/barcode/$data_to_encode.jpeg");
?>