<?

/* HOME */
class home
{
	/* __construct: optional, called at init */
	public function __construct()
	{
	}
	
	/* _default: required, default action if no subaction is specified */
	public function _default()
	{
		global $framework;
		
		$model = Array("view" => "home_homepage");
		
		$model['rs_recent'] = $framework->db->threads->find()->sort(array('updated' => -1))->limit(5);
		$model['rs_new'] = $framework->db->threads->find()->sort(array('started' => -1))->limit(2);
		$model['rs_new_img'] = $framework->db->images->find()->sort(array('uploaded' => -1))->limit(5);
		
		return $model;
	}
	
}

?>
