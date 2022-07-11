<?php
class e_dak extends CI_Controller
{
	function __construct(){
		parent::__construct();
		$this->cek_session();          
		$this->load->model('e-planning/Manajemen_model','mm');
		$this->load->model('e-monev/dashboard_model','dm');
		$this->load->model('e-monev/dashboard_unit_model', 'dum');
		$this->load->model('e-monev/laporan_monitoring_model','lmm');
		$this->load->model('e-monev/pendaftaran_model','pm');
		$this->load->model('e-planning/aktivitas_model','am');
		$this->load->model('e-planning/manajemen_model','mm');
		$this->load->model('basic_model','bm');
		$this->load->library("excel");
		// $this->load->library( array( 'excel' ,'../controllers/e-monev/pendaftaran_edak') );

	}
	
	function cek_session()
		{
		$kode_role = $this->session->userdata('kd_role');
		if($kode_role == '')
		{
			redirect('login/login_ulang');
		}
	}
	
	function index()
	{
		$this->input();
	}
	
	function get_satuan(){
		$satuan = $this->pm->get('ref_satuan')->result();
		$i = 0;
		foreach ($satuan as $row) {
			$datajson[$i]['KODE'] = $row->KodeSatuan;	
			$datajson[$i]['NAMA'] = $row->Satuan;
			$i++;
		}

		echo json_encode($datajson);
	}

	function get_json($table,$kode1,$kode2 = null)
	{
		if($table=='satker'){
			$query = $this->pm->get_where('ref_satker',$kode1,'kdsatker');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
					$datajson[$i]['ID'] = $row->kdsatker;	
					$datajson[$i]['NAMA'] = $row->nmsatker;
					$i++;
				}
			}
			else{	
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}
		else if($table=='jenis_dak'){
			$query = $this->pm->get_where('dak_jenis_dak',$kode1,'ID_JENIS_DAK');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->ID_JENIS_DAK;	
					$datajson[$i]['NAMA'] = $row->NAMA_JENIS_DAK;
					$i++;
				}
			}
			else{	
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}		
		else if($table=='kat'){
			$query = $this->pm->get_where('kategori',$kode1,'ID_KATEGORI');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->ID_KATEGORI;	
					$datajson[$i]['NAMA'] = $row->NAMA_KATEGORI;
					$i++;
				}
			}
			else{
				$datajson[0]['ID'] = 'Tidak ada ';
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}			
		else if($table=='provinsi'){
			$query = $this->pm->get_where('ref_provinsi',$kode1,'KodeProvinsi');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->KodeProvinsi;	
					$datajson[$i]['NAMA'] = $row->NamaProvinsi;
					$i++;
				}
			}
			else{
				$datajson[0]['ID'] = 'Tidak ada ';
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}			
		else if($table=='kabupaten'){
			$query = $this->pm->get_where_double('ref_kabupaten',$kode1,'KodeProvinsi',$kode2,'KodeKabupaten');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->KodeProvinsi;	
					$datajson[$i]['NAMA'] = $row->NamaKabupaten;
					$i++;
				}
			}
			else{
				$datajson[0]['ID'] = 'Tidak ada ';
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}
		else if($table=='menu'){
			$query = $this->pm->get_where_triple('menu',$kode1,'ID_SUBBIDANG',$kode2,'ID_KATEGORI', $this->session->userdata('thn_anggaran'), 'tahun');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->ID_MENU;	
					$datajson[$i]['NAMA'] = $row->NAMA;
					$i++;
				}
			}
			else{
				$datajson[0]['ID'] = 'Tidak ada ';
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}
		else if($table=='rumah_sakit'){
			$query = $this->pm->get_where('data_rumah_sakit',$kode1,'KODE_RS');
			$i=0;
			if($query->num_rows >0){
				foreach($query->result() as $row)
					{
				    $datajson[$i]['ID'] = $row->KODE_RS;	
					$datajson[$i]['NAMA'] = $row->NAMA_RS;
					$i++;
				}
			}
			else{	
				$datajson[0]['NAMA'] = 'Tidak ada ';
			}
		}					
		echo json_encode($datajson);
	}

	function grid()
	{
		$data['thang'] = $this->session->userdata('thn_anggaran');
		$data['content'] = $this->load->view('e-monev/dashboard_kementerian',$data,true);
		$this->load->view(VIEWPATH,$data);
	}

	function save_pagu(){
		$id_subbidang=$this->input->post('jenis_dak');
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$id_menu=$this->input->post('id_menu');
		$volume=$this->input->post('volume');
		$satuan=$this->input->post('satuan');
		$harga_satuan=$this->input->post('harga_satuan');
		$kode_rs = $this->input->post('kode_rs');
		if($kode_rs == null ){
			$kode_rs = 0;
		}
		$pagu=$this->input->post('pagu');
		$data2 = array(
            "ID_JENIS_DAK"=> $id_subbidang,
            "ID_KATEGORI"=> $kategori,
            "ID_MENU"=> $id_menu,
            "KodeKabupaten"=> $KodeKabupaten,
            "KodeProvinsi"=> $KodeProvinsi,
            "KODE_RS" => $kode_rs,
            "ID_MENU"=> $id_menu,
            "VOLUME" => $volume,
            "SATUAN" => $satuan,
            "HARGA_SATUAN" => $harga_satuan,
            "PAGU" => $pagu,
            "TAHUN_ANGGARAN" => $this->session->userdata('thn_anggaran'),
            'versi' => 1,
            'status' => 1,
            'perubahan' => 1
        );   
        // print_r($data2); exit();        		
        $this->pm->save($data2,'pagu');
        redirect('e-monev/e_dak/view_pagu2?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&kdsatker='.$kdsatker.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten.'&kode_rs='.$kode_rs);
	}

	function save_pagu_seluruh(){
		$id_subbidang=$this->input->post('jenis_dak');
		$jenis_dak = $id_subbidang;
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$kdrs=$this->input->post('kode_rs');
		$thn = $this->session->userdata('thn_anggaran');
		$pagu=$this->input->post('pagu');
		if($jenis_dak !='1' && $jenis_dak !='10' && $jenis_dak !='11' && $jenis_dak !='20' && $jenis_dak !='23'){
			$data2 = array(
	            "ID_SUBBIDANG"=> $id_subbidang,
	            "ID_KATEGORI"=> $kategori,
	            "KodeKabupaten"=> $KodeKabupaten,
	            "KodeProvinsi"=> $KodeProvinsi,
	            "pagu_seluruh" => $pagu,
	            'TAHUN_ANGGARAN' => $thn
	        );            		

	        $insert = $this->db->insert('pagu_seluruh',$data2);
		}
		else{
			$data2 = array(
	            "ID_Jenis_DAK"=> $id_subbidang,
	            "KodeKabupaten"=> $KodeKabupaten,
	            "KodeProvinsi"=> $KodeProvinsi,
	            "PAGU_SELURUH" => $pagu,
	            'KODE_RS' => $kdrs,
	            'TAHUN_ANGGARAN' => $thn
	        );            		

	        $insert = $this->db->insert('pagu_rs',$data2);
		}

		// print_r($data2); exit();
		
        redirect('e-monev/e_dak/view_pagu3?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten);
	}


	function update_pagu(){
		$id_subbidang=$this->input->post('jenis_dak');
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$id_pagu=$this->input->post('id_pagu');
		$hs=$this->input->post('harga_sat');
		$vol=$this->input->post('volume');
		// print_r($id_pagu); exit();
		$kdsatker=$this->input->post('kdsatker');
		$kdrs=$this->input->post('kode_rs');
		$pagu=$this->input->post('pagu');

		$data2 = array(
            "pagu" => str_replace(",","",$pagu),
            "HARGA_SATUAN" => str_replace(",","",$hs),
            "volume" => $vol
        );            		

        $this->pm->update('pagu', $data2, 'ID_PAGU', $id_pagu);
        redirect('e-monev/e_dak/view_pagu2?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&kdsatker='.$kdsatker.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten.'&kode_rs='.$kdrs);
	}

	function update_pagu_seluruh(){
		$id_subbidang=$this->input->post('jenis_dak');
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$id_pagu=$this->input->post('id_pagu');
		$pagu=$this->input->post('pagu');

		$data2 = array(
            "pagu_seluruh" => str_replace(",", "", $pagu)
        );            		
        $this->pm->update('pagu_seluruh', $data2, 'ID_PAGU', $id_pagu);
        redirect('e-monev/e_dak/view_pagu3?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten);
	}
	function delete_pagu(){
		$id_subbidang=$this->input->post('jenis_dak');
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$id_pagu=$this->input->post('id_pagu');
		$kdsatker=$this->input->post('kdsatker');
		$pagu=$this->input->post('pagu');
  		$kode_rs = $this->input->post('kode_rs');

        $this->pm->delete('pagu','ID_PAGU', $id_pagu);
        redirect('e-monev/e_dak/view_pagu2?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&kdsatker='.$kdsatker.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten.'&kode_rs='.$kode_rs);
	}
	function delete_pagu_seluruh(){
		$id_subbidang=$this->input->post('jenis_dak');
		$kategori=$this->input->post('kategori');
		$KodeProvinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$id_pagu=$this->input->post('id_pagu');
		$kdsatker=$this->input->post('kdsatker');
		$pagu=$this->input->post('pagu');
  		if($jenis_dak !='1' && $jenis_dak !='10' && $jenis_dak !='11' && $jenis_dak !='20' && $jenis_dak !='23'){
  			$this->pm->delete('pagu_seluruh','ID_PAGU', $id_pagu);	
  		}

        
        redirect('e-monev/e_dak/view_pagu3?jenis_dak='.$id_subbidang.'&kategori='.$kategori.'&KodeProvinsi='.$KodeProvinsi.'&KodeKabupaten='.$KodeKabupaten);
	}

	function view_edak()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi= $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis  --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$rs='';
		if($role==20){
			$data_rs=$this->pm->get_where('data_rumah_sakit', $this->session->userdata('kdsatker'), 'KODE_RS')->result();
			foreach($data_rs as $row){
				$rs='  <table class="table table-bordered">
					<thead>
							<tr>
									<th>Nama Rumah Sakit</th>
									<th>Alamat</th>
							</tr>
					</thead>
					<tbody>
							<tr>
									<td> <input checked type="radio" id="kode_rs" name="kode_rs" value="'.$row->KODE_RS.'">'.$row->NAMA_RS.'</td>
									<td>'.$row->ALAMAT.'</td>
							</tr>
					</table>';
			}
		}
		$data2['rs'] = $rs;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['role'] =$this->session->userdata('kd_role');
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'VIEW EDAK';			
		$data['content'] = $this->load->view('metronic/e-monev/view_dak',$data2,true);
			
		$this->load->view(VIEWPATH,$data);
	}

	function view_edak_nf()
	{    
		$role=$this->session->userdata('kd_role');
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak_nf')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$rs='';
		if($role==20){
			$data_rs=$this->pm->get_where('data_rumah_sakit', $this->session->userdata('kdsatker'), 'KODE_RS')->result();
			foreach($data_rs as $row){
				$rs=' 
					<tr>
					<td> Nama Rumah Sakit :</td>
					<td>'.$row->NAMA_RS.'</td>		
					</tr>';
			} 
		}
		$data2['rs']=$rs;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['kdkabupaten']=$kdkabupaten;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['role'] =$this->session->userdata('kd_role');
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'VIEW EDAK';
		$data['content'] = $this->load->view('metronic/e-monev/view_dak_nf',$data2,true);
			$this->load->view(VIEWPATH,$data);
	}

	function get_kabupaten($kode1)
	{
		$query = $this->pm->get_data_kabupaten($kode1);
		$i=0;
		if($query->num_rows >0){
			foreach($query->result() as $row)
				{
				$datajson[$i]['KodeKabupaten'] = $row->KodeKabupaten;
				$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
				$i++;
			}
		}
		else {	
		$datajson[0]['KodeKabupaten'] = '0';
		$datajson[0]['NamaKabupaten'] = 'Tidak ada kegiatan';
		}
		echo json_encode($datajson);
	}

	

	function get_satker2($p,$k)
	{
		$query = $this->pm->get_where_double('ref_satker',$p,'kdlokasi',$k,'kdkabkota');
		$i=0;
		if($query->num_rows >0){
			foreach($query->result() as $row)
				{
				$datajson[$i]['kdsatker'] = $row->kdsatker;
				$datajson[$i]['nmsatker'] = $row->nmsatker;
				$i++;
			}
		}
		else {	
			$datajson[$i]['kdsatker'] = 0;
			$datajson[$i]['nmsatker'] = '';
		}
		echo json_encode($datajson);
	}

	function get_data_pengajuan_2020($id='')
	{
		$tahun              = $this->session->userdata('thn_anggaran');

		$whereMonev = array(
					'id_pengajuan'  => $id
				);
		$pengajuan = $this->bm->select_where_array('pengajuan_monev_dak', $whereMonev)->row();

		// echo "okehYAA ".$pengajuan->ID_USER;

				$datapagu = array(
						'kodeprovinsi'  => $pengajuan->KodeProvinsi,
						'kodekabupaten' => $pengajuan->KodeKabupaten,
						'id_jenis_dak'  => $pengajuan->ID_SUBBIDANG,
						'tahun'         => $tahun
				);
				$totalPagu = $this->bm->select_where_array('dak_fisik_pagu', $datapagu)->row()->pagu;


				$wherePen = array(
					'kodekabupaten' => $pengajuan->KodeProvinsi,
					'kodeprovinsi'  => $pengajuan->KodeKabupaten,
					'id_jenis_dak'  => $pengajuan->ID_SUBBIDANG,
					'tahun'         => $tahun
				);
				$listpenunjang = $this->bm->select_where_array('dak_penunjang', $wherePen)->result();

				$dataMonev = array(
						'id_pengajuan' => $id,
						'triwulan'     => $pengajuan->waktu_laporan,
						'tahun'        => $tahun

				);
				$listmonev = $this->bm->select_where_array('data_monev_rka_2020', $dataMonev)->result();


		$tableProv = "ref_kabupaten";
		$whereProv = "WHERE KodeProvinsi='".$pengajuan->KodeProvinsi."' and KodeKabupaten='".$pengajuan->KodeKabupaten."'";
			
		$kabupaten = $this->bm->getAllWhere($tableProv,$whereProv)->row_array();

			$tableKat = "dak_jenis_dak";
			$whereKat = "WHERE ID_JENIS_DAK = '".$pengajuan->ID_SUBBIDANG."' ";
			$kategori = $this->bm->getAllWhere($tableKat,$whereKat)->row_array();

		$masalah = $this->pm->get('permasalahan_dak')->result();
		$satuan  = $this->pm->get('ref_satuan')->result();


				// header("Content-type=appalication/vnd.ms-excel");
				// header("content-disposition:attachment;filename=monev_".$pengajuan->waktu_laporan."_".$tahun.".xls");

		$data['pengajuan']     = $pengajuan;
		
		$data['listpenunjang'] = $listpenunjang;
		$data['totalPagu']     = $totalPagu;
		$data['listmonev']     = $listmonev;

			$data['totalPagu']     = $totalPagu;
			$data['satuan']        = $satuan;
			$data['masalah']       = $masalah;
			$data['namakabupaten'] = $kabupaten['NamaKabupaten'];
			$data['namakategori']  = $kategori['NAMA_JENIS_DAK'];

		$data['content']     = $this->load->view('metronic/e-monev/v_monev_detview',$data);
	}
	function get_data_pengajuan($id){
		$pengajuan = $this->bm->select_where('pengajuan_monev_dak', 'id_pengajuan', $id)->row();
		if($pengajuan->KD_RS != 0){
			$hasil = $this->pm->get_pengajuan_monev_rs($id);	
		}
		else{
			$hasil = $this->pm->get_pengajuan_monev($id);					
		}
		
		$i =0;
		$no = 1;
		if($hasil->num_rows() !=0){
			foreach ($hasil->result() as $row) {
			 	$datajson[$i]['NO'] = $no;
			 	$datajson[$i]['MENU'] = $row->nama_menu;
			 	$datajson[$i]['VOLUME'] = $row->VOLUME;
			 	$datajson[$i]['SATUAN'] = $row->Satuan;
			 	$datajson[$i]['HARGA_SATUAN'] = $row->HARGA_SATUAN;
			 	$datajson[$i]['PAGU'] = $row->PAGU;
			 	$datajson[$i]['REALISASI'] = $row->realisasi;
			 	$datajson[$i]['PERSENTASE'] = $row->persentase;
			 	$datajson[$i]['REALISASI_FISIK'] = $row->fisik;
			 	$datajson[$i]['MASALAH'] = $row->Masalah;
			 	if(isset($row->pagu_seluruh)){
			 		$datajson[$i]['PAGU_SELURUH'] = $row->pagu_seluruh;	
			 	}
			 	else{
			 		$datajson[$i]['PAGU_SELURUH'] = $row->PAGU_SELURUH;
			 	}
			 	
			 	$i++;
			 	$no++;

		 	}	
		}
		else{
			$datajson[$i]['NO'] = '';
		 	$datajson[$i]['MENU'] = '';
		 	$datajson[$i]['VOLUME'] = '';
		 	$datajson[$i]['SATUAN'] = '';
		 	$datajson[$i]['HARGA_SATUAN'] = '';
		 	$datajson[$i]['PAGU'] = '';
		 	$datajson[$i]['REALISASI'] = '';
		 	$datajson[$i]['PERSENTASE'] = '';
		 	$datajson[$i]['REALISASI_FISIK'] = '';
		 	$datajson[$i]['MASALAH'] = '';
		 	$datajson[$i]['PAGU_SELURUH'] = '';
		}

		
		 echo json_encode($datajson);
	}

	function view_pagu()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['000'] = 'seluruh indonesia';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
				$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$role=$this->session->userdata('kd_role');
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17){
			$selected_kabupaten=0;
						$kdkabupaten=0;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'View pagu';
			// $today=date('Y-m-d');
			// if ($today > '2015-12-31') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// } else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// if ($this->session->userdata('kdsatker') == '465915' || $this->session->userdata('kodeprovinsi') == '05') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// }
		$data['content'] = $this->load->view('metronic/e-monev/view_pagu',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_juknis()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['000'] = 'seluruh indonesia';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
				$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$role=$this->session->userdata('kd_role');
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_juknis',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelola_juknis()
	{


		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}



		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelola_juknis',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelola_menu()
	{
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}


		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelola_menu',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelola_pagu()
	{


		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}


		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelola_pagu',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_pagu_seluruh(){
		$tahun = $this->session->userdata('thn_anggaran');
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $tahun, "TAHUN_ANGGARAN" )->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_pagu_seluruh',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_pagu_rs(){
		$tahun = $this->session->userdata('thn_anggaran');
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $tahun, "TAHUN_ANGGARAN" )->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_pagu_rs',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function cetak_absensi2(){

		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh Indonesia';
		$option_provinsi['98'] = 'Seluruh Provinsi';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$option_kategori['0'] = 'Pilih Kategori';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data2['kategori']=$option_kategori;
		$data['judul'] = 'View Absensi';

		if($this->session->userdata('thn_anggaran') < 2018){
			$data['content'] = $this->load->view('metronic/e-monev/view_absensi2',$data2,true);
			$this->load->view(VIEWPATH,$data);	
		}
		else{
			$data['content'] = $this->load->view('metronic/e-monev/view_absensi_2018',$data2,true);
			$this->load->view(VIEWPATH,$data);
		}
		
	}

	function view_absensi2_nf(){
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		$kdsatker =  $this->session->userdata('kdsatker');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}

		}
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($kdprovinsi)->result() as $row) {
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata("thn_anggaran"), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';

		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}

		$role = $this->session->userdata('kd_role');
		$data2['role']	= $role;	
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['option_kabupaten'] = $option_kabupaten;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/view_absensi2_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelengkapan_2()
	{
		$kdsatker = $this->session->userdata('kdsatker');
		$role = $this->session->userdata('kd_role');
		$tahun = $this->session->userdata("thn_anggaran");
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $tahun, "TAHUN_ANGGARAN")->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($idprov)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		if($role == 20 ){
			$rs = $this->bm->select_where('data_rumah_sakit', 'KODE_RS', $this->session->userdata('kdsatker'))->row();
			$data2['rs'] = $rs;
		}
		$data2['option_provinsi'] = $option_provinsi;
		$data2['selected_state'] = $selected_state;
		$data2['KodeProvinsi'] = $idprov;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['role'] = $role;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View Kelengkapan';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelengkapan_2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}


	function view_kelengkapan_2_nf(){
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$data['e_monev'] = "";
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View Kelengkapan';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelengkapan_nf2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function kelengkapan_nf(){
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$kdsatker = $this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get_where('dak_nf_kategori', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
			$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}

		$role = $this->session->userdata('kd_role');
		$data2['role']	= $role;	
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['selected_state'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data['e_monev'] = "";
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View Kelengkapan';
		$data['content'] = $this->load->view('metronic/e-monev/kelengkapan_nf2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function view_proses_realisasi(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh Indonesia';
		$option_provinsi['98'] = 'Seluruh Provinsi';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata("thn_anggaran"), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/view_proses_realisasi',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function view_proses_realisasi_nf(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh Indonesia';
		$option_provinsi['98'] = 'Seluruh Provinsi';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/view_proses_realisasi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function laporan_realisasi_2(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->geT('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}
		$rs="";
		$role = $this->session->userdata('kd_role');	
		if($role==20){
			$data_rs=$this->pm->get_where('data_rumah_sakit', $this->session->userdata('kdsatker'), 'KODE_RS')->result();
			$rs = "
				<table class='table table-responsive'>
                     <tr>
                        <td width='15%'>Nama Rumah Sakit</td>
                        <td>
                           <input type='text' style='display:none;' id='rumah_sakit' readonly='true' name='rumah_sakit' value='". $data_rs[0]->KODE_RS."'><input  style='width:40%' type='text' class='form-control input-sm' readonly='true' value='".$data_rs[0]->NAMA_RS."' >

                        </td>
                     </tr>
                  </table>  
			";
		}
		else if($role == 18){
			$kab = $this->pm->get_where('ref_kabupaten', $kdprovinsi, 'KodeProvinsi')->result();
			$list_kab = array();
			foreach ($kab as $key => $value) {
				$list_kab[$value->KodeKabupaten] = $value->NamaKabupaten;
			}
			$data2['list_kab'] = $list_kab;
		}
 		$data2['rs'] = $rs;	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$tahun = $this->session->userdata('thn_anggaran');
		switch ($tahun) {
			case '2018':
				$data['content'] = $this->load->view('metronic/e-monev/view_laporan_realisasi',$data2,true);
				break;
			
			case '2019':
				$data['content'] = $this->load->view('metronic/e-monev/view_laporan_realisasi',$data2,true);
				break;
			
			case '2020':
				$data2['menu_input']= "Laporan Monev Fisik";
				$data2['title']= "MONEV";
				$data['content'] = $this->load->view('metronic/e-monev/view_laporan_realisasi_2020',$data2,true);
				break;

			default:
				$data['menu']= "Error ";
				$data['title']= "Error ";
				$data['pesan']= "Akses di tutup ";
				$data['content']    = $this->load->view('metronic/e-monev/v_eror',$data,true);
				break;
		}

		
		$this->load->view(VIEWPATH,$data);
	}
	function laporan_realisasi_2_nf(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/view_laporan_realisasi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function get_monev_detail($p,$k,$jenis_dak,$kategori, $waktu_laporan, $rs){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_detail($p,$k,$jenis_dak,$kategori ,$waktu_laporan,$tahun, $rs);
		$i=0;
		$no=1;
		$jumlah = 0;
		$fisik =0;
		$datajson = array();
		if($hasil->num_rows() != 0){
			foreach ($hasil->result() as $row) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['ID_PAGU'] = $row->ID_PAGU;
				$datajson[$i]['perubahan']= $row->perubahan;
				if ($tahun>='2019') {
					// cari nama menu di fisik;
					$table="data_fisik_tch";
					$where="WHERE kodekabupaten='".$k."' AND kodeprovinsi='".$p."' AND id_kategori='".$kategori."' and id_jenis_dak='".$jenis_dak."' AND rincian_kegiatan='".$row->nama_menu."'";

					$datafis = $this->pm->getAllWhere($table,$where)->row_array();
					$datajson[$i]['NAMA_MENU']    = $datafis['menu'];
				} else {


				}

				
				
				//echo $akses_tgl.'dan'.$date; 2020-01-10
				

				switch ($tahun) {
					case '2019':
						$tgl_buat = date('Y-m-d 23:59:59', strtotime($row->tanggal_pembuatan)) ;
						$param_date      = date('2020-01-15 23:59:59');
						if ($tgl_buat  <= $param_date) {
							$keterangan ='tepat' ;
						}else {
							$keterangan ='terlambat' ;
						}

						break;
					
					case '2020':
						$keterangan ='tepat' ;
						break;

					default:
						$keterangan ='tepat' ;
						break;
				}

				$datajson[$i]['NAMA']         = $row->nama_menu;
				$datajson[$i]['VOLUME']       = $row->VOLUME;
				$datajson[$i]['SATUAN']       = $row->Satuan;
				$datajson[$i]['HARGA_SATUAN'] = $row->HARGA_SATUAN;
				$datajson[$i]['PAGU']         = $row->PAGU;
				$datajson[$i]['PAGU_SELURUH'] = $row->pagu_seluruh;
				$datajson[$i]['REALISASI']    = $row->realisasi;
				$datajson[$i]['PERSENTASE']   = $row->persentase;
				$datajson[$i]['FISIK']        = $row->fisik;
				$datajson[$i]['status_pembuatan'] = $keterangan;
				if($row->vol_terpenuhi){
					$datajson[$i]['VOL_T'] = $row->vol_terpenuhi;	
				}
				else{
					$datajson[$i]['VOL_T'] = '';
				}
				if($row->vol_belum_terpenuhi){
					$datajson[$i]['VOL_B'] = $row->vol_belum_terpenuhi;	
				}
				else{
					$datajson[$i]['VOL_B'] ='';	
				}
				if($row->rincian_output){
					$datajson[$i]['RINCIAN'] = $row->rincian_output;	
				}
				else{
					$datajson[$i]['RINCIAN'] = '';
				}
				if($row->ID_SARPRAS == 3){
					$datajson[$i]['SATUAN2'] = 'Set';	
				}
				else{
					$datajson[$i]['SATUAN2'] = $row->Satuan;	
				}
				$datajson[$i]['MASALAH'] = $row->Masalah;
				$datajson[$i]['lokasi']  = $row->lokasi;
				$i++;
				$no++;	
			}
		}
		echo json_encode($datajson);
	}
	public function get_monev_detail_2020($d='',$k='',$provinsi='',$kabupaten='',$rs='',$tw='')
	{
		$tahun   = $this->session->userdata('thn_anggaran');

		// cek apakah pengajuan_monev_dak sudah input atau belum
		$whereMonev = array(
					'KodeKabupaten'  => $kabupaten,
					'KodeProvinsi'   => $provinsi,
					'ID_SUBBIDANG'   => $d,
					'ID_KATEGORI'    => $k,
					'KD_RS'          => $rs,
					'waktu_laporan'  => $tw,
					'TAHUN_ANGGARAN' => $this->session->userdata('thn_anggaran')
				);
		$monevrow = $this->bm->select_where_array('pengajuan_monev_dak', $whereMonev);

		$parmonev=$monevrow->num_rows();
		// echo "lksalkdlaksldaslk".$parmonev;

		if ($parmonev == '0') { ?>

			<div class="alert alert-warning">Anda Belum Menginput Realisasi Ini</div>

		<?php } else {
			# code...
		$datamonev=$monevrow->row_array();

		// echo "stringasdasd ".$datamonev['id_pengajuan'];
		

			// $listing=$this->pm->get_where_5('dak_penunjang',$kabupaten,'Kodekabupaten',$provinsi,'Kodeprovinsi',$d,'id_jenis_dak',$k,'id_kategori',$tahun,'tahun');
			$where = array(
					'kodekabupaten' => $kabupaten,
					'kodeprovinsi' => $provinsi,
					'id_jenis_dak' => $d,
					'id_kategori' => $k,
					'tahun' => $this->session->userdata('thn_anggaran')
				);

			$wheredak = array(
					'id_pengajuan' => $datamonev['id_pengajuan']
				);

				$masalah = $this->pm->get('permasalahan_dak')->result();
				$satuan  = $this->pm->get('ref_satuan')->result();

				$listfisik = $this->bm->select_where_array('data_monev_rka_2020', $wheredak)->result();


				// $table2="dak_penunjang dp";
    //         	$where2="INNER JOIN dak_penunjang_input dpi ON dp.idpenunjang= dpi.id_penunjang
				// 		WHERE dp.kodeprovinsi=".$provinsi." AND dp.kodekabupaten=".$kabupaten." AND dp.id_jenis_dak=".$d." AND dpi.triwulan=".$tw." ";	
    //         	$listing2 = $this->pm->getAllWhere($table2,$where2)->result();


				$listing2 = $this->pm->penunjangInput($provinsi,$kabupaten,$d,$tw,$tahun)->result();
				$listing = $this->bm->select_where_array('dak_penunjang', $where)->result();


				$triwulan = $tw;
				
			// $listing = 'ok';

			$wherepagu = array(
					'KodeKabupaten'  => $kabupaten,
					'KodeProvinsi'   => $provinsi,
					'ID_SUBBIDANG'   => $d,
					'ID_KATEGORI'    => $k,
					'TAHUN_ANGGARAN' => $this->session->userdata('thn_anggaran')
				);
			$listpagu = $this->bm->select_where_array('pagu_seluruh', $wherepagu)->row_array();
			$totalPagu = $listpagu['pagu_seluruh'];


				$data['triwulan'] = $triwulan;
				$data['kd_jd']    = $d;
				$data['kd_kat']   = $k;
				$data['kd_prov']  = $provinsi;
				$data['kd_kab']   = $kabupaten;
				$data['kd_rs']    = $rs;

				$data['masalah']   = $masalah;
				$data['satuan']    = $satuan;
				$data['listing']   = $listing;
				$data['listing2']   = $listing2;
				$data['listfisik'] = $listfisik;
				$data['datamonev'] = $datamonev;
				
				$data['totalPagu'] = $totalPagu;

		$data['content']   = $this->load->view('metronic/e-monev/v_monev_terinput_2020',$data);

		}
	}



	function get_monev_detail_nf($p,$k,$waktu_laporan){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_detail_nf($p,$k, $waktu_laporan,$tahun);
		$i=0;
		$no=1;
		$jumlah = 0;
		$fisik =0;
		$datajson[$i]['ID_PAGU'] = 0;
		if($hasil->num_rows() != 0){
			foreach ($hasil->result() as $row) {
				$id =  $row->kode_menu;
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['ID_PAGU'] = $id;
				$datajson[$i]['NAMA'] = $row->nama_menu;
				$datajson[$i]['PAGU'] = $row->PAGU;
				$datajson[$i]['PAGU_SELURUH'] = $row->pagu_seluruh;
				$datajson[$i]['REALISASI'] = $row->realisasi;
				$datajson[$i]['PERSENTASE'] = $row->persentase;
				$datajson[$i]['FISIK'] = $row->fisik;
				$datajson[$i]['MASALAH'] = $row->Masalah;
				$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
				$i++;
				if($id==1){
					$j = $i;	
					$query = $this->pm->get_pagu_bok($p, $k)->result();
					$query2 = $this->pm->get_where("data_monev_nf", $row->id_pengajuan, "id_pengajuan")->result();
					foreach ($query as $row2) {
						if($row2->id > 4){
							$datajson[$j]['NO'] = $no ."." . $row2->id;
							$datajson[$j]['ID_PAGU'] = $row2->id;
							$datajson[$j]['NAMA'] = $row2->NAMA;
							$datajson[$j]['PAGU'] = $row2->PAGU;
							$datajson[$j]['PAGU_SELURUH'] = $row->pagu_seluruh;
							$j++;
						}
					}
					foreach ($query2 as $row3) {
						if($row3->kode_menu > 4){
							$datajson[$i]['REALISASI'] = $row3->realisasi;
							$datajson[$i]['PERSENTASE'] = $row3->persentase;
							$datajson[$i]['FISIK'] = $row3->fisik;
							$masalah = $this->pm->get_where("permasalahan_dak", $row3->KodeMasalah, "KodeMasalah")->result();
							$datajson[$i]['MASALAH'] = $masalah[0]->Masalah;
							$i++;
						}
					}	
				}
				$no++;	
			}
		}
		else{
			$datajson[$i]['NO'] = '';
			$datajson[$i]['ID_PAGU'] = 0;
			$datajson[$i]['NAMA'] = '';
			$datajson[$i]['PAGU'] = '';
			$datajson[$i]['PAGU_SELURUH'] = '';
			$datajson[$i]['REALISASI'] = '';
			$datajson[$i]['PERSENTASE'] = '';
			$datajson[$i]['FISIK'] = '';
			$datajson[$i]['MASALAH'] = '';
			$datajson[$i]['id_pengajuan'] = '';
		}
		echo json_encode($datajson);
	}
	function delete_realisasi($p,$k,$jenis_dak,$kategori, $waktu_laporan, $rs){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_where_6('pengajuan_monev_dak',$p, 'KodeProvinsi', $k,'KodeKabupaten',$jenis_dak, 'ID_SUBBIDANG', $rs, 'KD_RS', $waktu_laporan, 'waktu_laporan', $tahun, 'TAHUN_ANGGARAN')->result();
		$this->pm->delete('pengajuan_monev_dak', 'id_pengajuan', $hasil[0]->id_pengajuan);
		$this->pm->delete('data_monev_rka', 'id_pengajuan', $hasil[0]->id_pengajuan);
		redirect('e-monev/e_dak/laporan_realisasi_2?status=4');
	}
	function delete_realisasi_nf($p,$k, $waktu_laporan){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_where_quadruple('pengajuan_monev_nf',$p, 'KodeProvinsi', $k,'KodeKabupaten', $waktu_laporan, 'waktu_laporan', $tahun, 'TAHUN_ANGGARAN'	)->result();
		$this->pm->delete('pengajuan_monev_nf', 'id_pengajuan', $hasil[0]->id_pengajuan);
		$this->pm->delete('data_monev_nf', 'id_pengajuan', $hasil[0]->id_pengajuan);
		redirect('e-monev/e_dak/laporan_realisasi_2_nf?status=4');
	}

	function cetak_realisasi($p,$k,$jenis_dak,$kategori, $waktu_laporan, $rs){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_detail($p,$k,$jenis_dak,$kategori, $waktu_laporan,$tahun, $rs)->result();
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Laporan Realisai');
		$kab = $this->pm->get_where_double('ref_kabupaten',$p, 'KodeProvinsi', $k , 'KodeKabupaten')->result();
		$dak = $this->pm->get_where('dak_jenis_dak',$jenis_dak, 'ID_JENIS_DAK')->result();
		$this->excel->getActiveSheet()->setCellValue('A1', 'Laporan Realisasi ' . $dak[0]->NAMA_JENIS_DAK . ' ' . $kab[0]->NamaKabupaten);
		foreach (range('A', 'J') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
			 $this->excel->getActiveSheet()->getStyle($char)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$this->excel->getActiveSheet()->getColumnDimension(A)->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension(B)->setWidth(35);
		$this->excel->getActiveSheet()->getColumnDimension(J)->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:J1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A2:J2');
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->mergeCells('B4:B5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Menu');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:F4');
		$this->excel->getActiveSheet()->setCellValue('G4', 'Realisasi');
		$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('G4:I4');
		$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('J4', 'Permasalahan');
		$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('J4:J5');
		$this->excel->getActiveSheet()->setCellValue('C5', 'Volume');
		$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D5', 'Satuan');
		$this->excel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E5', 'Harga Satuan');
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F5', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G5', 'Realisasi Dana');
		$this->excel->getActiveSheet()->getStyle('G5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H5', 'Persentase');
		$this->excel->getActiveSheet()->getStyle('H5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('I5', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('I5')->getFont()->setBold(true);
		
		
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'), 'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $this->excel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleArrayHead);
        $this->excel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleArrayHead);
        $i = 6;
        $no = 1;
        $realisasi=0;
        $fisik = 0;
        $pagu_seluruh = 0;
        foreach ($hasil as $row) {
        	$pagu_seluruh += $row->PAGU;
            $fisik += $row->fisik;
            $realisasi+= $row->realisasi;
        	$this->excel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($styleArray);
        	$this->excel->getActiveSheet()->setCellValue('A'.$i , $no);
        	$this->excel->getActiveSheet()->setCellValue('B'.$i , $row->nama_menu);
        	$this->excel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('C'.$i , $row->VOLUME);
            $this->excel->getActiveSheet()->setCellValue('D'.$i , $row->Satuan);
            $this->excel->getActiveSheet()->setCellValue('E'.$i , number_format($row->HARGA_SATUAN));
            $this->excel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('F'.$i , number_format($row->PAGU));
            $this->excel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('G'.$i , number_format($row->realisasi));
            $this->excel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('H'.$i , $row->persentase);
            $this->excel->getActiveSheet()->setCellValue('I'.$i , $row->fisik);
            $this->excel->getActiveSheet()->setCellValue('J'.$i , $row->Masalah);
            $this->excel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setWrapText(true);
            $no++;
            $i++;

        }
        $this->excel->getActiveSheet()->setCellValue('A'.$i , "Total Realisasi");
        $this->excel->getActiveSheet()->mergeCells('A'.$i .':E'.$i);
        $this->excel->getActiveSheet()->setCellValue('F'.$i , number_format($pagu_seluruh));
        $this->excel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('G'.$i , number_format($realisasi));
        $this->excel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $number = (float)$realisasi*100/$pagu_seluruh;
        $fisik = $fisik / ($no-1);
        $this->excel->getActiveSheet()->setCellValue('H'.$i, round($number, 2) . "%");
        $this->excel->getActiveSheet()->setCellValue('I'.$i , $fisik);
        $this->excel->getActiveSheet()->setCellValue('J'.$i , "");
        $this->excel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($styleArray2);
		$filename='Laporan Realisasi-'. $dak[0]->NAMA_JENIS_DAK . ' '. $kab[0]->NamaKabupaten .'_.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function cetak_realisasi_nf($p,$k, $waktu_laporan){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_detail_nf($p,$k,$waktu_laporan,$tahun)->result();
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Laporan Realisai');
		$kab = $this->pm->get_where_double('ref_kabupaten',$p, 'KodeProvinsi', $k , 'KodeKabupaten')->result();
		$this->excel->getActiveSheet()->setCellValue('A1', 'Laporan Realisasi ' . $kab[0]->NamaKabupaten);
		foreach (range('A', 'G') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
			 $this->excel->getActiveSheet()->getStyle($char)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$this->excel->getActiveSheet()->getColumnDimension(A)->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension(B)->setWidth(35);
		$this->excel->getActiveSheet()->getColumnDimension(G)->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:G1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A2:G2');
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->mergeCells('B4:B5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Menu');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:C5');
		$this->excel->getActiveSheet()->setCellValue('D4', 'Realisasi');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('D4:F4');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G4', 'Permasalahan');
		$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D5', 'Realisasi Dana');
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E5', 'Persentase');
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F5', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
		
		
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleArrayHead);
        $this->excel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArrayHead);
        $i = 6;
        $no = 1;
        $realisasi=0;
        $fisik = 0;
        $pagu_seluruh;

        foreach ($hasil as $row) {
        	$pagu_seluruh = $row->pagu_seluruh;
            $fisik += $row->fisik;
            $realisasi+= $row->realisasi;
        	$this->excel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray);
        	$this->excel->getActiveSheet()->setCellValue('A'.$i , $no);
        	$this->excel->getActiveSheet()->setCellValue('B'.$i , $row->nama_menu);
        	$this->excel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setWrapText(true);
            $this->excel->getActiveSheet()->setCellValue('C'.$i , number_format($row->PAGU));
            $this->excel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('D'.$i , number_format($row->realisasi));
            $this->excel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->setCellValue('E'.$i , $row->persentase);
            $this->excel->getActiveSheet()->setCellValue('F'.$i , $row->fisik);
            $this->excel->getActiveSheet()->setCellValue('G'.$i , $row->Masalah);
            $no++;
            $i++;

        }
        $this->excel->getActiveSheet()->setCellValue('A'.$i , "Total Realisasi");
        $this->excel->getActiveSheet()->mergeCells('A'.$i .':B'.$i);
        $this->excel->getActiveSheet()->setCellValue('C'.$i , number_format($pagu_seluruh));
        $this->excel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('D'.$i , number_format($realisasi));
        $this->excel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $number = (float)$realisasi*100/$pagu_seluruh;
        $fisik = $fisik / ($no -1);
        $this->excel->getActiveSheet()->setCellValue('E'.$i, round($number, 2) . "%");
        $this->excel->getActiveSheet()->setCellValue('F'.$i , round($fisik, 2));
        $this->excel->getActiveSheet()->setCellValue('G'.$i , "");
        $this->excel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray2);
		$filename='Laporan Realisasi Non Fisik'. $kab[0]->NamaKabupaten .'_.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}
	function cetak_realisasi_nf2($p,$k, $j, $waktu_laporan){
		$tahun = $this->session->userdata('thn_anggaran');
		// $hasil = $this->pm->get_monev_detail_nf($p,$k,$waktu_laporan,$tahun)->result();
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Laporan Realisai');
		$kab = $this->pm->get_where_double('ref_kabupaten',$p, 'KodeProvinsi', $k , 'KodeKabupaten')->result();
		$dak = $this->pm->get_where_double('dak_nf',$j, 'id_dak_nf', $tahun , 'TAHUN_ANGGARAN')->result();
		$this->excel->getActiveSheet()->setCellValue('A1', 'Laporan Realisasi NF ' . $dak[0]->nama_dak_nf . " -" .$kab[0]->NamaKabupaten);
		foreach (range('A', 'G') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
			 $this->excel->getActiveSheet()->getStyle($char)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$this->excel->getActiveSheet()->getColumnDimension(A)->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension(B)->setWidth(35);
		$this->excel->getActiveSheet()->getColumnDimension(G)->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:G1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A2:G2');
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->mergeCells('B4:B5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Menu');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:C5');
		$this->excel->getActiveSheet()->setCellValue('D4', 'Realisasi');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('D4:F4');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G4', 'Permasalahan');
		$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D5', 'Realisasi Dana');
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E5', 'Persentase');
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F5', 'Volume');
		$this->excel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
		 $this->excel->getActiveSheet()->mergeCells('G4:G5');
		
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($styleArrayHead);
        $this->excel->getActiveSheet()->getStyle('A5:G5')->applyFromArray($styleArrayHead);
        $i = 6;
        $no = 1;
        $realisasi=0;
        $fisik = 0;
        $pagu_seluruh = 0;
        $hasil = $this->pm->get_laporan_nf($p,$k,$j,$waktu_laporan,$tahun)->result();
        // print_r($hasil);
        // exit();
        if($hasil){
        	foreach ($hasil as $row) {
        		$this->excel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray);
		        $this->excel->getActiveSheet()->setCellValue('A'.$i , $no);
		       	$this->excel->getActiveSheet()->setCellValue('B'.$i , $row->nama_menu);
		       	$fisik += $row->fisik;
            	$realisasi+= $row->realisasi;
            	$pagu_seluruh += $row->jumlah;
            	$this->excel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setWrapText(true);
	            $this->excel->getActiveSheet()->setCellValue('C'.$i , number_format($row->jumlah));
	            $this->excel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	            $this->excel->getActiveSheet()->setCellValue('D'.$i , number_format($row->realisasi));
	            $this->excel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	            $this->excel->getActiveSheet()->setCellValue('E'.$i , $row->persentase);
	            $this->excel->getActiveSheet()->setCellValue('F'.$i , $row->fisik);
	            if($row->KodeMasalah != 0){
	            	$m = $this->pm->get_where("permasalahan_dak", $row->KodeMasalah , "KodeMasalah")->result();
	            	$this->excel->getActiveSheet()->setCellValue('G'.$i , $m[0]->Masalah);
	            	$this->excel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setWrapText(true);
	            }
	            else{
	            	 $this->excel->getActiveSheet()->setCellValue('G'.$i , 'Tidak ada Masalah');
	            }
	           
	            $no++;
	            $i++;
        	}
        }
        // $this->getActiveSheet()->getColumnDimension('G')->getAlignment()->setWrapText(true); 
        $this->excel->getActiveSheet()->setCellValue('A'.$i , "Total Realisasi");
        $this->excel->getActiveSheet()->mergeCells('A'.$i .':B'.$i);
        $this->excel->getActiveSheet()->setCellValue('C'.$i , number_format($pagu_seluruh));
        $this->excel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->setCellValue('D'.$i , number_format($realisasi));
        $this->excel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $number = (float)$realisasi*100/$pagu_seluruh;
        $this->excel->getActiveSheet()->setCellValue('E'.$i, round($number, 2) . "%");
        $this->excel->getActiveSheet()->setCellValue('F'.$i , $fisik);
        $this->excel->getActiveSheet()->setCellValue('G'.$i , "");
        $this->excel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray2);
		$filename='Laporan Realisasi Non Fisik'. $kab[0]->NamaKabupaten . '-'. $dak[0]->nama_dak_nf .'_.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function update_menu(){
		$id=$this->input->post('id');
		$level=$this->input->post('level');
		$nama=$this->input->post('nama');
		$kategori=$this->input->post('kategori');
		$subbidang=$this->input->post('subbidang');		

        // $this->output->enable_profiler(TRUE);
		if($level == 1){
			$tabel = 'menu_lv1';
		}else if ($level == 2){
			$tabel = 'menu_lv2';
		}else if ($level == 3){
			$tabel = 'menu_lv3';
		}

		$datamenu = array(
			'NAMA' =>$nama
		);
		
		$this->pm->update($tabel, $datamenu, 'ID_MENU', $id);
		redirect('e-monev/e_dak/view_menu?jenis_dak='.$subbidang.'&kategori='.$kategori.'&level=-');
		
	}
	function tambah_juknis()
	{


		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$data['e_monev'] = "";
		$data['jenis_dak'] = $option_jenis_dak;
		$data['judul'] = 'View pagu';
		$this->load->view('metronic/e-monev/tambah_juknis',$data);
		
	}
	function tambah_menu()
	{

		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_sarpras['0'] = '-- Pilih Sarpras    --';
		foreach ($this->pm->get('jenis_sarpras')->result() as $row){
			$option_sarpras[$row->ID_SARPRAS] = $row->NAMA_SARPRAS;
		}	

		$data['jenis_dak'] = $option_jenis_dak;
		$data['kategori'] = $option_kategori;
		$data['sarpras'] = $option_sarpras;			
		$data['e_monev'] = "";
		$data['judul'] = 'View pagu';
		$this->load->view('metronic/e-monev/tambah_menu',$data);
		
	}	

	function tambah_menu2()
	{
      	$jenis_dak=$this->input->post('jenis_dak');
      	$kategori=$this->input->post('kategori');

		$option_menu_lv1['0'] = '-- Pilih level 1  --';
		foreach ($this->pm->get_where_double('menu_lv1',$kategori,'ID_KATEGORI',$jenis_dak,'ID_SUBBIDANG')->result() as $row){
			$option_menu_lv1[$row->ID_MENU] = $row->NAMA;
		}
		$option_menu_lv2['0'] = '-- Pilih level 2  --';
		foreach ($this->pm->get_where_double('menu_lv2',$kategori,'ID_KATEGORI',$jenis_dak,'ID_SUBBIDANG')->result() as $row){
			$option_menu_lv2[$row->ID_MENU] = $row->NAMA;
		}
		$option_menu_lv3['0'] = '-- Pilih level 3  --';
		foreach ($this->pm->get_where_double('menu_lv3',$kategori,'ID_KATEGORI',$jenis_dak,'ID_SUBBIDANG')->result() as $row){
			$option_menu_lv3[$row->ID_MENU] = $row->NAMA;
		}	
		$option_satuan['0'] = '-- Pilih Satuan    --';
		foreach ($this->pm->get('ref_satuan')->result() as $row){
			$option_satuan[$row->KodeSatuan] = $row->Satuan;
		}
		$option_sarpras['0'] = '-- Pilih Sarpras    --';
		foreach ($this->pm->get('jenis_sarpras')->result() as $row){
			$option_sarpras[$row->ID_SARPRAS] = $row->NAMA_SARPRAS;
		}						
		$data['LVL1'] = $option_menu_lv1;
		$data['LVL2'] = $option_menu_lv2;
		$data['LVL3'] = $option_menu_lv3;
		$data['sarpras'] = $option_sarpras;	
		$data['satuan'] = $option_satuan;					
		$data['e_monev'] = "";
		$data['judul'] = 'View pagu';
		$this->load->view('metronic/e-monev/tambah_menu2',$data);
		
	}	

	function save_juknis(){

      	$nama=$this->input->post('nama');
      	$jenis_dak=$this->input->post('jenis_dak');
      	$jenis_kegiatan=$this->input->post('jenis_kegiatan');
      	$sub_kegiatan=$this->input->post('sub_kegiatan');
      	$tingkat=$this->input->post('tingkat');	
      	if($tingkat==1){

        $tingkatan='dak_jenis_kegiatan';
        $no_urut=$this->pm->get_last_menu($tingkatan,'ID_JENIS_DAK='.$jenis_dak)->row()->NO_URUT;
        $no_urut++;
      	$data2 = array(
        	"NO_URUT"=> $no_urut,
       		"JENIS_KEGIATAN"=> $nama,
            "ID_JENIS_DAK"=> $jenis_dak,
        );
      	}else if($tingkat==2){
      	$tingkatan='dak_sub_jenis_dak';
        $no_urut=$this->pm->get_last_menu($tingkatan,'ID_JENIS_DAK='.$jenis_kegiatan)->row()->NO_URUT;
        $no_urut++;      		
      	$data2 = array(
        	"NO_URUT"=> $no_urut,
       		"JENIS_DAK"=> $nama,
            "ID_JENIS_DAK"=> $jenis_kegiatan
        );
      	}else if($tingkat==3){
      	$tingkatan='dak_ss_jenis_kegiatan';	
        $no_urut=$this->pm->get_last_menu($tingkatan,'ID_SUB_JENIS_DAK='.$sub_kegiatan)->row()->NO_URUT;
        $no_urut=$no_urut++;        		
      	$data2 = array(
        	"NO_URUT"=> $no_urut,
       		"JENIS_KEGIATAN"=> $nama,
            "ID_SUB_JENIS_DAK"=> $sub_kegiatan
        );            		
      	}

        $insert = $this->db->insert($tingkatan,$data2);
       // redirect('e-monev/e_dak/tambah_juknis/');
	}


	function save_menu(){

      	$nama=$this->input->post('nama');
      	$jenis_dak=$this->input->post('jenis_dak');
      	$kategori=$this->input->post('kategori');
      	$level=$this->input->post('level');
      	$sarpras=$this->input->post('sarpras');
      	//$tahun=$this->session->userdata('thn_anggaran');	
      	$tahun=$this->session->userdata('thn_anggaran');	
   		
      	$data2 = array(
       		"NAMA"=> $nama,
            "ID_SUBBIDANG"=> $jenis_dak,
            "ID_KATEGORI"=> $kategori,
            "ID_SARPRAS"=> $sarpras,
            "TAHUN"=> $tahun
        );            		
      	if($level==1){
      		$tingkatan='menu_lv1';
      	}else if($level==2){
      		$tingkatan='menu_lv2';
      	}else if($level==3){
      		$tingkatan='menu_lv3';
      	}

        $insert = $this->db->insert($tingkatan,$data2);
        redirect('e-monev/e_dak/view_menu?jenis_dak='.$jenis_dak.'&kategori='.$kategori.'&level='.$level);
	}

	function save_menu2(){

      	$nama=$this->input->post('nama');
      	$lv1=$this->input->post('lv1');
      	$lv2=$this->input->post('lv2');
      	$lv3=$this->input->post('lv3');
      	$jenis_dak=$this->input->post('jenis_dak');
      	$sarpras=$this->input->post('sarpras');
      	$satuan=$this->input->post('satuan');
      	$kategori=$this->input->post('kategori');
      	if($lv1==0)$lv1=null;
      	if($lv2==0)$lv2=null;
      	if($lv3==0)$lv3=null;
        if($lv2==null && $lv3==null){
        	$level=1;
        }else if($lv2!=null && $lv3==null){
        	$level=2;
        }else if($lv2!=null && $lv3!=null){
        	$level=3;	
        }
      	//$tahun=$this->session->userdata('thn_anggaran');	
      	$tahun=2017;	
   		
      	$data2 = array(
       		"NAMA"=> $nama,
            "ID_SUBBIDANG"=> $jenis_dak,
            "ID_KATEGORI"=> $kategori,
            "ID_SARPRAS"=> $sarpras,
            "LV1"=> $lv1,
            "LV2"=> $lv2, 
            "LV3"=> $lv3,
            "KodeSatuan"=> $satuan,
            "LEVEL"=> $level,                       
            "TAHUN"=> $tahun
        );            		

        $insert = $this->db->insert('menu',$data2);
        redirect('e-monev/e_dak/view_menu?jenis_dak='.$jenis_dak.'&kategori='.$kategori.'&level=-');
	}
	function daftar_menu($d,$k){
		$lv1=array();
		$lv2=array();
		$lv3=array();
		$data=array();
		$nama=null;
		$no=1;
		$menu=$this->pm->get_where_double('menu',$d,'ID_SUBBIDANG',$k,'ID_KATEGORI')->result();
		echo "[";
		foreach($menu as $row){
			if((!in_array($row->LV1, $lv1)) && ($row->LV1!=null) ){

				$nama=$this->pm->get_where('menu_lv1',$row->LV1,'ID_MENU')->row()->NAMA;
				$lvl2=$this->pm->get_where('menu',$row->LV1,'LV1')->result();
				$rw[]= '{"NO":'.$no.',"NAMA":"'.$nama.'","AKSI":"","LEVEL":"1","ID":"'.$row->LV1.'","ID_KATEGORI":"'.$row->ID_KATEGORI.'","ID_SUBBIDANG":"'.$row->ID_SUBBIDANG.'"},';
				$no++;	
				foreach($lvl2 as $row2){
					if((!in_array($row2->LV2, $lv2)) && ($row2->LV2!=null) ){

						$nama=$this->pm->get_where('menu_lv2',$row2->LV2,'ID_MENU')->row()->NAMA;
						$lvl3=$this->pm->get_where('menu',$row2->LV2,'LV2')->result();
						$rw[]= '{"NO":'.$no.',"NAMA":"&nbsp&nbsp'.$nama.'","AKSI":"","LEVEL":"2","ID":"'.$row2->LV2.'","ID_KATEGORI":"'.$row->ID_KATEGORI.'","ID_SUBBIDANG":"'.$row->ID_SUBBIDANG.'"},';
						$no++;		
							foreach($lvl3 as $row3){
								if((!in_array($row3->LV3, $lv3)) && ($row3->LV3!=null) ){									
									$nama=$this->pm->get_where('menu_lv3',$row3->LV3,'ID_MENU')->row()->NAMA;
									$rw[]= '{"NO":'.$no.',"NAMA":"&nbsp&nbsp&nbsp&nbsp'.$nama.'","AKSI":"","LEVEL":"3","ID":"'.$row3->LV3.'","ID_KATEGORI":"'.$row->ID_KATEGORI.'","ID_SUBBIDANG":"'.$row->ID_SUBBIDANG.'"},';
									$no++;	
								}								
								$lv3[]=$row->LV3;
							}										
					}
					
					$lv2[]=$row->LV2;
				}

			}
			
			$lv1[]=$row->LV1;
		}
		if($nama !=null){
			end($rw);         // move the internal pointer to the end of the array
			$key = key($rw);
			$rw[$key]=substr_replace($rw[$key], '', -1);
		}
		echo implode($rw);
		echo "]";

	}

	function print_menu($d,$k){
		$header=$this->pm->get_where_double_order('menu',$d,'ID_SUBBIDANG',$k,'ID_KATEGORI','ID_SARPRAS')->result();
		$idk=array();
		$sarpras=array();
		$awal='A';
		$akhir='B';
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Menu Kegiatan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh ');	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'A2F5FF')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A9', 'PAGU');
		$this->excel->getActiveSheet()->getStyle('A9')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('A9:A11');
		$this->excel->getActiveSheet()->getStyle('A9:A11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('B9', 'DANA PENDAMPING');
		$this->excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('B9:B11');
		$this->excel->getActiveSheet()->getStyle('B9:B11')->applyFromArray($styleHeader);		
		$this->excel->getActiveSheet()->setCellValue('C9', 'TOTAL');
		$this->excel->getActiveSheet()->getStyle('C9')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('C9:C11');
		$this->excel->getActiveSheet()->getStyle('C9:C11')->applyFromArray($styleHeader);		
		$klm=3;
		$klm1=3;
		$i=1;
		foreach ($header as $row){
			
			if(empty($sarpras)){
				$nama=$this->pm->get_where('jenis_sarpras',$row->ID_SARPRAS,'ID_SARPRAS')->row()->NAMA_SARPRAS;
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'9',  $nama);
				$this->excel->getActiveSheet()->getStyle($kolom[$klm1].'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$awal[$i]=$kolom[$klm];
			}
			if((!empty($sarpras))&&(!in_array($row->ID_SARPRAS, $sarpras)))	{
				$akhir=$klm-1;
				$this->excel->getActiveSheet()->mergeCells($awal[$i].'9:'.$kolom[$akhir].'9');
				$this->excel->getActiveSheet()->getStyle($awal[$i].'9:'.$kolom[$akhir].'9')->applyFromArray($styleHeader);	
				$nama=$this->pm->get_where('jenis_sarpras',$row->ID_SARPRAS,'ID_SARPRAS')->row()->NAMA_SARPRAS;
				$this->excel->getActiveSheet()->getStyle($kolom[$klm1].'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'9',  $nama);
				$i++;
				$awal[$i]=$kolom[$klm];

			}	

			$sarpras[]=$row->ID_SARPRAS;
			$klm1=$klm;


			$this->excel->getActiveSheet()->setCellValue($kolom[$klm1].'10',  $row->NAMA);
			$this->excel->getActiveSheet()->getStyle($kolom[$klm1].'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'11',  'USULAN VOLUME');
			$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm])->setWidth(20);
			$klm ++;
			$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'11',  'USULAN LOKASI');
			$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm])->setWidth(20);
			$klm ++;
			$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'11',  'VOLUME');
			$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm])->setWidth(20);
			$klm ++;
			$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'11',  'UNIT COST');
			$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm])->setWidth(20);
			$klm ++;
			$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'11',  'JUMLAH');
			$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm])->setWidth(20);
			$this->excel->getActiveSheet()->mergeCells($kolom[$klm1].'10:'.$kolom[$klm].'10');
			$this->excel->getActiveSheet()->getStyle($kolom[$klm1].'10:'.$kolom[$klm].'10')->applyFromArray($styleHeader);
			$this->excel->getActiveSheet()->getStyle($kolom[$klm1].'11:'.$kolom[$klm].'11')->applyFromArray($styleHeader);	
			$akhir=$klm;
			$klm ++; 


		}
		if((!empty($sarpras)))	{
			$akhir=$klm-1;
			$this->excel->getActiveSheet()->mergeCells($awal[$i].'9:'.$kolom[$akhir].'9');
			$this->excel->getActiveSheet()->getStyle($awal[$i].'9:'.$kolom[$akhir].'9')->applyFromArray($styleHeader);	
		}			
		/*
		end($kolom);         // move the internal pointer to the end of the array
		$key = key($kolom);
		$akhir=$kolom[$key];		
		$this->excel->getActiveSheet()->mergeCells($awal.'9:'.$akhir.'9');
*/
		$filename='menu.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');					

	}

	function print_menu2(){
		$d=$this->input->post('jenis_dak2');
		$k=$this->input->post('kategori2');
		$menus=$_POST['menu'];
		$header=$this->pm->get_menu_dak($d,'ID_SUBBIDANG',$k,'ID_KATEGORI',$menus)->result();
		$idk=array();
		$sarpras=array();
		$awal='A';
		$akhir='B';
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Menu Kegiatan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh ');	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'A2F5FF')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A10', 'ID MENU');
		$this->excel->getActiveSheet()->getStyle('A10')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A10:A11');
		$this->excel->getActiveSheet()->getStyle('A10:A11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('B10', 'Menu DAK kesehatan');
		$this->excel->getActiveSheet()->getStyle('B10')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('B10:B11');
		$this->excel->getActiveSheet()->mergeCells('B10:C10');	
		$this->excel->getActiveSheet()->getStyle('B10:C10')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('B11:C11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->mergeCells('B10:C10');				
		$this->excel->getActiveSheet()->getStyle('B10:B11')->applyFromArray($styleHeader);		
		$this->excel->getActiveSheet()->setCellValue('D10', 'Usulan Volume');
		$this->excel->getActiveSheet()->getStyle('D10')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('D10:D11');
		$this->excel->getActiveSheet()->getStyle('D10:D11')->applyFromArray($styleHeader);	
		$this->excel->getActiveSheet()->setCellValue('E10', 'Usulan Lokasi');
		$this->excel->getActiveSheet()->getStyle('E10')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$this->excel->getActiveSheet()->mergeCells('E10:E11');
		$this->excel->getActiveSheet()->getStyle('E10:E11')->applyFromArray($styleHeader);	
		$this->excel->getActiveSheet()->setCellValue('F10', 'RKA DAK 2017');
		$this->excel->getActiveSheet()->getStyle('F10')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$this->excel->getActiveSheet()->setCellValue('F11', 'Volume');
		$this->excel->getActiveSheet()->getStyle('F11')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);		
		$this->excel->getActiveSheet()->getStyle('F10:F11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('G11', 'Unit Cost (rata-rata)');
		$this->excel->getActiveSheet()->getStyle('G11')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->excel->getActiveSheet()->getStyle('G10:G11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('H11', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle('H11')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$this->excel->getActiveSheet()->getStyle('H10:H11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('I11', 'Lokasi');
		$this->excel->getActiveSheet()->getStyle('I11')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$this->excel->getActiveSheet()->getStyle('I10:I11')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->mergeCells('F10:I10');		



		$brs=12;
		$i=1;
		foreach ($header as $row){
			
			if(empty($sarpras)){
				$nama=$this->pm->get_where('jenis_sarpras',$row->ID_SARPRAS,'ID_SARPRAS')->row()->NAMA_SARPRAS;
				$this->excel->getActiveSheet()->setCellValue('B'.$brs,  $nama);
				$this->excel->getActiveSheet()->getStyle('B'.$brs)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$awal[$i]=$brs;
			}
			if((!empty($sarpras))&&(!in_array($row->ID_SARPRAS, $sarpras)))	{
				$akhir=$brs-1;
				//$this->excel->getActiveSheet()->mergeCells('B'.$awal[$i].':B'.$akhir);
				//$this->excel->getActiveSheet()->getStyle('B'.$awal[$i].':B'.$akhir)->applyFromArray($styleHeader);	
				$nama=$this->pm->get_where('jenis_sarpras',$row->ID_SARPRAS,'ID_SARPRAS')->row()->NAMA_SARPRAS;
				$this->excel->getActiveSheet()->getStyle('B'.$awal[$i])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->setCellValue('B'.$brs,  $nama);
				$i++;
				$awal[$i]=$brs;

			}	

			$sarpras[]=$row->ID_SARPRAS;
			

            $this->excel->getActiveSheet()->setCellValue('A'.$brs,  $row->ID_MENU);
			$this->excel->getActiveSheet()->setCellValue('C'.$brs,  $row->NAMA);
			$brs++;


		}
		/*if((!empty($sarpras)))	{
			$this->excel->getActiveSheet()->mergeCells('B'.$awal[$i].':B'.$akhir);
			$this->excel->getActiveSheet()->mergeCells($awal.'9:'.$akhir.'9');
		}*/			
			
		

		$filename='menu.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');					

	}


function daftar_menu2($d,$k){
		$no=0;
		$query=$this->pm->get_where_double('menu',$d,'ID_SUBBIDANG',$k,'ID_KATEGORI');
		$i=0;
		
		if($query->num_rows() >0){
			foreach($query->result() as $row)
			{	
				$datajson[$i]['NAMA'] = $row->NAMA;
				$datajson[$i]['ID_MENU'] = $row->ID_MENU;
				$i++;
			}
		}
		else{	
			$datajson[0]['ID_MENU'] = '0';
			$datajson[0]['NAMA'] = 'Tidak ada ';
	}


	echo json_encode($datajson);

}

public function getJsonDetailMonevTerinput($id='')
{
	$where = array(
					'id_data_menu' => $id
				);
	$dataSmstr = $this->bm->select_where_array('data_monev_rka_2020', $where)->row_array();

		$satuan  = $this->pm->get('ref_satuan')->result();


			foreach ($satuan as $sat) { 
				if ($sat->KodeSatuan ==   $dataSmstr ['kd_satuan']  ){
					$satuannya = $sat->Satuan;
				} 
				
			} 

	$datajson[0]['id']            = $dataSmstr ['id_data_menu'];
	// $datajson[0]['menu']       = $pagu ['detail_menu'];
	// $datajson[0]['id']         = $dataSmstr ['id'];
	$datajson[0]['volume']        = $dataSmstr ['volume'];
	$datajson[0]['kegiatan']      = $dataSmstr ['detail_menu'];
	$datajson[0]['pagu']          = number_format($dataSmstr ['pagu'],0,',',',');
	$datajson[0]['volume_p']      = $dataSmstr ['volume_ril'];
	$datajson[0]['satuan']        = $satuannya;
	$datajson[0]['realisasi']     = number_format($dataSmstr ['realisasi'],0,',',',');
	$datajson[0]['output']        = $dataSmstr ['fisik'];
	$datajson[0]['realisasi_kon'] = number_format($dataSmstr ['realisasi_k'],0,',',',');
	$datajson[0]['volume_kon']    = $dataSmstr ['volume_k'];


	echo json_encode($datajson);

}
function getJsonDetailMonevPenunjanng($id='')
{

	$dataSmstr = $this->pm->getDetailPenunjangInput($id)->row_array();

	$satuan  = $this->pm->get('ref_satuan')->result();


			foreach ($satuan as $sat) { 
				if ($sat->KodeSatuan ==   $dataSmstr ['kd_satuan']  ){
					$satuannya = $sat->Satuan;
				} 
				
			} 
			


	$datajson[0]['id']            = $dataSmstr ['id'];
	$datajson[0]['volume']        = $dataSmstr ['volume'];
	$datajson[0]['kegiatan']      = $dataSmstr ['penunjang'];
	$datajson[0]['usulan']        = number_format($dataSmstr ['usulan'],0,',',',');
	$datajson[0]['volume_p']      = $dataSmstr ['volume_p'];
	$datajson[0]['satuan']        = $satuannya;
	$datajson[0]['realisasi']     = number_format($dataSmstr ['realisasi'],0,',',',');
	$datajson[0]['output']        = $dataSmstr ['output'];
	$datajson[0]['realisasi_kon'] = number_format($dataSmstr ['realisasi_kon'],0,',',',');
	$datajson[0]['volume_kon']    = $dataSmstr ['volume_kon'];
	// $datajson[0]['id'] = $dataSmstr ['id'];
	// $datajson[0]['id'] = $dataSmstr ['id'];
	// $datajson[0]['id'] = $dataSmstr ['id'];


	echo json_encode($datajson);
}
public function updateMonevFisikSave($value='')
{
	$verid         = $_POST['verid'];
	$volume_kon    = $_POST['volume_kon'];
	$realisasi_kon = $_POST['realisasi_kon'];
	
	$realisasi     = $_POST['realisasi'];
	$volume        = $_POST['volume'];
	$output        = $_POST['output'];

		$datax = array(
					'id_data_menu' => $verid,
					'volume_k'     => $volume_kon,
					'realisasi_k'  => str_replace(",", "", $realisasi_kon),
					'realisasi'    => str_replace(",", "", $realisasi),
					'volume_ril'   => $volume,
					'fisik'        => $output
				);

		$idx    = 'id_data_menu';
		$tablex = 'data_monev_rka_2020';

		// print_r($datax);

		$this->pm->updateData($idx,$tablex,$datax);

}
public function updatepenunjangSave($value='')
{
	$verid         = $_POST['verid'];
	$volume_kon    = $_POST['volume_kon'];
	$realisasi_kon = $_POST['realisasi_kon'];
	
	$realisasi     = $_POST['realisasi'];
	$volume        = $_POST['volume'];
	$output        = $_POST['output'];

		$datax = array(
					'id'            => $verid,
					'volume_kon'    => $volume_kon,
					'realisasi_kon' => str_replace(",", "", $realisasi_kon),
					'realisasi'     => str_replace(",", "", $realisasi),
					'volume'        => $volume,
					'output'        => str_replace(".00", "", $output),
					'status_edit'   => '1'
				);

		$idx='id';
		$tablex='dak_penunjang_input';

		// print_r($datax);
		$this->pm->updateData($idx,$tablex,$datax);
}
	function daftar_menu_lvl($d,$k,$level){
		$no=0;
		if($level==1){
			$tabel="menu_lv1";
			$l='1';
		}else if($level==2){
			$tabel="menu_lv2";
			$l='2';
		}else if($level==3){
			$tabel="menu_lv3";
			$l='3';
		}
		$menu=$this->pm->get_where_double($tabel,$d,'ID_SUBBIDANG',$k,'ID_KATEGORI')->result();
		foreach ($menu as $mn) {
			$no++;
			$mnu[] = array("NO" => $no, "NAMA" => $mn->NAMA, "AKSI" => "", "LEVEL" => $l , "ID" =>  $mn->ID_MENU , "ID_KATEGORI" =>  $mn->ID_KATEGORI, "ID_SUBBIDANG" =>  $mn->ID_SUBBIDANG);
			
		}
		echo json_encode($mnu);

	}




	function daftar_pagu_seluruh($p,$k,$jenis_dak,$kategori){
		$datajson = array();
		if($jenis_dak !='1' && $jenis_dak !='10' && $jenis_dak !='11' && $jenis_dak !='20' && $jenis_dak !='23'){
			$i=0;
			$no=1;
			$menu=$this->pm->get_pagu_3($p,$k,$jenis_dak,$kategori);
				if ($menu->num_rows>0){
					foreach($menu->result() as $row)
					{	$datajson[$i]['NO'] = $no;
						$datajson[$i]['ID_PAGU'] = $row->id_pagu;
						$datajson[$i]['nmrs'] = $row->id_pagu;
						$datajson[$i]['PAGU'] = number_format($row->pagu_seluruh);
						$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
						$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
						$datajson[$i]['AKSI'] = '';
						$i++;
						$no++;

					}
				}

		}
		else{
			$i=0;
			$no=1;
			$where = array(
				'KodeProvinsi' => $p,
				'KodeKabupaten' => $k,
				'ID_Jenis_DAK' => $jenis_dak,
				'TAHUN_ANGGARAN' => $this->session->userdata('thn_anggaran')
			);
			$menu=$this->bm->select_where_array('pagu_rs', $where);
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	$datajson[$i]['NO'] = $no;
					$datajson[$i]['ID_PAGU'] = $row->id;
					$datajson[$i]['nmrs'] = $this->pm->get_where('data_rumah_sakit', $row->KODE_RS, 'KODE_RS')->row()->NAMA_RS;
					$datajson[$i]['PAGU'] = number_format($row->PAGU_SELURUH);
					$datajson[$i]['NamaKabupaten'] = $this->pm->get_where_double('ref_kabupaten', $row->KodeProvinsi, 'KodeProvinsi', $row->KodeKabupaten, 'KodeKabupaten')->row()->NamaKabupaten;
					$datajson[$i]['NamaProvinsi'] = $this->pm->get_where('ref_provinsi', $row->KodeProvinsi, 'KodeProvinsi')->row()->NamaProvinsi;
					$datajson[$i]['AKSI'] = '';
					$i++;
					$no++;

				}
			}
		}
		

		echo json_encode($datajson);

	}

	function daftar_pagu_rs($p,$k,$jenis_dak,$kategori, $rs){
		$i=0;
		$no=1;
		$menu=$this->pm->get_pagu_rs($p,$k,$jenis_dak,$kategori, $rs);
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	$datajson[$i]['NO'] = $no;
					$datajson[$i]['ID_PAGU'] = $row->id;
					$datajson[$i]['PAGU'] = number_format($row->PAGU_SELURUH);
					$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
					$datajson[$i]['RS'] = $row->NAMA_RS;
					$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
					$datajson[$i]['AKSI'] = '';
					$i++;
					$no++;

				}
			}
		echo json_encode($datajson);

	}
	

	function daftar_laporan_seluruh($p,$k,$jenis_dak,$kategori,$waktu_laporan){
		
		$i=0;
		$no=1;
		$menu=$this->pm->get_pagu_4($p,$k,$jenis_dak,$kategori,$waktu_laporan);
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	$datajson[$i]['NO'] = $no;
					$datajson[$i]['ID_PAGU'] = $row->id_pagu;
					$datajson[$i]['REALISASI'] = $row->realisasi;
					$datajson[$i]['FISIK'] = round($row->fisik,2);
					$datajson[$i]['PERSENTASE'] = round($row->persentase,2);
					$datajson[$i]['PAGU'] = $row->pagu_seluruh;
					$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
					$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
					$datajson[$i]['AKSI'] = '';
					$i++;
					$no++;

				}
			}
		


		echo json_encode($datajson);

	}	

	function daftar_pengajuan_monev($p,$k,$jenis_dak,$kategori,$waktu_laporan, $kdrs=null){
		
		$i=0;
		$no=1;
		$tahun=$this->session->userdata('thn_anggaran');
		if($kdrs != null){
			$menu=$this->pm->get_monev_rka($p,$k,$jenis_dak,$kategori,$waktu_laporan,$tahun, $kdrs);
		}
		else{
			$menu=$this->pm->get_monev_rka($p,$k,$jenis_dak,$kategori,$waktu_laporan,$tahun);	
		}
		
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	$datajson[$i]['NO'] = $no;
					$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
					$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
					$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
					$datajson[$i]['NAMA_JENIS_DAK'] = $row->jenis_dak;
					$datajson[$i]['kategori'] = $row->kategori;
					$datajson[$i]['waktu_laporan'] = $row->waktu_laporan;					
					$datajson[$i]['RKA'] = $row->RKA;
						if (is_null($row->RKA) || $row->RKA=='') {
							$ket_rka='Belum Upload File RKA';
						} else {
							$ket_rka='Download RKA';
						}
						
						$filename_rka = 'file/'.$row->RKA;

						if (file_exists($filename_rka)) {
							$folder_rka='file';
						} else {
							$folder_rka='file_monev_fisik';
						}
						$datajson[$i]['ket_rka'] = $ket_rka;
						$datajson[$i]['folder_rka'] = $folder_rka;

					$datajson[$i]['SP2D'] = $row->rekap_sp2d;
						if (is_null($row->rekap_sp2d) || $row->rekap_sp2d=='') {
							$ket_sp2d='Belum Upload File sp2d';
						} else {
							$ket_sp2d='Download';
						}
						
						$filename_sp2d = 'file/'.$row->rekap_sp2d;

						if (file_exists($filename_sp2d)) {
							$folder_sp2d='file';
						} else {
							$folder_sp2d='file_monev_fisik';
						}

						$datajson[$i]['ket_sp2d'] = $ket_sp2d;
						$datajson[$i]['folder_sp2d'] = $folder_sp2d;

					$datajson[$i]['DOKUMENTASI'] = $row->Dokumentasi;
						if (is_null($row->Dokumentasi) || $row->Dokumentasi=='') {
							$ket_Dokumentasi='Belum Upload File Dokumentasi';
						} else {
							$ket_Dokumentasi='Download';
						}
						
						$filename_Dokumentasi = 'file/'.$row->Dokumentasi;

						if (file_exists($filename_Dokumentasi)) {
							$folder_Dokumentasi='file';
						} else {
							$folder_Dokumentasi='file_monev_fisik';
						}

						$datajson[$i]['ket_Dokumentasi'] = $ket_Dokumentasi;
						$datajson[$i]['folder_Dokumentasi'] = $folder_Dokumentasi;

					$datajson[$i]['data_lain'] = $row->data_lain;

						if (is_null($row->data_lain) || $row->data_lain=='') {
							$ket_lain='Belum Upload File lain';
						} else {
							$ket_lain='Download';
						}
						
						$filename_lain = 'file/'.$row->data_lain;

						if (file_exists($filename_lain)) {
							$folder_lain='file';
						} else {
							$folder_lain='file_monev_fisik';
						}

						$datajson[$i]['ket_lain'] = $ket_lain;
						$datajson[$i]['folder_lain'] = $folder_lain;


					$datajson[$i]['RS'] = '-';
					if($row->KD_RS){
						if($row->KD_RS != 0){
							$rs = $this->pm->get_where("data_rumah_sakit", $row->KD_RS, "KODE_RS")->result();
							if($rs != null){
								$datajson[$i]['RS'] = $rs[0]->NAMA_RS;
							}
							else{
								$puskes = $this->pm->get_where("data_puskesmas", $row->KD_RS, "KodePuskesmas")->result();
								if($puskes != null){
									$datajson[$i]['RS'] = $puskes[0]->NamaPuskesmas;	
								}
							}
						}

					}
					if($this->session->userdata('kd_role') == 27){
						$datajson[$i]['AKSI'] = '';
					}
					else{
						$datajson[$i]['AKSI'] = '<button data-id="'.$row->id_pengajuan.'" data-placement="top" title="Hapus Laporan" class="btn btn-default btn-hapus-laporan"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></button>';	
					}
					
					$i++;
					$no++;

				}
			}
		echo json_encode($datajson);

	}

	function daftar_pengajuan_nf($p, $k, $waktu_laporan){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_nf($p, $k, $waktu_laporan, $tahun);
		$no=1;
		$i=0;
		if($hasil->num_rows() != 0){
			foreach ($hasil->result() as $row) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
				$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
				$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
				$datajson[$i]['waktu_laporan'] = $row->waktu_laporan;					
				$datajson[$i]['RKA'] = $row->data_pendukung1;
				$datajson[$i]['SP2D'] = $row->data_pendukung2;
				$datajson[$i]['DOKUMENTASI'] = $row->data_pendukung3;
				$datajson[$i]['DATA_LAIN'] = $row->data_pendukung4;
				$datajson[$i]['AKSI'] = '<button data-id="'.$row->id_pengajuan.'" data-placement="top" title="Hapus Laporan" class="btn btn-default btn-hapus-laporan"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></button>';
				$i++;
				$no++;
			}
		}
		echo json_encode($datajson);

	}
	function daftar_pengajuan_nf2($p, $k,$waktu_laporan, $j){
		$tahun = $this->session->userdata('thn_anggaran');
		$hasil = $this->pm->get_monev_nf2($p, $k, $j, $waktu_laporan, $tahun);
		$no=1;
		$i=0;
		$datajson = array ();
		if($hasil->num_rows() != 0){
			foreach ($hasil->result() as $row) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
				$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
				$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
				$datajson[$i]['nmrs'] = '';
				if($row->KD_RS != 0){
					$nmrs = $this->pm->get_where('data_rumah_sakit', $row->KD_RS, 'KODE_RS')->row();
					if($nmrs){
						$datajson[$i]['nmrs'] = $nmrs->NAMA_RS;
					}
				
				}
				if($row->id_dak_nf){
					$subbidang = $this->pm->get_where_double('dak_nf', $tahun, 'TAHUN_ANGGARAN', $row->id_dak_nf, 'id_dak_nf')->row()->nama_dak_nf;	
				}
				else{
					$subbidang = '';
				}
				$datajson[$i]['subbidang'] = $subbidang;
				$datajson[$i]['waktu_laporan'] = $row->waktu_laporan;


				$datajson[$i]['RKA'] = $row->data_pendukung1;

						if (is_null($row->data_pendukung1) || $row->data_pendukung1=='') {
							$ket_rka='Belum Upload File RKA';
						} else {
							$ket_rka='Laporan Asli';
						}
						
						$filename_rka = 'file/'.$row->data_pendukung1;

						if (file_exists($filename_rka)) {
							$folder_rka='file';
						} else {
							$folder_rka='file_monev_nf';
						}

						$datajson[$i]['ket_rka'] = $ket_rka;
						$datajson[$i]['folder_rka'] = $folder_rka;

				$datajson[$i]['SP2D'] = $row->data_pendukung2;

						if (is_null($row->data_pendukung2) || $row->data_pendukung2=='') {
							$ket_sp2d='Belum Upload File sp2d';
						} else {
							$ket_sp2d='Rekap SP2D';
						}
						
						$filename_sp2d = 'file/'.$row->data_pendukung2;

						if (file_exists($filename_sp2d)) {
							$folder_sp2d='file';
						} else {
							$folder_sp2d='file_monev_nf';
						}

						$datajson[$i]['ket_sp2d'] = $ket_sp2d;
						$datajson[$i]['folder_sp2d'] = $folder_sp2d;


				$datajson[$i]['DOKUMENTASI'] = $row->data_pendukung3;

						if (is_null($row->data_pendukung3) || $row->data_pendukung3=='') {
							$ket_Dokumentasi='Belum Upload File Dokumentasi';
						} else {
							$ket_Dokumentasi='Dokumentasi';
						}
						
						$filename_Dokumentasi = 'file/'.$row->data_pendukung3;

						if (file_exists($filename_Dokumentasi)) {
							$folder_Dokumentasi='file';
						} else {
							$folder_Dokumentasi='file_monev_nf';
						}

						$datajson[$i]['ket_Dokumentasi'] = $ket_Dokumentasi;
						$datajson[$i]['folder_Dokumentasi'] = $folder_Dokumentasi;

				$datajson[$i]['DATA_LAIN'] = $row->data_pendukung4;
						if (is_null($row->data_pendukung4) || $row->data_pendukung4=='') {
							$ket_lain='Belum Upload File lain';
						} else {
							$ket_lain='Data Lain';
						}
						
						$filename_lain = 'file/'.$row->data_pendukung4;

						if (file_exists($filename_lain)) {
							$folder_lain='file';
						} else {
							$folder_lain='file_monev_nf';
						}

						$datajson[$i]['ket_lain'] = $ket_lain;
						$datajson[$i]['folder_lain'] = $folder_lain;


				if($this->session->userdata('kd_role') != 27){
					$datajson[$i]['AKSI'] = '<button data-id="'.$row->id_pengajuan.'" data-placement="top" title="Hapus Laporan" class="btn btn-default btn-hapus-laporan"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></button>';
				}
				else{
					$datajson[$i]['AKSI'] = '-';
				}
				$i++;
				$no++;
			}
		}
		echo json_encode($datajson);

	}
		function laporan_dak_2($p,$k,$jenis_dak,$kategori,$waktu_laporan){
		
		$i=0;
		$no=1;
		$tahun=$this->session->userdata('thn_anggaran');
		$menu=$this->pm->get_laporan($p,$k,$jenis_dak,$kategori,$waktu_laporan,$tahun);
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	$datajson[$i]['NO'] = $no;
					$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
					$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
					$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
					$datajson[$i]['NAMA_KATEGORI'] = $row->NAMA_KATEGORI;
					$datajson[$i]['NAMA_JENIS_DAK'] = $row->NAMA_JENIS_DAK;
					$datajson[$i]['waktu_laporan'] = $row->waktu_laporan;
					$datajson[$i]['AKSI'] = '';
					$i++;
					$no++;

				}
			}
		echo json_encode($datajson);

	}

	function daftar_rka($id_pengajuan){
		
		$i=0;
		$no=1;
		$menu=$this->pm->get_where('data_monev_rka',$id_pengajuan,'id_pengajuan');
			if ($menu->num_rows>0){
				foreach($menu->result() as $row)
				{	

					$datajson[$i]['NO'] = $no;
					$datajson[$i]['id_data_menu'] = $row->id_data_menu;
					$datajson[$i]['nama_menu'] = $row->nama_menu;
					$datajson[$i]['volume'] = $row->volume;
					$datajson[$i]['satuan'] = $row->satuan;
					$datajson[$i]['unit_cost'] = $row->unit_cost;					
					$datajson[$i]['jumlah'] = $row->jumlah;
					$datajson[$i]['fisik'] = $row->fisik;
					$i++;
					$no++;

				}
			}else{
					$datajson[$i]['NO'] = '0';
					$datajson[$i]['id_data_menu'] = '0';
					$datajson[$i]['nama_menu'] = '0';
					$datajson[$i]['volume'] = '0';
					$datajson[$i]['satuan'] = '0';
					$datajson[$i]['unit_cost'] = '0';					
					$datajson[$i]['jumlah'] = '0';
					$datajson[$i]['fisik'] = '0';				
			}
		echo json_encode($datajson);

	}

	function get_monev_menu($d,$k,$provinsi,$kabupaten)
	{	//$this->output->enable_profiler(TRUE);
			// if($this->pm->cek1('ref_satker_iku','kdsatker',$this->session->userdata('kdsatker')))

		$query = $this->pm->get_menu_monev($d,$k,$provinsi,$kabupaten);

			// else
			// $query = $this->pm->get_where_double('ref_iku', $kode1, 'KodeProgram', '1', 'KodeStatus');
		$i=0;

		if($query->num_rows != 0){
			foreach($query->result() as $row)
			{
				$datajson[$i]['ID_MENU'] = $row->ID_MENU;
				$datajson[$i]['NAMA'] = $row->NAMA;
				$datajson[$i]['SATUAN'] = $row->Satuan;
				$datajson[$i]['PAGU'] = $row->PAGU;
				$datajson[$i]['JUMLAH'] = $row->sum_jumlah;
				$datajson[$i]['UNIT_COST'] = $row->sum_unit;
				$datajson[$i]['FISIK'] = round($row->fisik,2);
				$datajson[$i]['VOLUME'] = $row->sum_volume;
					// foreach($this->pm->get_where('target_iku', $row->KodeIku, 'KodeIku')->result() as $r){
					// 	$datajson[$i]['TargetNasional'] = $r->TargetNasional;
					// }
				$i++;
			}
		}else{
				$datajson[$i]['ID_MENU'] = '0';
				$datajson[$i]['NAMA'] =  '0';
				$datajson[$i]['SATUAN'] = '0';
				$datajson[$i]['PAGU'] = '0';
				$datajson[$i]['JUMLAH'] = '0';
				$datajson[$i]['UNIT_COST'] = '0';
				$datajson[$i]['FISIK'] = '0';
				$datajson[$i]['VOLUME'] = '0';
		}
		echo json_encode($datajson);
	}	

	function testes(){
		$pagu = $this->pm->get_where_quadruple("pagu", 03, "KodeProvinsi", 18, "KodeKabupaten", 2017, "TAHUN_ANGGARAN", 1 , "ID_MENU")->result();
		print_r($pagu);
	}

	function daftar_pagu($p,$k,$jenis_dak,$kategori, $rs){
		$i=0;
		$no=1;
		$tahun = $this->session->userdata("thn_anggaran");
		$datajson = array();
		$menu=$this->pm->get_pagu_2($p,$k,$jenis_dak,$kategori,$rs, $tahun)->result();
			foreach($menu as $row)
			{	$datajson[$i]['NO'] = $no;
				$datajson[$i]['ID_PAGU'] = $row->ID_PAGU;
				$datajson[$i]['NAMA'] = $row->NAMA;
				$datajson[$i]['VOLUME'] = $row->VOLUME;
				$datajson[$i]['SATUAN'] = $row->Satuan;
				$datajson[$i]['HARGA_SATUAN'] = $row->HARGA_SATUAN;
				$datajson[$i]['PAGU'] = $row->PAGU;
				$datajson[$i]['PAGU_SELURUH'] = $row->pagu_seluruh;
				$datajson[$i]['NamaKabupaten'] = $row->NamaKabupaten;
				$datajson[$i]['NamaProvinsi'] = $row->NamaProvinsi;
				$datajson[$i]['AKSI'] = '';
				$i++;
				$no++;


			}
		

		echo json_encode($datajson);

	}

	function daftar_verifikasi($p,$k,$jenis_dak,$kategori,$waktu, $kdrs){
		$i=0;
		$no=1;
		$tahun = $this->session->userdata("thn_anggaran");
		// print_r($kdrs); exit;
		$hasil=$this->pm->get_verifikasi($p,$k,$jenis_dak,$kategori,$waktu, $tahun, $kdrs);
		if($hasil->num_rows() != 0){
			foreach($hasil->result() as $row){	
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
			$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
			$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;

			if($row->KD_RS == 0 || $row->KD_RS == null){
				$datajson[$i]['RS'] = ' ';
			}
			else{
				$rs = $this->pm->get_where('data_rumah_sakit', $row->KD_RS , "KODE_RS")->result();
				if($rs){
					$datajson[$i]['RS'] = $rs[0]->NAMA_RS;
				}
				else{
					$datajson[$i]['RS'] = ' ';
				}
			}

			$datajson[$i]['JD'] = $row->NAMA_JENIS_DAK;
			$datajson[$i]['KATEGORI'] = $row->NAMA_KATEGORI;
			$datajson[$i]['TRIWULAN'] = $waktu;
			$datajson[$i]['KELENGKAPAN'] = "";

			$datajson[$i]['PENDUKUNG1'] = $row->data_pendukung1;

						$filename_1 = 'file/'.$row->data_pendukung1;

						if (file_exists($filename_1)) {
							$folder_1='file';
						} else {
							$folder_1='file_monev_fisik';
						}

						$datajson[$i]['folder_1'] = $folder_1;

			$datajson[$i]['PENDUKUNG2'] = $row->data_pendukung2;
						$filename_2 = 'file/'.$row->data_pendukung2;

						if (file_exists($filename_2)) {
							$folder_2='file';
						} else {
							$folder_2='file_monev_fisik';
						}

						$datajson[$i]['folder_2'] = $folder_2;
			$datajson[$i]['PENDUKUNG3'] = $row->data_pendukung3;

						$filename_3 = 'file/'.$row->data_pendukung3;

						if (file_exists($filename_3)) {
							$folder_3='file';
						} else {
							$folder_3='file_monev_fisik';
						}

						$datajson[$i]['folder_3'] = $folder_3;
						

			if($row->data_pendukung4 != null){
				$datajson[$i]['PENDUKUNG4'] = $row->data_pendukung4;
			}else{
				$datajson[$i]['PENDUKUNG4'] = '#';
			}
						$filename_4 = 'file/'.$row->data_pendukung4;

						if (file_exists($filename_4)) {
							$folder_4='file';
						} else {
							$folder_4='file_monev_fisik';
						}

						$datajson[$i]['folder_1'] = $folder_1;

			if($this->session->userdata("kd_role") != 18){
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = "BELUM DIPROSES";
				}
				else if ($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "DITERIMA";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "DITOLAK";
				}
			}
			else{
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = '<div  id="verifikasi'. $row->id_pengajuan.'"><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'1\')"><img border="1px" src="'.base_url().'images/flexigrid/setujui.png"></a><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'2\')"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></a></div>';					
				}elseif($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "DITERIMA";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "DITOLAK";
				}

			}
			$datajson[$i]['LIHAT'] = "";
			$i++;
			$no++;

			}
		}
		echo json_encode($datajson);

	}

	function daftar_verifikasi_nf($p,$k,$waktu_laporan){
		$i=0;
		$no=1;
		$tahun = $this->session->userdata("thn_anggaran");
		$hasil=$this->pm->get_verifikasi_nf($p,$k,$waktu_laporan, $tahun);
		if($hasil->num_rows() != 0){
			foreach($hasil->result() as $row){	
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
			$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
			$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;
			$datajson[$i]['TRIWULAN'] = $waktu_laporan;
			$datajson[$i]['KELENGKAPAN'] = "";
			$datajson[$i]['PENDUKUNG1'] = $row->data_pendukung1;
			$datajson[$i]['PENDUKUNG2'] = $row->data_pendukung2;
			$datajson[$i]['PENDUKUNG3'] = $row->data_pendukung3;
			$datajson[$i]['PENDUKUNG4'] = $row->data_pendukung4;
			if($this->session->userdata("kd_role") != 18){
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = "BELUM DIPROSES";
				}
				else if ($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "DITERIMA";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "DITOLAK";
				}
			}
			else{
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = '<div  id="verifikasi'. $row->id_pengajuan.'"><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'1\')"><img border="1px" src="'.base_url().'images/flexigrid/setujui.png"></a><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'2\')"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></a></div>';					
				}elseif($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "DITERIMA";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "DITOLAK";
				}

			}
			$datajson[$i]['LIHAT'] = "";
			$i++;
			$no++;
			}
		}
		echo json_encode($datajson);
	}
	function daftar_verifikasi_nf2($p,$k,$waktu_laporan, $jenis_dak){
		$i=0;
		$no=1;
		$tahun = $this->session->userdata("thn_anggaran");
		$hasil=$this->pm->get_verifikasi_nf2($p,$k,$waktu_laporan, $jenis_dak, $tahun);
		if($hasil->num_rows() != 0){
			foreach($hasil->result() as $row){	
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['id_pengajuan'] = $row->id_pengajuan;
			$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
			$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;
			$datajson[$i]['TRIWULAN'] = $waktu_laporan;
			$datajson[$i]['NMRS'] = '';
			$where = array(
				'id_dak_nf' => $row->id_dak_nf,
				'TAHUN_ANGGARAN' => $tahun
			);
			$datajson[$i]['DAK'] = $this->bm->select_where_array('dak_nf', $where)->row()->nama_dak_nf;
			if($row->KD_RS != 0){
				$rs = $this->pm->get_where('data_rumah_sakit', $row->KD_RS, 'KODE_RS')->row();
				if($rs){
					$datajson[$i]['NMRS'] = $rs->NAMA_RS;
				}
			}
			$datajson[$i]['KELENGKAPAN'] = "";

			$datajson[$i]['PENDUKUNG1'] = $row->data_pendukung1;
						$filename_1 = 'file/'.$row->data_pendukung1;

						if (file_exists($filename_1)) {
							$folder_1='file';
						} else {
							$folder_1='file_monev_nf';
						}

						$datajson[$i]['folder_1'] = $folder_1;


			$datajson[$i]['PENDUKUNG2'] = $row->data_pendukung2;
						$filename_2 = 'file/'.$row->data_pendukung2;

						if (file_exists($filename_2)) {
							$folder_2='file';
						} else {
							$folder_2='file_monev_nf';
						}

						$datajson[$i]['folder_2'] = $folder_2;

			$datajson[$i]['PENDUKUNG3'] = $row->data_pendukung3;
						$filename_3 = 'file/'.$row->data_pendukung3;

						if (file_exists($filename_3)) {
							$folder_3='file';
						} else {
							$folder_3='file_monev_nf';
						}

						$datajson[$i]['folder_3'] = $folder_3;

			$datajson[$i]['PENDUKUNG4'] = $row->data_pendukung4;
						$filename_4 = 'file/'.$row->data_pendukung4;

						if (file_exists($filename_4)) {
							$folder_4='file';
						} else {
							$folder_4='file_monev_nf';
						}

						$datajson[$i]['folder_4'] = $folder_4;



			if($this->session->userdata("kd_role") != 18 && $this->session->userdata("kd_role") != 16   ){
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = "<button class='btn btn-sm btn-warning'><span class='glyphicon glyphicon-minus'></span> BELUM DIPROSES</button>";
				}
				else if ($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "<button class='btn btn-sm btn-success'><span class='glyphicon glyphicon-ok'></span> DITERIMA</button>";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "<button class='btn btn-sm btn-danger'><span class='glyphicon glyphicon-remove'></span> DITOLAK</button>";
				}
			}
			else{
				if($row->STATUS == 0){
					$datajson[$i]['VERIFIKASI'] = '<div  id="verifikasi'. $row->id_pengajuan.'"><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'1\')"><img border="1px" src="'.base_url().'images/flexigrid/setujui.png"></a><a data-placement="top" title="Setujui" class="btn btn-default"  onclick="verifikasi(\''.$row->id_pengajuan.'\' , \'2\')"><img border="1px" src="'.base_url().'images/flexigrid/tolak.png"></a></div>';					
				}elseif($row->STATUS == 1){
					$datajson[$i]['VERIFIKASI'] = "<button class='btn btn-sm btn-success'><span class='glyphicon glyphicon-ok'></span> DITERIMA</button>";
				}
				else{
					$datajson[$i]['VERIFIKASI'] = "<button class='btn btn-sm btn-danger'><span class='glyphicon glyphicon-remove'></span> DITOLAK</button>";
				}

			}
			$datajson[$i]['LIHAT'] = "";
			$i++;
			$no++;
			}
		}
		echo json_encode($datajson);
	}
	function verifikasi_pengajuan($id,$status){
		$data = array(
			'STATUS' =>$status,
			);	
		$this->pm->update('pengajuan_monev_dak', $data, 'id_pengajuan', $id);
		if($status==1){
			echo 'DITERIMA';
		}else if($status==2){
			echo 'DITOLAK';
		}
		
	}

	function verifikasi_pengajuan_nf($id,$status){
		$data = array(
			'STATUS' =>$status,
			);	
		$this->pm->update('pengajuan_monev_nf', $data, 'id_pengajuan', $id);
		if($status==1){
			echo 'DITERIMA';
		}else if($status==2){
			echo 'DITOLAK';
		}
		
	}

	function verifikasi_pengajuan_nf2($id,$status){
		$data = array(
			'STATUS' =>$status,
			);	
		$this->pm->update('dak_nf_laporan', $data, 'id_pengajuan', $id);
		if($status==1){
			echo 'DITERIMA';
		}else if($status==2){
			echo 'DITOLAK';
		}
		
	}

    function view_menu(){
        if(!isset($_GET["jenis_dak"])){
        $jenis_dak=$this->input->post('jenis_dak');
      	$kategori=$this->input->post('kategori');
      	$level=$this->input->post('level');
      	}else{
      	$jenis_dak=$_GET["jenis_dak"];
      	$kategori=$_GET["kategori"];
      	$level=$_GET["level"];	
      	}
      	$data['level']=$level;
      	$data['jenis_dak']=$jenis_dak;
      	$data['kategori']=$kategori;      	
		$this->load->view('e-monev/table_menu',$data);


    }	



    function view_pagu2(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kategori=$this->input->post('kategori');
	      	$rs = $this->input->post('kode_rs');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
	      	$rs = $_GET["kode_rs"];
      	}
      	if($rs == null){
      		$rs=0;
      	}
      	$data['jenis_dak']=$jenis_dak;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;
      	$data['kategori']=$kategori;
      	$data['rs'] = $rs;


		$this->load->view('e-monev/table_pagu2',$data);
    }	

    function table_realisasi2(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kdsatker=$this->input->post('kdsatker');
	      	$kategori=$this->input->post('kategori');
	      	$waktu=$this->input->post('waktu_laporan');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
	      	$kdsatker=$_GET["kdsatker"];
	      	$waktu=$_GET["waktu_laporan"];
      	}
      	if($kategori == 0 || $jenis_dak == 0){
      		$temp1=0;
      		$temp2=0;
      		echo "Jenis DAK atau Kategori Belum Dipilih";
      	}
      	else{
      		$kat = $this->pm->get_where('kategori', $kategori, 'ID_KATEGORI')->result();
	      	$sub = $this->pm->get_where('dak_jenis_dak', $jenis_dak, 'ID_JENIS_DAK')->result();
	      	$temp1 = $sub[0]->NAMA_JENIS_DAK;
	      	$temp2 = $kat[0]->NAMA_KATEGORI;
	      	$data['nm_sj'] = $temp1;
	      	$data['nm_kat'] = $temp2;
	      	$data['jenis_dak']=$jenis_dak;	
	      	$data['KodeProvinsi']=$KodeProvinsi;
	      	$data['KodeKabupaten']=$KodeKabupaten;
	      	$data['kdsatker']=$kdsatker;	
	      	$data['kategori']=$kategori;
	      	$data['waktu']=$waktu;
			$this->load->view('e-monev/table_realisasi2',$data);	
      	}
      	
    }
    function table_realisasi2_nf(){
    	if(isset($_POST["waktu_laporan"]) ){
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$waktu=$this->input->post('waktu_laporan');
      	}
    	if(isset($_GET["waktu_laporan"]) ){
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$waktu=$_GET["waktu_laporan"];
      	}
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['waktu']=$waktu;
		$this->load->view('e-monev/table_realisasi2_nf',$data);
    }	

    function daftar_realisasi_nf($p,$waktu){
    	$i=0;
		$no=1;
		$tahun = $this->session->userdata("thn_anggaran");
		if($p == 98){
			$provinsi = $this->pm->get('ref_provinsi')->result();
			foreach ($provinsi as $row ) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = "";
				$hasil = $this->pm->get_proses_realisasi2_nf($row->KodeProvinsi,$waktu, $tahun)->result();
				$hasil2 = $this->pm->get_pagu_prov_nf($row->KodeProvinsi, $tahun)->result();
				if($hasil[0]->realisasi> 0){
				 	$pagu = $hasil2[0]->pagu;
					
				 	$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 	if($pagu > 0){
				 		$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 		$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 	}
				 	else{
				 		$datajson[$i]['PAGU']  = 0;
				 		$datajson[$i]['PERSENTASE'] = 0;
				 	}
				 	$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] =  $hasil2[0]->pagu;
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }

				$i++;
				$no++;
			}	
		}
		else if($p ==99){
			$hasil = $this->pm->get_proses_realisasi2_nf(0,$waktu, $tahun)->result();
			$hasil2 = $this->pm->get_pagu_prov_nf(0, $tahun)->result();
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['PROVINSI'] = "Seluruh Indonesia";
			$datajson[$i]['KABUPATEN'] = "";
			if($hasil[0]->realisasi> 0){
				$pagu = $hasil2[0]->pagu;
				$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 if($pagu > 0){
				 	$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 	$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 }
				 else{
				 	$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 	$datajson[$i]['PERSENTASE'] = 0;
				 }
				 $datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] = $hasil2[0]->pagu;
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }
		}
		else{
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
			foreach ($kabupaten as $row ) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;
				$hasil = $this->pm->get_proses_realisasi_nf2($row->KodeProvinsi,$row->KodeKabupaten,$waktu, $tahun)->result();
				$pagu = $this->pm->get_pagu_kab_nf($row->KodeProvinsi,$row->KodeKabupaten, $tahun)->result();


				if($hasil[0]->realisasi> 0){
				 	$pagu = $pagu[0]->pagu;
					
				 	$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 	if($pagu > 0){
				 		$datajson[$i]['PAGU']  = $pagu;
				 		$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 	}
				 	else{
				 		$datajson[$i]['PAGU']  = 0;
				 		$datajson[$i]['PERSENTASE'] = 0;
				 	}
				 	$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] = ($pagu[0]->pagu)?$pagu[0]->pagu:"0";
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }

				$i++;
				$no++;
			}	
		}
		
		echo json_encode($datajson);
    }
    // function testes(){
    // 	$hasil = $this->pm->get_proses_realisasi_nf2(02,19,1, 2017)->result();
    // 	print_r($hasil); exit();
    // }
    function daftar_realisasi($p,$jenis_dak,$kategori,$waktu){
    	$i=0;
		$no=1;
		$datajson= array();
		$tahun = $this->session->userdata("thn_anggaran");
		if($p == 98){
			$provinsi = $this->pm->get('ref_provinsi')->result();
			foreach ($provinsi as $row ) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = "";
				$hasil = $this->pm->get_proses_realisasi2($row->KodeProvinsi,$jenis_dak,$kategori,$waktu, $tahun)->result();
				$hasil2 = $this->pm->get_pagu_prov($row->KodeProvinsi,$jenis_dak,$kategori, $tahun)->result();
				if($hasil[0]->realisasi> 0){
				 	$pagu = $hasil2[0]->pagu;
					
				 	$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 	if($pagu > 0){
				 		$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 		$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 	}
				 	else{
				 		$datajson[$i]['PAGU']  = 0;
				 		$datajson[$i]['PERSENTASE'] = 0;
				 	}
				 	$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] = ($hasil2[0]->pagu)?$hasil2[0]->pagu:"0";
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }

				$i++;
				$no++;
			}	
		}
		else if($p ==99){
			$hasil = $this->pm->get_proses_realisasi2(0,$jenis_dak,$kategori,$waktu, $tahun)->result();
			$hasil2 = $this->pm->get_pagu_prov(0,$jenis_dak,$kategori, $tahun)->result();
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['PROVINSI'] = "Seluruh Indonesia";
			$datajson[$i]['KABUPATEN'] = "";
			if($hasil[0]->realisasi> 0){
				$pagu = $hasil2[0]->pagu;
				$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 if($pagu > 0){
				 	$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 	$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 }
				 else{
				 	$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
				 	$datajson[$i]['PERSENTASE'] = 0;
				 }
				 $datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] = $hasil2[0]->pagu;
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }
		}
		else{
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
			foreach ($kabupaten as $row ) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;
				$hasil = $this->pm->get_proses_realisasi($row->KodeProvinsi,$row->KodeKabupaten,$jenis_dak,$kategori,$waktu, $tahun)->result();
				$pagu = $this->pm->get_pagu_kab($row->KodeProvinsi,$row->KodeKabupaten,$jenis_dak,$kategori, $tahun)->result();

				if($hasil[0]->realisasi> 0){
				 	$pagu = $pagu[0]->pagu;
					
				 	$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
				 	if($pagu > 0){
				 		$datajson[$i]['PAGU']  = $pagu;
				 		$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
				 	}
				 	else{
				 		$datajson[$i]['PAGU']  = 0;
				 		$datajson[$i]['PERSENTASE'] = 0;
				 	}
				 	$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
				 }
				 else{
				 	$datajson[$i]['PAGU'] = ($pagu[0]->pagu)?$pagu[0]->pagu:"0";
				 	$datajson[$i]['REALISASI'] = 0;
				 	$datajson[$i]['PERSENTASE'] = 0 ;
				 	$datajson[$i]['REALISASI_FISIK'] =0;	
				 }

				$i++;
				$no++;
			}	
		}
		
		echo json_encode($datajson);

    }
    function laporan_monev(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kategori=$this->input->post('kategori');
	      	$waktu_laporan=$this->input->post('waktu_laporan');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
	      	$waktu_laporan=$_GET["waktu_laporan"];
      	}
      	$data['jenis_dak']=$jenis_dak;	
      	$data['waktu_laporan']=$waktu_laporan;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;	
      	$data['kategori']=$kategori;
		$this->load->view('e-monev/table_monev',$data);


    }
    function view_pagu3(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kategori=$this->input->post('kategori');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
      	}
      	$data['jenis_dak']=$jenis_dak;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;	
      	$data['kategori']=$kategori;
		$this->load->view('e-monev/table_pagu3',$data);


    }

    function view_pagu_rs2(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kategori=$this->input->post('kategori');
	      	$rs=$this->input->post('rs');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
	      	$rs=$_GET["rs"];
      	}

      	$data['jenis_dak']=$jenis_dak;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;	
      	$data['kategori']=$kategori;
      	$data['rs']=$rs;
		$this->load->view('e-monev/table_pagu_rs',$data);


    }

	function view_monev_pagu(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}		
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}							
		$data2['nama'] = "tes";
		$data2['option'] = $selected_state;
		$data2['option_provinsi'] = $option_provinsi;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/pagu_monev',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

    function table_kelengkapan_2(){
    	if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kategori=$this->input->post('kategori');
	      	$waktu_laporan=$this->input->post('waktu_laporan');
	      	$kdrs = $this->input->post('kdrs');
      	}
    	if(isset($_GET["jenis_dak"]) ){
	    	$jenis_dak=$_GET["jenis_dak"];
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$kategori=$_GET["kategori"];
	      	$waktu_laporan=$_GET["waktu_laporan"];
	      	$kdrs = $_GET['kdrs'];
      	}
      	$data['waktu_laporan']=$waktu_laporan;	
      	$data['jenis_dak']=$jenis_dak;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;	
      	$data['kategori']=$kategori;
      	$data['kdrs'] = $kdrs;
		$this->load->view('e-monev/table_kelengkapan2',$data);
    }

    function table_kelengkapan_nf2(){
    	if(isset($_POST["waktu_laporan"]) ){
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$waktu_laporan=$this->input->post('waktu_laporan');
      	}
    	if(isset($_GET["waktu_laporan"]) ){
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$waktu_laporan=$_GET["waktu_laporan"];
      	}
      	$data['waktu_laporan']=$waktu_laporan;		
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;	
		$this->load->view('e-monev/table_kelengkapan_nf2',$data);

    }

     function t_kelengkapan_nf2(){
    	if(isset($_POST["waktu_laporan"]) ){
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$waktu_laporan=$this->input->post('waktu_laporan');
	      	$jenis_dak=$this->input->post('jenis_dak');
      	}
    	if(isset($_GET["waktu_laporan"]) ){
	      	$KodeProvinsi=$_GET["KodeProvinsi"]	;
	      	$KodeKabupaten=$_GET["KodeKabupaten"];
	      	$waktu_laporan=$_GET["waktu_laporan"];
	      	$jenis_dak=$_GET["jenis_dak"];

      	}
      	$data['waktu_laporan']=$waktu_laporan;		
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;
      	$data['jenis_dak']=$jenis_dak;	
		$this->load->view('metronic/e-monev/table_kelengkapan_nf',$data);

    }

	function delete_menu(){
        $jenis_dak=$this->input->post('subbidang');
      	$kategori=$this->input->post('kategori');		
      	$id=$this->input->post('id');
      	$level=$this->input->post('level');
		if($level==1){
			$tabel="menu_lv1";
			$l='1';
		}else if($level==2){
			$tabel="menu_lv2";
			$l='2';
		}else if($level==3){
			$tabel="menu_lv3";
			$l='3';
		}
		$this->pm->delete($tabel,'ID_MENU', $id);
		redirect('e-monev/e_dak/view_menu?jenis_dak='.$jenis_dak.'&kategori='.$kategori.'&level=-');

	}


	function delete_laporan_monev_nf2(){
		$id=$this->input->post('id_pagu');
		$this->pm->delete('pengajuan_monev_nf','id_pengajuan', $id);

		$cek2 = $this->pm->get_where('dak_survei_akreditasi', $id, 'id_pengajuan')->num_rows();
		if($cek2 > 0){
			$this->pm->delete('dak_survei_akreditasi','id_pengajuan', $id);				
		}

		echo 'ok';
	}

	function delete_laporan_monev(){
		$id=$this->input->post('id_pagu');
		$this->pm->delete('pengajuan_monev_dak','id_pengajuan', $id);
		$cek = $this->pm->get_where("data_monev_rka", $id, "id_pengajuan")->result();
		if($cek){
			$this->pm->delete('data_monev_rka','id_pengajuan', $id);
		}

		echo 'ok';
	}

	function delete_laporan_monev_nf(){
		$id=$this->input->post('id_pagu');
		$this->pm->delete('dak_nf_laporan','id_pengajuan', $id);
		$cek = $this->pm->get_where("dak_nf_rka", $id, "id_pengajuan")->result();
		if($cek){
			$this->pm->delete('dak_nf_rka','id_pengajuan', $id);
		}


		$data = array(
			'status' => "success",
			'message' =>  'Laporan berhasil dihapus'
		);
		echo json_encode($data);
	}

	function delete_laporan_detail_nf($id='')
	{
	

			$cek = $this->pm->get_where("dak_nf_laporan", $id, "id_pengajuan")->row_array();
			$waktu = $cek['waktu_laporan'];

			// cek akses penghapusan data

			switch ($waktu) {
				case '1':
					$idh='14';
					break;
				case '2':
					$idh='15';
					break;
				case '3':
					$idh='16';
					break;
				case '4':
					$idh='17';
					break;
				
				default:
					$idh='0';
					break;
			}

			if ($idh=='0') {

				echo "data tidak bisa di hapus";

			} else {
				# cek akses

				$tahun     = $this->session->userdata('thn_anggaran');
				$dataakses = $this->pm->getBatasAkhir($idh,$tahun);
				$akses     = $dataakses->row_array();
				$rows      = $dataakses->num_rows();

				if ($rows=='0') {
					echo "data tidak bisa di hapus";

				}else {	


					$date      = date('Y-m-d h:i:s');
					$akses_tgl = $akses['tgl_akses'];

					if ($akses_tgl  >= $date) {
					// hapus
						$pendukung1 = $cek['data_pendukung1'];
						$pendukung2 = $cek['data_pendukung2'];
						$pendukung3 = $cek['data_pendukung3'];
						$pendukung4 = $cek['data_pendukung4'];

							// proses hapus
							unlink("file/".$pendukung1);
							unlink("file/".$pendukung1);
							unlink("file/".$pendukung1);
							unlink("file/".$pendukung1);

							$this->pm->delete('dak_nf_laporan','id_pengajuan', $id);
							$cekz = $this->pm->get_where("dak_nf_rka", $id, "id_pengajuan")->result();


							if($cekz){
								 $this->pm->delete('dak_nf_rka','id_pengajuan', $id);
							}

							//echo 'ok';
							redirect('e-monev/e_dak/laporan_detail_nf');
					// end proses hapus

							// echo "data belum bisa terhapus = ".$id." asdas ".$waktu;


					} else {

					echo "data tidak bisa di hapus";


					}

				}


			}
			
	}
	function delete_laporan_detail_nf2($id){
		// $id=$this->input->post('id_pagu');


		$this->pm->delete('dak_nf_laporan','id_pengajuan', $id);
		$cek = $this->pm->get_where("dak_nf_rka", $id, "id_pengajuan")->result();


		if($cek){
			 $this->pm->delete('dak_nf_rka','id_pengajuan', $id);
		}



		echo 'ok';
		redirect('e-monev/e_dak/laporan_detail_nf');
	}
	function view_pagu_nf()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['000'] = 'seluruh indonesia';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak_nf')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_pagu_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelengkapan()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Kelengkapan';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelengkapan',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}


	function view_absensi()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh indonesia';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Absensi';
		$data['content'] = $this->load->view('metronic/e-monev/view_absensi',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_absensi_nf()
	{
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Absensi';
		$data['content'] = $this->load->view('metronic/e-monev/view_absensi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}


	function view_kelengkapan2()
	{   
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}

		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Kelengkapan';
		$data['content'] = $this->load->view('metronic/e-monev/view_kelengkapan2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_seluruh_menu(){
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}		
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['kategori'] = $option_kategori;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Seluruh Menu';
		$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_seluruh_menu2(){
		$thn_anggaran = $this->session->userdata('thn_anggaran');

		
			
		
		
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}		
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');

		$listkabupaten= $this->pm->get_kabupaten_detail($idprov,$idkab)->result();

		foreach ($listkabupaten  as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
 


		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['kdprov'] = $idprov;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['kategori'] = $option_kategori;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data2['tahun'] = $this->session->userdata('thn_anggaran');
		$data['judul'] = 'Cetak Seluruh Menu';
		
		
		if ($thn_anggaran >=2019) {

			$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu2',$data2,true);
			// view_seluruh_menu2_tch


		} else {

			$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu2',$data2,true);

		}

		$this->load->view(VIEWPATH,$data);
	}

	function view_seluruh_menu_tch(){
		$thn_anggaran = $this->session->userdata('thn_anggaran');

		
			
		
		
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}		
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');

		$listkabupaten= $this->pm->get_kabupaten_detail($idprov,$idkab)->result();

		foreach ($listkabupaten  as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
 


		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['kdprov'] = $idprov;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['kategori'] = $option_kategori;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data2['tahun'] = $this->session->userdata('thn_anggaran');
		$data['judul'] = 'Cetak Seluruh Menu';
		
		
		if ($thn_anggaran >=2019) {

			$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu2_tch',$data2,true);
			// 


		} else {

			$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu2',$data2,true);

		}

		$this->load->view(VIEWPATH,$data);
	}
	public function print_monevfisik_tch($p='',$k='',$w='')
	{



		$tahun = $this->session->userdata("thn_anggaran");
		$tw=$w;	
		if ($k=='100') {
				$table2="ref_kabupaten rk";
            	$where2="
            			INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=rk.KodeProvinsi
						WHERE rk.KodeProvinsi=".$p."";	
            	
            	 $namajudul= "ALL";
		} else {
				$table2="ref_kabupaten rk";
            	$where2="
            			INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=rk.KodeProvinsi
						WHERE rk.KodeProvinsi=".$p." AND rk.KodeKabupaten=".$k."";	


                        $tableKab="ref_kabupaten";
                        $whereKab="WHERE KodeProvinsi='".$p."' AND kodekabupaten='".$k."'" ;

                        $kabupaten=$this->bm->getAllWhere($tableKab,$whereKab)->row_array();

                       
                         $namajudul1=str_replace(".","_",$kabupaten['NamaKabupaten']);
                         $namajudul=str_replace(" ","_",$namajudul1);

		}
		
			
			$listing = $this->pm->getAllWhere($table2,$where2);


                                           
                                            
		header("Content-type=appalication/vnd.ms-excel");
				header("content-disposition:attachment;filename=MonevFisik_tw".$w."_".$namajudul.".xls");


		$data = array (	
			'listing' => $listing,
			'tahun'   => $tahun,
			'tw'      => $tw
			);
		$this->load->view('metronic/e-monev/v_monevmonitoringMising2', $data);
	}
	public function print_monevnonfisik_tch($p='',$k='',$w='')
	{
		
		$tahun = $this->session->userdata("thn_anggaran");
		
		//$tahun   = '2017';
		$tw=$w;	
			// $role    = $this->session->userdata('kd_role');
			// $tahun   = $this->session->userdata('thn_anggaran');
				// header("Content-type=appalication/vnd.ms-excel");
				// header("content-disposition:attachment;filename=LapDataMonevNonFisikTerinput".$tahun."_".$tw.".xls");
		if ($k=='100') {
				$table2="ref_kabupaten rk";
            	$where2="
            			INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=rk.KodeProvinsi
						WHERE rk.KodeProvinsi=".$p."";	

				 $namajudul= "ALL";
            	
		} else {
				$table2="ref_kabupaten rk";
            	$where2="
            			INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=rk.KodeProvinsi
						WHERE rk.KodeProvinsi=".$p." AND rk.KodeKabupaten=".$k."";	

						$tableKab="ref_kabupaten";
                        $whereKab="WHERE KodeProvinsi='".$p."' AND kodekabupaten='".$k."'" ;

                        $kabupaten=$this->bm->getAllWhere($tableKab,$whereKab)->row_array();

                       
                         $namajudul1=str_replace(".","_",$kabupaten['NamaKabupaten']);
                         $namajudul=str_replace(" ","_",$namajudul1);
		}
		
			header("Content-type=appalication/vnd.ms-excel");
				header("content-disposition:attachment;filename=MonevNonFisik_tw".$w."_".$namajudul.".xls");


			$listing = $this->pm->getAllWhere($table2,$where2);

			$data = array (	
				'listing' => $listing,
				'tahun'   => $tahun,
				'tw'      => $tw

				);
			$this->load->view('metronic/e-monev/v_monevmonitoringMisingnonfisik2', $data);

	}
	function view_seluruh_menu_nf2(){
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data2['selected_state'] = $selected_state;
		$data2['provinsi'] = $option_provinsi;
		$data2['role']=$role;
		$data['judul'] = 'Cetak Seluruh Menu NF';
		$data['content'] = $this->load->view('metronic/e-monev/view_seluruh_menu_nf2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}


	function print_realisasi_menu_a(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$j = 3;
		$tahun = $this->session->userdata("thn_anggaran");
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", $j, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($p== 0 || $k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'Puskesmas');
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU' );
		$rowx = '6';
		$rowx2 = '7';
		$column = 'D';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}

		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->mergeCells('C6:C7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');
		$col = 'D';
		$b=8;
		$c = 8;
		
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten );
			
			if($this->session->userdata('thn_anggaran') == 2017){
				$data_rs = $this->pm->get_where_double("data_puskesmas", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();	
			}
			else{
				$data_rs = $this->pm->get_where_double("data_puskesmas2018", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			}
			if($data_rs == null){
				$b++;
				$jml = 0;
				$col = 'D';
			}
			else{
				foreach ($data_rs as $row2) {
					$jml = 0;
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $row2->NamaPuskesmas);
					$pagu_s = $pagu_s = $this->pm->get_where_double("pagu_puskesmas", $row2->KodePuskesmas, "KodePuskesmas", $tahun, "TAHUN_ANGGARAN")->result();
					if($pagu_s != null){
						$this->excel->getActiveSheet()->setCellValue('C'.$b, number_format($pagu_s[0]->PAGU_SELURUH));
						$this->excel->getActiveSheet()->getStyle('C'. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$data = $this->pm->get_data_rujukan($row2->KodePuskesmas, $j, $w)->result();
						if($data){
								foreach ($data as $key => $value) {
									foreach ($menu as $key2 => $value2) {
										$col = 'D';
										if($value->kode_menu == $value2->ID_MENU){
											$this->excel->getActiveSheet()->setCellValue($col.$b, $value->volume);
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($value->pagu));
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($value->realisasi));
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, $value->fisik);
											$col++;
											$jml += $value->realisasi;
										}
										else{
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
										}
									}
								}
						}
						else{
							foreach ($menu as $key2 => $value2) {
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
							}
						}

					}
					else{
						$this->excel->getActiveSheet()->setCellValue('C'.$b, "0");
						foreach ($menu as $key2 => $value2) {
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
						}
					}
					$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($jml));
					$this->excel->getActiveSheet()->getStyle($col. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col = 'D';
					$b++;
				}
				
			}
			
		}


		$filename='rekap_realisasi_menu.xlsx';
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_realisasi_menu_a2(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$tahun = $this->session->userdata("thn_anggaran");
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", $j, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($p== 0 || $k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}

		if($this->session->userdata('thn_anggaran') ==  2017){
			$pagu = $this->pm->get_where('pagu_puskesmas', $tahun, 'TAHUN_ANGGARAN')->result();	
		}
		else{
			$pagu = $this->pm->get_where('pagu_rs', $tahun, 'TAHUN_ANGGARAN')->result();
		}
		
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'Puskesmas');
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU' );

		$rowx = '6';
		$rowx2 = '7';
		$column = 'D';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}

		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->mergeCells('C6:C7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');
		$col = 'D';
		$b=8;
		$c = 8;
		$data =  $this->pm->get_realisasi_dak($j, $w)->result();
		// print_r($data);exit();
		foreach ($kabupaten as $key => $value) {
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $value->NamaKabupaten );
			if($this->session->userdata('thn_anggaran') ==  2017){
				$data_rs = $this->pm->get_where_double("data_puskesmas", $value->KodeProvinsi, "KodeProvinsi", $value->KodeKabupaten, "KodeKabupaten")->result();
			}
			else{
				$data_rs = $this->pm->get_where_double("data_puskesmas2018", $value->KodeProvinsi, "KodeProvinsi", $value->KodeKabupaten, "KodeKabupaten")->result();
			}
			
			if($data_rs == null){
				$b++;
				$jml = 0;
				$col = 'D';
			}
			else{
				foreach ($data_rs as $key4 => $value4) {
					$this->excel->getActiveSheet()->setCellValue('A'.$b , $value->NamaKabupaten);
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $value4->NamaPuskesmas );
					$total = 0;
					$pagu_tot = 0;
					foreach ($menu as $key2 => $value2) {
						$ketemu = 0;
						foreach ($data as $key3 => $value3) {
							if($value4->KodePuskesmas == $value3->KODE_RS && $value2->ID_MENU == $value3->ID_MENU ){
								$ketemu = 1;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->volume);
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->pagu);
								$pagu_tot +=  $value3->pagu; 
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->realisasi);
								$total +=  $value3->realisasi; 
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->fisik);
								$col++;
							}

						}
						if($ketemu == 0){
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
						}

					}
					foreach ($pagu as $key5 => $value5) {
						if($this->session->userdata('thn_anggaran') == 2017){
							$kd= $value5->KodePuskesmas;
						}
						else{
							$kd = $value5->KODE_RS;
						}
						if($value4->KodePuskesmas == $kd){

							$this->excel->getActiveSheet()->setCellValue('C'.$b , $value5->PAGU_SELURUH);		
						}
					}
					$this->excel->getActiveSheet()->setCellValue($col.$b , $total);
					$col = 'D';
					$b++;
				}
				
			}
			
		}


		$filename='rekap_afirmasi.xlsx';
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_realisasi_menu_r(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$tahun = $this->session->userdata("thn_anggaran");
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", 1, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($p== 0 || $k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'Rumah Sakit');
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU' );
		$rowx = '6';
		$rowx2 = '7';
		$column = 'D';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}

		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->mergeCells('C6:C7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');
		$col = 'D';
		$b=8;
		$c = 8;
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten );
			$data_rs = $this->pm->get_where_double("data_rumah_sakit", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($data_rs == null){
				$b++;
				$jml = 0;
				$col = 'D';
			}
			else{
				foreach ($data_rs as $row2) {

					$jml = 0;
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $row2->NAMA_RS);
					$pagu_s = $pagu_s = $this->pm->get_where_double("pagu_rs", $row2->KODE_RS, "KODE_RS", $j, "ID_Jenis_DAK")->result();
					if($pagu_s != null){
						$this->excel->getActiveSheet()->setCellValue('C'.$b, number_format($pagu_s[0]->PAGU_SELURUH));
						$this->excel->getActiveSheet()->getStyle('C'. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$data = $this->pm->get_data_rujukan($row2->KODE_RS, $j, $w)->result();
						if($data){
								foreach ($data as $key => $value) {
									foreach ($menu as $key2 => $value2) {
										$col = 'D';
										if($value->kode_menu == $value2->ID_MENU){
											$this->excel->getActiveSheet()->setCellValue($col.$b, $value->volume);
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($value->pagu));
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($value->realisasi));
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, $value->fisik);
											$col++;
											$jml += $value->realisasi;
										}
										else{
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
											$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
											$col++;
										}
									}
								}
						}
						else{
							foreach ($menu as $key2 => $value2) {
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
								$col++;
							}
						}

					}
					else{
						$this->excel->getActiveSheet()->setCellValue('C'.$b, "0");
						foreach ($menu as $key2 => $value2) {
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b, '0');
							$col++;
						}
					}
					$this->excel->getActiveSheet()->setCellValue($col.$b, number_format($jml));
					$this->excel->getActiveSheet()->getStyle($col. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col = 'D';
					$b++;
				}
				
			}
			
		}
		if($j == 1){
			$filename='3. Rekap Rujukan.xlsx';	
		}
		else if($j==8){
			$filename='5. Rekap Penugasan.xlsx';
		}
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_realisasi_menu(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", $j, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		$rowx = '6';
		$rowx2 = '7';
		$column = 'C';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}
		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6' , 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');

		$i = 8;
		$b=8;
		$col = 'C';
		$tahun = $this->session->userdata("thn_anggaran");
		foreach ($kabupaten as $row) {
			$jml = 0;
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten );

			$pagu_s = $this->pm->get_where_triple("pagu_seluruh", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $j, "ID_SUBBIDANG", $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result();
			if($pagu_s != null){
				$this->excel->getActiveSheet()->setCellValue('B'.$i, number_format($pagu_s[0]->pagu_seluruh ));
				$this->excel->getActiveSheet()->getStyle('B'. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$data = $this->pm->get_data_nonrujukan($row->KodeProvinsi, $row->KodeKabupaten, $j, $w)->result();
				if($data){
					foreach ($data as $key => $value) {
						foreach ($menu as $key2 => $value2) {
							if($value->kode_menu == $value2->ID_MENU){
								$this->excel->getActiveSheet()->setCellValue($col.$i, $value->volume);
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($value->pagu));
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($value->realisasi));
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, $value->fisik);
								$col++;
								$jml += $value->realisasi;
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$col++;
							}
						}
					}
				}
				else{
					foreach ($menu as $key => $value) {
						$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						$col++;
					}
				}
		}
		else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, "0" );
				foreach ($menu as $key2 => $value2) {
					$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
					$col++;
				}
		}

			$this->excel->getActiveSheet()->setCellValue($col.$b , number_format($jml));
			$this->excel->getActiveSheet()->getStyle($col. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$col = 'C';
			$i++;

		}

		
		$filename='rekap_realisasi_menu.xlsx'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_realisasi_menu2(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", $j, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$pagu = $this->pm->get_where_double('pagu_seluruh', $j, 'ID_SUBBIDANG', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result();
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		$rowx = '6';
		$rowx2 = '7';
		$column = 'C';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}
		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6' , 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');

		$i = 8;
		$b=8;
		$col = 'C';
		$data =  $this->pm->get_realisasi_dak($j, $w)->result();

		foreach ($kabupaten as $key => $value) {
			$total = 0;
			$pagu_tot = 0;
			$this->excel->getActiveSheet()->setCellValue('A'.$b , $value->NamaKabupaten);
			foreach ($menu as $key2 => $value2) {
				$ketemu = 0;
				foreach ($data as $key3 => $value3) {
					if($value->NamaKabupaten == $value3->NamaKabupaten && $value2->ID_MENU == $value3->ID_MENU ){
						$ketemu = 1;
						$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->volume);
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->pagu);
						$pagu_tot +=  $value3->pagu; 
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->realisasi);
						$total +=  $value3->realisasi; 
						$col++;
						$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->fisik);
						$col++;
					}

				}
				if($ketemu == 0){
					$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
					$col++;
				}

			}
			foreach ($pagu as $key5 => $value5) {
				if($value5->KodeProvinsi == $value->KodeProvinsi && $value5->KodeKabupaten == $value->KodeKabupaten){
					$this->excel->getActiveSheet()->setCellValue('B'.$b , $value5->pagu_seluruh);		
				}
			}
			
			$this->excel->getActiveSheet()->setCellValue($col.$b , $total);
			$col = 'C';
			$b++;
		}
		$filename='r_menu-'.$subbidang[0]->NAMA_JENIS_DAK.'.xlsx'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');


	}

	function print_realisasi_menu_r2(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$tahun = $this->session->userdata("thn_anggaran");
		$subbidang = $this->pm->get_where("dak_jenis_dak", $j, "ID_JENIS_DAK")->result();
		$menu = $this->pm->get_where_double("menu", $j, "ID_SUBBIDANG", $tahun, "TAHUN")->result();
		if($p== 0 || $k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$pagu = $this->pm->get_where_double('pagu_rs', $j, 'ID_Jenis_DAK', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN' )->result();
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '. $subbidang[0]->NAMA_JENIS_DAK . ' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'Rumah Sakit');
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU' );
		$rowx = '6';
		$rowx2 = '7';
		$column = 'D';
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Volume' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Pagu' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Realisasi' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'Fisik' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->NAMA);
		    $column++;
			
		}
		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		$this->excel->getActiveSheet()->mergeCells('C6:C7');
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
		$this->excel->getActiveSheet()->getRowDimension(6)->setRowHeight(40);
		$i = --$column;
		$this->excel->getActiveSheet()->setCellValue($i.'6', 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$i."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$i."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($i.'6:'. $i .'7');
		$col = 'D';
		$b=8;
		$c = 8;
		$data =  $this->pm->get_realisasi_dak($j, $w)->result();
		foreach ($kabupaten as $key => $value) {
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $value->NamaKabupaten );
			$data_rs = $this->pm->get_where_double("data_rumah_sakit", $value->KodeProvinsi, "KodeProvinsi", $value->KodeKabupaten, "KodeKabupaten")->result();
			if($data_rs == null){
				$b++;
				$jml = 0;
				$col = 'D';
			}
			else{
				foreach ($data_rs as $key4 => $value4) {
					$this->excel->getActiveSheet()->setCellValue('A'.$b , $value->NamaKabupaten);
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $value4->NAMA_RS );
					$total = 0;
					$pagu_tot = 0;
					foreach ($menu as $key2 => $value2) {
						$ketemu = 0;
						foreach ($data as $key3 => $value3) {
							if($value4->KODE_RS == $value3->KODE_RS && $value2->ID_MENU == $value3->ID_MENU ){
								$ketemu = 1;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->volume);
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->pagu);
								$pagu_tot +=  $value3->pagu; 
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->realisasi);
								$total +=  $value3->realisasi; 
								$col++;
								$this->excel->getActiveSheet()->setCellValue($col.$b , $value3->fisik);
								$col++;
							}

						}
						if($ketemu == 0){
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
							$this->excel->getActiveSheet()->setCellValue($col.$b , '0');
							$col++;
						}

					}
					foreach ($pagu as $key5 => $value5) {
						if($value4->KODE_RS == $value5->KODE_RS){
							$this->excel->getActiveSheet()->setCellValue('C'.$b , $value5->PAGU_SELURUH);		
						}
					}
					$this->excel->getActiveSheet()->setCellValue($col.$b , $total);
					$col = 'D';
					$b++;
				}
				
				
			}
			
		}
		if($j == 1){
			$filename='3. Rekap Rujukan.xlsx';	
		}
		else if($j==8){
			$filename='5. Rekap Penugasan.xlsx';
		}
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_realisasi_menu_nf(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		if($k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu NF Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		// BOK
		$this->excel->getActiveSheet()->setCellValue('C6', 'BOK' );
		$this->excel->getActiveSheet()->setCellValue('C7', 'BOK Kabupaten/Kota' );
		$this->excel->getActiveSheet()->setCellValue('C8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('D8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('E8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('C7:E7');
		$this->excel->getActiveSheet()->setCellValue('F7', 'BOK Puskesmas' );
		$this->excel->getActiveSheet()->setCellValue('F8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('G8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('H8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('F7:H7');
		$this->excel->getActiveSheet()->setCellValue('I7', 'Distribusi Obat dan E-Logistic' );
		$this->excel->getActiveSheet()->setCellValue('I8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('J8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('K8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('I7:K7');
		$this->excel->getActiveSheet()->setCellValue('L7', 'JUMLAH' );
		$this->excel->getActiveSheet()->mergeCells('C6:L6');
		$this->excel->getActiveSheet()->mergeCells('L7:L8');
		//A RS
		$this->excel->getActiveSheet()->setCellValue('M6', 'AKREDITASI RUMAH_SAKIT' );
		$this->excel->getActiveSheet()->setCellValue('M8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('N8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('O8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('M6:O7');
		// // A PUS
		$this->excel->getActiveSheet()->setCellValue('P6', 'AKREDITASI PUSKESMAS' );
		$this->excel->getActiveSheet()->setCellValue('P8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('Q8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('R8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('P6:R7');
		// // JAMPER
		$this->excel->getActiveSheet()->setCellValue('S6', 'JAMINAN PERSALINAN' );
		$this->excel->getActiveSheet()->setCellValue('S8', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('T8', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('U8', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('S6:U7');
		// //JUMLAH
		$this->excel->getActiveSheet()->setCellValue('V6', 'JUMLAH' );
		$this->excel->getActiveSheet()->mergeCells('V6:V8');

		$this->excel->getActiveSheet()->mergeCells('A6:A8');
		$this->excel->getActiveSheet()->mergeCells('B6:B8');
		foreach (range('A', 'V') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(25);
		}
		$this->excel->getActiveSheet()->getStyle("A6:V8")->applyFromArray($styleArrayHead);
		
		$i=9;
		
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten );
			$pagu_s = $this->pm->get_where_double("pagu_seluruh_nf", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($pagu_s != null){
				$this->excel->getActiveSheet()->setCellValue('B'.$i, number_format($pagu_s[0]->pagu_seluruh ));
				$this->excel->getActiveSheet()->getStyle('B'. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
			else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, "0" );
			}
			$col ='C';
			$jumlah_bok =0;
			for ($b = 5 ; $b <= 7 ; $b++){
				$pagu_bok = $this->pm->get_where_triple("pagu_bok", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $b, "id_menu_nf")->result();
				if($pagu_bok != null){
					$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($pagu_bok[0]->PAGU ));
					$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col++;
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0" );
					$col++;
				}
				$realisasi = $this->pm->realisasi_menu_nf($row->KodeProvinsi, $row->KodeKabupaten, $b, $w, $tahun)->result();
				if($realisasi != null){
					$jumlah_bok+= $realisasi[0]->realisasi;
					$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($realisasi[0]->realisasi ));
					$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, $realisasi[0]->fisik );
					$col++;
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0" );
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0");
					$col++;
				}
			}
			$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($jumlah_bok));
			$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$col++;
			for ($b = 2 ; $b <= 4 ; $b++){
				$pagu = $this->pm->get_where_triple("pagu_nf", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $b, "id_menu_nf")->result();
				if($pagu != null){
					$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($pagu[0]->PAGU ));
					$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col++;
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0" );
					$col++;
				}
				$realisasi = $this->pm->realisasi_menu_nf($row->KodeProvinsi, $row->KodeKabupaten, $b, $w, $tahun)->result();
				if($realisasi != null){
					$jumlah_bok+= $realisasi[0]->realisasi;
					$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($realisasi[0]->realisasi ));
					$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, $realisasi[0]->fisik );
					$col++;
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0" );
					$col++;
					$this->excel->getActiveSheet()->setCellValue($col.$i, "0");
					$col++;
				}
			}
			$this->excel->getActiveSheet()->setCellValue($col.$i, number_format($jumlah_bok));
			$this->excel->getActiveSheet()->getStyle($col. $i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$col++;

			$i++;
		}
		
		$filename='rekap_realisasi_menu_nf.xlsx'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function verifikasi()
	{   
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Verifikasi';
		$data['content'] = $this->load->view('metronic/e-monev/view_verifikasi',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function verifikasi2(){   
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Semua Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}

		$option_kabupaten['0'] = '-- Semua Kabupaten --';
		foreach ($this->pm->get_where("ref_kabupaten", $kdprovinsi, "KodeProvinsi")->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$kdrs = $this->session->userdata('kdsatker');
		if($this->session->userdata('kd_role') == 20){
			$rs = $this->bm->select_where('data_rumah_sakit', 'KODE_RS', $kdrs)->row();
			if($rs){
				$nmrs = $rs->NAMA_RS;	
			}
			else{
				$nmrs ='';
			}
			$data2['kdrs']= $kdrs;
			$data2['nmrs'] = $nmrs;
		}
		else{
			$data2['kdrs']= '0';
		}
		
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['kategori'] = $option_kategori;
		$data2['option_provinsi'] = $option_provinsi;
		$data2['option_kabupaten'] = $option_kabupaten;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_verifikasi2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}
	function verifikasi2_nf(){   
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}

		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data['e_monev'] = "";
		$data2['option_provinsi'] = $option_provinsi;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/view_verifikasi_nf2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function verifikasi_nf2(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$kdsatker = $this->session->userdata('kdsatker');
			$kdprovinsi=$this->session->userdata('kodeprovinsi');
			$kdkabupaten=$this->session->userdata('kodekabupaten');
			if($kdsatker!=NULL){
				foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
					$selected_state = $row->NamaProvinsi;
				}
				foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
					$selected_kabupaten=$row->NamaKabupaten;
				}

			}
			$option_provinsi['0'] = '-- Pilih Provinsi --';
			foreach ($this->pm->get_provinsi()->result() as $row){
				$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
			}
			$option_kategori['0'] = '-- Pilih Kategori    --';
			foreach ($this->pm->get_where('dak_nf_kategori', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
				$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
			}
			$option_masalah['0'] = '-- Tidak Ada  --';

			foreach ($this->pm->get('permasalahan_dak')->result() as $row){
				$option_masalah[$row->KodeMasalah] = $row->Masalah;
			}


			$satuan = $this->pm->get('ref_satuan')->result();
			$option_satuan['2'] = 'Paket';
			foreach ($satuan as $row) {
				$option_satuan[$row->KodeSatuan] = $row->Satuan;
			}

		$role = $this->session->userdata('kd_role');
		$data2['role']	= $role;	
		$data2['nama'] = "tes";
		$data2['kategori'] = $option_kategori;
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;			
		$data2['provinsi'] = $selected_state;
		$data2['kabupaten'] = $selected_kabupaten;

		$data['e_monev'] = "";
		$data2['option_provinsi'] = $option_provinsi;
		$data['judul'] = 'View pagu';
		$data['content'] = $this->load->view('metronic/e-monev/verifikasi_nf2',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function verifikasi_nf()
	{   
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		$role=$this->session->userdata('kd_role');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}

		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Verifikasi';
			// $today=date('Y-m-d');
			// if ($today > '2015-12-31') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// } else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// if ($this->session->userdata('kdsatker') == '465915' || $this->session->userdata('kodeprovinsi') == '05') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// }
		$data['content'] = $this->load->view('metronic/e-monev/view_verifikasi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_pdf()
	{   
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi = $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$role=$this->session->userdata('kd_role');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['tgl']=date('d-m-Y');
		if($role==17 || $role==20){
			$data2['selected_kabupaten'] = $selected_kabupaten;
			$data2['kdkabupaten'] = $kdkabupaten;
		}
		$data2['role']=$role;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['selected_state'] = $selected_state;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View PDF';
			// $today=date('Y-m-d');
			// if ($today > '2015-12-31') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// } else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// if ($this->session->userdata('kdsatker') == '465915' || $this->session->userdata('kodeprovinsi') == '05') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// }
		$data['content'] = $this->load->view('metronic/e-monev/view_pdf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_pdf_nf()
	{   
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi=  $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$role=$this->session->userdata('kd_role');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$data2['tgl']=date('d-m-Y');
		if($role==17  || $role == 20){
			$data2['selected_kabupaten'] = $selected_kabupaten;
			$data2['kdkabupaten'] = $kdkabupaten;
		}

		$data2['role']=$role;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['selected_state'] = $selected_state;
		$data2['provinsi'] = $option_provinsi;
		$data['judul'] = 'View PDF NF';
		$data['content'] = $this->load->view('metronic/e-monev/view_pdf_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function view_kelengkapan_nf()
	{	
		$data2['kdprovinsi'] = $this->session->userdata('kodeprovinsi');
		$kdprovinsi=  $this->session->userdata('kodeprovinsi');
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$role=$this->session->userdata('kd_role');
		$option_rencana_anggaran;
		$option_kabupaten;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$selected_state = '-';
		$selected_worker = 0;
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$this->session->userdata('kodeprovinsi');
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak_nf')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$idkab=$this->session->userdata('kodekabupaten');
		$idprov=$this->session->userdata('kodeprovinsi');
		foreach ($this->pm->get_kabupaten_detail($idprov,$idkab)->result() as $row){
			$selected_kabupaten=$row->NamaKabupaten;
			$kdkabupaten=$row->KodeKabupaten;
		}
		if($role!=17 && $role!=20){
			$selected_kabupaten=0;
			$kdkabupaten=0;
		}
			// if($this->pm->cek1('ref_satker_program','kdsatker',$kdsatker))
			// else $data2['program']=$this->pm->get_where_double('ref_program','1','KodeStatus','024','KodeKementerian');
			// }
		$rs='';
		if($role==20){
			$data_rs=$this->pm->get_where('data_rumah_sakit', $this->session->userdata('kdsatker'), 'KODE_RS')->result();
			foreach($data_rs as $row){
				$rs=' 
					<tr>
					<td> Nama Rumah Sakit :</td>
					<td>'.$row->NAMA_RS.'</td>		
					</tr>';
			} 
		}
		$data2['rs']=$rs;		
		$data2['selected_kabupaten'] = $selected_kabupaten;
		$data2['kdkabupaten'] = $kdkabupaten;
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['provinsi'] = $option_provinsi;
		$data2['satker'] = $option_satker;
		$data2['role']=$role;
		$data['judul'] = 'View Kelengkapan Non Fisik';
			// $today=date('Y-m-d');
			// if ($today > '2015-12-31') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// } else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// if ($this->session->userdata('kdsatker') == '465915' || $this->session->userdata('kodeprovinsi') == '05') {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/pengajuan1',$data2,true);
			// }
			// else {
				// 	$data['content'] = $this->load->view('e-planning/tambah_pengusulan/tutup',$data2,true);
			// }
		$data['content'] = $this->load->view('metronic/e-monev/view_kelengkapan_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function get_jenis_kegiatan($kode1)
	{
		$query = $this->pm->get_where('dak_jenis_kegiatan',$kode1,'ID_JENIS_DAK');
		$i=0;
		if($query->num_rows >0){
			foreach($query->result() as $row)
				{
				$datajson[$i]['ID_DAK'] = $row->ID_DAK;
				$datajson[$i]['JENIS_KEGIATAN'] = $row->JENIS_KEGIATAN;
				$i++;
			}
		}
		else{	
			$datajson[0]['ID_DAK'] = '0';
			$datajson[0]['JENIS_KEGIATAN'] = 'Tidak ada kegiatan';
		}
		echo json_encode($datajson);
	}

	function get_sub_kegiatan($kode1)
	{
		$query = $this->pm->get_where('dak_sub_jenis_dak',$kode1,'ID_JENIS_DAK');
		$i=0;
		if($query->num_rows >0){
			foreach($query->result() as $row)
				{
				$datajson[$i]['ID_DAK'] = $row->ID_SUB_JENIS_DAK;
				$datajson[$i]['JENIS_KEGIATAN'] = $row->JENIS_DAK;
				$i++;
			}
		}
		else{	
			$datajson[0]['ID_DAK'] = '0';
		$datajson[0]['JENIS_KEGIATAN'] = 'Tidak ada kegiatan';
		}
		echo json_encode($datajson);
	}

	function get_ss_kegiatan($kode1)
	{
		$query = $this->pm->get_where('dak_ss_jenis_kegiatan',$kode1,'ID_SUB_JENIS_DAK');
		$i=0;
		if($query->num_rows >0){
			foreach($query->result() as $row)
				{
				$datajson[$i]['ID_DAK'] = $row->ID_SS_JENIS_KEGIATAN;
				$datajson[$i]['JENIS_KEGIATAN'] = $row->JENIS_KEGIATAN;
				$i++;
			}
		}
		else
				{	$datajson[0]['ID_DAK'] = '0';
		$datajson[0]['JENIS_KEGIATAN'] = 'Tidak ada kegiatan';
		}
		echo json_encode($datajson);
	}

	function table_juknis(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$d=0;
		$s=4;
		$juknis=$_GET["j"];
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$jk="tidak ada kegiatan";
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["d"]))$d=$_GET["d"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$kategori=0;
		$idx=0;
		$kabupaten=$k;
		$provinsi=$p;
		$pos=strpos($juknis, '.');
		//kalo ada titiknya berarti sub
		if ($pos  !== false) {
			$id=explode(".",$juknis);
			if(count($id)<3){
		//sub jenis kegiatan	
				if($this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0){
				//ambil data jenis kegiatan
					$jk=$this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->row()->JENIS_DAK;
					//cek apakah kategori atau menu
					if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0){
					$kategori=1;
					}else{
					$kategori=2;	
					}	
				}
			}else{
		// sub sub jenis kegiatan	
				if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->row()->JENIS_KEGIATAN;
					$kategori=2;
			}
		} else {
			if($this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->num_rows()){
			$jk=$this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->row()->JENIS_KEGIATAN;
				if($this->pm->get_where('dak_sub_jenis_dak',$juknis,'ID_JENIS_DAK')->num_rows()!=0){
				$kategori=1;
				}else{
				$kategori=2;	
				}				
			}
		}
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result() as $index => $row){
			$lokasi[$index]='-';	
			$l='';
			$laporan=$this->pm->dak_laporan($row->KodeKabupaten,$t,$row->KodeProvinsi,$d);
			if($laporan->num_rows !=0 ){
				foreach($laporan->result() as $row1){
					$l.=$row1->ID_LAPORAN_DAK.',';
				}
			}

			$l=substr_replace($l, '', -1);
			if($this->pm->sum_kabupaten($l)->num_rows !=0 ){
				$realisasi_daerah[$index]=$this->pm->sum_kegiatan('REALISASI_KEUANGAN_PELAKSANAAN',$l,$juknis)->row()->REALISASI_KEUANGAN_PELAKSANAAN;
				$perencanaan_daerah[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$perencanaan[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$jumlah[$index]=$this->pm->sum_kegiatan('JUMLAH_PELAKSANAAN',$l,$juknis)->row()->JUMLAH_PELAKSANAAN;
				if($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->num_rows !=0){
					foreach ($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->result() as $row){
						if($row->LOKASI_KEGIATAN != null)
						{
							$lokasi[$index]=$row->LOKASI_KEGIATAN;
						}
					}
				}else{
					$lokasi[$index]='belum';
				}
				$fisik2=0;
				foreach($this->pm->dak_kegiatan3($l,$juknis)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN > 0 && $perencanaan[$index]>0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan[$index];
						$fisik2+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}else{
						$fisik2+=0;
					}
				}
				$fisik[]=$fisik2;
			}
			else {$realisasi_daerah[]=0;
				$fisik[]='0%';
			}
		}
		$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_juknis?k='.$k.'&t='.$t.'&p='.$p.'&j='.$juknis.'" >
		<img src="'.base_url().'images/main/excel.png" > Print excel</a>
		';
		$data['k']=$k;
		$data['kabupaten']=$kabupaten;
		$data['kategori']=$kategori;
		$data['button']=$button;
		$data['total_pages']=$total_pages;
		$data['fisik']=$fisik;
		$data['realisasi_daerah']=$realisasi_daerah;
		$data['juknis']=$jk;
		$data['j']=$_GET["j"];
		$data['fisik']=$fisik;
		$data['lokasi']=$lokasi;
		$data['jumlah']=$jumlah;
		$data['t']=$t;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$data['jumlah']=$jumlah;
		$this->load->view('tabel_juknis',$data);
	}

	function table_juknis_indonesia(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$d=0;
		$s=4;
		$kategori=0;
		$juknis=$_GET["j"];
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$jk="tidak ada kegiatan";
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["d"]))$d=$_GET["d"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$idx=0;
		$kabupaten=$k;
		$provinsi=$p;
		$pos=strpos($juknis, '.');
		//kalo ada titiknya berarti sub
		if ($pos  !== false) {
			$id=explode(".",$juknis);
			if(count($id)<3){
			//sub jenis kegiatan	
				if($this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0){
				//ambil data jenis kegiatan
					$jk=$this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->row()->JENIS_DAK;
				//cek apakah kategori atau menu
					if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0){
						$kategori=1;
					}else{
						$kategori=2;	
					}	
				}
			}else{
		// sub sub jenis kegiatan	
				if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->row()->JENIS_KEGIATAN;
					$kategori=2;
			}
		} else {
			if($this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->num_rows()){
				$jk=$this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->row()->JENIS_KEGIATAN;
				if($this->pm->get_where('dak_sub_jenis_dak',$juknis,'ID_JENIS_DAK')->num_rows()!=0){
					$kategori=1;
				}else{
					$kategori=2;	
				}				
			}
		}
		$kabupaten=$this->pm->get_provinsi()->result();
		$total_records = $this->pm->get_provinsi()->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_provinsi()->result() as $index => $row){
			$l='';
			$laporan=$this->pm->dak_laporan_indonesia(0,$t,$row->KodeProvinsi,$d);
			if($laporan->num_rows !=0 ){
				foreach($laporan->result() as $row1){
					$l.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			$l=substr_replace($l, '', -1);
			if($this->pm->sum_kabupaten($l)->num_rows !=0 ){
				$realisasi_daerah[$index]=$this->pm->sum_kegiatan('REALISASI_KEUANGAN_PELAKSANAAN',$l,$juknis)->row()->REALISASI_KEUANGAN_PELAKSANAAN;
				$perencanaan_daerah[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$perencanaan[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$jumlah[$index]=$this->pm->sum_kegiatan('JUMLAH_PELAKSANAAN',$l,$juknis)->row()->JUMLAH_PELAKSANAAN;
				if($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->num_rows !=0){
					foreach ($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->result() as $row){
						$lokasi[$index]=$row->LOKASI_KEGIATAN;
					}
				}else{
					$lokasi[$index]=' - ';
				}
				$fisik2=0;
					foreach($this->pm->dak_kegiatan3($l,$juknis)->result() as $rw){
						if($rw->JUMLAH_TOTAL_PERENCANAAN>0 && $perencanaan[$index] >0){
								$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan[$index];
								$fisik2+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
							}else{
								$fisik2+=0;
							}
						}
					$fisik[]=$fisik2;
				}
				else {$realisasi_daerah[]=0;
					$fisik[]='0%';
				}
		}
		$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_juknis_indonesia?k='.$k.'&t='.$t.'&p='.$p.'&j='.$juknis.'" >
		<img src="'.base_url().'images/main/excel.png" > Print excel</a>
		';
		$data['k']=$k;
		$data['kategori']=$kategori;
		$data['kabupaten']=$kabupaten;
		$data['button']=$button;
		$data['total_pages']=$total_pages;
		$data['fisik']=$fisik;
		$data['realisasi_daerah']=$realisasi_daerah;
		$data['juknis']=$jk;
		$data['j']=$_GET["j"];
		$data['fisik']=$fisik;
		$data['lokasi']=$lokasi;
		$data['jumlah']=$jumlah;
		$data['t']=$t;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$data['jumlah']=$jumlah;
		$this->load->view('tabel_juknis_indonesia',$data);
	}
	function table_edak(){
	//tes komen
		$i=0;
		$j=0;
		$k=0;
		$t=0;
		$p=0;
		$d=0;
		$z=0;
		$n=0;
		$rs='';
		$idx=0;
		if($_GET["k"] && $_GET["d"] && $_GET["p"]){
			$k=$_GET["k"];
			$d=$_GET["d"];
			$p=$_GET["p"];
			$t=$_GET["t"];
		}
		if(isset($_GET["n"]))$n=$_GET["n"];
		$jns=$d;
		if($_GET["fungsi"]){ $fungsi=$_GET["fungsi"];}
		$seluruhjenis=array("jenis_kegiatan" => '',
			"no_urut" => '');
		$dak_kegiatan=array();
	//INPUT soal,nomor urut dari database
		if($this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
			foreach($this->pm->dak_jenis_kegiatan($d)->result() as $row){
				$seluruhjenis[] = array("jenis_kegiatan" => $row->JENIS_KEGIATAN,
					"no_urut" => $row->NO_URUT);
				if($this->pm->dak_sub_kegiatan($row->ID_DAK)->num_rows() > 0){
					foreach($this->pm->dak_sub_kegiatan($row->ID_DAK)->result() as $row2){
						$seluruhjenis[] = array("jenis_kegiatan" => $row2->JENIS_DAK,
							"no_urut" =>$row2->NO_URUT);
						if($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->num_rows() > 0){
							foreach($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->result() as $row3){
								$seluruhjenis[] = array("jenis_kegiatan" => $row3->JENIS_KEGIATAN,
									"no_urut" => $row3->NO_URUT);
								$j++;
							}}
							$i++;
						}}
						//tampilkan
					}}
		//Input Jawaban
					if($this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->num_rows() != 0 && $this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
						$idx=$this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->row()->ID_LAPORAN_DAK;
					if($jns==1 || $jns==5 || $jns==6){
							$kd_rs=$this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->row()->KD_RS;
							$nama=$this->pm->get_where('data_rumah_sakit',$kd_rs,'KODE_RS')->row()->NAMA_RS;
						$rs='<div class="alert alert-danger">Nama Rumah Sakit :'.$nama.'</div><br>';
						}
					$dak_kegiatan=$this->pm->dak_kegiatan($idx)->result();
				}
					
				if($this->pm->data_xl($idx)->num_rows !=0){foreach ($this->pm->data_xl($idx)->result() as $row){
					$z=$row->DATA_DAK;
					$x=$row->DATA_PDF;
					$y=$row->DATA_PDF_PENDUKUNG	;
				}
			}else{
				$z=0;
				$x=0;
				$y=0;
			}
		$data['data_kegiatan']=$dak_kegiatan;
		$data['id_laporan']=$idx;
		$data['rs']=$rs;
		$data['file_pdf']=$x;
		$data['file_pdf_pendukung']=$y;
		$data['file_dak']=$z;
		$data['data_edak']=$seluruhjenis;
		$data['fungsi']=$fungsi;
		$this->load->view('tabel_edak',$data);
	}
		
	function get_rs($id_rs) {
		$data_rs=$this->pm->get_where('data_rumah_sakit',$id_rs,'KODE_RS');
		foreach($data_rs->result() as $row){
			$mystring = '
					<table>
						<tr>
							<td><b>Nama Rumah sakit</b></td>
							<td>Jenis RS</td>
							<td>kota</td>
						</tr>
						<tr>
							<td>'.$row->NAMA_RS.'</td>
							<td>'.$row->JENIS_RS.'</td>
							<td>'.$row->KAB_KOTA.'</td>
						</tr>
					</table>
					';
					echo $mystring;
		}
	}

	function get_rs2($id_rs) {
		$data_rs=$this->pm->get_where('data_rumah_sakit',$id_rs,'KODE_RS');
			foreach($data_rs->result() as $row){
				echo $row->NAMA_RS;
			}
		}

	function table_edak_sarpras(){
			$i=0;
			$j=0;
			$k=0;
			$t=0;
			$p=0;
			$d=0;
			$z=0;
			$n=0;
			$id=0;
			$rs='';
			$sarpras=array();
			if(isset($_GET["fungsi"]))$fungsi=$_GET["fungsi"];
			if(isset($_GET["k"]))$k=$_GET["k"];
			if(isset($_GET["p"]))$p=$_GET["p"];
			if(isset($_GET["t"]))$t=$_GET["t"];
			if(isset($_GET["d"]))$d=$_GET["d"];
			if(isset($_GET["n"]))$n=$_GET["n"];
			$l_sarpras="";
			if($this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->num_rows() != 0){
				foreach($this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->result() as $row){
					$l_sarpras.=$row->ID_LAPORAN_DAK.',';}
					$l_sarpras=substr_replace($l_sarpras, '', -1);
						if($this->pm->dak_kegiatan_sarpras($l_sarpras)->num_rows !=0 ){
								$sarpras=$this->pm->dak_kegiatan_sarpras($l_sarpras)->result();
						}
							$z=$row->DATA_DAK;
							$id=$row->ID_LAPORAN_DAK;
			}
			if($d==5){
				if($this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->num_rows !=0){
					$kd_rs=$this->pm->dak_laporan_edak($k,$t,$p,$d,$n)->row()->KD_RS;
					$nama=$this->pm->get_where('data_rumah_sakit',$kd_rs,'KODE_RS')->row()->NAMA_RS;
					$rs='<div class="alert alert-danger">Nama Rumah Sakit :'.$nama.'</div><br>';
				}
			}
			$data['rs']=$rs;
			$data['id_laporan']=$id;
			$data['file_dak']=$z;
			$data['sarpras']=$sarpras;
			$data['fungsi']=$fungsi;
			$this->load->view('tabel_edak_sarpras',$data);
	}

	function table_edak_nf(){
		$fungsi='edak';
		$i=0;
		$j=0;
		$k=0;
		$t=1;
		$p=0;
		$d=0;
		$z=0;
		$x='file corrupt, harap hubungi admin';
		$y='file corrupt, harap hubungi admin';
		$y='file corrupt, harap hubungi admin';
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["t"]))$t=$_GET["t"];
	//INPUT soal,nomor urut dari database
		//Input Jawaban
		$kegiatan_nf=array();
			if($this->pm->dak_laporan_nf2($k, $t, $p, 4)->num_rows()!=0){
				foreach($this->pm->dak_laporan_nf2($k,$t,$p,4)->result() as $rw){
					$laporan_nf=$rw->ID_LAPORAN_DAK;
				}
			$kegiatan_nf=$this->pm->dak_kegiatan_nf($laporan_nf)->result();
			foreach($this->pm->get_where('dak_laporan_nf',$laporan_nf,'ID_LAPORAN_DAK')->result() as $row){
					$z=$row->DATA_DAK;
					$d=$row->ID_LAPORAN_DAK;
					$x=$row->DATA_PDF;
					$y=$row->DATA_PDF_PENDUKUNG	;
				}
			}
		$data['kegiatan_nf']=$kegiatan_nf;
		$data['id_laporan']=$d;
		$data['file_dak']=$z;
		$data['file_pdf']=$x;
		$data['file_pdf_pendukung']=$y;				
		$data['fungsi']=$fungsi;
		$this->load->view('tabel_edak_nf',$data);
	}
	function table_absensi(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){
				$laporan_farmasi[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,2,$s)->num_rows();
				$laporan_dasar[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,3,$s)->num_rows();
				$laporan_sarpras[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,4,$s)->num_rows();
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,1,$s)->num_rows();
			}
		}
		$persentasefarmasi = array();
		$persentasedasar = array();
		$persentasesarpras = array();
		$persentaserujukan = array();
		foreach($kabupaten as $index => $row){
			$farmasi[$index]=$this->pm->get_where_triple('data_pagu',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',0,'Farmasi >')->num_rows();	
			$dasar[$index]=$this->pm->get_where_triple('data_pagu',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',0,'Pelayanan_Dasar >')->num_rows();		
			$sarpras[$index]=$this->pm->get_where_triple('data_pagu',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',0,'Sarpras >')->num_rows();			
			$rujukan[$index]=$this->pm->get_where_triple('data_pagu',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',0,'Rujukan >')->num_rows();		
			
			$rs[$index]=$this->pm->get_where_double('data_rumah_sakit',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten')->num_rows();
			if($farmasi[$index] > 0){
				$persentasefarmasi[1][$index]=($laporan_farmasi[1][$index]/$farmasi[$index])*100;
	     		$persentasefarmasi[2][$index]=($laporan_farmasi[2][$index]/$farmasi[$index])*100;	
	     		$persentasefarmasi[3][$index]=($laporan_farmasi[3][$index]/$farmasi[$index])*100;	
	     		$persentasefarmasi[4][$index]=($laporan_farmasi[4][$index]/$farmasi[$index])*100;		
			}
			else{
				$persentasefarmasi[1][$index]= '0';
	     		$persentasefarmasi[2][$index]= '0';	
	     		$persentasefarmasi[3][$index]= '0';	
	     		$persentasefarmasi[4][$index]= '0';	
			}
			if($dasar[$index] > 0){
				$persentasedasar[1][$index]=($laporan_dasar[1][$index]/$dasar[$index])*100;
	     		$persentasedasar[2][$index]=($laporan_dasar[2][$index]/$dasar[$index])*100;	
	     		$persentasedasar[3][$index]=($laporan_dasar[3][$index]/$dasar[$index])*100;	
	     		$persentasedasar[4][$index]=($laporan_dasar[4][$index]/$dasar[$index])*100;		
			}
			else{
				$persentasedasar[1][$index]= '0';
	     		$persentasedasar[2][$index]= '0';	
	     		$persentasedasar[3][$index]= '0';	
	     		$persentasedasar[4][$index]= '0';	
			}
			if($sarpras[$index] > 0){
				$persentasesarpras[1][$index]=($laporan_sarpras[1][$index]/$sarpras[$index])*100;
	     		$persentasesarpras[2][$index]=($laporan_sarpras[2][$index]/$sarpras[$index])*100;	
	     		$persentasesarpras[3][$index]=($laporan_sarpras[3][$index]/$sarpras[$index])*100;	
	     		$persentasesarpras[4][$index]=($laporan_sarpras[4][$index]/$sarpras[$index])*100;		
			}
			else{
				$persentasesarpras[1][$index]= '0';
	     		$persentasesarpras[2][$index]= '0';	
	     		$persentasesarpras[3][$index]= '0';	
	     		$persentasesarpras[4][$index]= '0';	
			}
			if($rs[$index] > 0){
				$persentaserujukan[1][$index]=($laporan_rujukan[1][$index]/$rs[$index])*100;
	     		$persentaserujukan[2][$index]=($laporan_rujukan[2][$index]/$rs[$index])*100;	
	     		$persentaserujukan[3][$index]=($laporan_rujukan[3][$index]/$rs[$index])*100;	
	     		$persentaserujukan[4][$index]=($laporan_rujukan[4][$index]/$rs[$index])*100;		
			}
			else{
				$persentaserujukan[1][$index]= '0';
	     		$persentaserujukan[2][$index]= '0';	
	     		$persentaserujukan[3][$index]= '0';	
	     		$persentaserujukan[4][$index]= '0';	
			}			



		}
        $data['kabupaten'] = $kabupaten;
		$data['farmasi'] = $farmasi;
		$data['dasar'] = $dasar;
		$data['sarpras'] = $sarpras;
		$data['rujukan'] = $rs;
		$data['presensi_farmasi'] = $laporan_farmasi;
		$data['presensi_dasar'] =$laporan_dasar;
		$data['presensi_sarpras'] = $laporan_sarpras;
		$data['presensi_rujukan'] = $laporan_rujukan;
		$data['persentasefarmasi'] = $persentasefarmasi;
		$data['persentasedasar'] = $persentasedasar;
		$data['persentasesarpras'] = $persentasesarpras;
		$data['persentaserujukan'] = $persentaserujukan;

		// print_r($laporan_farmasi[2][2]); exit();

		// exit();


		//$data['persentaserujukan'] = $persentaserujukan;
		$this->load->view('tabel_absensi2',$data);		

	}



	function table_absensi_indonesia(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_provinsi()->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_provinsi()->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){
				$laporan_farmasi[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,2,$s)->num_rows();
				$laporan_dasar[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,3,$s)->num_rows();
				$laporan_sarpras[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,4,$s)->num_rows();
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,1,$s)->num_rows();
			}
		}
		$persentasefarmasi = array();
		$persentasedasar = array();
		$persentasesarpras = array();
		$persentaserujukan = array();

		foreach($kabupaten as $index => $row){
			$farmasi[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Farmasi >')->num_rows();	
			$dasar[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Pelayanan_Dasar >')->num_rows();	
			$sarpras[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Sarpras >')->num_rows();		
			$rujukan[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Rujukan >')->num_rows();
			$rs[$index]=$this->pm->get_where('data_rumah_sakit',$row->KodeProvinsi,'KodeProvinsi')->num_rows();
			if($farmasi[$index] > 0){
				$persentasefarmasi[1][$index]=($laporan_farmasi[1][$index]/$farmasi[$index])*100;
	     		$persentasefarmasi[2][$index]=($laporan_farmasi[2][$index]/$farmasi[$index])*100;	
	     		$persentasefarmasi[3][$index]=($laporan_farmasi[3][$index]/$farmasi[$index])*100;	
	     		$persentasefarmasi[4][$index]=($laporan_farmasi[4][$index]/$farmasi[$index])*100;		
			}
			else{
				$persentasefarmasi[1][$index]= '0';
	     		$persentasefarmasi[2][$index]= '0';	
	     		$persentasefarmasi[3][$index]= '0';	
	     		$persentasefarmasi[4][$index]= '0';	
			}
			if($dasar[$index] > 0){
				$persentasedasar[1][$index]=($laporan_dasar[1][$index]/$dasar[$index])*100;
	     		$persentasedasar[2][$index]=($laporan_dasar[2][$index]/$dasar[$index])*100;	
	     		$persentasedasar[3][$index]=($laporan_dasar[3][$index]/$dasar[$index])*100;	
	     		$persentasedasar[4][$index]=($laporan_dasar[4][$index]/$dasar[$index])*100;		
			}
			else{
				$persentasedasar[1][$index]= '0';
	     		$persentasedasar[2][$index]= '0';	
	     		$persentasedasar[3][$index]= '0';	
	     		$persentasedasar[4][$index]= '0';	
			}
			if($sarpras[$index] > 0){
				$persentasesarpras[1][$index]=($laporan_sarpras[1][$index]/$sarpras[$index])*100;
	     		$persentasesarpras[2][$index]=($laporan_sarpras[2][$index]/$sarpras[$index])*100;	
	     		$persentasesarpras[3][$index]=($laporan_sarpras[3][$index]/$sarpras[$index])*100;	
	     		$persentasesarpras[4][$index]=($laporan_sarpras[4][$index]/$sarpras[$index])*100;		
			}
			else{
				$persentasesarpras[1][$index]= '0';
	     		$persentasesarpras[2][$index]= '0';	
	     		$persentasesarpras[3][$index]= '0';	
	     		$persentasesarpras[4][$index]= '0';	
			}
			if($rs[$index] > 0){
				$persentaserujukan[1][$index]=($laporan_rujukan[1][$index]/$rs[$index])*100;
	     		$persentaserujukan[2][$index]=($laporan_rujukan[2][$index]/$rs[$index])*100;	
	     		$persentaserujukan[3][$index]=($laporan_rujukan[3][$index]/$rs[$index])*100;	
	     		$persentaserujukan[4][$index]=($laporan_rujukan[4][$index]/$rs[$index])*100;		
			}
			else{
				$persentaserujukan[1][$index]= '0';
	     		$persentaserujukan[2][$index]= '0';	
	     		$persentaserujukan[3][$index]= '0';	
	     		$persentaserujukan[4][$index]= '0';	
			}			



		}
		


		
        $data['kabupaten'] = $kabupaten;
		$data['farmasi'] = $farmasi;
		$data['dasar'] = $dasar;
		$data['sarpras'] = $sarpras;
		$data['rujukan'] = $rs;
		$data['presensi_farmasi'] = $laporan_farmasi;
		$data['presensi_dasar'] =$laporan_dasar;
		$data['presensi_sarpras'] = $laporan_sarpras;
		$data['presensi_rujukan'] = $laporan_rujukan;
		$data['persentasefarmasi'] = $persentasefarmasi;
		$data['persentasedasar'] = $persentasedasar;
		$data['persentasesarpras'] = $persentasesarpras;
		$data['persentaserujukan'] = $persentaserujukan;

		// print_r($laporan_farmasi[2][2]); exit();

		// exit();

		$this->load->view('tabel_absensi',$data);			

	}
	function table_kelengkapan(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; $prevpage=1; $currentpage =1; $nextpage = 2;
		}
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result() as $index => $row){
			$laporan_farmasi[]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$t,$row->KodeProvinsi,2,$s)->result();
			$laporan_rujukan[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,1,$s)->result();
			$laporan_rujukan_pr[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,6,$s)->result();
			$laporan_dasar[]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$t,$row->KodeProvinsi,3,$s)->result();
			$laporan_sarpras[]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$t,$row->KodeProvinsi,4,$s)->result();
			$laporan_sarpras_rjk[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,5,$s)->result();
		}
	
	
		$data['k']=$k;
		$data['laporan_farmasi']=$laporan_farmasi;
		$data['laporan_rujukan']=$laporan_rujukan;
		$data['laporan_rujukan_pr']=$laporan_rujukan_pr;	
		$data['laporan_dasar']=$laporan_dasar;
		$data['laporan_sarpras']=$laporan_sarpras;
		$data['laporan_sarpras_rjk']=$laporan_sarpras_rjk;
		$data['kabupaten']=$kabupaten;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_kelengkapan',$data);
	}

	function print_kelengkapan(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){
				$laporan_farmasi[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,2,$s)->num_rows();
				$laporan_dasar[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,3,$s)->num_rows();
				$laporan_sarpras[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,4,$s)->num_rows();
			}
		}


    	$style = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
       	 )
   		);
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:E4');
		$this->excel->getActiveSheet()->getStyle("B4:E4")->applyFromArray($style);
		$this->excel->getActiveSheet()->setCellValue('F4', 'Dasar');
		$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('F4:I4');
		$this->excel->getActiveSheet()->getStyle("F4:I4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('J4', 'Sarpras');
		$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('J4:M4');
		$this->excel->getActiveSheet()->getStyle("J4:M4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('D5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 4');			
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('G5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 4');		
		$this->excel->getActiveSheet()->setCellValue('J5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('M5', 'Triwulan 4');						
		$i=6;
		$b=6;
		foreach($kabupaten as $index => $row){
			$farmasi=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 2,2);	
			$dasar=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 3, 2);
			$sarpras=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 4, 2);		

			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
     		if($farmasi==1){				
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $laporan_farmasi[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_farmasi[2][$index]);	
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $laporan_farmasi[3][$index]);	
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $laporan_farmasi[4][$index]);	
			}else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, 'Tidak Ada Pagu');
				$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
				$this->excel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->applyFromArray($style);			
			}
			if($dasar==1){
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_dasar[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $laporan_dasar[2][$index]);	
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $laporan_dasar[3][$index]);	
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_dasar[4][$index]);
			}else{
				$this->excel->getActiveSheet()->setCellValue('F'.$i, 'Tidak Ada Pagu');
				$this->excel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->mergeCells('F'.$i.':I'.$i);
				$this->excel->getActiveSheet()->getStyle('F'.$i.':I'.$i)->applyFromArray($style);						
			}
			if($sarpras==1){				
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $laporan_sarpras[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $laporan_sarpras[2][$index]);	
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_sarpras[3][$index]);	
				$this->excel->getActiveSheet()->setCellValue('M'.$i, $laporan_sarpras[4][$index]);
			}else{
				$this->excel->getActiveSheet()->setCellValue('J'.$i, 'Tidak Ada Pagu');
				$this->excel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->mergeCells('J'.$i.':M'.$i);	
				$this->excel->getActiveSheet()->getStyle('J'.$i.':M'.$i)->applyFromArray($style);						
		}
			$i++;			
		}
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}

	function print_absensi_indonesia(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_provinsi()->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_provinsi()->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){
				$laporan_farmasi[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,2,$s)->num_rows();
				$laporan_dasar[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,3,$s)->num_rows();
				$laporan_sarpras[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,4,$s)->num_rows();
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_indonesia2(0,$tw,$row->KodeProvinsi,1,$s)->num_rows();
			}
		}


    	$style = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
       	 )
   		);
    	$stylecell = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
       	 )
   		);   		
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Absensi Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Absensi');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->getStyle("B4:M4")->applyFromArray($style);
		$this->excel->getActiveSheet()->setCellValue('N4', 'Dasar');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N4:Y4');
		$this->excel->getActiveSheet()->getStyle("N4:Y4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('Z4', 'Sarpras');
		$this->excel->getActiveSheet()->getStyle('Z4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('Z4:AI4');
		$this->excel->getActiveSheet()->getStyle("Z4:AI4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('AL4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('AL4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('AL4:AW4');
		$this->excel->getActiveSheet()->getStyle("AL4:AW4")->applyFromArray($style);			
		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		//dasar						
		$this->excel->getActiveSheet()->setCellValue('N5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('N5:P5');			
		$this->excel->getActiveSheet()->setCellValue('Q5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('Q5:S5');				
		$this->excel->getActiveSheet()->setCellValue('T5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('T5:V5');				
		$this->excel->getActiveSheet()->setCellValue('W5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('W5:Y5');
		//Sarpras						
		$this->excel->getActiveSheet()->setCellValue('Z5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('Z5:AB5');			
		$this->excel->getActiveSheet()->setCellValue('AC5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('AC5:AE5');			
		$this->excel->getActiveSheet()->setCellValue('AF5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('AF5:AH5');
		$this->excel->getActiveSheet()->setCellValue('AI5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('AI5:AK5');
		//Rujukan						
		$this->excel->getActiveSheet()->setCellValue('AL5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('AL5:AN5');			
		$this->excel->getActiveSheet()->setCellValue('AO5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('AO5:AQ5');			
		$this->excel->getActiveSheet()->setCellValue('AR5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('AR5:AT5');
		$this->excel->getActiveSheet()->setCellValue('AU5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('AU5:AW5');				
		
		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', 'persentase');
		//dasar
		$this->excel->getActiveSheet()->setCellValue('N6', 'N');
		$this->excel->getActiveSheet()->setCellValue('O6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('P6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('Q6', 'N');
		$this->excel->getActiveSheet()->setCellValue('R6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('S6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('T6', 'N');
		$this->excel->getActiveSheet()->setCellValue('U6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('V6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('W6', 'N');
		$this->excel->getActiveSheet()->setCellValue('X6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Y6', 'persentase');
		//sarpras
		$this->excel->getActiveSheet()->setCellValue('Z6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AA6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AB6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('AC6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AD6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AE6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('AF6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AG6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AH6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('AI6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AJ6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AK6', 'persentase');		
		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('AL6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AM6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AN6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('AO6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AP6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AQ6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('AR6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AS6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AT6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('AU6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AV6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AW6', 'persentase');																
		$i=7;
		$b=7;
		foreach($kabupaten as $index => $row){
			$farmasi[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Farmasi >')->num_rows();	
			$dasar[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Pelayanan_Dasar >')->num_rows();	
			$sarpras[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Sarpras >')->num_rows();		
			$rujukan[$index]=$this->pm->get_where_double('data_pagu',$row->KodeProvinsi,'KodeProvinsi',0,'Rujukan >')->num_rows();
			$rs[$index]=$this->pm->get_where('data_rumah_sakit',$row->KodeProvinsi,'KodeProvinsi')->num_rows();	
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);
     		if($farmasi[$index]>0){
     		    $persentase[1]=($laporan_farmasi[1][$index]/$farmasi[$index])*100;
     		    $persentase[2]=($laporan_farmasi[2][$index]/$farmasi[$index])*100;	
     		    $persentase[3]=($laporan_farmasi[3][$index]/$farmasi[$index])*100;	
     		    $persentase[4]=($laporan_farmasi[4][$index]/$farmasi[$index])*100;					
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $farmasi[$index]);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_farmasi[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i,  round($persentase[1], 2));		
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $farmasi[$index]);
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_farmasi[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i,  round($persentase[2], 2));	
				$this->excel->getActiveSheet()->setCellValue('H'.$i,$farmasi[$index]);
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_farmasi[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('J'.$i,  round($persentase[3], 2));	
				$this->excel->getActiveSheet()->setCellValue('K'.$i,$farmasi[$index]);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_farmasi[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('M'.$i,  round($persentase[4], 2));	
			}else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('C'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('D'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('E'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('F'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('G'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('I'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('L'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');				
			}
    		if($dasar[$index]>0){
     		    $persentase[1]=($laporan_dasar[1][$index]/$dasar[$index])*100;
     		    $persentase[2]=($laporan_dasar[2][$index]/$dasar[$index])*100;	
     		    $persentase[3]=($laporan_dasar[3][$index]/$dasar[$index])*100;	
     		    $persentase[4]=($laporan_dasar[4][$index]/$dasar[$index])*100;					
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $dasar[$index]);
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $laporan_dasar[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('P'.$i,  round($persentase[1], 2));		
				$this->excel->getActiveSheet()->setCellValue('Q'.$i,  $dasar[$index]);
				$this->excel->getActiveSheet()->setCellValue('R'.$i, $laporan_dasar[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('S'.$i,  round($persentase[2], 2));	
				$this->excel->getActiveSheet()->setCellValue('T'.$i,  $dasar[$index]);
				$this->excel->getActiveSheet()->setCellValue('U'.$i, $laporan_dasar[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('V'.$i,  round($persentase[3], 2));	
				$this->excel->getActiveSheet()->setCellValue('W'.$i,  $dasar[$index]);
				$this->excel->getActiveSheet()->setCellValue('X'.$i, $laporan_dasar[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('Y'.$i,  round($persentase[4], 2));	
			}else{

				$this->excel->getActiveSheet()->setCellValue('N'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('O'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('P'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('R'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('S'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('T'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('u'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('V'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('W'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('X'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('Y'.$i, '0');				
			}
    		if($sarpras[$index]>0){
     		    $persentase[1]=($laporan_sarpras[1][$index]/$sarpras[$index])*100;
     		    $persentase[2]=($laporan_sarpras[2][$index]/$sarpras[$index])*100;	
     		    $persentase[3]=($laporan_sarpras[3][$index]/$sarpras[$index])*100;	
     		    $persentase[4]=($laporan_sarpras[4][$index]/$sarpras[$index])*100;					
				$this->excel->getActiveSheet()->setCellValue('Z'.$i, $sarpras[$index]);
				$this->excel->getActiveSheet()->setCellValue('AA'.$i, $laporan_sarpras[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('AB'.$i,  round($persentase[1], 2));		
				$this->excel->getActiveSheet()->setCellValue('AC'.$i,$sarpras[$index]);
				$this->excel->getActiveSheet()->setCellValue('AD'.$i, $laporan_sarpras[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('AE'.$i,  round($persentase[2], 2));	
				$this->excel->getActiveSheet()->setCellValue('AF'.$i,$sarpras[$index]);
				$this->excel->getActiveSheet()->setCellValue('AG'.$i, $laporan_sarpras[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('AH'.$i,  round($persentase[3], 2));	
				$this->excel->getActiveSheet()->setCellValue('AI'.$i,$sarpras[$index]);
				$this->excel->getActiveSheet()->setCellValue('AJ'.$i, $laporan_sarpras[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('AK'.$i,  round($persentase[4], 2));	
			}else{
				$this->excel->getActiveSheet()->setCellValue('Z'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AA'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AB'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('AC'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AD'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AE'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('AF'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AG'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AH'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('AI'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AJ'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AK'.$i, '0');				
			}
    		if($rujukan[$index]>0){
     		    $persentase[1]=($laporan_rujukan[1][$index]/$rs[$index])*100;
     		    $persentase[2]=($laporan_rujukan[2][$index]/$rs[$index])*100;	
     		    $persentase[3]=($laporan_rujukan[3][$index]/$rs[$index])*100;	
     		    $persentase[4]=($laporan_rujukan[4][$index]/$rs[$index])*100;					
				$this->excel->getActiveSheet()->setCellValue('AL'.$i, $rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('AM'.$i, $laporan_rujukan[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('AN'.$i,  round($persentase[1], 2));		
				$this->excel->getActiveSheet()->setCellValue('AO'.$i,$rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('AP'.$i, $laporan_rujukan[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('AQ'.$i,  round($persentase[2], 2));	
				$this->excel->getActiveSheet()->setCellValue('AR'.$i,$rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('AS'.$i, $laporan_rujukan[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('AT'.$i,  round($persentase[3], 2));	
				$this->excel->getActiveSheet()->setCellValue('AU'.$i,$rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('AV'.$i, $laporan_rujukan[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('AW'.$i,  round($persentase[4], 2));	
			}else{
				$this->excel->getActiveSheet()->setCellValue('AL'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AM'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AN'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('AO'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AP'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AQ'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('AR'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AS'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AT'.$i, '0');		
				$this->excel->getActiveSheet()->setCellValue('AU'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AV'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AW'.$i, '0');				
			}									
		    $this->excel->getActiveSheet()->getStyle("B".$i.":AW".$i)->applyFromArray($stylecell);
			$i++;	

		}
		//total
		$this->excel->getActiveSheet()->getStyle('A'.$i.':AW'.$i)->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle("B".$i.":AW".$i)->applyFromArray($stylecell);
		$this->excel->getActiveSheet()->setCellValue('A'.$i, 'TOTAL');
	    $persentase[1]=(array_sum($laporan_farmasi[1])/array_sum($farmasi))*100;
	    $persentase[2]=(array_sum($laporan_farmasi[2])/array_sum($farmasi))*100;
	    $persentase[2]=(array_sum($laporan_farmasi[3])/array_sum($farmasi))*100;
	    $persentase[4]=(array_sum($laporan_farmasi[4])/array_sum($farmasi))*100;			
		$this->excel->getActiveSheet()->setCellValue('B'.$i, array_sum($farmasi));
		$this->excel->getActiveSheet()->setCellValue('C'.$i, array_sum($laporan_farmasi[1]));
		$this->excel->getActiveSheet()->setCellValue('D'.$i,  round($persentase[1], 2));		
		$this->excel->getActiveSheet()->setCellValue('E'.$i, array_sum($farmasi));
		$this->excel->getActiveSheet()->setCellValue('F'.$i, array_sum($laporan_farmasi[2]));
		$this->excel->getActiveSheet()->setCellValue('G'.$i,  round($persentase[2], 2));	
		$this->excel->getActiveSheet()->setCellValue('H'.$i, array_sum($farmasi));
		$this->excel->getActiveSheet()->setCellValue('I'.$i, array_sum($laporan_farmasi[3]));
		$this->excel->getActiveSheet()->setCellValue('J'.$i,  round($persentase[3], 2));	
		$this->excel->getActiveSheet()->setCellValue('K'.$i, array_sum($farmasi));
		$this->excel->getActiveSheet()->setCellValue('L'.$i,array_sum($laporan_farmasi[4]));
		$this->excel->getActiveSheet()->setCellValue('M'.$i,  round($persentase[4], 2));
	    $persentase[1]=(array_sum($laporan_dasar[1])/array_sum($dasar))*100;
	    $persentase[2]=(array_sum($laporan_dasar[2])/array_sum($dasar))*100;
	    $persentase[2]=(array_sum($laporan_dasar[3])/array_sum($dasar))*100;
	    $persentase[4]=(array_sum($laporan_dasar[4])/array_sum($dasar))*100;					
		$this->excel->getActiveSheet()->setCellValue('N'.$i, array_sum($dasar));
		$this->excel->getActiveSheet()->setCellValue('O'.$i, array_sum($laporan_dasar[1]));
		$this->excel->getActiveSheet()->setCellValue('P'.$i,  round($persentase[1], 2));		
		$this->excel->getActiveSheet()->setCellValue('Q'.$i,  array_sum($dasar));
		$this->excel->getActiveSheet()->setCellValue('R'.$i,array_sum($laporan_dasar[2]));
		$this->excel->getActiveSheet()->setCellValue('S'.$i,  round($persentase[2], 2));	
		$this->excel->getActiveSheet()->setCellValue('T'.$i,  array_sum($dasar));
		$this->excel->getActiveSheet()->setCellValue('U'.$i, array_sum($laporan_dasar[3]));
		$this->excel->getActiveSheet()->setCellValue('V'.$i,  round($persentase[3], 2));	
		$this->excel->getActiveSheet()->setCellValue('W'.$i,  array_sum($dasar));
		$this->excel->getActiveSheet()->setCellValue('X'.$i, array_sum($laporan_dasar[4]));
		$this->excel->getActiveSheet()->setCellValue('Y'.$i,  round($persentase[4], 2));
	    $persentase[1]=(array_sum($laporan_sarpras[1])/array_sum($sarpras))*100;
	    $persentase[2]=(array_sum($laporan_sarpras[2])/array_sum($sarpras))*100;
	    $persentase[3]=(array_sum($laporan_sarpras[3])/array_sum($sarpras))*100;
	    $persentase[4]=(array_sum($laporan_sarpras[4])/array_sum($sarpras))*100;			
		$this->excel->getActiveSheet()->setCellValue('Z'.$i, array_sum($sarpras));
		$this->excel->getActiveSheet()->setCellValue('AA'.$i, array_sum($laporan_sarpras[1]));
		$this->excel->getActiveSheet()->setCellValue('AB'.$i,  round($persentase[1], 2));		
		$this->excel->getActiveSheet()->setCellValue('AC'.$i,array_sum($sarpras));
		$this->excel->getActiveSheet()->setCellValue('AD'.$i,  array_sum($laporan_sarpras[2]));
		$this->excel->getActiveSheet()->setCellValue('AE'.$i,  round($persentase[2], 2));	
		$this->excel->getActiveSheet()->setCellValue('AF'.$i,array_sum($sarpras));
		$this->excel->getActiveSheet()->setCellValue('AG'.$i,  array_sum($laporan_sarpras[3]));
		$this->excel->getActiveSheet()->setCellValue('AH'.$i,  round($persentase[3], 2));	
		$this->excel->getActiveSheet()->setCellValue('AI'.$i,array_sum($sarpras));
		$this->excel->getActiveSheet()->setCellValue('AJ'.$i,  array_sum($laporan_sarpras[4]));
		$this->excel->getActiveSheet()->setCellValue('AK'.$i,  round($persentase[4], 2));	
	    $persentase[1]=(array_sum($laporan_rujukan[1])/array_sum($rs))*100;
	    $persentase[2]=(array_sum($laporan_rujukan[2])/array_sum($rs))*100;
	    $persentase[3]=(array_sum($laporan_rujukan[3])/array_sum($rs))*100;
	    $persentase[4]=(array_sum($laporan_rujukan[4])/array_sum($rs))*100;					
		$this->excel->getActiveSheet()->setCellValue('AL'.$i,array_sum($rs));
		$this->excel->getActiveSheet()->setCellValue('AM'.$i, array_sum($laporan_rujukan[1]));
		$this->excel->getActiveSheet()->setCellValue('AN'.$i,  round($persentase[1], 2));		
		$this->excel->getActiveSheet()->setCellValue('AO'.$i,array_sum($rs));
		$this->excel->getActiveSheet()->setCellValue('AP'.$i, array_sum($laporan_rujukan[2]));
		$this->excel->getActiveSheet()->setCellValue('AQ'.$i,  round($persentase[2], 2));	
		$this->excel->getActiveSheet()->setCellValue('AR'.$i,array_sum($rs));
		$this->excel->getActiveSheet()->setCellValue('AS'.$i, array_sum($laporan_rujukan[3]));
		$this->excel->getActiveSheet()->setCellValue('AT'.$i,  round($persentase[3], 2));	
		$this->excel->getActiveSheet()->setCellValue('AU'.$i,array_sum($rs));
		$this->excel->getActiveSheet()->setCellValue('AV'.$i, array_sum($laporan_rujukan[4]));
		$this->excel->getActiveSheet()->setCellValue('AW'.$i,  round($persentase[4], 2));					


		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}

	// function print_absensi_indonesia2(){
	// 	$d=0;
	// 	$k='00';
	// 	$p=0;
	// 	$s=4;
	// 	$kat=0;
	// 	if(isset($_GET["k"]))$k=$_GET["k"];
	// 	if(isset($_GET["p"]))$p=$_GET["p"];
	// 	if(isset($_GET["s"]))$s=$_GET["s"];
	// 	if(isset($_GET["kat"]))$kat=$_GET["kat"];
		
 //    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
 //        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
 //        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

	// 	$this->excel->setActiveSheetIndex(0);
	// 	$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
	// 	$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan DAK');
	// 	foreach (range('B', 'Y') as $char) {
	// 		$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
	// 	}
	// 	$this->excel->getActiveSheet()->getStyle("A4:BI4")->applyFromArray($styleArrayHead);
	// 	$this->excel->getActiveSheet()->getStyle("A5:BI5")->applyFromArray($styleArrayHead);
	// 	$this->excel->getActiveSheet()->getStyle("A6:BI6")->applyFromArray($styleArrayHead);
	// 	$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
	// 	$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
	// 	$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('A1:Y1');
	// 	$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	// 	//header
	// 	$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
	// 	$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('A4:A6');
	// 	$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
	// 	$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('B4:M4');
	// 	$this->excel->getActiveSheet()->setCellValue('N4', 'Dasar');
	// 	$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('N4:Y4');
	// 	$this->excel->getActiveSheet()->setCellValue('Z4', 'Rujukan');
	// 	$this->excel->getActiveSheet()->getStyle('Z4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('Z4:AK4');
	// 	$this->excel->getActiveSheet()->setCellValue('AL4', 'Afirmasi');
	// 	$this->excel->getActiveSheet()->getStyle('AL4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('AL4:AW4');
	// 	$this->excel->getActiveSheet()->setCellValue('AX4', 'Penugasan');
	// 	$this->excel->getActiveSheet()->getStyle('AX4')->getFont()->setBold(true);
	// 	$this->excel->getActiveSheet()->mergeCells('AX4:BI4');


	// 	//farmasi
	// 	$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
	// 	$this->excel->getActiveSheet()->mergeCells('B5:D5');		
	// 	$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
	// 	$this->excel->getActiveSheet()->mergeCells('E5:G5');			
	// 	$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
	// 	$this->excel->getActiveSheet()->mergeCells('H5:J5');					
	// 	$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
	// 	$this->excel->getActiveSheet()->mergeCells('K5:M5');
	// 	//dasar						
	// 	$this->excel->getActiveSheet()->setCellValue('N5', 'Triwulan 1');
	// 	$this->excel->getActiveSheet()->mergeCells('N5:P5');			
	// 	$this->excel->getActiveSheet()->setCellValue('Q5', 'Triwulan 2');
	// 	$this->excel->getActiveSheet()->mergeCells('Q5:S5');				
	// 	$this->excel->getActiveSheet()->setCellValue('T5', 'Triwulan 3');
	// 	$this->excel->getActiveSheet()->mergeCells('T5:V5');				
	// 	$this->excel->getActiveSheet()->setCellValue('W5', 'Triwulan 4');
	// 	$this->excel->getActiveSheet()->mergeCells('W5:Y5');
	// 	//RUJUKAN
	// 	$this->excel->getActiveSheet()->setCellValue('Z5', 'Triwulan 1');
	// 	$this->excel->getActiveSheet()->mergeCells('Z5:AB5');			
	// 	$this->excel->getActiveSheet()->setCellValue('AC5', 'Triwulan 2');
	// 	$this->excel->getActiveSheet()->mergeCells('AC5:AE5');				
	// 	$this->excel->getActiveSheet()->setCellValue('AF5', 'Triwulan 3');
	// 	$this->excel->getActiveSheet()->mergeCells('AF5:AH5');				
	// 	$this->excel->getActiveSheet()->setCellValue('AI5', 'Triwulan 4');
	// 	$this->excel->getActiveSheet()->mergeCells('AI5:AK5');
	// 	//Afirmasi
	// 	$this->excel->getActiveSheet()->setCellValue('AL5', 'Triwulan 1');
	// 	$this->excel->getActiveSheet()->mergeCells('AL5:AN5');			
	// 	$this->excel->getActiveSheet()->setCellValue('AO5', 'Triwulan 2');
	// 	$this->excel->getActiveSheet()->mergeCells('AO5:AQ5');				
	// 	$this->excel->getActiveSheet()->setCellValue('AR5', 'Triwulan 3');
	// 	$this->excel->getActiveSheet()->mergeCells('AR5:AT5');				
	// 	$this->excel->getActiveSheet()->setCellValue('AU5', 'Triwulan 4');
	// 	$this->excel->getActiveSheet()->mergeCells('AU5:AW5');
	// 	//Penugasan
	// 	$this->excel->getActiveSheet()->setCellValue('AX5', 'Triwulan 1');
	// 	$this->excel->getActiveSheet()->mergeCells('AX5:AZ5');			
	// 	$this->excel->getActiveSheet()->setCellValue('BA5', 'Triwulan 2');
	// 	$this->excel->getActiveSheet()->mergeCells('BA5:BC5');				
	// 	$this->excel->getActiveSheet()->setCellValue('BD5', 'Triwulan 3');
	// 	$this->excel->getActiveSheet()->mergeCells('BD5:BF5');				
	// 	$this->excel->getActiveSheet()->setCellValue('BG5', 'Triwulan 4');
	// 	$this->excel->getActiveSheet()->mergeCells('BG5:BI5');


	// 	//farmasi
	// 	$this->excel->getActiveSheet()->setCellValue('B6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('D6', '%');		
	// 	$this->excel->getActiveSheet()->setCellValue('E6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('G6', '%');
	// 	$this->excel->getActiveSheet()->setCellValue('H6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('J6', '%');	
	// 	$this->excel->getActiveSheet()->setCellValue('K6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('M6', '%');

	// 	//dasar
	// 	$this->excel->getActiveSheet()->setCellValue('N6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('O6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('P6', '%');		
	// 	$this->excel->getActiveSheet()->setCellValue('Q6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('R6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('S6', '%');
	// 	$this->excel->getActiveSheet()->setCellValue('T6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('U6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('V6', '%');	
	// 	$this->excel->getActiveSheet()->setCellValue('W6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('X6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('Y6', '%');											
	// 	//rujukan
	// 	$this->excel->getActiveSheet()->setCellValue('Z6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AA6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AB6', '%');		
	// 	$this->excel->getActiveSheet()->setCellValue('AC6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AD6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AE6', '%');
	// 	$this->excel->getActiveSheet()->setCellValue('AF6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AG6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AH6', '%');	
	// 	$this->excel->getActiveSheet()->setCellValue('AI6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AJ6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AK6', '%');
	// 	//Afirmasi
	// 	$this->excel->getActiveSheet()->setCellValue('AL6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AM6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AN6', '%');		
	// 	$this->excel->getActiveSheet()->setCellValue('AO6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AP6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AQ6', '%');
	// 	$this->excel->getActiveSheet()->setCellValue('AR6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AS6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AT6', '%');	
	// 	$this->excel->getActiveSheet()->setCellValue('AU6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AV6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AW6', '%');
	// 	//Penugasan
	// 	$this->excel->getActiveSheet()->setCellValue('AX6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('AY6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('AZ6', '%');		
	// 	$this->excel->getActiveSheet()->setCellValue('BA6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('BB6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('BC6', '%');
	// 	$this->excel->getActiveSheet()->setCellValue('BD6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('BE6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('BF6', '%');	
	// 	$this->excel->getActiveSheet()->setCellValue('BG6', 'N');
	// 	$this->excel->getActiveSheet()->setCellValue('BH6', 'Kumpul');
	// 	$this->excel->getActiveSheet()->setCellValue('BI6', '%');
	// 	$i = 7;
	// 	$data_provinsi = $this->pm->get_provinsi()->result();
	// 	foreach ($data_provinsi as $row) {
	// 		$this->excel->getActiveSheet()->setCellValue('A'. $i, $row->NamaProvinsi);
	// 		$i++;


	// 		//farmasi
	// 		// $this->pm->get_where_triple("pagu_seluruh", $row->KodeProvinsi, "KodeProvinsi", 2, "ID_SUBBIDANG" )->num_rows();
	// 	}

	// 	$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
	// 	ob_end_clean();
	// 	header('Content-Type: application/vnd.ms-excel'); //mime type
	// 	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
	// 	header('Cache-Control: max-age=0'); //no cache
	// 	//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
	// 	//if you want to save it as .XLSX Excel 2007 format
	// 	$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
	// 	//force user to download the Excel file without writing it to server's HD
	// 	$objWriter->save('php://output');

	// }	
	function print_absensi(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){
				$laporan_farmasi[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,2,$s)->num_rows();
				$laporan_dasar[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,3,$s)->num_rows();
				$laporan_sarpras[$tw][]=$this->pm->dak_laporan_status2($row->KodeKabupaten,$tw,$row->KodeProvinsi,4,$s)->num_rows();
			}
		}


    	$style = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
       	 )
   		);
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->getStyle("B4:M4")->applyFromArray($style);
		$this->excel->getActiveSheet()->setCellValue('N4', 'Dasar');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N4:Y4');
		$this->excel->getActiveSheet()->getStyle("N4:Y4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('Z4', 'Sarpras');
		$this->excel->getActiveSheet()->getStyle('Z4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('Z4:AI4');
		$this->excel->getActiveSheet()->getStyle("Z4:AI4")->applyFromArray($style);	
		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		//dasar						
		$this->excel->getActiveSheet()->setCellValue('N5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('N5:P5');			
		$this->excel->getActiveSheet()->setCellValue('Q5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('Q5:S5');				
		$this->excel->getActiveSheet()->setCellValue('T5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('T5:V5');				
		$this->excel->getActiveSheet()->setCellValue('W5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('W5:Y5');
		//Sarpras						
		$this->excel->getActiveSheet()->setCellValue('Z5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('Z5:AB5');			
		$this->excel->getActiveSheet()->setCellValue('AC5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('AC5:AE5');			
		$this->excel->getActiveSheet()->setCellValue('AF5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('AF5:AH5');
		$this->excel->getActiveSheet()->setCellValue('AI5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('AI5:AK5');		
		
		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', 'persentase');
		//dasar
		$this->excel->getActiveSheet()->setCellValue('N6', 'N');
		$this->excel->getActiveSheet()->setCellValue('O6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('P6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('Q6', 'N');
		$this->excel->getActiveSheet()->setCellValue('R6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('S6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('T6', 'N');
		$this->excel->getActiveSheet()->setCellValue('U6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('V6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('W6', 'N');
		$this->excel->getActiveSheet()->setCellValue('X6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Y6', 'persentase');
		//sarpras
		$this->excel->getActiveSheet()->setCellValue('Z6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AA6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AB6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('AC6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AD6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AE6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('AF6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AG6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AH6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('AI6', 'N');
		$this->excel->getActiveSheet()->setCellValue('AJ6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('AK6', 'persentase');														
		$i=7;
		$b=7;
		foreach($kabupaten as $index => $row){
			$farmasi=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 2,2);	
			$dasar=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 3, 2);
			$sarpras=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 4, 2);		

			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
     		if($farmasi==1){
     		    $persentase[1]=($laporan_farmasi[1][$index]/1)*100;
     		    $persentase[2]=($laporan_farmasi[2][$index]/1)*100;	
     		    $persentase[3]=($laporan_farmasi[3][$index]/1)*100;	
     		    $persentase[4]=($laporan_farmasi[4][$index]/1)*100;					
				$this->excel->getActiveSheet()->setCellValue('B'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_farmasi[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i,  round($persentase[1], 2).'%');		
				$this->excel->getActiveSheet()->setCellValue('E'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_farmasi[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i,  round($persentase[2], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('H'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_farmasi[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('J'.$i,  round($persentase[3], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_farmasi[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('M'.$i,  round($persentase[4], 2).'%');	
			}else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('C'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('D'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('E'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('F'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('G'.$i, '0%');	
				$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('I'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('J'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('L'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('M'.$i, '0%');				
			}
    		if($dasar==1){
     		    $persentase[1]=($laporan_dasar[1][$index]/1)*100;
     		    $persentase[2]=($laporan_dasar[2][$index]/1)*100;	
     		    $persentase[3]=($laporan_dasar[3][$index]/1)*100;	
     		    $persentase[4]=($laporan_dasar[4][$index]/1)*100;					
				$this->excel->getActiveSheet()->setCellValue('N'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $laporan_dasar[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('P'.$i,  round($persentase[1], 2).'%');		
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('R'.$i, $laporan_dasar[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('S'.$i,  round($persentase[2], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('T'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('U'.$i, $laporan_dasar[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('V'.$i,  round($persentase[3], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('W'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('X'.$i, $laporan_dasar[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('Y'.$i,  round($persentase[4], 2).'%');	
			}else{
				$this->excel->getActiveSheet()->setCellValue('N'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('O'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('P'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('R'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('S'.$i, '0%');	
				$this->excel->getActiveSheet()->setCellValue('T'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('u'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('V'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('W'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('X'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('Y'.$i, '0%');				
			}
    		if($sarpras==1){
     		    $persentase[1]=($laporan_sarpras[1][$index]/1)*100;
     		    $persentase[2]=($laporan_sarpras[2][$index]/1)*100;	
     		    $persentase[3]=($laporan_sarpras[3][$index]/1)*100;	
     		    $persentase[4]=($laporan_sarpras[4][$index]/1)*100;					
				$this->excel->getActiveSheet()->setCellValue('Z'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('AA'.$i, $laporan_sarpras[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('AB'.$i,  round($persentase[1], 2).'%');		
				$this->excel->getActiveSheet()->setCellValue('AC'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('AD'.$i, $laporan_sarpras[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('AE'.$i,  round($persentase[2], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('AF'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('AG'.$i, $laporan_sarpras[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('AH'.$i,  round($persentase[3], 2).'%');	
				$this->excel->getActiveSheet()->setCellValue('AI'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('AJ'.$i, $laporan_sarpras[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('AK'.$i,  round($persentase[4], 2).'%');	
			}else{
				$this->excel->getActiveSheet()->setCellValue('Z'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AA'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AB'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('AC'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AD'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AE'.$i, '0%');	
				$this->excel->getActiveSheet()->setCellValue('AF'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AG'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AH'.$i, '0%');		
				$this->excel->getActiveSheet()->setCellValue('AI'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AJ'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('AK'.$i, '0%');				
			}					
		
			$i++;			
		}
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}

	function print_absensi2_nf(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Presensi DAK Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan DAK Non Fisik');
		foreach (range('B', 'M') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle("A4:M4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A5:M5")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A6:M6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:M1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'DAK Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');


		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');

		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', '%');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', '%');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', '%');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', '%');


		$tahun = $this->session->userdata("thn_anggaran");
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		$i = 7;
		$b=7;

		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'. $i, $row->NamaKabupaten);
				
		
			$N = $this->pm->get_where_triple('pagu_seluruh_nf',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten', $tahun ,"TAHUN_ANGGARAN")->result();
			$col = 'B';
			for($index =1 ; $index<=4 ; $index++){
				if($N != null){
					if($N[0]->pagu_seluruh > 0){
						$this->excel->getActiveSheet()->setCellValue($col. $i, '1');
					}
					else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
					}
				}
				else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
				}
				$col++;
				$k = $this->pm->get_where_quadruple('pengajuan_monev_nf',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten', $index, 'WAKTU_LAPORAN', $tahun, "TAHUN_ANGGARAN")->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($k > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, '100%');
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col ++;
			}

			$i++;
		}
		$col=$col--;
		$i--;
		$this->excel->getActiveSheet()->getStyle('A7:' .'M' . $i)->applyFromArray($styleArray);
		$filename='Presens_DAK_NONFISIK.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_absensi_indonesia2_nf(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Presensi DAK Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan DAK Non Fisik');
		foreach (range('B', 'M') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle("A4:M4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A5:M5")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A6:M6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:M1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'DAK Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');


		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');

		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', '%');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', '%');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', '%');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', '%');


		$tahun = $this->session->userdata("thn_anggaran");
		$provinsi = $this->pm->get('ref_provinsi')->result();
		$i = 7;
		$b=7;
		foreach ($provinsi as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'. $i, $row->NamaProvinsi);
				
			//FARMASI
			$N = $this->pm->get_where_double('pagu_seluruh_nf',$row->KodeProvinsi,'KodeProvinsi', $tahun ,"TAHUN_ANGGARAN")->result();
			$jml =0;
			foreach ($N as $row2) {
				if($row2->pagu_seluruh > 0){
					$jml++;
				}
			}
			$col = 'B';
			for($index =1 ; $index<=4 ; $index++){
				$this->excel->getActiveSheet()->setCellValue($col. $i, $jml);
				$col++;
				$k = $this->pm->get_where_triple('pengajuan_monev_nf',$row->KodeProvinsi,'KodeProvinsi', $index, 'WAKTU_LAPORAN', $tahun, "TAHUN_ANGGARAN")->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($jml > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, ($k/$jml) * 100);
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col ++;
			}

			$i++;
		}
		$col=$col--;
		$i--;
		$this->excel->getActiveSheet()->getStyle('A7:' .'M' . $i)->applyFromArray($styleArray);
		$filename='Presens_DAK_NONFISIK.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_absensi_indonesia2(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["kat"]))$kat=$_GET["kat"];
		
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan DAK');
		foreach (range('B', 'Y') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle("A4:Y4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A5:Y5")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A6:Y6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->setCellValue('N4', 'Dasar');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N4:Y4');


		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		//dasar						
		$this->excel->getActiveSheet()->setCellValue('N5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('N5:P5');			
		$this->excel->getActiveSheet()->setCellValue('Q5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('Q5:S5');				
		$this->excel->getActiveSheet()->setCellValue('T5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('T5:V5');				
		$this->excel->getActiveSheet()->setCellValue('W5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('W5:Y5');

		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', '%');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', '%');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', '%');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', '%');

		//dasar
		$this->excel->getActiveSheet()->setCellValue('N6', 'N');
		$this->excel->getActiveSheet()->setCellValue('O6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('P6', '%');		
		$this->excel->getActiveSheet()->setCellValue('Q6', 'N');
		$this->excel->getActiveSheet()->setCellValue('R6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('S6', '%');
		$this->excel->getActiveSheet()->setCellValue('T6', 'N');
		$this->excel->getActiveSheet()->setCellValue('U6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('V6', '%');	
		$this->excel->getActiveSheet()->setCellValue('W6', 'N');
		$this->excel->getActiveSheet()->setCellValue('X6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Y6', '%');
													
		$tahun = $this->session->userdata("thn_anggaran");
		$provinsi = $this->pm->get("ref_provinsi")->result();
		$i = 7;
		$b=7;
		foreach ($provinsi as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'. $i, $row->NamaProvinsi);
			$array = array(
				'KodeProvinsi' => $row->KodeProvinsi,
				'ID_SUBBIDANG' => 2,
				'ID_KATEGORI' => $kat,
				'TAHUN_ANGGARAN' => $tahun,
				'pagu_seluruh >' => '0'
			);	
			//FARMASI
			$N = $this->pm->get_where_array('pagu_seluruh', $array)->num_rows();
			$col = 'B';
			for($index =1 ; $index<=4 ; $index++){
				if($N > 0 ){
					$this->excel->getActiveSheet()->setCellValue($col. $i, $N);
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
				}
				$col++;
				$k = $this->pm->get_where_5('pengajuan_monev_dak',$row->KodeProvinsi,'KodeProvinsi',2,'ID_SUBBIDANG', $kat,'ID_KATEGORI', $index, 'WAKTU_LAPORAN', $tahun, "TAHUN_ANGGARAN")->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($N > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, $k/$N*100);
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col ++;
			}

			//DASAR
			$array = array(
				'KodeProvinsi' => $row->KodeProvinsi,
				'ID_SUBBIDANG' => 3,
				'ID_KATEGORI' => $kat,
				'TAHUN_ANGGARAN' => $tahun,
				'pagu_seluruh >' => '0'
			);	
			$N = $this->pm->get_where_array('pagu_seluruh', $array)->num_rows();
			for($index =1 ; $index<=4 ; $index++){
				if($N > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, $N);
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
				}
				$col++;
				$k = $this->pm->get_where_quadruple('pengajuan_monev_dak',$row->KodeProvinsi,'KodeProvinsi',3,'ID_SUBBIDANG', $kat,'ID_KATEGORI', $index, 'WAKTU_LAPORAN')->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($N > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, $k/$N*100);
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col++;
			}
			$i++;
		}
		$col=$col--;
		$i--;
		$this->excel->getActiveSheet()->getStyle('A7:' .'Y' . $i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_absensi2(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["kat"]))$kat=$_GET["kat"];
		
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan DAK');
		foreach (range('B', 'Y') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle("A4:Y4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A5:Y5")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A6:Y6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:Y1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Farmasi');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->setCellValue('N4', 'Dasar');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N4:Y4');


		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		//dasar						
		$this->excel->getActiveSheet()->setCellValue('N5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('N5:P5');			
		$this->excel->getActiveSheet()->setCellValue('Q5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('Q5:S5');				
		$this->excel->getActiveSheet()->setCellValue('T5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('T5:V5');				
		$this->excel->getActiveSheet()->setCellValue('W5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('W5:Y5');

		//farmasi
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', '%');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', '%');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', '%');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', '%');

		//dasar
		$this->excel->getActiveSheet()->setCellValue('N6', 'N');
		$this->excel->getActiveSheet()->setCellValue('O6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('P6', '%');		
		$this->excel->getActiveSheet()->setCellValue('Q6', 'N');
		$this->excel->getActiveSheet()->setCellValue('R6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('S6', '%');
		$this->excel->getActiveSheet()->setCellValue('T6', 'N');
		$this->excel->getActiveSheet()->setCellValue('U6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('V6', '%');	
		$this->excel->getActiveSheet()->setCellValue('W6', 'N');
		$this->excel->getActiveSheet()->setCellValue('X6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Y6', '%');
													
		$tahun = $this->session->userdata("thn_anggaran");
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		$i = 7;
		$b=7;
		$dk = 0;
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'. $i, $row->NamaKabupaten);		
			if($kat == 1){
				$dk = 2;
			}
			elseif($kat == 5){
				$dk = 22;
			}
			//FARMASI
			$N = $this->pm->get_where_5('pagu_seluruh',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',$dk,'ID_SUBBIDANG', $kat,'ID_KATEGORI', $tahun ,"TAHUN_ANGGARAN")->result();
			$col = 'B';
			for($index =1 ; $index<=4 ; $index++){
				if($N != null){
					if($N[0]->pagu_seluruh > 0){
						$this->excel->getActiveSheet()->setCellValue($col. $i, '1');
					}
					else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
					}
				}
				else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
				}
				$col++;
				$k = $this->pm->get_where_6('pengajuan_monev_dak',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',$dk,'ID_SUBBIDANG', $kat,'ID_KATEGORI', $index, 'WAKTU_LAPORAN', $tahun, "TAHUN_ANGGARAN")->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($k > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, '100%');
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col ++;
			}

			//DASAR
			if($kat == 1){
				$dk = 3;
			}
			elseif($kat == 4){
				$dk = 19 ;
			}
			elseif($kat == 5){
				$dk = 21;
			}
			$N = $this->pm->get_where_quadruple('pagu_seluruh',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',$dk ,'ID_SUBBIDANG', $kat,'ID_KATEGORI')->result();
			for($index =1 ; $index<=4 ; $index++){
				if($N != null){
					if($N[0]->pagu_seluruh > 0){
						$this->excel->getActiveSheet()->setCellValue($col. $i, '1');
					}
					else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
					}
				}
				else{
						$this->excel->getActiveSheet()->setCellValue($col. $i, '0');	
				}
				$col++;
				$k = $this->pm->get_where_5('pengajuan_monev_dak',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten',3,'ID_SUBBIDANG', $kat,'ID_KATEGORI', $index, 'WAKTU_LAPORAN')->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col. $i, $k);
				$colK = $col ++;
				if($k > 0){
					$this->excel->getActiveSheet()->setCellValue($col. $i, '100%');
				}
				else{
					$this->excel->getActiveSheet()->setCellValue($col. $i, '0%');	
				}
				$col++;
			}
			$i++;
		}
		$col=$col--;
		$i--;
		$this->excel->getActiveSheet()->getStyle('A7:' .'Y' . $i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}	
	
	function print_kelengkapan_rujukan(){
		$d=0;
		$k=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){		
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,1,$s)->result();
				$laporan_sarpras_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,5,$s)->result();
			}
		}
		$this->excel->setActiveSheetIndex(0);
		$style = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'alignment' => array(
            	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        	)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan Rujukan');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($styleHeader);	
		$this->excel->getActiveSheet()->getStyle('A5:J5')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B5');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:F4');
		$this->excel->getActiveSheet()->setCellValue('G4', 'Sarpras Rujukan');
		$this->excel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('G4:J4');
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('D5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 4');			
		$this->excel->getActiveSheet()->setCellValue('G5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('J5', 'Triwulan 4');		
					
		$i=6;
		$b=6;
		$d=$i;
		$tahun = $this->session->userdata("thn_anggaran");
		$kolom=$this->createColumnsArray('FF');	
		foreach($kabupaten as $index => $row){
			$rujukan=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 1, 2);
			$sarpras_rs=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 4, 2);				
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten);
    		$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi')->result();
			if(!empty($data_rs)){
				$rs_loop=0;			
		 		foreach($data_rs as $index2 => $rows){
		 			$this->excel->getActiveSheet()->setCellValue('B'.$i, $rows->NAMA_RS);
		 			$kl=2;
		 			for ($tw = 1; $tw <= 4; $tw++){	 	
		 				$id_lap[$tw]=$this->pm->get_where_5('dak_laporan',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi',$tw,'WAKTU_LAPORAN', $rows->KODE_RS,'KD_RS', 1,'JENIS_DAK'); 					 		 		
		 				if($id_lap[$tw]->num_rows()!=0){
		 					$id_lp[$tw]=1;
		 				}else{
		 					$id_lp[$tw]='0';
		 				}
		 		$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$id_lp[$tw]);
		 		$kl++;	
		 	} 
		 	for ($tw = 1; $tw <= 4; $tw++){	 	
		 		$id_lap[$tw]=$this->pm->get_where_5('dak_laporan',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi',$tw,'WAKTU_LAPORAN', $rows->KODE_RS,'KD_RS', 5,'JENIS_DAK'); 					 		 		
		 		if($id_lap[$tw]->num_rows()!=0){
		 			$id_lp[$tw]=1;
		 		}else{
		 			$id_lp[$tw]='0';
		 		}
		 		$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$id_lp[$tw]);
		 		$kl++;	
		 	}   
		 	    $klm=$kl-1;
		 		$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);		 	
		 		$i++;
		 		$rs_loop++;
		 		
		}
			if($rs_loop!=0)$i=$i-1;		
		   	$this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($style);
		   	$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);		
			}else{  
		   		$this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($style);
		   		$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);	
			}			 
			$this->excel->getActiveSheet()->mergeCells('A'.$b.':A'.$i);
	  		$i++;	
	  		$b=$i;
	  		$c=$i-1;	  					
		}

		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();


		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}

	function print_absensi_rujukan_indonesia2(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Rujukan');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		
		$d=$i;
		$tahun = $this->session->userdata("thn_anggaran");
		$provinsi = $this->pm->get("ref_provinsi")->result();
		foreach ($provinsi as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);	
			$col = 'C';
			$N = $this->pm->pagu_rs($row->KodeProvinsi,1)->num_rows();
				for($j = 1 ;$j<=4; $j++){
					$K = $this->pm->get_where_quadruple("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", 1, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $tahun, "TAHUN_ANGGARAN")->num_rows();
						if($N > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $N);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($K > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($N>0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K/$N * 100);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
						}
						$col++;
					}
					$i++;
		}
		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}		

	function print_absensi_afirmasi_indonesia2(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Presensi Afirmasi');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Afirmasi');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}


		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		
		$d=$i;
		$tahun = $this->session->userdata("thn_anggaran");
		$provinsi = $this->pm->get("ref_provinsi")->result();
		foreach ($provinsi as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);	
			$col = 'C';
			$N = $this->pm->pagu_puskes($row->KodeProvinsi)->num_rows();
				for($j = 1 ;$j<=4; $j++){
					$K = $this->pm->get_where_quadruple("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", 9, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $tahun, "TAHUN_ANGGARAN")->num_rows();
						if($N > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $N);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($K > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($N>0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K/$N * 100);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
						}
						$col++;
					}
					$i++;
		}
		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_absensi_penugasan_indonesia2(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Presensi Afirmasi');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Penugasan');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}


		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Provinsi');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		
		$d=$i;
		$tahun = $this->session->userdata("thn_anggaran");
		$provinsi = $this->pm->get("ref_provinsi")->result();
		foreach ($provinsi as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);	
			$col = 'C';
			$N = $this->pm->pagu_rs($row->KodeProvinsi, 8)->num_rows();
				for($j = 1 ;$j<=4; $j++){
					$K = $this->pm->get_where_quadruple("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", 8, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $tahun, "TAHUN_ANGGARAN")->num_rows();
						if($N > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $N);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($K > 0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($N>0){
							$this->excel->getActiveSheet()->setCellValue($col.$i, $K/$N * 100);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
						}
						$col++;
					}
					$i++;
		}
		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}
	function print_absensi_rujukan2(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';

		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Rujukan');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		
		$d=$i;
		$tahun = $this->session->userdata("thn_anggaran");
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$rs = $this->pm->get_where_double("data_rumah_sakit", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($rs != null){
				foreach ($rs as $row2) {
				$col = 'C';
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $row2->NAMA_RS);
				$N = $this->pm->sum_pagu_rs($row->KodeProvinsi, $row->KodeKabupaten,1,1, $row2->KODE_RS)->result();
				for($j = 1 ;$j<=4; $j++){
					$K = $this->pm->get_where_6("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 1, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $tahun, "TAHUN_ANGGARAN", $row2->KODE_RS, "KD_RS")->num_rows();
						if($N[0] != null){
							if($N[0]->pagu > 0){
								$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
							}
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($K != null){
							if($K== 1){
								$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
								$x =1;
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$x =0;
							}
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
							$x =0;
						}
						$col++;
						if($x==1){
							$this->excel->getActiveSheet()->setCellValue($col.$i, '100%');
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
						}
						$col++;
					}
					$i++;
					
				}
			}
			else{
				$i++;
			}

		}

		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}
	function print_absensi_penugasan2(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';

		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Penugasan');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		
		$d=$i;
		
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$rs = $this->pm->get_where_double("data_rumah_sakit", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($rs != null){
				foreach ($rs as $row2) {
				$col = 'C';
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $row2->NAMA_RS);
				$N = $this->pm->sum_pagu_rs($row->KodeProvinsi, $row->KodeKabupaten,8,3, $row2->KODE_RS)->result();
				for($j = 1 ;$j<=4; $j++){
					$K = $this->pm->get_where_5("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 8, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $row2->KODE_RS, "KD_RS")->num_rows();
						if($N[0] != null){
							if($N[0]->pagu > 0){
								$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
							}
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
						}
						$col++;
						if($K == 1){
								$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
								$x =1;
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
							$x =0;
						}
						$col++;
						if($x==1){
							$this->excel->getActiveSheet()->setCellValue($col.$i, '100%');
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
						}
						$col++;
					}
					$i++;
					
				}
			}
			else{
				$i++;
			}

		}

		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}
	function print_absensi_afirmasi2(){
		$num_rec_per_page=10;
		$d=0;
		$k='00';

		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){		
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,1,$s)->result();
				$laporan_sarpras_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,5,$s)->result();
			}
		}
		$this->excel->setActiveSheetIndex(0);
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => false, 'color' => array('rgb' => '000000')), 'alignment' => array('wrap'=>true));
		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi Laporan Afirmasi');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:N1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:N4')->applyFromArray($styleArrayHead);	
		$this->excel->getActiveSheet()->getStyle('A5:N5')->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle('A6:N6')->applyFromArray($styleArrayHead);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Puskesmas');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B6');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Afirmasi');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');

		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', '%');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', '%');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', '%');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', '%');
				
		$i=7;
		$tahun = $this->session->userdata("thn_anggaran");
		$d=$i;
		
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$puskes = $this->pm->get_where_double("data_puskesmas", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($puskes != null){
				foreach ($puskes as $row2) {
					$col = 'C';
					$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);	
					$this->excel->getActiveSheet()->setCellValue('B'.$i, $row2->NamaPuskesmas);
					$N = $this->pm->sum_pagu_rs($row->KodeProvinsi, $row->KodeKabupaten,9,2, $row2->KodePuskesmas)->result();
					for($j = 1 ;$j<=4; $j++){
						$K = $this->pm->get_where_5("pengajuan_monev_dak", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 9, "ID_SUBBIDANG", $j, "WAKTU_LAPORAN", $tahun, "TAHUN_ANGGARAN")->num_rows();
							if($N[0] != null){
								if($N[0]->pagu > 0){
									$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
								}
								else{
									$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								}
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
							}
							$col++;
							if($K > 0){
									$this->excel->getActiveSheet()->setCellValue($col.$i, '1');
									$x =1;
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0');
								$x =0;
							}
							$col++;
							if($x==1){
								$this->excel->getActiveSheet()->setCellValue($col.$i, '100%');
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($col.$i, '0%');
							}
							$col++;
						}
						$i++;
				}
			}else{
				$i++;
			}
		}

		$this->excel->getActiveSheet()->getStyle('B7:B'.$i)->applyFromArray($styleArray2);
		$this->excel->getActiveSheet()->getStyle('A7:N'.$i)->applyFromArray($styleArray);
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}
	function print_absensi_rujukan(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';

		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$laporan_sarpras=array();
		$laporan_sarpras_rjk=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result();
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $index => $row){
			for ($tw = 1; $tw <= 4; $tw++){		
				$laporan_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,1,$s)->result();
				$laporan_sarpras_rujukan[$tw][]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$tw,$row->KodeProvinsi,5,$s)->result();
			}
		}
		$this->excel->setActiveSheetIndex(0);
		$style = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'alignment' => array(
            	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        	)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Rujukan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan Rujukan');
		foreach (range('C', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->getStyle('A4:Z4')->applyFromArray($styleHeader);	
		$this->excel->getActiveSheet()->getStyle('A5:Z5')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A6:Z6')->applyFromArray($styleHeader);		
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:B5');	
		$this->excel->getActiveSheet()->setCellValue('C4', 'Rujukan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('C4:N4');
		$this->excel->getActiveSheet()->setCellValue('O4', 'Sarpras Rujukan');
		$this->excel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('O4:Z4');
		//Rujukan
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('C5:E5');		
		$this->excel->getActiveSheet()->setCellValue('F5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('F5:H5');			
		$this->excel->getActiveSheet()->setCellValue('I5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('I5:K5');					
		$this->excel->getActiveSheet()->setCellValue('L5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('L5:N5');
		//Sarpras						
		$this->excel->getActiveSheet()->setCellValue('O5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('O5:Q5');			
		$this->excel->getActiveSheet()->setCellValue('R5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('R5:T5');				
		$this->excel->getActiveSheet()->setCellValue('U5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('U5:W5');				
		$this->excel->getActiveSheet()->setCellValue('X5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('X5:Z5');

		//rujukan
		$this->excel->getActiveSheet()->setCellValue('C6', 'N');
		$this->excel->getActiveSheet()->setCellValue('D6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('E6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('F6', 'N');
		$this->excel->getActiveSheet()->setCellValue('G6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('H6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('I6', 'N');
		$this->excel->getActiveSheet()->setCellValue('J6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('K6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('L6', 'N');
		$this->excel->getActiveSheet()->setCellValue('M6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('N6', 'persentase');
		//sarpras
		$this->excel->getActiveSheet()->setCellValue('O6', 'N');
		$this->excel->getActiveSheet()->setCellValue('P6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Q6', 'persentase');		
		$this->excel->getActiveSheet()->setCellValue('R6', 'N');
		$this->excel->getActiveSheet()->setCellValue('S6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('T6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('U6', 'N');
		$this->excel->getActiveSheet()->setCellValue('V6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('W6', 'persentase');	
		$this->excel->getActiveSheet()->setCellValue('X6', 'N');
		$this->excel->getActiveSheet()->setCellValue('Y6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('Z6', 'persentase');


					
		$i=7;
		$b=7;
		$d=$i;
		$kolom=$this->createColumnsArray('FF');	
		foreach($kabupaten as $index => $row){
			$rujukan=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 1, 2);
			$sarpras_rs=$this->pendaftaran_edak->pagu($row->KodeProvinsi, $row->KodeKabupaten, 4, 2);				
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten);
    		$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi')->result();
			if(!empty($data_rs)){
				$rs_loop=0;			
		 		foreach($data_rs as $index2 => $rows){
		 			$this->excel->getActiveSheet()->setCellValue('B'.$i, $rows->NAMA_RS);
		 			$kl=2;
		 			for ($tw = 1; $tw <= 4; $tw++){	 	
		 				$id_lap[$tw]=$this->pm->get_where_5('dak_laporan',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi',$tw,'WAKTU_LAPORAN', $rows->KODE_RS,'KD_RS', 1,'JENIS_DAK'); 					 		 		
		 				if($id_lap[$tw]->num_rows()!=0){
		 					$id_lp[$tw]=1;
		 				}else{
		 					$id_lp[$tw]='0';
		 				}
				 		if($rujukan!='-')
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$rujukan);
				 		else
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,'0');
				 		$kl++;			
				 		$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$id_lp[$tw]);
				 		$kl++;
				 		if($rujukan!='-'){
				 			$rata=($id_lp[$tw]/$rujukan)*100;
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$rata.'%');
				 		}else{
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,'0%');
				 		}
				 		$kl++;			 			
				 	} 
				 	for ($tw = 1; $tw <= 4; $tw++){	 	
				 		$id_lap[$tw]=$this->pm->get_where_5('dak_laporan',$row->KodeKabupaten,'KodeKabupaten',$row->KodeProvinsi,'KodeProvinsi',$tw,'WAKTU_LAPORAN', $rows->KODE_RS,'KD_RS', 5,'JENIS_DAK'); 					 		 		
				 		if($id_lap[$tw]->num_rows()!=0){
				 			$id_lp[$tw]=1;
				 		}else{
				 			$id_lp[$tw]='0';
				 		}
				 		if($sarpras_rs!='-')
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$sarpras_rs);
				 		else
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,'0');
				 		$kl++;			
				 		$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$id_lp[$tw]);
				 		$kl++;
				 		if($sarpras_rs!='-'){
				 			$rata=($id_lp[$tw]/$sarpras_rs)*100;
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,$rata.'%');
				 		}else{
				 			$this->excel->getActiveSheet()->setCellValue($kolom[$kl].$i,'0%');
				 		}
				 		$kl++;	
				 }   
				$klm=$kl-1;
		 		$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);		 	
		 		$i++;
		 		$rs_loop++;
		 		
		}
			if($rs_loop!=0)$i=$i-1;		
		   	$this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($style);
		   	$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);		
			}else{  
		   		$this->excel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($style);
		   		$this->excel->getActiveSheet()->getStyle('B'.$i.':'.$kolom[$klm].$i)->applyFromArray($style);	
			}			 
			$this->excel->getActiveSheet()->mergeCells('A'.$b.':A'.$i);
	  		$i++;	
	  		$b=$i;
	  		$c=$i-1;	  				

		}
		$filename='rekap_kelengkapan.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}	

	function table_verifikasi(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { 
			$page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; $prevpage=1; $currentpage =1; $nextpage = 2;
		}
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$s=4;
		$t=1;
		$p=0;
		if(isset($_GET["k"]) )$k=$_GET["k"];
		if(isset($_GET["p"]) )$p=$_GET["p"];
		if(isset($_GET["s"]) )$s=$_GET["s"];
		if(isset($_GET["t"]) )$t=$_GET["t"];
		$data_rs=array();    
		$laporan_farmasi=array();
		$kabupaten=$laporan=$this->pm->dak_laporan2_limit($k,$t,$p,0,$s,$num_rec_per_page, $start_from)->result();
		$total_records =$this->pm->dak_laporan2($k,$t,$p,0,$s)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$sarpras_id='';
		$sarpras_tw='';
		$laporan=$this->pm->dak_laporan2_limit($k,$t,$p,0,$s,$num_rec_per_page, $start_from)->result();
	
		$data['k']=$k;
		$data['laporan']=$laporan;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_verifikasi',$data);
	}


	function table_verifikasi2(){
		if(isset($_POST["jenis_dak"]) ){
	    	$jenis_dak=$this->input->post('jenis_dak');
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$kdsatker=$this->input->post('kdsatker');
	      	$kategori=$this->input->post('kategori');
	      	$waktu=$this->input->post('waktu');
	      	$kdrs=$this->input->post('kdrs');
	      	$tahun = $this->session->userdata('thn_anggaran');

      	}
    	
    	$data['kdrs'] = $kdrs;
    	// print_r($kdrs); exit;
      	$data['jenis_dak']=$jenis_dak;	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;
      	$data['kdsatker']=$kdsatker;	
      	$data['kategori']=$kategori;
      	$data['waktu']=$waktu;
      	$data['tahun']=$tahun;

		$this->load->view('e-monev/table_verifikasi2',$data);
	}
	function table_verifikasi2_nf(){
		if(isset($_POST["provinsi"]) ){
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$waktu=$this->input->post('waktu');
	      	$tahun = $this->session->userdata('thn_anggaran');
      	}
    	
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;
      	$data['waktu']=$waktu;
      	$data['tahun']=$tahun;

		$this->load->view('e-monev/table_verifikasi2_nf',$data);
	}

	function table_verifikasi_nf2(){
		if(isset($_POST["provinsi"]) ){
	      	$KodeProvinsi=$this->input->post('provinsi');		
	      	$KodeKabupaten=$this->input->post('KodeKabupaten');
	      	$waktu=$this->input->post('waktu');
	      	$tahun = $this->session->userdata('thn_anggaran');
	      	$jenis = $this->input->post('jenis_dak');
      	}
      	$data['KodeProvinsi']=$KodeProvinsi;
      	$data['KodeKabupaten']=$KodeKabupaten;
      	$data['waktu']=$waktu;
      	$data['tahun']=$tahun;
      	$data['jenis_dak'] = $jenis;
		$this->load->view('e-monev/table_verifikasi_nf2',$data);
	}

	function table_verifikasi_nf(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { 
			$page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; 
			$prevpage=1; 
			$currentpage =1; 
			$nextpage = 2;
		};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$s=4;
		$t=1;
		$p=0;
		if(isset($_GET["k"]) )$k=$_GET["k"];
		if(isset($_GET["p"]) )$p=$_GET["p"];
		if(isset($_GET["s"]) )$s=$_GET["s"];
		if(isset($_GET["t"]) )$t=$_GET["t"];
		$data_rs=array();    
		$laporan_farmasi=array();
		$kabupaten=$laporan=$this->pm->dak_laporan_nf2_limit($k,$t,$p,$s,$num_rec_per_page, $start_from)->result();
		$total_records =$this->pm->dak_laporan_nf_2($k,$t,$p,$s)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$sarpras_id='';
		$sarpras_tw='';
		$laporan=$this->pm->dak_laporan_nf2_limit($k,$t,$p,$s,$num_rec_per_page, $start_from)->result();
	
		$data['k']=$k;
		$data['laporan']=$laporan;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_verifikasi_nf',$data);
	}

	function table_pdf(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) 
		{ 
			$page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; 
			$prevpage=1; 
			$currentpage =1; 
			$nextpage = 2;
		};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		if($_GET["p"] ){
			$k=$_GET["k"];
			$p=$_GET["p"];
		}
		$data_rs=array();
		$laporan_farmasi=array();
		$laporan_rujukan=array();
		$laporan_dasar=array();
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$sarpras_id='';
		$sarpras_tw='';
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result() as $index => $row){
			$laporan_farmasi[]=$this->pm->dak_laporan($row->KodeKabupaten,$t,$row->KodeProvinsi,2)->result();
			$laporan_rujukan[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,1,4)->result();
			$laporan_rujukan_pr[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,6,4)->result();		
			$laporan_dasar[]=$this->pm->dak_laporan($row->KodeKabupaten,$t,$row->KodeProvinsi,3)->result();
			$laporan_sarpras[]=$this->pm->dak_laporan($row->KodeKabupaten,$t,$row->KodeProvinsi,4)->result();
			$laporan_sarpras_rjk[]=$this->pm->dak_laporan_rujukan($row->KodeKabupaten,$t,$row->KodeProvinsi,5,4)->result();
		}
	
		$data['k']=$k;
		$data['laporan_farmasi']=$laporan_farmasi;
		$data['laporan_rujukan']=$laporan_rujukan;
		$data['laporan_rujukan_pr']=$laporan_rujukan_pr;	
		$data['laporan_dasar']=$laporan_dasar;
		$data['laporan_sarpras']=$laporan_sarpras;
		$data['laporan_sarpras_rjk']=$laporan_sarpras_rjk;
		$data['data_rs']=$laporan_dasar;
		$data['kabupaten']=$kabupaten;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_pdf',$data);
	}

	function table_pdf_nf(){
		$laporan_nf=array();
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$laporan_bok=array();
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result() as $row){
			$laporan_nf[]=$this->pm->dak_laporan_nf2($row->KodeKabupaten,$t,$row->KodeProvinsi,$s)->result();
		}
		$rs='';

		$data['rs']=$rs;	
		$data['k']=$k;
		$data['laporan_nf']=$laporan_nf;
		$data['kabupaten']=$kabupaten;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_pdf_nf',$data);
	}



	function table_kelengkapan_nf(){
		$laporan_nf=array();
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$laporan_bok=array();
		$rs=array();
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result() as $row){
			$laporan_nf[]=$this->pm->dak_laporan_nf2($row->KodeKabupaten,$t,$row->KodeProvinsi,$s)->result();
			/*if($this->pm->dak_laporan_nf2($row->KodeKabupaten,$t,$row->KodeProvinsi,$s)->row()->KD_RS != 0){
				$kd=$this->pm->dak_laporan_nf2($row->KodeKabupaten,$t,$row->KodeProvinsi,$s)->row()->KD_RS;
				$rs[]=' :  '.$this->pm->get_where('data_rumah_sakit',$kd,'KODE_RS')->row()->NAMA_RS;
			}else{
				$rs[]='';
			}*/

		}
	
		$data['rs']=$rs;	
		$data['k']=$k;
		$data['laporan_nf']=$laporan_nf;
		$data['kabupaten']=$kabupaten;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_kelengkapan_nf',$data);
	}

	function print_kelengkapan_nf(){
		$laporan_nf=array();
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$laporan_bok=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result(); //count number of records

		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $row){
			for ($tw = 1; $tw <= 4; $tw++){	
				$laporan_nf[$tw][]=$this->pm->dak_laporan_nf2($row->KodeKabupaten,$tw,$row->KodeProvinsi,$s)->num_rows();
			}
		}

		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengakapan Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan Non Fisik');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:E4');
		$this->excel->getActiveSheet()->setCellValue('Bs5', 'Triwulan 1');
		$this->excel->getActiveSheet()->setCellValue('C5', 'Triwulan 2');	
		$this->excel->getActiveSheet()->setCellValue('D5', 'Triwulan 3');	
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 4');			
						
		$i=6;
		$b=6;	
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);	
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $laporan_nf[1][$index]);
			$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_nf[2][$index]);
			$this->excel->getActiveSheet()->setCellValue('D'.$i, $laporan_nf[3][$index]);	
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $laporan_nf[4][$index]);
			$i++;		
		}
		$filename='rekap_kelengkapan_nf.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}	

	function print_absensi_nf(){
		$laporan_nf=array();
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$laporan_bok=array();
		$kabupaten=$this->pm->get_kabupaten_detail2($p,$k)->result(); //count number of records

		foreach($this->pm->get_kabupaten_detail2($p,$k)->result() as $row){
		$pagu[]=$this->pm->get_where_double('data_pagu_nf',$row->KodeProvinsi,'KodeProvinsi',$row->KodeKabupaten,'KodeKabupaten')->num_rows();	
			for ($tw = 1; $tw <= 4; $tw++){	
				$laporan_nf[$tw][]=$this->pm->dak_laporan_nf2($row->KodeKabupaten,$tw,$row->KodeProvinsi,$s)->num_rows();
			}
		}
    	$style = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
       	 )
   		);
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengakapan Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan Non Fisik');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:E4');
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');	
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', 'persentase');		
						
		$i=7;
		$b=7;	
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);				
			if($pagu[$index]!=0){
			    $persentase[1]=($laporan_nf[1][$index]/1)*100;
	 		    $persentase[2]=($laporan_nf[2][$index]/1)*100;	
	 		    $persentase[3]=($laporan_nf[3][$index]/1)*100;	
	 		    $persentase[4]=($laporan_nf[4][$index]/1)*100;		
				$this->excel->getActiveSheet()->setCellValue('B'.$i, '1');
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_nf[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, $persentase[1]);			
				$this->excel->getActiveSheet()->setCellValue('E'.$i, '1');			
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_nf[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $persentase[2]);				
				$this->excel->getActiveSheet()->setCellValue('H'.$i, '1');						
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_nf[3][$index]);
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $persentase[3]);	
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '1');						
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_nf[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('M'.$i, $persentase[3]);				
			}else{
				$this->excel->getActiveSheet()->setCellValue('B'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('C'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('D'.$i, '0');			
				$this->excel->getActiveSheet()->setCellValue('E'.$i, '0');			
				$this->excel->getActiveSheet()->setCellValue('F'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('G'.$i, '0');				
				$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');						
				$this->excel->getActiveSheet()->setCellValue('I'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');						
				$this->excel->getActiveSheet()->setCellValue('L'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');						
			}
			$this->excel->getActiveSheet()->getStyle('B'.$i.':M'.$i)->applyFromArray($style);
			$i++;			
		
		}

		$filename='rekap_kelengkapan_nf.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}	

	function print_absensi_indonesia_nf(){
		$laporan_nf=array();
		$d=0;
		$k='00';
		$t=0;
		$p=0;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$laporan_bok=array();
		$kabupaten=$this->pm->get_provinsi()->result(); //count number of records

		foreach($this->pm->get_provinsi()->result() as $row){
			$pagu[]=$this->pm->get_where('data_pagu_nf',$row->KodeProvinsi,'KodeProvinsi')->num_rows();
			for ($tw = 1; $tw <= 4; $tw++){	
				$laporan_nf[$tw][]=$this->pm->dak_laporan_nf_2(0,$tw,$row->KodeProvinsi,$s)->num_rows();
			}
		}
    	$style = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
       	 )
   		);
    	$style_cell = array(
      			 'alignment' => array(
      		      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
       	 )
   		);   		
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengakapan Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Kelengkapan Non Fisik');
		foreach (range('A', 'Q') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A5');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:E4');
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');
		$this->excel->getActiveSheet()->getStyle("B4:M4")->applyFromArray($style);	
		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');	
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');
		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', 'persentase');
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', 'persentase');		
						
		$i=7;
		$b=7;	
		foreach($kabupaten as $index => $row){
		    $persentase[1]=($laporan_nf[1][$index]/$pagu[$index])*100;
 		    $persentase[2]=($laporan_nf[2][$index]/$pagu[$index])*100;	
 		    $persentase[3]=($laporan_nf[3][$index]/$pagu[$index])*100;	
 		    $persentase[4]=($laporan_nf[4][$index]/$pagu[$index])*100;	
 		    $this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);
			if($pagu>0){
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $pagu[$index]);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_nf[1][$index]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, round($persentase[1],2));			
				$this->excel->getActiveSheet()->setCellValue('E'.$i, $pagu[$index]);			
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_nf[2][$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, round($persentase[2],2));				
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $pagu[$index]);						
				$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_nf[3][$index]);
	 			$this->excel->getActiveSheet()->setCellValue('J'.$i, round($persentase[3],2));	
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $pagu[$index]);						
				$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_nf[4][$index]);
				$this->excel->getActiveSheet()->setCellValue('M'.$i, round($persentase[4],2));	
			}else{	
				$this->excel->getActiveSheet()->setCellValue('B'.$i,'0');
				$this->excel->getActiveSheet()->setCellValue('C'.$i,'0');
				$this->excel->getActiveSheet()->setCellValue('D'.$i,'0');			
				$this->excel->getActiveSheet()->setCellValue('E'.$i,'0');			
				$this->excel->getActiveSheet()->setCellValue('F'.$i,'0');
				$this->excel->getActiveSheet()->setCellValue('G'.$i,'0');				
				$this->excel->getActiveSheet()->setCellValue('H'.$i,'0');						
				$this->excel->getActiveSheet()->setCellValue('I'.$i,'0');
				$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');	
				$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');						
				$this->excel->getActiveSheet()->setCellValue('L'.$i,'0');
				$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');
			}
			$this->excel->getActiveSheet()->getStyle("B".$i.":M".$i)->applyFromArray($style_cell);
			$i++;			
		}
		$persentase[1]=(array_sum($laporan_nf[1])/array_sum($pagu))*100;
	    $persentase[2]=(array_sum($laporan_nf[2])/array_sum($pagu))*100;
	    $persentase[3]=(array_sum($laporan_nf[3])/array_sum($pagu))*100;
	    $persentase[4]=(array_sum($laporan_nf[4])/array_sum($pagu))*100;
		$this->excel->getActiveSheet()->setCellValue('A'.$i, 'TOTAL');	
		$this->excel->getActiveSheet()->setCellValue('B'.$i, array_sum($pagu));
		$this->excel->getActiveSheet()->setCellValue('C'.$i, $laporan_nf[1][$index]);
		$this->excel->getActiveSheet()->setCellValue('D'.$i,  round($persentase[1],2));			
		$this->excel->getActiveSheet()->setCellValue('E'.$i,array_sum($pagu));			
		$this->excel->getActiveSheet()->setCellValue('F'.$i, $laporan_nf[2][$index]);
		$this->excel->getActiveSheet()->setCellValue('G'.$i,  round($persentase[2],2));				
		$this->excel->getActiveSheet()->setCellValue('H'.$i,array_sum($pagu));						
		$this->excel->getActiveSheet()->setCellValue('I'.$i, $laporan_nf[3][$index]);
		$this->excel->getActiveSheet()->setCellValue('J'.$i,  round($persentase[3],2));	
		$this->excel->getActiveSheet()->setCellValue('K'.$i,array_sum($pagu));						
		$this->excel->getActiveSheet()->setCellValue('L'.$i, $laporan_nf[4][$index]);
		$this->excel->getActiveSheet()->setCellValue('M'.$i,  round($persentase[4],2));	
		$this->excel->getActiveSheet()->getStyle("B".$i.":M".$i)->applyFromArray($style_cell);		
		$filename='rekap_kelengkapan_nf.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}	


	function table_pagu(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; 
			$prevpage=1; 
			$currentpage =1; 
			$nextpage = 2;
		}
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$status=4;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$status=$_GET["s"];
		
		$kabupaten=$k;
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_data_kabupaten_limit($p,$num_rec_per_page, $start_from,$k)->result()  as $index => $rowz){
			$l='';
			$l_rujukan='';
			$l_rujukan_total='';
			$l_total='';
			$l_farmasi='';
			$l_farmasi_total='';
			$l_dasar='';
			$l_dasar_total='';
			$l_sarpras='';
			$l_sarpras_total='';
			$l_sarpras_rjk='';
			$l_sarpras_rjk_total='';			
			$l_tambahan='';
			$l_tambahan_total='';			
			$l_total='';
			$l_total_total='';			
			$laporan_rujukan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_rujukan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,1,$status);
			$laporan_farmasi=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_farmasi_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,2,$status);
			$laporan_dasar=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_dasar_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,3,$status);
			$laporan_sarpras=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,4,$status);
			$laporan_tambahan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,7,$status);
			$laporan_tambahan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,7,$status);		
			$laporan_sarpras_rjk=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,5,$status);
			$laporan_sarpras_rjk_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,5,$status);
			$laporan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,0,$status);
			if($laporan_rujukan->num_rows !=0 ){
				foreach($laporan_rujukan->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_total->num_rows !=0 ){
				foreach($laporan_rujukan_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi->num_rows !=0 ){
				foreach($laporan_farmasi->result() as $row2){
					$l_farmasi.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi_total->num_rows !=0 ){
				foreach($laporan_farmasi_total->result() as $row2){
					$l_farmasi_total.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar->num_rows !=0 ){
				foreach($laporan_dasar->result() as $row3){
					$l_dasar.=$row3->ID_LAPORAN_DAK.',';
				}
			}

			if($laporan_dasar_total->num_rows !=0 ){
				foreach($laporan_dasar_total->result() as $row3){
					$l_dasar_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras->num_rows !=0 ){
				foreach($laporan_sarpras->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_total->num_rows !=0 ){
				foreach($laporan_sarpras_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan->num_rows !=0 ){
				foreach($laporan_tambahan->result() as $row3){
					$l_tambahan.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan_total->num_rows !=0 ){
				foreach($laporan_tambahan_total->result() as $row3){
					$l_tambahan_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}			
			if($laporan_sarpras_rjk->num_rows !=0 ){
				foreach($laporan_sarpras_rjk->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk_total->num_rows !=0 ){
				foreach($laporan_sarpras_rjk_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_total->num_rows !=0 ){
				foreach($laporan_total->result() as $row3){
					$l_total.=$row3->ID_LAPORAN_DAK.',';}
			}
			$l_rujukan=substr_replace($l_rujukan, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan)->num_rows !=0 ){
				$realisasi_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan;
				$perencanaan_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan2;
				$perencanaan_rujukan[$index]=$this->pm->sum_kabupaten($l_rujukan)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_rujukan)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$jml_rs=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,1,$status)->num_rows();	
				if($jml_rs!=0){
					$fisik_rujukan[]=$fisik/($jml_rs+1);
				}else{
					$fisik_rujukan[]=$fisik;
				}
			}
			else {$realisasi_daerah_rujukan[]=0;
				$fisik_rujukan[]='0%';
			}
			$l_rujukan_total=substr_replace($l_rujukan_total, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan_total)->num_rows !=0 ){
				$realisasi_daerah_rujukan_total[]=$this->pm->sum_kabupaten($l_rujukan_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_rujukan_total[]=0;
			}
			$l_farmasi=substr_replace($l_farmasi, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi)->num_rows !=0 ){
				$realisasi_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan;
				$perencanaan_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan2;
				$perencanaan_farmasi[$index]=$this->pm->sum_kabupaten($l_farmasi)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_farmasi)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_farmasi[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_farmasi[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_farmasi[]=$fisik;
			}
			else {$realisasi_daerah_farmasi[]='0';
				$fisik_farmasi[]='0%';
			}
			$l_farmasi_total=substr_replace($l_farmasi_total, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi_total)->num_rows !=0 ){
				$realisasi_daerah_farmasi_total[]=$this->pm->sum_kabupaten($l_farmasi_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_farmasi_total[]=0;
			}
			$l_dasar=substr_replace($l_dasar, '', -1);
			if($this->pm->sum_kabupaten($l_dasar)->num_rows !=0 ){
				$realisasi_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan;
				$perencanaan_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan2;
				$perencanaan_dasar[$index]=$this->pm->sum_kabupaten($l_dasar)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_dasar)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_dasar[]=$fisik;
			}
			else {
				$realisasi_daerah_dasar[]=0;
				$fisik_dasar[]='0%';
			}
			$l_dasar_total=substr_replace($l_dasar_total, '', -1);
			if($this->pm->sum_kabupaten($l_dasar_total)->num_rows !=0 ){
					$realisasi_daerah_dasar_total[]=$this->pm->sum_kabupaten($l_dasar_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_dasar_total[]=0;
			}
			$l_sarpras=substr_replace($l_sarpras, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras)->num_rows !=0 ){
				$realisasi_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan;
				$perencanaan_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan2;
				$perencanaan_sarpras[$index]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan_sarpras($l_sarpras)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_sarpras[$index] !=0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_sarpras[$index];
					}else{
						$bobot=0;
					}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
					$fisik_sarpras[]=$fisik;
			}else {
				$realisasi_daerah_sarpras[]=0;
				$fisik_sarpras[]='0%';
			}
				$l_sarpras_total=substr_replace($l_sarpras_total, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_sarpras_total)->num_rows !=0 ){
					$realisasi_daerah_sarpras_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
				}else {
					$realisasi_daerah_sarpras_total[]=0;
				}
				$l_tambahan=substr_replace($l_tambahan, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_tambahan)->num_rows !=0 ){
					$realisasi_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan;
					$perencanaan_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan2;
					$fisik=0;
					foreach($this->pm->dak_kegiatan_tambahan($l_tambahan)->result() as $rw){
						if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_tambahan[$index] !=0){
							$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
						}else{
							$bobot=0;
						}
						$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}
					$fisik_tambahan[]=$fisik;
				}
				else {$realisasi_daerah_tambahan[]=0;
					$fisik_tambahan[]='0%';
				}
				$l_tambahan_total=substr_replace($l_tambahan_total, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_tambahan_total)->num_rows !=0 ){
					$realisasi_daerah_tambahan_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
				}
				else {
					$realisasi_daerah_tambahan_total[]=0;
				}				
				$l_total=substr_replace($l_total, '', -1);
				if($this->pm->sum_kabupaten_total($l_total)->num_rows !=0 ){
					$realisasi_daerah_total[]=$this->pm->sum_kabupaten_total($l_total)->row()->pelaksanaan;
					if( $realisasi_daerah_farmasi[$index]!=0){
						$bobot_farmasi=($realisasi_daerah_farmasi[$index]/$realisasi_daerah_total[$index]);
						$f_farmasi=$fisik_farmasi[$index]*$bobot_farmasi;
					}else {
						$f_farmasi=0;
					}
					if( $realisasi_daerah_dasar[$index]!=0){
						$bobot_dasar=($realisasi_daerah_dasar[$index]/$realisasi_daerah_total[$index]);
						$f_dasar=$fisik_dasar[$index]*$bobot_dasar;
					}else{
						$f_dasar=0;
					}
					if( $realisasi_daerah_rujukan[$index]!=0){
						$bobot_rujukan=($realisasi_daerah_rujukan[$index]/$realisasi_daerah_total[$index]);
						$f_rujukan=$fisik_rujukan[$index]*$bobot_rujukan;
					}else{
						$f_rujukan=0;
					}
					if( $realisasi_daerah_sarpras[$index]!=0){
						$bobot_sarpras=($realisasi_daerah_sarpras[$index]/$realisasi_daerah_total[$index]);
						$f_sarpras=$fisik_sarpras[$index]*$bobot_sarpras;
					}
					else{
						$f_sarpras=0;
					}
					if( $realisasi_daerah_tambahan[$index]!=0){
						$bobot_tambahan=($realisasi_daerah_tambahan[$index]/$realisasi_daerah_tambahan[$index]);
						$f_tambahan=$fisik_tambahan[$index]*$bobot_tambahan;
					}
					else{
						$f_tambahan=0;
					}							
					$f_total[]=($f_farmasi+$f_dasar+$f_rujukan+$f_sarpras+$f_tambahan)/5;
				}
				else {
					$realisasi_daerah_total[]=0;
					$f_total[]=0;
				}
				if($this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
					$pagu=$this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
					$realisasi_pagu_rujukan[]=$pagu->row()->Rujukan;
					$realisasi_pagu_farmasi[]=$pagu->row()->Farmasi;
					$realisasi_pagu_dasar[]=$pagu->row()->Pelayanan_Dasar;
					$realisasi_pagu_sarpras[]=$pagu->row()->Sarpras;
					$realisasi_pagu_tambahan[]=$pagu->row()->Tambahan_Dak_Kesehatan;				
					$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
				} else {
					$realisasi_pagu_rujukan[] = "0";
					$realisasi_pagu_farmasi[] = "0";
					$realisasi_pagu_sarpras[] = "0";
					$realisasi_pagu_tambahan[] = "0";				
					$realisasi_pagu_dasar[] = "0";
					$realisasi_pagu_total[] = "0";
				}
	//the following var_dump is only showing the last record.
	//need to show all rows (which should be 2)
	//var_dump($data); exit;
		}
		$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_pagu?p='.$p.'&t='.$t.'&k='.$k.'&status='.$status.'" >
		<img src="'.base_url().'images/main/excel.png" > Print excel</a>';
		$total= $this->total_table_pagu($p,$k,$t,$status);
      
		$data['k']=$k;
		$data['kabupaten']=$kabupaten;
		$data['button']=$button;
		$data['nama_daerah']=$nama;
		$data['total_pages']=$total_pages;
		$data['realisasi_daerah_farmasi']=$realisasi_daerah_farmasi;
		$data['realisasi_daerah_tambahan']=$realisasi_daerah_tambahan;		
		$data['fisik_farmasi']=$fisik_farmasi;
		$data['f_total']=$f_total;
		$data['perencanaan_daerah_farmasi']=$perencanaan_daerah_farmasi;
		$data['realisasi_daerah_farmasi_total']=$realisasi_daerah_farmasi_total;
		$data['realisasi_daerah_dasar']=$realisasi_daerah_dasar;
		$data['fisik_dasar']=$fisik_dasar;
		$data['perencanaan_daerah_dasar']=$perencanaan_daerah_dasar;
		$data['realisasi_daerah_dasar_total']=$realisasi_daerah_dasar_total;
		$data['realisasi_daerah_rujukan']=$realisasi_daerah_rujukan;
		$data['fisik_rujukan']=$fisik_rujukan;
		$data['realisasi_daerah_rujukan_total']=$realisasi_daerah_rujukan_total;
		$data['perencanaan_daerah_rujukan']=$perencanaan_daerah_rujukan;
		$data['perencanaan_daerah_tambahan']=$perencanaan_daerah_tambahan;		
		$data['realisasi_daerah_total']=$realisasi_daerah_total;
		$data['realisasi_pagu_dasar']=$realisasi_pagu_dasar;
		$data['realisasi_pagu_rujukan']=$realisasi_pagu_rujukan;
		$data['realisasi_pagu_farmasi']=$realisasi_pagu_farmasi;
		$data['realisasi_pagu_sarpras']=$realisasi_pagu_sarpras;
		$data['realisasi_pagu_tambahan']=$realisasi_pagu_tambahan;		
		$data['realisasi_pagu_total']=$realisasi_pagu_total;
		$data['total_pagu_dasar']=$total['pagu_dasar'];
		$data['total_pagu_rujukan']=$total['pagu_rujukan'];
		$data['total_pagu_farmasi']=$total['pagu_farmasi'];
		$data['total_pagu_sarpras']=$total['pagu_sarpras'];
		$data['total_pagu_total']=$total['pagu_total'];
		$data['total_daerah_dasar']=$total['daerah_dasar'];
		$data['total_daerah_rujukan']=$total['daerah_rujukan'];
		$data['total_daerah_tambahan']=$total['daerah_rujukan'];		
		$data['total_daerah_farmasi']=$total['daerah_farmasi'];
		$data['total_daerah_sarpras']=$total['daerah_sarpras'];
		$data['total_daerah_tambahan']=$total['daerah_tambahan'];		
		$data['total_fisik_farmasi']=$total['fisik_farmasi'];	
		$data['total_fisik_rujukan']=$total['fisik_rujukan'];
		$data['total_fisik_dasar']=$total['fisik_dasar'];
		$data['total_fisik_sarpras']=$total['fisik_sarpras'];	
		$data['total_fisik_tambahan']=$total['fisik_tambahan'];		
		$data['total_fisik_total']=$total['fisik_total'];										
		$data['total_daerah_total']=$total['daerah_total'];		
		$data['realisasi_daerah_sarpras']=$realisasi_daerah_sarpras;
		$data['fisik_sarpras']=$fisik_sarpras;
		$data['perencanaan_daerah_sarpras']=$perencanaan_daerah_sarpras;
		$data['realisasi_daerah_sarpras_total']=$realisasi_daerah_sarpras_total;
		$data['t']=$t;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;

		$this->load->view('tabel_pagu',$data);
	}


	function total_table_pagu($p,$k,$t,$status){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { 
			$page  = $_GET["page"];
			$currentpage = (int) $_GET['page'];
			$prevpage = $currentpage - 1;
			$nextpage = $currentpage + 1;
		} else { 
			$page=1; 
			$prevpage=1; 
			$currentpage =1; 
			$nextpage = 2;
		}
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$provinsi=0;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		$kabupaten=$k;
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten($p)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$l_farmasi_t='';
		$l_dasar_t='';
		$l_rujukan_t='';
		$l_sarpras_t='';
		$l_tambahan_t='';
		foreach($this->pm->get_data_kabupaten($p)->result()  as $index => $rowz){
			$l='';
			$l_rujukan='';
			$l_rujukan_total='';
			$l_total='';
			$l_farmasi='';
			$l_farmasi_total='';
			$l_dasar='';
			$l_dasar_total='';
			$l_sarpras='';
			$l_sarpras_total='';
			$l_tambahan='';
			$l_tambahan_total='';
			$laporan_rujukan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,1,$status);		
			$laporan_rujukan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,1,$status);
			$laporan_rujukan_pr=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,6,$status);	
			$laporan_rujukan_pr_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,6,$status);		
			$laporan_farmasi=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_farmasi_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,2,$status);
			$laporan_dasar=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_dasar_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,3,$status);
			$laporan_sarpras=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_rjk=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,5,$status);
			$laporan_sarpras_rjk_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,5,$status);
			$laporan_tambahan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,7,$status);
			$laporan_tambahan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,7,$status);			
			$laporan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,0,$status);
			if($laporan_rujukan->num_rows !=0 ){
				foreach($laporan_rujukan->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
					$l_rujukan_t.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_total->num_rows !=0 ){
				foreach($laporan_rujukan_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_pr->num_rows !=0 ){
				foreach($laporan_rujukan_pr->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
					$l_rujukan_t.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_pr_total->num_rows !=0 ){
				foreach($laporan_rujukan_pr_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}			
			if($laporan_farmasi->num_rows !=0 ){
				foreach($laporan_farmasi->result() as $row2){
					$l_farmasi.=$row2->ID_LAPORAN_DAK.',';
					$l_farmasi_t.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi_total->num_rows !=0 ){
				foreach($laporan_farmasi_total->result() as $row2){
					$l_farmasi_total.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar->num_rows !=0 ){
				foreach($laporan_dasar->result() as $row3){
					$l_dasar.=$row3->ID_LAPORAN_DAK.',';
					$l_dasar_t.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar_total->num_rows !=0 ){
				foreach($laporan_dasar_total->result() as $row3){
					$l_dasar_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras->num_rows !=0 ){
				foreach($laporan_sarpras->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
					$l_sarpras_t.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_total->num_rows !=0 ){
				foreach($laporan_sarpras_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan->num_rows !=0 ){
				foreach($laporan_tambahan->result() as $row3){
					$l_tambahan.=$row3->ID_LAPORAN_DAK.',';
					$l_tambahan_t.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan_total->num_rows !=0 ){
				foreach($laporan_tambahan_total->result() as $row3){
					$l_tambahan_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}			
			if($laporan_sarpras_rjk->num_rows !=0 ){
				foreach($laporan_sarpras_rjk->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk_total->num_rows !=0 ){
				foreach($laporan_sarpras_rjk_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_total->num_rows !=0 ){
				foreach($laporan_total->result() as $row3){
					$l_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			$l_rujukan=substr_replace($l_rujukan, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan)->num_rows !=0 ){
				$realisasi_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan;
				$perencanaan_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan2;
				$perencanaan_rujukan[$index]=$this->pm->sum_kabupaten($l_rujukan)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_rujukan)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
					}else{
					$bobot=0;
					}	
				$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_rujukan[]=$fisik;
			}
			else {
				$realisasi_daerah_rujukan[]=0;
				$fisik_rujukan[]='0%';
			}
			$l_rujukan_total=substr_replace($l_rujukan_total, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan_total)->num_rows !=0 ){
				$realisasi_daerah_rujukan_total[]=$this->pm->sum_kabupaten($l_rujukan_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_rujukan_total[]=0;
			}
			$l_farmasi=substr_replace($l_farmasi, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi)->num_rows !=0 ){
				$realisasi_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan;
				$perencanaan_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan2;
				$perencanaan_farmasi[$index]=$this->pm->sum_kabupaten($l_farmasi)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_farmasi)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_farmasi[$index] !=0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_farmasi[$index];
					}else{
					$bobot=0;
					}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_farmasi[]=$fisik;
			}
			else {
				$realisasi_daerah_farmasi[]='0';
				$fisik_farmasi[]='0%';
			}
			$l_farmasi_total=substr_replace($l_farmasi_total, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi_total)->num_rows !=0 ){
				$realisasi_daerah_farmasi_total[]=$this->pm->sum_kabupaten($l_farmasi_total)->row()->pelaksanaan;
			}else {
				$realisasi_daerah_farmasi_total[]=0;
			}
				$l_dasar=substr_replace($l_dasar, '', -1);
				if($this->pm->sum_kabupaten($l_dasar)->num_rows !=0 ){
					$realisasi_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan;
					$perencanaan_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan2;
					$perencanaan_dasar[$index]=$this->pm->sum_kabupaten($l_dasar)->row()->perencanaan;
					$fisik=0;
					foreach($this->pm->dak_kegiatan2($l_dasar)->result() as $rw){
						if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
						}else{
						$bobot=0;
						}
						$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}
					$fisik_dasar[]=$fisik;
				}
				else {
					$realisasi_daerah_dasar[]=0;
					$fisik_dasar[]='0%';
				}
				$l_dasar_total=substr_replace($l_dasar_total, '', -1);
				if($this->pm->sum_kabupaten($l_dasar_total)->num_rows !=0 ){
					$realisasi_daerah_dasar_total[]=$this->pm->sum_kabupaten($l_dasar_total)->row()->pelaksanaan;
				}else {
					$realisasi_daerah_dasar_total[]=0;
				}
				$l_sarpras=substr_replace($l_sarpras, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_sarpras)->num_rows !=0 ){
					$realisasi_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan;
					$perencanaan_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan2;
					$perencanaan_sarpras[$index]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->perencanaan;
					$fisik=0;
					foreach($this->pm->dak_kegiatan_sarpras($l_sarpras)->result() as $rw){
						if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_sarpras[$index] !=0){
							$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_sarpras[$index];
						}else{
							$bobot=0;
						}
						$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}
					$fisik_sarpras[]=$fisik;
				}
					else {$realisasi_daerah_sarpras[]=0;
							$fisik_sarpras[]='0%';
					}
					$l_sarpras_total=substr_replace($l_sarpras_total, '', -1);
					if($this->pm->sum_kabupaten_sarpras($l_sarpras_total)->num_rows !=0 ){
						$realisasi_daerah_sarpras_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;}
					else {$realisasi_daerah_sarpras_total[]=0;
					}
					$l_tambahan=substr_replace($l_tambahan, '', -1);
					if($this->pm->sum_kabupaten_tambahan($l_tambahan)->num_rows !=0 ){
						$realisasi_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan;
						$perencanaan_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan2;
						$perencanaan_tambahan[$index]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->perencanaan;
						$fisik=0;
						foreach($this->pm->dak_kegiatan_tambahan($l_tambahan)->result() as $rw){
							if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_tambahan[$index] !=0){
								$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_tambahan[$index];
							}else{
								$bobot=0;
							}
							$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
						}
						$fisik_tambahan[]=$fisik;
					}
					else {$realisasi_daerah_tambahan[]=0;
							$fisik_tambahan[]='0%';
					}
					$l_tambahan_total=substr_replace($l_tambahan_total, '', -1);
					if($this->pm->sum_kabupaten_tambahan($l_tambahan_total)->num_rows !=0 ){
						$realisasi_daerah_tambahan_total[]=$this->pm->sum_kabupaten_tambahan($l_tambahan_total)->row()->pelaksanaan;}
					else {$realisasi_daerah_tambahan_total[]=0;
					}


						$l_total=substr_replace($l_total, '', -1);
						if($this->pm->sum_kabupaten_total($l_total)->num_rows !=0 ){
							$realisasi_daerah_total[]=$this->pm->sum_kabupaten_total($l_total)->row()->pelaksanaan;
							if( $realisasi_daerah_farmasi[$index]!=0){
								$bobot_farmasi=($realisasi_daerah_farmasi[$index]/$realisasi_daerah_total[$index]);
								$f_farmasi=$fisik_farmasi[$index]*$bobot_farmasi;
							}else {
								$f_farmasi=0;
							}
							if( $realisasi_daerah_dasar[$index]!=0){
								$bobot_dasar=($realisasi_daerah_dasar[$index]/$realisasi_daerah_total[$index]);
								$f_dasar=$fisik_dasar[$index]*$bobot_dasar;
							}else{
								$f_dasar=0;
							}
							if( $realisasi_daerah_rujukan[$index]!=0){
								$bobot_rujukan=($realisasi_daerah_rujukan[$index]/$realisasi_daerah_total[$index]);
								$f_rujukan=$fisik_rujukan[$index]*$bobot_rujukan;
							}else{
								$f_rujukan=0;
							}
							if( $realisasi_daerah_sarpras[$index]!=0){
								$bobot_sarpras=($realisasi_daerah_sarpras[$index]/$realisasi_daerah_total[$index]);
								$f_sarpras=$fisik_sarpras[$index]*$bobot_sarpras;
							}
							else{
								$f_sarpras=0;
							}
							if( $realisasi_daerah_tambahan[$index]!=0){
								/*$bobot_tambahan=($realisasi_daerah_tambahan[$index]/$realisasi_daerah_total[$index]);
								$f_tambahan=$fisik_tambahan[$index]*$bobot_tambahan*/;
							}
							else{
								$f_tambahan=0;
							}							
							$f_total[]=$f_farmasi+$f_dasar+$f_rujukan+$f_sarpras;
						}
						else {$realisasi_daerah_total[]=0;
							$f_total[]=0;
						}
			/*$nama_kabupaten=$rowz->NamaKabupaten;
			if ( (strpos($nama_kabupaten, 'KAB') !== false) ||  (strpos($nama_kabupaten, 'KOTA') !== false)) {
			
			$nama_kabupaten=$rowz->NamaKabupaten;
			}
			else
			{$nama_kabupaten='Provinsi '.$nama_kabupaten;}*/
			if($this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
				$pagu=$this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
				$realisasi_pagu_rujukan[]=$pagu->row()->Rujukan;
				$realisasi_pagu_farmasi[]=$pagu->row()->Farmasi;
				$realisasi_pagu_dasar[]=$pagu->row()->Pelayanan_Dasar;
				$realisasi_pagu_sarpras[]=$pagu->row()->Sarpras;
				$realisasi_pagu_tambahan[]=$pagu->row()->Tambahan_Dak_Kesehatan;				
				$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
			} else {
				$realisasi_pagu_rujukan[] = "0";
				$realisasi_pagu_farmasi[] = "0";
				$realisasi_pagu_sarpras[] = "0";
				$realisasi_pagu_tambahan[] = "0";				
				$realisasi_pagu_dasar[] = "0";
				$realisasi_pagu_total[] = "0";
			}
	//the following var_dump is only showing the last record.
	//need to show all rows (which should be 2)
	//var_dump($data); exit;
		}
        $l_rujukan_t=substr_replace($l_rujukan_t, '', -1);
        $l_farmasi_t=substr_replace($l_farmasi_t, '', -1);
        $l_dasar_t=substr_replace($l_dasar_t, '', -1);
        $l_sarpras_t=substr_replace($l_sarpras_t, '', -1);
        $l_tambahan_t=substr_replace($l_sarpras_t, '', -1);
        $fisik_farmasit=0;
        $fisik_rujukant=0;
        $fisik_dasart=0;
        $fisik_sarprast=0;
        $fisik_tambahant=0;
		foreach($this->pm->dak_kegiatan2($l_farmasi_t)->result() as $rw){
			if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && array_sum($perencanaan_farmasi) !=0){
				$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/ array_sum($perencanaan_farmasi);
			}else{
				$bobot=0;
			}
				
				$fisik_farmasit+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
		}
		$fisik_farmasi_t=$fisik_farmasit;
		foreach($this->pm->dak_kegiatan2($l_dasar_t)->result() as $rw){
			if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && array_sum($perencanaan_dasar) !=0){
				$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/ array_sum($perencanaan_dasar);
			}else{
				$bobot=0;
			}
				
				$fisik_dasart+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
		}
		$fisik_dasar_t=$fisik_dasart;
		foreach($this->pm->dak_kegiatan2($l_rujukan_t)->result() as $rw){
			if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && array_sum($perencanaan_rujukan) !=0){
				$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/ array_sum($perencanaan_rujukan);
			}else{
				$bobot=0;
			}
				
				$fisik_rujukant+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
		}
		$fisik_rujukan_t=$fisik_rujukant;		
		foreach($this->pm->dak_kegiatan_sarpras($l_sarpras_t)->result() as $rw){
			if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && array_sum($perencanaan_sarpras) !=0){
				$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/ array_sum($perencanaan_sarpras);
			}else{
				$bobot=0;
			}
				
				$fisik_sarprast+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
		}
		$fisik_sarpras_t=$fisik_sarprast;
		foreach($this->pm->dak_kegiatan_tambahan($l_tambahan_t)->result() as $rw){
			if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && array_sum($perencanaan_tambahan) !=0){
				$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/ array_sum($perencanaan_tambahan);
			}else{
				$bobot=0;
			}
				
				$fisik_tambahant+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
		}
		$fisik_tambahan_t=$fisik_tambahant;				
		if($fisik_sarprast !=0 || $fisik_rujukant !=0 || $fisik_dasart !=0 || $fisik_farmasit !=0 || $fisik_tambahant !=0){
			$fisik_total_t=($fisik_sarprast + $fisik_rujukant + $fisik_dasart + $fisik_farmasit+ $fisik_tambahant)/4;
		}else{
			$fisik_total_t=0;
		}								
		

		$total['pagu_rujukan']=	array_sum($realisasi_pagu_rujukan);
		$total['pagu_farmasi']=array_sum($realisasi_pagu_farmasi);
		$total['pagu_dasar']= array_sum($realisasi_pagu_dasar);
		$total['pagu_sarpras']=array_sum($realisasi_pagu_sarpras);
		$total['pagu_tambahan']=array_sum($realisasi_pagu_tambahan);				
		$total['pagu_total']=array_sum($realisasi_pagu_total);
		$total['daerah_rujukan']=	array_sum($realisasi_daerah_rujukan);
		$total['daerah_farmasi']=array_sum($realisasi_daerah_farmasi);
		$total['daerah_dasar']= array_sum($realisasi_daerah_dasar);
		$total['daerah_sarpras']=array_sum($realisasi_daerah_sarpras);
		$total['daerah_tambahan']= array_sum($realisasi_daerah_tambahan);				
		$total['daerah_total']=array_sum($realisasi_daerah_total);
		$total['fisik_farmasi']=$fisik_farmasi_t;
		$total['fisik_dasar']=$fisik_dasar_t;
		$total['fisik_rujukan']=$fisik_rujukan_t;
		$total['fisik_sarpras']=$fisik_sarpras_t;
		$total['fisik_tambahan']=$fisik_tambahan_t;
		$total['fisik_total']=$fisik_total_t;				
		return $total;
	}


	function rekap_juknis(){
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$d=0;
		$s=4;
		$juknis=$_GET["j"];
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$jk="tidak ada kegiatan";
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["d"]))$d=$_GET["d"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$idx=0;
		$kabupaten=$k;
		$provinsi=$p;
		$pos=strpos($juknis, '.');
		if ($pos  !== false) {
			$id=explode(".",$juknis);
			if(count($id)<3){
				if($this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->row()->JENIS_DAK;
			}else{
				if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->row()->JENIS_KEGIATAN;
				}
		} else {
			if($this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->num_rows())
				$jk=$this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->row()->JENIS_KEGIATAN;
		}
		$kabupaten=$this->pm->get_data_kabupaten($p,$k)->result();
		foreach($this->pm->get_data_kabupaten($p,$k)->result() as $index => $row){
				$l='';
			$laporan=$this->pm->dak_laporan($row->KodeKabupaten,$t,$row->KodeProvinsi,$d);
			if($laporan->num_rows !=0 ){
				foreach($laporan->result() as $row1){
					$l.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			$l=substr_replace($l, '', -1);
			if($this->pm->sum_kabupaten($l)->num_rows !=0 ){
				$realisasi_daerah[$index]=$this->pm->sum_kegiatan('REALISASI_KEUANGAN_PELAKSANAAN',$l,$juknis)->row()->REALISASI_KEUANGAN_PELAKSANAAN;
				$perencanaan_daerah[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$perencanaan[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$jumlah[$index]=$this->pm->sum_kegiatan('JUMLAH_PELAKSANAAN',$l,$juknis)->row()->JUMLAH_PELAKSANAAN;
				if($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->num_rows !=0){
					foreach ($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->result() as $row){
						$lokasi[$index]=$row->LOKASI_KEGIATAN;
					}
				}else{
					$lokasi[$index]='belum';
				}
				$fisik2=0;
				foreach($this->pm->dak_kegiatan3($l,$juknis)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN > 0 && $perencanaan[$index]>0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan[$index];
						$fisik2+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}else{
						$fisik2+=0;
					}
					}
				$fisik[]=$fisik2;
			}
			else {$realisasi_daerah[]=0;
				$fisik[]='0%';
			}
		}
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Detail Menu');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Juknis');
		$this->excel->getActiveSheet()->setCellValue('A2', $jk);
		foreach (range('A', 'E') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A2:D2');
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Daerah');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B4', 'Satuan');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D4', 'Realisasi Fisik');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E4', 'Lokasi');
		$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$i=5;
		$b=5;
		//cell
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$i++;
		}
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('B'.$b, $jumlah[$index]);
			$this->excel->getActiveSheet()->setCellValue('C'.$b, $this->mm->rupiah($realisasi_daerah[$index]));
			$this->excel->getActiveSheet()->setCellValue('D'.$b, round($fisik[$index],2));;
			$this->excel->getActiveSheet()->setCellValue('E'.$b, $lokasi[$index]);
			$b++;
		}
		$filename='rekap_juknis.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function rekap_juknis_indonesia(){
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$d=0;
		$s=4;
		$juknis=$_GET["j"];
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$jk="tidak ada kegiatan";
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["d"]))$d=$_GET["d"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$idx=0;
		$kabupaten=$k;
		$provinsi=$p;
		$pos=strpos($juknis, '.');
		if ($pos  !== false) {
			$id=explode(".",$juknis);
			if(count($id)<3){
				if($this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_sub_jenis_dak',$id[1],'ID_SUB_JENIS_DAK')->row()->JENIS_DAK;
			}else{
				if($this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->num_rows()!=0)
					$jk=$this->pm->get_where('dak_ss_jenis_kegiatan',$id[2],'ID_SS_JENIS_KEGIATAN')->row()->JENIS_KEGIATAN;
				}
		} else {
			if($this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->num_rows())
				$jk=$this->pm->get_where('dak_jenis_kegiatan',$juknis,'ID_DAK')->row()->JENIS_KEGIATAN;
		}
		$kabupaten=$this->pm->get_provinsi()->result();
		foreach($this->pm->get_provinsi()->result() as $index => $row){
				$l='';
			$laporan=$this->pm->dak_laporan(0,$t,$row->KodeProvinsi,$d);
			if($laporan->num_rows !=0 ){
				foreach($laporan->result() as $row1){
					$l.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			$l=substr_replace($l, '', -1);
			if($this->pm->sum_kabupaten($l)->num_rows !=0 ){
				$realisasi_daerah[$index]=$this->pm->sum_kegiatan('REALISASI_KEUANGAN_PELAKSANAAN',$l,$juknis)->row()->REALISASI_KEUANGAN_PELAKSANAAN;
				$perencanaan_daerah[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$perencanaan[$index]=$this->pm->sum_kegiatan('JUMLAH_TOTAL_PERENCANAAN',$l,$juknis)->row()->JUMLAH_TOTAL_PERENCANAAN;
				$jumlah[$index]=$this->pm->sum_kegiatan('JUMLAH_PELAKSANAAN',$l,$juknis)->row()->JUMLAH_PELAKSANAAN;
				if($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->num_rows !=0){
					foreach ($this->pm->get_where_double_like('dak_kegiatan',$juknis,'ID_JENIS_KEGIATAN',$l)->result() as $row){
						$lokasi[$index]=$row->LOKASI_KEGIATAN;
					}
				}else{
					$lokasi[$index]='belum';
				}
				$fisik2=0;
				foreach($this->pm->dak_kegiatan3($l,$juknis)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN > 0 && $perencanaan[$index]>0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan[$index];
						$fisik2+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}else{
						$fisik2+=0;
					}
					}
				$fisik[]=$fisik2;
			}
			else {$realisasi_daerah[]=0;
				$fisik[]='0%';
			}
		}
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Detail Menu');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Juknis');
		$this->excel->getActiveSheet()->setCellValue('A2', $jk);
		foreach (range('A', 'E') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A2:D2');
		$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Daerah');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('B4', 'Satuan');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D4', 'Realisasi Fisik');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E4', 'Lokasi');
		$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$i=5;
		$b=5;
		//cell
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);
			$i++;
		}
		foreach($kabupaten as $index => $row){
			$this->excel->getActiveSheet()->setCellValue('B'.$b, $jumlah[$index]);
			$this->excel->getActiveSheet()->setCellValue('C'.$b, $this->mm->rupiah($realisasi_daerah[$index]));
			$this->excel->getActiveSheet()->setCellValue('D'.$b, round($fisik[$index],2));;
			$this->excel->getActiveSheet()->setCellValue('E'.$b, $lokasi[$index]);
			$b++;
		}
	$filename='rekap_juknis.xls'; //save our workbook as this file name\
	ob_end_clean();
	header('Content-Type: application/vnd.ms-excel'); //mime type
	header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
	header('Cache-Control: max-age=0'); //no cache
	//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
	//if you want to save it as .XLSX Excel 2007 format
	$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
	//force user to download the Excel file without writing it to server's HD
	$objWriter->save('php://output');
	}

	function rekap_pagu()
	{
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		$status=4;
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["status"]))$status=$_GET["status"];
		$kabupaten=$k;
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten2($p,$k)->result();
		foreach($this->pm->get_data_kabupaten2($p,$k)->result()  as $index => $rowz){
			$l='';
			$l_tambahan = '';	
			$l_tambahan_total = '';
			$l_rujukan='';
			$l_rujukan_total='';
			$l_total='';
			$l_farmasi='';
			$l_farmasi_total='';
			$l_dasar='';
			$l_dasar_total='';
			$l_sarpras='';
			$l_sarpras_total='';
			$l_sarpras_rjk='';
			$l_sarpras_rjk_total='';			
			$laporan_rujukan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_rujukan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,1,$status);
			$laporan_farmasi=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_farmasi_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,2,$status);
			$laporan_dasar=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_dasar_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,3,$status);
			$laporan_sarpras=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,4,$status);
			$laporan_tambahan=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,7,$status);
			$laporan_tambahan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,7,$status);		
			$laporan_sarpras_rjk=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,5,$status);
			$laporan_sarpras_rjk_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,0,$rowz->KodeProvinsi,5,$status);
			$laporan_total=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,0,$status);
			if($laporan_rujukan->num_rows !=0 ){
				foreach($laporan_rujukan->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_total->num_rows !=0 ){
				foreach($laporan_rujukan_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi->num_rows !=0 ){
				foreach($laporan_farmasi->result() as $row2){
					$l_farmasi.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi_total->num_rows !=0 ){
				foreach($laporan_farmasi_total->result() as $row2){
					$l_farmasi_total.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar->num_rows !=0 ){
				foreach($laporan_dasar->result() as $row3){
					$l_dasar.=$row3->ID_LAPORAN_DAK.',';
				}
			}

			if($laporan_dasar_total->num_rows !=0 ){
				foreach($laporan_dasar_total->result() as $row3){
					$l_dasar_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras->num_rows !=0 ){
				foreach($laporan_sarpras->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_total->num_rows !=0 ){
				foreach($laporan_sarpras_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan->num_rows !=0 ){
				foreach($laporan_tambahan->result() as $row3){
					$l_tambahan.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_tambahan_total->num_rows !=0 ){
				foreach($laporan_tambahan_total->result() as $row3){
					$l_tambahan_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}			
			if($laporan_sarpras_rjk->num_rows !=0 ){
				foreach($laporan_sarpras_rjk->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk_total->num_rows !=0 ){
				foreach($laporan_sarpras_rjk_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_total->num_rows !=0 ){
				foreach($laporan_total->result() as $row3){
					$l_total.=$row3->ID_LAPORAN_DAK.',';}
			}
			$l_rujukan=substr_replace($l_rujukan, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan)->num_rows !=0 ){
				$realisasi_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan;
				$perencanaan_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan2;
				$perencanaan_rujukan[$index]=$this->pm->sum_kabupaten($l_rujukan)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_rujukan)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$jml_rs=$this->pm->dak_laporan_status2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,1,$status)->num_rows();	
				if($jml_rs!=0){
					$fisik_rujukan[]=$fisik/($jml_rs+1);
				}else{
					$fisik_rujukan[]=$fisik;
				}
			}
			else {$realisasi_daerah_rujukan[]=0;
				$fisik_rujukan[]='0%';
			}
			$l_rujukan_total=substr_replace($l_rujukan_total, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan_total)->num_rows !=0 ){
				$realisasi_daerah_rujukan_total[]=$this->pm->sum_kabupaten($l_rujukan_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_rujukan_total[]=0;
			}
			$l_farmasi=substr_replace($l_farmasi, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi)->num_rows !=0 ){
				$realisasi_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan;
				$perencanaan_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan2;
				$perencanaan_farmasi[$index]=$this->pm->sum_kabupaten($l_farmasi)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_farmasi)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_farmasi[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_farmasi[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_farmasi[]=$fisik;
			}
			else {$realisasi_daerah_farmasi[]='0';
				$fisik_farmasi[]='0%';
			}
			$l_farmasi_total=substr_replace($l_farmasi_total, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi_total)->num_rows !=0 ){
				$realisasi_daerah_farmasi_total[]=$this->pm->sum_kabupaten($l_farmasi_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_farmasi_total[]=0;
			}
			$l_dasar=substr_replace($l_dasar, '', -1);
			if($this->pm->sum_kabupaten($l_dasar)->num_rows !=0 ){
				$realisasi_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan;
				$perencanaan_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan2;
				$perencanaan_dasar[$index]=$this->pm->sum_kabupaten($l_dasar)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan2($l_dasar)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_rujukan[$index] !=0){
					$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
					}else{
					$bobot=0;
						}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
				$fisik_dasar[]=$fisik;
			}
			else {
				$realisasi_daerah_dasar[]=0;
				$fisik_dasar[]='0%';
			}
			$l_dasar_total=substr_replace($l_dasar_total, '', -1);
			if($this->pm->sum_kabupaten($l_dasar_total)->num_rows !=0 ){
					$realisasi_daerah_dasar_total[]=$this->pm->sum_kabupaten($l_dasar_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_dasar_total[]=0;
			}
			$l_sarpras=substr_replace($l_sarpras, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras)->num_rows !=0 ){
				$realisasi_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan;
				$perencanaan_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan2;
				$perencanaan_sarpras[$index]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->perencanaan;
				$fisik=0;
				foreach($this->pm->dak_kegiatan_sarpras($l_sarpras)->result() as $rw){
					if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_sarpras[$index] !=0){
						$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_sarpras[$index];
					}else{
						$bobot=0;
					}
					
					$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
				}
					$fisik_sarpras[]=$fisik;
			}else {
				$realisasi_daerah_sarpras[]=0;
				$fisik_sarpras[]='0%';
			}
				$l_sarpras_total=substr_replace($l_sarpras_total, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_sarpras_total)->num_rows !=0 ){
					$realisasi_daerah_sarpras_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
				}else {
					$realisasi_daerah_sarpras_total[]=0;
				}
				$l_tambahan=substr_replace($l_tambahan, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_tambahan)->num_rows !=0 ){
					$realisasi_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan;
					$perencanaan_daerah_tambahan[]=$this->pm->sum_kabupaten_tambahan($l_tambahan)->row()->pelaksanaan2;
					$fisik=0;
					foreach($this->pm->dak_kegiatan_tambahan($l_tambahan)->result() as $rw){
						if($rw->JUMLAH_TOTAL_PERENCANAAN != 0 && $perencanaan_tambahan[$index] !=0){
							$bobot=$rw->JUMLAH_TOTAL_PERENCANAAN/$perencanaan_rujukan[$index];
						}else{
							$bobot=0;
						}
						$fisik+=$rw->REALISASI_FISIK_PELAKSANAAN*$bobot;
					}
					$fisik_tambahan[]=$fisik;
				}
				else {$realisasi_daerah_tambahan[]=0;
					$fisik_tambahan[]='0%';
				}
				$l_tambahan_total=substr_replace($l_tambahan_total, '', -1);
				if($this->pm->sum_kabupaten_sarpras($l_tambahan_total)->num_rows !=0 ){
					$realisasi_daerah_tambahan_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
				}
				else {
					$realisasi_daerah_tambahan_total[]=0;
				}				
				$l_total=substr_replace($l_total, '', -1);
				if($this->pm->sum_kabupaten_total($l_total)->num_rows !=0 ){
					$realisasi_daerah_total[]=$this->pm->sum_kabupaten_total($l_total)->row()->pelaksanaan;
					if( $realisasi_daerah_farmasi[$index]!=0){
						$bobot_farmasi=($realisasi_daerah_farmasi[$index]/$realisasi_daerah_total[$index]);
						$f_farmasi=$fisik_farmasi[$index]*$bobot_farmasi;
					}else {
						$f_farmasi=0;
					}
					if( $realisasi_daerah_dasar[$index]!=0){
						$bobot_dasar=($realisasi_daerah_dasar[$index]/$realisasi_daerah_total[$index]);
						$f_dasar=$fisik_dasar[$index]*$bobot_dasar;
					}else{
						$f_dasar=0;
					}
					if( $realisasi_daerah_rujukan[$index]!=0){
						$bobot_rujukan=($realisasi_daerah_rujukan[$index]/$realisasi_daerah_total[$index]);
						$f_rujukan=$fisik_rujukan[$index]*$bobot_rujukan;
					}else{
						$f_rujukan=0;
					}
					if( $realisasi_daerah_sarpras[$index]!=0){
						$bobot_sarpras=($realisasi_daerah_sarpras[$index]/$realisasi_daerah_total[$index]);
						$f_sarpras=$fisik_sarpras[$index]*$bobot_sarpras;
					}
					else{
						$f_sarpras=0;
					}
					if( $realisasi_daerah_tambahan[$index]!=0){
						$bobot_tambahan=($realisasi_daerah_tambahan[$index]/$realisasi_daerah_tambahan[$index]);
						$f_tambahan=$fisik_tambahan[$index]*$bobot_tambahan;
					}
					else{
						$f_tambahan=0;
					}							
					$f_total[]=($f_farmasi+$f_dasar+$f_rujukan+$f_sarpras+$f_tambahan)/5;
				}
				else {
					$realisasi_daerah_total[]=0;
					$f_total[]=0;
				}
				if($this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
					$pagu=$this->pm->get_where_double('data_pagu',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
					$realisasi_pagu_rujukan[]=$pagu->row()->Rujukan;
					$realisasi_pagu_farmasi[]=$pagu->row()->Farmasi;
					$realisasi_pagu_dasar[]=$pagu->row()->Pelayanan_Dasar;
					$realisasi_pagu_sarpras[]=$pagu->row()->Sarpras;
					$realisasi_pagu_tambahan[]=$pagu->row()->Tambahan_Dak_Kesehatan;				
					$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
				} else {
					$realisasi_pagu_rujukan[] = "0";
					$realisasi_pagu_farmasi[] = "0";
					$realisasi_pagu_sarpras[] = "0";
					$realisasi_pagu_tambahan[] = "0";				
					$realisasi_pagu_dasar[] = "0";
					$realisasi_pagu_total[] = "0";
				}
			//the following var_dump is only showing the last record.
			//need to show all rows (which should be 2)
			//var_dump($data); exit;
			}
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Realisasi Pagu');
			$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Realisasi Pagu');
			foreach (range('A', 'U') as $char) {
				$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
			}
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A1:D1');
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//header
			$this->excel->getActiveSheet()->setCellValue('A3', 'Kabupaten kota');
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A3:A4');
			$this->excel->getActiveSheet()->setCellValue('B3', 'Rujukan');
			$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('B3:E3');
			$this->excel->getActiveSheet()->setCellValue('F3', 'farmasi');
			$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('F3:I3');
			$this->excel->getActiveSheet()->setCellValue('J3', 'Pelayanan Dasar');
			$this->excel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('J3:M3');
			$this->excel->getActiveSheet()->setCellValue('N3', 'Sarana Prasarana');
			$this->excel->getActiveSheet()->getStyle('N3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('N3:Q3');
			$this->excel->getActiveSheet()->setCellValue('R3', 'total');
			$this->excel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('R3:U3');
			$this->excel->getActiveSheet()->setCellValue('B4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('I4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('L4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('M4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('N4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('O4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('P4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('Q4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('R4', 'Pagu Total');
			$this->excel->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('S4', 'Realisasi Total');
			$this->excel->getActiveSheet()->getStyle('S4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('T4', 'Fisik Total');
			$this->excel->getActiveSheet()->getStyle('T4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('U4', 'Presentase Total');
			$this->excel->getActiveSheet()->getStyle('U4')->getFont()->setBold(true);
			$i=6;
			$b=6;
			$f=5;
//cell
			foreach($kabupaten as $index => $row){
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
				$i++;
			}
			foreach($kabupaten as $index => $row){
				$this->excel->getActiveSheet()->getStyle('B'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('C'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('D'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('E'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('F'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('G'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('H'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('I'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('J'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('K'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('L'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('M'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('N'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('O'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('P'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('Q'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('R'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('S'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('T'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
				$this->excel->getActiveSheet()->getStyle('U'.$f)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

				if($realisasi_pagu_rujukan[$index] !='-'){
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $realisasi_pagu_rujukan[$index]);
					$this->excel->getActiveSheet()->setCellValue('C'.$b, $realisasi_daerah_rujukan[$index]);
					$this->excel->getActiveSheet()->setCellValue('D'.$b, round($fisik_rujukan[$index],2));
					if( $realisasi_daerah_rujukan[$index]!=0 && $realisasi_pagu_rujukan[$index]!=0){
						$status=$realisasi_daerah_rujukan[$index]/$realisasi_pagu_rujukan[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('E'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('F'.$b, $realisasi_pagu_farmasi[$index]);
					$this->excel->getActiveSheet()->setCellValue('G'.$b, $realisasi_daerah_farmasi[$index]);
					$this->excel->getActiveSheet()->setCellValue('H'.$b, round($fisik_farmasi[$index],2));
					if( $realisasi_daerah_farmasi[$index]!=0 && $realisasi_pagu_farmasi[$index]!=0){
						$status=$realisasi_daerah_farmasi[$index]/$realisasi_pagu_farmasi[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('I'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('J'.$b, $realisasi_pagu_dasar[$index]);
					$this->excel->getActiveSheet()->setCellValue('K'.$b, $realisasi_daerah_dasar[$index]);
					$this->excel->getActiveSheet()->setCellValue('L'.$b, round($fisik_dasar[$index],2));
					if( $realisasi_daerah_dasar[$index]!=0 && $realisasi_pagu_dasar[$index]!=0){
						$status=$realisasi_daerah_dasar[$index]/$realisasi_pagu_dasar[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('M'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('N'.$b, $realisasi_pagu_sarpras[$index]);
					$this->excel->getActiveSheet()->setCellValue('O'.$b, $realisasi_daerah_sarpras[$index]);
					$this->excel->getActiveSheet()->setCellValue('P'.$b, round($fisik_sarpras[$index],2));
					if( $realisasi_daerah_sarpras[$index]!=0 && $realisasi_pagu_sarpras[$index]!=0){
						$status=$realisasi_daerah_sarpras[$index]/$realisasi_pagu_sarpras[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('Q'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('R'.$b, $realisasi_pagu_total[$index]);
					$this->excel->getActiveSheet()->setCellValue('S'.$b, $realisasi_daerah_total[$index]);
					$this->excel->getActiveSheet()->setCellValue('T'.$b, round($f_total[$index],2));
					if( $realisasi_daerah_total[$index]!=0 && $realisasi_pagu_total[$index]!=0){
						$status=$realisasi_daerah_total[$index]/$realisasi_pagu_total[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('U'.$b, round($status, 2));
					}
				}
				$b++;
				$f++;
			}
			$this->excel->getActiveSheet()->setCellValue('A5', 'TOTAL :');
			$this->excel->getActiveSheet()->setCellValue('B5', array_sum($realisasi_pagu_rujukan));
			$this->excel->getActiveSheet()->setCellValue('C5', array_sum($realisasi_daerah_rujukan));
			$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
			//$this->excel->getActiveSheet()->setCellValue('D5'.$b, round($fisik_rujukan[$index],2));
			if( array_sum($realisasi_daerah_rujukan)!=0 && array_sum($realisasi_pagu_rujukan)!=0){
				$status=array_sum($realisasi_daerah_rujukan)/array_sum($realisasi_pagu_rujukan)*100;
				$this->excel->getActiveSheet()->setCellValue('E5', round($status, 2));
				$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
			}
			$this->excel->getActiveSheet()->setCellValue('F5', array_sum($realisasi_pagu_farmasi));
			$this->excel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G5', array_sum($realisasi_daerah_farmasi));
			$this->excel->getActiveSheet()->getStyle('G5')->getFont()->setBold(true);
			//$this->excel->getActiveSheet()->setCellValue('H5'.$b, round($fisik_farmasi[$index],2));
			if( array_sum($realisasi_daerah_farmasi)!=0 && array_sum($realisasi_pagu_farmasi)!=0){
				$status= array_sum($realisasi_daerah_farmasi)/array_sum($realisasi_pagu_farmasi)*100;
				$this->excel->getActiveSheet()->setCellValue('I5', round($status, 2));
				$this->excel->getActiveSheet()->getStyle('I5')->getFont()->setBold(true);
			}
			$this->excel->getActiveSheet()->setCellValue('J5', array_sum($realisasi_pagu_dasar));
			$this->excel->getActiveSheet()->getStyle('J5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K5', array_sum($realisasi_daerah_dasar));
			$this->excel->getActiveSheet()->getStyle('K5')->getFont()->setBold(true);
			//$this->excel->getActiveSheet()->setCellValue('L5', round($fisik_dasar[$index],2));
			if( array_sum($realisasi_daerah_dasar)!=0 && array_sum($realisasi_pagu_dasar)!=0){
				$status=array_sum($realisasi_daerah_dasar)/array_sum($realisasi_pagu_dasar)*100;
				$this->excel->getActiveSheet()->setCellValue('M5', round($status, 2));
				$this->excel->getActiveSheet()->getStyle('M5')->getFont()->setBold(true);
			}
			$this->excel->getActiveSheet()->setCellValue('N5', array_sum($realisasi_pagu_sarpras));
			$this->excel->getActiveSheet()->getStyle('N5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('O5', array_sum($realisasi_daerah_sarpras));
			$this->excel->getActiveSheet()->getStyle('O5')->getFont()->setBold(true);
			//$this->excel->getActiveSheet()->setCellValue('P5'.$b, round($fisik_sarpras[$index],2));
			if( array_sum($realisasi_daerah_sarpras)!=0 && array_sum($realisasi_pagu_sarpras)!=0){
				$status=array_sum($realisasi_daerah_sarpras)/array_sum($realisasi_pagu_sarpras)*100;
				$this->excel->getActiveSheet()->setCellValue('Q5', round($status, 2));
				$this->excel->getActiveSheet()->getStyle('Q5')->getFont()->setBold(true);
			}
			$this->excel->getActiveSheet()->setCellValue('R5', array_sum($realisasi_pagu_total));
			$this->excel->getActiveSheet()->getStyle('R5')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('S5', array_sum($realisasi_daerah_total));
			$this->excel->getActiveSheet()->getStyle('S5')->getFont()->setBold(true);
			//$this->excel->getActiveSheet()->setCellValue('T5'.$b, round($f_total[$index],2));
			if( array_sum($realisasi_daerah_total)!=0 && array_sum($realisasi_pagu_total)!=0){
				$status=array_sum($realisasi_daerah_total)/array_sum($realisasi_pagu_total)*100;
				$this->excel->getActiveSheet()->setCellValue('U5', round($status, 2));
				$this->excel->getActiveSheet()->getStyle('U5')->getFont()->setBold(true);
			}
		$filename='rekap_pagu.xls'; //save our workbook as this file name
		ob_end_clean();		
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}


	function table_pagu_indonesia_nf(){
		//$this->output->enable_profiler(TRUE);
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
		$currentpage = (int) $_GET['page'];
		$prevpage = $currentpage - 1;
		$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$total_records = $this->pm->get_provinsi_limit($num_rec_per_page, $start_from)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$s=4;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$kabupaten=$k;
		$provinsi=$p;
		$provinsi2=$this->pm->get_provinsi()->result();
		foreach($this->pm->get_provinsi()->result() as $index => $rowz){
			$l=''; $l_bok='';
			$l_bok_total='';
			$l_total='';
			$l_akreditasi_rs='';
			$l_akreditasi_rs_total='';
			$l_akreditasi_puskesmas='';
			$l_akreditasi_puskesmas_total='';
			$laporan_nf=0;
			foreach($this->pm->dak_laporan_nf_indo(0,$t,$rowz->KodeProvinsi,$s)->result() as $rw){
				$laporan_nf.=$rw->ID_LAPORAN_DAK.',';
			}
			$laporan_nf=substr_replace($laporan_nf, '', -1);
			if($this->pm->sum_kabupaten_nf2($laporan_nf,1)->num_rows !=0 ){
				$realisasi_daerah_bok[]=$this->pm->sum_kabupaten_nf2($laporan_nf,1)->row()->pelaksanaan;
				$realisasi_daerah_jampersal[]=$this->pm->sum_kabupaten_nf2($laporan_nf,2)->row()->pelaksanaan;
				$realisasi_daerah_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf2($laporan_nf,4)->row()->pelaksanaan;
				$realisasi_daerah_akreditasi_rs[]=$this->pm->sum_kabupaten_nf2($laporan_nf,3)->row()->pelaksanaan;
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_nf2($laporan_nf,0)->row()->pelaksanaan;
				$realisasi_daerah_lainnya[]=$this->pm->sum_kabupaten_nf2($laporan_nf,5)->row()->pelaksanaan;
			}else {
				$realisasi_daerah_bok[]=0;
				$realisasi_daerah_jampersal[]=0;
				$realisasi_daerah_akreditasi_puskesmas[]=0;
				$realisasi_daerah_akreditasi_rs[]=0;
				$realisasi_daerah_lainnya[]=0;
			}
				if($this->pm->sum4('data_pagu_nf','BANTUAN_OPERASIONAL_KESEHATAN', 'AKREDITASI_RUMAH_SAKIT','AKREDITASI_PUSKESMAS','JAMINAN_PERSALINAN','KodeProvinsi',$rowz->KodeProvinsi)->num_rows !=0 ){
					$pagu=$this->pm->sum4('data_pagu_nf','BANTUAN_OPERASIONAL_KESEHATAN', 'AKREDITASI_RUMAH_SAKIT','AKREDITASI_PUSKESMAS','JAMINAN_PERSALINAN','KodeProvinsi',$rowz->KodeProvinsi);
					$realisasi_pagu_bok[]=$pagu->row()->BANTUAN_OPERASIONAL_KESEHATAN;
					$realisasi_pagu_akreditasi_rs[]=$pagu->row()->AKREDITASI_RUMAH_SAKIT;
					$realisasi_pagu_akreditasi_puskesmas[]=$pagu->row()->AKREDITASI_PUSKESMAS;
					$realisasi_pagu_jampersal[]=$pagu->row()->JAMINAN_PERSALINAN;
					$realisasi_pagu_total[]=$realisasi_pagu_jampersal[$index] + $realisasi_pagu_bok[$index] + $realisasi_pagu_akreditasi_rs[$index] +  $realisasi_pagu_akreditasi_puskesmas[$index];
				} else {
					$realisasi_pagu_bok[] = 0;
					$realisasi_pagu_akreditasi_rs[] = 0;
					$realisasi_pagu_akreditasi_puskesmas[] = 0;
					$realisasi_pagu_jampersal[] = 0;
					$realisasi_pagu_total[] = 0;
				}				
			//the following var_dump is only showing the last record.
			//need to show all rows (which should be 2)
			//var_dump($data); exit;
		}
			$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_indonesia_nf?p='.$p.'&t='.$t.'&k='.$k.'&s='.$s.'" >
			<img src="'.base_url().'images/main/excel.png" > Print excel</a>
			';
			$data['k']=$k;
			$data['button']=$button;
			$data['provinsi2']=$provinsi2;
			$data['nama_daerah']=$nama;
			$data['realisasi_daerah_jampersal']=$realisasi_daerah_jampersal;
			$data['realisasi_daerah_akreditasi_rs']=$realisasi_daerah_akreditasi_rs;
			$data['realisasi_daerah_akreditasi_puskesmas']=$realisasi_daerah_akreditasi_puskesmas;
			$data['realisasi_daerah_bok']=$realisasi_daerah_bok;
			$data['realisasi_daerah_total']=$realisasi_daerah_total;
			$data['realisasi_daerah_lainnya']=$realisasi_daerah_lainnya;
			$data['realisasi_pagu_akreditasi_puskesmas']=$realisasi_pagu_akreditasi_puskesmas;
			$data['realisasi_pagu_bok']=$realisasi_pagu_bok;
			$data['realisasi_pagu_akreditasi_rs']=$realisasi_pagu_akreditasi_rs;
			$data['realisasi_pagu_jampersal']=$realisasi_pagu_jampersal;
			$data['realisasi_pagu_total']=$realisasi_pagu_total;
			$data['t']=$t;
			$data['total_pages']=$total_pages;
			$data['currentpage']=$currentpage;
			$data['nextpage']=$nextpage;
			$data['prevpage']=$prevpage;
			$this->load->view('tabel_pagu_indonesia_nf',$data);
	}

	function table_pagu_indonesia(){
		$num_rec_per_page=10;
		if (isset($_GET["page"])) { $page  = $_GET["page"];
		$currentpage = (int) $_GET['page'];
		$prevpage = $currentpage - 1;
		$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$status=4;
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$status=$_GET["s"];
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		$total_records = $this->pm->get_provinsi()->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		$provinsi2=$this->pm->get_provinsi()->result();
		foreach($this->pm->get_provinsi()->result() as $index=>$rowz){
			$l='';
			$l_rujukan='';
			$l_rujukan_total='';
			$l_total='';
			$l_farmasi='';
			$l_farmasi_total='';
			$l_dasar='';
			$l_dasar_total='';
			$l_sarpras='';
			$l_sarpras_total='';
			$l_sarpras_rjk='';
			$l_sarpras_rjk_total='';		
			$realisasi_pagu_rujukan[$index]=array();
			$realisasi_pagu_farmasi[$index]=array();
			$realisasi_pagu_dasar[$index]=array();
			$realisasi_pagu_sarpras[$index]=array();
			$realisasi_pagu_total[$index]=array();
			$pagu_rujukan[]=array();
			$pagu_farmasi[]=array();
			$pagu_dasar[]=array();
			$pagu_sarpras[]=array();
			$total[]=array();				
			$laporan_rujukan=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_rujukan_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_farmasi=$this->pm->dak_laporan3(0,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_farmasi_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_dasar=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_dasar_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_sarpras=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,4,$status);		
			$laporan_sarpras_rjk=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,5,$status);
			$laporan_sarpras_rjk_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,5,$status);				
			$laporan_total=$this->pm->dak_laporan3(0,$t,$rowz->KodeProvinsi,0);
			if($laporan_rujukan->num_rows !=0 ){
				foreach($laporan_rujukan->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_total->num_rows !=0 ){
				foreach($laporan_rujukan_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi->num_rows !=0 ){
				foreach($laporan_farmasi->result() as $row2){
					$l_farmasi.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi_total->num_rows !=0 ){
				foreach($laporan_farmasi_total->result() as $row2){
					$l_farmasi_total.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar->num_rows !=0 ){
				foreach($laporan_dasar->result() as $row3){
					$l_dasar.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar_total->num_rows !=0 ){
				foreach($laporan_dasar_total->result() as $row3){
					$l_dasar_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras->num_rows !=0 ){
				foreach($laporan_sarpras->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_total->num_rows !=0 ){
				foreach($laporan_sarpras_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}	
			if($laporan_sarpras_rjk->num_rows !=0 ){
				foreach($laporan_sarpras_rjk->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk_total->num_rows !=0 ){
				foreach($laporan_sarpras_rjk_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}			
			if($laporan_total->num_rows !=0 ){
			foreach($laporan_total->result() as $row3){
				$l_total.=$row3->ID_LAPORAN_DAK.',';}
			}
			$l_rujukan=substr_replace($l_rujukan, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan)->num_rows !=0 ){
				$realisasi_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan;
				$perencanaan_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan2;
			}
			else {$realisasi_daerah_rujukan[]=0;}
			$l_rujukan_total=substr_replace($l_rujukan_total, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan_total)->num_rows !=0 ){
				$realisasi_daerah_rujukan_total[]=$this->pm->sum_kabupaten($l_rujukan_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_rujukan_total[]=0;}
			$l_farmasi=substr_replace($l_farmasi, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi)->num_rows !=0 ){
				$realisasi_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan;
				$perencanaan_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan2;
			}
			$l_farmasi_total=substr_replace($l_farmasi_total, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi_total)->num_rows !=0 ){
				$realisasi_daerah_farmasi_total[]=$this->pm->sum_kabupaten($l_farmasi_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_farmasi_total[]=0;}
			$l_dasar=substr_replace($l_dasar, '', -1);
			if($this->pm->sum_kabupaten($l_dasar)->num_rows !=0 ){
					$realisasi_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan;
					$perencanaan_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan2;
			}
			else {$realisasi_daerah_dasar[]=0;}
			$l_dasar_total=substr_replace($l_dasar_total, '', -1);
			if($this->pm->sum_kabupaten($l_dasar_total)->num_rows !=0 ){
					$realisasi_daerah_dasar_total[]=$this->pm->sum_kabupaten($l_dasar_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_dasar_total[]=0;}
			$l_sarpras=substr_replace($l_sarpras, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras)->num_rows !=0 ){
					$realisasi_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan;
					$perencanaan_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan2;
			}
			else {$realisasi_daerah_sarpras[]=0;}
			$l_sarpras_total=substr_replace($l_sarpras_total, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras_total)->num_rows !=0 ){
				$realisasi_daerah_sarpras_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_sarpras_total[]=0;}
				$l_total=substr_replace($l_total, '', -1);
			if($this->pm->sum_kabupaten_total($l_total)->num_rows !=0 ){
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_total($l_total)->row()->pelaksanaan;
			}
			else {
				$realisasi_daerah_total[]=0;
			}
			foreach ($this->pm->get_data_kabupaten($rowz->KodeProvinsi)->result() as $index2=>$rowzz){
					$pagu_rujukan[]=array();
					$pagu_farmasi[]=array();
					$pagu_dasar[]=array();
					$pagu_sarpras[]=array();
					$total[]=array();						
				if($this->pm->get_where_double('data_pagu',$rowzz->KodeKabupaten,'KodeKabupaten',$rowzz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
					$pagu=$this->pm->get_where_double('data_pagu',$rowzz->KodeKabupaten,'KodeKabupaten',$rowzz->KodeProvinsi,'KodeProvinsi');
					$pagu_rujukan[]=$pagu->row()->Rujukan;
					$pagu_farmasi[]=$pagu->row()->Farmasi;
					$pagu_dasar[]=$pagu->row()->Pelayanan_Dasar;
					$pagu_sarpras[]=$pagu->row()->Sarpras;
					$total[]=$this->pm->get_pagu_keseluruhan($rowzz->KodeKabupaten,$rowzz->KodeProvinsi)->row()->total;
				} else {
					$pagu_rujukan[]= 0;
					$pagu_farmasi[]= 0;
					$pagu_sarpras[]=0;
					$pagu_dasar[]=0;
					$pagu_total[]=0;

				}
					
			}
			$realisasi_pagu_rujukan[$index]=array_sum ($pagu_rujukan );
			$realisasi_pagu_farmasi[$index]=array_sum ($pagu_farmasi);
			$realisasi_pagu_dasar[$index]=array_sum ( $pagu_dasar);
			$realisasi_pagu_sarpras[$index]=array_sum ( $pagu_sarpras);
			$realisasi_pagu_total[$index]=array_sum ( $total);
			unset($pagu_rujukan);
			unset($pagu_farmasi);
			unset($pagu_dasar);
			unset($pagu_sarpras);
			unset($total);

		}
		$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_indonesia?s='.$status.'&t='.$t.'" >
		<img src="'.base_url().'images/main/excel.png" > Print excel</a>
		';
		$data['k']=$k;
		$data['provinsi2']=$provinsi2;
		$data['button']=$button;
		$data['nama_daerah']=$nama;
		$data['total_pages']=$total_pages;
		$data['realisasi_daerah_farmasi']=$realisasi_daerah_farmasi;
		$data['perencanaan_daerah_farmasi']=$perencanaan_daerah_farmasi;
		$data['realisasi_daerah_dasar']=$realisasi_daerah_dasar;
		$data['realisasi_daerah_rujukan']=$realisasi_daerah_rujukan;
		$data['realisasi_daerah_total']=$realisasi_daerah_total;
		$data['realisasi_pagu_dasar']=$realisasi_pagu_dasar;
		$data['realisasi_pagu_rujukan']=$realisasi_pagu_rujukan;
		$data['realisasi_pagu_farmasi']=$realisasi_pagu_farmasi;
		$data['realisasi_pagu_sarpras']=$realisasi_pagu_sarpras;
		$data['realisasi_pagu_total']=$realisasi_pagu_total;
		$data['realisasi_daerah_sarpras']=$realisasi_daerah_sarpras;
		$data['t']=$t;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$data['total_pagu_rujukan']=array_sum($realisasi_pagu_rujukan);
		$data['total_pagu_farmasi']=array_sum($realisasi_pagu_farmasi);
		$data['total_pagu_dasar']= array_sum($realisasi_pagu_dasar);
		$data['total_pagu_sarpras']=array_sum($realisasi_pagu_sarpras);
		$data['total_pagu_total']=array_sum($realisasi_pagu_total);
		$data['total_daerah_rujukan']=	array_sum($realisasi_daerah_rujukan);
		$data['total_daerah_farmasi']=array_sum($realisasi_daerah_farmasi);
		$data['total_daerah_dasar']= array_sum($realisasi_daerah_dasar);
		$data['total_daerah_sarpras']=array_sum($realisasi_daerah_sarpras);
		$data['total_daerah_total']=array_sum($realisasi_daerah_total);	
		$this->load->view('tabel_pagu_indonesia',$data);
	}



	function createColumnsArray($end_column, $first_letters = '')
	{
  		$columns = array();
  		$length = strlen($end_column);
 		$letters = range('A', 'Z');

  		// Iterate over 26 letters.
  		foreach ($letters as $letter) {
      // Paste the $first_letters before the next.
     	 $column = $first_letters . $letter;

      	// Add the column to the final array.
      	$columns[] = $column;

      	// If it was the end column that was added, return the columns.
      	if ($column == $end_column)
          return $columns;
  		}

 		 // Add the column children.
  		foreach ($columns as $column) {
      	// Don't itterate if the $end_column was already set in a previous itteration.
      	// Stop iterating if you've reached the maximum character length.
     		 if (!in_array($end_column, $columns) && strlen($column) < $length) {
       		 	 $new_columns = $this->createColumnsArray($end_column, $column);
   	    	 	  // Merge the new columns which were created with the final columns array.
        		  $columns = array_merge($columns, $new_columns);
   	   		}
  		}

  		return $columns;
	}
  
	function rekap_menu_seluruh(){
		$j=0;
		$i=0;
		$k=0;
		$p=0;
		$s=4;
		$t=1;
		$d=1; 
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["t"]))$t=$_GET["t"];		
		if(isset($_GET["d"]))$d=$_GET["d"];	

		if($d==1){
			$dak='rujukan';
		}else if($d==2){
			$dak='Farmasi';
		}else if($d==3){
			$dak='Pelayanan Dasar';
		}
		$daerah=$this->pm->get_data_kabupaten_limit2($p,$k)->result();
		$seluruhjenis=array();
		$dak_kegiatan=array();
		$idk=array();
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Seluruh');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh '.$dak.' Triwulan '.$t);	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Daerah');
		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('A6:A9');
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU');
		$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('B6:B9');	
		$klm=2;
		//header menu juknis
		if($this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
			foreach($this->pm->dak_jenis_kegiatan($d)->result() as $row){
				$seluruhjenis[] = $row->JENIS_KEGIATAN;
				$id1=$row->ID_DAK;
				$idk[]=$row->ID_DAK;
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'6',  $row->JENIS_KEGIATAN);
				$this->excel->getActiveSheet()->getStyle($kolom[$klm].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				if($this->pm->dak_sub_kegiatan($row->ID_DAK)->num_rows() > 0){
					$klms=$klm;
					foreach($this->pm->dak_sub_kegiatan($row->ID_DAK)->result() as $row2){
						$seluruhjenis[] =$row2->JENIS_DAK;
						$id2=$row2->ID_SUB_JENIS_DAK;
						$idk[]=$id1.'.'.$id2;
						$this->excel->getActiveSheet()->setCellValue($kolom[$klms].'7',  $row2->JENIS_DAK);	
						$this->excel->getActiveSheet()->getStyle($kolom[$klms].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);										
						if($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->num_rows() > 0){
							$klmss=$klms;
							foreach($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->result() as $row3){
								$seluruhjenis[] =$row3->JENIS_KEGIATAN; 
								$id3=$row3->ID_SS_JENIS_KEGIATAN;
								$id_k[]=$id1.'.'.$id2.'.'.$id3;	
								$idk[]=$id1.'.'.$id2.'.'.$id3;							
								$j++;
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss].'8',  $row3->JENIS_KEGIATAN);
								$this->excel->getActiveSheet()->getStyle($kolom[$klmss].'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$klmss2=$klmss;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Jumlah');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Realisasi');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Fisik');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$this->excel->getActiveSheet()->mergeCells($kolom[$klmss].'8:'.$kolom[$klmss2].'8');
								$klmss=$klmss2;									
								$klmss++;		
							}
							$klmss=$klmss-1;
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klmss].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klmss].'7');
							$klms=$klmss;
							$klms++;
						}else{

							$id_k[]=$id1.'.'.$id2;
							$klms2=$klms;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Jumlah');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;							
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Realisasi');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Fisik');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms2].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klms2].'7');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'8:'.$kolom[$klms2].'8');
							$klms=$klms2;							
							$klms++;							
						}						
						$i++;
					}
					//$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms].'6');					
					$klms=$klms-1;
					$klm=$klms;
					$klm++;
				}else{
					$id_k[]=$row->ID_DAK;
					$klm2=$klm;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Jumlah');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;					
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Realisasi');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Fisik');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klm2].'6');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'7:'.$kolom[$klm2].'7');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'8:'.$kolom[$klm2].'8');
					$klm=$klm2;
					$klm++;

				}

			}}

		//header menu juknis end	
		//header end


		$i=10;	
		$end=end($kol);
		$this->excel->getActiveSheet()->getStyle('A6:'.$end.'6')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A7:'.$end.'7')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A8:'.$end.'8')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A9:'.$end.'9')->applyFromArray($styleHeader);
		
		foreach($daerah as  $row2){
			    $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
				$pagu=$this->pm->get_where_double('data_pagu',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
				$id_laporan=$this->pm->get_where_quadruple('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN',$d,'JENIS_DAK');
				if($id_laporan->num_rows() !=0){
					$id_lp=$id_laporan->row()->ID_LAPORAN_DAK;
					$xx[]=$id_laporan->row()->ID_LAPORAN_DAK;
				}else{
					$id_lp=0;
				}
				if($d==1){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Rujukan;
					}else{
					$_pagu=0;	
					}
				}else if($d==2){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Farmasi;
					}else{
					$_pagu=0;	
					}
				}else if($d==3){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Pelayanan_Dasar;
					}else{
					$_pagu=0;	
					}
				}
				$klx=0;
				//nama daerah
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row2->NamaKabupaten);
				//end nama daerah
				//pagu
				$this->excel->getActiveSheet()->setCellValue('B'.$i, number_format($_pagu));				
				//end pagu
				foreach($id_k as $id){
				//realisasi dan fisik

					$realisasi=$this->pm->get_where_double2('dak_kegiatan','ID_JENIS_KEGIATAN',$id,'ID_LAPORAN_DAK',$id_lp);
					
					if($realisasi->num_rows() !=0){
						$id_l[]=$id;
						$jumlah=$realisasi->row()->JUMLAH_PELAKSANAAN;
						$realisasi_daerah=$realisasi->row()->REALISASI_KEUANGAN_PELAKSANAAN;
						$fisik=$realisasi->row()->REALISASI_FISIK_PELAKSANAAN;
						if($jumlah<1)$jumlah='0';
						if($realisasi_daerah<1)$realisasi_daerah='0';
						if($fisik<1)$fisik='0';						
					}else{
						$realisasi_daerah='0';
						$fisik='0';
						$jumlah='0';
					}
                    
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $jumlah);
					$klx++;
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $realisasi_daerah);
					$klx++;
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $fisik);
					$klx++;	
				//end realisasi dan fisik		
				}

				$i++;
		}	
		unset($styleArray);
		$filename='rekap_seluruh_menu.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		
	}  


	function rekap_menu_sarpras_seluruh(){
		$j=0;
		$i=0;
		$k=0;
		$p=0;
		$s=4;
		$t=1;
		$d=1; 
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["t"]))$t=$_GET["t"];		
		if(isset($_GET["d"]))$d=$_GET["d"];	
		$id_dak=4;
		if($d==1){
			$id_dak='5';
			$dak='rujukan';
		}else if($d==2){
			$dak='Farmasi';
		}else if($d==3){
			$dak='Pelayanan Dasar';
		}
		$daerah=$this->pm->get_data_kabupaten_limit2($p,$k)->result();
		$seluruhjenis=array();
		$dak_kegiatan=array();
		$idk=array();
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Seluruh');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh Sarpras '.$dak.' Triwulan '.$t);	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Daerah');
		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('A6:A9');
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU');
		$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('B6:B9');	
		$klm=2;
		//header menu juknis
		if($this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
			foreach($this->pm->dak_jenis_kegiatan($d)->result() as $row){
				$seluruhjenis[] = $row->JENIS_KEGIATAN;
				$id1=$row->ID_DAK;
				$idk[]=$row->ID_DAK;
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'6',  $row->JENIS_KEGIATAN);
				$this->excel->getActiveSheet()->getStyle($kolom[$klm].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				if($this->pm->dak_sub_kegiatan($row->ID_DAK)->num_rows() > 0){
					$klms=$klm;
					foreach($this->pm->dak_sub_kegiatan($row->ID_DAK)->result() as $row2){
						$seluruhjenis[] =$row2->JENIS_DAK;
						$id2=$row2->ID_SUB_JENIS_DAK;
						$idk[]=$id1.'.'.$id2;
						$this->excel->getActiveSheet()->setCellValue($kolom[$klms].'7',  $row2->JENIS_DAK);	
						$this->excel->getActiveSheet()->getStyle($kolom[$klms].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);										
						if($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->num_rows() > 0){
							$klmss=$klms;
							foreach($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->result() as $row3){
								$seluruhjenis[] =$row3->JENIS_KEGIATAN; 
								$id3=$row3->ID_SS_JENIS_KEGIATAN;
								$id_k[]=$d.'.'.$id1.'.'.$id2.'.'.$id3;	
								$idk[]=$id1.'.'.$id2.'.'.$id3;							
								$j++;
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss].'8',  $row3->JENIS_KEGIATAN);
								$this->excel->getActiveSheet()->getStyle($kolom[$klmss].'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$klmss2=$klmss;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Jumlah');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;								
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Realisasi');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Fisik');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$this->excel->getActiveSheet()->mergeCells($kolom[$klmss].'8:'.$kolom[$klmss2].'8');
								$klmss=$klmss2;									
								$klmss++;		
							}
							$klmss=$klmss-1;
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klmss].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klmss].'7');
							$klms=$klmss;
							$klms++;
						}else{

							$id_k[]=$d.'.'.$id1.'.'.$id2;
							$klms2=$klms;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Jumlah');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;							
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Realisasi');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Fisik');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms2].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klms2].'7');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'8:'.$kolom[$klms2].'8');
							$klms=$klms2;							
							$klms++;							
						}						
						$i++;
					}
					//$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms].'6');					
					$klms=$klms-1;
					$klm=$klms;
					$klm++;
				}else{
					$id_k[]=$d.'.'.$row->ID_DAK;
					$klm2=$klm;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Jumlah');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;					
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Realisasi');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Fisik');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klm2].'6');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'7:'.$kolom[$klm2].'7');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'8:'.$kolom[$klm2].'8');
					$klm=$klm2;
					$klm++;

				}

			}}

		//header menu juknis end	
		//header end


		$i=10;	
		$end=end($kol);
		$this->excel->getActiveSheet()->getStyle('A6:'.$end.'6')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A7:'.$end.'7')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A8:'.$end.'8')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A9:'.$end.'9')->applyFromArray($styleHeader);
		
		foreach($daerah as  $row2){
			    $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
				$pagu=$this->pm->get_where_double('data_pagu',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
				$id_laporan=$this->pm->get_where_quadruple('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN',$id_dak,'JENIS_DAK');
				if($id_laporan->num_rows() !=0){
					$id_lp=$id_laporan->row()->ID_LAPORAN_DAK;
				}else{
					$id_lp=0;
				}
				if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Sarpras;
				}else{
					$_pagu=0;	
				}
				
				$klx=0;
				//nama daerah
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row2->NamaKabupaten);
				//end nama daerah
				//pagu
				$this->excel->getActiveSheet()->setCellValue('B'.$i, number_format($_pagu));				
				//end pagu
				foreach($id_k as $id){
				//realisasi dan fisik

					$realisasi=$this->pm->get_where_double2('dak_kegiatan_sarpras','KODE',$id,'ID_LAPORAN_DAK',$id_lp);
					
					if($realisasi->num_rows() !=0){
						$id_l[]=$id;
						$jumlah=$realisasi->row()->JUMLAH_PELAKSANAAN;
						$realisasi_daerah=$realisasi->row()->REALISASI_KEUANGAN_PELAKSANAAN;
						$fisik=$realisasi->row()->REALISASI_FISIK_PELAKSANAAN;
					}else{
						$realisasi_daerah='0';
						$fisik='0';
						$jumlah='0';
					}
                    
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $jumlah);
					$klx++;
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $realisasi_daerah);
					$klx++;
					$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $fisik);
					$klx++;	
					
					
					
				//end realisasi dan fisik		
				}

				$i++;
		}	
		unset($styleArray);
		$filename='rekap_seluruh_menu.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		
	}  


	function rekap_menu_rjk_seluruh(){
		$j=0;
		$i=0;
		$t=1;
		$d=1;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["t"]))$t=$_GET["t"];


		$daerah=$this->pm->get_data_kabupaten_limit2($p,$k)->result();
		$seluruhjenis=array();
		$dak_kegiatan=array();
		$idk=array();
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Seluruh');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh Rujukan Triwulan '.$t);	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten');
		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('A6:A9');
		$this->excel->getActiveSheet()->setCellValue('B6', 'Nama Provinsi');
		$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('B6:B9');	
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU');
		$this->excel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('C6:C9');				
		$this->excel->getActiveSheet()->setCellValue('D6', 'Nama Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('D6:D9');	
		$this->excel->getActiveSheet()->setCellValue('E6', 'Penyelenggara');
		$this->excel->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('E6:E9');		

		$klm=5;

		//header menu juknis
		if($this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
			foreach($this->pm->dak_jenis_kegiatan($d)->result() as $row){
				$seluruhjenis[] = $row->JENIS_KEGIATAN;
				$id1=$row->ID_DAK;
				$idk[]=$row->ID_DAK;
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'6',  $row->JENIS_KEGIATAN);
				$this->excel->getActiveSheet()->getStyle($kolom[$klm].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				if($this->pm->dak_sub_kegiatan($row->ID_DAK)->num_rows() > 0){
					$klms=$klm;
					foreach($this->pm->dak_sub_kegiatan($row->ID_DAK)->result() as $row2){
						$seluruhjenis[] =$row2->JENIS_DAK;
						$id2=$row2->ID_SUB_JENIS_DAK;
						$idk[]=$id1.'.'.$id2;
						$this->excel->getActiveSheet()->setCellValue($kolom[$klms].'7',  $row2->JENIS_DAK);	
						$this->excel->getActiveSheet()->getStyle($kolom[$klms].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);										
						if($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->num_rows() > 0){
							$klmss=$klms;
							foreach($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->result() as $row3){
								$seluruhjenis[] =$row3->JENIS_KEGIATAN; 
								$id3=$row3->ID_SS_JENIS_KEGIATAN;
								$id_k[]=$id1.'.'.$id2.'.'.$id3;	
								$idk[]=$id1.'.'.$id2.'.'.$id3;							
								$j++;
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss].'8',  $row3->JENIS_KEGIATAN);
								$this->excel->getActiveSheet()->getStyle($kolom[$klmss].'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$klmss2=$klmss;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Jumlah');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;								
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Realisasi');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Fisik');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$this->excel->getActiveSheet()->mergeCells($kolom[$klmss].'8:'.$kolom[$klmss2].'8');
								$klmss=$klmss2;									
								$klmss++;		
							}
							$klmss=$klmss-1;
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klmss].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klmss].'7');
							$klms=$klmss;
							$klms++;
						}else{

							$id_k[]=$id1.'.'.$id2;
							$klms2=$klms;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Jumlah');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;							
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Realisasi');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Fisik');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms2].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klms2].'7');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'8:'.$kolom[$klms2].'8');
							$klms=$klms2;							
							$klms++;							
						}						
						$i++;
					}
					//$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms].'6');					
					$klms=$klms-1;
					$klm=$klms;
					$klm++;
				}else{
					$id_k[]=$row->ID_DAK;
					$klm2=$klm;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Jumlah');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;					
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Realisasi');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Fisik');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klm2].'6');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'7:'.$kolom[$klm2].'7');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'8:'.$kolom[$klm2].'8');
					$klm=$klm2;
					$klm++;

				}

			}}

		//header menu juknis end	
		//header end


		$i=10;	
		$b=10;
		$end=end($kol);
		$this->excel->getActiveSheet()->getStyle('A6:'.$end.'6')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A7:'.$end.'7')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A8:'.$end.'8')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A9:'.$end.'9')->applyFromArray($styleHeader);
		
		foreach($daerah as  $row2){
			    $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
			   // $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
				$pagu=$this->pm->get_where_double('data_pagu',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
				$id_laporan=$this->pm->get_where_quadruple('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN','1','JENIS_DAK');
				if($id_laporan->num_rows() !=0){
					$id_lp=$id_laporan->row()->ID_LAPORAN_DAK;
				}else{
					$id_lp=0;
				}
				if($d==1){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Rujukan;
					}else{
					$_pagu=0;	
					}
				}else if($d==2){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Farmasi;
					}else{
					$_pagu=0;	
					}
				}else if($d==3){
					if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Pelayanan_Dasar;
					}else{
					$_pagu=0;	
					}
				}
				
				//nama daerah
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row2->NamaKabupaten);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $row2->NamaProvinsi);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, number_format($_pagu));		
				//end nama daerah
				//rs
				$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi')->result();
				if(!empty($data_rs)) {	
					 $rs_loop=0;				
					 foreach($data_rs as $index2 => $row3){
                            $klx=0;
		 					$this->excel->getActiveSheet()->setCellValue('D'.$i, $row3->NAMA_RS);
		 					$this->excel->getActiveSheet()->setCellValue('E'.$i, $row3->PENYELENGGARA);
		 					$id_lap=$this->pm->get_where_5('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN', $row3->KODE_RS,'KD_RS', 1,'JENIS_DAK');
		 					if($id_lap->num_rows()!=0){
		 						$id_lp=$id_lap->row()->ID_LAPORAN_DAK;
		 					}else{
		 						$id_lp='0';
		 					}

		 					//$this->excel->getActiveSheet()->setCellValue('E'.$i, $jml[$rows->KD_RS]);
		 					$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
							foreach($id_k as $id){
							//realisasi dan fisik
								$realisasi=$this->pm->get_where_double2('dak_kegiatan','ID_JENIS_KEGIATAN',$id,'ID_LAPORAN_DAK',$id_lp);
					
								if($realisasi->num_rows() !=0){
									$realisasi_daerah=$realisasi->row()->REALISASI_KEUANGAN_PELAKSANAAN;
									$fisik=$realisasi->row()->REALISASI_FISIK_PELAKSANAAN;
									$jumlah=$realisasi->row()->JUMLAH_PELAKSANAAN;
									if($jumlah<1)$jumlah='0';
									if($realisasi_daerah<1)$realisasi_daerah='0';
									if($fisik<1)$fisik='0';											
								}else{
									$realisasi_daerah='0';
									$fisik='0';
									$jumlah='0';
								}
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $jumlah);
								$klx++;
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $realisasi_daerah);
								$klx++;
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $fisik);
								$klx++;						
								//end realisasi dan fisik		
							}		 					
		 					$i++;
		 					 $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
		 					$rs_loop++;

		 				
					}
					if($rs_loop!=0)$i=$i-1;		
				}	
				$this->excel->getActiveSheet()->mergeCells('A'.$b.':A'.$i);
				$this->excel->getActiveSheet()->mergeCells('B'.$b.':B'.$i);		
				$this->excel->getActiveSheet()->mergeCells('C'.$b.':C'.$i);							
	  			$i++;	
	  			$b=$i;
		}	
		//unset($styleArray);
		$filename='rekap_seluruh_menu_rujukan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		
	}  

	function rekap_menu_sarpras_rjk_seluruh(){
		$j=0;
		$i=0;
		$t=1;
		$d=1;
		$s=4;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["t"]))$t=$_GET["t"];


		$daerah=$this->pm->get_data_kabupaten_limit2($p,$k)->result();
		$seluruhjenis=array();
		$dak_kegiatan=array();
		$idk=array();
		$kolom=$this->createColumnsArray('ZZ');
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Seluruh');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Menu Seluruh Sarpras Rujukan Triwulan '.$t);	
		$styleArray = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			)
		);
		$styleHeader = array(
  			'borders' => array(
    			'allborders' => array(
      			'style' => PHPExcel_Style_Border::BORDER_THIN
    			)
  			),
  			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'CFC1C1')
        		)	
		);		
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		//header			
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten');
		$this->excel->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('A6:A9');
		$this->excel->getActiveSheet()->setCellValue('B6', 'Nama Provinsi');
		$this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('B6:B9');	
		$this->excel->getActiveSheet()->setCellValue('C6', 'PAGU');
		$this->excel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('C6:C9');				
		$this->excel->getActiveSheet()->setCellValue('D6', 'Nama Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('D6:D9');	
		$this->excel->getActiveSheet()->setCellValue('E6', 'Penyelenggara');
		$this->excel->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$this->excel->getActiveSheet()->mergeCells('E6:E9');		

		$klm=5;

		//header menu juknis
		if($this->pm->dak_jenis_kegiatan($d)->num_rows() != 0){
			foreach($this->pm->dak_jenis_kegiatan($d)->result() as $row){
				$seluruhjenis[] = $row->JENIS_KEGIATAN;
				$id1=$row->ID_DAK;
				$idk[]=$row->ID_DAK;
				$this->excel->getActiveSheet()->setCellValue($kolom[$klm].'6',  $row->JENIS_KEGIATAN);
				$this->excel->getActiveSheet()->getStyle($kolom[$klm].'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
				if($this->pm->dak_sub_kegiatan($row->ID_DAK)->num_rows() > 0){
					$klms=$klm;
					foreach($this->pm->dak_sub_kegiatan($row->ID_DAK)->result() as $row2){
						$seluruhjenis[] =$row2->JENIS_DAK;
						$id2=$row2->ID_SUB_JENIS_DAK;
						$idk[]=$id1.'.'.$id2;
						$this->excel->getActiveSheet()->setCellValue($kolom[$klms].'7',  $row2->JENIS_DAK);	
						$this->excel->getActiveSheet()->getStyle($kolom[$klms].'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);										
						if($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->num_rows() > 0){
							$klmss=$klms;
							foreach($this->pm->dak_ss_kegiatan($row2->ID_SUB_JENIS_DAK)->result() as $row3){
								$seluruhjenis[] =$row3->JENIS_KEGIATAN; 
								$id3=$row3->ID_SS_JENIS_KEGIATAN;
								$id_k[]=$d.'.'.$id1.'.'.$id2.'.'.$id3;	
								$idk[]=$id1.'.'.$id2.'.'.$id3;							
								$j++;
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss].'8',  $row3->JENIS_KEGIATAN);
								$this->excel->getActiveSheet()->getStyle($kolom[$klmss].'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$klmss2=$klmss;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Jumlah');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;								
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Realisasi');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$klmss2++;
								$kol[]=$kolom[$klmss2];
								$this->excel->getActiveSheet()->setCellValue($kolom[$klmss2].'9',  'Fisik');
								$this->excel->getActiveSheet()->getColumnDimension($kolom[$klmss2])->setWidth(40);
								$this->excel->getActiveSheet()->mergeCells($kolom[$klmss].'8:'.$kolom[$klmss2].'8');
								$klmss=$klmss2;									
								$klmss++;		
							}
							$klmss=$klmss-1;
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klmss].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klmss].'7');
							$klms=$klmss;
							$klms++;
						}else{

							$id_k[]=$d.'.'.$id1.'.'.$id2;
							$klms2=$klms;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Jumlah');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;							
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Realisasi');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$klms2++;
							$kol[]=$kolom[$klms2];
							$this->excel->getActiveSheet()->setCellValue($kolom[$klms2].'9',  'Fisik');
							$this->excel->getActiveSheet()->getColumnDimension($kolom[$klms2])->setWidth(40);
							$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms2].'6');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'7:'.$kolom[$klms2].'7');
							$this->excel->getActiveSheet()->mergeCells($kolom[$klms].'8:'.$kolom[$klms2].'8');
							$klms=$klms2;							
							$klms++;							
						}						
						$i++;
					}
					//$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klms].'6');					
					$klms=$klms-1;
					$klm=$klms;
					$klm++;
				}else{
					$id_k[]=$d.'.'.$row->ID_DAK;
					$klm2=$klm;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Jumlah');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;					
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Realisasi');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$klm2++;
					$kol[]=$kolom[$klm2];
					$this->excel->getActiveSheet()->setCellValue($kolom[$klm2].'9',  'Fisik');
					$this->excel->getActiveSheet()->getColumnDimension($kolom[$klm2])->setWidth(40);
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'6:'.$kolom[$klm2].'6');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'7:'.$kolom[$klm2].'7');
					$this->excel->getActiveSheet()->mergeCells($kolom[$klm].'8:'.$kolom[$klm2].'8');
					$klm=$klm2;
					$klm++;

				}

			}}

		//header menu juknis end	
		//header end


		$i=10;	
		$b=10;
		$end=end($kol);
		$this->excel->getActiveSheet()->getStyle('A6:'.$end.'6')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A7:'.$end.'7')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A8:'.$end.'8')->applyFromArray($styleHeader);
		$this->excel->getActiveSheet()->getStyle('A9:'.$end.'9')->applyFromArray($styleHeader);
		
		foreach($daerah as  $row2){
			    $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
			   // $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
				$pagu=$this->pm->get_where_double('data_pagu',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
				$id_laporan=$this->pm->get_where_quadruple('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN','1','JENIS_DAK');
				if($id_laporan->num_rows() !=0){
					$id_lp=$id_laporan->row()->ID_LAPORAN_DAK;
				}else{
					$id_lp=0;
				}
				if($pagu->num_rows() !=0){
					$_pagu=$pagu->row()->Sarpras;
				}else{
					$_pagu=0;	
				}
				
				//nama daerah
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row2->NamaKabupaten);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $row2->NamaProvinsi);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, number_format($_pagu));		
				//end nama daerah
				//rs
				$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi')->result();
				if(!empty($data_rs)) {	
					 $rs_loop=0;				
					 foreach($data_rs as $index2 => $row3){
                            $klx=0;
		 					$this->excel->getActiveSheet()->setCellValue('D'.$i, $row3->NAMA_RS);
		 					$this->excel->getActiveSheet()->setCellValue('E'.$i, $row3->PENYELENGGARA);
		 					$id_lap=$this->pm->get_where_5('dak_laporan',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi',$t,'WAKTU_LAPORAN', $row3->KODE_RS,'KD_RS', 5,'JENIS_DAK');
		 					if($id_lap->num_rows()!=0){
		 						$id_lp=$id_lap->row()->ID_LAPORAN_DAK;
		 					}else{
		 						$id_lp='0';
		 					}

		 					//$this->excel->getActiveSheet()->setCellValue('E'.$i, $jml[$rows->KD_RS]);
		 					$data_rs=$this->pm->get_where_double('data_rumah_sakit',$row2->KodeKabupaten,'KodeKabupaten',$row2->KodeProvinsi,'KodeProvinsi');
							foreach($id_k as $id){
							//realisasi dan fisik
								$realisasi=$this->pm->get_where_double2('dak_kegiatan_sarpras','KODE',$id,'ID_LAPORAN_DAK',$id_lp);
					
								if($realisasi->num_rows() !=0){
									$realisasi_daerah=$realisasi->row()->REALISASI_KEUANGAN_PELAKSANAAN;
									$fisik=$realisasi->row()->REALISASI_FISIK_PELAKSANAAN;
									$jumlah=$realisasi->row()->JUMLAH_PELAKSANAAN;
								}else{
									$realisasi_daerah='0';
									$fisik='0';
									$jumlah='0';
								}
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $jumlah);
								$klx++;
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $realisasi_daerah);
								$klx++;
								$this->excel->getActiveSheet()->setCellValue($kol[$klx].$i, $fisik);
								$klx++;						
								//end realisasi dan fisik		
							}		 					
		 					$i++;
		 					 $this->excel->getActiveSheet()->getStyle('A'.$i.':'.$end.$i)->applyFromArray($styleArray);
		 					$rs_loop++;

		 				
					}
					if($rs_loop!=0)$i=$i-1;		
				}	
				$this->excel->getActiveSheet()->mergeCells('A'.$b.':A'.$i);
				$this->excel->getActiveSheet()->mergeCells('B'.$b.':B'.$i);		
				$this->excel->getActiveSheet()->mergeCells('C'.$b.':C'.$i);							
	  			$i++;	
	  			$b=$i;
		}	
		//unset($styleArray);
		$filename='rekap_seluruh_menu_rujukan.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cacheq
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
		
	}  

	function rekap_indonesia(){
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$status=4;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$status2=$_GET["s"];
		$provinsi2=$this->pm->get_provinsi()->result();
		foreach($this->pm->get_provinsi()->result() as $index=>$rowz){
			$l='';
			$l_rujukan='';
			$l_rujukan_total='';
			$l_total='';
			$l_farmasi='';
			$l_farmasi_total='';
			$l_dasar='';
			$l_dasar_total='';
			$l_sarpras='';
			$l_sarpras_total='';
		
			$realisasi_pagu_rujukan[$index]=array();
			$realisasi_pagu_farmasi[$index]=array();
			$realisasi_pagu_dasar[$index]=array();
			$realisasi_pagu_sarpras[$index]=array();
			$realisasi_pagu_sarpras_rjk[$index]=array();
			$realisasi_pagu_total[$index]=array();
			$laporan_rujukan=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_rujukan_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,1,$status);
			$laporan_farmasi=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_farmasi_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,2,$status);
			$laporan_dasar=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_dasar_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,3,$status);
			$laporan_sarpras=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,4,$status);
			$laporan_sarpras_rjk=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,5,$status);
			$laporan_sarpras_rjk_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,5,$status);								
			$laporan_total=$this->pm->dak_laporan2(0,$t,$rowz->KodeProvinsi,0,$status);		
			if($laporan_rujukan->num_rows !=0 ){
				foreach($laporan_rujukan->result() as $row1){
					$l_rujukan.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_rujukan_total->num_rows !=0 ){
				foreach($laporan_rujukan_total->result() as $row1){
					$l_rujukan_total.=$row1->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi->num_rows !=0 ){
				foreach($laporan_farmasi->result() as $row2){
					$l_farmasi.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_farmasi_total->num_rows !=0 ){
				foreach($laporan_farmasi_total->result() as $row2){
					$l_farmasi_total.=$row2->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar->num_rows !=0 ){
				foreach($laporan_dasar->result() as $row3){
					$l_dasar.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_dasar_total->num_rows !=0 ){
				foreach($laporan_dasar_total->result() as $row3){
					$l_dasar_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras->num_rows !=0 ){
				foreach($laporan_sarpras->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_total->num_rows !=0 ){
				foreach($laporan_sarpras_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk->num_rows !=0 ){
				foreach($laporan_sarpras_rjk->result() as $row3){
					$l_sarpras.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			if($laporan_sarpras_rjk_total->num_rows !=0 ){
				foreach($laporan_sarpras_rjk_total->result() as $row3){
					$l_sarpras_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}						
			if($laporan_total->num_rows !=0 ){
				foreach($laporan_total->result() as $row3){
					$l_total.=$row3->ID_LAPORAN_DAK.',';
				}
			}
			$l_rujukan=substr_replace($l_rujukan, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan)->num_rows !=0 ){
				$realisasi_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan;
				$perencanaan_daerah_rujukan[]=$this->pm->sum_kabupaten($l_rujukan)->row()->pelaksanaan2;
			}
			else {$realisasi_daerah_rujukan[]=0;}
			$l_rujukan_total=substr_replace($l_rujukan_total, '', -1);
			if($this->pm->sum_kabupaten($l_rujukan_total)->num_rows !=0 ){
				$realisasi_daerah_rujukan_total[]=$this->pm->sum_kabupaten($l_rujukan_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_rujukan_total[]=0;}
			$l_farmasi=substr_replace($l_farmasi, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi)->num_rows !=0 ){
				$realisasi_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan;
				$perencanaan_daerah_farmasi[]=$this->pm->sum_kabupaten($l_farmasi)->row()->pelaksanaan2;
			}
			$l_farmasi_total=substr_replace($l_farmasi_total, '', -1);
			if($this->pm->sum_kabupaten($l_farmasi_total)->num_rows !=0 ){
				$realisasi_daerah_farmasi_total[]=$this->pm->sum_kabupaten($l_farmasi_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_farmasi_total[]=0;
			}
			$l_dasar=substr_replace($l_dasar, '', -1);
			if($this->pm->sum_kabupaten($l_dasar)->num_rows !=0 ){
				$realisasi_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan;
				$perencanaan_daerah_dasar[]=$this->pm->sum_kabupaten($l_dasar)->row()->pelaksanaan2;
			}
			else {
				$realisasi_daerah_dasar[]=0;
			}
			$l_dasar_total=substr_replace($l_dasar_total, '', -1);
			if($this->pm->sum_kabupaten($l_dasar_total)->num_rows !=0 ){
			$realisasi_daerah_dasar_total[]=$this->pm->sum_kabupaten($l_dasar_total)->row()->pelaksanaan;}
			else {$realisasi_daerah_dasar_total[]=0;}
			$l_sarpras=substr_replace($l_sarpras, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras)->num_rows !=0 ){
				$realisasi_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan;
				$perencanaan_daerah_sarpras[]=$this->pm->sum_kabupaten_sarpras($l_sarpras)->row()->pelaksanaan2;
			}
			else {
				$realisasi_daerah_sarpras[]=0;
			}
			$l_sarpras_total=substr_replace($l_sarpras_total, '', -1);
			if($this->pm->sum_kabupaten_sarpras($l_sarpras_total)->num_rows !=0 ){
				$realisasi_daerah_sarpras_total[]=$this->pm->sum_kabupaten_sarpras($l_sarpras_total)->row()->pelaksanaan;
			}
			else {$realisasi_daerah_sarpras_total[]=0;}
			$l_total=substr_replace($l_total, '', -1);
			if($this->pm->sum_kabupaten_total($l_total)->num_rows !=0 ){
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_total($l_total)->row()->pelaksanaan;}
					else {$realisasi_daerah_total[]=0;}
				foreach ($this->pm->get_data_kabupaten($rowz->KodeProvinsi)->result() as $index2=>$rowzz){
					$pagu_rujukan[]=array();
					$pagu_farmasi[]=array();
					$pagu_dasar[]=array();
					$pagu_sarpras[]=array();
					$total[]=array();		
					if($this->pm->get_where_double('data_pagu',$rowzz->KodeKabupaten,'KodeKabupaten',$rowzz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
						$pagu=$this->pm->get_where_double('data_pagu',$rowzz->KodeKabupaten,'KodeKabupaten',$rowzz->KodeProvinsi,'KodeProvinsi');
						$pagu_rujukan[]=$pagu->row()->Rujukan;
						$pagu_farmasi[]=$pagu->row()->Farmasi;
						$pagu_dasar[]=$pagu->row()->Pelayanan_Dasar;
						$pagu_sarpras[]=$pagu->row()->Sarpras;
						$total[]=$this->pm->get_pagu_keseluruhan($rowzz->KodeKabupaten,$rowzz->KodeProvinsi)->row()->total;
					} else {
						$pagu_rujukan[]= 0;
						$pagu_farmasi[]= 0;
						$pagu_sarpras[]=0;
						$pagu_dasar[]=0;
						$pagu_total[]=0;
					}
				}
				$realisasi_pagu_rujukan[$index]=array_sum ($pagu_rujukan );
				$realisasi_pagu_farmasi[$index]=array_sum ($pagu_farmasi);
				$realisasi_pagu_dasar[$index]=array_sum ( $pagu_dasar);
				$realisasi_pagu_sarpras[$index]=array_sum ( $pagu_sarpras);
				$realisasi_pagu_total[$index]=array_sum ( $total);
				unset($pagu_rujukan);
				unset($pagu_farmasi);
				unset($pagu_dasar);
				unset($pagu_sarpras);
				unset($total);				

			}
			$style = array(
						 'alignment' => array(
					      'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				       	 )
				   	);
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->setTitle('Realisasi Pagu');
			$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Realisasi Pagu');
			foreach (range('A', 'U') as $char) {
				$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
			}
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A1:D1');
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			//header
			$this->excel->getActiveSheet()->setCellValue('A3', 'Provinsi');
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('A3:A4');
			$this->excel->getActiveSheet()->setCellValue('B3', 'Rujukan');
			$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('B3:E3');
			$this->excel->getActiveSheet()->setCellValue('F3', 'farmasi');
			$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('F3:I3');
			$this->excel->getActiveSheet()->setCellValue('J3', 'Pelayanan Dasar');
			$this->excel->getActiveSheet()->getStyle('J3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('J3:M3');
			$this->excel->getActiveSheet()->setCellValue('N3', 'Sarana Prasarana');
			$this->excel->getActiveSheet()->getStyle('N3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('N3:Q3');
			$this->excel->getActiveSheet()->setCellValue('R3', 'total');
			$this->excel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->mergeCells('R3:U3');
			$this->excel->getActiveSheet()->setCellValue('B4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('D4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('E4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('F4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('G4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('H4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);			
			$this->excel->getActiveSheet()->setCellValue('I4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('J4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('K4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('L4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('M4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('N4', 'Pagu');
			$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('O4', 'Realisasi Triwulan');
			$this->excel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('P4', 'Fisik Triwulan');
			$this->excel->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('Q4', 'Presentase Triwulan');
			$this->excel->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('R4', 'Pagu Total');
			$this->excel->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('S4', 'Realisasi Total');
			$this->excel->getActiveSheet()->getStyle('S4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('T4', 'Fisik Total');
			$this->excel->getActiveSheet()->getStyle('T4')->getFont()->setBold(true);
			$this->excel->getActiveSheet()->setCellValue('U4', 'Presentase Total');
			$this->excel->getActiveSheet()->getStyle('U4')->getFont()->setBold(true);
			$i=5;
			$b=5;
//cell
			foreach($provinsi2 as $index => $row){
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);
				$i++;
			}
			foreach($provinsi2 as $index => $row){
				$rata_rujukan[$index]=$this->pm->get_average_laporan(1,$t,$row->KodeProvinsi,$status2)->row()->rata;
				$rata_farmasi[$index]=$this->pm->get_average_laporan(2,$t,$row->KodeProvinsi,$status2)->row()->rata;
				$rata_dasar[$index]=$this->pm->get_average_laporan(3,$t,$row->KodeProvinsi,$status2)->row()->rata;
				$rata_sarpras[$index]=$this->pm->get_average_sarpras($t,$row->KodeProvinsi,$status2)->row()->rata;
				$rata_seluruh[$index]=$this->pm->get_average_laporan_seluruh($t,$row->KodeProvinsi,$status2)->row()->rata;	
					$this->excel->getActiveSheet()->getStyle('B'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('C'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('D'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('E'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('F'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('G'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('H'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('I'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('J'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('K'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('L'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('M'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('N'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('O'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('P'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('Q'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('R'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					$this->excel->getActiveSheet()->getStyle('S'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('T'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					//$this->excel->getActiveSheet()->getStyle('U'.$b)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
					
					$this->excel->getActiveSheet()->setCellValue('B'.$b, number_format($realisasi_pagu_rujukan[$index]));
					$this->excel->getActiveSheet()->setCellValue('C'.$b, number_format($realisasi_daerah_rujukan[$index]));
					$this->excel->getActiveSheet()->setCellValue('D'.$b,  round($rata_rujukan[$index],2));
					if( $realisasi_daerah_rujukan[$index]!=0 && $realisasi_pagu_rujukan[$index]!=0){
						$status=$realisasi_daerah_rujukan[$index]/$realisasi_pagu_rujukan[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('E'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('F'.$b, number_format($realisasi_pagu_farmasi[$index]));
					$this->excel->getActiveSheet()->setCellValue('G'.$b, number_format($realisasi_daerah_farmasi[$index]));
					$this->excel->getActiveSheet()->setCellValue('H'.$b, round($rata_farmasi[$index],2));							
					if( $realisasi_daerah_farmasi[$index]!=0 && $realisasi_pagu_farmasi[$index]!=0){
						$status=$realisasi_daerah_farmasi[$index]/$realisasi_pagu_farmasi[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('I'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('J'.$b, number_format($realisasi_pagu_dasar[$index]));
					$this->excel->getActiveSheet()->setCellValue('K'.$b, number_format($realisasi_daerah_dasar[$index]));
					$this->excel->getActiveSheet()->setCellValue('L'.$b, round($rata_dasar[$index],2));							
					if( $realisasi_daerah_dasar[$index]!=0 && $realisasi_pagu_dasar[$index]!=0){
						$status=$realisasi_daerah_dasar[$index]/$realisasi_pagu_dasar[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('M'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('N'.$b, number_format($realisasi_pagu_sarpras[$index]));
					$this->excel->getActiveSheet()->setCellValue('O'.$b, number_format($realisasi_daerah_sarpras[$index]));
					$this->excel->getActiveSheet()->setCellValue('P'.$b, round($rata_sarpras[$index]));							
					if( $realisasi_daerah_sarpras[$index]!=0 && $realisasi_pagu_sarpras[$index]!=0){
						$status=$realisasi_daerah_sarpras[$index]/$realisasi_pagu_sarpras[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('Q'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->setCellValue('R'.$b, number_format($realisasi_pagu_total[$index]));
					$this->excel->getActiveSheet()->setCellValue('S'.$b, number_format($realisasi_daerah_total[$index]));
					$this->excel->getActiveSheet()->setCellValue('T'.$b,round( $rata_seluruh[$index],2));	
					if( $realisasi_daerah_total[$index]!=0 && $realisasi_pagu_total[$index]!=0){
						$status=$realisasi_daerah_total[$index]/$realisasi_pagu_total[$index]*100;
						$this->excel->getActiveSheet()->setCellValue('U'.$b, round($status, 2));
					}
					$this->excel->getActiveSheet()->getStyle("B".$b.":U".$b)->applyFromArray($style);
			
			$b++;
		}
		//buat total

		$this->excel->getActiveSheet()->setCellValue('A'.$b,'TOTAL');
		$this->excel->getActiveSheet()->setCellValue('B'.$b, number_format(array_sum($realisasi_pagu_rujukan)));
		$this->excel->getActiveSheet()->setCellValue('C'.$b, number_format(array_sum($realisasi_daerah_rujukan)));
		$this->excel->getActiveSheet()->setCellValue('D'.$b, round(array_sum($rata_rujukan)/ count($rata_rujukan),2) );
		if( array_sum($realisasi_daerah_rujukan)!=0 && array_sum($realisasi_pagu_rujukan)!=0){
			$status=array_sum($realisasi_daerah_rujukan)/array_sum($realisasi_pagu_rujukan)*100;
			$this->excel->getActiveSheet()->setCellValue('E'.$b, round($status, 2));
		}
		$this->excel->getActiveSheet()->setCellValue('F'.$b, number_format(array_sum($realisasi_pagu_farmasi)));
		$this->excel->getActiveSheet()->setCellValue('G'.$b, number_format(array_sum($realisasi_daerah_farmasi)));
		$this->excel->getActiveSheet()->setCellValue('H'.$b, round(array_sum($rata_farmasi)/ count($rata_farmasi),2)  );							
		if( array_sum($realisasi_daerah_farmasi)!=0 && array_sum($realisasi_pagu_farmasi)!=0){
			$status=array_sum($realisasi_daerah_farmasi)/array_sum($realisasi_pagu_farmasi)*100;
			$this->excel->getActiveSheet()->setCellValue('I'.$b, round($status, 2));
		}
		$this->excel->getActiveSheet()->setCellValue('J'.$b, number_format(array_sum($realisasi_pagu_dasar)));
		$this->excel->getActiveSheet()->setCellValue('K'.$b, number_format(array_sum($realisasi_daerah_dasar)));
		$this->excel->getActiveSheet()->setCellValue('L'.$b, round(array_sum($rata_dasar)/ count($rata_dasar),2)  );							
		if( array_sum($realisasi_daerah_dasar)!=0 && array_sum($realisasi_pagu_dasar)!=0){
			$status=array_sum($realisasi_daerah_dasar)/array_sum($realisasi_pagu_dasar)*100;
			$this->excel->getActiveSheet()->setCellValue('M'.$b, round($status, 2));
		}
		$this->excel->getActiveSheet()->setCellValue('N'.$b, number_format(array_sum($realisasi_pagu_sarpras)));
		$this->excel->getActiveSheet()->setCellValue('O'.$b, number_format(array_sum($realisasi_daerah_sarpras)));
		$this->excel->getActiveSheet()->setCellValue('P'.$b, round(array_sum($rata_sarpras)/ count($rata_sarpras),2) );							
		if( array_sum($realisasi_daerah_sarpras)!=0 && array_sum($realisasi_pagu_sarpras)!=0){
			$status=array_sum($realisasi_daerah_sarpras)/array_sum($realisasi_pagu_sarpras)*100;
			$this->excel->getActiveSheet()->setCellValue('Q'.$b, round($status, 2));
		}
		$this->excel->getActiveSheet()->setCellValue('R'.$b, number_format(array_sum($realisasi_pagu_total)));
		$this->excel->getActiveSheet()->setCellValue('S'.$b, number_format(array_sum($realisasi_daerah_total)));
		$this->excel->getActiveSheet()->setCellValue('T'.$b, round(array_sum($rata_seluruh)/ count($rata_seluruh),2) );	
		if( array_sum($realisasi_daerah_total)!=0 && array_sum($realisasi_pagu_total)!=0){
			$status=array_sum($realisasi_daerah_total)/array_sum($realisasi_pagu_total)*100;
			$this->excel->getActiveSheet()->setCellValue('U'.$b, round($status, 2));
		}
		$this->excel->getActiveSheet()->getStyle("B".$b.":U".$b)->applyFromArray($style);
		$filename='rekap_pagu.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function rekap_indonesia_nf(){
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$s=4;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$kabupaten=$k;
		$provinsi=$p;
		$provinsi2=$this->pm->get_provinsi()->result();
		foreach($this->pm->get_provinsi()->result() as $index => $rowz){
			$l=''; $l_bok='';
			$l_bok_total='';
			$l_total='';
			$l_akreditasi_rs='';
			$l_akreditasi_rs_total='';
			$l_akreditasi_puskesmas='';
			$l_akreditasi_puskesmas_total='';
			$laporan_nf=0;
			foreach($this->pm->dak_laporan_nf_indo(0,$t,$rowz->KodeProvinsi,$s)->result() as $rw){
				$laporan_nf.=$rw->ID_LAPORAN_DAK.',';
			}
			$laporan_nf=substr_replace($laporan_nf, '', -1);
			if($this->pm->sum_kabupaten_nf2($laporan_nf,1)->num_rows !=0 ){
				$realisasi_daerah_bok[]=$this->pm->sum_kabupaten_nf2($laporan_nf,1)->row()->pelaksanaan;
				$fisik_bok[]=$this->pm->sum_kabupaten_nf2($laporan_nf,1)->row()->rata;
				$realisasi_daerah_jampersal[]=$this->pm->sum_kabupaten_nf2($laporan_nf,2)->row()->pelaksanaan;
				$fisik_jampersal[]=$this->pm->sum_kabupaten_nf2($laporan_nf,2)->row()->rata;
				$realisasi_daerah_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf2($laporan_nf,4)->row()->pelaksanaan;
				$fisik_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf2($laporan_nf,4)->row()->rata;
				$realisasi_daerah_akreditasi_rs[]=$this->pm->sum_kabupaten_nf2($laporan_nf,3)->row()->pelaksanaan;
				$fisik_akreditasi_rs[]=$this->pm->sum_kabupaten_nf2($laporan_nf,3)->row()->rata;
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_nf2($laporan_nf,0)->row()->pelaksanaan;
				$fisik_total[]=$this->pm->sum_kabupaten_nf2($laporan_nf,0)->row()->rata;
				$realisasi_daerah_lainnya[]=$this->pm->sum_kabupaten_nf2($laporan_nf,5)->row()->pelaksanaan;
			}else {
				$realisasi_daerah_bok[]=0;
				$fisik_bok=0;
				$realisasi_daerah_jampersal[]=0;
				$fisik_jampersal=0;
				$realisasi_daerah_akreditasi_puskesmas[]=0;
				$fisik_akreditasi_puskesmas=0;
				$realisasi_daerah_akreditasi_rs[]=0;
				$fisik_akreditasi_rs=0;
				$fisik_total=0;
				$realisasi_daerah_lainnya[]=0;
			}

				if($this->pm->sum4('data_pagu_nf','BANTUAN_OPERASIONAL_KESEHATAN', 'AKREDITASI_RUMAH_SAKIT','AKREDITASI_PUSKESMAS','JAMINAN_PERSALINAN','KodeProvinsi',$rowz->KodeProvinsi)->num_rows !=0 ){
					$pagu=$this->pm->sum4('data_pagu_nf','BANTUAN_OPERASIONAL_KESEHATAN', 'AKREDITASI_RUMAH_SAKIT','AKREDITASI_PUSKESMAS','JAMINAN_PERSALINAN','KodeProvinsi',$rowz->KodeProvinsi);
					$realisasi_pagu_bok[]=$pagu->row()->BANTUAN_OPERASIONAL_KESEHATAN;
					$realisasi_pagu_akreditasi_rs[]=$pagu->row()->AKREDITASI_RUMAH_SAKIT;
					$realisasi_pagu_akreditasi_puskesmas[]=$pagu->row()->AKREDITASI_PUSKESMAS;
					$realisasi_pagu_jampersal[]=$pagu->row()->JAMINAN_PERSALINAN;
					$realisasi_pagu_total[]=$realisasi_pagu_jampersal[$index] + $realisasi_pagu_bok[$index] + $realisasi_pagu_akreditasi_rs[$index] +  $realisasi_pagu_akreditasi_puskesmas[$index];
				} else {
					$realisasi_pagu_bok[] = 0;
					$realisasi_pagu_akreditasi_rs[] = 0;
					$realisasi_pagu_akreditasi_puskesmas[] = 0;
					$realisasi_pagu_jampersal[] = 0;
					$realisasi_pagu_total[] = 0;
				}	
		}
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Pagu Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Realisasi Pagu Non Fisik');
		foreach (range('A', 'U') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A3', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A3:A4');
		$this->excel->getActiveSheet()->setCellValue('B3', 'BOK');
		$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B3:E3');
		$this->excel->getActiveSheet()->setCellValue('F3', 'Jaminan persalinan');
		$this->excel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('F3:I3');
		$this->excel->getActiveSheet()->setCellValue('I3', 'Akreditasi Puskesmas');
		$this->excel->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('J3:M3');
		$this->excel->getActiveSheet()->setCellValue('N3', 'Akreditasi Rumah Sakit');
		$this->excel->getActiveSheet()->getStyle('N3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N3:Q3');
		$this->excel->getActiveSheet()->setCellValue('R3', 'total');
		$this->excel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('R3:U3');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D4', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('E4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H4', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('I4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('J4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('K4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('L4', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('M4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('N4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('O4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('P4', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('Q4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('Q4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('R4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('R4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('S4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('S4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('T4', 'Fisik');
		$this->excel->getActiveSheet()->getStyle('T4')->getFont()->setBold(true);	
		$this->excel->getActiveSheet()->setCellValue('U4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('U4')->getFont()->setBold(true);		
		$i=5;
		$b=5;
		foreach($provinsi2 as $index => $row){
			
				$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaProvinsi);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, $realisasi_pagu_bok[$index]);
				$this->excel->getActiveSheet()->setCellValue('C'.$i, $realisasi_daerah_bok[$index]);
				$this->excel->getActiveSheet()->setCellValue('D'.$i, round($fisik_bok[$index],2));
				if( $realisasi_daerah_bok[$index]!=0 && $realisasi_pagu_bok[$index]!=0){
					$status=$realisasi_daerah_bok[$index]/$realisasi_pagu_bok[$index]*100;
					$this->excel->getActiveSheet()->setCellValue('E'.$i, round($status, 2));
				}
				$this->excel->getActiveSheet()->setCellValue('F'.$i, $realisasi_pagu_jampersal[$index]);
				$this->excel->getActiveSheet()->setCellValue('G'.$i, $realisasi_daerah_jampersal[$index]);
				$this->excel->getActiveSheet()->setCellValue('H'.$i, round($fisik_jampersal[$index]));
				if( $realisasi_daerah_jampersal[$index]!=0 && $realisasi_pagu_jampersal[$index]!=0){
					$status=$realisasi_daerah_jampersal[$index]/$realisasi_pagu_jampersal[$index]*100;
					$this->excel->getActiveSheet()->setCellValue('I'.$i, round($status, 2));
				}
				$this->excel->getActiveSheet()->setCellValue('J'.$i, $realisasi_pagu_akreditasi_puskesmas[$index]);
				$this->excel->getActiveSheet()->setCellValue('K'.$i, $realisasi_daerah_akreditasi_puskesmas[$index]);
				$this->excel->getActiveSheet()->setCellValue('L'.$i, round($fisik_akreditasi_puskesmas[$index],2));
				if( $realisasi_daerah_akreditasi_puskesmas[$index]!=0 && $realisasi_pagu_akreditasi_puskesmas[$index]!=0){
					$status=$realisasi_daerah_akreditasi_puskesmas[$index]/$realisasi_pagu_akreditasi_puskesmas[$index]*100;
					$this->excel->getActiveSheet()->setCellValue('M'.$i, round($status, 2));
				}
				$this->excel->getActiveSheet()->setCellValue('N'.$i, $realisasi_pagu_akreditasi_rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('O'.$i, $realisasi_daerah_akreditasi_rs[$index]);
				$this->excel->getActiveSheet()->setCellValue('P'.$i, round($fisik_akreditasi_rs[$index],2));
				if( $realisasi_daerah_akreditasi_rs[$index]!=0 && $realisasi_pagu_akreditasi_rs[$index]!=0){
					$status=$realisasi_daerah_akreditasi_rs[$index]/$realisasi_pagu_akreditasi_rs[$index]*100;
					$this->excel->getActiveSheet()->setCellValue('Q'.$i, round($status, 2));
				}
				$this->excel->getActiveSheet()->setCellValue('R'.$i, $realisasi_pagu_total[$index]);
				$this->excel->getActiveSheet()->setCellValue('S'.$i, $realisasi_daerah_total[$index]);
				$this->excel->getActiveSheet()->setCellValue('T'.$i, round($fisik_total[$index],2 ));
				if( $realisasi_daerah_total[$index]!=0 && $realisasi_pagu_total[$index]!=0){
					$status=$realisasi_daerah_total[$index]/$realisasi_pagu_total[$index]*100;
					$this->excel->getActiveSheet()->setCellValue('U'.$i, round($status, 2));
				}
				$i++;
			
		}       
	        $fbok=array_sum($fisik_bok)/count($fisik_bok);
	        $fjampersal=array_sum($fisik_jampersal)/count($fisik_jampersal);
	        $fakreditasi_puskesmas=array_sum($fisik_jampersal)/count($fisik_akreditasi_puskesmas);
	        $fakreditasi_rs=array_sum($fisik_akreditasi_rs)/count($fisik_akreditasi_rs);
	        $ftotal=(array_sum($fisik_akreditasi_puskesmas) + array_sum($fisik_bok) +  array_sum($fisik_akreditasi_rs) + array_sum($fisik_jampersal))/(count($fisik_akreditasi_puskesmas) + count($fisik_bok) +  count($fisik_akreditasi_rs) + count($fisik_jampersal));
			$this->excel->getActiveSheet()->setCellValue('A'.$i, 'Total');
			$this->excel->getActiveSheet()->setCellValue('B'.$i, array_sum($realisasi_pagu_bok));
			$this->excel->getActiveSheet()->setCellValue('C'.$i, array_sum($realisasi_daerah_bok));
			$this->excel->getActiveSheet()->setCellValue('D'.$i, round($fbok,2));
			if( array_sum($realisasi_daerah_bok)!=0 && array_sum($realisasi_pagu_bok)!=0){
				$status=array_sum($realisasi_daerah_bok)/array_sum($realisasi_pagu_bok)*100;
				$this->excel->getActiveSheet()->setCellValue('E'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('F'.$i, array_sum($realisasi_pagu_jampersal));
			$this->excel->getActiveSheet()->setCellValue('G'.$i, array_sum($realisasi_daerah_jampersal));
			$this->excel->getActiveSheet()->setCellValue('H'.$i, round($fjampersal,2));			
			if( array_sum($realisasi_daerah_jampersal)!=0 && array_sum($realisasi_pagu_jampersal)!=0){
				$status=array_sum($realisasi_daerah_jampersal)/array_sum($realisasi_pagu_jampersal)*100;
				$this->excel->getActiveSheet()->setCellValue('I'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('J'.$i, array_sum($realisasi_pagu_akreditasi_puskesmas));
			$this->excel->getActiveSheet()->setCellValue('K'.$i, array_sum($realisasi_daerah_akreditasi_puskesmas));
			$this->excel->getActiveSheet()->setCellValue('L'.$i, round($fakreditasi_puskesmas,2));	
			if( array_sum($realisasi_daerah_akreditasi_puskesmas)!=0 && array_sum($realisasi_pagu_akreditasi_puskesmas)!=0){
				$status=array_sum($realisasi_daerah_akreditasi_puskesmas)/array_sum($realisasi_pagu_akreditasi_puskesmas)*100;
				$this->excel->getActiveSheet()->setCellValue('M'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('N'.$i, array_sum($realisasi_pagu_akreditasi_rs));
			$this->excel->getActiveSheet()->setCellValue('O'.$i, array_sum($realisasi_daerah_akreditasi_rs));
			$this->excel->getActiveSheet()->setCellValue('P'.$i, round($fakreditasi_rs,2));	
			if( array_sum($realisasi_daerah_akreditasi_rs)!=0 && array_sum($realisasi_pagu_akreditasi_rs)!=0){
				$status=array_sum($realisasi_daerah_akreditasi_rs)/array_sum($realisasi_pagu_akreditasi_rs)*100;
				$this->excel->getActiveSheet()->setCellValue('Q'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('R'.$i, array_sum($realisasi_pagu_total));
			$this->excel->getActiveSheet()->setCellValue('S'.$i, array_sum($realisasi_daerah_total));
			$this->excel->getActiveSheet()->setCellValue('T'.$i, round($ftotal,2));	
			if( array_sum($realisasi_daerah_total)!=0 && array_sum($realisasi_pagu_total)!=0){
				$status=array_sum($realisasi_daerah_total)/array_sum($realisasi_pagu_total)*100;
				$this->excel->getActiveSheet()->setCellValue('U'.$i, round($status, 2));
			}
		$filename='rekap_pagu_nf.xls'; //save our workbook as this file name
		ob_end_clean();	
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function total_pagu_nf($p,$K,$t,$s){

		$kabupaten=0;
		$provinsi=0;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten($p)->result();//count number of recordss
		$j_bok=0;
		$j_jampersal=0;
		$j_ap= 0;
		$j_ars=0;
		foreach($this->pm->get_data_kabupaten($p)->result() as $index => $rowz){
			$l=''; 
			$l_bok='';
			$l_bok_total='';
			$l_total='';
			$l_akreditasi_rs='';
			$l_akreditasi_rs_total='';
			$l_akreditasi_puskesmas='';
			$l_akreditasi_puskesmas_total='';
			$laporan_nf=0;
			$realisasi_daerah_bok[]=0;
			$realisasi_fisik_bok[]=0;
	        $perencanaan_daerah_bok[]=0;
	        $perencanaan_daerah_jampersal[]=0;
			$realisasi_daerah_jampersal[]=0;
			$realisasi_fisik_jampersal[]=0;
			$realisasi_daerah_akreditasi_puskesmas[]=0;
			$realisasi_fisik_akreditasi_puskesmas[]=0;			
			$realisasi_daerah_akreditasi_rs[]=0;
			$realisasi_fisik_akreditasi_rs[]=0;		
			$realisasi_daerah_total[]=0;
			$realisasi_daerah_lainnya[]=0;
			$realisasi_fisik_total[]=0;
			$perencanaan_daerah_bok[]=0;
			$perencanaan_daerah_jampersal[]=0;
			$perencanaan_akreditasi_puskesmas[]=0;
			$perencanaan_akreditasi_rs[]=0;

			foreach($this->pm->dak_laporan_nf2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,$s)->result() as $rw){
				$laporan_nf=$rw->ID_LAPORAN_DAK;
			}
			if($this->pm->sum_kabupaten_nf($laporan_nf,0)->num_rows !=0 ){

				$realisasi_daerah_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->pelaksanaan;
				$realisasi_fisik_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->REALISASI_FISIK_PELAKSANAAN;
				$perencanaan_daerah_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->pelaksanaan2;
				if($this->pm->sum_kabupaten_nf($laporan_nf,1)->num_rows() >0){
					$j_bok++;
				}

				$realisasi_daerah_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->pelaksanaan;
				$realisasi_fisik_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->REALISASI_FISIK_PELAKSANAAN;
				$perencanaan_daerah_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->pelaksanaan2;			
				if($this->pm->sum_kabupaten_nf($laporan_nf,2)->num_rows() >0){
					$j_jampersal++;
				}

				$realisasi_daerah_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->pelaksanaan;
				$realisasi_fisik_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->REALISASI_FISIK_PELAKSANAAN;	
				$perencanaan_daerah_jam[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->pelaksanaan2;	
				if($this->pm->sum_kabupaten_nf($laporan_nf,4)->num_rows() >0){
					$j_ap++;
				}					

				$realisasi_daerah_akreditasi_rs[]=$this->pm->sum_kabupaten_nf($laporan_nf,3)->row()->pelaksanaan;
				$realisasi_fisik_akreditasi_rs[]=$this->pm->sum_kabupaten_nf($laporan_nf,3)->row()->REALISASI_FISIK_PELAKSANAAN;
				if($this->pm->sum_kabupaten_nf($laporan_nf,1)->num_rows() >0){
					$j_ars++;
				}		

				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_nf($laporan_nf,0)->row()->pelaksanaan;
				$realisasi_daerah_lainnya[]=$this->pm->sum_kabupaten_nf($laporan_nf,5)->row()->pelaksanaan;
				$realisasi_fisik_total[]=($realisasi_fisik_bok[$index]+$realisasi_fisik_jampersal[$index]+$realisasi_fisik_jampersal[$index]+$realisasi_fisik_akreditasi_rs[$index]+$realisasi_fisik_akreditasi_puskesmas[$index])/4;
			
			} else {
				$realisasi_daerah_bok[]=0;
				$realisasi_fisik_bok[]=0;
				$realisasi_daerah_jampersal[]=0;
				$realisasi_fisik_jampersal[]=0;
				$realisasi_daerah_akreditasi_puskesmas[]=0;
				$realisasi_fisik_akreditasi_puskesmas[]=0;			
				$realisasi_daerah_akreditasi_rs[]=0;
				$realisasi_fisik_akreditasi_rs[]=0;		
				$realisasi_daerah_total[]=0;
				$realisasi_daerah_lainnya[]=0;
				$realisasi_fisik_total[]=0;
			}
		/*
		$nama_kabupaten=$rowz->NamaKabupaten;
		if ( (strpos($nama_kabupaten, 'KAB') !== false) ||  (strpos($nama_kabupaten, 'KOTA') !== false)) {
		
		$nama_kabupaten=$rowz->NamaKabupaten;
		}
		else
		{$nama_kabupaten='Pr[ovinsi '.$nama_kabupaten;}*/
		if($this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
			$pagu=$this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
			$realisasi_pagu_bok[]=$pagu->row()->BANTUAN_OPERASIONAL_KESEHATAN;
			$realisasi_pagu_akreditasi_rs[]=$pagu->row()->AKREDITASI_RUMAH_SAKIT;
			$realisasi_pagu_akreditasi_puskesmas[]=$pagu->row()->AKREDITASI_PUSKESMAS;
			$realisasi_pagu_jampersal[]=$pagu->row()->JAMINAN_PERSALINAN;
			$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan_nf($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
		} else {
			$realisasi_pagu_bok[] = 0;
			$realisasi_pagu_akreditasi_rs[] = 0;
			$realisasi_pagu_akreditasi_puskesmas[] = 0;
			$realisasi_pagu_jampersal[] = 0;
			$realisasi_pagu_total[] = 0;
		}
		//the following var_dump is only showing the last record.
		//need to show all rows (which should be 2)
		//var_dump($data); exit;




		}
       	$total_fisik_bok=0;
        if($j_bok>0){
       	 $total_fisik_bok=array_sum($realisasi_fisik_bok)/$j_bok;
        }
       $total_fisik_akreditasi_puskesmas=0;
       if($j_ap>0){
       	 $total_fisik_akreditasi_puskesmas=array_sum($realisasi_fisik_akreditasi_puskesmas)/$j_ap;
        }       
       $total_fisik_akreditasi_rs=0;
       if($j_ars>0){
       	 $total_fisik_akreditasi_rs=array_sum($realisasi_fisik_akreditasi_puskesmas)/$j_ars;
        } 
       $total_fisik_jampersal=0;
       if($j_jampersal>0){
       	 $total_fisik_jampersal=array_sum($realisasi_fisik_jampersal)/$j_jampersal;
        }        
		$total['total_pagu_bok']=array_sum($realisasi_pagu_bok);
		$total['total_pagu_akreditasi_rs']=array_sum($realisasi_pagu_akreditasi_rs);
		$total['total_pagu_akreditasi_puskesmas']=array_sum($realisasi_pagu_akreditasi_puskesmas);
		$total['total_pagu_jampersal']=array_sum($realisasi_pagu_jampersal);
		$total['total_pagu_total']=array_sum($realisasi_pagu_total);
		$total['total_daerah_bok']=array_sum($realisasi_daerah_bok);
		$total['total_daerah_akreditasi_rs']=array_sum($realisasi_daerah_akreditasi_rs);
		$total['total_daerah_akreditasi_puskesmas']=array_sum($realisasi_daerah_akreditasi_puskesmas);
		$total['total_daerah_jampersal']=array_sum($realisasi_daerah_jampersal);
		$total['total_daerah_total']=array_sum($realisasi_daerah_total);
		$total['total_fisik_bok']=$total_fisik_bok;
		$total['total_fisik_jampersal']=$total_fisik_jampersal;
		$total['total_fisik_akreditasi_puskesmas']=$total_fisik_akreditasi_puskesmas;	
		$total['total_fisik_akreditasi_rs']=$total_fisik_akreditasi_rs;	
		$total['total_fisik_total']=$total_fisik_akreditasi_rs+$total_fisik_akreditasi_puskesmas + $total_fisik_jampersal + $total_fisik_bok;						
		return $total;
	}


	function table_pagu_nf()
	{
		$num_rec_per_page=10;
		if (isset($_GET["page"])) 
		{ 
		$page  = $_GET["page"];
		$currentpage = (int) $_GET['page'];
		$prevpage = $currentpage - 1;
		$nextpage = $currentpage + 1;
		} else { $page=1; $prevpage=1; $currentpage =1; $nextpage = 2;};
		$start_from = ($page-1) * $num_rec_per_page;
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$s=4;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$kabupaten=$k;
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten2_limit($p,$num_rec_per_page, $start_from,$k)->result();
		$total_records = $this->pm->get_data_kabupaten($p)->num_rows();  //count number of records
		$total_pages = ceil($total_records / $num_rec_per_page);
		foreach($this->pm->get_data_kabupaten2_limit($p,$num_rec_per_page, $start_from,$k)->result() as $index => $rowz){
				$l=''; $l_bok='';
				$l_bok_total='';
				$l_total='';
				$l_akreditasi_rs='';
				$l_akreditasi_rs_total='';
				$l_akreditasi_puskesmas='';
				$l_akreditasi_puskesmas_total='';
				$laporan_nf=0;
			foreach($this->pm->dak_laporan_nf2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,$s)->result() as $rw){
				$laporan_nf=$rw->ID_LAPORAN_DAK;
			}
			
			if($this->pm->sum_kabupaten_nf($laporan_nf,0)->num_rows !=0 ){
				$realisasi_daerah_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->pelaksanaan;
				$realisasi_fisik_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->REALISASI_FISIK_PELAKSANAAN;

				$realisasi_daerah_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->pelaksanaan;
				$realisasi_fisik_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->REALISASI_FISIK_PELAKSANAAN;

				$realisasi_daerah_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->pelaksanaan;
				$realisasi_fisik_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->REALISASI_FISIK_PELAKSANAAN;			
				$realisasi_daerah_akreditasi_rs[]=$this->pm->sum_kabupaten_nf($laporan_nf,3)->row()->pelaksanaan;
				$realisasi_fisik_akreditasi_rs[]=$this->pm->sum_kabupaten_nf($laporan_nf,3)->row()->REALISASI_FISIK_PELAKSANAAN;	
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_nf($laporan_nf,0)->row()->pelaksanaan;
				$realisasi_daerah_lainnya[]=$this->pm->sum_kabupaten_nf($laporan_nf,5)->row()->pelaksanaan;
				$realisasi_fisik_total[]=($realisasi_fisik_bok[$index]+$realisasi_fisik_jampersal[$index]+$realisasi_fisik_jampersal[$index]+$realisasi_fisik_akreditasi_rs[$index]+$realisasi_fisik_akreditasi_puskesmas[$index])/4;
			} else {
				$realisasi_daerah_bok[]=0;
				$realisasi_fisik_bok[]=0;
				$realisasi_daerah_jampersal[]=0;
				$realisasi_fisik_jampersal[]=0;
				$realisasi_daerah_akreditasi_puskesmas[]=0;
				$realisasi_fisik_akreditasi_puskesmas[]=0;			
				$realisasi_daerah_akreditasi_rs[]=0;
				$realisasi_fisik_akreditasi_rs[]=0;		
				$realisasi_daerah_total[]=0;
				$realisasi_daerah_lainnya[]=0;
				$realisasi_fisik_total[]=0;
			}
			if($this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
				$pagu=$this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
				$realisasi_pagu_bok[]=$pagu->row()->BANTUAN_OPERASIONAL_KESEHATAN;
				$realisasi_pagu_akreditasi_rs[]=$pagu->row()->AKREDITASI_RUMAH_SAKIT;
				$realisasi_pagu_akreditasi_puskesmas[]=$pagu->row()->AKREDITASI_PUSKESMAS;
				$realisasi_pagu_jampersal[]=$pagu->row()->JAMINAN_PERSALINAN;
				$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan_nf($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
			} else {
				$realisasi_pagu_bok[] = 0;
				$realisasi_pagu_akreditasi_rs[] = 0;
				$realisasi_pagu_akreditasi_puskesmas[] = 0;
				$realisasi_pagu_jampersal[] = 0;
				$realisasi_pagu_total[] = 0;
			}


		}
		$button='<a class="btn btn-default" href="'.base_url().'index.php/e-monev/e_dak/rekap_pagu_nf?p='.$p.'&t='.$t.'&k='.$k.'&s='.$s.'" >
		<img src="'.base_url().'images/main/excel.png" > Print excel</a>
		';

		$total= $this->total_pagu_nf($p,$k,$t,$s);
		$data['k']=$k;
		$data['button']=$button;
		$data['kabupaten']=$kabupaten;
		$data['nama_daerah']=$nama;
		$data['total_pages']=$total_pages;
		$data['realisasi_daerah_jampersal']=$realisasi_daerah_jampersal;
		$data['total_daerah_jampersal']=$total['total_daerah_jampersal'];
		$data['realisasi_fisik_jampersal']=$realisasi_fisik_jampersal;	
		$data['realisasi_daerah_akreditasi_rs']=$realisasi_daerah_akreditasi_rs;
		$data['total_daerah_akreditasi_rs']=$total['total_daerah_akreditasi_rs'];	
		$data['realisasi_fisik_akreditasi_rs']=$realisasi_fisik_akreditasi_rs;	
		$data['realisasi_daerah_akreditasi_puskesmas']=$realisasi_daerah_akreditasi_puskesmas;
		$data['total_daerah_akreditasi_puskesmas']=$total['total_daerah_akreditasi_puskesmas'];	
		$data['realisasi_fisik_akreditasi_puskesmas']=$realisasi_fisik_akreditasi_puskesmas;
		$data['realisasi_daerah_bok']=$realisasi_daerah_bok;
		$data['total_daerah_bok']=$total['total_daerah_bok'];		
		$data['realisasi_fisik_bok']=$realisasi_fisik_bok;	
		$data['realisasi_daerah_total']=$realisasi_daerah_total;
		$data['total_daerah_total']=$total['total_daerah_total'];		
		$data['realisasi_fisik_total']=$realisasi_fisik_total;	
		$data['realisasi_daerah_lainnya']=$realisasi_daerah_lainnya;
		$data['realisasi_pagu_akreditasi_puskesmas']=$realisasi_pagu_akreditasi_puskesmas;
		$data['realisasi_pagu_bok']=$realisasi_pagu_bok;
		$data['realisasi_pagu_akreditasi_rs']=$realisasi_pagu_akreditasi_rs;
		$data['realisasi_pagu_jampersal']=$realisasi_pagu_jampersal;
		$data['realisasi_pagu_total']=$realisasi_pagu_total;
		$data['total_pagu_akreditasi_puskesmas']=$total['total_pagu_akreditasi_puskesmas'];	
		$data['total_pagu_akreditasi_rs']=$total['total_pagu_akreditasi_rs'];		
		$data['total_pagu_bok']=$total['total_pagu_bok'];	
		$data['total_pagu_jampersal']=$total['total_pagu_jampersal'];					
		$data['total_pagu_total']=$total['total_pagu_total'];	
		$data['total_fisik_akreditasi_puskesmas']=$total['total_fisik_akreditasi_puskesmas'];	
		$data['total_fisik_akreditasi_rs']=$total['total_fisik_akreditasi_rs'];		
		$data['total_fisik_bok']=$total['total_fisik_bok'];	
		$data['total_fisik_jampersal']=$total['total_fisik_jampersal'];					
		$data['total_fisik_total']=$total['total_fisik_total'];	
		$data['t']=$t;
		$data['total_pages']=$total_pages;
		$data['currentpage']=$currentpage;
		$data['nextpage']=$nextpage;
		$data['prevpage']=$prevpage;
		$this->load->view('tabel_pagu_nf',$data);
	}


	function rekap_pagu_nf(){
		$kabupaten=0;
		$t=1;
		$provinsi=0;
		$k=0;
		$p=0;
		$realisasi_daerah=array();
		$realisasi_pagu=array();
		$nama='';
		$persentase=array();
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["t"]))$t=$_GET["t"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		$kabupaten=$k;
		$provinsi=$p;
		$kabupaten=$this->pm->get_data_kabupaten2($p,$k)->result();
		foreach($this->pm->get_data_kabupaten2($p,$k)->result() as $rowz){
			$l=''; $l_bok='';
			$l_bok_total='';
			$l_total='';
			$l_akreditasi_rs='';
			$l_akreditasi_rs_total='';
			$l_akreditasi_puskesmas='';
			$l_akreditasi_puskesmas_total='';
			$laporan_nf=0;
			foreach($this->pm->dak_laporan_nf2($rowz->KodeKabupaten,$t,$rowz->KodeProvinsi,$s)->result() as $rw){
				$laporan_nf=$rw->ID_LAPORAN_DAK;
			}
			if($this->pm->sum_kabupaten_nf($laporan_nf,0)->num_rows !=0 ){
				$realisasi_daerah_bok[]=$this->pm->sum_kabupaten_nf($laporan_nf,1)->row()->pelaksanaan;
				$realisasi_daerah_jampersal[]=$this->pm->sum_kabupaten_nf($laporan_nf,2)->row()->pelaksanaan;
				$realisasi_daerah_akreditasi_puskesmas[]=$this->pm->sum_kabupaten_nf($laporan_nf,4)->row()->pelaksanaan;
				$realisasi_daerah_akreditasi_rs[]=$this->pm->sum_kabupaten_nf($laporan_nf,3)->row()->pelaksanaan;
				$realisasi_daerah_total[]=$this->pm->sum_kabupaten_nf($laporan_nf,0)->row()->pelaksanaan;
				$realisasi_daerah_lainnya[]=$this->pm->sum_kabupaten_nf($laporan_nf,5)->row()->pelaksanaan;
			}else {
				$realisasi_daerah_bok[]=0;
				$realisasi_daerah_jampersal[]=0;
				$realisasi_daerah_akreditasi_puskesmas[]=0;
				$realisasi_daerah_akreditasi_rs[]=0;
				$realisasi_daerah_lainnya[]=0;
			}
	/*$nama_kabupaten=$rowz->NamaKabupaten;
	if ( (strpos($nama_kabupaten, 'KAB') !== false) ||  (strpos($nama_kabupaten, 'KOTA') !== false)) {
	
	$nama_kabupaten=$rowz->NamaKabupaten;
	}
	else
	{$nama_kabupaten='Provinsi '.$nama_kabupaten;}*/
			if($this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi')->num_rows !=0 ){
				$pagu=$this->pm->get_where_double('data_pagu_nf',$rowz->KodeKabupaten,'KodeKabupaten',$rowz->KodeProvinsi,'KodeProvinsi');
				$realisasi_pagu_bok[]=$pagu->row()->BANTUAN_OPERASIONAL_KESEHATAN;
				$realisasi_pagu_akreditasi_rs[]=$pagu->row()->AKREDITASI_RUMAH_SAKIT;
				$realisasi_pagu_akreditasi_puskesmas[]=$pagu->row()->AKREDITASI_PUSKESMAS;
				$realisasi_pagu_jampersal[]=$pagu->row()->JAMINAN_PERSALINAN;
				$realisasi_pagu_total[]=$this->pm->get_pagu_keseluruhan_nf($rowz->KodeKabupaten,$rowz->KodeProvinsi)->row()->total;
			} else {
				$realisasi_pagu_bok[] = "-";
				$realisasi_pagu_akreditasi_rs[] = "-";
				$realisasi_pagu_akreditasi_puskesmas[] = "-";
				$realisasi_pagu_jampersal[] = "-";
				$realisasi_pagu_total[] = "-";
			}
		}
		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Pagu Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Realisasi Pagu Non Fisik');
		foreach (range('A', 'U') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(20);
		}
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:D1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A3', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A3:A4');
		$this->excel->getActiveSheet()->setCellValue('B3', 'BOK');
		$this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B3:D3');
		$this->excel->getActiveSheet()->setCellValue('E3', 'Jaminan persalinan');
		$this->excel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('E3:G3');
		$this->excel->getActiveSheet()->setCellValue('H3', 'Akreditasi Puskesmas');
		$this->excel->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('H3:J3');
		$this->excel->getActiveSheet()->setCellValue('K3', 'Akreditasi Puskesmas');
		$this->excel->getActiveSheet()->getStyle('K3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('K3:M3');
		$this->excel->getActiveSheet()->setCellValue('N3', 'total');
		$this->excel->getActiveSheet()->getStyle('N3')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('N3:P3');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('C4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('D4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('E4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('F4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('G4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('G4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('H4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('I4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('I4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('J4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('J4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('K4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('K4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('L4', 'Realisasi Triwulan');
		$this->excel->getActiveSheet()->getStyle('L4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('M4', 'Presentase Triwulan');
		$this->excel->getActiveSheet()->getStyle('M4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('N4', 'Pagu Total');
		$this->excel->getActiveSheet()->getStyle('N4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('O4', 'Realisasi Total');
		$this->excel->getActiveSheet()->getStyle('O4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('P4', 'Presentase Total');
		$this->excel->getActiveSheet()->getStyle('P4')->getFont()->setBold(true);
		$i=6;
		$b=6;
		foreach($kabupaten as $index => $row){
		
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $realisasi_pagu_bok[$index]);
			$this->excel->getActiveSheet()->setCellValue('C'.$i, $realisasi_daerah_bok[$index]);
			if( $realisasi_daerah_bok[$index]!=0 && $realisasi_pagu_bok[$index]!=0){
				$status=$realisasi_daerah_bok[$index]/$realisasi_pagu_bok[$index]*100;
				$this->excel->getActiveSheet()->setCellValue('D'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('E'.$i, $realisasi_pagu_jampersal[$index]);
			$this->excel->getActiveSheet()->setCellValue('F'.$i, $realisasi_daerah_jampersal[$index]);
			if( $realisasi_daerah_jampersal[$index]!=0 && $realisasi_pagu_jampersal[$index]!=0){
				$status=$realisasi_daerah_jampersal[$index]/$realisasi_pagu_jampersal[$index]*100;
				$this->excel->getActiveSheet()->setCellValue('G'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('H'.$i, $realisasi_pagu_akreditasi_puskesmas[$index]);
			$this->excel->getActiveSheet()->setCellValue('I'.$i, $realisasi_daerah_akreditasi_puskesmas[$index]);
			if( $realisasi_daerah_akreditasi_puskesmas[$index]!=0 && $realisasi_pagu_akreditasi_puskesmas[$index]!=0){
				$status=$realisasi_daerah_akreditasi_puskesmas[$index]/$realisasi_pagu_akreditasi_puskesmas[$index]*100;
				$this->excel->getActiveSheet()->setCellValue('J'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('K'.$i, $realisasi_pagu_akreditasi_rs[$index]);
			$this->excel->getActiveSheet()->setCellValue('L'.$i, $realisasi_daerah_akreditasi_rs[$index]);
			if( $realisasi_daerah_akreditasi_rs[$index]!=0 && $realisasi_pagu_akreditasi_rs[$index]!=0){
				$status=$realisasi_daerah_akreditasi_rs[$index]/$realisasi_pagu_akreditasi_rs[$index]*100;
				$this->excel->getActiveSheet()->setCellValue('M'.$i, round($status, 2));
			}
			$this->excel->getActiveSheet()->setCellValue('N'.$i, $realisasi_pagu_total[$index]);
			$this->excel->getActiveSheet()->setCellValue('O'.$i, $realisasi_daerah_total[$index]);
			if( $realisasi_daerah_total[$index]!=0 && $realisasi_pagu_total[$index]!=0){
				$status=$realisasi_daerah_total[$index]/$realisasi_pagu_total[$index]*100;
				$this->excel->getActiveSheet()->setCellValue('P'.$i, round($status, 2));
			}
			$i++;
		
		}
		$this->excel->getActiveSheet()->setCellValue('A5', 'TOTAL :');
		$this->excel->getActiveSheet()->setCellValue('B5', array_sum($realisasi_pagu_bok));
		$this->excel->getActiveSheet()->setCellValue('C5', array_sum($realisasi_daerah_bok));
		$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->setCellValue('D5'.$b, round($fisik_bok[$index],2));
		if( array_sum($realisasi_daerah_bok)!=0 && array_sum($realisasi_pagu_bok)!=0){
			$status=array_sum($realisasi_daerah_bok)/array_sum($realisasi_pagu_bok)*100;
			$this->excel->getActiveSheet()->setCellValue('D5', round($status, 2));
			$this->excel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
		}
		$this->excel->getActiveSheet()->setCellValue('E5', array_sum($realisasi_pagu_jampersal));
		$this->excel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('F5', array_sum($realisasi_daerah_jampersal));
		$this->excel->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->setCellValue('H5'.$b, round($fisik_jampersal[$index],2));
		if( array_sum($realisasi_daerah_jampersal)!=0 && array_sum($realisasi_pagu_jampersal)!=0){
			$status= array_sum($realisasi_daerah_jampersal)/array_sum($realisasi_pagu_jampersal)*100;
			$this->excel->getActiveSheet()->setCellValue('G5', round($status, 2));
			$this->excel->getActiveSheet()->getStyle('G5')->getFont()->setBold(true);
		}
		$this->excel->getActiveSheet()->setCellValue('H5', array_sum($realisasi_pagu_akreditasi_puskesmas));
		$this->excel->getActiveSheet()->getStyle('H5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('I5', array_sum($realisasi_daerah_akreditasi_puskesmas));
		$this->excel->getActiveSheet()->getStyle('I5')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->setCellValue('L5', round($fisik_akreditasi_puskesmas[$index],2));
		if( array_sum($realisasi_daerah_akreditasi_puskesmas)!=0 && array_sum($realisasi_pagu_akreditasi_puskesmas)!=0){
			$status=array_sum($realisasi_daerah_akreditasi_puskesmas)/array_sum($realisasi_pagu_akreditasi_puskesmas)*100;
			$this->excel->getActiveSheet()->setCellValue('J5', round($status, 2));
			$this->excel->getActiveSheet()->getStyle('J5')->getFont()->setBold(true);
		}
		$this->excel->getActiveSheet()->setCellValue('K5', array_sum($realisasi_pagu_akreditasi_rs));
		$this->excel->getActiveSheet()->getStyle('K5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('L5', array_sum($realisasi_daerah_akreditasi_rs));
		$this->excel->getActiveSheet()->getStyle('L5')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->setCellValue('P5'.$b, round($fisik_akreditasi_rs[$index],2));
		if( array_sum($realisasi_daerah_akreditasi_rs)!=0 && array_sum($realisasi_pagu_akreditasi_rs)!=0){
			$status=array_sum($realisasi_daerah_akreditasi_rs)/array_sum($realisasi_pagu_akreditasi_rs)*100;
			$this->excel->getActiveSheet()->setCellValue('M5', round($status, 2));
			$this->excel->getActiveSheet()->getStyle('M5')->getFont()->setBold(true);
		}
		$this->excel->getActiveSheet()->setCellValue('N5', array_sum($realisasi_pagu_total));
		$this->excel->getActiveSheet()->getStyle('N5')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->setCellValue('O5', array_sum($realisasi_daerah_total));
		$this->excel->getActiveSheet()->getStyle('O5')->getFont()->setBold(true);
		//$this->excel->getActiveSheet()->setCellValue('T5'.$b, round($f_total[$index],2));
		if( array_sum($realisasi_daerah_total)!=0 && array_sum($realisasi_pagu_total)!=0){
			$status=array_sum($realisasi_daerah_total)/array_sum($realisasi_pagu_total)*100;
			$this->excel->getActiveSheet()->setCellValue('P5', round($status, 2));
			$this->excel->getActiveSheet()->getStyle('P5')->getFont()->setBold(true);
		}
		$filename='rekap_pagu_nf.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function has_provinsi ($kdunit){
		$cek = $this->dm->get_prov_by_unit($kdunit);
		return $cek->num_rows() > 0 ? true : false;
	}

	function edit_dak($id_laporan){
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$laporan = $this->pm->get_dak_laporan($id_laporan);
		foreach($laporan->result() as $row) {
			$data2['selected_provinsi'] = $row->KodeProvinsi;
			$data2['selected_kabupaten'] = $row->KodeKabupaten;
			$data2['selected_dak'] = $row->JENIS_DAK;
			$data2['selected_waktu'] = $row->WAKTU_LAPORAN;
			$data2['data_dak'] = $row->DATA_DAK;
			$data2['dak_pdf'] = $row->DATA_PDF;
			$data2['dak_pdf_tambahan'] = $row->DATA_PDF_PENDUKUNG;
			$data2['id_laporan']=$id_laporan;
		}
		$id_provinsi='';
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		foreach($this->pm->get_dak_laporan_nf($id_laporan)->result() as $row){
			$id_provinsi=$row->KodeProvinsi;
		}
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_dak'] = $option_jenis_dak;
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['laporan'] = $laporan;
		$data2['provinsi'] = $option_provinsi;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'edit dak';
		$data2['error_file'] = '';
		if($this->session->userdata('upload_file') != ''){
			$data2['error_file'] = $this->session->userdata('upload_file');
			$this->session->unset_userdata('upload_file');
		}
		$data['content'] = $this->load->view('metronic/e-monev/edit_dak',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function edit_dak_nf($id_laporan){
		$kdsatker = $this->session->userdata('kdsatker');
		$thn_anggaran = $this->session->userdata('thn_anggaran');
		$option_rencana_anggaran;
		$option_jenis_satker;
		$option_jenis_dak;
		$kdsatker= $this->session->userdata('kdsatker');
		$laporan = $this->pm->get_dak_laporan_nf($id_laporan);
		foreach($laporan->result() as $row) {
			$data2['selected_provinsi'] = $row->KodeProvinsi;
			$data2['selected_kabupaten'] = $row->KodeKabupaten;
			$data2['selected_dak'] = $row->JENIS_DAK;
			$data2['selected_waktu'] = $row->WAKTU_LAPORAN;
			$data2['data_dak'] = $row->DATA_DAK;
			$data2['id_laporan']=$id_laporan;
		}
		$id_provinsi='';
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_satker($kdsatker)->result() as $row){
				$selected_state = $row->NamaProvinsi;
				$selected_worker = $row->kdsatker;
			}
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$id_provinsi=$laporan->row()->KodeProvinsi;
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($id_provinsi)->result() as $row){
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get('dak_jenis_dak_nf')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_satker['0'] = '-- Pilih SatKer --';
		foreach($this->pm->get_satker()->result() as $row){
			$option_satker[$row->kdsatker] = $row->nmsatker;
		}
		foreach($this->pm->get('ref_jenis_satker')->result() as $row){
			$option_jenis_satker[$row->KodeJenisSatker] = $row->JenisSatker;
		}
		$KodePengajuan;
		foreach($this->pm->get_KodePengajuan()->result() as $row){
			$KodePengajuan = $row->KodePengajuan+1;
		}
		$data2['tgl']=date('d-m-Y');
		$data2['kd_pengajuan'] = $KodePengajuan;
		$data2['kdsatker'] = $kdsatker;
		$data2['thn_anggaran'] = $thn_anggaran;
		$data['e_monev'] = "";
		$data2['jenis_satker'] = $option_jenis_satker;
		$data2['selected_state'] = $selected_state;
		$data2['selected_worker'] = $selected_worker;
		$data2['laporan'] = $laporan;
		$data2['provinsi'] = $option_provinsi;
		$data2['KodeKabupaten'] = $option_kabupaten;
		$data2['satker'] = $option_satker;
		$data['judul'] = 'edit dak';
		$data2['error_file'] = '';
		if($this->session->userdata('upload_file') != ''){
			$data2['error_file'] = $this->session->userdata('upload_file');
			$this->session->unset_userdata('upload_file');
		}
		$data['content'] = $this->load->view('metronic/e-monev/edit_dak_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function update_dak($id_laporan){
		$provinsi=$this->input->post('provinsi');
		$jenis_dak=$this->input->post('jenis_dak');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$waktu_laporan=$this->input->post('waktu_laporan');
		$datadak = array(
			'KodeKabupaten' =>$KodeKabupaten,
			'KodeProvinsi' => $provinsi,
			'JENIS_DAK' => $jenis_dak,
			'WAKTU_LAPORAN' => $waktu_laporan
			);
		
		$this->pm->update('dak_laporan', $datadak, 'ID_LAPORAN_DAK', $id_laporan);
		redirect('e-monev/e_dak/view_kelengkapan');
	}

	function update_dak_nf($id_laporan){
		$provinsi=$this->input->post('provinsi');
		$KodeKabupaten=$this->input->post('KodeKabupaten');
		$waktu_laporan=$this->input->post('waktu_laporan');
		$datadak = array(
			'KodeKabupaten' =>$KodeKabupaten,
			'KodeProvinsi' => $provinsi,
			'WAKTU_LAPORAN' => $waktu_laporan
			);
		
		$this->pm->update('dak_laporan_nf', $datadak, 'ID_LAPORAN_DAK', $id_laporan);
		redirect('e-monev/e_dak/view_kelengkapan_nf');
	}

	function veri($id,$status){
		$datadak = array(
			'STATUS' =>$status,
			);
		$this->pm->update('dak_laporan', $datadak, 'ID_LAPORAN_DAK', $id);
		if($status==1){
			echo 'DITERIMA';
		}else if($status==2){
			echo 'DITOLAK';
		}else{
			echo 'DIPERTIMBANGKAN';
		}
	}

	function veri_nf($id,$status){
		$datadak = array(
			'STATUS' =>$status,
			);
		
		$this->pm->update('dak_laporan_nf', $datadak, 'ID_LAPORAN_DAK', $id);
		if($status==1){
			echo 'DITERIMA';
		}else if($status==2){
			echo 'DITOLAK';
		}else{
			echo 'DIPERTIMBANGKAN';
		}
	}

	function keterangan($id,$keterangan){
		$keterangan=rawurldecode($keterangan);
		$datadak = array(
			'KETERANGAN' =>$keterangan
			);
		
		$this->pm->update('dak_laporan', $datadak, 'ID_LAPORAN_DAK', $id);
		echo $keterangan;
	}

	function keterangan_nf($id,$keterangan){
		$keterangan=rawurldecode($keterangan);
		$datadak = array(
			'KETERANGAN' =>$keterangan
			);
		
		$this->pm->update('dak_laporan_nf', $datadak, 'ID_LAPORAN_DAK', $id);
		echo $keterangan;
	}

	function delete_dak($id_laporan){
		$filedak=$this->pm->get_where('dak_laporan', $id_laporan, 'ID_LAPORAN_DAK')->row()->DATA_DAK;
		$filepdf=$this->pm->get_where('dak_laporan', $id_laporan, 'ID_LAPORAN_DAK')->row()->DATA_PDF;
		$filepdfpendukung=$this->pm->get_where('dak_laporan', $id_laporan, 'ID_LAPORAN_DAK')->row()->DATA_PDF_PENDUKUNG;
		unlink("file/".$filedak);
		unlink("file/".$filepdf);
		unlink("file/".$filepdfpendukung);
		$this->pm->delete('dak_laporan','ID_LAPORAN_DAK', $id_laporan);
		$this->pm->delete('dak_kegiatan', 'ID_LAPORAN_DAK', $id_laporan);
		redirect('e-monev/e_dak/view_kelengkapan');
	}

	function delete_dak_nf($id_laporan){
		$this->pm->delete('dak_laporan_nf','ID_LAPORAN_DAK', $id_laporan);
		$this->pm->delete('dak_kegiatan_nf', 'ID_LAPORAN_DAK', $id_laporan);
		redirect('e-monev/e_dak/view_kelengkapan_nf');
	}

	function delete_file_laporan($id_laporan) {
		$data = array('DATA_DAK' => NULL);
		$this->pm->update('dak_laporan', $data, 'ID_LAPORAN_DAK', $id_laporan);
		//redirect('e-planning/manajemen/grid_pengajuan');
		redirect('e-monev/e_dak/edit_dak/'.$id_laporan);
	}

	function delete_file_laporan_nf($id_laporan) {
		$data = array('DATA_DAK' => NULL);
		$this->pm->update('dak_laporan', $data, 'ID_LAPORAN_DAK', $id_laporan);
			//redirect('e-planning/manajemen/grid_pengajuan');
		redirect('e-monev/e_dak/edit_dak_nf/'.$id_laporan);
	}

	function getProv($kdunit, $thang, $pagu_total){
		$thang = $this->session->userdata('thn_anggaran');
		$kdunit = $this->session->userdata('kdunit');
		$bulan = date("n")-1;
		// pagu total unit utama
		$pagu_total = 0;
		$pagu_total += $this->dum->get_pagu_total_swakelola($thang, $kdunit)->row()->jumlah;
		$pagu_total += $this->dum->get_pagu_total_kontraktual($thang, $kdunit)->row()->nilaikontrak;
		$result = array();
		$records = $this->dum->get_provinsi();
		foreach($records->result_array() as $row){
			$row['id'] = 'prop#'.$kdunit.'#'.$row['KodeProvinsi'].'#'.$thang.'#'.$pagu_total;
			$jns = $this->dum->get_jnssat_by_prov($kdunit, $row['KodeProvinsi']);
			foreach($jns->result_array() as $row2){
				$pagu_jnssat = 0;
				$merah = 0;
				$kuning = 0;
				$hijau = 0;
				$biru = 0;
				$fisik_jnssat = 0;
				$fisik = 0;
				$pagu_progress = 0;
				$kegiatan = $this->dum->get_kegiatan_by_satker($row2['kdunit'], $row2['kdlokasi'], $row2['kdsatker'], $thang);
				$progress_satker=0;
				$count_kegiatan=0;
					foreach($kegiatan->result() as $data_kegiatan){//8
						$kdjendok = $data_kegiatan->kdjendok;
						$kddept = $data_kegiatan->kddept;
						$kdprogram = $data_kegiatan->kdprogram;
						$kdoutput = $data_kegiatan->kdoutput;
						$kdlokasi = $data_kegiatan->kdlokasi;
						$kdkabkota = $data_kegiatan->kdkabkota;
						$kddekon = $data_kegiatan->kddekon;
						$kdsoutput = $data_kegiatan->kdsoutput;
						$kdgiat = $data_kegiatan->kdgiat;
						$kdsatker = $data_kegiatan->kdsatker;
						$progress_fisik = 0;
						//$progress_fisik_output = 0;
						$progress_fisik_total = 0;
						$count_progress_fisik = 0;
						$paket = $this->dum->get_suboutput_by_satker($data_kegiatan->kdgiat, $kdunit, $kdlokasi, $kdsatker, $thang);
						foreach($paket->result() as $pk){//ini menampilkan outputnya
							$kdjendok_paket = $pk->kdjendok;
							$kddept_paket = $pk->kddept;
							$kdprogram_paket = $pk->kdprogram;
							$kdoutput_paket = $pk->kdoutput;
							$kdlokasi_paket = $pk->kdlokasi;
							$kdkabkota_paket = $pk->kdkabkota;
							$kddekon_paket = $pk->kddekon;
							$kdsoutput_paket = $pk->kdsoutput;
							$kdgiat_paket = $pk->kdgiat;
							//ngecek apakah input laporan sudah diisi atau belom
							$cek_paket = $this->lmm->cek_paket_by_kdoutput($thang, $kdjendok_paket, $kdsatker, $kddept_paket, $kdunit, $kdprogram_paket, $kdgiat_paket, $kdoutput_paket, $kdlokasi_paket, $kdkabkota_paket, $kddekon_paket, $kdsoutput_paket);
							if ($cek_paket->num_rows > 0) {
								foreach ($cek_paket->result() as $row2) {//ini hasilnya cuman 1
									$idpaket = $row2->idpaket;
								}
								//PROGRESS FISIK
								if($this->lmm->get_progress_by_idpaket($idpaket)->num_rows() > 0) {
									//ambil progress fisik tiap bulan per output
									$progress_fisik_output = $this->lmm->get_progress_by_idpaket_and_month($idpaket,date("m"))->row()->progress;
									$progress_fisik = $progress_fisik_output / $paket->num_rows();
									// filter flag warna prog fisik (merah kuning hijau biru)
									if($progress_fisik_output <= 50 ){
										$merah += 1;
									}elseif($progress_fisik_output >= 51 && $progress_fisik_output <= 75){
										$kuning += 1;
									}elseif($progress_fisik_output >= 76 && $progress_fisik_output <= 100){
										$hijau += 1;
									}elseif($progress_fisik_output > 100){
										$biru += 1;
									}
								}
								else {
									$progress_fisik = 0;
								}
								$progress_satker += $progress_fisik;
							}
						}
					}
					$count_kegiatan = $this->lmm->cek_kegiatan_terisi($thang, $kdjendok, $kdsatker, $kddept, $kdunit, $kdprogram);
					if ($count_kegiatan > 0) {
						$progress_satker = $progress_satker / $count_kegiatan;
					}
					else {
						$progress_satker = 0;
					}
					
				}
				//filter warna prog fisik
				$row['merah'] = $merah;
				$row['kuning'] = $kuning;
				$row['hijau'] = $hijau;
				$row['biru'] = $biru;
				$row['prog'] = round($progress_satker,1).'%';
				$progress_satker = 0;
				
				$row['name'] = $row['NamaProvinsi'];
				$jumlah_paket = $this->dum->count_output_by_prov($kdunit, $row['KodeProvinsi'], $thang);
				$row['paket'] = $jumlah_paket.' OUTPUT';
				$row['state'] = $this->has_jnssat($kdunit, $row['KodeProvinsi'],$thang) ? 'closed' : 'open';
				array_push($result, $row);
			}
			echo json_encode($result);
		}

		function getKegiatan($kdunit, $kdlokasi, $kdsatker, $thang, $pagu_total)
		{
			$bulan = date("n")-1;
			$result = array();
			$records = $this->dum->get_kegiatan_by_satker($kdunit, $kdlokasi, $kdsatker, $thang);
			foreach($records->result_array() as $row){
				$row['id'] = 'kegiatan#'.$row['kdgiat'].'#'.$row['kdunit'].'#'.$row['kdlokasi'].'#'.$row['kdsatker'].'#'.$thang.'#'.$pagu_total;
				$pagu_skmp = 0;
				$merah = 0;
				$kuning = 0;
				$hijau = 0;
				$biru = 0;
				$fisik_skmp = 0;
				$fisik = 0;
				$pagu_progress = 0;
				$progress_fisik = 0;
				$progress_fisik_output = 0;
				$progress_fisik_total = 0;
				$count_progress_fisik = 0;
				$paket = $this->dum->get_suboutput_by_satker($row['kdgiat'], $kdunit, $kdlokasi, $kdsatker, $thang);
				foreach($paket->result() as $pk){
					$kdjendok = $pk->kdjendok;
					$kddept = $pk->kddept;
					$kdprogram = $pk->kdprogram;
					$kdoutput = $pk->kdoutput;
					$kdlokasi = $pk->kdlokasi;
					$kdkabkota = $pk->kdkabkota;
					$kddekon = $pk->kddekon;
					$kdsoutput = $pk->kdsoutput;
					$kdgiat = $pk->kdgiat;
					//ngecek apakah input laporan sudah diisi atau belom
					$cek_paket = $this->lmm->cek_paket_by_kdoutput($thang, $kdjendok, $kdsatker, $kddept, $kdunit, $kdprogram, $kdgiat, $kdoutput, $kdlokasi, $kdkabkota, $kddekon, $kdsoutput);
					if ($cek_paket->num_rows > 0) {
						foreach ($cek_paket->result() as $row2) {
							$idpaket = $row2->idpaket;
						}
						//PROGRESS FISIK
						if($this->lmm->get_progress_by_idpaket($idpaket)->num_rows() > 0) {
							//ambil progress fisik tiap bulan per output
							$progress_fisik_output = $this->lmm->get_progress_by_idpaket_and_month($idpaket,date("m"))->row()->progress;
							//hitung jumlah total output per kegiatan
							$count_progress_fisik = $paket->num_rows();
							//menjumlahkan semua progress fisik
							$progress_fisik_total += $progress_fisik_output;
							//hasil akhirnya
							$progress_fisik = $progress_fisik_total / $count_progress_fisik;
							if($progress_fisik <= 50 ){
								$merah += 1;
							}elseif($progress_fisik >= 51 && $progress_fisik <= 75){
								$kuning += 1;
							}elseif($progress_fisik >= 76 && $progress_fisik <= 100){
								$hijau += 1;
							}elseif($progress_fisik > 100){
								$biru += 1;
							}
						}
						else {
							$progress_fisik = 0;
						}
					}
				}
				
				if($pagu_progress > 0 && $pagu_total > 0) {
					// realisasi fisik unit utama per jenis satker
					$fisik = $pagu_progress / $pagu_total;
				}
				
				//filter warna prog fisik
				$row['merah'] = $merah;
				$row['kuning'] = $kuning;
				$row['hijau'] = $hijau;
				$row['biru'] = $biru;
				//$row['prog'] = round($fisik, 2).'%';
				$row['prog'] = $progress_fisik.'%';
				
				$row['name'] = strtoupper($row['nmgiat']);
				$row['paket'] = 'Program : '.$row['kdprogram'];
				$row['state'] = $this->has_output($row['kdgiat'], $kdunit, $kdlokasi, $row['kdsatker'], $thang) ? 'closed' : 'open';
				array_push($result, $row);
			}
			echo json_encode($result);
		}

		function has_output($kdgiat, $kdunit, $kdlokasi, $kdsatker, $thang)
		{
			$rs =  $this->dm->get_suboutput_by_satker($kdgiat, $kdunit, $kdlokasi, $kdsatker, $thang);
			return $rs->num_rows() > 0 ? true : false;
		}
		// function has_paket($kdunit, $kdlokasi, $kdsatker, $thang)
		// {
			// 	$rs =  $this->dm->get_subkomponen_by_satker($kdunit, $kdlokasi, $kdsatker, $thang);
			// 	return $rs->num_rows() > 0 ? true : false;
		// }
		
		// function getPaket($kdunit, $kdlokasi, $kdsatker, $thang, $pagu_total)
		// {
			// 	$bulan = date("n")-1;
			// 	$result = array();
			// 	$records = $this->dm->get_subkomponen_by_satker($kdunit, $kdlokasi, $kdsatker, $thang);
			// 	foreach($records->result_array() as $row){
				// 		$row['id'] = 'paket#'.$kdunit.'#'.$kdlokasi.'#'.$kdsatker.'#'.$row['kdskmpnen'].'#'.$thang.'#'.$pagu_total;
				// 		$pagu_skmp = 0;
				// 		$merah = 0;
				// 		$kuning = 0;
				// 		$hijau = 0;
				// 		$biru = 0;
				// 		$fisik_skmp = 0;
				// 		$fisik = 0;
				// 		$pagu_progress = 0;
				// 		// pagu total unit utama per jenis satker provinsi
				// 		$pagu_skmp += $this->dm->get_pagu_skmp_swakelola($thang, $row['kdjendok'], $kdsatker, $row['kddept'], $kdunit, $row['kdprogram'], $row['kdgiat'], $row['kdoutput'], $row['kdsoutput'], $row['kdkmpnen'], $row['kdskmpnen'])->row()->jumlah;
				// 		$pagu_skmp += $this->dm->get_pagu_skmp_kontraktual($thang, $row['kdjendok'], $kdsatker, $row['kddept'], $kdunit, $row['kdprogram'], $row['kdgiat'], $row['kdoutput'], $row['kdsoutput'], $row['kdkmpnen'], $row['kdskmpnen'])->row()->nilaikontrak;
				// 		$pagu_swakelola = 0;
				// 		$pagu_kontraktual = 0;
				// 		$prog_fis_swakelola = 0;
				// 		$prog_fis_kontraktual = 0;
				// 		 if($pagu_skmp > 0){
					// 			$paket = $this->dm->get_paket($thang, $row['kdjendok'], $row['kdsatker'], $row['kddept'], $row['kdunit'], $row['kdprogram'], $row['kdgiat'], $row['kdoutput'], $row['kdsoutput'], $row['kdkmpnen'], $row['kdskmpnen']);
					// 			if($paket->num_rows() > 0) {
						// 				foreach($paket->result() as $pk){
							// 					$pagu_swakelola = $this->dm->get_swakelola($pk->idpaket)->row()->jumlah;
							// 					$pagu_kontraktual = $this->dm->get_kontraktual($pk->idpaket)->row()->nilaikontrak;
							// 					//mengambil nilai progress fisik paket per bulan
							// 					if(isset($this->dm->get_progres_fisik_swakelola_per_bulan($pk->idpaket, $bulan)->row()->progress))
								// 						$prog_fis_swakelola = $this->dm->get_progres_fisik_swakelola_per_bulan($pk->idpaket, $bulan)->row()->progress;
							// 					if(isset($this->dm->get_progres_fisik_kontraktual_per_bulan($pk->idpaket, $bulan)->row()->progress))
								// 						$prog_fis_kontraktual = $this->dm->get_progres_fisik_kontraktual_per_bulan($pk->idpaket, $bulan)->row()->progress;
						// 				}
					// 			}
					// 			if($prog_fis_swakelola > 0 && $prog_fis_kontraktual > 0) {
						// 				$pagu_progress += $prog_fis_swakelola*$pagu_swakelola + $prog_fis_kontraktual*$pagu_kontraktual;
						// 				// realisasi fisik komponen unit utama per jenis satker per provinsi
						// 				$fisik_skmp = ($prog_fis_swakelola*$pagu_swakelola + $prog_fis_kontraktual*$pagu_kontraktual) / $pagu_skmp;
					// 			}
					// 		  }
				// 		// filter flag warna prog fisik (merah kuning hijau biru)
				// 		if($fisik_skmp <= 50 ){
					// 			$merah += 1;
				// 		}elseif($fisik_skmp >= 51 && $fisik_skmp <= 75){
					// 			$kuning += 1;
				// 		}elseif($fisik_skmp >= 76 && $fisik_skmp <= 100){
					// 			$hijau += 1;
				// 		}elseif($fisik_skmp > 100){
					// 			$biru += 1;
				// 		}
				// 		if($pagu_progress > 0 && $pagu_total > 0) {
					// 			// realisasi fisik unit utama per jenis satker
					// 			$fisik = $pagu_progress / $pagu_total;
				// 		}
				// 		//filter warna prog fisik
				// 		$row['merah'] = $merah;
				// 		$row['kuning'] = $kuning;
				// 		$row['hijau'] = $hijau;
				// 		$row['biru'] = $biru;
				// 		$row['prog'] = round($fisik, 2).'%';
				// 		$row['name'] = '['.$row['kdgiat'].'.'.$row['kdoutput'].'.'.$row['kdsoutput'].'.'.$row['kdkmpnen'].'.'.$row['kdskmpnen'].'] '.$row['urskmpnen'];
				// 		$row['paket'] = 'Program : '.$row['kdprogram'];
				// 		$row['state'] = 'open';
				// 		array_push($result, $row);
			// 	}
			// 	echo json_encode($result);
		// }
		
		
		//grafik rencana fisik
		function kelola_menu_nf(){
			$option_jenis_dak['0'] = '-- Pilih Jenis    --';
			foreach ($this->pm->get('dak_nf')->result() as $row){
				$option_jenis_dak[$row->id_dak_nf] = $row->nama_dak_nf;
			}
			$option_kategori['0'] = '-- Pilih Kategori    --';
			foreach ($this->pm->get('dak_nf_kategori')->result() as $row){
				$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
			}
			$data['e_monev'] = "";
			$data2['jenis_dak'] = $option_jenis_dak;
			$data2['kategori'] = $option_kategori;
			$data['judul'] = 'View pagu';
			$data['content'] = $this->load->view('metronic/e-monev/kelola_menu_nf',$data2,true);
			$this->load->view(VIEWPATH,$data);
		}

		function get_dak(){
			$kategori = $this->input->post("kategori");
			$tahun = $this->session->userdata("thn_anggaran");
			$role = $this->session->userdata('kd_role');
			if($role != 19 ){
				$data = $this->pm->get_where_double("dak_jenis_dak", $kategori, "ID_KATEGORI", $tahun, "TAHUN_ANGGARAN")->result();
				$i=0;
				if($data != null){
					foreach ($data as $row) {
						$datajson[$i]['id'] = $row->ID_JENIS_DAK;
						$datajson[$i]['dak'] = $row->NAMA_JENIS_DAK;
						$i++;
					}
				}
				else{
					$datajson[0]['id'] = 0;
					$datajson[0]['dak'] = "";
				}	
			}else{
				$i = 0;
				if($kategori == 1 && $this->session->userdata('kdunit') == 07){
					$datajson[$i]['id'] = '2';
					$datajson[$i]['dak'] = 'FARMASI';
				}
				else if($this->session->userdata('kdunit') == 04){
					$i =0;
					if($kategori == 1){
						$datajson[$i]['id'] = '1';
						$datajson[$i]['dak'] = 'RUJUKAN';
						$i++;
						$datajson[$i]['id'] = '3';
						$datajson[$i]['dak'] = 'DASAR';
						$i++;
					}
					elseif($kategori == 2){
						$datajson[$i]['id'] = '9';
						$datajson[$i]['dak'] = 'PUSKESMAS';
						$i++;
						$datajson[$i]['id'] = '17';
						$datajson[$i]['dak'] = 'RS PRATAMA';
						$i++;
					}
					elseif($kategori == 3){
						$datajson[$i]['id'] = '10';
						$datajson[$i]['dak'] = 'RS PENUGASAN';
						$i++;
					}
					elseif($kategori == 4){
						$datajson[$i]['id'] = '19';
						$datajson[$i]['dak'] = 'DASAR';
						$i++;
						$datajson[$i]['id'] = '20';
						$datajson[$i]['dak'] = 'RUJUKAN';
						$i++;
					}
					else{
						$datajson[$i]['id'] = '21';
						$datajson[$i]['dak'] = 'DASAR';
						$i++;
						$datajson[$i]['id'] = '23';
						$datajson[$i]['dak'] = 'RUJUKAN';
						$i++;
					}
					
				}
				elseif($kategori == 1 && $this->session->userdata('kdunit') == 03){
					$datajson[$i]['id'] = '3';
					$datajson[$i]['dak'] = 'DASAR';
				}
				else{
					$datajson[$i]['id'] = '0';
					$datajson[$i]['dak'] = '-';
				}
			}
			

			header('Content-Type: application/json');
			echo json_encode($datajson);
		}

		function get_dak_nf(){
			$kategori = $this->input->post("kategori");
			$tahun = $this->session->userdata('thn_anggaran');
			$role = $this->session->userdata('kd_role');
			$unit = $this->session->userdata('kdunit');
			
			$i=0;
			$datajson = array();
			if($role == 19 && $tahun == 2017){
				if($unit == '07'){
					$datajson[$i]['id'] = 3;
					$datajson[$i]['dak_nf'] = 'Distribusi Obat dan E-logistik';
				}
				elseif($unit == '03'){
					$data = $this->pm->get_where_double("dak_nf",$kategori, "id_kategori_nf", $tahun, "TAHUN_ANGGARAN")->result();
					foreach ($data as $row) {
						if($kategori == 1 || $kategori == 4){
							$datajson[$i]['id'] = $row->id_dak_nf;
							$datajson[$i]['dak_nf'] = $row->nama_dak_nf;
							$i++;
						}
					}
				}
				elseif($unit == '04') {
					$data = $this->pm->get_where_double("dak_nf",$kategori, "id_kategori_nf", $tahun, "TAHUN_ANGGARAN")->result();
					foreach ($data as $row) {
						if($kategori == 10 || $kategori == 11){
							$datajson[$i]['id'] = $row->id_dak_nf;
							$datajson[$i]['dak_nf'] = $row->nama_dak_nf;
							$i++;
						}
					}
				}
			}
			else{
				$data = $this->pm->get_where_double("dak_nf",$kategori, "id_kategori_nf", $tahun, "TAHUN_ANGGARAN")->result();
				if($data != null){
					
					foreach ($data as $row) {
						$datajson[$i]['id'] = $row->id_dak_nf;
						$datajson[$i]['dak_nf'] = $row->nama_dak_nf;
							$i++;
						}
					}
					else{
						$datajson[0]['id'] = 0;
						$datajson[0]['dak_nf'] = "";
					}	
			}
			

			header('Content-Type: application/json');
			echo json_encode($datajson);
		}

		function import_menu_nf(){
			$jenis_dak=$this->input->post('jenis_dak');
			$kategori=$this->input->post('kategori');
			$tahun = $this->session->userdata('thn_anggaran');
			$array=0;
			for($i = 1; $i <= 1; $i++) {	

			$config['upload_path'] = "./file";
			$config['allowed_types'] ='doc|docx|pdf|xls|xlsx|txt';
			$config['max_size']	= '5000';


			// create directory if doesn't exist
			if(!is_dir($config['upload_path']))
			mkdir($config['upload_path'], 0777);
			$this->load->library('excel');
			$this->load->library('upload', $config);
			//$nama_file=$this->input->post('file');
			if(!empty($_FILES['file'.$i]['name'])){			
				//$upload = $this->upload->do_upload('file'.$i);
				//$data[$i] = $this->upload->data();
				//if($data[$i]['file_size'] > 0) $file[$i] = $data[$i]['file_name'];
				if(!$this->upload->do_upload('file'.$i)){
					$notif_upload = '<font color="red"><b>'.$this->upload->display_errors("<p>Error Upload : ", "</p>").'</b></font>';
					$this->session->set_userdata('upload_file', $notif_upload);
					redirect('e-monev/e_dak/import_menu_nf');
				}
				else{
					$data[$i] = $this->upload->data();
					if($data[$i]['file_size'] > 0) $file[$i] = $data[$i]['file_name'];                    
					  	$inputFileName = './file/'.$data[$i]['file_name'];
              		  	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            	      	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
          		      	$objPHPExcel = $objReader->load($inputFileName);
                      
            		  	$sheet = $objPHPExcel->getSheet(0);
           			  	$highestRow = $sheet->getHighestRow();
          			  	$highestColumn = $sheet->getHighestColumn();
                    	
	            		for ($row = 1; $row <= $highestRow; $row++){ //  Read a row of data into an array                 
	                    	$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
	                                                NULL,
	                                                TRUE,
	                                                FALSE);
	                		//Sesuaikan sama nama kolom tabel di database                                
	                		$data = array(
	             			       "id_menu"=> $rowData[0][0],
	              			       "nama_menu"=> $rowData[0][1],
	              			       "id_dak_nf"=> $jenis_dak,
	              			       "id_kategori_nf"=> $kategori,
	              			       "TAHUN" => $tahun,
	              			       "id_sarpras"=> $rowData[0][2],
	              			);
	                		//sesuaikan nama dengan nama tabel_pagu
	             			$insert = $this->db->insert("dak_nf_menu",$data);

	             		}
	             		redirect('e-monev/e_dak/kelola_menu_nf?status=5');

				}
			}}
			
		}
		
		function input_monev_nf(){
			$kdsatker = $this->session->userdata('kdsatker');
			$kdprovinsi=$this->session->userdata('kodeprovinsi');
			$kdkabupaten=$this->session->userdata('kodekabupaten');
			if($kdsatker!=NULL){
				foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
					$selected_state = $row->NamaProvinsi;
				}
				foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
					$selected_kabupaten=$row->NamaKabupaten;
				}

			}
			$option_provinsi['0'] = '-- Pilih Provinsi --';
			foreach ($this->pm->get_provinsi()->result() as $row){
				$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
			}
			$option_kategori['0'] = '-- Pilih Kategori    --';
			foreach ($this->pm->get_where('dak_nf_kategori', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN' )->result() as $row){
				$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
			}
			$option_masalah['0'] = '-- Tidak Ada  --';

			foreach ($this->pm->get('permasalahan_dak')->result() as $row){
				$option_masalah[$row->KodeMasalah] = $row->Masalah;
			}


			$satuan = $this->pm->get('ref_satuan')->result();
			$option_satuan['2'] = 'Paket';
			foreach ($satuan as $row) {
				$option_satuan[$row->KodeSatuan] = $row->Satuan;
			}

			$role = $this->session->userdata('kd_role');
			$data2['role']	= $role;	
			$data2['nama'] = "tes";
			$data2['option_provinsi'] = $option_provinsi;
			$data2['provinsi'] = $selected_state;
			$data2['KodeProvinsi'] = $kdprovinsi;
			$data2['kabupaten'] = $selected_kabupaten;
			$data2['KodeKabupaten'] = $kdkabupaten;
			$data2['kategori'] = $option_kategori;
			$data2['masalah'] = $option_masalah;
			$data2['satuan']= $option_satuan;
			$data['content'] = $this->load->view('metronic/e-monev/monev_nf',$data2,true);
			$this->load->view(VIEWPATH,$data);
		}

		function get_monev_nf($provinsi='', $kabupaten='', $jenis_dak=''){
			$kdsatker="";
			if($this->session->userdata('kd_role')!=16){
				$provinsi=$this->session->userdata('kodeprovinsi');
				$kabupaten=$this->session->userdata('kodekabupaten');
				$kdsatker=$this->session->userdata('kdsatker');
			}
			$tahun = $this->session->userdata('thn_anggaran');
			

			// perubahan tecno untuk proses input monev nonfisik
			if ($tahun>=2019) {
				// header('Content-Type: application/json');
				if ($jenis_dak=="17") {

									$i  = 0;
									
									$datajson[$i]['NO'] = $no;
									$datajson[$i]['ID_MENU'] = '0';
									$datajson[$i]['NAMA'] = '1';
									$datajson[$i]['VOLUME'] = '1';
									$datajson[$i]['SATUAN'] = '';
									$datajson[$i]['HARGA_SATUAN'] = '';
									$datajson[$i]['JUMLAH'] = '';
				} else {
				// cek data kedalam dak_nf_pagu dan dak_nf_pagus
				$query = $this->pm->get_pagu_nf_tecno($provinsi,$kabupaten,$jenis_dak,$tahun);
				if($query->num_rows != 0){
					// jika ada 
					$pagu_seluruh = $this->pm->get_where_quadruple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, 'TAHUN_ANGGARAN')->result();
					$i  = 0;
					$no = 1;

					foreach ($query->result() as $row) {
							$datajson[$i]['NO']      = $tahun;
							$datajson[$i]['ID_MENU'] = $row->id_menu;
							$datajson[$i]['NAMA']    = $row->nama_menu;
							$datajson[$i]['JUMLAH']  = $row->jumlah;	
							if($pagu_seluruh != null){
								$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
							}else{
								$datajson[$i]['PAGU_SELURUH'] = 0;
							}
							$i++;		
						}
					$no++;

				}else{
					// jika tidak ada 
						// proses save dak_nf_pagu
						$qqery = $this->pm->getIdbudgeting($provinsi,$kabupaten,$jenis_dak,$tahun);
						$dataId = $qqery->row_array();
						$numquer= $qqery->num_rows();

						if ($numquer=='0') {
							$idData = '0';
						} else {
							$idData = $dataId['id_pengajuan'];
						}
						
						
						$query1  = $this->pm->getBudgetingData($idData);

						$pagu_seluruh1 = $this->pm->getBudgetingPagu($provinsi,$kabupaten,$jenis_dak,$tahun)->result();
							

							if($query1->num_rows != 0){
								// jika data ada
									$i  = 0;
									$no = 0;
									// jabarkan data budgetdaknf_data dan budgetdaknf_pengajuan
									foreach ($query1->result() as $row) {
										$data1 = array(
												'KodeProvinsi'   => $dataId['kdprovinsi'],
												'KodeKabupaten'  => $dataId['kdkabupaten'],
												'jumlah'         => $row->jumlah,
												'id_dak_nf'      => $dataId['kategori'],
												'TAHUN_ANGGARAN' => $dataId['tahun_anggaran'],
												'id_menu_nf'     => $row->id_menu,
												'id_kategori_nf' => $dataId['kategori'],
												'kdrumahsakit' 	 => $dataId['kdrumahsakit'],
												'volume'         => $row->volume,
												'harga_satuan'   => $row->harga_satuan,
											
											);
										$this->pm->save($data1,"dak_nf_pagu");
									}

									// jabarkan data sbelum input
											$data2 = array(
												'id_dak_nf'      => $dataId['kategori'],
												'KodeProvinsi'   => $dataId['kdprovinsi'],
												'KodeKabupaten'  => $dataId['kdkabupaten'],
												'pagu'           => $pagu_seluruh1[0]->nominal,
												'TAHUN_ANGGARAN' => $dataId['tahun_anggaran'],
												'id_kategori_nf' => $dataId['kategori'],
												'kdrumahsakit'   => $dataId['kdrumahsakit'],
											);
							// proses save dak_nf_pagus
											$this->pm->save($data2,"dak_nf_pagus");

									// ambil data json di table pagu pagus yg sudah di inputkan

										// $query = $this->pm->get_where_double('ref_iku', $kode1, 'KodeProgram', '1', 'KodeStatus');
										$query = $this->pm->get_pagu_nf_($provinsi,$kabupaten,$jenis_dak,$tahun);
										$i=0;
										$no = 1;
										if($query->num_rows != 0){
											
											$pagu_seluruh = $this->pm->get_where_quadruple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, 'TAHUN_ANGGARAN')->result();
											
											foreach ($query->result() as $row) {
													$datajson[$i]['NO'] = $no;
													$datajson[$i]['ID_MENU'] =$row->id_menu;
													$datajson[$i]['NAMA'] = $row->nama_menu;
													$datajson[$i]['JUMLAH'] = $row->jumlah;	
													if($pagu_seluruh != null){
														$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
													}else{
														$datajson[$i]['PAGU_SELURUH'] = 0;
													}
													$i++;		
												}
												$no++;
										}else{
												$datajson[$i]['NO'] = $no;
												$datajson[$i]['ID_MENU'] = '0';
												$datajson[$i]['NAMA'] = '';
												$datajson[$i]['VOLUME'] = '';
												$datajson[$i]['SATUAN'] = '';
												$datajson[$i]['HARGA_SATUAN'] = '';
												$datajson[$i]['JUMLAH'] = '';	
										}
							}else{ 
								// JIKA DATA TIDAK ADA
								$i=0;
								$no = 1;
									$datajson[$i]['NO'] = $no;
									$datajson[$i]['ID_MENU'] = '0';
									$datajson[$i]['NAMA'] = '';
									$datajson[$i]['VOLUME'] = '';
									$datajson[$i]['SATUAN'] = '';
									$datajson[$i]['HARGA_SATUAN'] = '';
									$datajson[$i]['JUMLAH'] = '';
							}
					}

				}
			} else {
				# code...
				// else
				// $query = $this->pm->get_where_double('ref_iku', $kode1, 'KodeProgram', '1', 'KodeStatus');
				$query = $this->pm->get_pagu_nf_($provinsi,$kabupaten,$jenis_dak,$tahun);
				$i=0;
				$no = 1;
				if($query->num_rows != 0){
					
					$pagu_seluruh = $this->pm->get_where_quadruple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, 'TAHUN_ANGGARAN')->result();
					
					foreach ($query->result() as $row) {
							$datajson[$i]['NO'] = $no;
							$datajson[$i]['ID_MENU'] =$row->id_menu;
							$datajson[$i]['NAMA'] = $row->nama_menu;
							$datajson[$i]['JUMLAH'] = $row->jumlah;	
							if($pagu_seluruh != null){
								$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
							}else{
								$datajson[$i]['PAGU_SELURUH'] = 0;
							}
							$i++;		
						}
						$no++;
				}else{
						$datajson[$i]['NO'] = $no;
						$datajson[$i]['ID_MENU'] = '0';
						$datajson[$i]['NAMA'] = '';
						$datajson[$i]['VOLUME'] = '';
						$datajson[$i]['SATUAN'] = '';
						$datajson[$i]['HARGA_SATUAN'] = '';
						$datajson[$i]['JUMLAH'] = '';	
				}
			}
			echo json_encode($datajson);
		}
		function get_monev_nf_rs($provinsi, $kabupaten, $jenis_dak, $kdrs){
			$kdsatker="";
			if($this->session->userdata('kd_role')!=16){
				$provinsi=$this->session->userdata('kodeprovinsi');
				$kabupaten=$this->session->userdata('kodekabupaten');
				$kdsatker=$this->session->userdata('kdsatker');
			}
			$tahun = $this->session->userdata('thn_anggaran');

			if ($tahun>=2019) {

				$query = $this->pm->get_pagu_nf_tecno2($provinsi,$kabupaten,$jenis_dak,$kdrs,$tahun);
				if($query->num_rows != 0){
					// jika ada 
					$pagu_seluruh = $this->pm->get_where_quadruple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, 'TAHUN_ANGGARAN' , $kdsatker, 'kdrumahsakit')->result();
					$i  = 0;
					$no = 1;

					foreach ($query->result() as $row) {
							$datajson[$i]['NO']      = $tahun;
							$datajson[$i]['ID_MENU'] = $row->id_menu;
							$datajson[$i]['NAMA']    = $row->nama_menu;
							$datajson[$i]['JUMLAH']  = $row->jumlah;	
							if($pagu_seluruh != null){
								$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
							}else{
								$datajson[$i]['PAGU_SELURUH'] = 0;
							}
							$i++;		
						}
					$no++;

				}else{

					$qqery = $this->pm->getIdbudgeting_rs($provinsi,$kabupaten,$jenis_dak,$tahun, $kdrs);
						$dataId = $qqery->row_array();
						$numquer= $qqery->num_rows();

						if ($numquer=='0') {
							$idData = '0';
						} else {
							$idData = $dataId['id_pengajuan'];
						}
						
						
						$query1  = $this->pm->getBudgetingData($idData);

						$pagu_seluruh1 = $this->pm->getBudgetingPagu_rs($provinsi,$kabupaten,$jenis_dak,$tahun, $kdrs)->result();
							

							if($query1->num_rows != 0){
								// jika data ada
									$i  = 0;
									$no = 0;
									// jabarkan data budgetdaknf_data dan budgetdaknf_pengajuan
									foreach ($query1->result() as $row) {
										$data1 = array(
												'KodeProvinsi'   => $dataId['kdprovinsi'],
												'KodeKabupaten'  => $dataId['kdkabupaten'],
												'jumlah'         => $row->jumlah,
												'id_dak_nf'      => $dataId['kategori'],
												'TAHUN_ANGGARAN' => $dataId['tahun_anggaran'],
												'id_menu_nf'     => $row->id_menu,
												'id_kategori_nf' => $dataId['kategori'],
												'kdrumahsakit' 	 => $dataId['kdrumahsakit'],
												'volume'         => $row->volume,
												'harga_satuan'   => $row->harga_satuan,
											
											);
										$this->pm->save($data1,"dak_nf_pagu");
									}

									// jabarkan data sbelum input
											$data2 = array(
												'id_dak_nf'      => $dataId['kategori'],
												'KodeProvinsi'   => $dataId['kdprovinsi'],
												'KodeKabupaten'  => $dataId['kdkabupaten'],
												'pagu'           => $pagu_seluruh1[0]->nominal,
												'TAHUN_ANGGARAN' => $dataId['tahun_anggaran'],
												'id_kategori_nf' => $dataId['kategori'],
												'kdrumahsakit'   => $dataId['kdrumahsakit'],
											);
							// proses save dak_nf_pagus
											$this->pm->save($data2,"dak_nf_pagus");

									// ambil data json di table pagu pagus yg sudah di inputkan

										// $query = $this->pm->get_where_double('ref_iku', $kode1, 'KodeProgram', '1', 'KodeStatus');
										$query = $this->pm->get_pagu_nf_($provinsi,$kabupaten,$jenis_dak,$tahun);
										$i=0;
										$no = 1;
										if($query->num_rows != 0){
											
											$pagu_seluruh = $this->pm->get_where_quadruple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, 'TAHUN_ANGGARAN')->result();
											
											foreach ($query->result() as $row) {
													$datajson[$i]['NO'] = $no;
													$datajson[$i]['ID_MENU'] =$row->id_menu;
													$datajson[$i]['NAMA'] = $row->nama_menu;
													$datajson[$i]['JUMLAH'] = $row->jumlah;	
													if($pagu_seluruh != null){
														$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
													}else{
														$datajson[$i]['PAGU_SELURUH'] = 0;
													}
													$i++;		
												}
												$no++;
										}else{
												$datajson[$i]['NO'] = $no;
												$datajson[$i]['ID_MENU'] = '0';
												$datajson[$i]['NAMA'] = '';
												$datajson[$i]['VOLUME'] = '';
												$datajson[$i]['SATUAN'] = '';
												$datajson[$i]['HARGA_SATUAN'] = '';
												$datajson[$i]['JUMLAH'] = '';	
										}
							}else{ 
								// JIKA DATA TIDAK ADA
								$i=0;
								$no = 1;
									$datajson[$i]['NO'] = $no;
									$datajson[$i]['ID_MENU'] = '0';
									$datajson[$i]['NAMA'] = '';
									$datajson[$i]['VOLUME'] = '';
									$datajson[$i]['SATUAN'] = '';
									$datajson[$i]['HARGA_SATUAN'] = '';
									$datajson[$i]['JUMLAH'] = '';
							}

				}


			}else {
				// dibawah tahun 2019
					$query = $this->pm->get_pagu_nf_rs($provinsi,$kabupaten,$jenis_dak,$tahun, $kdrs);

						// else
						// $query = $this->pm->get_where_double('ref_iku', $kode1, 'KodeProgram', '1', 'KodeStatus');
					$i=0;
					$no = 1;
					if($query->num_rows != 0){
						$pagu_seluruh = $this->pm->get_where_triple("dak_nf_pagus", $provinsi, "KodeProvinsi", $kabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $kdrs, "kdrumahsakit")->result();
						
						foreach ($query->result() as $row) {
								$datajson[$i]['NO'] = $no;
								$datajson[$i]['ID_MENU'] =$row->id_menu;
								$datajson[$i]['NAMA'] = $row->nama_menu;
								$datajson[$i]['JUMLAH'] = $row->jumlah;	
								if($pagu_seluruh != null){
									$datajson[$i]['PAGU_SELURUH'] = $pagu_seluruh[0]->pagu;
								}else{
									$datajson[$i]['PAGU_SELURUH'] = 0;
								}
								$i++;		
							}
							$no++;
					}else{
							$datajson[$i]['NO'] = $no;
							$datajson[$i]['ID_MENU'] = '0';
							$datajson[$i]['NAMA'] = '';
							$datajson[$i]['VOLUME'] = '';
							$datajson[$i]['SATUAN'] = '';
							$datajson[$i]['HARGA_SATUAN'] = '';
							$datajson[$i]['JUMLAH'] = '';	
					}

			}
			echo json_encode($datajson);
		}
		function penginputan_monev_dak_nf(){
			$kdsatker=$this->session->userdata('kdsatker');
			$ID_USER=$this->session->userdata('id_user');	
			$thn_anggaran=$this->session->userdata('thn_anggaran');	
			$jenis_dak=$this->input->post('jenis_dak');
			$kategori=$this->input->post('kategori');
			$provinsi=$this->input->post('KodeProvinsi');
			$nama_menu=$this->input->post('nama_menu');
			$kodekabupaten=$this->input->post('KodeKabupaten');		
			$tgl=date("Y-m-d");
			$waktu_laporan=$this->input->post('waktu_laporan');		
			$id_menu=$this->input->post('id_menu');
			$id=$this->input->post('id_menu'); 		
			$fisik=$this->input->post('fisik');
			$masalah = $this->input->post('masalah');
			$realisasi = $this->input->post('realisasi');
			$rumah_sakit = $this->input->post('rumah_sakit');
			$persen = $this->input->post('persen');
			$masalah = $this->input->post('masalah');
			$pagu = $this->input->post("pagu");
			$tahun = $this->session->userdata('thn_anggaran');
			$jumlah = $this->input->post('jumlah');
			$where = array(
				'waktu_laporan' => $waktu_laporan,
				'KodeProvinsi' => $provinsi,
				'KodeKabupaten' => $kodekabupaten,
				'id_dak_nf' => $jenis_dak,
				'TAHUN_ANGGARAN' => $thn_anggaran
			);
			if($rumah_sakit != 0){
				$where['KD_RS'] = $rumah_sakit;
			}
			$cek_laporan = $this->bm->select_where_array('dak_nf_laporan', $where)->num_rows();
			// $cek_laporan = $this->pm->get_where_quadruple("dak_nf_laporan",  $waktu_laporan, "waktu_laporan", $provinsi, "KodeProvinsi", $kodekabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf")->num_rows();
			$lokasi  = $this->input->post("lokasi");
			if($cek_laporan != 0){
				redirect('e-monev/pendaftaran_edak/monev_nf_2018?status=1');
			}

			if(empty($_POST['kode_rs']))$kode_rs='0';	
			for($i = 1; $i <= 4; $i++) {	
				$config['upload_path'] = "./file";
				$config['allowed_types'] ='doc|docx|pdf|xls|xlsx|txt';
				$config['max_size']	= '5000';
				$config['file_name'] = 'PDF_'.$provinsi.$kodekabupaten.$waktu_laporan.$i;
				$config['encrypt_name'] = TRUE;

				// create directory if doesn't exist
				if(!is_dir($config['upload_path']))
				mkdir($config['upload_path'], 0777);

				$this->load->library('upload', $config);
				//$nama_file=$this->input->post('file');
				if(!empty($_FILES['filepdf'.$i]['name'])){			
				//$upload = $this->upload->do_upload('file'.$i);
				//$data[$i] = $this->upload->data();
				//if($data[$i]['file_size'] > 0) $file[$i] = $data[$i]['file_name'];
					if(!$this->upload->do_upload('filepdf'.$i)){
					$notif_upload = '<font color="red"><b>'.$this->upload->display_errors("<p>Error Upload : ", "</p>").'</b></font>';
					$this->session->set_userdata('upload_file', $notif_upload);
					redirect('e-monev/pendaftaran_edak/monev_nf_2018');
					}else{
					$data[$i] = $this->upload->data();
					if($data[$i]['file_size'] > 0) $filepdf[$i] = $data[$i]['file_name'];
					}
				}
				if(!empty($_FILES['filepdf'.$i]['name'])){			
				//$upload = $this->upload->do_upload('file'.$i);
				//$data[$i] = $this->upload->data();
				//if($data[$i]['file_size'] > 0) $file[$i] = $data[$i]['file_name'];
					if(!$this->upload->do_upload('filepdf'.$i)){
						$notif_upload = '<font color="red"><b>'.$this->upload->display_errors("<p>Error Upload : ", "</p>").'</b></font>';
						$this->session->set_userdata('upload_file', $notif_upload);
						if($tahun == 2017){
							redirect('e-monev/pendaftaran_edak/input_monev_nf');
						}
						else{
							redirect('e-monev/pendaftaran_edak/monev_nf_2018');
						}
						
					}else{
						$data[$i] = $this->upload->data();
						if($data[$i]['file_size'] > 0) $filepdf[$i] = $data[$i]['file_name'];
					}
				}			
			}
				$config['upload_path'] = "./file";
				$config['allowed_types'] ='doc|docx|pdf|xls|xlsx|txt';
				$config['max_size']	= '5000';
				$config['file_name'] = 'PDF_'.$provinsi.$kodekabupaten.$waktu_laporan.$i;
				$config['encrypt_name'] = TRUE;

				
			$data= array(	
					 'tanggal_pembuatan' => $tgl,
					 'ID_USER' =>$ID_USER,
					 'KodeProvinsi' => $provinsi,
					 'KodeKabupaten'=> $kodekabupaten,
					 'id_dak_nf' => $jenis_dak,
					 'id_kategori_nf' => $kategori,
					 'kdsatker' => $kdsatker,
					 'TAHUN_ANGGARAN' => $thn_anggaran,
			         'waktu_laporan' => $waktu_laporan,
					 'data_pendukung1' => $filepdf[1],
					 'data_pendukung2' => $filepdf[2],
					 'data_pendukung3' => $filepdf[3],
					 'data_pendukung4' => $filepdf[4],
					 'KD_RS' => $rumah_sakit		
					 );
	      	$this->pm->save($data,'dak_nf_laporan');
			$id_pengajuan = $this->mm->get_where5('dak_nf_laporan','ID_USER',$ID_USER,'KodeProvinsi',$provinsi,'KodeKabupaten',$kodekabupaten,'kdsatker',$kdsatker,'data_pendukung1',$filepdf[1])->row()->id_pengajuan;
			$vol_survei =0;
			foreach( $id_menu as $index =>$idmenu){
					$id_p = $this->pm->get_where_5('dak_nf_pagu', $jenis_dak, 'id_dak_nf', $provinsi, 'KodeProvinsi',$kodekabupaten,'KodeKabupaten',$idmenu, 'id_menu_nf', $tahun, "TAHUN_ANGGARAN")->result();
				$data_pagu =array(
						'jumlah' => str_replace(",", "", $jumlah[$index]),
				);
				$this->pm->update("dak_nf_pagu", $data_pagu, "id_pagu" , $id_p[0]->id_pagu);

				$datax= array(	
						 'id_menu_nf' => $idmenu,
						 'id_dak_nf'  => $jenis_dak,
						 'nama_menu' =>$nama_menu[$index],
						 'id_pengajuan' => $id_pengajuan,
						 'realisasi'=> str_replace(",", "", $realisasi[$index]),
						 'persentase' => round($persen[$index],2),
				         'fisik' => $fisik[$index],
				         'KodeMasalah' => $masalah[$index],
				         'lokasi' => $lokasi[$index]	
						 );
		      	$this->pm->save($datax,'dak_nf_rka');
			}
			//end input laporan
			if( $jenis_dak != 4 && $jenis_dak != 5){

				redirect('e-monev/pendaftaran_edak/monev_nf_2018?status=5');
			}
			else{
				if ($tahun == 2017){
					$vol_survei = max($fisik);
				redirect('e-monev/e_dak/input_akreditasi/'.$id_pengajuan.'/'. $vol_survei);
				}
				else {
					redirect('e-monev/pendaftaran_edak/monev_nf_2018?status=5');
				}
				
			}
	       /*$data_sb= array(	
					 'id_pengajuan' => $id_pengajuan,
					 'id_kategori' =>$kategori,
					 'id_sbidang' => $jenis_dak,	
					 );
	      	$this->pm->save($data_sb,'data_sbidang');
	        		$data_kategori= array(	
					 'id_pengajuan' => $id_pengajuan,
					 'id_kategori' =>$kategori	
					 );
	      	$this->pm->save($data_kategori,'data_kategori');*/        
	      	//redirect('e-monev/pendaftaran_edak/tambah_dak2?status=5');
		}public function penginputan_monev_dak_nf2020xx($value='')
		{
			$kdsatker      = $this->session->userdata('kdsatker');
			$ID_USER       = $this->session->userdata('id_user');	
			$thn_anggaran  = $this->session->userdata('thn_anggaran');	
			$tahun         = $this->session->userdata('thn_anggaran');
			
			$jenis_dak     = $this->input->post('kate_gori');
			$provinsi      = $this->input->post('kd_prov');
			$kodekabupaten = $this->input->post('kd_kab');
			$waktu_laporan = $this->input->post('tri_wulan');
			$kdrs          = $this->input->post('kd_rs');


				$total_pagu = $this->input->post("total_pagusemua");
				$nama_menu  = $this->input->post('nama_menu');
				$kode_menu  = $this->input->post('kode_menu');
				$id_menu    = $this->input->post('id_menu');	
				$volume     = $this->input->post("volume");
				$satuan     = $this->input->post("satuan");
				$pagu       = $this->input->post("alokasi");
				$realisasi  = $this->input->post('realis');
				$persen     = $this->input->post('persen');
				
				
				$fisik      = $this->input->post('o_persen');
				
				$pelaksana  = $this->input->post("pelaksana");
				$lokasi     = $this->input->post('lokasi');
				$keterangan = $this->input->post('keterangan');
				
				$masalah    = $this->input->post('masalah');
				
				$tracer= $this->input->post('tracer');

				
				
			$tgl    = date("Y-m-d");
			$second = date('s');

			$bakal_name= $thn_anggaran.'_'.$provinsi.'_'.$kodekabupaten.'_'.$jenis_dak.'_'.$waktu_laporan;

			echo $bakal_name;
		}

		public function penginputan_monev_dak_nf2020($value='')
		{
			$kdsatker      = $this->session->userdata('kdsatker');
			$ID_USER       = $this->session->userdata('id_user');	
			$thn_anggaran  = $this->session->userdata('thn_anggaran');	
			$tahun         = $this->session->userdata('thn_anggaran');
			
			$jenis_dak     = $this->input->post('kate_gori');
			$provinsi      = $this->input->post('kd_prov');
			$kodekabupaten = $this->input->post('kd_kab');
			$waktu_laporan = $this->input->post('tri_wulan');
			$kdrs          = $this->input->post('kd_rs');


				$total_pagu = $this->input->post("total_pagusemua");
				$nama_menu  = $this->input->post('nama_menu');
				$kode_menu  = $this->input->post('kode_menu');
				$id_menu    = $this->input->post('id_menu');	
				$volume     = $this->input->post("volume");
				$satuan     = $this->input->post("satuan");
				$pagu       = $this->input->post("alokasi");
				$realisasi  = $this->input->post('realis');
				$persen     = $this->input->post('persen');
				
				
				$fisik      = $this->input->post('o_persen');
				
				$pelaksana  = $this->input->post("pelaksana");
				$lokasi     = $this->input->post('lokasi');
				$keterangan = $this->input->post('keterangan');
				
				$masalah    = $this->input->post('masalah');
				
				$tracer= $this->input->post('tracer');

				
				
			$tgl    = date("Y-m-d");
			$second = date('s');

			$bakal_name= $thn_anggaran.'_'.$provinsi.'_'.$kodekabupaten.'_'.$jenis_dak.'_'.$waktu_laporan;

			for($i = 1; $i <= 4; $i++) {
				$config['upload_path'] = "./file_monev_nf";
				$config['allowed_types'] ='doc|docx|pdf|xls|xlsx|txt|PDF';
				$config['max_size']	= '5000';

				$extension = end(explode(".", $_FILES['filepdf'.$i]['name']));

				switch ($i) {
					case '1':
						$nama_f='laporan';
						break;
					
					case '2':
						$nama_f='sp2d';
						break;
					case '3':
						$nama_f='dokumentasi';
						break;
					case '4':
						$nama_f='lainnya';
						break;
					
					default:
						$nama_f='unknow';
						break;
				}

				$new_name = $bakal_name.'-'.$nama_f.'_'.$second;

				$_FILES['filepdf'.$i]['name'] = $new_name.'.'.$extension;

				// $config['file_name'] = 'PDF_'.$provinsi.$kodekabupaten.$waktu_laporan.$i.$tahun.$second;
				// $config['encrypt_name'] = TRUE;

				if(!is_dir($config['upload_path']))
					mkdir($config['upload_path'], 0777);

					$this->load->library('upload', $config);
					//$nama_file=$this->input->post('file');
					if(!empty($_FILES['filepdf'.$i]['name'])){	

					if(!$this->upload->do_upload('filepdf'.$i)){

							
						$this->session->set_flashdata('gagal','Format File Harus PDF (Tidak Lebih Dari 5 MB)');
						redirect('e-monev/pendaftaran_edak/monev_nf_2018');	
					}else{

						$data[$i] = $this->upload->data();
						if($data[$i]['file_size'] > 0) $filepdf[$i] = $data[$i]['file_name'];

					}
				}else{

					$this->session->set_flashdata('gagal','file tidak boleh kosong');
					redirect('e-monev/pendaftaran_edak/monev_nf_2018');	
				}

			}		
				

			// simpan ke dak nf laporan

				$datalaporan = array(	
							'tanggal_pembuatan' => $tgl,
							'ID_USER'           => $ID_USER,
							'KodeProvinsi'      => $provinsi,
							'KodeKabupaten'     => $kodekabupaten,
							'id_dak_nf'         => $jenis_dak,
							'id_kategori_nf'    => $jenis_dak,
							'kdsatker'          => $kdsatker,
							'TAHUN_ANGGARAN'    => $thn_anggaran,
							'waktu_laporan'     => $waktu_laporan,
							'data_pendukung1'   => $filepdf[1],
							'data_pendukung2'   => $filepdf[2],
							'data_pendukung3'   => $filepdf[3],
							'data_pendukung4'   => $filepdf[4],
							'KD_RS'             => $kdrs		
				);

				 $this->pm->save($datalaporan,"dak_nf_laporan");
				// print_r($datalaporan);
				
				$pengajuan_data = $this->mm->get_where5('dak_nf_laporan','ID_USER',$ID_USER,'KodeProvinsi',$provinsi,'KodeKabupaten',$kodekabupaten,'kdsatker',$kdsatker,'data_pendukung1',$filepdf[1]);

				$id_pengajuan =$pengajuan_data->row()->id_pengajuan;
				$par_id       =$pengajuan_data->num_rows();

				//Simpan Ke dak_nf_pagus
				if ($par_id !='0') {
					$datapagus = array(
						'id_dak_nf'      => $jenis_dak,
						'KodeProvinsi'   => $provinsi,
						'KodeKabupaten'  => $kodekabupaten,
						'pagu'           => str_replace(",", "", $total_pagu),
						'TAHUN_ANGGARAN' => $thn_anggaran,
						'id_kategori_nf' => $jenis_dak,
						'kdrumahsakit'   => $kdrs,
					);
							
				 $this->pm->save($datapagus,"dak_nf_pagus");
				} else {
					
				}
				
			//simpan ke dak_nf_rka
				foreach( $id_menu as $index =>$idmenu){
					$datanfrka         = array(	
							'id_dak_nf'    => $jenis_dak,
							'id_menu_nf'   => $kode_menu[$index],
							'nama_menu'    => $nama_menu[$index],
							'id_pengajuan' => $id_pengajuan,
							'realisasi'    => str_replace(",", "", $realisasi[$index]),
							'persentase'   => round($persen[$index],2),
							'fisik'        => $fisik[$index],
							'KodeMasalah'  => $masalah[$index],
							'lokasi'       => $lokasi[$index],
							'volume'       => $volume[$index],
							'satuan_kode'  => $satuan[$index],
							'pelaksana'    => $pelaksana[$index],
							'keterangan'   => $keterangan[$index],
							'tracer'       => $tracer[$index]
					);
		      		$this->pm->save($datanfrka,'dak_nf_rka');

					// print_r($datanfrka);

				// cek apabila ada di pagu abaikan
				// ababila tidak ada di save
				if ($par_id !='0') {
					$datapagu = array(
							'KodeProvinsi'   => $provinsi,
							'KodeKabupaten'  => $kodekabupaten,
							'jumlah'         => str_replace(",", "", $pagu[$index]),
							'id_dak_nf'      => $jenis_dak,
							'TAHUN_ANGGARAN' => $thn_anggaran,
							'id_menu_nf'     => $kode_menu[$index],
							'id_kategori_nf' => $jenis_dak,
							'kdrumahsakit' 	 => $kdrs,
							'volume'         => $volume[$index],
							'harga_satuan'   => str_replace(",", "", $pagu[$index])
											
					);
				    $this->pm->save($datapagu,"dak_nf_pagu");
				} else {
					
				}
					
					
				}
			$this->session->set_flashdata('sukses','file berhasil di simpan');
			redirect('e-monev/pendaftaran_edak/monev_nf_2018');	
				
		}

		function input_akreditasi($id_pengajuan, $vol_survei){
			$pengajuan = $this->pm->get_where('dak_nf_laporan', $id_pengajuan, 'id_pengajuan')->row();
			$provinsi = $this->pm->get_where('ref_provinsi', $pengajuan->KodeProvinsi, 'KodeProvinsi')->row()->NamaProvinsi;
			$kabupaten = $this->pm->get_where_double('ref_kabupaten', $pengajuan->KodeProvinsi, 'KodeProvinsi', $pengajuan->KodeKabupaten, 'KodeKabupaten')->row()->NamaKabupaten;
			$jenis_akreditasi = $this->pm->get_where('dak_nf', $pengajuan->id_dak_nf, 'id_dak_nf')->row()->nama_dak_nf;
			if($pengajuan->id_dak_nf == 4){
				$option_rs['0'] = "-- Pilih Rumah Sakit --"; 
				$rs = $this->pm->get_where_double('data_rumah_sakit', $pengajuan->KodeProvinsi, 'KodeProvinsi', $pengajuan->KodeKabupaten, 'KodeKabupaten')->result();
				if($rs){
					foreach ($rs as $key => $value) {
						$option_rs[$value->KODE_RS] = $value->NAMA_RS;	
					}
				}
			}
			else{
				$option_rs['0'] = "-- Pilih Puskesmas --"; 
				$rs = $this->pm->get_where_double('data_puskesmas', $pengajuan->KodeProvinsi, 'KodeProvinsi', $pengajuan->KodeKabupaten, 'KodeKabupaten')->result();
				if($rs){
					foreach ($rs as $key => $value) {
						$option_rs[$value->KodePuskesmas] = $value->NamaPuskesmas;	
					}
				}
			}
			$option_akreditasi[0] = "-- Pilih Akreditasi --";
			
			$daftar_akreditasi_op = $this->pm->get_where_double('ref_akreditasi', $this->session->userdata('thn_anggaran'), 'tahun', $pengajuan->id_dak_nf, 'id_dak_nf')->result();
			foreach ($daftar_akreditasi_op as $key => $value) {
				$option_akreditasi[$value->id] = $value->nama_akreditasi;
			}

			$data['option_rs'] = $option_rs;
			$data['option_akreditasi'] = $option_akreditasi;
			$data['vol'] = $vol_survei;
			$data['provinsi'] = $provinsi;
			$data['kabupaten'] = $kabupaten;
			$data['jenis_akreditasi'] = $jenis_akreditasi;
			$data['pengajuan'] = $pengajuan;

			if($pengajuan->id_dak_nf == 4){
				$data['content'] = $this->load->view('metronic/e-monev/akreditasi',$data,true);	
			}
			else{
				$data['content'] = $this->load->view('metronic/e-monev/akreditasi_puskesmas',$data,true);
			}
			
			$this->load->view(VIEWPATH,$data);
		}
		
		function save_akreditasi(){
			$id_pengajuan = $this->input->post('id_pengajuan');
			$id_dak_nf = $this->input->post('id_dak_nf');
			$kode_rs = $this->input->post('kode_rs');
			$sebelum_akreditasi = $this->input->post('sebelum_akreditasi');
			$sesudah_akreditasi = $this->input->post('sesudah_akreditasi');
			$keterangan = $this->input->post('keterangan');
			foreach ($kode_rs as $key => $value) {
				$nama_rs = $this->pm->get_where('data_rumah_sakit', $value, 'KODE_RS')->row();
				$data = array(
					'id_pengajuan' => $id_pengajuan[$key],
					'KODE_RS' => $value,
					'before' => '',
					'after' => $sesudah_akreditasi[$key],
					'nama' => $nama_rs->NAMA_RS,
					'keterangan' => $keterangan[$key]
				);
				$this->pm->save($data,'dak_survei_akreditasi');
			}
			redirect('e-monev/e_dak/input_monev_nf?status=5');
		}

		function save_akreditasi_puskesmas(){
			$id_pengajuan = $this->input->post('id_pengajuan');
			$id_dak_nf = $this->input->post('id_dak_nf');
			$sesudah_akreditasi = $this->input->post('sesudah_akreditasi');
			$nama_rs = $this->input->post('nama_rs');
			$keterangan = $this->input->post('keterangan');
			$pengajuan = $this->pm->get_where('dak_nf_laporan', end($id_pengajuan), 'id_pengajuan')->row();
			foreach ($id_dak_nf as $key => $value) {
				$data = array ('id_pengajuan' => $id_pengajuan[$key],
					'KODE_RS' => $pengajuan->KodeProvinsi.$pengajuan->KodeKabupaten.rand(pow(10, 4-1), pow(10, 4)-1),
					'before' => '',
					'after' => $sesudah_akreditasi[$key],
					'nama' => $nama_rs[$key],
					'keterangan' => $keterangan[$key]
				);
				$this->pm->save($data,'dak_survei_akreditasi');
			}
			redirect('e-monev/e_dak/input_monev_nf?status=5');
		}
		function halaman_akreditasi(){
			if($this->session->userdata('kd_role') == 17){
				$query = array(
					'KodeProvinsi' => $this->session->userdata('kodeprovinsi'),
					'KodeKabupaten' => $this->session->userdata('kodekabupaten'),
					'id_dak_nf' => 4,
					'waktu_laporan' => 4,
					'TAHUN_ANGGARAN' => $this->session->userdata('thn_anggaran')
				);
				$pengajuan_rs = $this->pm->get_where_array('dak_nf_laporan', $query)->row();
				$id_pengajuan = array();
				if($pengajuan_rs){
					array_push($id_pengajuan, $pengajuan_rs->id_pengajuan);
				}
				$daftar_akreditasi = array();
				if($id_pengajuan){
					$daftar_akreditasi = $this->pm->get_where_in('dak_survei_akreditasi', 'id_pengajuan', $id_pengajuan)->result();	
				}
				$daftar_rs = array();
				$i=0;
				if($daftar_akreditasi){
					$id_dak_nf = $pengajuan_rs->id_dak_nf;
					foreach ($daftar_akreditasi as $key => $value) {
						$daftar_rs[$i]['nama_rs'] = $this->pm->get_where('data_rumah_sakit', $value->KODE_RS, 'KODE_RS')->row()->NAMA_RS;
						$daftar_rs[$i]['before'] = '';
						$daftar_rs[$i]['after'] = $this->pm->get_where_triple('ref_akreditasi', $value->after, 'id', $this->session->userdata('thn_anggaran'), 'tahun', 4, 'id_dak_nf')->row()->nama_akreditasi;
						if($value->keterangan){
							$daftar_rs[$i]['keterangan'] = $value->keterangan;
						}
						else{
							$daftar_rs[$i]['keterangan'] = '';
						}
						$i++;	

					}
				}
				$query = array(
					'KodeProvinsi' => $this->session->userdata('kodeprovinsi'),
					'KodeKabupaten' => $this->session->userdata('kodekabupaten'),
					'id_dak_nf' => 5,
					'waktu_laporan' => 4,
					'TAHUN_ANGGARAN' => $this->session->userdata('thn_anggaran')
				);
				$pengajuan_puskesmas = $this->pm->get_where_array('dak_nf_laporan', $query)->row();
				$id_pengajuan = array();
				if($pengajuan_puskesmas){
					array_push($id_pengajuan, $pengajuan_puskesmas->id_pengajuan);	
				}
				$daftar_akreditasi = array();
				if($id_pengajuan){
					$daftar_akreditasi = $this->pm->get_where_in('dak_survei_akreditasi', 'id_pengajuan', $id_pengajuan)->result();	
				}
				$daftar_puskes = array();
				$i=0;
				if($daftar_akreditasi){
					$id_dak_nf = $pengajuan_puskesmas->id_dak_nf;
					foreach ($daftar_akreditasi as $key => $value) {
						$daftar_puskes[$i]['nama_rs'] = $value->nama;
						$daftar_puskes[$i]['before'] = 0;	
						$daftar_puskes[$i]['after'] = $this->pm->get_where_triple('ref_akreditasi', $value->after, 'id', $this->session->userdata('thn_anggaran'), 'tahun', $id_dak_nf, 'id_dak_nf')->row()->nama_akreditasi;
						
						if($value->keterangan){
							$daftar_puskes[$i]['keterangan'] = $value->keterangan;
						}
						else{
							$daftar_puskes[$i]['keterangan'] = '';
						}
						$i++;								
					}
				}
				
				$data['daftar_rs'] = $daftar_rs;
				$data['daftar_puskes'] = $daftar_puskes;
				$data['provinsi'] = $this->pm->get_where('ref_provinsi', $this->session->userdata('kodeprovinsi'), 'KodeProvinsi')->row()->NamaProvinsi;
				$data['kabupaten'] = $this->pm->get_where_double('ref_kabupaten', $this->session->userdata('kodeprovinsi'), 'KodeProvinsi', $this->session->userdata('kodekabupaten'), 'KodeKabupaten')->row()->NamaKabupaten;
				$data['content'] = $this->load->view('metronic/e-monev/list_akreditasi',$data,true);
				$this->load->view(VIEWPATH,$data);
			}
			else{
				$option_provinsi['0'] = '-- Pilih Provinsi --';
				foreach ($this->pm->get_provinsi()->result() as $row){
					$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
				}
				$data['option_provinsi'] = $option_provinsi;
				$data['daftar_rs'] = array();
				$data['daftar_puskes'] = array();
				$data['content'] = $this->load->view('metronic/e-monev/list_akreditasi',$data,true);
				$this->load->view(VIEWPATH,$data);
			}
		}

		function cari_akreditasi($provinsi, $kabupaten){
			$pengajuan_rs = $this->pm->get_where_triple('dak_nf_laporan', $provinsi, 'KodeProvinsi', $kabupaten, 'KodeKabupaten', 4, 'id_dak_nf')->result();
				$id_pengajuan = array();
				$id;
			foreach ($pengajuan_rs as $key => $value) {
				array_push($id_pengajuan, $value->id_pengajuan);
				$id = $value->id_pengajuan;
			}
			$daftar_akreditasi = array();
			if($id_pengajuan){
				$daftar_akreditasi = $this->pm->get_where_in('dak_survei_akreditasi', 'id_pengajuan', $id_pengajuan)->result();	
			}
			$daftar_rs = array();
			$i=0;
			if($daftar_akreditasi){
				$id_dak_nf = $id;
				foreach ($daftar_akreditasi as $key => $value) {
					if($value->after){
						$daftar_rs[$i]['nama_rs'] = $this->pm->get_where('data_rumah_sakit', $value->KODE_RS, 'KODE_RS')->row()->NAMA_RS;
						$daftar_rs[$i]['after'] = $this->pm->get_where_triple('ref_akreditasi', $value->after, 'id', $this->session->userdata('thn_anggaran'), 'tahun', 4, 'id_dak_nf')->row()->nama_akreditasi;	
						if($value->keterangan){
							$daftar_rs[$i]['keterangan'] = $value->keterangan;
						}
						else{
							$daftar_rs[$i]['keterangan'] = '';
						}
					}
					$i++;									
				}
			}
			
			$pengajuan_puskesmas = $this->pm->get_where_triple('dak_nf_laporan', $provinsi, 'KodeProvinsi', $kabupaten, 'KodeKabupaten', 5, 'id_dak_nf')->result();
			$id_pengajuan = array();
			foreach ($pengajuan_puskesmas as $key => $value) {
				array_push($id_pengajuan, $value->id_pengajuan);
			}
			$daftar_akreditasi = array();
			if($id_pengajuan){
				$daftar_akreditasi = $this->pm->get_where_in('dak_survei_akreditasi', 'id_pengajuan', $id_pengajuan)->result();

			}
			$daftar_puskes = array();
			$i=0;
			if($daftar_akreditasi){
				foreach ($daftar_akreditasi as $key => $value) {
					if($value->after){
						$daftar_puskes[$i]['nama_rs'] = $value->nama;
						$daftar_puskes[$i]['after'] = $this->pm->get_where_triple('ref_akreditasi', $value->after, 'id', $this->session->userdata('thn_anggaran'), 'tahun', 5, 'id_dak_nf')->row()->nama_akreditasi;	
						if($value->keterangan){
							$daftar_puskes[$i]['keterangan'] = $value->keterangan;
						}
						else{
							$daftar_puskes[$i]['keterangan'] = '';
						}
					}
					$i++;									
				}
			}
			$data = array(
				'status' => 'success',
				'rs' => $daftar_rs,
				'puskes' => $daftar_puskes
			);
			header('Content-Type: application/json');
			echo json_encode($data);
		}	
			
		function laporan_detail_nf(){
			$kdsatker = $this->session->userdata('kdsatker');
			$kdprovinsi=$this->session->userdata('kodeprovinsi');
			$kdkabupaten=$this->session->userdata('kodekabupaten');
			$role = $this->session->userdata('kd_role');
			if($kdsatker!=NULL){
				foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
					$selected_state = $row->NamaProvinsi;
				}
				foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
					$selected_kabupaten=$row->NamaKabupaten;
				}

			}
			$option_provinsi['0'] = '-- Pilih Provinsi --';
			foreach ($this->pm->get_provinsi()->result() as $row){
				$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
			}
			$option_kategori['0'] = '-- Pilih Kategori    --';
			foreach ($this->pm->get_where('dak_nf_kategori', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
				$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
			}


			if($role == 19){
				$option_kategori = array();
				$unit = $this->session->userdata('kdunit');
				if($unit == '07'){
					$option_kategori['0'] = '-- Pilih Kategori    --';
					$option_kategori['1'] = 'BOK';
				}
				if($unit == '03'){
					$option_kategori['0'] = '-- Pilih Kategori    --';
					$option_kategori['1'] = 'BOK';
					$option_kategori['4'] = 'Jampersal';	
				}
			}

			$option_masalah['0'] = '-- Tidak Ada  --';

			foreach ($this->pm->get('permasalahan_dak')->result() as $row){
				$option_masalah[$row->KodeMasalah] = $row->Masalah;
			}


			$satuan = $this->pm->get('ref_satuan')->result();
			$option_satuan['2'] = 'Paket';
			foreach ($satuan as $row) {
				$option_satuan[$row->KodeSatuan] = $row->Satuan;
			}

			
			$data2['role']	= $role;	
			$data2['nama'] = "tes";
			$data2['option_provinsi'] = $option_provinsi;
			$data2['provinsi'] = $selected_state;
			$data2['KodeProvinsi'] = $kdprovinsi;
			$data2['kabupaten'] = $selected_kabupaten;
			$data2['KodeKabupaten'] = $kdkabupaten;
			$data2['kategori'] = $option_kategori;
			$data2['masalah'] = $option_masalah;
			$data2['satuan']= $option_satuan;
			$data['content'] = $this->load->view('metronic/e-monev/detail_monev_nf',$data2,true);
			$this->load->view(VIEWPATH,$data);

		}

		function get_laporan_nf($p,$k,$jenis_dak, $waktu_laporan, $kdrs = null){
			$tahun = $this->session->userdata('thn_anggaran');
			$hasil = $this->pm->get_laporan_nf($p,$k,$jenis_dak,$waktu_laporan,$tahun, $kdrs)->result();
			$i=0;
			$no=1;
			$jumlah = 0;
			$fisik =0;
			$datajson = array();
			if($hasil){
				foreach ($hasil as $row) {
					$datajson[$i]['NO'] = $no;
					$datajson[$i]['ID_PAGU'] = $row->id_pagu;
					$datajson[$i]['NAMA'] = $row->nama_menu;
					$datajson[$i]['PAGU'] = number_format($row->jumlah);
					$datajson[$i]['PAGU_SELURUH'] = number_format($row->pagu);
					$datajson[$i]['REALISASI'] = number_format($row->realisasi);
					$datajson[$i]['PERSENTASE'] = $row->persentase;
					$datajson[$i]['FISIK'] = $row->fisik;
					$datajson[$i]['id_laporan'] = $row->id_pengajuan;
					if($row->lokasi != null){
						$datajson[$i]['lokasi'] = $row->lokasi;
					}
					else{
						$datajson[$i]['lokasi'] = '';
					}
					$masalah = $this->pm->get_where("permasalahan_dak", $row->KodeMasalah, "KodeMasalah")->result();
					$datajson[$i]['MASALAH'] = $masalah[0]->Masalah;
					$i++;
					$no++;	
				}
			}
			echo json_encode($datajson);
		}
		// function tes_data(){
		// 	$tahun = $this->session->userdata('thn_anggaran'); 
		// 	$dak_nf = $this->pm->get_where("dak_nf", $tahun, "TAHUN_ANGGARAN")->result();
			
		// 	$menu = $this->pm->get_where("menu", $tahun, "TAHUN")->result();
		// 	foreach ($menu as $row) {
		// 		echo $row->NAMA;
		// 	}
		// 	print_r($dak_nf);
		// 	exit();
		// }

		function isi_pagu($provinsi, $kabupaten, $jenis_dak){
			$kabupaten = $this->pm->get_data_kabupaten2($provinsi, $kabupaten)->result();
			$tahun = $this->session->userdata("thn_anggaran");
			foreach ($kabupaten as $row) {
				$cek_pagu = $this->pm->get_where_quadruple("dak_nf_pagus", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf", $tahun, "TAHUN_ANGGARAN")->result();
				if($cek_pagu != null){
					if($cek_pagu[0]->pagu > 0){
						$menu = $this->pm->get_where("dak_nf_menu", $jenis_dak, "id_dak_nf")->result();
						foreach ($menu as $row2) {
							$cek_pagu2 = $this->pm->get_where_5("dak_nf_pagu", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $jenis_dak, "id_dak_nf",  $row2->id_menu, "id_menu_nf", $tahun, "TAHUN_ANGGARAN")->result();
							if($cek_pagu2 == null || $cek_pagu2[0]->jumlah == 0){
								$data = array(
								'id_dak_nf' => $jenis_dak,
								'KodeProvinsi' => $row->KodeProvinsi,
								'KodeKabupaten' => $row->KodeKabupaten,
								'TAHUN_ANGGARAN' => $tahun,
								'id_menu_nf' => $row2->id_menu,
								'jumlah' => 0,
								
								);
								$this->pm->save($data,"dak_nf_pagu");
									
							}
						}
						echo "ID = " .$jenis_dak . "=>" . $row->NamaKabupaten . "->" . "ok <br>";
						
					}
				}
			}

		}

		function loop_pagu(){
			$kabupaten = $this->pm->get_data_kabupaten(0)->result();
			foreach ($kabupaten as $row) {
				for($i = 1 ; $i<=4 ; $i++){
					$this->isi_pagu($row->KodeProvinsi, $row->KodeKabupaten, $i);
				}
			}
		}
		function rekap_seluruh_menu_nf2(){
			$kdsatker = $this->session->userdata('kdsatker');
			$kdprovinsi=$this->session->userdata('kodeprovinsi');
			$kdkabupaten=$this->session->userdata('kodekabupaten');
			if($kdsatker!=NULL){
				foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
					$selected_state = $row->NamaProvinsi;
				}
				foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
					$selected_kabupaten=$row->NamaKabupaten;
				}

			}
			$option_provinsi['0'] = '-- Pilih Provinsi --';
			foreach ($this->pm->get_provinsi()->result() as $row){
				$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
			}
			$option_kategori['0'] = '-- Pilih Kategori    --';
			foreach ($this->pm->get_where('dak_nf_kategori', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
				$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
			}
			$option_masalah['0'] = '-- Tidak Ada  --';

			foreach ($this->pm->get('permasalahan_dak')->result() as $row){
				$option_masalah[$row->KodeMasalah] = $row->Masalah;
			}


			$satuan = $this->pm->get('ref_satuan')->result();
			$option_satuan['2'] = 'Paket';
			foreach ($satuan as $row) {
				$option_satuan[$row->KodeSatuan] = $row->Satuan;
			}

			$role = $this->session->userdata('kd_role');
			$data2['role']	= $role;	
			$data2['nama'] = "tes";
			$data2['option_provinsi'] = $option_provinsi;
			$data2['provinsi'] = $selected_state;
			$data2['KodeProvinsi'] = $kdprovinsi;
			$data2['kabupaten'] = $selected_kabupaten;
			$data2['KodeKabupaten'] = $kdkabupaten;
			$data2['kategori'] = $option_kategori;
			$data2['masalah'] = $option_masalah;
			$data2['satuan']= $option_satuan;
			$data['content'] = $this->load->view('metronic/e-monev/rekap_seluruh_menu_nf',$data2,true);
			$this->load->view(VIEWPATH,$data);

	}
	function print_realisasi_menu_nf2(){
		$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if($k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $dak_nf = $this->pm->get_where('dak_nf', $j, 'id_dak_nf')->row();
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Realisasi Menu NF');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu '.$dak_nf->nama_dak_nf.' Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		$menu = $this->pm->get_where_double("dak_nf_menu", $j, "id_dak_nf", $tahun, "TAHUN")->result();
		$rowx = '6';
		$rowx2 = '7';
		$column = 'C';
		$this->excel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
		foreach ($menu as $row) {
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'PAGU' );
		    $i = $column++;
			$this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'REALISASI' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'PERSENTASE' );
		    $column++;
		    $this->excel->getActiveSheet()->setCellValue($column.$rowx2, 'FISIK' );
		    $this->excel->getActiveSheet()->mergeCells($i.$rowx.':'.$column.$rowx);
		    $this->excel->getActiveSheet()->setCellValue($i.$rowx, $row->nama_menu);
		    $column++;
			
		}
		$this->excel->getActiveSheet()->setCellValue($column.'6' , 'Jumlah');
		$this->excel->getActiveSheet()->getStyle("A6:".$column."6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A7:".$column."7")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->mergeCells($column.'6:'. $column .'7');
		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');

		
		$b = 8;
		foreach ($kabupaten as $row) {
			$column = 'C';
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten );
			$jumlah = 0;
			$pagu_s = 0;
			$pagu_s = $this->pm->get_where_quadruple('dak_nf_pagus', $j, "id_dak_nf", $tahun ,"TAHUN_ANGGARAN", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->row();
			if($pagu_s){
				foreach ($menu as $row2) {
					$pagu = $this->pm->get_where_5("dak_nf_pagu", $j, "id_dak_nf", $row2->id_menu, "id_menu_nf", $tahun ,"TAHUN_ANGGARAN", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten" );
					if($pagu->num_rows() > 0 ){
						$pagu = $pagu->result();
						$this->excel->getActiveSheet()->setCellValue($column.$b, number_format($pagu[0]->jumlah));
						$this->excel->getActiveSheet()->getStyle($column. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$column++;
						$laporan = $this->pm->get_where_quadruple("dak_nf_laporan", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $j, "id_dak_nf", $tahun, "TAHUN_ANGGARAN");
						if($laporan->num_rows() > 0){
							$laporan = $laporan->result();
							$realisasi = $this->pm->get_where_double("dak_nf_rka", $laporan[0]->id_pengajuan, "id_pengajuan", $row2->id_menu, "id_menu_nf")->result();
							if($realisasi){
								$this->excel->getActiveSheet()->setCellValue($column.$b, number_format($realisasi[0]->realisasi));
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($column.$b, number_format(0));
							}
							
							$this->excel->getActiveSheet()->getStyle($column. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$column++;
							if($realisasi){
								$this->excel->getActiveSheet()->setCellValue($column.$b, $realisasi[0]->persentase);
								$column++;	
								$this->excel->getActiveSheet()->setCellValue($column.$b, number_format($realisasi[0]->fisik));
								$jumlah += $realisasi[0]->realisasi;
							}
							else{
								$this->excel->getActiveSheet()->setCellValue($column.$b, '0');
								$column++;	
								$this->excel->getActiveSheet()->setCellValue($column.$b, number_format(0));
							}
							
							
							$this->excel->getActiveSheet()->getStyle($column. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							$column++;	
							
						}
						else{
							$this->excel->getActiveSheet()->setCellValue($column.$b, '0');
							$column++;	
							$this->excel->getActiveSheet()->setCellValue($column.$b, '0');
							$column++;	
							$this->excel->getActiveSheet()->setCellValue($column.$b, '0');
							$column++;	
						}
						$this->excel->getActiveSheet()->setCellValue('B'.$b, number_format($pagu_s->pagu));
						$this->excel->getActiveSheet()->getStyle('B'. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->setCellValue($column.$b, number_format($jumlah));
						$this->excel->getActiveSheet()->getStyle($column. $b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						// $pagu_s += $pagu[0]->jumlah;
					}
					else{
						$this->excel->getActiveSheet()->setCellValue($column.$b, '0');
						$column++;	
					}
				}
			}
			$b++;
		}



		$filename='rekap_realisasi_menu '.$dak_nf->nama_dak_nf.'.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function view_presensi_nf(){
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		$kdsatker =  $this->session->userdata('kdsatker');
		$tahun = $this->session->userdata("thn_anggaran");
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}

		}
		$option_kabupaten['0'] = '-- Pilih Kabupaten --';
		foreach ($this->pm->get_data_kabupaten($kdprovinsi)->result() as $row) {
			$option_kabupaten[$row->KodeKabupaten] = $row->NamaKabupaten;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] ='Seluruh Indonesia ';
		$option_provinsi['98'] =' Seluruh Provinsi ';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get_where('dak_nf_kategori', $tahun, "TAHUN_ANGGARAN")->result() as $row){
			$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
		}	
		$option_jenis_dak['0'] = '-- Pilih subbidang    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata("thn_anggaran"), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_masalah['0'] = '-- Tidak Ada  --';

		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}

		$role = $this->session->userdata('kd_role');
		$data2['role']	= $role;	
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['option_kabupaten'] = $option_kabupaten;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;
		$data2['jenis_dak'] = $option_jenis_dak;
		$data['content'] = $this->load->view('metronic/e-monev/view_presensi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function print_presensi_nf(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$j=0;
		$kat=0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if($j == 0){
			redirect('e-monev/e_dak/view_presensi_nf	');
		}		
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

        $nama_file = $this->pm->get_where("dak_nf", $j, "id_dak_nf")->result();
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Presensi DAK Non Fisik');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Daftar Presensi');
		foreach (range('B', 'M') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(10);
		}
		$this->excel->getActiveSheet()->getStyle("A4:M4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A5:M5")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getStyle("A6:M6")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:M1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//header
		$this->excel->getActiveSheet()->setCellValue('A4', 'Kabupaten kota');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A4:A6');
		$this->excel->getActiveSheet()->setCellValue('B4', 'DAK Non Fisik');
		$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('B4:M4');


		$this->excel->getActiveSheet()->setCellValue('B5', 'Triwulan 1');
		$this->excel->getActiveSheet()->mergeCells('B5:D5');		
		$this->excel->getActiveSheet()->setCellValue('E5', 'Triwulan 2');
		$this->excel->getActiveSheet()->mergeCells('E5:G5');			
		$this->excel->getActiveSheet()->setCellValue('H5', 'Triwulan 3');
		$this->excel->getActiveSheet()->mergeCells('H5:J5');					
		$this->excel->getActiveSheet()->setCellValue('K5', 'Triwulan 4');
		$this->excel->getActiveSheet()->mergeCells('K5:M5');

		$this->excel->getActiveSheet()->setCellValue('B6', 'N');
		$this->excel->getActiveSheet()->setCellValue('C6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('D6', '%');		
		$this->excel->getActiveSheet()->setCellValue('E6', 'N');
		$this->excel->getActiveSheet()->setCellValue('F6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('G6', '%');
		$this->excel->getActiveSheet()->setCellValue('H6', 'N');
		$this->excel->getActiveSheet()->setCellValue('I6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('J6', '%');	
		$this->excel->getActiveSheet()->setCellValue('K6', 'N');
		$this->excel->getActiveSheet()->setCellValue('L6', 'Kumpul');
		$this->excel->getActiveSheet()->setCellValue('M6', '%');


		$tahun = $this->session->userdata("thn_anggaran");
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		
		$b = 7;
		// print_r($p); exit;
		if($p == 99){
			$where = array(
					'TAHUN_ANGGARAN' => $tahun,
					'id_dak_nf' => $j, 
					'pagu >' => 0
			);
			$column = 'B';
			$where = array(
					'TAHUN_ANGGARAN' => $tahun,
					'id_dak_nf' => $j, 
					// 'pagu >' => 0
			);
			$pagu = $this->bm->select_where_array('dak_nf_pagus', $where)->num_rows();
			$this->excel->getActiveSheet()->setCellValue('A'.$b, 'Seluruh Indonesia');
			for ($i=1; $i < 5; $i++) { 
				$where['waktu_laporan'] = $i ;
				$laporan = $this->bm->select_where_array('dak_nf_laporan', $where)->num_rows();
				$this->excel->getActiveSheet()->setCellValue($column.$b, $pagu);
				$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$column++;
				$this->excel->getActiveSheet()->setCellValue($column.$b, $laporan);
				$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$column++;
				if($pagu > 0){
					$persentase = round(($laporan / $pagu) * 100 , 2);
				}
				else{
					$persentase = 0;
				}
				$this->excel->getActiveSheet()->setCellValue($column.$b, $persentase."%");
				$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$column++;
			}


		}
		elseif($p == 98){
			$kabupaten = $this->bm->select_all('ref_provinsi');	
			foreach ($kabupaten as $row) {
				$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaProvinsi);
				$where = array(
					'TAHUN_ANGGARAN' => $tahun,
					'id_dak_nf' => $j,
					'KodeProvinsi' => $row->KodeProvinsi,
					'pagu >' => 0
				);
				$pagu = $this->bm->select_where_array('dak_nf_pagus', $where)->num_rows();
				$column = 'B';
				$where = array(
					'TAHUN_ANGGARAN' => $tahun,
					'id_dak_nf' => $j,
					'KodeProvinsi' => $row->KodeProvinsi,
					// 'pagu >' => 0
				);
				for($i = 1; $i<=4 ; $i++){
					$where['waktu_laporan'] = $i ;
					$laporan = $this->bm->select_where_array('dak_nf_laporan', $where)->num_rows();
					$this->excel->getActiveSheet()->setCellValue($column.$b, $pagu);
					$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$column++;
					$this->excel->getActiveSheet()->setCellValue($column.$b, $laporan);
					$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$column++;
					if($pagu > 0){
						$persentase = round(($laporan / $pagu) * 100 , 2);
					}
					else{
						$persentase = 0;
					}
					$this->excel->getActiveSheet()->setCellValue($column.$b, $persentase."%");
					$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$column++;
				}
				$b++;
			}
		}
		else{
			foreach ($kabupaten as $row) {
				$this->excel->getActiveSheet()->setCellValue('A'.$b, $row->NamaKabupaten);
				$pagu = $this->pm->get_where_triple("dak_nf_pagus", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $j, "id_dak_nf")->result();
				$column = 'B';
				for($i = 1; $i<=4 ; $i++){
					if($pagu != null){
						if($pagu[0]->pagu > 0){
							$temp = 1;
						}
						else{
							$temp =0;
						}
						$this->excel->getActiveSheet()->setCellValue($column.$b, $temp);
						$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$column++;
						$laporan = $this->pm->get_where_5("dak_nf_laporan", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", $j, "id_dak_nf", $tahun, "TAHUN_ANGGARAN", $i, "waktu_laporan")->num_rows();
						$this->excel->getActiveSheet()->setCellValue($column.$b, $laporan);
						$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$column++;
						$persentase = ($laporan / 1) * 100;
						$this->excel->getActiveSheet()->setCellValue($column.$b, $persentase."%");
						$this->excel->getActiveSheet()->getStyle($column.$b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$column++;
					}
					else{
						$this->excel->getActiveSheet()->setCellValue($column.$b, "0");
						$column++;
						$this->excel->getActiveSheet()->setCellValue($column.$b, "0");
						$column++;
						$this->excel->getActiveSheet()->setCellValue($column.$b, "0");
						$column++;
					}
				}
				$b++;
			}
		}
		
		
		if($nama_file == null){
			$filename='gagal.xls';
		}
		else{
			$filename='Presensi-'.$nama_file[0]->nama_dak_nf.'.xls';
		}
		 //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function proses_realisasi_nf(){
		$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh Indonesia';
		$option_provinsi['98'] = 'Seluruh Provinsi';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$tahun = $this->session->userdata("thn_anggaran");
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get_where('dak_nf_kategori', $tahun, "TAHUN_ANGGARAN")->result() as $row){
			$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
		}
		$role = $this->session->userdata("kd_role");
		if($role == 19){
			$unit = $this->session->userdata('kdunit');
			$tahun = $this->session->userdata('thn_anggaran');
			if($tahun == 2017){
				$option_kategori = array();
				if($unit == '07'){
					$option_kategori[0] = "-- Pilih Kategori--";
					$option_kategori[1] = "BOK"; 
				}
				else if($unit == '03'){
					$option_kategori[0] = "-- Pilih Kategori--";
					$option_kategori[1] = "BOK";
					$option_kategori[4] = "Jampersal";	
				}	
			}
			
		}

		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;

		$data['content'] = $this->load->view('metronic/e-monev/proses_realisasi_nf',$data2,true);
		$this->load->view(VIEWPATH,$data);
	}

	function table_realisasi_nf(){
    	if(isset($_POST["waktu_laporan"]) ){
	      	$provinsi=$this->input->post('provinsi');
	      	$kabupaten=$this->input->post('kabupaten');
	      	$jenis_dak=$this->input->post('jenis_dak');		
	      	$waktu=$this->input->post('waktu_laporan');
      	}
    	if(isset($_GET["waktu_laporan"]) ){
	      	$provinsi=$_GET["provinsi"]	;
	      	$kabupaten=$_GET["kabupaten"];
	      	$jenis_dak=$_GET["jenis_dak"]	;
	      	$waktu=$_GET["waktu_laporan"];
      	}
      	// print_r($provinsi);
      	// print_r($kabupaten);
      	// print_r($jenis_dak);
      	// exit();
      	$data['provinsi']=$provinsi;
      	$data['kabupaten']=$kabupaten;
      	$data['jenis_dak']=$jenis_dak;
      	$data['waktu']=$waktu;
		$this->load->view('e-monev/table_realisasi_nf',$data);
    }

    function tes_q(){
    	$i = $this->pm->get_proses_realisasi_provinsi(02, 1,1, 2017 )->result();
    	print_r($i);
    }

    function daftar_realisasi_nf2($p,$k,$j,$waktu){
    	$i=0;
		$no=1;
		
		$tahun = $this->session->userdata("thn_anggaran");
		if($p == 98){
			$provinsi = $this->pm->get('ref_provinsi')->result();
			foreach ($provinsi as $row ) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = "";
				$hasil2 = $this->pm->get_pagu_prov_nf2($row->KodeProvinsi, $j, $tahun)->result();
				// print_r($hasil2); exit();
				if($hasil2 != null){
					$hasil = $this->pm->get_proses_realisasi_provinsi($row->KodeProvinsi, $j, $waktu, $tahun)->result();
					if($hasil[0] !=  null && $hasil[0]->realisasi > 0 && $hasil2[0] !=  null && $hasil2[0]->pagu > 0){
						$pagu = $hasil2[0]->pagu;
						$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
						if($pagu > 0){
							$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
							$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
							$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
						}
						else{
							$datajson[$i]['PAGU']  = 0;
							$datajson[$i]['PERSENTASE'] = 0;
							$datajson[$i]['REALISASI_FISIK'] = 0;
						}
						
					}
					else{
						$datajson[$i]['PAGU'] =  $hasil2[0]->pagu;
						$datajson[$i]['REALISASI'] = 0;
						$datajson[$i]['PERSENTASE'] = 0 ;
						$datajson[$i]['REALISASI_FISIK'] =0;	
					}
				}
				else{
						$datajson[$i]['REALISASI'] = 0;
						$datajson[$i]['PAGU']  =0;
						$datajson[$i]['PERSENTASE'] = 0 ;
						$datajson[$i]['REALISASI_FISIK'] =0;
				}

				$i++;
				$no++;
			}	
		}
		else if($p == 99){
			$datajson[$i]['NO'] = $no;
			$datajson[$i]['PROVINSI'] = "Indonesia";
			$datajson[$i]['KABUPATEN'] = "";
			$hasil2 = $this->pm->get_pagu_prov_nf2(0, $j, $tahun)->result();
			if($hasil2 != null){
				$hasil = $this->pm->get_proses_realisasi_provinsi(0, $j, $waktu, $tahun)->result();
				if($hasil[0] !=  null && $hasil[0]->realisasi > 0 && $hasil2[0] && $hasil2[0]->pagu > 0){
					$pagu = $hasil2[0]->pagu;
					$datajson[$i]['REALISASI'] = $real = $hasil[0]->realisasi;
					if($pagu > 0){
						$datajson[$i]['PAGU']  = $hasil2[0]->pagu;
						$datajson[$i]['PERSENTASE'] = round(($real*100)/$pagu,2) ;
						$datajson[$i]['REALISASI_FISIK'] = round($hasil[0]->fisik,2);
					}
					else{
						$datajson[$i]['PAGU']  = 0;
						$datajson[$i]['PERSENTASE'] = 0;
						$datajson[$i]['REALISASI_FISIK'] = 0;
					}
						
				}
				else{
						$datajson[$i]['PAGU'] =  $hasil2[0]->pagu;
						$datajson[$i]['REALISASI'] = 0;
						$datajson[$i]['PERSENTASE'] = 0 ;
						$datajson[$i]['REALISASI_FISIK'] =0;	
				}
			}
			else{
				$datajson[$i]['REALISASI'] = 0;
				$datajson[$i]['PAGU']  =0;
				$datajson[$i]['PERSENTASE'] = 0 ;
				$datajson[$i]['REALISASI_FISIK'] =0;
			}
			$i++;
		}
		else{
			$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
			// print_r($kabupaten);
			foreach ($kabupaten as $row) {
				$datajson[$i]['NO'] = $no;
				$datajson[$i]['PROVINSI'] = $row->NamaProvinsi;
				$datajson[$i]['KABUPATEN'] = $row->NamaKabupaten;
				$hasil2 = $this->pm->get_pagu_kab_nf2($row->KodeProvinsi, $row->KodeKabupaten, $j, $tahun)->result();
				if($hasil2 != null){
					if($hasil2[0]->pagu != null){
						$pagu = $hasil2[0]->pagu;
						$datajson[$i]['PAGU'] =$pagu;
					}
					else{
						$datajson[$i]['PAGU'] =0;
						$pagu = 0;
					}
					$hasil = $this->pm->get_proses_realisasi_kabupaten($row->KodeProvinsi, $row->KodeKabupaten, $j, $waktu, $tahun)->result();
					if($hasil != null){
						if($pagu > 0){
							$persentase = round(($hasil[0]->realisasi*100)/$pagu,2) ;
						}
						else{
							$persentase = 0;
						}
						if($hasil[0]->realisasi != null || $hasil[0]->fisik != null){
							$datajson[$i]['REALISASI'] = $hasil[0]->realisasi;
							$datajson[$i]['PERSENTASE'] = $persentase ;
							$datajson[$i]['REALISASI_FISIK'] =$hasil[0]->fisik;							
						}
						else{
							$datajson[$i]['REALISASI'] = 0;
							$datajson[$i]['PERSENTASE'] = 0 ;
							$datajson[$i]['REALISASI_FISIK'] =0;		
						}

					}
					else{
						$datajson[$i]['REALISASI'] = 0;
						$datajson[$i]['PERSENTASE'] = 0 ;
						$datajson[$i]['REALISASI_FISIK'] =0;	
					}
				}
				else{
						$datajson[$i]['REALISASI'] = 0;
						$datajson[$i]['PAGU'] =0;
						$datajson[$i]['PERSENTASE'] = 0 ;
						$datajson[$i]['REALISASI_FISIK'] =0;
				}
				
				$i++;
				$no++;
			}
		}
		echo json_encode($datajson);
    }

    function tes_graphic(){
    	$kdsatker=$this->session->userdata('kdsatker');
		$kdprovinsi=$this->session->userdata('kodeprovinsi');
		$kdkabupaten=$this->session->userdata('kodekabupaten');
		if($kdsatker!=NULL){
			foreach($this->pm->get_data_provinsi($kdprovinsi)->result() as $row){
				$selected_state = $row->NamaProvinsi;
			}
			foreach ($this->pm->get_kabupaten_detail($kdprovinsi,$kdkabupaten)->result() as $row){
				$selected_kabupaten=$row->NamaKabupaten;
			}

		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		$option_provinsi['99'] = 'Seluruh Indonesia';
		$option_provinsi['98'] = 'Seluruh Provinsi';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}
		$tahun = $this->session->userdata("thn_anggaran");
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get_where('dak_nf_kategori', $tahun, "TAHUN_ANGGARAN")->result() as $row){
			$option_kategori[$row->id_kategori_nf] = $row->nama_kategori;
		}	
		$option_masalah['0'] = '-- Tidak Ada  --';
		foreach ($this->pm->get('permasalahan_dak')->result() as $row){
			$option_masalah[$row->KodeMasalah] = $row->Masalah;
		}	
		$data2['role']	= $this->session->userdata('kd_role');				
		$data2['nama'] = "tes";
		$data2['option_provinsi'] = $option_provinsi;
		$data2['provinsi'] = $selected_state;
		$data2['KodeProvinsi'] = $kdprovinsi;
		$data2['kabupaten'] = $selected_kabupaten;
		$data2['KodeKabupaten'] = $kdkabupaten;
		$data2['kategori'] = $option_kategori;
		$data2['masalah'] = $option_masalah;

		$data['content'] = $this->load->view('metronic/e-monev/graphic',$data2,true);
		$this->load->view(VIEWPATH,$data);
    }

    function realisasi_dashboard($tw){
    	$tahun = $this->session->userdata('thn_anggaran');
    	$jenis_dak = $this->pm->get_where('dak_jenis_dak', $tahun, "TAHUN_ANGGARAN")->result();
    	$i = 0;
    	foreach ($jenis_dak as $row) {
    		$pagu = $this->pm->get_pagu_dashboard($row->ID_JENIS_DAK, $tahun)->result();
    		$realisasi = $this->pm->get_realisasi_dashboard($row->ID_JENIS_DAK,$tw,$tahun)->result();
    		$datajson[$i]['DAK'] = $row->NAMA_JENIS_DAK;
    		$datajson[$i]['PAGU'] = $pagu[0]->pagu/1000000000;
    		$datajson[$i]['REALISASI'] = $realisasi[0]->realisasi/1000000000;

    		$i++;
    		
    	}

    	echo json_encode($datajson);
    }
    function absensi_dashboard($tw){
    	$tahun = $this->session->userdata('thn_anggaran');
    	$jenis_dak = $this->pm->get_where('dak_jenis_dak', $tahun, "TAHUN_ANGGARAN")->result();
    	
    	$n_rs = $this->pm->get_n_dashboard_dak_rs()->result();
    	$n_puskes = $this->pm->get_n_dashboard_dak_puskes()->result();
    	$n_non_rs = $this->pm->get_n_non_rs()->result();
    	$i = 0;
    	foreach ($jenis_dak as $row) {
    		$k = $this->pm->get_where_triple("pengajuan_monev_dak", $row->ID_JENIS_DAK, "ID_SUBBIDANG", $tahun, "TAHUN_ANGGARAN", $tw, "WAKTU_LAPORAN")->num_rows();
    		$datajson[$i]['K'] = $k;
    		if($row->ID_JENIS_DAK == 1 || $row->ID_JENIS_DAK == 8){
    			$datajson[$i]['DAK'] = $row->NAMA_JENIS_DAK;
    			foreach ($n_rs as $row2) {
    				if($row2->ID_Jenis_DAK == $row->ID_JENIS_DAK){
    					$datajson[$i]['N'] = number_format($row2->n);
    					
    				} 
    			}
    		}
    		else if( $row->ID_JENIS_DAK == 9){
    			$datajson[$i]['DAK'] = $row->NAMA_JENIS_DAK;
    			$datajson[$i]['N'] = $n_puskes[0]->n;
    			// $datajson[$i]['K'] = 10;
    		}
    		else{
    			$datajson[$i]['DAK'] = $row->NAMA_JENIS_DAK;
    			foreach ($n_non_rs as $row2) {
    				if($row->ID_JENIS_DAK == $row2->ID_SUBBIDANG){
    					$datajson[$i]['N'] = $row2->n;
    				}
    			}
    		}
    		$i++;
    	}
    	echo json_encode($datajson);
    }

    function print_dak_2016_fisik(){
    	if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["j"]))$j=$_GET["j"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Laporan Realisasi');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi DAK Fisik Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		// RUJUKAN
		$this->excel->getActiveSheet()->setCellValue('C6', 'RUJUKAN' );
		$this->excel->getActiveSheet()->setCellValue('C7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('D7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('E7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('C6:E6');
		// FARMASI
		$this->excel->getActiveSheet()->setCellValue('F6', 'FARMASI' );
		$this->excel->getActiveSheet()->setCellValue('F7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('G7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('H7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('F6:H6');
		// DASAR
		$this->excel->getActiveSheet()->setCellValue('I6', 'DASAR' );
		$this->excel->getActiveSheet()->setCellValue('I7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('J7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('K7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('I6:K6');
		// SARPRAS
		$this->excel->getActiveSheet()->setCellValue('L6', 'SARPRAS' );
		$this->excel->getActiveSheet()->setCellValue('L7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('M7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('N7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('L6:N6');
		// SARRAS RUJUKAN
		$this->excel->getActiveSheet()->setCellValue('O6', 'DAK TAMBAHAN' );
		$this->excel->getActiveSheet()->setCellValue('O7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('P7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('Q7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('O6:Q6');


		$this->excel->getActiveSheet()->setCellValue('R6', 'JUMLAH' );
		$this->excel->getActiveSheet()->mergeCells('R6:R7');

		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		foreach (range('A', 'R') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(25);
		}
		$this->excel->getActiveSheet()->getStyle("A6:R7")->applyFromArray($styleArrayHead);
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		$i=8;
		foreach ($kabupaten as $row) {
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$pagu_seluruh = 0;
			$realisasi_seluruh = 0;
			$pagu = $this->pm->get_where_double("data_pagu", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($pagu != null){
				if($pagu[0]->Rujukan != 0 && $pagu[0]->Rujukan != null){
					$this->excel->getActiveSheet()->setCellValue('C'.$i, $pagu[0]->Rujukan);
					$pagu_seluruh += $pagu[0]->Rujukan;
					$laporan_r = array();
					$laporan_rujukan = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 1, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_rujukan != null){
						foreach ($laporan_rujukan as $row2) {
							array_push($laporan_r, $row2->ID_LAPORAN_DAK);
						}
					}
					$laporan_sarpras_rujukan = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 6, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_sarpras_rujukan != null){
						foreach ($laporan_sarpras_rujukan as $row2) {
							array_push($laporan_r, $row2->ID_LAPORAN_DAK);
						}
					}
					if($laporan_r){
						$rka = $this->pm->get_realisasi_fisik($laporan_r)->result();
						if($rka){
							$this->excel->getActiveSheet()->setCellValue('D'.$i, $rka[0]->realisasi );
							$realisasi_seluruh += $rka[0]->realisasi;
							$this->excel->getActiveSheet()->setCellValue('E'.$i, $rka[0]->fisik );
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('D'.$i, "0" );
							$this->excel->getActiveSheet()->setCellValue('E'.$i, "0");
						}
					}
					else{
						$this->excel->getActiveSheet()->setCellValue('D'.$i, "0" );
						$this->excel->getActiveSheet()->setCellValue('E'.$i, "0");
					}


				}
				else{
					$this->excel->getActiveSheet()->setCellValue('C'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('D'.$i, "0" );
					$this->excel->getActiveSheet()->setCellValue('E'.$i, "0");
				}

				if($pagu[0]->Farmasi != 0 && $pagu[0]->Farmasi != null){
					$this->excel->getActiveSheet()->setCellValue('F'.$i, $pagu[0]->Farmasi);
					$pagu_seluruh += $pagu[0]->Farmasi;
					$laporan_farmasi = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 2, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_farmasi){
						$rka=$this->pm->get_realisasi_fisik($laporan_farmasi[0]->ID_LAPORAN_DAK)->result();
						$realisasi_seluruh += $rka[0]->realisasi;
						$this->excel->getActiveSheet()->setCellValue('G'.$i, $rka[0]->realisasi);
						$this->excel->getActiveSheet()->setCellValue('H'.$i, $rka[0]->fisik);
					}
					else{
						$this->excel->getActiveSheet()->setCellValue('G'.$i, '0');
						$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');	
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('F'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('G'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');	
				}
				if($pagu[0]->Pelayanan_Dasar != 0 && $pagu[0]->Pelayanan_Dasar != null){
					$this->excel->getActiveSheet()->setCellValue('I'.$i, $pagu[0]->Pelayanan_Dasar);
					$pagu_seluruh += $pagu[0]->Pelayanan_Dasar;
					$laporan_dasar =  $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 3, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_dasar){
						$rka=$this->pm->get_realisasi_fisik($laporan_dasar[0]->ID_LAPORAN_DAK)->result();
						$this->excel->getActiveSheet()->setCellValue('J'.$i, $rka[0]->realisasi);
						$realisasi_seluruh += $rka[0]->realisasi;
						$this->excel->getActiveSheet()->setCellValue('K'.$i, $rka[0]->fisik);
					}
					else{
						$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');
						$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');	
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('I'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');
				}
				if($pagu[0]->Sarpras != 0 && $pagu[0]->Sarpras != null){
					$laporan_s = array();
					$this->excel->getActiveSheet()->setCellValue('L'.$i, $pagu[0]->Sarpras);
					$pagu_seluruh += $pagu[0]->Sarpras;
					$laporan_sarpras = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 4, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					$laporan_sarpras_rujukan = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 5, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_sarpras){
						foreach ($laporan_sarpras as $row2) {
							array_push($laporan_s, $row2->ID_LAPORAN_DAK);
						}
					}
					if($laporan_sarpras_rujukan){
						foreach ($laporan_sarpras_rujukan as $row2) {
							array_push($laporan_s, $row2->ID_LAPORAN_DAK);
						}
					}
					if($laporan_s){
						$rka=$this->pm->get_realisasi_fisik($laporan_s)->result();
						$this->excel->getActiveSheet()->setCellValue('M'.$i, $rka[0]->realisasi);
						$realisasi_seluruh += $rka[0]->realisasi;
						$this->excel->getActiveSheet()->setCellValue('N'.$i, $rka[0]->fisik);
					}
					else{
						$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');
						$this->excel->getActiveSheet()->setCellValue('N'.$i, '0');	
					}

				}
				else{
					$this->excel->getActiveSheet()->setCellValue('L'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('N'.$i, '0');
				}
				if($pagu[0]->Tambahan_Dak_Kesehatan != 0 && $pagu[0]->Tambahan_Dak_Kesehatan != null){
					$this->excel->getActiveSheet()->setCellValue('O'.$i, $pagu[0]->Tambahan_Dak_Kesehatan);
					$pagu_seluruh += $pagu[0]->Tambahan_Dak_Kesehatan;
					$laporan_tambahan = $this->pm->get_where_quadruple("dak_laporan", $row->KodeProvinsi , "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten", 7, "JENIS_DAK", $w, "WAKTU_LAPORAN")->result();
					if($laporan_tambahan){
						$rka=$this->pm->get_realisasi_fisik($laporan_tambahan[0]->ID_LAPORAN_DAK)->result();
						$this->excel->getActiveSheet()->setCellValue('P'.$i, $rka[0]->realisasi);
						$realisasi_seluruh += $rka[0]->realisasi;
						$this->excel->getActiveSheet()->setCellValue('Q'.$i, $rka[0]->fisik);
					}
					else{
						$this->excel->getActiveSheet()->setCellValue('P'.$i, '0');
						$this->excel->getActiveSheet()->setCellValue('Q'.$i, '0');
					}

				}
				else{
					$this->excel->getActiveSheet()->setCellValue('O'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('P'.$i, '0');
					$this->excel->getActiveSheet()->setCellValue('Q'.$i, '0');
				}

			}
			else{
				$this->excel->getActiveSheet()->setCellValue('C'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('F'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('I'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('L'.$i, '0');
				$this->excel->getActiveSheet()->setCellValue('O'.$i, '0');
			}
			$this->excel->getActiveSheet()->setCellValue('B'.$i, $pagu_seluruh);
			$this->excel->getActiveSheet()->setCellValue('R'.$i, $realisasi_seluruh);
			$i++;
		}
		
		$filename='rekap_realisasi_menu_fisik.xlsx'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');


    }

     function print_dak_2016_nf(){
    	$k=0;
		$p=0;
		$j=0;
		$w=0;
		$tahun = $this->session->userdata("thn_anggaran");
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["w"]))$w=$_GET["w"];
		if($k == 0 ){
			$kabupaten = $this->pm->get_data_kabupaten($p)->result();
		}
		else{
			$kabupaten = $this->pm->get_where_double("ref_kabupaten", $p , "KodeProvinsi", $k, "KodeKabupaten")->result();
		}
		$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN), 'color' => array('rgb'=> '3598dc')), 'font'  => array('bold' => true, 'color' => array('rgb' => 'ffffff'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '3598dc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'wrap' => true), );
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Kelengkapan Laporan');
		$this->excel->getActiveSheet()->setCellValue('A1', 'Realisasi Menu NF Triwulan ' . $w );	
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->excel->getActiveSheet()->setCellValue('A6', 'Nama Kabupaten / Kota' );
		$this->excel->getActiveSheet()->setCellValue('B6', 'PAGU' );
		// BOK
		$this->excel->getActiveSheet()->setCellValue('C6', 'BOK' );
		$this->excel->getActiveSheet()->setCellValue('C7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('D7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('E7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('C6:E6');
		//A RS
		$this->excel->getActiveSheet()->setCellValue('F6', 'JAMINAN PERSALINAN' );
		$this->excel->getActiveSheet()->setCellValue('F7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('G7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('H7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('F6:H6');
		// // A PUS
		$this->excel->getActiveSheet()->setCellValue('I6', 'AKREDITASI RUMAH SAKIT' );
		$this->excel->getActiveSheet()->setCellValue('I7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('J7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('K7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('I6:K6');
		// // JAMPER
		$this->excel->getActiveSheet()->setCellValue('L6', 'AKREDITASI PUSKESMAS' );
		$this->excel->getActiveSheet()->setCellValue('L7', 'Pagu' );
		$this->excel->getActiveSheet()->setCellValue('M7', 'Realisasi' );
		$this->excel->getActiveSheet()->setCellValue('N7', 'Fisik' );
		$this->excel->getActiveSheet()->mergeCells('L6:N6');
		// //JUMLAH
		$this->excel->getActiveSheet()->setCellValue('O6', 'JUMLAH' );
		$this->excel->getActiveSheet()->mergeCells('O6:O7');

		$this->excel->getActiveSheet()->mergeCells('A6:A7');
		$this->excel->getActiveSheet()->mergeCells('B6:B7');
		foreach (range('A', 'O') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(25);
		}
		$this->excel->getActiveSheet()->getStyle("A6:O7")->applyFromArray($styleArrayHead);
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();

		$i= 8;
		foreach ($kabupaten as $row) {
			$jumlah = 0;
			$pagus = 0;
			$this->excel->getActiveSheet()->setCellValue('A'.$i, $row->NamaKabupaten);
			$pagu = $this->pm->get_where_double("data_pagu_nf", $row->KodeProvinsi, "KodeProvinsi", $row->KodeKabupaten, "KodeKabupaten")->result();
			if($pagu != null){
				$laporan = $this->pm->get_where_triple("dak_laporan_nf", $row->KodeProvinsi, "KodeProvinsi",$row->KodeKabupaten, "KodeKabupaten", $w, "WAKTU_LAPORAN")->result();
				if($pagu[0]->BANTUAN_OPERASIONAL_KESEHATAN != null){
					$pagus+=$pagu[0]->BANTUAN_OPERASIONAL_KESEHATAN;
					$this->excel->getActiveSheet()->setCellValue('C'. $i, $pagu[0]->BANTUAN_OPERASIONAL_KESEHATAN);
					if($laporan != null){
						$kegiatan = $this->pm->get_where_double("dak_kegiatan_nf", $laporan[0]->ID_LAPORAN_DAK,"ID_LAPORAN_DAK" , 1, "ID_JENIS_DAK")->result();
						
						if($kegiatan != null){
							$this->excel->getActiveSheet()->setCellValue('D'.$i, $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN);
							$jumlah+= $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN;
							$this->excel->getActiveSheet()->setCellValue('E'.$i, $kegiatan[0]->REALISASI_FISIK_PELAKSANAAN);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('D'.$i, '0');
							$this->excel->getActiveSheet()->setCellValue('E'.$i, '0');
						}
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('C'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('D'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('E'. $i, '0');

				}
				if($pagu[0]->JAMINAN_PERSALINAN != null && $pagu[0]->JAMINAN_PERSALINAN != 0){
					$pagus+=$pagu[0]->JAMINAN_PERSALINAN;
					$this->excel->getActiveSheet()->setCellValue('F'. $i, $pagu[0]->JAMINAN_PERSALINAN);
					if($laporan != null){
						$kegiatan = $this->pm->get_where_double("dak_kegiatan_nf", $laporan[0]->ID_LAPORAN_DAK,"ID_LAPORAN_DAK", 2, "ID_JENIS_DAK")->result();
						if($kegiatan != null){
							$this->excel->getActiveSheet()->setCellValue('G'.$i, $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN);
							$jumlah+= $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN;
							$this->excel->getActiveSheet()->setCellValue('H'.$i, $kegiatan[0]->REALISASI_FISIK_PELAKSANAAN);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('G'.$i, '0');
							$this->excel->getActiveSheet()->setCellValue('H'.$i, '0');
						}
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('F'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('G'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('H'. $i, '0');

				}
				if($pagu[0]->AKREDITASI_RUMAH_SAKIT != null && $pagu[0]->AKREDITASI_RUMAH_SAKIT != 0){
					$this->excel->getActiveSheet()->setCellValue('I'. $i, $pagu[0]->AKREDITASI_RUMAH_SAKIT);
					$pagus+=$pagu[0]->AKREDITASI_RUMAH_SAKIT;
					if($laporan != null){
						$kegiatan = $this->pm->get_where_double("dak_kegiatan_nf", $laporan[0]->ID_LAPORAN_DAK,"ID_LAPORAN_DAK", 3, "ID_JENIS_DAK")->result();
						if($kegiatan != null){
							$this->excel->getActiveSheet()->setCellValue('J'.$i, $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN);
							$jumlah+= $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN;
							$this->excel->getActiveSheet()->setCellValue('K'.$i, $kegiatan[0]->REALISASI_FISIK_PELAKSANAAN);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('J'.$i, '0');
							$this->excel->getActiveSheet()->setCellValue('K'.$i, '0');
						}
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('I'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('J'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('K'. $i, '0');

				}
				if($pagu[0]->AKREDITASI_PUSKESMAS != null && $pagu[0]->AKREDITASI_PUSKESMAS != 0){
					$this->excel->getActiveSheet()->setCellValue('L'. $i, $pagu[0]->BANTUAN_OPERASIONAL_KESEHATAN);
					$pagus+=$pagu[0]->JAMINAN_PERSALINAN;
					if($laporan != null){
						$kegiatan = $this->pm->get_where_double("dak_kegiatan_nf", $laporan[0]->ID_LAPORAN_DAK,"ID_LAPORAN_DAK", 1, "ID_JENIS_DAK")->result();
						if($kegiatan != null){
							$this->excel->getActiveSheet()->setCellValue('M'.$i, $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN);
							$jumlah+= $kegiatan[0]->REALISASI_KEUANGAN_PELAKSANAAN;
							$this->excel->getActiveSheet()->setCellValue('N'.$i, $kegiatan[0]->REALISASI_FISIK_PELAKSANAAN);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('M'.$i, '0');
							$this->excel->getActiveSheet()->setCellValue('N'.$i, '0');
						}
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('L'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('M'. $i, '0');
					$this->excel->getActiveSheet()->setCellValue('N'. $i, '0');

				}

			}
			else{
				$this->excel->getActiveSheet()->setCellValue('C'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('D'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('E'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('F'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('G'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('H'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('I'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('J'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('K'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('L'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('M'. $i, '0');
				$this->excel->getActiveSheet()->setCellValue('N'. $i, '0');

			}
			$this->excel->getActiveSheet()->setCellValue('O'. $i, $jumlah);
			$this->excel->getActiveSheet()->setCellValue('B'. $i, $pagus);
			$i++;

		}

		$filename='rekap_realisasi_menu_nf.xlsx'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');


    }

    function generate_tb_dak_realisasi(){
    	$hasil = $this->pm->generate_report()->result();
    	$data = array();
    	foreach ($hasil as $key => $value) {
    		$temp = array(
    			'KodeProvinsi' => $value->KodeProvinsi,
    			'KodeKabupaten' => $value->KodeKabupaten,
    			'KODE_RS' => $value->KODE_RS,
    			'NamaKabupaten' => $value->NamaKabupaten,
    			'ID_SUBBIDANG' => $value->ID_SUBBIDANG,
    			'waktu_laporan' => $value->waktu_laporan,
    			'ID_MENU' => $value->ID_MENU,
    			'VOLUME' => $value->VOLUME,
    			'PAGU' => $value->PAGU,
    			'perubahan' => $value->perubahan,
    			'realisasi' => $value->realisasi,
    			'persentase' => $value->persentase,
    			'fisik' => $value->fisik

    		);
    		array_push($data, $temp);
    	}
    	if($data){
    		$this->db->truncate('dak_realisasi'); 
    		$this->db->insert_batch('dak_realisasi', $data);
    		echo "Sukses generate tabel";
    	}
    }
    function print_absensi_2018(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		$jd = 0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["kat"]))$kat=$_GET["kat"];
		if(isset($_GET["jd"]))$jd=$_GET["jd"];
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $tahun = $this->session->userdata('thn_anggaran');
        $jenis_dak = $this->pm->get_where_double('dak_jenis_dak', $tahun, 'TAHUN_ANGGARAN', $jd, 'ID_JENIS_DAK')->row();
        $kategori = $this->pm->get_where('kategori', $kat, 'ID_KATEGORI')->row();
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('cetak_absensi');
		$this->excel->getActiveSheet()->setCellValue('A1', strtoupper('Absensi Monev '. $kategori->NAMA_KATEGORI . '-' . $jenis_dak->NAMA_JENIS_DAK));
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:H1');
		foreach (range('B', 'H') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(15);
		}
		$this->excel->getActiveSheet()->getStyle("A4:H4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Nama Provinsi');
		$this->excel->getActiveSheet()->setCellValue('C4', 'Nama Kabupaten');
		$this->excel->getActiveSheet()->setCellValue('D4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$menu = $this->pm->get_where_double('menu', $tahun, 'TAHUN', $jd, 'ID_SUBBIDANG')->result();
		$col ='E';
		for ($i=1; $i<=4  ; $i++) { 
			$this->excel->getActiveSheet()->setCellValue($col.'4', 'Triwulan '.$i);
			$col++;
		}
		// $p ='02';
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$tahun = $this->session->userdata('thn_anggaran');
		//provinsi biasa
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		//seluruh indonesia
		if($p == '99'){
			$kabupaten = array();
			$kabupaten[0]['NamaProvinsi'] = 'Seluruh Indonesia';
			$kabupaten[0]['NamaKabupaten'] = 'Seluruh Indonesia';
			$kabupaten[0] = (object) $kabupaten[0];
		}
		if($p == '98'){
			$kabupaten = $this->bm->select_all('ref_provinsi');
		}
		$kabupaten = (object) $kabupaten;
		// print_r($kabupaten); exit();
		$b =5;
		$no =1;
		foreach ($kabupaten as $key => $value) {
			$col = 'E';
			$this->excel->getActiveSheet()->setCellValue('A'.$b, $no);
			if($p != 99){
				$NamaProvinsi = $this->pm->get_where('ref_provinsi', $value->KodeProvinsi, 'KodeProvinsi')->row()->NamaProvinsi;
			}
			else{
				$NamaProvinsi = $value->NamaProvinsi;
			}
			if(!isset($value->NamaKabupaten)){
				$value->NamaKabupaten ='';
			}
			$this->excel->getActiveSheet()->setCellValue('B'.$b, $NamaProvinsi);
			$this->excel->getActiveSheet()->setCellValue('C'.$b, $value->NamaKabupaten);
			if(!isset($value->KodeKabupaten)){
				$value->KodeKabupaten = 0;
			}
			$where = array(
							'KodeProvinsi' => $value->KodeProvinsi,
							'KodeKabupaten' => $value->KodeKabupaten,
							'TAHUN_ANGGARAN' => $tahun,
							'ID_SUBBIDANG' => $jd
						);
			if($p == '99'){
				unset($where['KodeProvinsi']);
				unset($where['KodeKabupaten']);
			}
			if($p == '98'){
				unset($where['KodeKabupaten']);
			}
			$pagu = $this->bm->select_where_array('pagu_seluruh', $where)->num_rows();
			$this->excel->getActiveSheet()->setCellValue('D'.$b, $pagu);
			if($pagu > 0){
				for ($i=1; $i <=4 ; $i++) { 
					$this->excel->getActiveSheet()->setCellValue($col.$b, 'Triwulan '.$i);
					$where['waktu_laporan'] = $i;
					$absen = $this->bm->select_where_array('pengajuan_monev_dak', $where)->num_rows();
					$this->excel->getActiveSheet()->setCellValue($col.$b, $absen);
					$col++;
				}
			}
			$b++;
			$no++;
		}
		$this->excel->getActiveSheet()->getStyle('A5:' .'H' . $b)->applyFromArray($styleArray);
		$filename='Absensi-Monev-'.$jenis_dak->NAMA_JENIS_DAK.'.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function print_absensi_rujukan_2018(){
		$d=0;
		$k='00';
		$p=0;
		$s=4;
		$kat=0;
		$jd = 0;
		if(isset($_GET["k"]))$k=$_GET["k"];
		if(isset($_GET["p"]))$p=$_GET["p"];
		if(isset($_GET["s"]))$s=$_GET["s"];
		if(isset($_GET["kat"]))$kat=$_GET["kat"];
		if(isset($_GET["jd"]))$jd=$_GET["jd"];
    	$styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
        $styleArrayHead = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size'  => 10, 'name' => 'Verdana'), 'fill'=> array('type' => PHPExcel_Style_Fill::FILL_SOLID,'color' => array('rgb' => '#99cccc')), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $styleArray2 = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)), 'font'  => array('bold' => true, 'color' => array('rgb' => '000000'), 'size'  => 10, 'name' => 'Verdana'));
        $tahun = $this->session->userdata('thn_anggaran');
        $jenis_dak = $this->pm->get_where_double('dak_jenis_dak', $tahun, 'TAHUN_ANGGARAN', $jd, 'ID_JENIS_DAK')->row();
        $kategori = $this->pm->get_where('kategori', $kat, 'ID_KATEGORI')->row();
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle($jenis_dak->NAMA_JENIS_DAK);
		$this->excel->getActiveSheet()->setCellValue('A1', strtoupper('Absensi Monev '. $kategori->NAMA_KATEGORI . '-' . $jenis_dak->NAMA_JENIS_DAK));
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->mergeCells('A1:I1');
		foreach (range('B', 'H') as $char) {
			$this->excel->getActiveSheet()->getColumnDimension($char)->setWidth(15);
		}
		$this->excel->getActiveSheet()->getStyle("A4:I4")->applyFromArray($styleArrayHead);
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Nama Provinsi');
		$this->excel->getActiveSheet()->setCellValue('C4', 'Nama Kabupaten');
		if($jd == 9){
			$this->excel->getActiveSheet()->setCellValue('D4', 'Rumah Sakit');
		}
		else{
			$this->excel->getActiveSheet()->setCellValue('D4', 'Puskesmas');
		}
		$this->excel->getActiveSheet()->setCellValue('E4', 'Pagu');
		$this->excel->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
		$menu = $this->pm->get_where_double('menu', $tahun, 'TAHUN', $jd, 'ID_SUBBIDANG')->result();
		$col ='F';
		for ($i=1; $i<=4  ; $i++) { 
			$this->excel->getActiveSheet()->setCellValue($col.'4', 'Triwulan '.$i);
			$col++;
		}
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$tahun = $this->session->userdata('thn_anggaran');
		//provinsi biasa
		$kabupaten = $this->pm->get_data_kabupaten2($p, $k)->result();
		//seluruh indonesia
		if($p == '99'){
			$kabupaten = array();
			$kabupaten[0]['NamaProvinsi'] = 'Seluruh Indonesia';
			$kabupaten[0]['NamaKabupaten'] = 'Seluruh Indonesia';
			$kabupaten[0] = (object) $kabupaten[0];
		}
		$kabupaten = (object) $kabupaten;
		$b =5;
		$no =1;
		$col ='F';
		if($p == '99'){
			$whereP = array(
				'TAHUN_ANGGARAN' => $tahun,
				'ID_Jenis_DAK' => $jd
			);
			foreach ($kabupaten as $key => $value) {
				$this->excel->getActiveSheet()->setCellValue('A'.$b, $no);
				$this->excel->getActiveSheet()->setCellValue('B'.$b, $value->NamaKabupaten);
				$this->excel->getActiveSheet()->setCellValue('C'.$b, $value->NamaKabupaten);
				$this->excel->getActiveSheet()->setCellValue('D'.$b, $value->NamaKabupaten);
				// print_r($whereP); exit();
				$pagu = $this->bm->select_where_array('pagu_rs', $whereP)->num_rows();
				$this->excel->getActiveSheet()->setCellValue('E'.$b, $pagu);

			}
			for ($i=1; $i<=4  ; $i++) { 
				unset($whereP['ID_Jenis_DAK']);
				$whereP['ID_SUBBIDANG'] = $jd;
				$whereP['waktu_laporan'] = $i;
				$absen = $this->bm->select_where_array('pengajuan_monev_dak', $whereP)->num_rows();
				$this->excel->getActiveSheet()->setCellValue($col.$b, $absen);
				$col++;
			}
			
		}
		elseif($p == '98'){
			$kabupaten = $this->bm->select_all('ref_provinsi');
			foreach ($kabupaten as $key => $value) {
				$col ='F';
				$this->excel->getActiveSheet()->setCellValue('A'.$b, $no);
				$no++;
				$this->excel->getActiveSheet()->setCellValue('B'.$b, $value->NamaProvinsi);
				$this->excel->getActiveSheet()->setCellValue('C'.$b, '');
				$this->excel->getActiveSheet()->setCellValue('D'.$b, '');
				// print_r($whereP); exit();
				$whereP = array(
					'TAHUN_ANGGARAN' => $tahun,
					'ID_Jenis_DAK' => $jd,
					'KodeProvinsi' => $value->KodeProvinsi
				);
				$pagu = $this->bm->select_where_array('pagu_rs', $whereP)->num_rows();
				$this->excel->getActiveSheet()->setCellValue('E'.$b, $pagu);
				for ($i=1; $i<=4  ; $i++) { 
					unset($whereP['ID_Jenis_DAK']);
					$whereP['ID_SUBBIDANG'] = $jd;
					$whereP['waktu_laporan'] = $i;
					$absen = $this->bm->select_where_array('pengajuan_monev_dak', $whereP)->num_rows();
					$this->excel->getActiveSheet()->setCellValue($col.$b, $absen);
					$col++;
				}
				$b++;

			}
		}
		else{
			foreach ($kabupaten as $key => $value) {
				$where = array(
							'KodeProvinsi' => $value->KodeProvinsi,
							'KodeKabupaten' => $value->KodeKabupaten,
							);
				if($jd == 9){
					$rs = $this->bm->select_where_array('data_puskesmas2018', $where)->result();
				}
				else{
					$rs = $this->bm->select_where_array('data_rumah_sakit', $where)->result();
				}
				if($rs){
					foreach ($rs as $key2 => $value2) {
						$this->excel->getActiveSheet()->setCellValue('A'.$b, $no);
						
						$NamaProvinsi = $this->pm->get_where('ref_provinsi', $value->KodeProvinsi, 'KodeProvinsi')->row()->NamaProvinsi;
						$this->excel->getActiveSheet()->setCellValue('B'.$b, $NamaProvinsi);
						$this->excel->getActiveSheet()->setCellValue('C'.$b, $value->NamaKabupaten);
						$col = 'F';
						if($jd == 9){
							$this->excel->getActiveSheet()->setCellValue('D'.$b, $value2->NamaPuskesmas);
						}
						else{
							$this->excel->getActiveSheet()->setCellValue('D'.$b, $value2->NAMA_RS);
						}
						
						if($jd == 9){
							$where['KODE_RS'] = $value2->KodePuskesmas;
						}
						else{
							$where['KODE_RS'] = $value2->KODE_RS;
						}
						$where['ID_Jenis_DAK'] = $jd;
						$where['TAHUN_ANGGARAN'] = $tahun;
						$pagu = $this->bm->select_where_array('pagu_rs', $where)->num_rows();
						$this->excel->getActiveSheet()->setCellValue('E'.$b, $pagu);
						if($pagu > 0){
							for ($i=1; $i <=4 ; $i++) { 
								if($jd == 9){
									$where2 = array(
										'KodeProvinsi' => $value->KodeProvinsi,
										'KodeKabupaten' => $value->KodeKabupaten,
										'ID_SUBBIDANG' => $jd,
										'waktu_laporan' => $i,
										'TAHUN_ANGGARAN' => $tahun,
										'KD_RS' => $value2->KodePuskesmas
									);
								}
								else{
									$where2 = array(
										'KodeProvinsi' => $value->KodeProvinsi,
										'KodeKabupaten' => $value->KodeKabupaten,
										'ID_SUBBIDANG' => $jd,
										'waktu_laporan' => $i,
										'TAHUN_ANGGARAN' => $tahun,
										'KD_RS' => $value2->KODE_RS
									);
								}
								$this->excel->getActiveSheet()->setCellValue($col.$b, 'Triwulan '.$i);
								$absen = $this->bm->select_where_array('pengajuan_monev_dak', $where2)->num_rows();
								$this->excel->getActiveSheet()->setCellValue($col.$b, $absen);
								$col++;
							}
						}
						$b++;
						$no++;
					}
				}
				else{
					$this->excel->getActiveSheet()->setCellValue('A'.$b, $no);
					$NamaProvinsi = $this->pm->get_where('ref_provinsi', $value->KodeProvinsi, 'KodeProvinsi')->row()->NamaProvinsi;
					$this->excel->getActiveSheet()->setCellValue('B'.$b, $NamaProvinsi);
					$this->excel->getActiveSheet()->setCellValue('C'.$b, $value->NamaKabupaten);
					$b++;
					$no++;	
				}
				
				
			}
		}
		
		$this->excel->getActiveSheet()->getStyle('A5:' .'I' . $b)->applyFromArray($styleArray);
		$filename='Absensi-Monev-'.$jenis_dak->NAMA_JENIS_DAK.'.xls'; //save our workbook as this file name
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	function master_menu(){
		$tahun = $this->session->userdata('thn_anggaran');
		$data['judul'] = 'View pagu';
		$option_jenis_dak['0'] = '-- Pilih Jenis    --';
		foreach ($this->pm->get_where('dak_jenis_dak', $this->session->userdata('thn_anggaran'), 'TAHUN_ANGGARAN')->result() as $row){
			$option_jenis_dak[$row->ID_JENIS_DAK] = $row->NAMA_JENIS_DAK;
		}
		$option_kategori['0'] = '-- Pilih Kategori    --';
		foreach ($this->pm->get('kategori')->result() as $row){
			$option_kategori[$row->ID_KATEGORI] = $row->NAMA_KATEGORI;
		}
		$option_provinsi['0'] = '-- Pilih Provinsi --';
		foreach ($this->pm->get_provinsi()->result() as $row){
			$option_provinsi[$row->KodeProvinsi] = $row->NamaProvinsi;
		}

		$spa = $this->bm->select_all('ref_sarpras');
		$option_spa[0] = '-- Pilih Jenis Sarpras --';
		foreach ($spa as $key => $value) {
			$option_spa[$value->id] = $value->nama;
		}

		$satuan = $this->bm->select_all('ref_satuan');
		$option_satuan[0] = '-- Pilih Satuan --';
		foreach ($satuan as $key => $value) {
			$option_satuan[$value->KodeSatuan] = $value->Satuan; 
		}

		$kelompok = $this->bm->select_all('ref_pengelompokan');
		$option_kelompok[0] = '-- Pilih Kelompok --';
		foreach ($kelompok as $key => $value) {
			$option_kelompok[$value->id] = $value->Nama_Pengelompokan;
		}

		$data['option_kelompok'] = $option_kelompok;
		$data['option_satuan'] = $option_satuan;
		$data['option_spa'] = $option_spa;
		$data['e_monev'] = "";
		$data['jenis_dak'] = $option_jenis_dak;
		$data['kategori'] = $option_kategori;
		$data['provinsi'] = $option_provinsi;
		$data['role'] = $this->session->userdata('kd_role');
		$data['content'] = $this->load->view('metronic/e-monev/menu_dak',$data,true);
		$this->load->view(VIEWPATH,$data);

	}

	function get_list_menu(){
		$kategori = $this->input->post('kategori');
		$jenis_dak = $this->input->post('jenis_dak');

		$where = array(
			'ID_SUBBIDANG' => $jenis_dak,
			'ID_KATEGORI' => $kategori,
			'tahun' => $this->session->userdata('thn_anggaran')
		);
		$menu = $this->bm->select_where_array('menu', $where)->result();
		$i =0;
		$no=1;
		$datajson = array();
		foreach ($menu as $key => $value) {
			$datajson[$i]['NAMA'] = $value->NAMA;
			$datajson[$i]['id'] =$value->id;
			$datajson[$i]['ID_MENU'] = $value->ID_MENU;
			if($value->KodeSatuan != 0){
				$datajson[$i]['SATUAN'] = $this->pm->get_where('ref_satuan', $value->KodeSatuan, 'KodeSatuan')->row()->Satuan;	
			}
			else{
				$datajson[$i]['SATUAN'] = '';
			}
			
			$datajson[$i]['SARPRAS'] = $this->pm->get_where('ref_sarpras', $value->ID_SARPRAS, 'id')->row()->nama;
			$datajson[$i]['KELOMPOK'] = $this->pm->get_where('ref_pengelompokan', $value->ID_PENGELOMPOKAN, 'id')->row()->Nama_Pengelompokan;
			$datajson[$i]['NO'] =$no; $no++;
			$datajson[$i]['AKSI'] = '';
			$i++;
		}
		if($i > 0){
			$data = array(
				'status' => 'success',
				'message' => ' Data ditemukan',
				'data' => $datajson
			);	
		}
		else{
			$data = array(
				'status' => 'error',
				'message' => ' Data tidak ditemukan',
				'data' => ''
			);
		}
		echo json_encode($data);
		
	}

	function delete_menu_dak($id){
		$this->pm->delete('menu','id', $id);
		$data = array(
			'status' => 'success',
			'message' => 'Berhasil delete',
			'data' => ''
		);
		echo json_encode($data);
	}

	function tambah_menu_dak(){
		$insert_data = $this->input->post();
		
		$insert_data['TAHUN'] = $this->session->userdata('thn_anggaran');
		$insert_data['ID_FARMASI'] = 0;
		$insert_data['ID_PENUGASAN'] = 0;
		$insert_data['LEVEL'] =1;

		$hitung_jumlah = $this->pm->get_where_double('menu', $this->session->userdata('thn_anggaran'), 'TAHUN', $insert_data['ID_SUBBIDANG'], 'ID_SUBBIDANG')->num_rows();
		$insert_data['ID_MENU'] = $hitung_jumlah+1;
		// print_r($insert_data); exit();	
		$this->pm->save($insert_data,'menu');

		$data = array(
			'status' => 'success',
			'message'=> 'data berhasil diinput',
			'data' => ''
		);
		echo json_encode($data);
	}

	function get_single_data_menu ($id){
		$where = array(
			'id' => $id,
		);
		$menu = $this->bm->select_where_array('menu', $where)->row();
		$i = $this->bm->select_where_array('menu', $where)->num_rows();
		$no=1;
		$datajson = array();
		if($i > 0){
			$data = array(
				'status' => 'success',
				'message' => ' Data ditemukan',
				'data' => $menu
			);	
		}
		else{
			$data = array(
				'status' => 'error',
				'message' => ' Data tidak ditemukan',
				'data' => ''
			);
		}
		echo json_encode($data);
	}

	function update_menu_dak(){
		$insert_data = $this->input->post();
		$id = $insert_data['id'];
		unset($insert_data['Uid']);
		// $hitung_jumlah = $this->pm->get_where_double('menu', $this->session->userdata('thn_anggaran'), 'TAHUN', $insert_data['ID_SUBBIDANG'], 'ID_SUBBIDANG')->num_rows();
		// print_r($insert_data); exit();	
		$this->pm->update('menu', $insert_data, 'id', $id);

		$data = array(
			'status' => 'success',
			'message'=> 'data berhasil diinput',
			'data' => ''
		);
		echo json_encode($data);
	}public function nonfisikmising($value='')
	{
		// ambil data monev non fisik
		// $tahun   = $this->session->userdata('thn_anggaran');
		$tahun = '2019';
		$tw    = '1';
		$akses = '1';

		$tables="data_akhir_input pmd";
		$wheres="where pmd.id_akses='".$akses."' AND pmd.triwulan='".$tw."' And pmd.tahun='".$tahun."' ";
		$datanya=$this->bm->getAllWhere($tables,$wheres)->row_array();
			// $role    = $this->session->userdata('kd_role');
			// $tahun   = $this->session->userdata('thn_anggaran');
				header("Content-type=appalication/vnd.ms-excel");
				header("content-disposition:attachment;filename=LapDataMonevNonFisikTerinputBaru".$tahun."_".$tw.".xls");

			$where   = "WHERE KodeKabupaten ='00'
						ORDER BY NamaKabupaten ASC";
			$listing = $this->pm->cariProv($where);

			$data = array (	
				'listing'   => $listing,
				'tahun'   => $tahun,
				'tw'   => $tw,
				'akses_date' => $datanya['tgl_terakhir']

				);
			$this->load->view('metronic/e-monev/v_monevmonitoringMisingnonfisik', $data);
	}public function getMonevSubbidang($value='')
	{
		header("Content-type=appalication/vnd.ms-excel");
				header("content-disposition:attachment;filename=lapMonevNONFisikStunting2019.xls");

		$table2="dak_nf_pagus pmd";
            	$where2="
            			INNER JOIN dak_nf_kategori djd ON djd.id_kategori_nf=pmd.id_dak_nf
						INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pmd.KodeProvinsi
						INNER JOIN ref_kabupaten rk ON rk.KodeProvinsi=pmd.KodeProvinsi AND rk.KodeKabupaten=pmd.KodeKabupaten
						WHERE pmd.TAHUN_ANGGARAN='2019' and pmd.id_dak_nf='23' ";	
            	$listing = $this->pm->getAllWhere($table2,$where2);

			$data = array (	
					'listing'   => $listing
					);

			$this->load->view('metronic/e-monev/v_monevSubbidangnf', $data);
	}		

}
?>