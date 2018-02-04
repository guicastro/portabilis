<?php

/*#######################################################
|														|
| Arquivo com a classe de geração de arquivo Word		|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;



/*### CLASSE DE GERAÇÃO ARQUIVO WORD (HERDA CRUD) ###*/
class Word {

	protected $Filename;
	protected $Type;
	protected $Output;
	protected $Header;
	protected $Footer;
	protected $Content;
	protected $Path;
	protected $mPDFDocument;


	/*### CONSTRUTOR ###*/
	public function __construct($container) {

		//ARMAZENA AS VARIÁVEIS DOS CAMINHOS DAS PASTAS E HTTP
		$this->Path = array("AbsolutePath" => $container['AbsolutePath'],
							"RelativePath" => $container['RelativePath'],
							"HttpReferer" => $container['HttpReferer']);

	}
	/*### CONSTRUTOR ###*/





	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetFilename($Filename) {

		$this->Filename = $Filename;
		return $this->Filename;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetOutput($Output) {

		$this->Output = $Output;
		return $this->Output;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetType($Type) {

		$this->Type = $Type;
		return $this->Type;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O CABEÇALHO ###*/
	public function SetHeader($Header) {

		$this->Header = $Header;
		return $this->Header;
	}
	/*### ARMAZENA O CABEÇALHO ###*/



	/*### ARMAZENA O RODAPÉ ###*/
	public function SetFooter($Footer) {

		$this->Footer = $Footer;
		return $this->Footer;
	}
	/*### ARMAZENA O RODAPÉ ###*/



	/*### ARMAZENA O CONTEÚDO DO ARQUIVO ###*/
	public function SetContent($Content) {

		$this->Content = $Content;
		return $this->Content;
	}
	/*### ARMAZENA O CONTEÚDO DO ARQUIVO ###*/



	public function GenerateWord() {



		$PHPWord = new \PhpOffice\PhpWord\PhpWord();


		$CSSBootstrap = file_get_contents($this->Path["AbsolutePath"].'/lib/smartadmin/css/bootstrap.min.css');

		$Section = $PHPWord->addSection();

		\PhpOffice\PhpWord\Shared\Html::addHtml($Section, $html);

		/*### APAGA OS ARQUIVOS DO DIRETÓRIO TEMPORÁRIO ###*/
		// echo "<br>hash: ".$this->hash;
		$directoryIterator = new \DirectoryIterator($this->Path["AbsolutePath"]."/tmp");
		foreach($directoryIterator as $ArquivoWord)
			{
				// echo "<br><br>".$ArquivoWord->__toString();
				// echo "<br>".$ArquivoWord->getType();
				// echo "<br>".$ArquivoWord->getExtension();
				// echo "<br>".$ArquivoWord->getPathname();
				// echo "<br>".$ArquivoWord->getCTime();
				// echo "<br>".(time() - $ArquivoWord->getCTime());

				if(strtolower($ArquivoWord->getExtension())=="docx") {

					if((time() - $ArquivoWord->getCTime())>=300) @unlink($ArquivoWord->getPathname());
				}
			}
		/*### APAGA OS ARQUIVOS DO DIRETÓRIO TEMPORÁRIO ###*/


		// $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($PHPWord, 'Word2007');

		$PHPWord->save($container['AbsolutePath'].'/tmp/'.$this->Filename.".docx");

		if($this->Output<>"file") {

			header('Content-Description: File Transfer');
			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header("Content-Disposition: attachment; filename=".$this->Filename.".docx");
			readfile($container['AbsolutePath'].'/tmp/'.$this->Filename.".docx");
		}


	}



}
/*### CLASSE DE GERAÇÃO ARQUIVO WORD (HERDA CRUD) ###*/
