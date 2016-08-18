<?php 

	class InternalUser{
		
		protected $id = null;
		protected $adi = null;
		protected $soyadi = null;
		protected $kadi = null;
		protected $sifre = null;
		protected $onay = null;
		protected $bildirim = null;
		protected $sidebar = null;
		protected $yetki = null;
		protected $resim = null;
		protected $noimg = "noimg.gif";
		protected $all=null;
			
		public function login($kadi=null,$sifre=null,$cookie = false){
			
			if(trim($kadi)==null || trim($sifre)==null){
			
				Message::set("Kullanıcı Adı Veya Şifre Boş Bırakılamaz");
				return  false;
			
			}
			
			$sifre = sha1(md5($sifre));
			$login = DB::
			where("kadi=",$kadi,"and")->
			where("sifre=",$sifre,"and")->
			where("onay=",1)->
			get("kullanici");
			
			
			if($login->totalRows()>0){
				
				$kullanici = $login->row();
				
				Session::insert("Kullanici_bilgi",json_encode($kullanici));
				
				if($cookie){
					Cookie::insert("Kullanici_bilgi",json_encode(array("kadi"=>$kullanici->kadi,"sifre"=>$kullanici->sifre)),60*60*24*30);
				}
				Message::set("Giriş Başarılı");
				return true;
				
			}else{
				
				Message::set("Kullanıcı adı veya şifre hatalı");
				return false;
				
			}
			
		}
			
		public function fblogin($fbid=null){
			
			if(trim($fbid)==null){
			
				Message::set("Kullanıcı Adı Veya Şifre Boş Bırakılamaz");
				return  false;
			
			}
			
			$login = DB::
			where("fbid=",$fbid,"and")->
			where("onay=",1,"or")->
			get("kullanici");
			
			
			if($login->totalRows()>0){
				
				$kullanici = $login->row();
				
				if($kullanici->kadi==null || $kullanici->sifre==null){
					
					Session::insert("fbid",$fbid);
					redirect(baseurl("login/fbusername/"));
					
				}else{
				
					Session::insert("Kullanici_bilgi",json_encode($kullanici));
					
					Message::set("Giriş Başarılı");
					return true;
					
				}
				
			}else{
				
				Message::set("Kullanıcı adı veya şifre hatalı");
				return false;
				
			}
			
		}
		
		public function id(){
			
			return $this->get("id");
			
		}
		
		public function adi(){
			
			return $this->get("adi");
			
		}
		
		public function soyadi(){
			
			return $this->get("soyadi");
			
		}
		
		public function adisoyadi(){
			
			return $this->get("adi")." ".$this->get("soyadi");
			
		}
		
		public function email(){
			
			return $this->get("email");
			
		}
		
		public function kadi(){
			
			return $this->get("kadi");
			
		}
		
		public function onay(){
			
			return $this->get("onay");
			
		}
		
		public function bildirim(){
			
			return $this->get("bildirim");
			
		}
		
		public function sidebar(){
			
			return $this->get("sidebar");
			
		}
		
		public function yetki(){
			
			return $this->get("yetki");
			
		}
		
		public function resim(){
			
			return $this->get("resim");
			
		}
		
		public function kucukresim(){
			
			return $this->get("kucukresim");
			
		}
		
		public function ortaresim(){
			
			return $this->get("ortaresim");
			
		}
		
		public function yetkiadi(){
			
			return $this->get("yetkisi");
			
		}
		
		public function check(){
			
			return Session::select("Kullanici_bilgi");
			
		}
		
		public function active($kadi=null,$sifre=null){
			
			return DB::where("kadi=",$kadi,"and")->where("sifre=",$sifre)->update("kullanici",array("onay"=>1));
			
		}
		
		protected function get($key=null){
			
			if(Cookie::select("Kullanici_bilgi")){
				$kullanici = json_decode(Cookie::select("Kullanici_bilgi"),true);
			}else{
				$kullanici = json_decode(Session::select("Kullanici_bilgi"),true);
			}
			
			$kullanici = DB::select("kullanici.id,
			kullanici.adi,
			kullanici.soyadi,
			kullanici.tel,
			kullanici.hakkinda,
			kullanici.kadi,
			kullanici.onay,
			kullanici.cinsiyet,
			kullanici.yetki,
			(select yetkiadi from yetki where yetki.id = kullanici.yetki) as yetkisi,
			kullanici.email,
			concat(kullanici.adi,' ',kullanici.soyadi) as adisoyadi,
			if((select count(*) from dosyalar where id=kullanici.resim)>0,(select adi from dosyalar where id=kullanici.resim),'noimg.gif') as resim,
			if((select count(*) from dosyalar where id=kullanici.resim)>0,(select orta from dosyalar where id=kullanici.resim),'noimg.gif') as ortaresim,
			if((select count(*) from dosyalar where id=kullanici.resim)>0,(select kucuk from dosyalar where id=kullanici.resim),'noimg.gif') as kucukresim
			")->
			where("kullanici.kadi=",$kullanici["kadi"],"and")->
			where("kullanici.sifre=",$kullanici["sifre"],"and")->
			where("kullanici.onay=",1)->
			get("kullanici");
			
			if($kullanici->totalRows()<1){
				
				redirect(baseurl()."login/out");
				
				
			}else{
				
				$row = $kullanici->row();
				$kullanici = json_decode(json_encode($row),true);
				
			}
			
			if(User::check()){
				
				if(isset($key)){
				
					return $kullanici[$key];
					
				}else{
					
					return $row;
					
				}
				
			}else{
				
				return false;
				
			}
			
		}
		
		public function row(){
			
			return $this->get();
			
		}
		
		public function logout(){
			
			Session::deleteAll();
			Cookie::deleteAll();
			
		}
		
	}
	
?>