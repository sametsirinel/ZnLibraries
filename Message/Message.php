<?php 

	class InternalMessage{
		
		public function set($val=null){
			
			if($val==null)
			
				Session::insert("HataMesaji","Herhangi Bir Hata Mesajı Yok ! .");
			
			else
				
				Session::insert("HataMesaji",$val);
			
			return $this->get();
			
		}
		
		public function get(){
			
			return Session::select("HataMesaji");
			
		}
		
		public function destroy(){
			
			Session::delete("HataMesaji");
			
		}
		
	}
	
?>