<?php 
class InternalWarning
{	
	public function set($message = "Mesaj Parametresi Girilmedi", $type = "info", $url = null, $uyari = true)
	{	
		$array = array("WarningMessage"=>$message,"WarningMessageType"=>$type);
		$json  = json_encode($array);
		
		if( $uyari )
		{
			Session::insert("WarningMessage",$json);
		}

		if( $url == null )
		{
			redirect($_SERVER["HTTP_REFERER"]);
		}
		else
		{
			redirect(baseurl($url));		
		}
		
	}
	
	public function get()
	{
		
		$val 		= Session::select("WarningMessage");
		$array 		= json_decode($val,true);
		$message 	= $array["WarningMessage"];
		$type 		= $array["WarningMessageType"];

		return $this->control($message,$type);
	}
	
	protected function success($message){
		
		return '<div class="alert alert-success" role="alert"><i class="fa fa-thumbs-o-up"></i> '.$message.'</div>';
		
	}
	
	protected function info($message){
		
		return '<div class="alert alert-info" role="alert">'.$message.'</div>';
		
	}
	
	protected function warning($message){
		
		return '<div class="alert alert-warning" role="alert">'.$message.'</div>';
		
	}
	
	protected function danger($message){
		
		return '<div class="alert alert-danger" role="alert">'.$message.'</div>';
		
	}
	
	protected function control($message, $type)
	{	
		if( ! ( empty($message) && empty($type) ) )
		{
			Session::delete("WarningMessage");

			if( $type=="danger" )
			{
				
				return $this->danger($message);
				
			}
			elseif( $type=="warning" )
			{
				
				return $this->warning($message);
				
			}
			elseif( $type=="success" )
			{
				
				return $this->success($message);
				
			}
			else
			{
				
				return $this->info($message);	
			}
		}

		return false;	
	}	
}