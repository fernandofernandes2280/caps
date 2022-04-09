<?php

namespace App\Controller\Admin;

use App\Utils\View;

class Alert{
	
	//Método responsavel por retornar uma mensagem de erro
	public static function getError($message){
		return View::render('admin/alert/status',[
				'tipo' => 'danger',
				'mensagem' => $message
		]);
	}
	
	//Método responsavel por retornar uma mensagem de sucesso
	public static function getSuccess($message){
		return View::render('admin/alert/status',[
				'tipo' => 'success',
				'mensagem' => $message
		]);
	}
	
	

	
}