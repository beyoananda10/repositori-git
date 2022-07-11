<?php
// ubah keterangan di sini
class Planning_model extends CI_Model {

    public function __construct(){
    	parent::__construct();
    }

 	function get_menu_rka($id_pengajuan){
        $this->db->select("data_rka.*, menu.ID_SARPRAS, ref_satuan.Satuan as nama_satuan, ref_metode_pbj.Jenis_metode");
        $this->db->from("data_rka");
        $this->db->join("menu", "menu.ID_MENU = data_rka.kode_menu");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = menu.KodeSatuan");
        $this->db->join("ref_metode_pbj", "data_rka.pbj = ref_metode_pbj.id");
        $this->db->where("id_pengajuan", $id_pengajuan);
        $this->db->where('menu.TAHUN', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_menu($id_pengajuan){
        $this->db->select("data_rka.*, menu.ID_SARPRAS, ref_satuan.Satuan as nama_satuan");
        $this->db->from("data_rka");
        $this->db->join("menu", "menu.ID_MENU = data_rka.kode_menu");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = menu.KodeSatuan");
        // $this->db->join("ref_metode_pbj", "data_rka.pbj = ref_metode_pbj.id");
        $this->db->where("id_pengajuan", $id_pengajuan);
        $this->db->where('menu.TAHUN', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_data_rka($kdprovinsi, $kdkabupaten, $kategori, $jenis_dak) {
        $query = "SELECT data_rka.* FROM data_rka JOIN pengajuan_dak ON pengajuan_dak.id_pengajuan = data_rka.id_pengajuan WHERE pengajuan_dak.kdprovinsi = '".$kdprovinsi."' AND pengajuan_dak.kdkabupaten = '".$kdkabupaten."' AND pengajuan_dak.kategori = '".$kategori."' AND pengajuan_dak.jenis_dak = '".$jenis_dak."'";
        $data = $this->db->query($query);
        return $data->result();
    }

    function get_data_rka_row($menu_id, $kdprovinsi, $kdkabupaten, $kategori, $jenis_dak, $kdrs) {
        $query = "SELECT data_rka.* FROM data_rka JOIN pengajuan_dak ON pengajuan_dak.id_pengajuan = data_rka.id_pengajuan WHERE pengajuan_dak.kdprovinsi = '".$kdprovinsi."' AND pengajuan_dak.kdkabupaten = '".$kdkabupaten."' AND pengajuan_dak.kategori = '".$kategori."' AND pengajuan_dak.jenis_dak = '".$jenis_dak."'  AND pengajuan_dak.kdrumahsakit = '".$kdrs."' AND data_rka.kode_menu = '".$menu_id."'";
        $data = $this->db->query($query);
        return $data->row();
    }

    function get_info($kdprovinsi, $kdkabupaten, $kategori, $jenis_dak, $kdrs) {
        $query = "SELECT info_pengajuan_dak.* FROM info_pengajuan_dak JOIN pengajuan_dak ON pengajuan_dak.id_pengajuan = info_pengajuan_dak.id_pengajuan WHERE pengajuan_dak.kdprovinsi = '".$kdprovinsi."' AND pengajuan_dak.kdkabupaten = '".$kdkabupaten."' AND pengajuan_dak.kategori = '".$kategori."' AND pengajuan_dak.jenis_dak = '".$jenis_dak."'  AND pengajuan_dak.kdrumahsakit = '".$kdrs."'";
        $data = $this->db->query($query);
        return $data->result();
    }

    function get_pengajuan($provinsi, $kabupaten, $jenis_dak, $tahun){
        $this->db->select("pengajuan_dak.id_pengajuan, pengajuan_dak.status as stat, ref_dak_bapenas.status as stats");
        $this->db->from("pengajuan_dak");
        $this->db->join("ref_dak_bapenas", "pengajuan_dak.kdprovinsi = ref_dak_bapenas.kdprovinsi and pengajuan_dak.kdkabupaten = ref_dak_bapenas.kdkabupaten and pengajuan_dak.jenis_dak = ref_dak_bapenas.id_jenis_dak");
        $this->db->where("pengajuan_dak.jenis_dak", $jenis_dak);
        $this->db->where("pengajuan_dak.kdprovinsi", $provinsi);
        $this->db->where("pengajuan_dak.kdkabupaten", $kabupaten);
        $this->db->where("pengajuan_dak.v_rka", "rakontek-" .$this->session->userdata('thn_anggaran'));
        $this->db->where("tahun_anggaran", $tahun);
        return $this->db->get();
    }

    function get_pengajuan_rka($provinsi, $kabupaten, $jenis_dak, $tahun){
        $this->db->select("pengajuan_dak.id_pengajuan, pengajuan_dak.status as stat");
        $this->db->from("pengajuan_dak");
        $this->db->where("pengajuan_dak.jenis_dak", $jenis_dak);
        $this->db->where("pengajuan_dak.kdprovinsi", $provinsi);
        $this->db->where("pengajuan_dak.kdkabupaten", $kabupaten);
        $this->db->where("tahun_anggaran", $tahun);
        $this->db->where('status', 1);
        $this->db->where("v_rka", "rka-".$tahun);
        return $this->db->get();
    }

    function get_pengajuan_rka_rujukan($kode_rs, $jenis_dak, $tahun){
        $this->db->select("pengajuan_dak.id_pengajuan, pengajuan_dak.status as stat");
        $this->db->from("pengajuan_dak");
        $this->db->where("pengajuan_dak.jenis_dak", $jenis_dak);
        $this->db->where("pengajuan_dak.kdrumahsakit", $kode_rs);
        $this->db->where("tahun_anggaran", $tahun);
        $this->db->where("v_rka", "rka-".$tahun);
        return $this->db->get();
    }

     function get_pengajuan_rka_nf($provinsi, $kabupaten, $kategori, $tahun){
        $this->db->select("pengajuan_rka_nf.id_pengajuan, pengajuan_rka_nf.status as stat");
        $this->db->from("pengajuan_rka_nf");
        $this->db->where("pengajuan_rka_nf.kategori", $kategori);
        $this->db->where("pengajuan_rka_nf.kdprovinsi", $provinsi);
        $this->db->where("pengajuan_rka_nf.kdkabupaten", $kabupaten);
        $this->db->where("tahun_anggaran", $tahun);
        return $this->db->get();
    }

    function get_pagu_rka_absen($provinsi, $kabupaten, $jenis_dak){
        $this->db->select("*");
        $this->db->from("ref_pagu_rka");
        $this->db->where('kdprovinsi', $provinsi);
        $this->db->where('kdkabupaten', $kabupaten);
        $this->db->where('id_jenisdak', $jenis_dak);
        $this->db->where('status', 1);
        return $this->db->get();
    }

    function get_pagu_rka_absen_nf($provinsi, $kabupaten, $jenis_dak){
        $this->db->select("*");
        $this->db->from("ref_pagu_rka_nf");
        $this->db->where('kdprovinsi', $provinsi);
        $this->db->where('kdkabupaten', $kabupaten);
        $this->db->where('id_kategori', $jenis_dak);
        $this->db->where('status', 1);
        return $this->db->get();
    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
    function get_pagu_rka_rujukan_nf($kode_rs, $jenis_dak){
        $this->db->select("*");
        $this->db->from("pagu_rs_rka_nf");
        $this->db->where('KODE_RS', $kode_rs);
        $this->db->where('id_jenisdak', $jenis_dak);
        $this->db->where('nominal >', 0);
        return $this->db->get();
    }

    function get_pagu_rka_rujukan($kode_rs, $jenis_dak){
        $this->db->select("*");
        $this->db->from("pagu_rs_rka");
        $this->db->where('KODE_RS', $kode_rs);
        $this->db->where('id_jenisdak', $jenis_dak);
        $this->db->where('nominal >', 0);
        return $this->db->get();
    }



    function get_rka_nf($id_pengajuan, $id_menu){
        $this->db->select("data_nf_rka.volume volume, data_nf_rka.dak dak");
        $this->db->from("pengajuan_rka_nf");
        $this->db->join("data_nf_rka", "pengajuan_rka_nf.id_pengajuan = data_nf_rka.id_pengajuan");
        $this->db->where("pengajuan_rka_nf.id_pengajuan", $id_pengajuan);
        $this->db->where("kode_menu", $id_menu);
        return $this->db->get();

    }

    function get_rka_provinsi_nf($kdprovinsi, $id_menu){
        $query = "
            select sum(volume) VOL,
            sum(replace(data_nf_rka.dak,',','')) DAK
            from data_nf_rka
            join pengajuan_rka_nf on pengajuan_rka_nf.id_pengajuan = data_nf_rka.id_pengajuan
            where pengajuan_rka_nf.kdprovinsi = '".$kdprovinsi."' and kode_menu = '".$id_menu."' 
            group by data_nf_rka.kode_menu, pengajuan_rka_nf.kdprovinsi

       ";
       $data = $this->db->query($query);
        return $data;
    }

    function get_rka($id_pengajuan, $id_menu){
        $this->db->select("data_rka.volume volume, data_rka.dak dak, ref_satuan.Satuan satuan, data_rka.harga_satuan, data_rka.jumlah");
        $this->db->from("pengajuan_dak");
        $this->db->join("data_rka", "pengajuan_dak.id_pengajuan = data_rka.id_pengajuan");
        $this->db->join("(select * from menu where tahun = ". $this->session->userdata('thn_anggaran') .") menu", "menu.ID_MENU = data_rka.kode_menu and pengajuan_dak.jenis_dak = menu.ID_SUBBIDANG or menu.ID_PENUGASAN = pengajuan_dak.jenis_dak");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = menu.KodeSatuan");
        $this->db->where("pengajuan_dak.id_pengajuan", $id_pengajuan);
        $this->db->where("kode_menu", $id_menu);
        return $this->db->get();

    }

    function get_rka_provinsi($kdprovinsi, $id_menu){
       $query = "
            select sum(volume) VOL, ref_satuan.Satuan SAT,
            sum(replace(data_rka.dak,',','')) DAK
            from data_rka
            join pengajuan_dak on pengajuan_dak.id_pengajuan = data_rka.id_pengajuan
            join (select * from menu where tahun = ".$this->session->userdata('thn_anggaran').") menu on menu.ID_MENU = data_rka.kode_menu
            join ref_satuan on ref_satuan.KodeSatuan = menu.KodeSatuan
            where pengajuan_dak.v_rka = 'rka-".$this->session->userdata('thn_anggaran')."' and pengajuan_dak.kdprovinsi = '".$kdprovinsi."' and kode_menu = '".$id_menu."' 
            group by data_rka.kode_menu, pengajuan_dak.kdprovinsi

       ";
       $data = $this->db->query($query);
        return $data;

 
    }

    function select_where_in_order($table, $kolom, $array, $id){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where_in($kolom, $array);
        $this->db->order_by($id);
        return $this->db->get();

    }

    function get_pengajuan_rujukan($kode_rs, $jenis_dak, $tahun){
        $this->db->select("pengajuan_dak.id_pengajuan, pengajuan_dak.status as stat, ref_dak_bapenas.status as stats");
        $this->db->from("pengajuan_dak");
        $this->db->join("ref_dak_bapenas", "pengajuan_dak.kdprovinsi = ref_dak_bapenas.kdprovinsi and pengajuan_dak.kdkabupaten = ref_dak_bapenas.kdkabupaten and pengajuan_dak.jenis_dak = ref_dak_bapenas.id_jenis_dak");
        $this->db->where("pengajuan_dak.jenis_dak", $jenis_dak);
        $this->db->where("pengajuan_dak.kdrumahsakit", $kode_rs);
        $this->db->where("tahun_anggaran", $tahun);
        return $this->db->get();
    }


    function cek_pengajuan($kdprovinsi, $kdkabupaten, $kategori, $jenis_dak, $kdsatker) {
        $query = "SELECT * FROM `pengajuan_dak` WHERE kdprovinsi = '".$kdprovinsi."' AND kdkabupaten = '".$kdkabupaten."' AND kategori = '".$kategori."' AND jenis_dak = '".$jenis_dak."' AND kdsatker = '".$kdsatker."'";
        $data = $this->db->query($query);
        return $data;
    }

    function sum_usulan($status, $jenis_dak) {
        if($status == 5){
            $stats = "";
        }
        else{
            $stats= $this->db->where("status", $status);
        }
        if($jenis_dak != 0 ){
             $jenis= $this->db->where("jenis_dak", $jenis_dak);
        }
        else{
            $jenis="";
        }

        $this->db->select("*");
        $this->db->from("pengajuan_dak");
        $this->db->join("data_rka", "pengajuan_dak.id_pengajuan = data_rka.id_pengajuan");
        $stats;
        $jenis;
        return $this->db->get();
    }

    function sum_usulan_nf($status) {
        if($status == 5){
            $stats = "";
        }
        else{
            $stats= $this->db->where("status", $status);
        }
        $this->db->select("*");
        $this->db->from("pengajuan_dak_nf");
        $this->db->join("data_rka_nf", "pengajuan_dak_nf.id_pengajuan = data_rka_nf.id_pengajuan");
        $stats;
        return $this->db->get();
    }

    function get_pagu_nonrs($jenis_dak, $provinsi){
        $this->db->select('*');
        $this->db->from('ref_pagu_rka');
        $this->db->where('ref_pagu_rka.id_jenisdak', $jenis_dak);
        $this->db->where('nominal >', 0);
        $this->db->where_in('kdprovinsi', $provinsi);
        return $this->db->get();

    }

    function get_pagu_nonrs_nf($jenis_dak, $provinsi){
        $this->db->select('*');
        $this->db->from('ref_pagu_rka_nf');
        $this->db->where('ref_pagu_rka_nf.id_kategori', $jenis_dak);
        $this->db->where('nominal >', 0);
        $this->db->where_in('kdprovinsi', $provinsi);
        return $this->db->get();

    }

    function get_pengajuan_nonrs($status, $jenis_dak, $provinsi){
        $this->db->select('*');
        $this->db->from('pengajuan_dak');
        $this->db->where('jenis_dak', $jenis_dak);
        $this->db->where('status', $status);
        $this->db->where('v_rka', 'rka-'.$this->session->userdata('thn_anggaran'));
        $this->db->where_in('kdprovinsi', $provinsi);
        return $this->db->get();
    }

    function get_pengajuan_nonrs_nf($status, $jenis_dak, $provinsi){
        $this->db->select('*');
        $this->db->from('pengajuan_rka_nf');
        $this->db->where('kategori', $jenis_dak);
        $this->db->where('status', $status);
        $this->db->where_in('kdprovinsi', $provinsi);
        return $this->db->get();
    }

    function get_pagu_rs($jenis_dak, $provinsi){
        $this->db->select('*');
        $this->db->from('pagu_rs_rka');
        $this->db->where('pagu_rs_rka.id_jenisdak', $jenis_dak);
        $this->db->where_in('KodeProvinsi', $provinsi);
        $this->db->where('nominal >', 0);
        return $this->db->get();
    }

    function get_pagu_rs_nf($id_kategori, $provinsi){
        $this->db->select('*');
        $this->db->from('pagu_rs_rka_nf');
        $this->db->where('pagu_rs_rka_nf.id_jenisdak', $id_kategori);
        $this->db->where_in('KodeProvinsi', $provinsi);
        $this->db->where('nominal >', 0);
        return $this->db->get();
    }

    function get_rekap_provinsi($provinsi, $kabupaten, $jenis_dak){
        $this->db->select("data_rka.nama_menu menu, data_rka.volume volume, data_rka.dak");
        $this->db->from("pengajuan_dak");
        $this->db->join("data_rka", "pengajuan_dak.id_pengajuan =  data_rka.id_pengajuan");
        $this->db->where("pengajuan_dak.kdprovinsi", $provinsi);
        $this->db->where("pengajuan_dak.jenis_dak", $jenis_dak);
        $this->db->where("pengajuan_dak.v_rka", 'rka-'.$this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_pagu_rka_seluruh($jenis_dak, $provinsi){
        $this->db->select("*");
        $this->db->from("ref_pagu_rka");        
        $this->db->where('id_jenisdak', $jenis_dak);
        $this->db->where_in('kdprovinsi', $provinsi);
        $this->db->group_by('kdprovinsi, kdkabupaten');
        return $this->db->get();   
    }
    function get_seluruh_pengajuan($provinsi, $status){
        $this->db->select("*");
        $this->db->from("pengajuan_dak");       
        $this->db->where_in('kdprovinsi', $provinsi);
        $this->db->where('status', $status);
        return $this->db->get();
    }

    function get_pengajuan_non_verif($jenis_dak, $provinsi){
        $provinsi =  implode(',', $provinsi);
        $sql = "SELECT ref_provinsi.NamaProvinsi, ref_kabupaten.NamaKabupaten, pengajuan_dak.status, pengajuan_dak.jenis_dak FROM ref_pagu_rka AS FULL LEFT OUTER JOIN pengajuan_dak ON pengajuan_dak.jenis_dak = `FULL`.id_jenisdak AND pengajuan_dak.kdprovinsi = `FULL`.kdprovinsi AND pengajuan_dak.kdkabupaten = FULL.kdkabupaten inner join ref_kabupaten on ref_kabupaten.KodeKabupaten = pengajuan_dak.kdkabupaten and ref_kabupaten.KodeProvinsi = pengajuan_dak.kdprovinsi inner join ref_provinsi on pengajuan_dak.kdprovinsi = ref_provinsi.KodeProvinsi where pengajuan_dak.v_rka = 'rka-2018' and `FULL`.kdprovinsi in (".$provinsi.") and pengajuan_dak.jenis_dak = ". $jenis_dak ." and pengajuan_dak.`status` != 1 order by ref_kabupaten.id";
        return $this->db->query($sql);
    }

    function get_pengajuan_non_verif_rs($jenis_dak, $provinsi){
        $provinsi =  implode(',', $provinsi);
        $sql = "SELECT data_rumah_sakit.NAMA_RS,ref_provinsi.NamaProvinsi, ref_kabupaten.NamaKabupaten, pengajuan_dak.status FROM pagu_rs_rka AS FULL LEFT OUTER JOIN pengajuan_dak ON pengajuan_dak.jenis_dak = FULL.id_jenisdak AND pengajuan_dak.kdrumahsakit = FULL.KODE_RS inner join ref_kabupaten on ref_kabupaten.KodeKabupaten = pengajuan_dak.kdkabupaten and ref_kabupaten.KodeProvinsi = pengajuan_dak.kdprovinsi inner join data_rumah_sakit on pengajuan_dak.kdrumahsakit = data_rumah_sakit.KODE_RS inner join ref_provinsi on pengajuan_dak.kdprovinsi = ref_provinsi.KodeProvinsi where pengajuan_dak.v_rka = 'rka-2018' and pengajuan_dak.jenis_dak = ". $jenis_dak ." and pengajuan_dak.kdprovinsi in (".$provinsi.") and pengajuan_dak.`status` != 1  order by ref_kabupaten.id";
        return $this->db->query($sql);
    }
    function get_pengajuan_non_verif_afirmasi($jenis_dak, $provinsi){
        $provinsi =  implode(',', $provinsi);
        $sql = "SELECT data_puskesmas2018.NamaPuskesmas ,ref_provinsi.NamaProvinsi, ref_kabupaten.NamaKabupaten, data_puskesmas2018.NamaPuskesmas,  pengajuan_dak.status FROM pagu_rs_rka AS FULL LEFT OUTER JOIN pengajuan_dak ON pengajuan_dak.jenis_dak = FULL.id_jenisdak AND pengajuan_dak.kdrumahsakit = FULL.KODE_RS inner join ref_kabupaten on ref_kabupaten.KodeKabupaten = pengajuan_dak.kdkabupaten and ref_kabupaten.KodeProvinsi = pengajuan_dak.kdprovinsi inner join data_puskesmas2018 on pengajuan_dak.kdrumahsakit = data_puskesmas2018.KodePuskesmas inner join ref_provinsi on pengajuan_dak.kdprovinsi = ref_provinsi.KodeProvinsi where pengajuan_dak.v_rka = 'rka-2018' and pengajuan_dak.jenis_dak = ". $jenis_dak ." and pengajuan_dak.kdprovinsi in (". $provinsi .") and pengajuan_dak.`status` != 1 order by ref_kabupaten.id";
        return $this->db->query($sql);
    }

    function get_pagu_rs_kab($provinsi, $kabupaten, $jenis_dak){
        $this->db->select_sum('nominal');
        $this->db->from('pagu_rs_rka');
        $this->db->where('pagu_rs_rka.Tahun_Anggaran', $this->session->userdata('thn_anggaran'));
        $this->db->where('pagu_rs_rka.KodeProvinsi', $provinsi);
        $this->db->where('pagu_rs_rka.KodeKabupaten', $kabupaten);
        $this->db->where('pagu_rs_rka.id_jenisdak', $jenis_dak);
        return $this->db->get();

    }


    function get_pengajuan_rka_($provinsi, $kabupaten, $id_menu, $jenis_dak){
        $this->db->select_sum('volume');
        $this->db->select('ref_satuan.Satuan');
        $this->db->select(`sum(replace(data_rka.dak,',',''))`);
        $this->db->from('data_rka');
        $this->db->join('pengajuan_dak', 'pengajuan_dak.id_pengajuan = data_rka.id_pengajuan');
        $this->db->join("menu", "menu.ID_MENU = data_rka.kode_menu");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = menu.KodeSatuan");
        $this->db->where('pengajuan_dak.kdprovinsi', $provinsi);
        $this->db->where('pengajuan_dak.kdkabupaten', $kabupaten);
        $this->db->where('pengajuan_dak.jenis_dak', $jenis_dak);
        $this->db->where('data_rka.kode_menu', $id_menu);
        $this->db->group_by('data_rka.kode_menu');
        return $this->db->get();
    }

    function get_all_rka(){
        $query = " SELECT data_rka.kode_menu as 'ID_MENU', pengajuan_dak.kdprovinsi as 'KodeProvinsi', pengajuan_dak.kdkabupaten as 'KodeKabupaten', data_rka.volume as 'VOLUME', data_rka.satuan as 'SATUAN', data_rka.harga_satuan as 'HARGA_SATUAN',data_rka.dak as 'PAGU', pengajuan_dak.kdrumahsakit as 'KODE_RS', pengajuan_dak.jenis_dak as 'ID_JENIS_DAK', pengajuan_dak.kategori as 'ID_KATEGORI', pengajuan_dak.tahun_anggaran as 'TAHUN_ANGGARAN' FROM `data_rka` JOIN pengajuan_dak ON data_rka.id_pengajuan = pengajuan_dak.id_pengajuan where v_rka = 'rka-". $this->session->userdata('thn_anggaran') ."' and pbj != 0";
        return $this->db->query($query);
    }

    function get_all_rka_nf(){
        $query = "SELECT data_nf_rka.kode_menu as 'ID_MENU', pengajuan_rka_nf.kdprovinsi as 'KodeProvinsi', pengajuan_rka_nf.kdkabupaten as 'KodeKabupaten', data_nf_rka.volume as 'VOLUME', data_nf_rka.satuan as 'SATUAN',data_nf_rka.harga_satuan as 'HARGA_SATUAN',data_nf_rka.jumlah as 'PAGU', pengajuan_rka_nf.kdrumahsakit as 'KODE_RS', pengajuan_rka_nf.jenis_dak as 'ID_JENIS_DAK', pengajuan_rka_nf.kategori as 'ID_KATEGORI', pengajuan_rka_nf.tahun_anggaran as 'TAHUN_ANGGARAN' 
            FROM `data_nf_rka` JOIN pengajuan_rka_nf ON data_nf_rka.id_pengajuan = pengajuan_rka_nf.id_pengajuan WHERE pengajuan_rka_nf.tahun_anggaran = ". $this->session->userdata('thn_anggaran');
        return $this->db->query($query);
    }

    function get_pd_kab_kot(){
        $query = "SELECT pagu_rs.KodeProvinsi, pagu_rs.KodeKabupaten, pagu_rs.ID_Jenis_DAK as ID_SUBBIDANG, SUM(pagu_rs.PAGU_SELURUH) as pagu FROM `pagu_rs` where ID_Jenis_DAK = 20 GROUP BY  pagu_rs.KodeProvinsi, pagu_rs.KodeKabupaten, ID_Jenis_DAK ORDER BY ID_Jenis_DAK";
        return $this->db->query($query);
    }

    function get_sum_detail($id_detail){
        $query = "select sum(jumlah) as total from rakontek_data where id_detail_rincian=".$id_detail;
        return $this->db->query($query);
    }

    function get_subbidang_unit($kdunit){
        $this->db->select('*');
        $this->db->from('rakontek_mapping_unit');
        $this->db->join('(select * from rakontek_nomenklatur where level = "rincian menu") rkm', 'rakontek_mapping_unit.id_nomenklatur = rkm.id');
        $this->db->where('rkm.tahun', $this->session->userdata('thn_anggaran'));
        $this->db->where('rakontek_mapping_unit.kdunit', $kdunit);
        $this->db->group_by('rkm.kdsubbidang');
        return $this->db->get();
    }

    function get_menu_unit($kdunit, $parent){
        $this->db->select('*');
        $this->db->from('rakontek_mapping_unit');
        $this->db->join('(select * from rakontek_nomenklatur where parent='.$parent.') rkm', 'rkm.id = rakontek_mapping_unit.id_nomenklatur');
        $this->db->where('rkm.tahun', $this->session->userdata('thn_anggaran'));
        $this->db->where('rakontek_mapping_unit.kdunit', $kdunit);
        return $this->db->get();
    }
    function get_rincian_unit($kdunit, $parent){
        $this->db->select('*');
        $this->db->from('rakontek_nomenklatur');
        $this->db->join('(select * from rakontek_nomenklatur where level = "rincian") rkm', 'rakontek_nomenklatur.kdrincian = rkm.kdrincian', 'left');
        $this->db->where('rakontek_nomenklatur.tahun', $this->session->userdata('thn_anggaran'));
        $this->db->where('rakontek_nomenklatur.kdunit', $kdunit);
        $this->db->where('rakontek_nomenklatur.parent', $parent);
        $this->db->group_by('rakontek_nomenklatur.kdsubbidang, rakontek_nomenklatur.kdmenu, rakontek_nomenklatur.kdrincian');
        return $this->db->get();
    }

    function get_usulan_all($jenis, $status){
        $query ="SELECT rm.jenis ,SUM(rakontek_data.jumlah) as jumlah FROM `rakontek_usulan` rd
            JOIN rakontek_nomenklatur rm ON rm.kdsubbidang = rd.kode_subbidang and rm.kdmenu  = rd.kode_menukegiatan and rm.kdrincian = rd.kode_rincian
            JOIN rakontek_data ON rd.id = rakontek_data.id_detail_rincian WHERE status = ".$status;
        $query .= " GROUP BY rm.jenis";
        return $this->db->query($query);
    }

    function get_total_usulan($jenis, $status, $kdprovinsi, $kdkabupaten){
        $this->db->select('rd.*');
        $this->db->from('rakontek_usulan rd');
        $this->db->join('rakontek_nomenklatur rm', 'rm.kdsubbidang = rd.kode_subbidang and rm.kdmenu  = rd.kode_menukegiatan and rm.kdrincian = rd.kode_rincian' , 'left');
        $this->db->where('rd.kode_provinsi', $kdprovinsi);
        $this->db->where('rd.kode_kabupaten', $kdkabupaten);
        $this->db->where('rm.jenis', $jenis);
        if($status > 0){
             $this->db->where('rd.status_usulan > 0');
        }
        return $this->db->get();
    }


    function get_list_rakontek_bapp ($jenis,$prov,$kab,$kode){

        $query = "SELECT *  FROM `rakontek_usulan` rd
            JOIN rakontek_nomenklatur rm ON rm.kdsubbidang = rd.kode_subbidang and rm.kdmenu  = rd.kode_menukegiatan and rm.kdrincian = rd.kode_rincian
                        where rd.kode_provinsi = $prov and rd.kode_kabupaten = $kab and rm.jenis = '$jenis' and rd.kode_subbidang = $kode ";
        return $this->db->query($query);    
    }

     function get_list_rakontek_bapp_($jenis,$prov,$kab){

        $query = "SELECT * FROM `rakontek_usulan` rd
            JOIN rakontek_nomenklatur rm ON rm.kdsubbidang = rd.kode_subbidang and rm.kdmenu  = rd.kode_menukegiatan and rm.kdrincian = rd.kode_rincian
                        where rd.kode_provinsi = $prov and rd.kode_kabupaten = $kab and rm.jenis = '$jenis' group by rm.kdsubbidang ";
        return $this->db->query($query);    
}
    function get_subbidang_jenis($jenis){
        $this->db->select('*');
        $this->db->from('rakontek_nomenklatur');
        $this->db->where('rakontek_nomenklatur.jenis', $jenis);
        $this->db->where('rakontek_nomenklatur.level', 'sub-bidang');
        $this->db->group_by('rakontek_nomenklatur.kdsubbidang');
        return $this->db->get();

    }

    function get_rincian_menu_subbidang($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.nomenklatur_rincian, sum(rakontek_usulan.nilai_usulan) as jumlah, rm.id_sarpras');
        $this->db->from('rakontek_usulan');
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.status_usulan', 1);
        $this->db->group_by('rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();

    }

     function get_rincian_menu_subbidang_v($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras, sum(rv.volume) as volume');
        $this->db->from('rakontek_usulan');
        $this->db->join('rakontek_hasil_verifikasi rv', 'rakontek_usulan.id = rv.id_detail_rincian' );
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.status_usulan', 2);
        $this->db->group_by('rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();

    }

    function get_rincian_menu_subbidang_v2($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.*,rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras, sum(rv.volume) as volume');
        $this->db->from('rakontek_usulan');
        $this->db->join('rakontek_hasil_verifikasi rv', 'rakontek_usulan.id = rv.id_detail_rincian' );
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        // $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        
        
        $this->db->where('rakontek_usulan.status_usulan', 2);
        $this->db->group_by('rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();

    }function get_rincian_menu_subbidang_v3($kdprovinsi, $kdkabupaten, $subbidang){ 
         $sql=" SELECT 
                rakontek_usulan.*,
                rakontek_usulan.nomenklatur_rincian,
                sum(rv.jumlah) as jumlah,
                rm.id_sarpras,
                sum(rv.volume) as volume
                FROM rakontek_usulan
                JOIN rakontek_hasil_verifikasi rv ON rakontek_usulan.id = rv.id_detail_rincian
                LEFT JOIN (select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm 
                ON rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian
                WHERE
                rakontek_usulan.kode_provinsi='$kdprovinsi'
                 -- AND rakontek_usulan.kode_kabupaten='$kdkabupaten'
                AND rakontek_usulan.kode_subbidang='$subbidang'
                AND rakontek_usulan.status_usulan='2'
                GROUP BY rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian, rakontek_usulan.kode_kabupaten ";
            return $this->db->query($sql);

    }

    function get_rincian_menu_subbidang_faskes_v3($kdprovinsi, $kdkabupaten, $subbidang){
            $sql="  SELECT
            rakontek_usulan.*,rakontek_usulan.kode_referensi,rakontek_usulan.nama_referensi ,rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras
            from rakontek_usulan
            LEFT JOIN (select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm
             ON 
            rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian
            JOIN rakontek_hasil_verifikasi rv ON rv.id_detail_rincian = rakontek_usulan.id
            WHERE 
            rakontek_usulan.kode_provinsi='$kdprovinsi'
             -- AND rakontek_usulan.kode_kabupaten='$kdkabupaten'
            AND rakontek_usulan.kode_subbidang='$subbidang'
            AND rakontek_usulan.status_usulan='2'
            GROUP BY rakontek_usulan.kode_referensi ,rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian , rakontek_usulan.kode_kabupaten
            ORDER BY rakontek_usulan.kode_referensi, rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian ";
                    return $this->db->query($sql);
    }
     function get_rincian_menu_subbidang_faskes_v2($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.*,rakontek_usulan.kode_referensi,rakontek_usulan.nama_referensi ,rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras');
        $this->db->from('rakontek_usulan');
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->join('rakontek_hasil_verifikasi rv', 'rv.id_detail_rincian = rakontek_usulan.id' );
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        // $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.status_usulan', 2);
        $this->db->group_by('rakontek_usulan.kode_referensi ,rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        $this->db->order_by('rakontek_usulan.kode_referensi, rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();
    }

    function get_pagu_subbidang_bappenas($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('sum(rakontek_usulan.nilai_usulan) as jumlah');
        $this->db->from('rakontek_usulan');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->group_by('rakontek_usulan.kode_subbidang');
        return $this->db->get();
    }

    function get_pagu_subbidang_bappenas_rs($kdprovinsi, $kdkabupaten, $subbidang, $kode_referensi){
         $this->db->select('sum(rakontek_usulan.nilai_usulan) as jumlah');
        $this->db->from('rakontek_usulan');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.kode_referensi', $kode_referensi);
        $this->db->group_by('rakontek_usulan.kode_subbidang');
        return $this->db->get();
    }

    function get_rincian_menu_subbidang_faskes($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.kode_referensi,rakontek_usulan.nama_referensi ,rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras');
        $this->db->from('rakontek_usulan');
         $this->db->join('rakontek_hasil_verifikasi rv', 'rakontek_usulan.id = rv.id_detail_rincian' );
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.status_usulan', 1);
        $this->db->group_by('rakontek_usulan.kode_referensi ,rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        $this->db->order_by('rakontek_usulan.kode_referensi, rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();
    }

     function get_rincian_menu_subbidang_faskes_v($kdprovinsi, $kdkabupaten, $subbidang){
        $this->db->select('rakontek_usulan.kode_referensi,rakontek_usulan.nama_referensi ,rakontek_usulan.nomenklatur_rincian, sum(rv.jumlah) as jumlah, rm.id_sarpras');
        $this->db->from('rakontek_usulan');
        $this->db->join('(select * from rakontek_mapping_menu  GROUP BY kdsubbidang, kdmenu, kdrincian) rm', 'rm.kdsubbidang = rakontek_usulan.kode_subbidang and rm.kdmenu  = rakontek_usulan.kode_menukegiatan and rm.kdrincian = rakontek_usulan.kode_rincian' , 'left');
        $this->db->join('rakontek_hasil_verifikasi rv', 'rv.id_detail_rincian = rakontek_usulan.id' );
        $this->db->where('rakontek_usulan.kode_subbidang', $subbidang);
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.status_usulan', 2);
        $this->db->group_by('rakontek_usulan.kode_referensi ,rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        $this->db->order_by('rakontek_usulan.kode_referensi, rakontek_usulan.kode_menukegiatan, rakontek_usulan.kode_rincian');
        return $this->db->get();
    }

    function get_usulan_rakontek_provkab($kdprovinsi, $kdkabupaten){
        $this->db->select('*');
        $this->db->from('rakontek_usulan');
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where_not_in('rakontek_usulan.kode_subbidang', array('02','09'));
        $this->db->group_by('rakontek_usulan.kode_subbidang');
        return $this->db->get();
    }

    function get_usulan_rakontek_provkab_rs($kdprovinsi, $kdkabupaten,$kode){
        $this->db->select('*');
        $this->db->from('rakontek_usulan');
        $this->db->join('ref_rumahsakit','rakontek_usulan.kode_referensi = ref_rumahsakit.kdbappenas');
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where_in('rakontek_usulan.kode_subbidang',$kode);
        $this->db->group_by('rakontek_usulan.kode_subbidang,rakontek_usulan.kode_referensi');
        return $this->db->get();
    }


     function get_usulan_rakontek_provkab_rs_S($kdprovinsi, $kdkabupaten,$kode,$rs,$kdsatker){
        $this->db->select('*');
        $this->db->from('rakontek_usulan');
        $this->db->join('ref_rumahsakit','rakontek_usulan.kode_referensi = ref_rumahsakit.kdbappenas', 'left');
        $this->db->where('rakontek_usulan.kode_provinsi', $kdprovinsi);
        $this->db->where('rakontek_usulan.kode_kabupaten', $kdkabupaten);
        $this->db->where('rakontek_usulan.kode_referensi', $rs);
        $this->db->where('ref_rumahsakit.kdrumahsakit',$kdsatker);
        $this->db->where_in('rakontek_usulan.kode_subbidang',$kode);
        $this->db->group_by('rakontek_usulan.kode_subbidang, rakontek_usulan.kode_referensi');
        return $this->db->get();
    }



   function get_user_rs($kdsatker, $kdprovinsi,$kdkabupaten){
        $query = ("SELECT * FROM `users` join ref_rumahsakit on users.kdsatker = ref_rumahsakit.kdrumahsakit where KodeProvinsi = $kdprovinsi and KodeKabupaten = $kdkabupaten and kdrumahsakit = '$kdsatker'");

        $data = $this->db->query($query);
        return $data;
    }

    function subbidang_usulan_bappenas($periode){
        $query = "SELECT sum(rakontek_usulan.nilai_usulan) as jumlah, rakontek_usulan.* FROM rakontek_usulan where periode = ".$periode." GROUP BY rakontek_usulan.kode_provinsi, rakontek_usulan.kode_kabupaten, rakontek_usulan.kode_subbidang";
        $data = $this->db->query($query);
        return $data;
    }

    function subbidang_hasil_verifikasi($periode){
        $query = "SELECT sum(rakontek_hasil_verifikasi.jumlah) as jumlah, rakontek_usulan.* FROM rakontek_usulan JOIN rakontek_hasil_verifikasi ON rakontek_hasil_verifikasi.id_detail_rincian = rakontek_usulan.id where periode = ".$periode." GROUP BY rakontek_usulan.kode_provinsi, rakontek_usulan.kode_kabupaten, rakontek_usulan.kode_subbidang";
        $data = $this->db->query($query);
        return $data;   
    }
    function rekap_nonfisik(){
        $query = "
        select kdprovinsi, kdkabupaten, kategori,sum(replace(data_nf_rka.dak,',','')) DAK from pengajuan_rka_nf  JOIN data_nf_rka ON pengajuan_rka_nf.id_pengajuan = data_nf_rka.id_pengajuan where tahun_anggaran = ".$this->session->userdata('thn_anggaran')." GROUP BY kdprovinsi, kdkabupaten, kategori";
        $data = $this->db->query($query);
        return $data;   
    }

    function rekap_rs_rincian($kdsubbidang){
        $query = "select kode_provinsi, kode_kabupaten,  kode_referensi, kode_menukegiatan, kode_rincian, SUM(nilai_usulan) as usulan , SUM(rakontek_hasil_verifikasi.jumlah) as verifikasi from  (SELECT * from rakontek_usulan where kode_subbidang = '".$kdsubbidang."' and status_usulan in (1,2) ) rakontek_usulan
            INNER JOIN rakontek_hasil_verifikasi ON rakontek_usulan.id = rakontek_hasil_verifikasi.id_detail_rincian
            GROUP BY kode_provinsi, kode_kabupaten, kode_referensi, kode_menukegiatan, kode_rincian";
        $data = $this->db->query($query);
        return $data; 
    }

    function rekap_detail_nonfisik($tahun, $kdprovinsi = null){
        if($kdprovinsi != null){
            $where ='budgetdaknf_pagu.kdprovinsi = ' .$kdprovinsi. ' and ';
        }
        else{
            $where = '';
        }
        $query = "SELECT ref_provinsi.NamaProvinsi nmprovinsi, ref_kabupaten.NamaKabupaten nmkabupaten, ref_rumahsakit.nama_rs nmrs ,dak_nf_kategori.nama_kategori nmkategori,  
                (SELECT sum(bg.`status`) FROM budgetdaknf_pengajuan bg WHERE bg.kdprovinsi = budgetdaknf_pagu.kdprovinsi 
                    and bg.kdkabupaten = budgetdaknf_pagu.kdkabupaten 
                    and bg.kategori = budgetdaknf_pagu.id_kategori 
                    and bg.tahun_anggaran = budgetdaknf_pagu.tahun 
                    and bg.kdrumahsakit = budgetdaknf_pagu.kdrumahsakit and bg.isActive =1 ) status, ref_kabupaten.id
                FROM budgetdaknf_pagu
                LEFT JOIN ref_provinsi ON budgetdaknf_pagu.kdprovinsi = ref_provinsi.KodeProvinsi
                LEFT JOIN ref_kabupaten ON budgetdaknf_pagu.kdprovinsi = ref_kabupaten.KodeProvinsi and budgetdaknf_pagu.kdkabupaten = ref_kabupaten.KodeKabupaten
                LEFT JOIN dak_nf_kategori ON budgetdaknf_pagu.id_kategori = dak_nf_kategori.id_kategori_nf and budgetdaknf_pagu.tahun = dak_nf_kategori.TAHUN_ANGGARAN
                LEFT JOIN ref_rumahsakit ON budgetdaknf_pagu.kdrumahsakit = ref_rumahsakit.kdrumahsakit
                WHERE budgetdaknf_pagu.nominal > 0 and ".$where." budgetdaknf_pagu.tahun = ".$tahun."
                ORDER BY ref_kabupaten.id
        ";
        $data = $this->db->query($query);
        return $data;
    }public function getAksesmenu($id='')
    {
       $sql=" SELECT
               *
                FROM dak_nf_kategoriakses_tch mc
                WHERE mc.id_kategori='$id'
                ";
                return $this->db->query($sql);
    }public function getAksesmenu_provinsi($id='',$prov='',$kab='')
    {
       $sql="   SELECT
                COUNT(1) AS jml
                FROM dak_nf_katagoriprov_tch mc
                WHERE mc.id_menu='$id' and mc.id_prov='$prov' and mc.id_kab='$kab'
                ";
                return $this->db->query($sql);
    }public function getCountpengajuan($prov='',$kab='',$kat='',$thn='')
    {
         $sql=" SELECT
                count(1) as jml,
                bp.id_pengajuan,
                bp.status_ver_menu
                FROM pengajuan_rka_nf bp
                Where bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.kategori='$kat' AND  bp.tahun_anggaran='$thn'";
                return $this->db->query($sql);
    }public function getListpengajuan($prov='',$kab='',$kat='',$thn='')
    {
        $sql=" SELECT
                bp.*,
                rk.NamaKabupaten,
                k.nama_kategori
                FROM pengajuan_rka_nf bp
                INNER JOIN dak_nf_kategori k ON k.id_kategori_nf=bp.kategori
                INNER JOIN ref_kabupaten rk ON rk.kodeprovinsi=bp.kdprovinsi AND rk.kodekabupaten=bp.kdkabupaten
                Where bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.kategori='$kat' AND  bp.tahun_anggaran='$thn'";
                return $this->db->query($sql);
    }public function cekdatawhere($table='',$where='')
    {
        $sql="  SELECT
                count(1) as jml
                FROM $table
                $where ";
                return $this->db->query($sql);
    }public function cekdatapengajuan($kab='',$prov='',$jd='',$kat='',$tw='',$thn='')
    {
        $sql="  SELECT
                count(1) as jml,
                pmd.id_pengajuan
                FROM pengajuan_monev_dak pmd
                INNER JOIN data_monev_rka dmr ON dmr.id_pengajuan = pmd.id_pengajuan
                where pmd.kodekabupaten='$kab' AND pmd.kodeprovinsi='$prov' And pmd.tahun_anggaran='$thn' AND pmd.waktu_laporan='$tw' AND pmd.ID_SUBBIDANG='$jd' AND pmd.ID_KATEGORI='$kat' ";
                return $this->db->query($sql);
    }public function delleteModel($id,$table,$data)
    {
        $this->db->where($id,$data[$id]);
        $this->db->delete($table,$data);
    }public function getHistory($id='')
    {
        $sql=" SELECT
                vun.id_pengajuan,
                vun.id_rka_nf,
                vun.status_verifikasi,
                vun.date_verifikator,
                vun.id_verifikator,
                u.NAMA_USER,
                dnr.nama_menu,
                ru.nama
                FROM verifikasi_usulan_nf vun
                INNER JOIN data_nf_rka dnr ON dnr.id=vun.id_rka_nf
                LEFT JOIN ref_unitutama ru ON ru.id=vun.id_unit
                INNER JOIN users u ON u.USER_ID=vun.id_verifikator
                Where vun.id_pengajuan='$id'";
                return $this->db->query($sql);
    }public function getTTDapproval($id='')
    {
         $sql="SELECT
                vun.id_pengajuan,
                vun.id_rka_nf,
                vun.status_verifikasi,
                vun.date_verifikator,
                vun.id_verifikator,
                u.NAMA_USER,
                dnr.nama_menu,
                ru.*
                FROM verifikasi_usulan_nf vun
                INNER JOIN data_nf_rka dnr ON dnr.id=vun.id_rka_nf
                LEFT JOIN ref_unitutama ru ON ru.id=vun.id_unit
                INNER JOIN users u ON u.USER_ID=vun.id_verifikator
                WHERE vun.id_pengajuan='$id' and id_unit !='0'
                GROUP BY id_unit";
                return $this->db->query($sql);
    }public function getCountKategori($thn='',$prov='',$kab='',$kat='')
    {
        $sql="  SELECT
                COUNT(1) as jml
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov'  AND rk.KodeKabupaten='$kab' AND dnk.id_kategori_nf='$kat'
                AND da.status_hide='0' ";
                return $this->db->query($sql);
    }public function getCountKategori1($thn='',$prov='',$kat='')
    {
        $sql="  SELECT
                COUNT(1) as jml
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov'  AND dnk.id_kategori_nf='$kat' AND da.status_hide='0' ";
                return $this->db->query($sql);
    }public function getCountKategoriid($id='')
    {
        $sql="  SELECT
                COUNT(1) as jml
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.id_pengajuan='$id' ";
                return $this->db->query($sql);
    }public function getCountKategoriVerif($thn='',$prov='',$kab='',$kat='')
    {
         $sql=" SELECT
                vu.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN verifikasi_usulan_nf vu ON vu.id_rka_nf=dnr.id
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov'  AND rk.KodeKabupaten='$kab' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0'
                GROUP BY vu.id_rka_nf ";
                return $this->db->query($sql);
    }public function getCountKategoriVerifid($id='')
    {
         $sql=" SELECT
                vu.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN verifikasi_usulan_nf vu ON vu.id_rka_nf=dnr.id
                WHERE da.id_pengajuan='$id' AND da.status_hide='0'
                GROUP BY vu.id_rka_nf ";
                return $this->db->query($sql);
    }public function getCountKategoriVerif1($thn='',$prov='',$kat='')
    {
         $sql=" SELECT
                vu.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN verifikasi_usulan_nf vu ON vu.id_rka_nf=dnr.id
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0'
                GROUP BY vu.id_rka_nf ";
                return $this->db->query($sql);
    }public function getCountKategoriVerif2($thn='',$prov='',$kat='',$status='')
    {
         $sql=" 
         SELECT * FROM (
                SELECT
                vu.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN verifikasi_usulan_nf vu ON vu.id_rka_nf=dnr.id
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0'
                GROUP BY vu.id_rka_nf ) AS tb
                WHERE tb.status_verifikasi='$status'";
                return $this->db->query($sql);
    }public function getCountKategoriVerif3($thn='',$prov='',$kab='',$kat='',$status='')
    {
         $sql=" 
         SELECT * FROM (
                SELECT
                vu.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN verifikasi_usulan_nf vu ON vu.id_rka_nf=dnr.id
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov' AND rk.KodeKabupaten='$kab' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0'
                GROUP BY vu.id_rka_nf ) AS tb
                WHERE tb.status_verifikasi='$status'";
                return $this->db->query($sql);
    }public function getDataTemplate($thn='',$prov='',$kat='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                rk.KodeProvinsi,
                rp.namaprovinsi,
                rk.KodeKabupaten,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                dnr.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.tahun_anggaran='$thn' AND rk.KodeProvinsi='$prov' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0' ";
                return $this->db->query($sql);
    }public function getDataTemplate2($thn='',$kat='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                rk.KodeProvinsi,
                rp.namaprovinsi,
                rk.KodeKabupaten,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                dnr.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.tahun_anggaran='$thn' AND dnk.id_kategori_nf='$kat' AND da.status_hide='0' ";
                return $this->db->query($sql);
    }public function getSubbidangadvokasi($kk='',$kp='')
    {
         $sql="     SELECT
                    sa.sub_bidang as NAMA_JENIS_DAK,
                    sa.kd_subbidang as ID_JENIS_DAK
                    from singkronisasi_fisik_advokasi sa
                    WHERE sa.kdkabupaten='$kk' and sa.kdprovinsi='$kp'
                    GROUP BY sa.kd_subbidang ";
                return $this->db->query($sql);
    }
    // latihan dulu kali yaaa
}   
