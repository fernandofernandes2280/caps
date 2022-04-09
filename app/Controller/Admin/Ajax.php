<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class Ajax extends Page {
	
	public static function create($request):void
	{
		//$callback["data"]=$data;
		$postVars = $request->getPostVars();
		$id2 = ($postVars["id"]);
		//echo json_encode($id);
		echo ($id2);
	}
	
	
}