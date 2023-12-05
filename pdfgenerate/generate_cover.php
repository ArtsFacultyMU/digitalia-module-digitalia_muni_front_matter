<?php
use setasign\Fpdi\Tfpdf\Fpdi;

require_once('tfpdf/tfpdf.php');
require_once('FPDI/src/autoload.php');

define("_SYSTEM_TTFONTS", "/usr/share/fonts/corefonts/");

$type = 1; // 1 - article, 2 - chapter
$title = "Nějaký opravdu hodně dlouhý název článku/kapitoly, abychom pořádně viděli, jak to vypadá, když se to zalamuje na více řádků";
$authors = array("Adler, Jan", "Krejčíř, Vlastimil", "Strakošová, Alžbeta");
$url = "https://digilib.phil.muni.cz/...";
$handle = "https://hdl.handle.net/...";
$license = "CC BY 4.0";
$footer = "Copyright MU 2022";
$citation_title = "Skvělý časopis";
$citation_rest = ", Vol. 22 (1993), No. 2, 123--135"; 

$input_pdf = "B_Philosophica_01-1953-1_5.pdf";


$originalpdf = new Fpdi();
$originalpdf->setSourceFile($input_pdf);
$tplIdx = $originalpdf->importPage(1);
$origsize = $originalpdf->getImportedPageSize($tplIdx);


print_r($origsize);


// Instanciation of inherited class
#$pdf = new tFPDF($origsize['orientation'], 'mm', array($origsize['width'], $origsize['height']));
$pdf = new tFPDF();
$pdf->AddFont('Arial', '', 'arial.ttf', true);
$pdf->AddFont('ArialB', '', 'arialbd.ttf', true);
$pdf->AddFont('ArialI', '', 'ariali.ttf', true);
$pdf->AddFont('CourierB', '', 'courbd.ttf', true);
$pdf->AddPage();

// Logo
$pdf->Image('logo.png',10,10,35);
$pdf->SetLineWidth(0.2);
$pdf->Line(10, 30, 200, 30);
$pdf->Ln(25);

// title
$pdf->SetFont('ArialB', '', 20);
$pdf->Write(10, $title);
$pdf->Ln(15);

// authors
$pdf->SetFont('Arial', '', 18); 
foreach($authors as $author) {
	$pdf->Write(10, $author);
	$pdf->Ln(10);
}

// citation
$pdf->Ln(15);
$pdf->SetFont('ArialI', '', 15);
$pdf->Write(10, $citation_title);
$pdf->SetFont('Arial', '', 15);
$pdf->Write(10, $citation_rest);
$pdf->Ln(10);




$pdf->SetY(-55);

// links 
$pdf->SetFont('Arial', '', 15);
$pdf->Write(10, 'URL: ');
$pdf->SetFont('CourierB', '', 15);
$pdf->Write(10, $url);
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 18);
$pdf->Write(10, 'Handle: ');
$pdf->SetFont('CourierB', '', 15);
$pdf->Write(10, $handle);
$pdf->Ln(10);

// licence
$pdf->SetFont('Arial', '', 15);
$pdf->Write(10, 'License: ');
$pdf->Write(10, $license);

$pdf->Output("F", "cover.pdf");
  

$files = array("cover.pdf", $input_pdf);
$pdfi = new Fpdi();
foreach($files as $file) {
	$pageCount = $pdfi->setSourceFile($file);
	for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
		$pageId = $pdfi->ImportPage($pageNo);
		$s = $pdfi->getTemplatesize($pageId);
		$pdfi->AddPage($s['orientation'], $s);
    $pdfi->useImportedPage($pageId);
	}
}

$pdfi->Output('F', "concatenated.pdf"); 

?>
