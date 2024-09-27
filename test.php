<?php

use mikehaertl\pdftk\Pdf;

#$MYLIBDIR="/var/www/html/drupal/web/modules/custom/digitalia_muni_front_matter";
$MYLIBDIR='.';

require_once("$MYLIBDIR/custom_libs/tfpdf/tfpdf.php");
require_once("$MYLIBDIR/vendor/autoload.php");

  $input_pdf = "${MYLIBDIR}/122929.pdf";
  $originalpdf = new mikehaertl\pdftk\Pdf($input_pdf);
  $data = $originalpdf->getData();
  if (! $data) { echo "Error: '" .  $originalpdf->getError() . "'\n"; }
  #$dims = explode(" ", $data['PageMedia'][0]['Dimensions']);
  #$origsizeX = $dims[0];
  #$origsizeY = $dims[1];
	#echo "$origsizeX x $origsizeY\n";

	var_dump($data);

	exit(0);
  // set page margin size dynamically (smaller pages => smaller margin)
  $marginssize = $origsizeX*0.1;
  // start to prepare cover.pdf
  $pdf = new tFPDF("P", 'pt', array($origsizeX, $origsizeY));
  $pdf->AddFont('Myfont', '', "candara.ttf", true);
  $pdf->AddFont('MyfontB', '', "candarab.ttf", true);
  $pdf->AddFont('MyfontI', '', "candarai.ttf", true);
  $pdf->setRightMargin($marginssize);
  $pdf->setLeftMargin($marginssize);
  $pdf->AddPage();
  // set space between authors and title and title and citation
  $biggerspace = 30;
  if ($origsizeY < 570) {
    // if page is smaller than 570pt it's safer to use small space on top
	  $pdf->Ln(5);

	  if ($origsizeY < 450) {
	  	$pdf->Ln(0);
		$biggerspace = 13;
	  }

  }
  else {
    $pdf->Ln(round($origsizeY*0.1));
  }

  // just for debug
  //$pdf->SetFont('Myfont', '', $fontsize);
  //$pdf->Write($linesize, "DEBUG: $type: '$citation_book_authors'");
  //$pdf->Ln(10);
  
  // authors
  $pdf->SetFont('Myfont', '', $fontsize);
  $authorsprint = "";
  foreach($authors as $author) {
    $authorsprint .= $author . "; ";
  }
  $authorsprint = substr($authorsprint, 0, -2);
  $pdf->Write($linesize, $authorsprint);
  $pdf->Ln($linesize+10);
  // editors
  $pdf->SetFont('Myfont', '', $fontsize);
  $editorsprint = "";
  foreach($editors as $editor) {
    $editorsprint .= $editor . " (editor); ";
  }
  $editorsprint = substr($editorsprint, 0, -2);
  $pdf->Write($linesize, $editorsprint);
  $pdf->Ln($linesize+$biggerspace);
  // title
  $pdf->SetFont('MyfontB', '', $fontsize);
  $pdf->Write($linesize, $title);
    
  $pdf->Ln($linesize+$biggerspace);
  // citation
  //if ($isMonograph || $isChapter) {
  if (isset($citation_book_authors) && $citation_book_authors != '') {
    $pdf->SetFont('Myfont', '', $fontsize);
    //$pdf->Write($linesize, "In: ");
    $pdf->Write($linesize, $citation_book_authors);
  }
  $pdf->SetFont('MyfontI', '', $fontsize);
  $pdf->Write($linesize, $citation_title . " ");
  if ($isMonograph && count($editors) > 0) {
    $pdf->SetFont('Myfont', '', $fontsize);
    $pdf->Write($linesize, $editorsprint . ". ");
  }
  $pdf->SetFont('Myfont', '', $fontsize);
  $pdf->Write($linesize, $citation_rest);
  $pdf->Ln($linesize+10);
  // ISBN, ISSN
  $pdf->SetFont('Myfont', '', $fontsize);
  if (strlen($isbn) > 0) { $pdf->Write($linesize, "$isbn"); }
  $pdf->Ln($linesize);
  if (strlen($issn) > 0) { $pdf->Write($linesize, "$issn"); }
  $pdf->Ln($linesize+$biggerspace+10);
  // identifiers
  if (strlen($doi) > 0) {
    $pdf->Write($linesize, "Stable URL (DOI): ");
    $pdf->SetFont('Myfont', 'U', $fontsize);
    $pdf->Write($linesize, $doi_complete, $doi_complete);
    $pdf->SetFont('Myfont', '', $fontsize);
    $pdf->Ln($linesize);
  }     
  $pdf->Write($linesize, "Stable URL (handle): ");
  $pdf->SetFont('Myfont', 'U', $fontsize);
  $pdf->Write($linesize, $handle_complete, $handle_complete);
  $pdf->SetFont('Myfont', '', $fontsize);
  $pdf->Ln($linesize);
  // license
  if (strlen($license) > 0) {
    $pdf->Write($linesize, "License: ");
    $pdf->SetFont('Myfont', 'U', $fontsize);
    $pdf->Write($linesize, $license, $license_link);
    $pdf->SetFont('Myfont', '', $fontsize);
    $pdf->Ln($linesize);
  }

  $version = date("d. m. Y");
  if (strlen($version) > 0) {
    $pdf->SetFont('Myfont', '', $fontsize);
    $pdf->Write($linesize, "Access Date: $version");
    $pdf->Ln($linesize);
  }

  $pdf->Write($linesize, "Version: " . $file_created);

  

  // footer
  // if page is smaller than 440 pt the Terms of use text is 3-lines and we must push it higher
  $footerY = -130;
  if ($origsizeX < 440) { 
    $footerY = -150; 
    if ($origsizeX < 340) {
      $footerY = -160;
    }
  }
  $pdf->SetY($footerY);
  $pdf->SetFont('Myfont', '', 10);
  $pdf->Write(14, "Terms of use: Digital Library of the Faculty of Arts, Masaryk University provides access to digitized documents strictly for personal use, unless otherwise specified.");
  $pdf->Ln(20);
  $pdf->SetFillColor(0,0,0);
  $pdf->Cell(0,0.3,"",0,1,'C',true);
  $actualY = $pdf->GetY();
  $pdf->setY($actualY+10);
  $pdf->setX($marginssize+5);
  $pdf->Image("/var/www/html/drupal/web/modules/custom/digitalia_muni_front_matter/custom_libs/images/logo.png", null, null, 100);
  $footer_font = 8;
  $pdf->SetFont('Myfont', '', $footer_font);
  $pdf->setY($actualY+10);
  $pdf->setX(round($origsizeX/2));
  if ($origsizeX < 490) {
    if ($origsizeX < 380) {
      $footer_font = 7;
      $pdf->SetFont('Myfont', '', $footer_font);
    }
    // if page is not wide enough we must split the "Digital Library of..." text into two lines
    $pdf->write($footer_font, "Digital Library of the Faculty of Arts,");
    $pdf->Ln($footer_font+1);
    $pdf->setX(round($origsizeX/2));
    $pdf->write($footer_font, "Masaryk University");
  }
  else {
    $pdf->write($footer_font, "Digital Library of the Faculty of Arts, Masaryk University");
  }
  $pdf->setX(round($origsizeX/2));
  $pdf->Ln($footer_font+4);
  $pdf->setX(round($origsizeX/2));
  $pdf->write($footer_font, "digilib.phil.muni.cz");
   
  $pdf->Output("F", $cover_pdf);
  $joinpdf = new mikehaertl\pdftk\Pdf([$cover_pdf, $input_pdf]);
  $file_altered = $joinpdf->cat()->toString();
  
  //save to public files
  $location = '/tmp/'.$file_name;
  $res = file_put_contents($location, $file_altered);
  unlink($input_pdf);
  unlink($cover_pdf);
  return $location;
