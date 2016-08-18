<?php 

	class InternalDosya{
		
		public $encode = false;
		
		public $extensions = 'jpg|jpeg|png|gif';
		
		public $maxsize = 33554432; // 32 Mb a kadar izin var
		
		public $info = array();
		
		public $path = null;
		
		public $resimadi = null;
				
		public $ortaadi = null;
				
		public $kucukadi = null;
				
		public $ortaWidth = 640;
		
		public $ortaHeight = 640;
		
		public $kucukWidth = 320;
		
		public $kucukHeight = 320;
		
				
		public function profilimg($alanid=null){
			
			$ayarlar = array(
			
				'encode' => $this->encode, 
				'prefix' => time()."_".rand(0,99)."_",  
				'extensions' => "jpg|jpeg|png|gif",  
				'maxsize' => 1024*1024*10
				
			);
			
			if(Upload::settings($ayarlar)->start("file")){
				
				Message::set(1);
				
			}else{
				
				Message::set(0);
				
			}
						
			$this->info = Upload::info();
			$this->path = $this->info->path;
			$this->resimadi =  $this->info->encodeName;
			$uzanti = substr($this->info->encodeName,-4);
			if($uzanti==".jpg" || $uzanti =="jpeg" || $uzanti == ".png" || $uzanti == ".gif"){
				
				$this->ortaadi =  $this->boyutlandir(160,160);
				$this->kucukadi =  $this->boyutlandir(50,50);
				
			}else{

				Message::set("0");
			
			}
			
			$this->insert();
			
			return DB::insertId();
			
		}
				
		public function upload($alanid=null){
			
			$ayarlar = array(
			
				'encode' => $this->encode, 
				'prefix' => time()."_",  
				'extensions' => $this->extensions,  
				'maxsize' => $this->maxsize
				
			);
			
			Upload::settings($ayarlar)->start("file");
			
			$this->info = Upload::info();
			$this->path = $this->info->path;
			$this->resimadi =  $this->info->encodeName;
			$this->ortaadi =  $this->boyutlandir($this->ortaWidth,$this->ortaHeight);
			$this->kucukadi =  $this->boyutlandir($this->kucukWidth,$this->kucukHeight);
			if($this->insert())
				echo "Başarılı";
			else
				echo "Başarısız";
			
			return DB::insertId();
			
		}
		
		public function base64($file){
			
			$filename = time()."_".rand(0,999).".jpg";
			
			if($this->base64_to_jpeg($file,"Application/Resources/Uploads/$filename")){
				
				$this->resimadi = $filename;
				$this->ortaadi =  $this->boyutlandir($this->ortaWidth,$this->ortaHeight);
				$this->kucukadi =  $this->boyutlandir($this->kucukWidth,$this->kucukHeight);
				
				if($this->insert()){
					
					return DB::insertId();
					
				}else{
					
					return false;
					
				}
				
			}else{
				
				return false;
				
			}
			
		}
		
		protected function base64_to_jpeg( $base64_string, $output_file ) {
			
			$ifp = fopen( $output_file, "wb" ); 
			
			if(fwrite( $ifp, base64_decode($base64_string))){
				
				fclose( $ifp ); 
				return true;
				
			}else{
				
				fclose( $ifp ); 
				return false;
				
			}
			
			
		}
		
		public function getResim($url,$resimadi){
				
				$resimyolu =  UPLOADS_DIR.$resimadi;
				
				copy($url,$resimyolu);
				
				$this->path = UPLOADS_DIR.$resimadi;
				$this->resimadi =  $resimadi;
				$this->encodeName =  $resimadi;
				$this->ortaadi =  $this->boyutlandir(160,160);
				$this->kucukadi =  $this->boyutlandir(50,50);
				
				$this->insert();
				
				return DB::insertId();
			
		}
		
		protected function boyutlandir($width=160,$height=90){
			
			$ayarlar = array(
		
				'rewidth' => $width,
				'reheight'=>$height
						
			);
			
        	$ortanca = Image::thumb(UPLOADS_DIR.$this->resimadi, $ayarlar);
			
			$parcala = explode("/",$ortanca);
			$uzanti = substr($this->resimadi,-4);
			if($uzanti==".jpg" || $uzanti =="jpeg" || $uzanti == ".png" || $uzanti == ".gif"){
				
				return "thumbs/".$parcala[count($parcala)-1];
				
			}else{
				return $parcala[count($parcala)-1];
			}
			
		}
		
		protected function insert(){
			
			return Db::insert("dosyalar",array("adi"=>$this->resimadi,"orta"=>$this->ortaadi,"kucuk"=>$this->kucukadi));
			
		}
		
		public function modal(){
			
			echo '<div class="modal inmodal fade" id="ResimSec" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="tabs-container">
									<div class="modal-header" style="padding-bottom:0px;">
										<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Kapat</span></button>
										<h4 class="modal-title">Resim Seçme Ve Yükleme Alanı</h4>
										<small class="font-bold">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</small>
										
										<ul class="nav nav-tabs" style="margin-top:40px;">
											<li class="active"><a data-toggle="tab" href="#tab-1">Resim Seç</a></li>
											<li class=""><a data-toggle="tab" href="#tab-2">Resim Yükle</a></li>
										</ul>
									</div>
										<div class="tab-content">
											<div id="tab-1" class="tab-pane active">
												<div class="modal-body">
													<div >
														<input type="text" class="form-control" onkeyup="searchImg()" placeholder="Resmi İsminden Aramak İçin Burayı Kullanabilirsiniz" name="ImgSearch"/>
													</div>
													<div id="lastItem">
													
													</div>
												</div>
											</div>
											<div id="tab-2" class="tab-pane">
												<div class="modal-body">
													<form id="test-drop-zone" class="dropzone dz-clickable" action="'.baseurl("panel/pdosyalar/doInsert").'">
														
													</form>
													<form id="test-drop-zone" class="dropzone dz-clickable" action="'.baseurl("panel/pdosyalar/doInsert").'">
														<div class="dropzone-previews"></div>
														<div class="dz-default dz-message"><span>Resmi Seçmek İçin Tıklayın</span></div>
													</form>
												</div>
											</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-white" data-dismiss="modal">Kapat</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="ImgToggle()">Save changes</button>
								</div>
							</div>
						</div>
					</div>';
			
		}
		
		public function iconModal(){
				
			$icons="";
					
			foreach(DB::get("fontlar")->result() as $icon){
				
				$icons.= '<label for="radio'.$icon->id.'" class="col-md-4">
			<div class="thumnail">
				<p class="text-center"><i class="fa fa-5x fa-'.$icon->adi.'"></i></p>
				<input type="radio" name="icon" id="radio'.$icon->id.'" onclick="selectImg('.$icon->id.')" />
			</div>
			<div class="clearfix"></div>
		</label>	';
				
			}
			
			echo '<div class="modal inmodal fade" id="ResimSec" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="tabs-container">
									<div class="modal-header" style="padding-bottom:0px;">
										<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Kapat</span></button>
										<h4 class="modal-title">İcon Seçme Alanı</h4>
									</div>
										<div class="tab-content">
											<div id="tab-1" class="tab-pane active">
												<div class="modal-body">
													<div class="row">
													'.$icons.'
													</div>
													<div class="clearfix"></div>
												</div>
											</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-white" data-dismiss="modal">Kapat</button>
									<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="ImgToggle()">İconu Kaydet</button>
								</div>
							</div>
						</div>
					</div>';
			
		}
		
		
		
		public function modalButton($btn="Resim Seç"){
			
			echo '<button type="button" class="btn btn-primary col-xs-12" data-toggle="modal" data-target="#ResimSec">
                                   '.$btn.'
                                </button>';
			
		}
		
	}
	
?>