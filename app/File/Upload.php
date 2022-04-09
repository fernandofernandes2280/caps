<?php

namespace App\File;


use CoffeeCode\Uploader\Image;
use \App\Model\Entity\User as EntityUser;

class Upload{
	
	//nome do arquivo sem a extensão
	private $name;
	
	//extensão do arquivo (sem ponto)
	private $extension;
	
	//type do arquivo
	private $type;
	
	//nome temporário/ caminho temporário do arquivo 
	private $tmpName;
	
	//código de erro do upload
	private $error; 
	
	//tamanho do arquivo
	private $size;
	
	//contador de duplicacao de arquivo
	private $duplicate = 0;
	
	public function  __construct($file){
		$this->type = $file['type'];
		$this->tmpName = $file['tmp_name'];
		$this->error = $file['error'];
		$this->size = $file['size'];
		$info = pathinfo($file['name']);
		$this->name = $info['filename'];
		$this->extension = $info['extension'];
	}
	
	//método responsável por alterar o nome do arquivo
	public function setName($name){
		$this->name = $name;
		
	}
	
	//Método responsavel por gerar um novo nome aleatório
	public function generateNewName(){
		
		$this->name = time().'-'.rand(100000,999999).'-'.uniqid();
		
	}
	
	//método responsável por retornar o nome do arquivo com asua extensão
	public function getBaseName(){
		//valida extensao
		$extension = strlen($this->extension) ? '.'.$this->extension : '';
		
		//Valida duplicação
		$duplicates = $this->duplicate > 0 ? '-'.$this->duplicate : '';
		
		//retorna o nome completo
		return $this->name.$duplicates.$extension;
		
	}
	
	//Método responsávelm por obter o nome possível para o arquivo
	private function getPossibleBasename($dir,$overwrite){
		
		//Sobrescrever o arquivo
		if($overwrite) return $this->getBaseName();
		
		//Náo pode sobrescrever arquivo
		$basename = $this->getBaseName();
		
		//vericar duplicacao
		if(!file_exists($dir.'/'.$basename)){
			return $basename; 
		}
		
		//Incrementar duplicacoes
		$this->duplicate++;
		
		//Retorno o próprio método
		return $this->getPossibleBasename($dir, $overwrite);
	}
	
	//método responsável por mover o arquivo de upload
	public function upload($dir, $overwrite = true ){
		//verificar erro
		if($this->error != 0) return false;
		
		//caminho completo de destino
		$path = $dir.'/'.$this->getPossibleBaseName($dir,$overwrite);
		
	//	var_dump($path);exit;
		
		
		//move o arquivo para a pasta de destino
		return move_uploaded_file($this->tmpName, $path);

	}
	
	

	public static function setUploadImages($request){
		
		//Post Vars
		$postVars = $request->getPostVars();
		//redireciona caso seja feita busca rápida pelo prontuário
		if(@$postVars['pront']){
			if(isset($_SESSION['visitor']['usuario']['id'])){
			$request->getRouter()->redirect('/visitor/pacientes?pront='.@$postVars['pront']);
			}else{
				$request->getRouter()->redirect('/admin/pacientes?pront='.@$postVars['pront']);
			}
		}
		
		
		
		$upload = new Image(__DIR__.'/files', '/images');
		
		$files = $request->getFileVars();
		
		if(!empty($files['image'])){
			$file = $files['image'];
			
			//verifica se o arquivo existe e se o tipo é permitido
			if(empty($file['type']) || !in_array($file['type'], $upload::isAllowed())  ){
				$request->getRouter()->redirect('/admin/pacientes'); 
				
			}else{
				//faz o upload da imagem
				
				//instancia de upload
				$obUpload = new Upload($files['image']);

					//gera um nome aleatório pro arquivo
					$obUpload->generateNewName();
					
					//Move os arquivos de upload
					$sucesso = $obUpload->upload(__DIR__.'/files/images',false);
					if($sucesso){
						//verifica qual usuario está logado
						@$_SESSION['admin'] ?  $id = $_SESSION['admin']['usuario']['id'] : $id = $_SESSION['visitor']['usuario']['id'];
						//busca usuário no banco
						$obUser = EntityUser::getUserById($id);
						//caminho da imagem completo gravada no banco
						$filename = __DIR__.'/files/images/'.$obUser->foto;
						//verifica se o arquivo existe, se existir, atribui as permissoes e apaga o arquivo anterior
						if (file_exists($filename)){
							chmod($filename, 0777);
							unlink($filename);}
						//salva o nome do arquivo no banco
						$obUser->foto = $obUpload->getBaseName();
						$obUser->Atualizar();
						//redireciona de acordo com usuário logado
						@$_SESSION['admin'] ? $request->getRouter()->redirect('/admin/pacientes') : $request->getRouter()->redirect('/visitor/pacientes');
						//exit;
					}
				//	echo 'Problemas ao enviar o arquivo <br>';
					
				
			//	exit;
			//  $uploaded = $upload->upload($file, pathinfo($file['name'], PATHINFO_FILENAME),350);
			
			//  chmod(__DIR__."/File/files/images/" . $file['name'], 0777); //Corrige a permissão do arquivo.
				
			}
			//mkdir(__DIR__.'/files/teste',0777,true);
			
		//	var_dump(pathinfo($file['name']));exit;
		}
		
	}
	
	
	
	public static function setUploadArquivos($request){
		
		$fileVars = $request->getFileVars();
		
		if(isset($fileVars['arquivo'])){
			
			
			$uploads = Upload::createMultiploUpload($fileVars['arquivo']);
			
			foreach ($uploads as $obUpload){
				
				//Move os arquivos de upload
				$sucesso = $obUpload->upload(__DIR__.'/files',false);
				if($sucesso){
					echo 'Arquivo <strong>'.$obUpload->getBaseName(). '</strong> enviado com sucesso!<br>';
					continue;
				}
				echo 'Problemas ao enviar o arquivo <br>';
				
			}
			exit;
/*			
			//instancia de upload
			$obUpload = new Upload($fileVars['arquivo']);
			
			//Altera o nome do arquivo
		//	$obUpload->setName('novo-arquivo-com-nome-alterado');
			
			
			//gera um nome aleatório pro arquivo
			$obUpload->generateNewName();
			
			//Move os arquivos de upload
			$sucesso = $obUpload->upload(__DIR__.'/files',false);
			if($sucesso){
				echo 'Arquivo <strong>'.$obUpload->getBaseName(). '</strong> enviado com sucesso';
				exit;
			}
				die('Problemas ao enviar o arquivo');
			
			//var_dump($fileVars['type']);
	*/		
		}
	
	}
	
	//método responsável por criar instancias de uploads para multiplos arquivos
	public static function createMultiploUpload($files) {
		$uploads =[];
		
		foreach ($files['name'] as $key => $value){
			//array de arquivos
			$file = [
				'name' => $files['name'][$key],
				'type' => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error' => $files['error'][$key],
				'size' => $files['size'][$key],
				
			];
			
			//Nova instancia
			$uploads[] = new Upload($file);
		}
		
		return $uploads;
		
	}
	
	
	
}