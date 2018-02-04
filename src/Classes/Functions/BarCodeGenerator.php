<?php

/*#######################################################
|														|
| Arquivo com a classe que cria um Código de Barra  	|
| e retorna como imagem ou objeto						|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;


/*### CLASSE QUE GERA O CÓDIGO DE BARRAS ###*/
class BarcodeGenerator {

	protected $Type;
	protected $Value;
	protected $Return;
	protected $BarcodeClass;
	protected $Barcode;

	/*### CONSTRUTOR ###*/
	public function __construct() {

		// $this->$BarcodeClass = new \Zend\Barcode\Barcode\Barcode();
	}
	/*### CONSTRUTOR ###*/


	/*### ARMAZENA O TIPO DE CÓDIGO DE BARRAS ###*/
	public function setType($Type) {

		$this->Type = $Type;
	}
	/*### ARMAZENA O TIPO DE CÓDIGO DE BARRAS ###*/


	/*### ARMAZENA O VALOR QUE SERÁ GERADO NO CÓDIGO DE BARRAS ###*/
	public function setValue($Value) {

		$this->Value = $Value;
	}
	/*### ARMAZENA O VALOR QUE SERÁ GERADO NO CÓDIGO DE BARRAS ###*/


	/*### ARMAZENA O FORMATO DE RETORNO DA CLASSE (OBJ OU IMG) ###*/
	public function setReturn($Return) {

		$this->Return = $Return;
	}
	/*### ARMAZENA O FORMATO DE RETORNO DA CLASSE (OBJ OU IMG) ###*/


	/*### GERA O CÓDIGO EM BARRAS ###*/
	public function GenerateBarcode() {

		// Only the text to draw is required
		$barcodeOptions = array('text' => $this->Value);

		// No required options
		$rendererOptions = array();
		$renderer = \Zend\Barcode\Barcode::factory(
		    $this->Type, 'image', $barcodeOptions, $rendererOptions
		);

		// $renderer->render();

		$this->Barcode = $renderer;
	}
	/*### GERA O CÓDIGO EM BARRAS ###*/


	/*### RETORNA O CÓDIGO DE BARRAS ###*/
	public function ReturnBarcode() {

		switch ($this->Return) {
			case 'image':
					return $this->Barcode->render();
				break;
			case 'object':
					echo json_encode($this->Barcode);
				break;
		}
	}
	/*### RETORNA O CÓDIGO DE BARRAS ###*/



}
/*### CLASSE QUE GERA O CÓDIGO DE BARRAS ###*/
