<?php
namespace App\Controller;
use App\Model\GroupModel as Group;
/**
* 
*/
class GroupController
{
	
	function __construct()
	{
		# code...
	}

	public function indexAction()
	{

	}

	/*

	*/
	public function getClarromAction()
	{

	}

	/*
	 *
	*/
	public function getClassRoomOptionsAction($id_group)
	{
		$group = new Group(DB);

		$classRoom = $group->getClassRoomList($id_group)['data'];

		foreach ($classRoom as $key => $student) {
			echo "<option value='".$student['idstudents']."'>".
					utf8_encode(
						$student['primer_ape_alu']." ".
						$student['segundo_ape_alu']." ".
						$student['primer_nom_alu']." ".
						$student['segundo_nom_alu']
					)
				."</option>";
		}
	}
}
?>