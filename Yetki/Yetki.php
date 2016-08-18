<?php 

	class InternalYetki{
		
		protected $yetkiId = 0;
		protected $durum = false;
		protected	$yonlendirmelinki = "panel/error/yetki"; 
				
		public function select($alanid=null){
			
			if($alanid==null){
				
				return false;
				
			}else{
				
				return $this->verivarmi($this->dbsql($alanid,1));
				
			}
			
		}
				
		public function insert($alanid=null){
			
			if($alanid==null){
				
				return false;
				
			}else{
				
				return $this->verivarmi($this->dbsql($alanid,2));
				
			}
			
		}
				
		public function update($alanid=null){
			
			if($alanid==null){
				
				return false;
				
			}else{
				
				return $this->verivarmi($this->dbsql($alanid,3));
				
			}
			
		}
				
		public function delete($alanid=null){
			
			if($alanid==null){
				
				return false;
				
			}else{
				
				return $this->verivarmi($this->dbsql($alanid,4));
				
			}
			
		}
				
		public function other($alanid=null,$alanyetkisi=0){
			
			if($alanid==null){
				
				return false;
				
			}else{
				
				return $this->verivarmi($this->dbsql($alanid,$alanyetkisi));
				
			}
			
		}
		
		protected function yetkial(){
			
			$this->yetkiId = User::yetki();
			
		}
		
		protected function dbsql($alanid,$alanyetkisi){
			
			$this->yetkial();
			
			return DB::where("yetkiid=",$this->yetkiId,"and")->
							where("alanid=",$alanid,"and")->
							where("alanyetkiid=",$alanyetkisi)->
							get("yetkilimi");
			
		}
		
		protected function verivarmi($db){
			
			if($db->totalRows()<1){
				
				redirect($this->url());
				
			}else{
				
				return true;
				
			}
			
		}
		
		protected function url(){
			
			return baseurl($this->yonlendirmelinki);
			
		}
		
		public function check($alanid=0){
			
			$this->yetkial();
			
			if(DB::where("alanid=",$alanid,"and")->where("yetkiId=",$this->yetkiId)->get("yetkilimi")->totalRows()<1){
				
				return false;
				
			}else{
				
				return true;
				
			}
			
		}
		
		public function kontrol($alanid=0,$alanyetki=0){
			
			$this->yetkial();
			
			if(DB::where("yetkiid=",$this->yetkiId,"and")->where("alanid=",$alanid,"and")->where("alanyetkiid=",$alanyetki)->get("yetkilimi")->totalRows()<1){
				
				return false;
				
			}else{
				
				return true;
				
			}
			
		}
		
	}
	
?>