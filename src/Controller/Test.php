<?php

/*#######################################################
|														|
| Arquivo espelho do Controller para testes gerais		|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/


header('Content-Type: text/html; charset=utf-8');


//CARREGA O ARQUIVO DE AUTOLOAD DE TODAS AS CLASSES (COMPOSER)
require_once("../../vendor/autoload.php");

//CARREGA O ARQUIVO DE CONFIGURAÇÃO GERAL
require_once("../../config/config.php");

//CARREGA O SERVIÇO DO PIMPLE
require_once("../Service/Service.php");


$telefone = "+55-- (sds";

echo substr($telefone,0,3);
echo "<br>";
echo substr($telefone,3);

// $MaskValue = $container['MaskValue'];

// var_dump($MaskValue->ValidarCPF('00706944194','validate'));

// // Creating the new document...
// $phpWord = new \PhpOffice\PhpWord\PhpWord();

// /* Note: any element you append to a document must reside inside of a Section. */

// // Adding an empty Section to the document...
// $section = $phpWord->addSection();
// // Adding Text element to the Section having font styled by default...
// $section->addText(
//     '"Learn from yesterday, live for today, hope for tomorrow. '
//         . 'The important thing is not to stop questioning." '
//         . '(Albert Einstein)'
// );

// /*
//  * Note: it's possible to customize font style of the Text element you add in three ways:
//  * - inline;
//  * - using named font style (new font style object will be implicitly created);
//  * - using explicitly created font style object.
//  */

// // Adding Text element with font customized inline...
// $section->addText(
//     '"Great achievement is usually born of great sacrifice, '
//         . 'and is never the result of selfishness." '
//         . '(Napoleon Hill)',
//     array('name' => 'Tahoma', 'size' => 10)
// );

// // Adding Text element with font customized using named font style...
// $fontStyleName = 'oneUserDefinedStyle';
// $phpWord->addFontStyle(
//     $fontStyleName,
//     array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
// );
// $section->addText(
//     '"The greatest accomplishment is not in never falling, '
//         . 'but in rising again after you fall." '
//         . '(Vince Lombardi)',
//     $fontStyleName
// );

// // Adding Text element with font customized using explicitly created font style object...
// $fontStyle = new \PhpOffice\PhpWord\Style\Font();
// $fontStyle->setBold(true);
// $fontStyle->setName('Tahoma');
// $fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
// $myTextElement->setFontStyle($fontStyle);

// // Saving the document as OOXML file...
// $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');


// $Filename = 'helloWorld.docx';
// $Filepath = $container['AbsolutePath'].'/tmp/'.$Filename;

// $objWriter->save($Filepath);

// header("Content-Disposition: attachment; filename=".$Filename);
// readfile($Filepath); // or echo file_get_contents($temp_file);
// unlink($Filepath);


/* Note: we skip RTF, because it's not XML-based and requires a different example. */
/* Note: we skip PDF, because "HTML-to-PDF" approach is used to create PDF documents. */
?>