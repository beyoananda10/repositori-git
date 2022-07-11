<?php
class Pendaftaran_model extends CI_Model {
        /** semangat coyyy
        * Constructor
        *menambahkan css ke sanaa hahahah
        */
    public function __construct(){
    parent::__construct();
            $this->CI = get_instance();
                    $this->load->database();
    }
        
    function getSingleUser($kd)
    {
        $this->db->select('*');
        $this->db->from('prioritas_iku');
        $this->db->where('KodeIku', $kd);
        $getAllUser = $this->db->get()->result_object();
        if (count($getAllUser) > 0 )
        {
            return $getAllUser[0];
        }
        return NULL;
    }
    
    function get_KodePengajuan(){
        $this->db->select_max('KD_PENGAJUAN','KodePengajuan');
        $this->db->from('pengajuan');
        return $this->db->get();
    }
    
    function get_where_array($table, $array){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($array);
        return $this->db->get();
    }
    
    function get_where_double_order($tabel,$parameter,$kolom,$parameter2,$kolom2,$kolom3){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->order_by($kolom3, 'asc');
        return $this->db->get();
    }

    function get_where_order($tabel,$parameter,$kolom,$kolom3){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->order_by($kolom3, 'asc');
        return $this->db->get();
    }   

    function get_menu_dak($parameter,$kolom,$parameter2,$kolom2,$parameters){
        $this->db->select('*');
        $this->db->from('menu');
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where_in('ID_MENU', $parameters);
        $this->db->order_by('ID_SARPRAS', 'asc');
        return $this->db->get();
    }  

    function get_query($table,$id){
        return $this->db->query("select *
                            from $table
                            WHERE $id");
    }
    

    function get_max($tabel,$kolom,$alias){
        $this->db->select_max($kolom,$alias);
        $this->db->from($tabel);
        return $this->db->get();
    }
    
    function cek_Tupoksi($KD_PENGAJUAN, $KodeTupoksi){
        $this->db->select('*');
        $this->db->from('data_tupoksi');
        $this->db->where('KD_PENGAJUAN', $KD_PENGAJUAN);
        $this->db->where('KodeTupoksi', $KodeTupoksi);
        $return = $this->db->get();
        
            if($return->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    function cekProp($kd){
        $this->db->select('*');
        $this->db->from('ref_provinsi');
        $this->db->where('KodeProvinsi', $kd);
        $return = $this->db->get();
        
        if($return->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    function cekFokus($kd){
        $this->db->select('*');
        $this->db->from('fokus_prioritas');
        $this->db->where('idFokusPrioritas', $kd);
        $return = $this->db->get();
        
        if($return->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    function cekReform($kd){
        $this->db->select('*');
        $this->db->from('reformasi_kesehatan');
        $this->db->where('idReformasiKesehatan', $kd);
        $return = $this->db->get();
        
        if($return->num_rows() > 0)
            return true;
        else
            return false;
    }
    
    function cek1($tabel, $kolom1, $param1){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom1, $param1);
        $return = $this->db->get();
        
            if($return->num_rows() > 0)
            return true;
        else
            return false;
    }

    function cek($tabel, $kolom1, $param1, $kolom2, $param2){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom1, $param1);
        $this->db->where($kolom2, $param2);
        $return = $this->db->get();
        
            if($return->num_rows() > 0)
            return true;
        else
            return false;
    }
    function table_rs($id){

    return $this->db->where("(NAMA_RS LIKE '%$id%' or  KODE_RS LIKE '%$id%')")
                    ->limit('3')
                    ->order_by('KODE_RS', 'ASC')
                    ->get('data_rumah_sakit')
                    ->result();
    
    }
    function get_average_join1($table,$kolom, $kolom1, $param1,$table_join, $table_parameter){
        $this->db->select("AVG($kolom) as rata");
        $this->db->from($table);
        $this->db->join($table_join, $table_parameter);    
        $this->db->where($kolom1, $param1);
        return $this->db->get();
    }

    function get_where2_not_in($table, $kolom, $data, $kolom2, $data2, $kolom3, $ignore){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($kolom, $data);
        $this->db->where($kolom2, $data2);
        $this->db->where_not_in($kolom3, $ignore);
        return $this->db->get();

    }
   
   function get_where_in($table, $kolom, $array){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where_in($kolom, $array);
        return $this->db->get();
   } 

   function get_where_in_order($table, $kolom, $array, $order){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where_in($kolom, $array);
        $this->db->order_by($order);
        return $this->db->get();
   } 

   function get_where_double_in($table, $where, $kolom, $array){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($where);
        $this->db->where_in($kolom, $array);
        return $this->db->get();
   } 

   function get_where_double_in_order($table, $where, $kolom, $array, $order){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where($where);
        $this->db->where_in($kolom, $array);
        $this->db->order_by($order);
        return $this->db->get();
   } 

    function get_average_join2($table,$kolom, $kolom1, $param1, $kolom2, $param2,$table_join, $table_parameter){
        $this->db->select("AVG($kolom) as rata");
        $this->db->from($table);
        $this->db->join($table_join, $table_parameter);               
        $this->db->where($kolom1, $param1);
        $this->db->where($kolom2, $param2);        
        return $this->db->get();
    }  
    function get_average_laporan($jenis_dak,$waktu,$provinsi,$status){
        if($status!=4){
           $stat=$this->db->where('STATUS', $status);    
        }else{
            $stat='';
        }
        $this->db->select("AVG(REALISASI_FISIK_PELAKSANAAN) as rata");
        $this->db->from('dak_laporan');
        $this->db->join('dak_kegiatan','dak_laporan.ID_LAPORAN_DAK=dak_kegiatan.ID_LAPORAN_DAK');               
        $this->db->where('JENIS_DAK',$jenis_dak);
        $this->db->where('WAKTU_LAPORAN', $waktu);
        $stat;       
        $this->db->where('KodeProvinsi', $provinsi);                       
        return $this->db->get();
    }     
    function get_average_laporan_seluruh($waktu,$provinsi,$status){
        if($status!=4){
           $stat=$this->db->where('STATUS', $status);    
        }else{
            $stat='';
        }        
        $this->db->select("AVG(REALISASI_FISIK_PELAKSANAAN) as rata");
        $this->db->from('dak_laporan');
        $this->db->join("dak_kegiatan","dak_laporan.ID_LAPORAN_DAK = dak_kegiatan.ID_LAPORAN_DAK" , 'INNER');               
        $this->db->where('WAKTU_LAPORAN', $waktu);
        $stat;    
        $this->db->where('KodeProvinsi', $provinsi);                       
        return $this->db->get();
    }          

    function get_average_sarpras($waktu,$provinsi,$status){
        if($status!=4){
           $stat=$this->db->where('STATUS', $status);    
        }else{
            $stat='';
        }        
        $this->db->select("AVG(REALISASI_FISIK_PELAKSANAAN) as rata");
        $this->db->from('dak_laporan');
        $this->db->join("dak_kegiatan_sarpras","dak_laporan.ID_LAPORAN_DAK = dak_kegiatan_sarpras.ID_LAPORAN_DAK", 'INNER');               
        $this->db->where('WAKTU_LAPORAN', $waktu);
        $this->db->where('KodeProvinsi', $provinsi);
        $stat;
        $this->db->where("( JENIS_DAK='4' or JENIS_DAK='5')");                
        return $this->db->get();
    }      
    function dak_jenis_kegiatan($id){

    return $this->db->where("(ID_JENIS_DAK  ='$id' )")
                    ->order_by('NO_URUT', 'ASC')
                    ->get('dak_jenis_kegiatan');
    }
    function dak_jenis_kegiatan_nf($id){

    return $this->db->where("(ID_JENIS_DAK  ='$id' )")
                    ->order_by('NO_URUT', 'ASC')
                    ->get('dak_jenis_kegiatan_nf');
    }
    function dak_sub_kegiatan($ids){
    return $this->db->where("(ID_JENIS_DAK  ='$ids' )")
                    ->order_by('NO_URUT', 'ASC')
                    ->get('dak_sub_jenis_dak');
    }
    function dak_sub_kegiatan_nf($ids){

    return $this->db->where("(ID_JENIS_DAK  ='$ids' )")
                    ->order_by('NO_URUT', 'ASC')
                    ->get('dak_sub_jenis_dak_nf');
    }
    function dak_ss_kegiatan($idss){

    return $this->db->where("(ID_SUB_JENIS_DAK ='$idss' )")
                    ->order_by('NO_URUT', 'ASC')
                    ->order_by('ID_SUB_JENIS_DAK', 'ASC')
                    ->get('dak_ss_jenis_kegiatan');
    }

    function dak_ss_kegiatan_nf($idss){

    return $this->db->where("(ID_SUB_JENIS_DAK ='$idss' )")
    ->order_by('NO_URUT', 'ASC')
    ->get('dak_ss_jenis_kegiatan_nf');
    }

    function dak_kegiatan($id){

    return $this->db->where("(ID_LAPORAN_DAK ='$id' )")
                    ->order_by('ID_KEGIATAN', 'ASC')
                    ->get('dak_kegiatan');
    }
    function dak_kegiatan_sarpras($l){

        if($l==''){
          $l="ID_LAPORAN_DAK = 0";
        }
        else{
          $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select *
                                from dak_kegiatan_sarpras
                                WHERE $l
                                order by ID_LAPORAN_DAK");
    }

    function dak_kegiatan_nf($id){

    return $this->db->where("(ID_LAPORAN_DAK ='$id' )")
    ->order_by('ID_KEGIATAN', 'ASC')
    ->get('dak_kegiatan_nf');
    }

    function get_kabupaten_detail($prov,$kab){

        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $this->db->where("(KodeProvinsi ='$prov' )");
        $this->db->where("(KodeKabupaten ='$kab' )");
        return $this->db->get();
    }

    function get_kabupaten_detail2($prov,$kab){
        if($kab!=0){
            $kab= $this->db->where("(ref_kabupaten.KodeKabupaten ='$kab' )");
        }
        else{
            $kab= '';
        }
        if($prov!=0){
            $prov= $this->db->where("(ref_kabupaten.KodeProvinsi ='$prov' )");
        }
        else{
            $prov= '';
        }        
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $this->db->join('data_pagu', 'ref_kabupaten.KodeKabupaten=data_pagu.KodeKabupaten AND ref_kabupaten.KodeProvinsi=data_pagu.KodeProvinsi');
        $prov;
        $kab;
        return $this->db->get();
    }


    function dak_laporan_edak($k, $t, $p, $d, $rs){
    if($d!=0){
    $dak=" and dak_laporan.JENIS_DAK='$d'";}

    else{
    $dak='';
    }
    if($k!=0){
    $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

    else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
    $kabupaten="and dak_laporan.KodeKabupaten='00'";;
    }else {
    $kabupaten='';
    }
    if($t!=0){
    $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

    else{
    $waktu='';
    }
    if($p!=0){
    $provinsi="and dak_laporan.KodeProvinsi='$p'";}

    else{
    $provinsi='';
    }
    return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
    WHERE 1 $kabupaten $dak $waktu $provinsi and KD_RS='$rs'
    order by dak_laporan.ID_LAPORAN_DAK
    ");
    }



    function dak_laporan($k, $t, $p, $d){
    if($d!=0){
        $dak=" and dak_laporan.JENIS_DAK='$d'";}

    else{
        $dak='';
    }
    if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

    else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
        $kabupaten="and dak_laporan.KodeKabupaten='00'";;
    }
    else if($k=='00' && $p!=0 ) {
    $kabupaten="and dak_laporan.KodeKabupaten='00'";;
    }
    else {
            $kabupaten='';
    }
    if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

    else{
        $waktu='';
    }
            if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

    else{
        $provinsi='';
    }
    if($this->session->userdata('kd_role')==20){
    $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
    }else{
    $rs='';
    }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
                                WHERE 1 $kabupaten $dak $waktu $provinsi $rs
                                order by dak_laporan.ID_LAPORAN_DAK
                                ");
    }


    function dak_laporan_indonesia($k, $t, $p, $d){
    if($d!=0){
        $dak=" and dak_laporan.JENIS_DAK='$d'";}

    else{
        $dak='';
    }
    if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

    else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
        $kabupaten="and dak_laporan.KodeKabupaten='00'";;
    }
    else {
            $kabupaten='';
    }
    if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

    else{
        $waktu='';
    }
            if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

    else{
        $provinsi='';
    }
    if($this->session->userdata('kd_role')==20){
    $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
    }else{
    $rs='';
    }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
                                WHERE 1 $kabupaten $dak $waktu $provinsi $rs
                                order by dak_laporan.ID_LAPORAN_DAK
                                ");
    }

    function dak_laporan_indonesia2($k, $t, $p, $d, $s){
        if($d!=0){
            $dak=" and dak_laporan.JENIS_DAK='$d'";}

        else{
            $dak='';
        }
        if($k!=0){
            $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

        else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
            $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }
        else {
                $kabupaten='';
        }
        if($t!=0){
            $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

        else{
            $waktu='';
        }
                if($p!=0){
            $provinsi="and dak_laporan.KodeProvinsi='$p'";}

        else{
            $provinsi='';
        }
        if($s!=4){
            $status="and dak_laporan.status='$s'";}

        else{
            $status='';
        }    
        if($this->session->userdata('kd_role')==20){
        $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
        }else{
        $rs='';
        }
        if($d==1){
            $group="group by KD_RS";
        }else{
            $group="";
        }
            return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
                                    WHERE 1 $kabupaten $dak $waktu $provinsi $status $rs $group
                                    order by dak_laporan.ID_LAPORAN_DAK  
                                    ");
    
    }

    function dak_laporan_status2($k, $t, $p, $d, $s){
        if($d!=0){
        $dak=" and dak_laporan.JENIS_DAK='$d'";}

        else{
        $dak='';
        }
        if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

        else if($k==0 && $p!=0 ) {
        $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }else {
        $kabupaten='';
        }
        if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

        else{
        $waktu='';
        }
        if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

        else{
        $provinsi='';
        }
        if($s!=4){
        $s="and dak_laporan.STATUS='$s' ";}

        else{
        $s='';
        }
        if($this->session->userdata('kd_role')==20){
        $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
        }else{
        $rs='';
        }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
        WHERE 1 $kabupaten $dak $waktu $provinsi  $s $rs
        order by dak_laporan.ID_LAPORAN_DAK
        ");

    }

    function dak_laporan_status($k, $t, $p, $d)
    {
        if($d!=0){
        $dak=" and dak_laporan.JENIS_DAK='$d'";}

        else{
        $dak='';
        }
        if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

        else if($k==0 && $p!=0 ) {
        $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }else {
        $kabupaten='';
        }
        if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

        else{
        $waktu='';
        }
        if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

        else{
        $provinsi='';
        }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
        WHERE 1 $kabupaten $dak $waktu $provinsi
        order by dak_laporan.ID_LAPORAN_DAK
        ");
    }

    function dak_laporan2_limit($k, $t, $p, $d,$s,$limit,$start)
    {
        if($d!=0){
            $dak=" and dak_laporan.JENIS_DAK='$d'";}

        else{
            $dak='';
        }
        if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

        else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
        $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }else {
        $kabupaten='';
        }
        if($t!=0){
            $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

        else{
            $waktu='';
        }
        if($s!=4){
            $status="and dak_laporan.STATUS='$s'";}

        else{
            $status='';
        }
                if($p!=0){
            $provinsi="and dak_laporan.KodeProvinsi='$p'";}

        else{
            $provinsi='';
        }
        if($this->session->userdata('kd_role')==20){
        $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
        }else{
        $rs='';
        }
            return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
                join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan.KodeKabupaten
                join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan.KodeProvinsi
                                    WHERE dak_laporan.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $dak $waktu $provinsi $status $rs                  
                                    order by dak_laporan.ID_LAPORAN_DAK
                                    LIMIT $start,$limit  
                                    ");
    }

    function dak_laporan2($k, $t, $p, $d,$s){
        if($d!=0){
            $dak=" and dak_laporan.JENIS_DAK='$d'";}

    else{
        $dak='';
    }
    if($k!=0){
    $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

    else if($k==0 && $p!=0 && $this->session->userdata('kd_role')==17) {
    $kabupaten="and dak_laporan.KodeKabupaten='00'";;
    }else {
    $kabupaten='';
    }
    if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

    else{
        $waktu='';
    }
    if($s!=4){
        $status="and dak_laporan.STATUS='$s'";}

    else{
        $status='';
    }
            if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

    else{
        $provinsi='';
    }
    if($this->session->userdata('kd_role')==20){
    $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
    }else{
    $rs='';
    }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
            join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan.KodeKabupaten
            join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan.KodeProvinsi
                                WHERE dak_laporan.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $dak $waktu $provinsi $status $rs
                                order by dak_laporan.ID_LAPORAN_DAK
                                ");
    }

    function dak_laporan_nf2($k, $t, $p, $s){
        if($k!=0){
        $kabupaten="and dak_laporan_nf.KodeKabupaten='$k'";}

        else if($k==0 && $p!=0  && ($this->session->userdata('kd_role')==17 && $this->session->userdata('kodekabupaten')=='00'  )) {
        $kabupaten="and dak_laporan_nf.KodeKabupaten='00'";;
        }
        else if($k=='00' && $p!=0  ) {
        $kabupaten="and dak_laporan_nf.KodeKabupaten='00'";
        }
        else {
        $kabupaten='';
        }
        if($t!=0){
        $waktu="and dak_laporan_nf.WAKTU_LAPORAN='$t'";}

        else{
        $waktu='';
        }
        if($p!=0){
        $provinsi="and dak_laporan_nf.KodeProvinsi='$p'";}

        else{
        $provinsi='';
        }
        if($s!=4){
        $s="and dak_laporan_nf.STATUS='$s' ";}

        else{
        $s='';
        }
        return $this->db->query("select * from dak_laporan_nf
        join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan_nf.KodeKabupaten
        join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan_nf.KodeProvinsi
        WHERE dak_laporan_nf.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $waktu $provinsi $s
        order by dak_laporan_nf.ID_LAPORAN_DAK
        ");
    }


    function dak_laporan_nf_2($k, $t, $p, $s){
        if($k!=0){
        $kabupaten="and dak_laporan_nf.KodeKabupaten='$k'";}
        else {
        $kabupaten='';
        }
        if($t!=0){
        $waktu="and dak_laporan_nf.WAKTU_LAPORAN='$t'";}

        else{
        $waktu='';
        }
        if($p!=0){
        $provinsi="and dak_laporan_nf.KodeProvinsi='$p'";}

        else{
        $provinsi='';
        }
        if($s!=4){
        $s="and dak_laporan_nf.STATUS='$s' ";}

        else{
        $s='';
        }
        return $this->db->query("select * from dak_laporan_nf
        join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan_nf.KodeKabupaten
        join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan_nf.KodeProvinsi
        WHERE dak_laporan_nf.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $waktu $provinsi $s
        order by dak_laporan_nf.ID_LAPORAN_DAK
        ");
    }


    function dak_laporan_nf2_limit($k, $t, $p, $s,$limit,$start){
        if($k!=0){
        $kabupaten="and dak_laporan_nf.KodeKabupaten='$k'";
        }

        else if($k==0 && $p!=0  && ($this->session->userdata('kd_role')==17 && $this->session->userdata('kodekabupaten')=='00'  )) {
        $kabupaten="and dak_laporan_nf.KodeKabupaten='00'";;
        }
        else {
        $kabupaten='';
        }
        if($t!=0){
        $waktu="and dak_laporan_nf.WAKTU_LAPORAN='$t'";}

        else{
        $waktu='';
        }
        if($p!=0){
        $provinsi="and dak_laporan_nf.KodeProvinsi='$p'";}

        else{
        $provinsi='';
        }
        if($s!=4){
        $s="and dak_laporan_nf.STATUS='$s' ";}

        else{
        $s='';
        }
        return $this->db->query("select * from dak_laporan_nf
                                join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan_nf.KodeKabupaten
                                join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan_nf.KodeProvinsi
                                WHERE dak_laporan_nf.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $waktu $provinsi $s
                                order by dak_laporan_nf.ID_LAPORAN_DAK
                                LIMIT $start,$limit  
                                ");
    }


    function dak_laporan3($k, $t, $p, $d){
    if($d!=0){
        $dak=" and dak_laporan.JENIS_DAK='$d'";}

    else{
        $dak='';
    }
    if($k!=0){
        $kabupaten="and dak_laporan.KodeKabupaten='$k'";}

    else if($k==0 && $p!=0 ) {
        $kabupaten="";;
    }else {
            $kabupaten='';
    }
    if($t!=0){
        $waktu="and dak_laporan.WAKTU_LAPORAN='$t'";}

    else{
        $waktu='';
    }
            if($p!=0){
        $provinsi="and dak_laporan.KodeProvinsi='$p'";}

    else{
        $provinsi='';
    }
        return $this->db->query("select * from dak_laporan left join dak_jenis_dak on dak_jenis_dak.ID_JENIS_DAK=dak_laporan.JENIS_DAK
                                WHERE 1 $kabupaten $dak $waktu $provinsi
                                order by dak_laporan.ID_LAPORAN_DAK
                                ");
    }

    function dak_laporan_rujukan($k, $t, $p, $d, $s){
        if($d!=0){
            $dak=" and JENIS_DAK='$d'";}

        else{
            $dak='';
        }
        if($k!=0){
            $kabupaten="and dak_laporan.KodeKabupaten='$k'";}
        else if($k==0 && $p!=0  && ($this->session->userdata('kd_role')==17 && $this->session->userdata('kodekabupaten')=='00'  )){
            $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }
        else if($k==0 && $p!=0 ) {
            $kabupaten="and dak_laporan.KodeKabupaten='00'";;
        }
        else{
            $kabupaten="";
        }
        if($t!=0){
            $waktu="and WAKTU_LAPORAN='$t'";}
        else{
            $waktu='';
        }
        if($s!=4){
            $s="and dak_laporan.STATUS='$s' ";}
        else{
            $s='';
        }
        if($this->session->userdata('kd_role')==20){
            $rs="and KD_RS='".$this->session->userdata('kdsatker')."'";
        }else{
            $rs='';
        }
        return $this->db->where("(dak_laporan.KodeProvinsi='$p' $kabupaten $waktu $dak $s $rs)")
                    ->join('data_rumah_sakit', 'data_rumah_sakit.KODE_RS = dak_laporan.KD_RS')
                    ->order_by('ID_LAPORAN_DAK', 'ASC')
                    ->get('dak_laporan');
    }

    function dak_laporan_rs($t,$s,$rs,$d){

        if($t!=0){
            $waktu="WAKTU_LAPORAN='$t'";}
        else{
            $waktu='';
        }
        if($s!=4){
            $s="and dak_laporan.STATUS='$s' ";}
        else{
            $s='';
        }
        if($d!=0){
            $dak=" and JENIS_DAK='$d'";}

        else{
            $dak='';
        } 
        if($rs !=0 ){
            $rs="and KD_RS='$rs'";
        }else{
            $rs='';
        }
        return $this->db->where("( $waktu $s $rs $dak)")
                    ->join('data_rumah_sakit', 'data_rumah_sakit.KODE_RS = dak_laporan.KD_RS')
                    ->order_by('ID_LAPORAN_DAK', 'ASC')
                    ->get('dak_laporan');
    }


    function dak_laporan_nf_indo($k,$t, $p, $s){
        if($k!=0){
            $kabupaten="and dak_laporan_nf.KodeKabupaten='$k'";
        }
        else {
            $kabupaten='';
        }
        if($t!=0){
            $waktu="and dak_laporan_nf.WAKTU_LAPORAN='$t'";
        }else{
            $waktu='';
        }
        if($p!=0){
            $provinsi="and dak_laporan_nf.KodeProvinsi='$p'";
        }else{
            $provinsi='';
        }
        if($s!=4){
            $s="and dak_laporan_nf.STATUS='$s' ";
        }
        else{
            $s='';
        }
        return $this->db->query("select * from dak_laporan_nf
                                join ref_kabupaten on ref_kabupaten.KodeKabupaten=dak_laporan_nf.KodeKabupaten
                                join ref_provinsi on ref_provinsi.KodeProvinsi=dak_laporan_nf.KodeProvinsi
                                WHERE dak_laporan_nf.KodeProvinsi=ref_kabupaten.KodeProvinsi $kabupaten $waktu $provinsi $s
                                order by dak_laporan_nf.ID_LAPORAN_DAK
                                ");
    }


    function dak_laporan_nf_s($k, $t, $p){
        if($k!=0){
            $kabupaten="and KodeKabupaten='$k'";}
        else if($k==0 && $p!=0  && ($this->session->userdata('kd_role')==17 && $this->session->userdata('kodekabupaten')=='00'  )){
            $kabupaten="and KodeKabupaten='00'";;
        }
        else{
            $kabupaten="";;
        }
        if($t!=0){
            $waktu="and WAKTU_LAPORAN='$t'";}
        else{
            $waktu='';
        }
        return $this->db->where("(KodeProvinsi='$p' $kabupaten $waktu)")
                        ->where("(STATUS = '1')")
                        ->order_by('ID_LAPORAN_DAK', 'ASC')
                        ->get('dak_laporan_nf');
    }

    function dak_laporan_nf3(){
        return $this->db->get('dak_laporan_nf');
    }

    function get_pagu($dt){

        return $this->db->where("Nama_Daerah like'%{$dt}%'")
                        ->order_by('no', 'ASC')
                        ->get('data_pagu');
    }


    function get_pagu_2($p,$k,$jenis_dak,$kategori,$rs, $tahun){
        $rumah_sakit = "";
        if($rs != 0){
            $rumah_sakit = $this->db->where('pagu.KODE_RS', $rs);
        }
        $this->db->select("*");
        $this->db->from('pagu');
        $this->db->join('(select * from menu where tahun='.$this->session->userdata('thn_anggaran').')menu','pagu.ID_MENU=menu.ID_MENU and (pagu.ID_JENIS_DAK = menu.ID_SUBBIDANG or pagu.ID_JENIS_DAK = menu.ID_PENUGASAN )');
        $this->db->join('(select * from pagu_seluruh where TAHUN_ANGGARAN = '. $this->session->userdata('thn_anggaran') .') pagu_seluruh','pagu.KodeProvinsi=pagu_seluruh.KodeProvinsi And pagu.KodeKabupaten=pagu_seluruh.KodeKabupaten AND pagu.ID_JENIS_DAK=pagu_seluruh.ID_SUBBIDANG AND pagu.ID_KATEGORI=pagu_seluruh.ID_KATEGORI');
        $this->db->join('ref_kabupaten','pagu.KodeKabupaten=ref_kabupaten.KodeKabupaten AND pagu.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->join('ref_satuan','menu.KodeSatuan=ref_satuan.KodeSatuan'); 
        $this->db->join('ref_provinsi','pagu.KodeProvinsi=ref_provinsi.KodeProvinsi');  
        $this->db->where('pagu.KodeProvinsi', $p);
        $this->db->where('pagu.KodeKabupaten', $k); 
        $rumah_sakit;    
        $this->db->where('pagu.ID_JENIS_DAK', $jenis_dak);
        $this->db->where('pagu.status', 1); 
        $this->db->where('pagu.ID_KATEGORI', $kategori);
        $this->db->where('menu.TAHUN', $tahun); 
        $this->db->group_by('pagu.ID_MENU'); 
        return $this->db->get();

    }

    function get_pagu_rujukan_2($p,$k,$rs,$jenis_dak,$kategori, $tahun){
        $this->db->select('*');
        $this->db->from('pagu');
        $this->db->join('(select * from pagu_seluruh where TAHUN_ANGGARAN = '.$this->session->userdata('thn_anggaran').') pagu_seluruh','pagu.KodeProvinsi=pagu_seluruh.KodeProvinsi And pagu.KodeKabupaten=pagu_seluruh.KodeKabupaten AND pagu.ID_JENIS_DAK=pagu_seluruh.ID_SUBBIDANG AND pagu.ID_KATEGORI=pagu_seluruh.ID_KATEGORI'); 
        $this->db->join('(select * from menu where tahun='.$this->session->userdata('thn_anggaran').')menu','pagu.ID_MENU=menu.ID_MENU and (pagu.ID_JENIS_DAK = menu.ID_SUBBIDANG or pagu.ID_JENIS_DAK = menu.ID_PENUGASAN )');       
        $this->db->join('(select * from dak_jenis_dak where TAHUN_ANGGARAN = '.$this->session->userdata('thn_anggaran').') dak_jenis_dak','pagu.ID_JENIS_DAK=dak_jenis_dak.ID_JENIS_DAK');
        $this->db->join('kategori','pagu.ID_KATEGORI=kategori.ID_KATEGORI');
        $this->db->join('ref_provinsi','pagu.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_satuan','menu.KodeSatuan=ref_satuan.KodeSatuan');
        $this->db->join('ref_kabupaten','pagu.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        $this->db->where('ref_kabupaten.KodeKabupaten', $k);        
        $this->db->where('pagu.ID_JENIS_DAK', $jenis_dak);
        $this->db->where('pagu.TAHUN_ANGGARAN', $tahun);
        $this->db->where('pagu.KODE_RS', $rs);  
        $this->db->where('pagu.status', 1);  
        $this->db->where('kategori.ID_KATEGORI', $kategori);
        $this->db->where('menu.TAHUN', $tahun); 
        $this->db->group_by("pagu.ID_PAGU");      
        return $this->db->get();
    }
    function get_pagu_2_nf($p,$k, $tahun){
        $this->db->select('*');
        $this->db->from('pagu_nf');
        $this->db->join('pagu_seluruh_nf','pagu_nf.KodeProvinsi=pagu_seluruh_nf.KodeProvinsi And pagu_nf.KodeKabupaten=pagu_seluruh_nf.KodeKabupaten AND pagu_nf.ID_KATEGORI=pagu_seluruh_nf.ID_KATEGORI');        
        $this->db->join('kategori','pagu_nf.ID_KATEGORI=kategori.ID_KATEGORI');
        $this->db->join('ref_provinsi','pagu_nf.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_kabupaten','pagu_nf.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->join('menu_nf','pagu_nf.id_menu_nf=menu_nf.id_menu_nf');
        $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        $this->db->where('ref_kabupaten.KodeKabupaten', $k); 
        $this->db->where('menu_nf.TAHUN_ANGGARAN', $tahun);       
        return $this->db->get();
    }

    function get_pagu_nf_($p, $k, $jenis, $tahun){
        $this->db->select('*');
        $this->db->from('dak_nf_pagu');
        $this->db->join("dak_nf_menu", "dak_nf_pagu.id_menu_nf = dak_nf_menu.id_menu and (dak_nf_pagu.id_dak_nf = dak_nf_menu.id_dak_nf or dak_nf_pagu.id_dak_nf = dak_nf_menu.id_kategori_nf)");
        $this->db->where('dak_nf_pagu.KodeProvinsi', $p);
        $this->db->where('dak_nf_pagu.KodeKabupaten', $k); 
        $this->db->where('dak_nf_pagu.id_dak_nf', $jenis); 
        $this->db->where('dak_nf_pagu.TAHUN_ANGGARAN', $tahun); 
        return $this->db->get();      
    }

    function get_pagu_nf_rs($p, $k, $jenis, $tahun, $kdrs){
        $this->db->select('*');
        $this->db->from('dak_nf_pagu');
        $this->db->join("dak_nf_menu", "dak_nf_pagu.id_menu_nf = dak_nf_menu.id_menu and (dak_nf_pagu.id_dak_nf = dak_nf_menu.id_dak_nf or dak_nf_pagu.id_dak_nf = dak_nf_menu.id_kategori_nf)");
        $this->db->where('dak_nf_pagu.KodeProvinsi', $p);
        $this->db->where('dak_nf_pagu.KodeKabupaten', $k); 
        $this->db->where('dak_nf_pagu.id_dak_nf', $jenis); 
        $this->db->where('dak_nf_pagu.kdrumahsakit', $kdrs); 
        $this->db->where('dak_nf_pagu.TAHUN_ANGGARAN', $tahun); 
        return $this->db->get();      
    }

    function get_verifikasi($p, $k, $jenis_dak, $kategori,$waktu, $tahun, $kdrs){
        $provinsi ="";
        $kabupaten= "";
        if($p != 0){
            $provinsi = $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        }
        if ($k != 0){
            $kabupaten = $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }
        $this->db->select("*");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("ref_provinsi", "ref_provinsi.KodeProvinsi = pengajuan_monev_dak.KodeProvinsi");
        $this->db->join('ref_kabupaten','pengajuan_monev_dak.KodeKabupaten=ref_kabupaten.KodeKabupaten AND pengajuan_monev_dak.KodeProvinsi=ref_kabupaten.KodeProvinsi'); 
         $this->db->join('dak_jenis_dak','pengajuan_monev_dak.ID_SUBBIDANG=dak_jenis_dak.ID_JENIS_DAK');
        $this->db->join('kategori','pengajuan_monev_dak.ID_KATEGORI=kategori.ID_KATEGORI');       
        if($jenis_dak != '0'){
            $this->db->where('dak_jenis_dak.ID_JENIS_DAK', $jenis_dak);     
        }
        if($kategori != '0'){
            $this->db->where('kategori.ID_KATEGORI', $kategori);    
        }
        if($waktu != '0'){
            $this->db->where('pengajuan_monev_dak.WAKTU_LAPORAN', $waktu);    
        }
        if($kdrs != 0){
            $this->db->where('pengajuan_monev_dak.KD_RS', $kdrs);
        }
        $this->db->where('pengajuan_monev_dak.TAHUN_ANGGARAN', $tahun);
        $this->db->group_by('id_pengajuan');
        $this->db->order_by("ref_kabupaten.id,ref_kabupaten.KodeProvinsi, ref_kabupaten.KodeKabupaten, pengajuan_monev_dak.ID_SUBBIDANG, pengajuan_monev_dak.WAKTU_LAPORAN");
        return $this->db->get();
    }

    function get_verifikasi_nf($p, $k, $waktu, $tahun){
        $provinsi =""; 
        $kabupaten= "";
        if($p != 0){
            $provinsi = $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        }
        if ($k != 0){
            $kabupaten = $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }
        $this->db->select("*");
        $this->db->from("pengajuan_monev_nf");
        $this->db->join("ref_provinsi", "ref_provinsi.KodeProvinsi = pengajuan_monev_nf.KodeProvinsi");
        $this->db->join('ref_kabupaten','pengajuan_monev_nf.KodeKabupaten=ref_kabupaten.KodeKabupaten AND pengajuan_monev_nf.KodeProvinsi=ref_kabupaten.KodeProvinsi'); 
        $this->db->where('pengajuan_monev_nf.TAHUN_ANGGARAN', $tahun);
        $this->db->where('pengajuan_monev_nf.WAKTU_LAPORAN', $waktu);
        $this->db->order_by("ref_kabupaten.KodeProvinsi");
        $this->db->order_by("ref_kabupaten.KodeKabupaten");
        return $this->db->get();
    }

    function get_verifikasi_nf2($p, $k, $waktu, $jenis_dak , $tahun){
        $provinsi =""; 
        $kabupaten= "";
        $this->db->select("*");
        $this->db->from("dak_nf_laporan");
        $this->db->join("ref_provinsi", "ref_provinsi.KodeProvinsi = dak_nf_laporan.KodeProvinsi");
        $this->db->join('ref_kabupaten','dak_nf_laporan.KodeKabupaten=ref_kabupaten.KodeKabupaten AND dak_nf_laporan.KodeProvinsi=ref_kabupaten.KodeProvinsi'); 
        if($p != 0){
            $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        }
        if ($k != 0){
            $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }
        if($jenis_dak != 0){    
            $this->db->where('dak_nf_laporan.id_dak_nf', $jenis_dak);
        }
        $this->db->where('dak_nf_laporan.TAHUN_ANGGARAN', $tahun);
        $this->db->where('dak_nf_laporan.WAKTU_LAPORAN', $waktu);
        $this->db->order_by("ref_kabupaten.id");
        return $this->db->get();
    }
    function get_proses_realisasi($p,$k,$jenis_dak,$kategori, $waktu,$tahun){
        $this->db->select("AVG(data_monev_rka.fisik) as fisik, SUM(data_monev_rka.realisasi) as realisasi, pagu_seluruh.pagu_seluruh as pagu");
        $this->db->from('data_monev_rka');
        $this->db->join('pengajuan_monev_dak','data_monev_rka.id_pengajuan = pengajuan_monev_dak.id_pengajuan');
        $this->db->join('(select * from pagu_seluruh where TAHUN_ANGGARAN = '.$tahun.') pagu_seluruh','pengajuan_monev_dak.KodeProvinsi=pagu_seluruh.KodeProvinsi And pengajuan_monev_dak.KodeKabupaten=pagu_seluruh.KodeKabupaten AND pengajuan_monev_dak.ID_SUBBIDANG=pagu_seluruh.ID_SUBBIDANG AND pengajuan_monev_dak.ID_KATEGORI=pagu_seluruh.ID_KATEGORI', 'left');        
        $this->db->where('pengajuan_monev_dak.KodeProvinsi', $p);
        $this->db->where('pengajuan_monev_dak.KodeKabupaten', $k);        
        $this->db->where('pengajuan_monev_dak.ID_SUBBIDANG', $jenis_dak); 
        $this->db->where('pengajuan_monev_dak.ID_KATEGORI', $kategori);
        $this->db->where('pengajuan_monev_dak.WAKTU_LAPORAN', $waktu);
        $this->db->where('pengajuan_monev_dak.TAHUN_ANGGARAN', $tahun);       
        return $this->db->get();
    }

    function get_proses_realisasi2($p,$jenis_dak,$kategori,$waktu, $tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('pengajuan_monev_dak.KodeProvinsi', $p);  
        }
        $this->db->select("AVG(data_monev_rka.fisik) as fisik, SUM(data_monev_rka.realisasi) as realisasi");
        $this->db->from('data_monev_rka');
        $this->db->join('pengajuan_monev_dak','data_monev_rka.id_pengajuan = pengajuan_monev_dak.id_pengajuan');
        $this->db->join('(select * from pagu_seluruh where TAHUN_ANGGARAN = '.$tahun.') pagu_seluruh','pengajuan_monev_dak.KodeProvinsi=pagu_seluruh.KodeProvinsi And pengajuan_monev_dak.KodeKabupaten=pagu_seluruh.KodeKabupaten AND pengajuan_monev_dak.ID_SUBBIDANG=pagu_seluruh.ID_SUBBIDANG AND pengajuan_monev_dak.ID_KATEGORI=pagu_seluruh.ID_KATEGORI');        
        $this->db->where('pengajuan_monev_dak.ID_SUBBIDANG', $jenis_dak); 
        $provinsi;
        $this->db->where('pengajuan_monev_dak.WAKTU_LAPORAN', $waktu);
        $this->db->where('pengajuan_monev_dak.ID_KATEGORI', $kategori);
        $this->db->where('pengajuan_monev_dak.TAHUN_ANGGARAN', $tahun);       
        return $this->db->get();
    }
    function get_proses_realisasi2_nf($p,$waktu, $tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('pengajuan_monev_nf.KodeProvinsi', $p);  
        }
        $this->db->select("AVG(data_monev_nf.fisik) as fisik, SUM(data_monev_nf.realisasi) as realisasi");
        $this->db->from('data_monev_nf');
        $this->db->join('pengajuan_monev_nf','data_monev_nf.id_pengajuan = pengajuan_monev_nf.id_pengajuan');
        $this->db->join('pagu_seluruh_nf','pengajuan_monev_nf.KodeProvinsi=pagu_seluruh_nf.KodeProvinsi And pengajuan_monev_nf.KodeKabupaten=pagu_seluruh_nf.KodeKabupaten');        
        $provinsi;
        $this->db->where('pengajuan_monev_nf.WAKTU_LAPORAN', $waktu);
        $this->db->where('data_monev_nf.kode_menu <', 5);
        $this->db->where('pengajuan_monev_nf.TAHUN_ANGGARAN', $tahun);       
        return $this->db->get();
    }
    function get_proses_realisasi_provinsi($p,$j,$waktu, $tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('dak_nf_laporan.KodeProvinsi', $p);  
        }
        $this->db->select("AVG(dak_nf_rka.fisik) as fisik, SUM(dak_nf_rka.realisasi) as realisasi");
        $this->db->from('dak_nf_rka');
        $this->db->join('dak_nf_laporan','dak_nf_rka.id_pengajuan = dak_nf_laporan.id_pengajuan');
        $this->db->join('(select * from dak_nf_pagus where TAHUN_ANGGARAN='.$tahun.') as dak_nf_pagus','dak_nf_laporan.KodeProvinsi=dak_nf_pagus.KodeProvinsi and dak_nf_laporan.KodeKabupaten=dak_nf_pagus.KodeKabupaten and dak_nf_laporan.id_dak_nf = dak_nf_pagus.id_dak_nf and dak_nf_laporan.TAHUN_ANGGARAN = dak_nf_pagus.TAHUN_ANGGARAN');        
        $provinsi;
        $this->db->where('dak_nf_laporan.WAKTU_LAPORAN', $waktu);
        $this->db->where('dak_nf_pagus.TAHUN_ANGGARAN', $tahun);
        $this->db->where('dak_nf_laporan.id_dak_nf', $j);         
        return $this->db->get();
    }
    function get_proses_realisasi_kabupaten($p,$k,$j,$waktu, $tahun){
        $provinsi = "";
        $kabupaten = "";
        $this->db->select("AVG(dak_nf_rka.fisik) as fisik, SUM(dak_nf_rka.realisasi) as realisasi");
        $this->db->from('dak_nf_rka');
        $this->db->join('dak_nf_laporan','dak_nf_rka.id_pengajuan = dak_nf_laporan.id_pengajuan');
        $this->db->join('(select * from dak_nf_pagus where TAHUN_ANGGARAN='.$tahun.') dak_nf_pagus','dak_nf_laporan.KodeProvinsi=dak_nf_pagus.KodeProvinsi and dak_nf_laporan.KodeKabupaten=dak_nf_pagus.KodeKabupaten and dak_nf_laporan.id_dak_nf = dak_nf_pagus.id_dak_nf and dak_nf_laporan.TAHUN_ANGGARAN = dak_nf_pagus.TAHUN_ANGGARAN');        
        $provinsi;
        $this->db->where('dak_nf_laporan.WAKTU_LAPORAN', $waktu);
        $this->db->where('dak_nf_pagus.TAHUN_ANGGARAN', $tahun);
        $this->db->where('dak_nf_laporan.id_dak_nf', $j);
         $provinsi = $this->db->where('dak_nf_laporan.KodeProvinsi', $p); 
        $kabupaten = $this->db->where('dak_nf_laporan.KodeKabupaten', $k);          
        return $this->db->get();
    }
    function get_proses_realisasi_nf2($p,$k, $waktu, $tahun){
        $this->db->select("AVG(data_monev_nf.fisik) as fisik, SUM(data_monev_nf.realisasi) as realisasi");
        $this->db->from('pengajuan_monev_nf');
        $this->db->join('data_monev_nf','data_monev_nf.id_pengajuan = pengajuan_monev_nf.id_pengajuan');    
        $this->db->where('pengajuan_monev_nf.KodeProvinsi', $p); 
        $this->db->where('pengajuan_monev_nf.KodeKabupaten', $k);
        $this->db->where('data_monev_nf.kode_menu <', 5); 
        $this->db->where('pengajuan_monev_nf.WAKTU_LAPORAN', $waktu);
        $this->db->where('pengajuan_monev_nf.TAHUN_ANGGARAN', $tahun);       
        return $this->db->get();
    }
    function get_pagu_kab($p,$k,$jenis_dak,$kategori, $tahun){
        $provinsi = "";
        $this->db->select("SUM(pagu_seluruh.pagu_seluruh) as pagu");
        $this->db->from("pagu_seluruh");
        $this->db->where('pagu_seluruh.KodeProvinsi', $p);
        $this->db->where('pagu_seluruh.KodeKabupaten', $k);
        $this->db->where('pagu_seluruh.ID_SUBBIDANG', $jenis_dak);
        $this->db->where('pagu_seluruh.ID_KATEGORI', $kategori);
        $this->db->where('pagu_seluruh.TAHUN_ANGGARAN', $tahun);
        return $this->db->get();
    }

    function get_pagu_kab_nf($p,$k, $tahun){
        $this->db->select("SUM(pagu_seluruh_nf.pagu_seluruh) as pagu");
        $this->db->from("pagu_seluruh_nf");
        $this->db->where('pagu_seluruh_nf.KodeProvinsi', $p);
        $this->db->where('pagu_seluruh_nf.KodeKabupaten', $k);
        $this->db->where('pagu_seluruh_nf.TAHUN_ANGGARAN', $tahun);
        return $this->db->get();
    }
    function get_pagu_prov($p,$jenis_dak,$kategori, $tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('pagu_seluruh.KodeProvinsi', $p);
        }
        $this->db->select("SUM(pagu_seluruh.pagu_seluruh) as pagu ");
        $this->db->from("pagu_seluruh");
        $provinsi;
        $this->db->where('pagu_seluruh.ID_SUBBIDANG', $jenis_dak);
        $this->db->where('pagu_seluruh.ID_KATEGORI', $kategori);
        $this->db->where('pagu_seluruh.TAHUN_ANGGARAN', $tahun);
        return $this->db->get();
    }
    function get_pagu_prov_nf($p, $tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('KodeProvinsi', $p);
        }
        $this->db->select("SUM(pagu_seluruh_nf.pagu_seluruh) as pagu ");
        $this->db->from("pagu_seluruh_nf");
        $provinsi;
        $this->db->where('TAHUN_ANGGARAN', $tahun);
        return $this->db->get();
    }
    function get_pagu_prov_nf2($p, $j,$tahun){
        $provinsi = "";
        if($p != 0){
            $provinsi = $this->db->where('KodeProvinsi', $p);
        }
        $this->db->select("SUM(dak_nf_pagus.pagu) as pagu ");
        $this->db->from("dak_nf_pagus");
        $provinsi;
        $this->db->where('TAHUN_ANGGARAN', $tahun);
        $this->db->where('id_dak_nf', $j);
        return $this->db->get();
    }
    function get_pagu_kab_nf2($p, $k, $j,$tahun){
        $provinsi  = "";
        $kabupaten = "";
        $provinsi = $this->db->where('KodeProvinsi', $p);
        $kabupaten = $this->db->where('KodeKabupaten', $k);   
        $this->db->select("SUM(dak_nf_pagus.pagu) as pagu ");
        $this->db->from("dak_nf_pagus");
        $provinsi;
        $kabupaten;
        $this->db->where('TAHUN_ANGGARAN', $tahun);
        $this->db->where('id_dak_nf', $j);
        return $this->db->get();
    }
    function get_monev_rka($p, $k,$jenis_dak,$kategori, $waktu_laporan, $tahun, $kdrs =null){
        $provinsi = "";
        $kabupaten ="";
        if ($p != '0') {
            $provinsi = $this->db->where('ref_kabupaten.KodeProvinsi', $p); 
        }

        if($k != '0'){
            $kabupaten = $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }
        if($waktu_laporan != '0'){
            $this->db->where('pengajuan_monev_dak.waktu_laporan', $waktu_laporan);
        }
        if($jenis_dak != '0'){
            $this->db->where('pengajuan_monev_dak.ID_SUBBIDANG', $jenis_dak); 
        }
        if($kategori != '0'){
            $this->db->where('pengajuan_monev_dak.ID_KATEGORI', $kategori);  
        }
        if($kdrs != null){
            $this->db->where('pengajuan_monev_dak.KD_RS', $kdrs);   
        }
        $this->db->select('ref_kabupaten.NamaKabupaten, ref_provinsi.NamaProvinsi, pengajuan_monev_dak.data_pendukung1 as RKA, pengajuan_monev_dak.data_pendukung2 as rekap_sp2d, pengajuan_monev_dak.data_pendukung3 as Dokumentasi, pengajuan_monev_dak.data_pendukung4 as data_lain, dak_jenis_dak.NAMA_JENIS_DAK as jenis_dak, kategori.NAMA_KATEGORI as kategori, pengajuan_monev_dak.waktu_laporan, pengajuan_monev_dak.id_pengajuan, pengajuan_monev_dak.KD_RS, pengajuan_monev_dak.ID_SUBBIDANG ');
        $this->db->from('ref_kabupaten');    
        $this->db->join('pengajuan_monev_dak','ref_kabupaten.KodeKabupaten=pengajuan_monev_dak.KodeKabupaten and pengajuan_monev_dak.KodeProvinsi = ref_kabupaten.KodeProvinsi');
        $this->db->join('ref_provinsi','ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi'); 
        $this->db->join('dak_jenis_dak','dak_jenis_dak.ID_JENIS_DAK=pengajuan_monev_dak.ID_SUBBIDANG'); 
        $this->db->join('kategori','kategori.ID_KATEGORI=pengajuan_monev_dak.ID_KATEGORI');    
        $this->db->where('pengajuan_monev_dak.TAHUN_ANGGARAN', $tahun);  
        $provinsi;
        $kabupaten;
        $this->db->group_by('pengajuan_monev_dak.id_pengajuan');
        $this->db->order_by('ref_kabupaten.KodeProvinsi, ref_kabupaten.KodeKabupaten ');       
        return $this->db->get();
    }
    function get_monev_nf($p, $k, $waktu_laporan, $tahun){
        $provinsi = "";
        $kabupaten ="";
        if ($p != '0') {
            $provinsi = $this->db->where('ref_kabupaten.KodeProvinsi', $p); 
        }

        if($k != '0'){
            $kabupaten = $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }

        $this->db->select("*");
        $this->db->from("ref_kabupaten");
        $this->db->join('pengajuan_monev_nf','ref_kabupaten.KodeKabupaten=pengajuan_monev_nf.KodeKabupaten and pengajuan_monev_nf.KodeProvinsi = ref_kabupaten.KodeProvinsi'); 
        $this->db->join('ref_provinsi','ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->where('pengajuan_monev_nf.waktu_laporan', $waktu_laporan); 
        $provinsi;
        $kabupaten;
        return $this->db->get();
    }
    function get_monev_nf2($p, $k, $j, $waktu_laporan, $tahun){
        $provinsi = "";
        $kabupaten ="";
        if ($p != '0') {
            $provinsi = $this->db->where('ref_kabupaten.KodeProvinsi', $p); 
        }
        if($k != '0'){
            $kabupaten = $this->db->where('ref_kabupaten.KodeKabupaten', $k);
        }
        
        $this->db->select("*");
        $this->db->from("ref_kabupaten");
        $this->db->join('dak_nf_laporan','ref_kabupaten.KodeKabupaten=dak_nf_laporan.KodeKabupaten and dak_nf_laporan.KodeProvinsi = ref_kabupaten.KodeProvinsi'); 
        $this->db->join('ref_provinsi','ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->where('dak_nf_laporan.waktu_laporan', $waktu_laporan);
        $this->db->where("TAHUN_ANGGARAN", $tahun);
        if($j != '0'){
                $this->db->where('dak_nf_laporan.id_dak_nf', $j);  
        }
        $provinsi;
        $kabupaten;
        $this->db->order_by('ref_kabupaten.id');
        return $this->db->get();
    }
    function get_monev_detail($p, $k,$jenis_dak,$kategori, $waktu_laporan, $tahun, $rs){
        $this->db->select("*");
        $this->db->from("data_monev_rka");
        $this->db->join('pengajuan_monev_dak','data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan'); 
        $this->db->join('(select * from menu where tahun = '.$tahun .') menu','menu.ID_MENU = data_monev_rka.kode_menu and ( pengajuan_monev_dak.ID_SUBBIDANG = menu.ID_SUBBIDANG or menu.ID_PENUGASAN = pengajuan_monev_dak.ID_SUBBIDANG)');
        $this->db->join('(select * from pagu where status = 1 and pagu.TAHUN_ANGGARAN = '.$tahun.') pagu','pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and pagu.ID_MENU = data_monev_rka.kode_menu and pengajuan_monev_dak.KD_RS = pagu.KODE_RS');
        $this->db->join('pagu_seluruh','pengajuan_monev_dak.KodeProvinsi = pagu_seluruh.KodeProvinsi and pagu_seluruh.KodeKabupaten = pengajuan_monev_dak.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu_seluruh.ID_SUBBIDANG and pengajuan_monev_dak.ID_KATEGORI = pagu_seluruh.ID_KATEGORI');
        $this->db->join('ref_satuan','ref_satuan.KodeSatuan = menu.KodeSatuan');
        $this->db->join("dak_capaian_output do", "do.id_pengajuan = pengajuan_monev_dak.id_pengajuan and do.ID_MENU = data_monev_rka.kode_menu", "LEFT");
        $this->db->join('permasalahan_dak','permasalahan_dak.KodeMasalah = data_monev_rka.KodeMasalah');
        $this->db->where('pengajuan_monev_dak.KodeProvinsi', $p);
        $this->db->where('pengajuan_monev_dak.KodeKabupaten', $k);
        $this->db->where('pengajuan_monev_dak.ID_SUBBIDANG', $jenis_dak); 
        $this->db->where('pengajuan_monev_dak.ID_KATEGORI', $kategori); 
        $this->db->where('pengajuan_monev_dak.waktu_laporan', $waktu_laporan); 
        $this->db->where('pengajuan_monev_dak.TAHUN_ANGGARAN', $tahun); 
        $this->db->where('pengajuan_monev_dak.KD_RS', $rs); 
        // $this->db->where('pagu.status', 1);
        $this->db->group_by('data_monev_rka.kode_menu');
        return $this->db->get();
    }function get_monev_detail2($p="", $tw="", $tahun=""){
        $this->db->select("*");
        $this->db->from("data_monev_rka");
        $this->db->join('pengajuan_monev_dak','data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan'); 
        $this->db->join('(select * from menu where tahun = '. $tahun .') menu','menu.ID_MENU = data_monev_rka.kode_menu and ( pengajuan_monev_dak.ID_SUBBIDANG = menu.ID_SUBBIDANG or menu.ID_PENUGASAN = pengajuan_monev_dak.ID_SUBBIDANG)');
        $this->db->join('(select * from pagu where status = 1 and pagu.TAHUN_ANGGARAN = '.$tahun.') pagu','pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and pagu.ID_MENU = data_monev_rka.kode_menu and pengajuan_monev_dak.KD_RS = pagu.KODE_RS');
        $this->db->join('ref_satuan','ref_satuan.KodeSatuan = menu.KodeSatuan');
        $this->db->join('permasalahan_dak','permasalahan_dak.KodeMasalah = data_monev_rka.KodeMasalah');
        $this->db->where('pengajuan_monev_dak.KodeProvinsi', $p);
         $this->db->where('pengajuan_monev_dak.WAKTU_LAPORAN', $tw);
        // $this->db->where('pagu.status', 1);
        $this->db->group_by('data_monev_rka.kode_menu');
        $this->db->order_by('pengajuan_monev_dak.KodeKabupaten');
        return $this->db->get();
    }function get_monev_detail32($tw="", $tahun=""){
        $this->db->select("*");
        $this->db->from("data_monev_rka");
        $this->db->join('pengajuan_monev_dak','data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan'); 
        $this->db->join('(select * from menu where tahun = '. $tahun .') menu','menu.ID_MENU = data_monev_rka.kode_menu and ( pengajuan_monev_dak.ID_SUBBIDANG = menu.ID_SUBBIDANG or menu.ID_PENUGASAN = pengajuan_monev_dak.ID_SUBBIDANG)');
        $this->db->join('(select * from pagu where status = 1 and pagu.TAHUN_ANGGARAN = '.$tahun.') pagu','pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and pagu.ID_MENU = data_monev_rka.kode_menu and pengajuan_monev_dak.KD_RS = pagu.KODE_RS');
        $this->db->join('ref_satuan','ref_satuan.KodeSatuan = menu.KodeSatuan');
        $this->db->join('permasalahan_dak','permasalahan_dak.KodeMasalah = data_monev_rka.KodeMasalah');
        // $this->db->where('pengajuan_monev_dak.KodeProvinsi', $p);
         $this->db->where('pengajuan_monev_dak.WAKTU_LAPORAN', $tw);
        // $this->db->where('pagu.status', 1);
        $this->db->group_by('data_monev_rka.kode_menu');
        $this->db->order_by('pengajuan_monev_dak.KodeKabupaten');
        return $this->db->get();
    }

    function get_laporan_nf($provinsi, $kabupaten, $jenis_dak, $waktu_laporan, $tahun, $kdrs){
        $this->db->select('*');
        $this->db->from('dak_nf_laporan');
        $this->db->join('dak_nf_rka', 'dak_nf_rka.id_pengajuan =  dak_nf_laporan.id_pengajuan');
        $this->db->join('dak_nf_pagus', 'dak_nf_pagus.id_dak_nf =  dak_nf_laporan.id_dak_nf and dak_nf_laporan.KodeProvinsi = dak_nf_pagus.KodeProvinsi and dak_nf_laporan.KodeKabupaten = dak_nf_pagus.KodeKabupaten');
        $this->db->join('dak_nf_pagu', 'dak_nf_pagu.id_dak_nf =  dak_nf_laporan.id_dak_nf and dak_nf_laporan.KodeProvinsi = dak_nf_pagu.KodeProvinsi and dak_nf_laporan.KodeKabupaten = dak_nf_pagu.KodeKabupaten and dak_nf_pagu.id_menu_nf = dak_nf_rka.id_menu_nf');
        $this->db->where('dak_nf_laporan.KodeProvinsi', $provinsi);
        $this->db->where('dak_nf_laporan.KodeKabupaten', $kabupaten);
        $this->db->where('dak_nf_laporan.id_dak_nf', $jenis_dak); 
        $this->db->where('dak_nf_laporan.waktu_laporan', $waktu_laporan); 
        $this->db->where('dak_nf_laporan.TAHUN_ANGGARAN', $tahun);
        if($kdrs != 0 && $kdrs != null){
            $this->db->where('dak_nf_laporan.KD_RS', $kdrs);
        }
        $this->db->group_by('dak_nf_rka.id_menu_nf');
        return $this->db->get();
    }
    function get_laporan_nf2($provinsi, $kabupaten, $waktu_laporan, $tahun, $kdrs){
        $this->db->select('*');
        $this->db->from('dak_nf_laporan');
        $this->db->join('dak_nf_rka', 'dak_nf_rka.id_pengajuan =  dak_nf_laporan.id_pengajuan');
        $this->db->join('dak_nf_pagus', 'dak_nf_pagus.id_dak_nf =  dak_nf_laporan.id_dak_nf and dak_nf_laporan.KodeProvinsi = dak_nf_pagus.KodeProvinsi and dak_nf_laporan.KodeKabupaten = dak_nf_pagus.KodeKabupaten');
        $this->db->join('dak_nf_pagu', 'dak_nf_pagu.id_dak_nf =  dak_nf_laporan.id_dak_nf and dak_nf_laporan.KodeProvinsi = dak_nf_pagu.KodeProvinsi and dak_nf_laporan.KodeKabupaten = dak_nf_pagu.KodeKabupaten and dak_nf_pagu.id_menu_nf = dak_nf_rka.id_menu_nf');
        $this->db->where('dak_nf_laporan.KodeProvinsi', $provinsi);
        $this->db->where('dak_nf_laporan.KodeKabupaten', $kabupaten);
        $this->db->where('dak_nf_laporan.waktu_laporan', $waktu_laporan); 
        $this->db->where('dak_nf_laporan.TAHUN_ANGGARAN', $tahun);
        if($kdrs != 0 && $kdrs != null){
            $this->db->where('dak_nf_laporan.KD_RS', $kdrs);
        }
        
        return $this->db->get();
    }

    function get_monev_detail_nf($p, $k, $waktu_laporan, $tahun){
        $this->db->select("*");
        $this->db->from("data_monev_nf");
        $this->db->join('pengajuan_monev_nf','data_monev_nf.id_pengajuan=pengajuan_monev_nf.id_pengajuan'); 
        $this->db->join('pagu_nf','pengajuan_monev_nf.KodeProvinsi = pagu_nf.KodeProvinsi and pengajuan_monev_nf.KodeKabupaten = pagu_nf.KodeKabupaten and pagu_nf.id_menu_nf = data_monev_nf.kode_menu');
        $this->db->join('pagu_seluruh_nf','pengajuan_monev_nf.KodeProvinsi = pagu_seluruh_nf.KodeProvinsi and pagu_seluruh_nf.KodeKabupaten = pengajuan_monev_nf.KodeKabupaten');     
        $this->db->join('permasalahan_dak','permasalahan_dak.KodeMasalah = data_monev_nf.KodeMasalah');
        $this->db->where('pengajuan_monev_nf.KodeProvinsi', $p);
        $this->db->where('pengajuan_monev_nf.KodeKabupaten', $k);
        $this->db->where('pengajuan_monev_nf.waktu_laporan', $waktu_laporan); 
        $this->db->where('pengajuan_monev_nf.TAHUN_ANGGARAN', $tahun); 
        $this->db->group_by('data_monev_nf.kode_menu');
        return $this->db->get();
    }    
    function get_pagu_4($p,$k,$jenis_dak,$kategori,$waktu_laporan){
        if($k!='0'){
            $kabupaten=$this->db->where('ref_kabupaten.KodeKabupaten', $k);               
        }else{
            $kabupaten='';
        }
        $sum_realisasi="(SELECT sum(jumlah)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan ) as realisasi";
        $sum_fisik=" (SELECT avg(fisik)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan ) as fisik";
        $sum_persentasi="(select (sum(data_monev_rka.jumlah)/pagu_seluruh.pagu_seluruh)*100 from pagu_seluruh,data_monev_rka,pengajuan_monev_dak WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan) as persentase";    
        $this->db->select("*");
        $this->db->select($sum_realisasi);
        $this->db->select($sum_fisik);
        $this->db->select($sum_persentasi);
        $this->db->from('pagu_seluruh');
        $this->db->join('dak_jenis_dak','pagu_seluruh.ID_SUBBIDANG=dak_jenis_dak.ID_JENIS_DAK');
        $this->db->join('ref_provinsi','pagu_seluruh.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_kabupaten','pagu_seluruh.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');        
        $this->db->join('pengajuan_monev_dak','pagu_seluruh.KodeKabupaten=pengajuan_monev_dak.KodeKabupaten AND ref_provinsi.KodeProvinsi=pengajuan_monev_dak.KodeProvinsi AND pagu_seluruh.ID_SUBBIDANG=pengajuan_monev_dak.ID_SUBBIDANG');
        $this->db->join('kategori','pagu_seluruh.ID_KATEGORI=kategori.ID_KATEGORI and pengajuan_monev_dak.ID_KATEGORI=kategori.ID_KATEGORI');        
        $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        $kabupaten;
        $this->db->where('dak_jenis_dak.ID_JENIS_DAK', $jenis_dak); 
        $this->db->where('kategori.ID_KATEGORI', $kategori); 
        $this->db->where('waktu_laporan', $waktu_laporan);        
        $this->db->group_by('pengajuan_monev_dak.id_pengajuan');
        return $this->db->get();
    }    

    function get_pagu_3($p,$k,$jenis_dak,$kategori){
        if($k!='0'){
            $kabupaten=$this->db->where('ref_kabupaten.KodeKabupaten', $k);               
        }else{
            $kabupaten='';
        }
        $this->db->select('*');
        $this->db->from('pagu_seluruh');
        $this->db->join('dak_jenis_dak','pagu_seluruh.ID_SUBBIDANG=dak_jenis_dak.ID_JENIS_DAK and pagu_seluruh.TAHUN_ANGGARAN = dak_jenis_dak.TAHUN_ANGGARAN', "left");
        $this->db->join('kategori','pagu_seluruh.ID_KATEGORI=kategori.ID_KATEGORI');
        $this->db->join('ref_provinsi','pagu_seluruh.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_kabupaten','pagu_seluruh.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        $kabupaten;
        $this->db->where('dak_jenis_dak.ID_JENIS_DAK', $jenis_dak); 
        $this->db->where('pagu_seluruh.TAHUN_ANGGARAN', $this->session->userdata('thn_anggaran')); 
        $this->db->where('kategori.ID_KATEGORI', $kategori);        
        
        return $this->db->get();
    }

     function get_pagu_rs($p,$k,$jenis_dak,$kategori, $rs){
        if($k!='0'){
            $kabupaten=$this->db->where('ref_kabupaten.KodeKabupaten', $k);               
        }else{
            $kabupaten='';
        }
        $this->db->select('*');
        $this->db->from('pagu_rs');
        $this->db->join('dak_jenis_dak','pagu_rs.ID_Jenis_DAK=dak_jenis_dak.ID_JENIS_DAK');
        $this->db->join('ref_provinsi','pagu_rs.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_kabupaten','pagu_rs.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $this->db->join('data_rumah_sakit','pagu_rs.KODE_RS=data_rumah_sakit.KODE_RS');
        $this->db->where('ref_kabupaten.KodeProvinsi', $p);
        $kabupaten;
        $this->db->where('dak_jenis_dak.ID_JENIS_DAK', $jenis_dak);         
        $this->db->where('pagu_rs.KODE_RS', $rs);   
        return $this->db->get();
    }    

    function get_laporan($p,$k,$jenis_dak,$kategori,$waktu_laporan,$tahun){
        if($k!='0'){
            $kabupaten=$this->db->where('ref_kabupaten.KodeKabupaten', $k);               
        }else{
            $kabupaten='';
        }

        if($p!='0'){
            $provinsi=$this->db->where('ref_kabupaten.KodeProvinsi', $p);               
        }else{
            $provinsi='';
        }        
        $this->db->select('*');
        $this->db->from('pengajuan_monev_dak');
        $this->db->join('dak_jenis_dak','pengajuan_monev_dak.ID_SUBBIDANG=dak_jenis_dak.ID_JENIS_DAK');
        $this->db->join('kategori','pengajuan_monev_dak.ID_KATEGORI=kategori.ID_KATEGORI');
        $this->db->join('ref_provinsi','pengajuan_monev_dak.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->join('ref_kabupaten','pengajuan_monev_dak.KodeKabupaten=ref_kabupaten.KodeKabupaten AND ref_provinsi.KodeProvinsi=ref_kabupaten.KodeProvinsi');
        $provinsi;
        $kabupaten;
        $this->db->where('dak_jenis_dak.ID_JENIS_DAK', $jenis_dak); 
        $this->db->where('kategori.ID_KATEGORI', $kategori);
        $this->db->where('waktu_laporan', $waktu_laporan);            
        $this->db->where('TAHUN_ANGGARAN', $tahun);   
        return $this->db->get();
    }  
    function get_pagu2($k,$p,$kolom){

        return $this->db->where("KodeProvinsi ='$p'")
                        ->where("KodeKabupaten ='$k'")
                        ->where("$kolom !=0")
                        ->order_by('no', 'ASC')
                        ->get('data_pagu');
    }

    function get_pagu_nf($k,$p){

    return $this->db->where("KodeProvinsi ='$p'")
                    ->where("KodeKabupaten ='$k'")
                    ->order_by('no', 'ASC')
                    ->get('data_pagu_nf');
    }

    function get_pagu_keseluruhan($k,$p){
        $this->db->select("sum(Farmasi + Pelayanan_Dasar + Rujukan + Sarpras + Tambahan_Dak_Kesehatan) as total");
        $this->db->from('data_pagu');
        $this->db->where('KodeKabupaten', $k);
        $this->db->where('KodeProvinsi', $p);
        return $this->db->get();
    }

    function count_pagu($kolom){
        return $this->db->select("count($kolom) as $kolom")
                        ->where("$kolom !=0")
                        ->order_by('no', 'ASC')
                        ->get('data_pagu')
                    ->row()->$kolom;
    }

    function count_pagu_nf(){
        return $this->db->select("count(*) as jml")
                        ->order_by('no', 'ASC')
                        ->get('data_pagu_non_fisik')
                        ->row()->jml;
    }

    function get_pagu_keseluruhan_nf($k,$p){
        return $this->db->select('sum(BANTUAN_OPERASIONAL_KESEHATAN + AKREDITASI_RUMAH_SAKIT + AKREDITASI_PUSKESMAS + JAMINAN_PERSALINAN ) as total')
                        ->where('KodeKabupaten', $k)
                        ->where('KodeProvinsi', $p)
                        ->order_by('no', 'ASC')
                        ->get('data_pagu_nf');
    }

    function sum_kabupaten($l){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{
            $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN) as   perencanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2  , round(AVG( REALISASI_FISIK_PELAKSANAAN ), 0)as fisik
                                from dak_kegiatan
                                WHERE $l
                                order by ID_LAPORAN_DAK");
    }

    function sum_kegiatan($kolom,$l,$juknis){
        if($l==''){
        $l="ID_LAPORAN_DAK = 0";}
        else{
        $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select SUM($kolom) as $kolom
                                from dak_kegiatan
                                WHERE $l and ID_JENIS_KEGIATAN like '$juknis'
                                order by ID_LAPORAN_DAK");
    }
    function dak_kegiatan2($l){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{
            $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select *
                                from dak_kegiatan
                                WHERE $l
                                order by ID_LAPORAN_DAK");
    }

    function dak_kegiatan2_nf($l, $j){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";
        }
        else{
            $l='ID_LAPORAN_DAK in ('.$l.')';
        }

            if($j==''){
            $j="";}
        else{
        $j='and ID_JENIS_DAK='.$j;
            }
            return $this->db->query("select *
                                    from dak_kegiatan
                                    WHERE $l $j
                                    order by ID_LAPORAN_DAK");
    }


    function dak_kegiatan3($l,$juknis){
        if($l==''){
        $l="ID_LAPORAN_DAK = 0";}
        else{
        $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select *
                                from dak_kegiatan
                                WHERE $l  and ID_JENIS_KEGIATAN like '$juknis%'
                                order by ID_LAPORAN_DAK");
    }


    function dak_kegiatan_tambahan($l){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{
            $l='ID_LAPORAN_DAK in ('.$l.')';
        }   
            return $this->db->query("select *
                                    from dak_kegiatan_tambahan 
                                    WHERE $l 
                                    order by ID_LAPORAN_DAK");

    }
    function sum_kabupaten_sarpras($l){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{

          $l='ID_LAPORAN_DAK in ('.$l.')';
        }   
            return $this->db->query("select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2, round(AVG( REALISASI_FISIK_PELAKSANAAN ), 0) as fisik , SUM( JUMLAH_TOTAL_PERENCANAAN) as perencanaan
                                    from dak_kegiatan_sarpras 
                                    WHERE $l 
                                    order by ID_LAPORAN_DAK");

    }
    function sum_kabupaten_tambahan($l){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{

          $l='ID_LAPORAN_DAK in ('.$l.')';
        }   
            return $this->db->query("select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2, round(AVG( REALISASI_FISIK_PELAKSANAAN ), 0) as fisik , SUM( JUMLAH_TOTAL_PERENCANAAN) as perencanaan
                                    from dak_kegiatan_tambahan 
                                    WHERE $l 
                                    order by ID_LAPORAN_DAK");

    }    
    function sum_kabupaten_total($l){
        if($l==''){
        $l="ID_LAPORAN_DAK = 0";}
        else{
        $l='ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select sum(pelaksanaan) as pelaksanaan, sum(pelaksanaan2) as pelaksanaan2 from (
                                select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2 from dak_kegiatan_sarpras WHERE $l
                                union all
                                select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan  , SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2 from dak_kegiatan WHERE $l
                                )a
                                ");
    }
    function cek_sarpras($l){
        if($l==''){
        $l="0";
        }
        $this->db->select('count(*) as jml');
        $this->db->from('dak_kegiatan_sarpras');
        $this->db->where('ID_LAPORAN_DAK',$l);
        return $this->db->get();
                
    }
    function sum_kabupaten_nf($l,$d){
        if($l==''){
            $l="ID_LAPORAN_DAK = 0";}
        else{
        $l='ID_LAPORAN_DAK ='.$l.' ';
        }
        if($d==''|| $d==0){
            $d="";}
        else{
        $d='and ID_JENIS_DAK ='.$d.' ';
                }
            return $this->db->query("select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan ,REALISASI_FISIK_PELAKSANAAN  ,SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2 
                                    from dak_kegiatan_nf
                                    WHERE $l $d
                                    order by ID_LAPORAN_DAK");
    }

    function sum_kabupaten_nf2($l,$d){
        if($l==''){
        $l="ID_LAPORAN_DAK = 0";}
        else{
        $l='ID_LAPORAN_DAK in('.$l.')';
        }
        if($d==''|| $d==0){
        $d="";}
        else{
        $d='and ID_JENIS_DAK ='.$d.' ';
        }
        return $this->db->query("select SUM( REALISASI_KEUANGAN_PELAKSANAAN ) as pelaksanaan ,SUM( JUMLAH_TOTAL_PERENCANAAN ) as pelaksanaan2,REALISASI_FISIK_PELAKSANAAN, AVG(REALISASI_FISIK_PELAKSANAAN) as rata
        from dak_kegiatan_nf
        WHERE $l $d
        order by ID_LAPORAN_DAK");
    }
        function get_dak_laporan($id){

         return $this->db->where("(ID_LAPORAN_DAK ='$id' )")
                         ->order_by('ID_LAPORAN_DAK', 'ASC')
                         ->get('dak_laporan');
        }

        function get_dak_laporan_nf($id){

             return $this->db->where("(ID_LAPORAN_DAK ='$id' )")
                         ->order_by('ID_LAPORAN_DAK', 'ASC')
                         ->get('dak_laporan_nf');
        }
    function last_laporan(){

        $query=$this->db->select('ID_LAPORAN_DAK')
                    ->limit('1')
                    ->order_by('ID_LAPORAN_DAK', 'DESC')
                    ->get('dak_laporan_nf')
                    ->row();
        return $query->ID_LAPORAN_DAK;
    }

    function data_xl($id){

    return $query=$this->db->select('*')
                            ->where("(ID_LAPORAN_DAK ='$id' )")
                            ->limit('1')
                            ->get('dak_laporan');

    }
        
    function cek3($tabel, $kolom1, $param1, $kolom2, $param2, $kolom3, $param3){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom1, $param1);
        $this->db->where($kolom2, $param2);
        $this->db->where($kolom3, $param3);
        $return = $this->db->get();
        
            if($return->num_rows() > 0)
            return true;
        else
            return false;
    }

    function hapus_tupoksi($KD_PENGAJUAN){
        $this->db->where('KD_PENGAJUAN',$KD_PENGAJUAN);
        $this->db->delete('data_tupoksi');
    }
    
    function hapus($tabel, $kolom, $parameter){
        $this->db->where($kolom,$parameter);
        $this->db->delete($tabel);
    }
    
    function get_pengajuan(){
        $start = isset($paramArr['start'])?$paramArr['start']:NULL;
        $limit = isset($paramArr['limit'])?$paramArr['start']:NULL;
        $sortField = isset($paramArr['sortField'])?$paramArr['sortField']:'JUDUL_PROPOSAL';
        $sortOrder = isset($paramArr['sortOrder'])?$paramArr['sortOrder']:'asc';
        $whereParam = isset($paramArr['whereParam'])?$paramArr['whereParam']:NULL;
        if(!empty($start) && !empty($limit)) $optLimit = "limit $start,$limit";
        else $optLimit = NULL;
        
        if(!empty($whereParam)) $whereParam = "and (".$whereParam.")";
        $whereClause = "where true ".$whereParam;
        
        $SQL = "SELECT * FROM pengajuan $whereClause order by $sortField $sortOrder $optLimit ";
        $result = $this->db->query($SQL);
        
        if($result->num_rows() > 0) {
        $custlist = $result->result();
        return $custlist;
        } else {
        return null;
        }
    }
    
    function get($tabel){
        $this->db->select('*');
        $this->db->from($tabel);
        return $this->db->get();
    }
    
    function get_where($tabel,$parameter,$kolom){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        return $this->db->get();
    }

    function get_order_desc($tabel, $kolom){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->order_by($kolom,'DESC');
        return $this->db->get();
    }

    function get_where_double_like($tabel,$parameter,$kolom,$l){
        if($l==''){
            $l="and ID_LAPORAN_DAK = 0";}
        else{
            $l='and ID_LAPORAN_DAK in ('.$l.')';
        }
        return $this->db->query("select *
                                from $tabel
                                WHERE $kolom like'$parameter%' $l
                                order by ID_LAPORAN_DAK");
    }

    function get_pagu_indonesia($provinsi, $subbidang){

    }
    function get_where_double($tabel,$parameter,$kolom,$parameter2,$kolom2){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        return $this->db->get();
    }

    
    public function get_where_double4($tahun='',$jub='')
    {
        $sql="  SELECT
                *
                from menu
                WHERE TAHUN ='$tahun'
                and ID_SUBBIDANG='$jub'
                GROUP BY NAMA";
        return $this->db->query($sql);
    }
function get_where_in3($tabel,$parameter,$kolom,$parameter2,$kolom2,$parameter3,$kolom3){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where_in($kolom3,$parameter3);
        return $this->db->get();
    }
    
    function get_where_triple($tabel,$parameter,$kolom,$parameter2,$kolom2,$parameter3,$kolom3){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where($kolom3,$parameter3);
        return $this->db->get();
    }
    function get_where_quadruple($tabel,$parameter,$kolom,$parameter2,$kolom2,$parameter3,$kolom3, $parameter4,$kolom4){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where($kolom3,$parameter3);
        $this->db->where($kolom4,$parameter4);
        return $this->db->get();
    }
    function get_where_5($tabel,$parameter,$kolom,$parameter2,$kolom2,$parameter3,$kolom3, $parameter4,$kolom4, $parameter5,$kolom5){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where($kolom3,$parameter3);
        $this->db->where($kolom4,$parameter4);
        $this->db->where($kolom5,$parameter5);
        return $this->db->get();
    }
    function get_where_6($tabel,$parameter,$kolom,$parameter2,$kolom2,$parameter3,$kolom3, $parameter4,$kolom4, $parameter5,$kolom5, $parameter6,$kolom6){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->where($kolom,$parameter);
        $this->db->where($kolom2,$parameter2);
        $this->db->where($kolom3,$parameter3);
        $this->db->where($kolom4,$parameter4);
        $this->db->where($kolom5,$parameter5);
        $this->db->where($kolom6,$parameter6);
        return $this->db->get();
    }
    function get_n_dashboard_dak_rs(){
        $this->db->select("ID_Jenis_DAK");
        $this->db->select("COUNT(KODE_RS) as n");
        $this->db->from("pagu_rs");
        $this->db->where("pagu_rs.PAGU_SELURUH >", "0");
        $this->db->where('pagu_rs.TAHUN_ANGGARAN', $this->session->userdata('thn_anggaran'));   
        $this->db->group_by("ID_Jenis_DAK");
        return $this->db->get();
    }
    function get_n_dashboard_dak_puskes(){
        $this->db->select("COUNT(KodePuskesmas) as n");
        $this->db->from("pagu_puskesmas");
        $this->db->where("pagu_puskesmas.PAGU_SELURUH >", "0"); 
        $this->db->where('pagu_puskesmas.TAHUN_ANGGARAN', $this->session->userdata('thn_anggaran'));   
        return $this->db->get();
    }
    function get_n_non_rs(){
        $this->db->select("ID_SUBBIDANG");  
        $this->db->select("COUNT(KodeProvinsi) as n");
        $this->db->from("pagu_seluruh");
        $this->db->where("pagu_seluruh.PAGU_SELURUH >", "0");
        $this->db->where('pagu_seluruh.TAHUN_ANGGARAN', $this->session->userdata('thn_anggaran'));
        $this->db->group_by("ID_SUBBIDANG");
        return $this->db->get(); 
    }
    function get_group_by($table, $kolom1){
        $this->db->select("*");
        $this->db->from($table); 
        $this->db->group_by($kolom1);
        return $this->db->get();
    }
    function get_where_satker($tabel,$parameter,$kolom,$parameter2,$kolom2){
        return $this->db->query("select *
                            from $tabel
                            WHERE $kolom = $parameter and $kolom2 = $parameter2 and STATUS!=2
                            ");
    }
    
    function get_data_pengajuan($kd_pengajuan){
        $this->db->select('*');
        $this->db->from('pengajuan');
        $this->db->join('ref_satker','pengajuan.NO_REG_SATKER=ref_satker.kdsatker');
        $this->db->join('ref_provinsi','ref_satker.kdlokasi=ref_provinsi.KodeProvinsi');
        $this->db->where('KD_PENGAJUAN',$kd_pengajuan);
        $this->db->order_by('TANGGAL_PENGAJUAN', 'desc');
        return $this->db->get();
    }

    function get_data_satker($kd_satker){
        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->join('ref_provinsi','ref_satker.kdlokasi=ref_provinsi.KodeProvinsi');
        $this->db->where('kdsatker',$kd_satker);
        return $this->db->get();
    }
    function get_data_provinsi($kd_provinsi){
    $this->db->select('*');
    $this->db->from('ref_provinsi');
    $this->db->where('KodeProvinsi',$kd_provinsi);
    return $this->db->get();
    }
    function get_data_kabupaten($kd_provinsi){
        if($kd_provinsi!=0){
            $p=$this->db->where('ref_kabupaten.KodeProvinsi',$kd_provinsi);
        }else{
            $p='';
        }
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $p;
        $this->db->join('ref_provinsi','ref_kabupaten.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->order_by('ref_kabupaten.id');
        return $this->db->get();
    }

    function sum_pagu_rs($KodeProvinsi, $KodeKabupaten, $jenis_dak, $kategori, $KODE_RS){
        $this->db->select("sum(PAGU) as pagu");
        $this->db->from("pagu");
        $this->db->where("KodeProvinsi", $KodeProvinsi);
        $this->db->where("KodeKabupaten", $KodeKabupaten);
        $this->db->where("ID_JENIS_DAK", $jenis_dak);
        $this->db->where("ID_KATEGORI", $kategori);
        $this->db->where("TAHUN_ANGGARAN", $this->session->userdata("thn_anggaran"));
        $this->db->where("KODE_RS", $KODE_RS);
        return $this->db->get();
    }
     function get_data_kabupaten2($kd_provinsi, $kd_kabupaten){
        if($kd_provinsi!='0'){
            $p=$this->db->where('ref_kabupaten.KodeProvinsi',$kd_provinsi);
        }else{
            $p='';
        }
        if($kd_kabupaten!='0'){
            $k=$this->db->where('ref_kabupaten.KodeKabupaten',$kd_kabupaten);
        }else{
            $k='';
        }
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $p;
        $this->db->join('ref_provinsi','ref_kabupaten.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->order_by('ref_kabupaten.id');
        return $this->db->get();
    }
    function get_nama_kabupaten($kd_provinsi,$kd_kabupaten){
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $this->db->where('KodeProvinsi',$kd_provinsi);
        $this->db->where('KodeKabupaten',$kd_kabupaten);
        return $this->db->get();
    }
        
    function get_provinsi(){
        $this->db->select('*');
        $this->db->from('ref_provinsi');
            $this->db->order_by('NamaProvinsi','asc');
        return $this->db->get();
    }
    function get_provinsi_limit($limit,$start){
        $this->db->select('*');
        $this->db->from('ref_provinsi');
        $this->db->limit($limit, $start);
        $this->db->order_by('KodeProvinsi','asc');
        return $this->db->get();
    }

    function get_data_kabupaten_limit($kd_provinsi,$limit,$start,$kd_kabupaten){
        if($kd_kabupaten!=0){
        $k=$this->db->where('ref_kabupaten.KodeKabupaten',$kd_kabupaten);
        }
        else if($kd_kabupaten==0 && $kd_provinsi!=0 && $this->session->userdata('kd_role')==17) {
        $k=$this->db->where('ref_kabupaten.KodeKabupaten','00');
        }else {
        $k='';
        }
        if($kd_provinsi!=0){
        $p=$this->db->where('ref_kabupaten.KodeProvinsi',$kd_provinsi);
            }else{
        $p='';
        }
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $p;
        $k;
        $this->db->join('ref_provinsi','ref_kabupaten.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->limit($limit, $start);
        $this->db->order_by('ref_provinsi.KodeProvinsi','asc');
        return $this->db->get();
    }

    function get_last_menu($table,$id){

         $query ="select * from $table where $id order by NO_URUT DESC limit 1";
         return $this->db->query($query);

    }



    function get_data_kabupaten_limit2($kd_provinsi,$kd_kabupaten){
        if($kd_kabupaten!=0){
            $k=$this->db->where('ref_kabupaten.KodeKabupaten',$kd_kabupaten);
        }else{
            $k='';
        }
        if($kd_provinsi!=0){
            $p=$this->db->where('ref_kabupaten.KodeProvinsi',$kd_provinsi);
        }else{
            $p='';
        }
        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $p;
        $k;
        $this->db->join('ref_provinsi','ref_kabupaten.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->order_by('ref_provinsi.KodeProvinsi','asc');
        return $this->db->get();
    }

    function get_pengajuan_monev($id){
        $this->db->select("*");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("data_monev_rka","data_monev_rka.id_pengajuan = pengajuan_monev_dak.id_pengajuan");
        $this->db->join("(select * from pagu where status = 1 and TAHUN_ANGGARAN = ".$this->session->userdata('thn_anggaran').") pagu", "pagu.ID_MENU=data_monev_rka.kode_menu and pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and pengajuan_monev_dak.ID_KATEGORI = pagu.ID_KATEGORI and pengajuan_monev_dak.KD_RS = pagu.KODE_RS");
        $this->db->join("(select * from pagu_seluruh where TAHUN_ANGGARAN = ".$this->session->userdata('thn_anggaran').") pagu_seluruh", "pagu_seluruh.KodeProvinsi = pengajuan_monev_dak.KodeProvinsi and pagu_seluruh.KodeKabupaten = pengajuan_monev_dak.KodeKabupaten and pagu_seluruh.ID_SUBBIDANG = pengajuan_monev_dak.ID_SUBBIDANG and pengajuan_monev_dak.ID_KATEGORI = pagu_seluruh.ID_KATEGORI");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = pagu.SATUAN");
        $this->db->join("permasalahan_dak", "permasalahan_dak.KodeMasalah = data_monev_rka.KodeMasalah");
        $this->db->where("data_monev_rka.id_pengajuan", $id);
        $this->db->where("pagu.status", 1);
        $this->db->group_by('kode_menu');
        $this->db->order_by("data_monev_rka.kode_menu");
        return $this->db->get();

    }

    function get_pengajuan_monev_rs($id){
        $this->db->select("*");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("data_monev_rka","data_monev_rka.id_pengajuan = pengajuan_monev_dak.id_pengajuan");
        $this->db->join("(select * from pagu where status = 1 and TAHUN_ANGGARAN = ".$this->session->userdata('thn_anggaran').") pagu", "pagu.ID_MENU=data_monev_rka.kode_menu and pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and pengajuan_monev_dak.ID_KATEGORI = pagu.ID_KATEGORI and pengajuan_monev_dak.KD_RS = pagu.KODE_RS");
        $this->db->join("(select * from pagu_rs where TAHUN_ANGGARAN = ".$this->session->userdata('thn_anggaran').") pagu_rs", "pagu_rs.KodeProvinsi = pengajuan_monev_dak.KodeProvinsi and pagu_rs.KodeKabupaten = pengajuan_monev_dak.KodeKabupaten and pagu_rs.ID_Jenis_DAK = pengajuan_monev_dak.ID_SUBBIDANG  and pengajuan_monev_dak.KD_RS = pagu_rs.KODE_RS");
        $this->db->join("ref_satuan", "ref_satuan.KodeSatuan = pagu.SATUAN");
        $this->db->join("permasalahan_dak", "permasalahan_dak.KodeMasalah = data_monev_rka.KodeMasalah");
        $this->db->where("data_monev_rka.id_pengajuan", $id);
        $this->db->where("pagu.status", 1);
        $this->db->group_by('kode_menu');
        $this->db->order_by("data_monev_rka.kode_menu");
        return $this->db->get();

    }



    function get_data_kabupaten2_limit($kd_provinsi,$limit, $start,$kd_kabupaten){
        if($kd_kabupaten!=0){
        $k=$this->db->where('ref_kabupaten.KodeKabupaten',$kd_kabupaten);
        }else{
        $k='';
        }
        if($kd_provinsi!=0){
        $p=$this->db->where('ref_kabupaten.KodeProvinsi',$kd_provinsi);
        }else{
        $p='';
        }

        $this->db->select('*');
        $this->db->from('ref_kabupaten');
        $p;
        $k;
        $this->db->join('ref_provinsi','ref_kabupaten.KodeProvinsi=ref_provinsi.KodeProvinsi');
        $this->db->limit($limit, $start);
        $this->db->order_by('ref_provinsi.KodeProvinsi','asc');
        return $this->db->get();
    }
        
    function get_satker(){
        foreach($this->get_kode_kementrian()->result() as $row){
            $KodeKementrian = $row->KodeKementrian;
        }
        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->where('kddept',$KodeKementrian);
        $this->db->order_by('nmsatker','asc');
        return $this->db->get();
    }
 
    function get_satker2(){

        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->where('kddept',$KodeKementrian);
        $this->db->order_by('nmsatker','asc');
        return $this->db->get();
    }

    function get_kegiatan_satker($kodeprogram){
        $this->db->select('*');
        $this->db->from('ref_satker_kegiatan');
        $this->db->join('ref_kegiatan', 'ref_kegiatan.KodeKegiatan = ref_satker_kegiatan.KodeKegiatan');
        $this->db->where('kdsatker',$this->session->userdata('kdsatker'));
        //$this->db->where('KodeSubFungsi',$kodefungsi.'.'.$kodesubfungsi);
        $this->db->where('KodeProgram',$kodeprogram);
        return $this->db->get();
    }
    
    function get_kegiatan($KodeFungsi, $KodeSubFungsi, $KodeProgram){
        if($this->get_kegiatan_satker()->result() != NULL){
            foreach($this->get_kegiatan_satker()->result() as $row){
                $kodekegiatan[] = $row->KodeKegiatan;
            }
            $this->db->select('*');
            $this->db->from('ref_kegiatan');
            $this->db->where_in('KodeKegiatan',$kodekegiatan);
            $this->db->where('KodeFungsi',$KodeFungsi);
            $this->db->where('KodeSubFungsi',$KodeFungsi.".".$KodeSubFungsi);
            $this->db->where('KodeProgram',$KodeProgram);
            return $this->db->get();
        }else return NULL;
    }
    
    function get_kode_kementrian(){
        $this->db->select('*');
        $this->db->from('ref_kode_kementrian');
        return $this->db->get();
    }
    
    function get_fungsi(){
        $this->db->select('*');
        $this->db->from('ref_fungsi');
        return $this->db->get();
    }
    
    function search($keyword, $kolom, $tabel){
        $this->db->select('*');
        $this->db->from($tabel);
                $this->db->like($kolom, $keyword);
        return $this->db->get();
    }
    
    function search_sub_fungsi($kodeFungsi, $keyword, $kolom){
        $this->db->select('*');
        $this->db->from('ref_sub_fungsi');
        $this->db->where('KodeFungsi',$kodeFungsi);
        $this->db->like($kolom, $keyword);
        return $this->db->get();
    }
    
    function search_kegiatan($KodeFungsi, $KodeSubFungsi, $KodeProgram,$keyword,$kolom){
        if($this->get_kegiatan_satker()->result() != NULL){
            foreach($this->get_kegiatan_satker()->result() as $row){
                $kodekegiatan[] = $row->KodeKegiatan;
            }
            $this->db->select('*');
            $this->db->from('ref_kegiatan');
            $this->db->where_in('KodeKegiatan',$kodekegiatan);
            $this->db->where('KodeFungsi',$KodeFungsi);
            $this->db->where('KodeSubFungsi',$KodeFungsi.".".$KodeSubFungsi);
            $this->db->where('KodeProgram',$KodeProgram);
            $this->db->like($kolom, $keyword);
            return $this->db->get();
        }else return NULL;
    }
    
    
    function get_sub_fungsi($kode_fungsi){
        $this->db->select('*');
        $this->db->from('ref_sub_fungsi');
                $this->db->where('KodeFungsi',$kode_fungsi);
        return $this->db->get();
    }
    
    function get_program_kegiatan($kode_fungsi,$KodeSubFungsi){
        foreach($this->get_kode_kementrian()->result() as $row){
            $KodeKementrian = $row->KodeKementrian;
        }
        $this->db->select('KodeProgram');
        $this->db->from('ref_kegiatan');
        $this->db->where('KodeFungsi',$kode_fungsi);
        $this->db->where('KodeSubFungsi',$kode_fungsi.'.'.$KodeSubFungsi);
        $this->db->where('KodeKementerian',$KodeKementrian);
        return $this->db->get();
    }
    
    function get_program_satker(){
        $this->db->select('*');
        $this->db->from('ref_satker_program');
        $this->db->join('ref_program', 'ref_program.KodeProgram = ref_satker_program.KodeProgram');
        $this->db->where('kdsatker',$this->session->userdata('kdsatker'));
        return $this->db->get();
    }
        
    function get_program_manage($kode_fungsi,$KodeSubFungsi){
        if($this->get_program_satker()->result() != NULL){
            foreach($this->get_program_satker()->result() as $row){
                $kodeprogram[] = $row->KodeProgram;
            }
            $this->db->select('*');
            $this->db->from('ref_program');
            $this->db->where('KodeStatus','1');
            $this->db->where_in('KodeProgram',$kodeprogram);
            return $this->db->get();
        }else return NULL;
    }
    
    function search_program($kodeFungsi, $KodeSubFungsi, $keyword, $kolom){
        if($this->get_program_satker()->result() != NULL){
            foreach($this->get_program_satker()->result() as $row){
                $kodeprogram[] = $row->KodeProgram;
            }
            $this->db->select('*');
            $this->db->from('ref_program');
            $this->db->where_in('KodeProgram',$kodeprogram);
            $this->db->like($kolom, $keyword);
            return $this->db->get();
        }else return NULL;
    }
    
    function get_iku_satker($kodeprogram){
        $this->db->select('*');
        $this->db->from('ref_satker_iku');
        $this->db->join('ref_iku','ref_satker_iku.KodeIku = ref_iku.KodeIku');
        $this->db->where('kdsatker',$this->session->userdata('kdsatker'));
        $this->db->where('KodeProgram',$kodeprogram);
        return $this->db->get();
    }
    
    function get_iku($KodeProgram){
        if($this->get_iku_satker()->result() != NULL){
            foreach($this->get_iku_satker()->result() as $row){
                $kodeiku[] = $row->KodeIku;
            }
            $this->db->select('*');
            $this->db->from('ref_iku');
            $this->db->where_in('KodeIku',$kodeiku);
            return $this->db->get();
        }else return NULL;
    }
    
    function search_iku($KodeProgram, $keyword, $kolom){
        if($this->get_iku_satker()->result() != NULL){
            foreach($this->get_iku_satker()->result() as $row){
                $kodeiku[] = $row->KodeIku;
            }
            $this->db->select('*');
            $this->db->from('ref_iku');
            $this->db->where('KodeProgram', $KodeProgram);
            $this->db->where_in('KodeIku',$kodeiku);
            $this->db->like($kolom, $keyword);
            return $this->db->get();
        }else return NULL;
    }
    function get_target_iku($kodeiku,$tahun) {
        if($kodeiku != NULL){
            $this->db->select('*');
            $this->db->from('target_iku');
            $this->db->where('KodeIku',$kodeiku);
            $this->db->where('idThnAnggaran',$tahun);
            return $this->db->get();
        }else return NULL;
    }
    
    function get_ikk_satker($kodekegiatan){
        $this->db->select('*');
        $this->db->from('ref_satker_ikk');
        $this->db->join('ref_ikk','ref_satker_ikk.KodeIkk = ref_ikk.KodeIkk');
        $this->db->where('kdsatker',$this->session->userdata('kdsatker'));
        $this->db->where('KodeKegiatan',$kodekegiatan);
        return $this->db->get();
    }
    
    function get_ikk($KodeKegiatan){
        if($this->get_ikk_satker()->result() != NULL){
            foreach($this->get_ikk_satker()->result() as $row){
                $kodeikk[] = $row->KodeIkk;
            }
            $this->db->select('*');
            $this->db->from('ref_ikk');
            $this->db->where_in('KodeIkk',$kodeikk);
            $this->db->where_in('KodeKegiatan',$KodeKegiatan);
            return $this->db->get();
        }else return NULL;
    }
    
    function search_ikk($KodeKegiatan, $keyword, $kolom){
        if($this->get_ikk_satker()->result() != NULL){
            foreach($this->get_ikk_satker()->result() as $row){
                $kodeikk[] = $row->KodeIkk;
            }
            $this->db->select('*');
            $this->db->from('ref_ikk');
            $this->db->where('KodeKegiatan', $KodeKegiatan);
            $this->db->like($kolom, $keyword);
            return $this->db->get();
        }else return NULL;
    }
    
    function get_menu_kegiatan($KodeProgram,$KodeKegiatan,$KodeIkk){
        $this->db->select('*');
        $this->db->from('ref_menu_kegiatan');
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->where('KodeIkk',$KodeIkk);
        return $this->db->get();
    }
 
    function get_menu($d,$k,$provinsi,$kabupaten,$kdsatker){
        $this->db->select('*');
        $this->db->from('menu');
        $this->db->join('ref_satuan', 'menu.KodeSatuan = ref_satuan.KodeSatuan');
        $this->db->join('pagu', 'menu.ID_MENU = pagu.ID_MENU');
        $this->db->where('menu.ID_KATEGORI',$k);
        $this->db->where('ID_SUBBIDANG',$d);
        $this->db->where('KodeProvinsi',$provinsi);
        $this->db->where('pagu.status', 1); 
        $this->db->where('KodeKabupaten',$kabupaten);
        return $this->db->get();
    }

    function get_menu_monev($d,$k,$provinsi,$kabupaten){
        if($provinsi!=0){
            $p=$this->db->where('pengajuan_monev_dak.KodeProvinsi',$provinsi);
        }else{
            $p='';
        }
        if($kabupaten!=0){
            $kb=$this->db->where('pengajuan_monev_dak.KodeKabupaten',$kabupaten);
        }else{
            $kb='';
        }
        $sum_jumlah="(SELECT sum(jumlah)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan AND menu.ID_MENU =data_monev_rka.kode_menu)  as sum_jumlah";
        $sum_volume="(SELECT sum(volume)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan AND menu.ID_MENU =data_monev_rka.kode_menu)  as sum_volume"; 
        $sum_unit="(SELECT sum(unit_cost)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan AND menu.ID_MENU =data_monev_rka.kode_menu)  as sum_unit"; 
        $sum_fisik=" (SELECT avg(fisik)  
            FROM data_monev_rka 
            WHERE data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan AND menu.ID_MENU =data_monev_rka.kode_menu) as fisik";                              
        $this->db->select('*');
        $this->db->select($sum_jumlah);
        $this->db->select($sum_volume);
        $this->db->select($sum_unit);
        $this->db->select($sum_fisik);
        $this->db->from('menu');
        $this->db->join('pagu', 'menu.ID_MENU = pagu.ID_MENU');
        $this->db->join('pagu_seluruh','pagu.KodeProvinsi=pagu_seluruh.KodeProvinsi And pagu.KodeKabupaten=pagu_seluruh.KodeKabupaten AND pagu.ID_JENIS_DAK=pagu_seluruh.ID_SUBBIDANG AND pagu.ID_KATEGORI=pagu_seluruh.ID_KATEGORI');          
        $this->db->join('ref_satuan', 'menu.KodeSatuan = ref_satuan.KodeSatuan');
        $this->db->join('pengajuan_monev_dak','pagu_seluruh.KodeKabupaten=pengajuan_monev_dak.KodeKabupaten AND pagu_seluruh.KodeProvinsi=pengajuan_monev_dak.KodeProvinsi AND pagu_seluruh.ID_SUBBIDANG=pengajuan_monev_dak.ID_SUBBIDANG');        
        $this->db->where('menu.ID_KATEGORI',$k);
        $this->db->where('pengajuan_monev_dak.ID_SUBBIDANG',$d);
        $p;
        $kb;
        $this->db->group_by('menu.ID_MENU');
        return $this->db->get();
    }

    function search_menu_kegiatan($KodeIkk, $keyword, $kolom){
        $this->db->select('*');
        $this->db->from('ref_menu_kegiatan');
        $this->db->where('KodeIkk', $KodeIkk);
        $this->db->like($kolom, $keyword);
        return $this->db->get();
    }
    
    function save($data, $table){
        $this->db->insert($table, $data);
    }
    
    function update($table, $data, $kolom, $parameter){
        $this->db->where($kolom, $parameter);
        $this->db->update($table, $data);
    }
    
     function update_where($table, $data, $where){
        $this->db->where($where);
        $this->db->update($table, $data);
    }

    function update2($table, $data, $kolom, $parameter, $kolom2, $parameter2){
        $this->db->where($kolom, $parameter);
        $this->db->where($kolom2, $parameter2);
        $this->db->update($table, $data);
    }
    function delete($table, $kolom, $parameter){
        $this->db->where($kolom, $parameter);
        $this->db->delete($table);
    }
    
    function pengajuan($data){
        $this->db->insert('pengajuan',$data);
    }
    function pengajuan_edak($data){
        $this->db->insert('dak_laporan',$data);
    }
    function pengajuan_edak_nf($data){
        $this->db->insert('dak_laporan_nf',$data);
    }
    
    //filtering
    function get_program($kode_kementrian){
        $this->db->select('*');
        $this->db->from('ref_program');
        $this->db->where('KodeKementrian',$kode_kementrian);
        return $this->db->get();
    }
// Unit Utama
    function get_satker_unit_utama(){
        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->like('nmsatker','biro perencanaan dan anggaran');
        $this->db->or_like('nmsatker','inspektorat','both');
        $this->db->or_like('nmsatker','sekretariat ditjen');
        $this->db->or_like('nmsatker','sekretariat badan');
        /*$this->db->where('kdsatker', '416151');
        $this->db->or_where('kdsatker', '465827');
        $this->db->or_where('kdsatker', '465895');
        $this->db->or_where('kdsatker', '465909');
        $this->db->or_where('kdsatker', '466080');
        $this->db->or_where('kdsatker', '630870');
        $this->db->or_where('kdsatker', '465915');
        $this->db->or_where('kdsatker', '415366');*/
        $this->db->order_by('nmsatker', 'asc');
        return $this->db->get();
    }
// Kantor Pusat
    function get_satker_kp(){
        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->where('kdjnssat', '1');
        $this->db->not_like('nmsatker', 'biro perencanaan dan anggaran');
        $this->db->not_like('nmsatker', 'inspektorat', 'both');
        $this->db->not_like('nmsatker', 'sekretariat ditjen');
        $this->db->not_like('nmsatker', 'sekretariat badan');
        $this->db->order_by('nmsatker', 'asc');
        return $this->db->get();
    }
// Kantor Daerah
    function get_satker_kd(){
        $this->db->select('*');
        $this->db->from('ref_satker');
        $this->db->where('kdjnssat', '2');
        $this->db->order_by('nmsatker', 'asc');
        return $this->db->get();
    }
    function get_order($table, $kolom){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->order_by("kolom");
        return $this->db->get();
    }
    // function get_satker_rs(){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->like('nmsatker', 'rs');
        // $this->db->not_like('nmsatker', 'rsud');
        // $this->db->or_like('nmsatker', 'rumah sakit');
        // $this->db->not_like('nmsatker', 'rumah sakit umum daerah');
        // return $this->db->get();
    // }
    // function get_satker_kementerian(){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->like('nmsatker', 'direktorat');
        // return $this->db->get();
    // }
    // function get_satker_pusat(){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->like('nmsatker', 'pusat', 'after');
        // return $this->db->get();
    // }
    
// Dekon
    function get_satker_provinsi(){
        $this->db->select('*');
        $this->db->from('ref_satker');
        //$this->db->like('nmsatker', 'dinas kesehatan pro');
        // $this->db->where('kdunit', '01');
        // $this->db->where('kdunit','00');
        $this->db->where('kdkabkota','00');
        $this->db->where('nomorsp !=','');
        $this->db->where('kdkppn !=','');
        $this->db->where('kdunit_awa !=','');
        $this->db->order_by('kdlokasi', 'asc');
        return $this->db->get();
    }
// Tugas Pembantuan

    // function get_satker_tp2($kab,$prov){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->where('kdkabkota', $kab);
        // $this->db->where('kdlokasi', $prov);
        // //$this->db->where('kdunit', '01');
        // $this->db->like('nmsatker','rsud');
        // return $this->db->get();
    // }
    // function get_satker_tp3($kab,$prov){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->where('kdkabkota', $kab);
        // $this->db->where('kdlokasi', $prov);
        // //$this->db->where('kdunit', '01');
        // $this->db->like('nmsatker','rumah sakit umum daerah');
        // return $this->db->get();
    // }
    // function get_satker_tp4($kab,$prov){
        // $this->db->select('*');
        // $this->db->from('ref_satker');
        // $this->db->where('kdkabkota', $kab);
        // $this->db->where('kdlokasi', $prov);
        // //$this->db->where('kdunit', '01');
        // $this->db->like('nmsatker','dinas kesehatan k');
        // return $this->db->get();
    // }
    //cek database

    
    function sum4($tabel,$koloms,$koloms2,$koloms3,$koloms4, $kolom1,$param){
        $this->db->select_sum($koloms);
        $this->db->select_sum($koloms2);
        $this->db->select_sum($koloms3);
        $this->db->select_sum($koloms4);
        $this->db->from($tabel);
        $this->db->where($kolom1, $param);
        return  $this->db->get();

    }
    
    function sum2($tabel,$kolom, $kolom1,$param,$kolom2,$param2){
        $this->db->select_sum($kolom);
        $this->db->from($tabel);
        $this->db->where($kolom1, $param);
        $this->db->where($kolom2, $param2);
        $return = $this->db->get()->result();
        $Biaya = 0;
        foreach($return as $row){
            $Biaya = $row->$kolom;
        }
        return $Biaya;
    }

    function get_where_double2($tabel, $kolom1,$param,$kolom2,$param2){
        $this->db->select("REALISASI_KEUANGAN_PELAKSANAAN,REALISASI_FISIK_PELAKSANAAN , JUMLAH_PELAKSANAAN");
        $this->db->from($tabel);
        $this->db->where($kolom1, $param);
        $this->db->where($kolom2, $param2);
        return $this->db->get();
    }
    
    function get_biaya($tabel,$kolom, $kolom1,$param,$kolom2,$param2){
        $this->db->select_sum($kolom);
        $this->db->from($tabel);
        $this->db->where($kolom1, $param);
        $this->db->where($kolom2, $param2);
        $return = $this->db->get()->result();
        $Biaya = 0;
        foreach($return as $row){
            $Biaya = $row->$kolom;
        }
        return $Biaya;
    }
    
    function get_biaya_fp($kd_pengajuan, $kd_fp){
        $this->db->select_sum('Jumlah');
        $this->db->from('aktivitas');
        $this->db->join('fp_aktivitas', 'aktivitas.KodeAktivitas = fp_aktivitas.KodeAktivitas');
        $this->db->where('KD_PENGAJUAN', $kd_pengajuan);
        $this->db->where('idFokusPrioritas', $kd_fp);
        $return = $this->db->get()->result();
        $Biaya = 0;
        foreach($return as $row){
            $Biaya = $row->Jumlah;
        }
        return $Biaya;
    }
    
    function get_biaya_rk($kd_pengajuan, $kd_rk){
        $this->db->select_sum('Jumlah');
        $this->db->from('aktivitas');
        $this->db->join('rk_aktivitas', 'aktivitas.KodeAktivitas = rk_aktivitas.KodeAktivitas');
        $this->db->where('KD_PENGAJUAN', $kd_pengajuan);
        $this->db->where('idReformasiKesehatan', $kd_rk);
        $return = $this->db->get()->result();
        $Biaya = 0;
        foreach($return as $row){
            $Biaya = $row->Jumlah;
        }
        return $Biaya;
    }
    
    function hapus_kegiatan($KodePengajuan,$KodeFungsi, $KodeSubFungsi, $KodeProgram, $KodeKegiatan){
        $this->db->where('KD_PENGAJUAN',$KodePengajuan);
        $this->db->where('KodeFungsi',$KodeFungsi);
        $this->db->where('KodeSubFungsi',$KodeFungsi.".".$KodeSubFungsi);
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->delete('data_kegiatan');
    }
    
    function cek_ikk($KodePengajuan,$KodeFungsi, $KodeSubFungsi, $KodeProgram, $KodeKegiatan, $KodeIkk){
        $this->db->select('count(*) as jumlah');
        $this->db->from('data_ikk');
        $this->db->where('KD_PENGAJUAN',$KodePengajuan);
        $this->db->where('KodeFungsi',$KodeFungsi);
        $this->db->where('KodeSubFungsi',$KodeSubFungsi);
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->where('KodeIkk',$KodeIkk);
        return $this->db->get();
    }
    
    function hapus_ikk($KodePengajuan, $KodeFungsi, $KodeSubFungsi, $KodeProgram, $KodeKegiatan, $KodeIkk){
        $this->db->where('KD_PENGAJUAN',$KodePengajuan);
        $this->db->where('KodeFungsi',$KodeFungsi);
        $this->db->where('KodeSubFungsi',$KodeSubFungsi);
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->where('KodeIkk',$KodeIkk);
        $this->db->delete('data_ikk');
    }
    
    function cek_menu_kegiatan($KodePengajuan,$KodeFungsi, $KodeSubFungsi, $KodeProgram, $KodeKegiatan, $KodeIkk, $KodeMenuKegiatan){
        $this->db->select('count(*) as jumlah');
        $this->db->from('data_menu_kegiatan');
        $this->db->where('KD_PENGAJUAN',$KodePengajuan);
        $this->db->where('KodeFungsi',$KodeFungsi);
        $this->db->where('KodeSubFungsi',$KodeSubFungsi);
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->where('KodeIkk',$KodeIkk);
        $this->db->where('KodeMenuKegiatan',$KodeMenuKegiatan);
        return $this->db->get();
    }
    
    function hapus_menu_kegiatan($KodePengajuan, $KodeFungsi, $KodeSubFungsi, $KodeProgram, $KodeKegiatan, $KodeIkk, $KodeMenuKegiatan){
        $this->db->where('KD_PENGAJUAN',$KodePengajuan);
        $this->db->where('KodeFungsi',$KodeFungsi);
        $this->db->where('KodeSubFungsi',$KodeSubFungsi);
        $this->db->where('KodeProgram',$KodeProgram);
        $this->db->where('KodeKegiatan',$KodeKegiatan);
        $this->db->where('KodeIkk',$KodeIkk);
        $this->db->where('KodeMenuKegiatan',$KodeMenuKegiatan);
        $this->db->delete('data_menu_kegiatan');
    }
    
    function get_data_flexigrid($tabel)
    {
        $this->db->select('*');
        $this->db->from($tabel);
                $this->CI->flexigrid->build_query();
        $query['records'] = $this->db->get();
        
        $this->db->select('*');
        $this->db->from($tabel);
        $this->CI->flexigrid->build_query(FALSE);
        $query['record_count'] = $this->db->count_all_results();
        return $query;
    }
    
    function get_data_flexigrid_join($tabel,$tabel_join,$parameter_join){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join($tabel_join,$parameter_join);
        $this->CI->flexigrid->build_query();
        $query['records'] = $this->db->get();
        
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join($tabel_join,$parameter_join);
        $this->CI->flexigrid->build_query(FALSE);
        $query['record_count'] = $this->db->count_all_results();
        return $query;
    }
    
    function get_data_flexigrid_joins(){
        $this->db->select('*');
        $this->db->from('prioritas_program');
        //$this->db->join('ref_jenis_prioritas','ref_jenis_prioritas.KodeJenisPrioritas=prioritas_program.KodeJenisPrioritas');
        $this->db->join('ref_periode','ref_periode.idPeriode=prioritas_program.idPeriode');
        $this->db->join('ref_tahun_anggaran','ref_tahun_anggaran.idThnAnggaran=prioritas_program.idThnAnggaran');
        $this->db->group_by('prioritas_program.idThnAnggaran');
        //$this->db->join('ref_program','ref_program.KodeProgram=prioritas_program.KodeProgram');
        $this->CI->flexigrid->build_query();
        $query['records'] = $this->db->get();
        
        $this->db->select('*');
        $this->db->from('prioritas_program');
        //$this->db->join('ref_jenis_prioritas','ref_jenis_prioritas.KodeJenisPrioritas=prioritas_program.KodeJenisPrioritas');
        $this->db->join('ref_periode','ref_periode.idPeriode=prioritas_program.idPeriode');
        $this->db->join('ref_tahun_anggaran','ref_tahun_anggaran.idThnAnggaran=prioritas_program.idThnAnggaran');
        $this->db->group_by('prioritas_program.idThnAnggaran');
        //$this->db->join('ref_program','ref_program.KodeProgram=prioritas_program.KodeProgram');
        $this->CI->flexigrid->build_query(FALSE);
        $query['record_count'] = $query['records']->num_rows();
        return $query;
    }
    
    function get_join_where($tabel,$tabel_join,$param_join,$kolom,$param){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join($tabel_join, $param_join);
        $this->db->where($kolom, $param);
        return $this->db->get();
    }
    
    function get_data_flexigrid_double_join($tabel,$tabel_join,$parameter_join,$tabel_join2,$parameter_join2){
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join($tabel_join,$parameter_join);
        $this->db->join($tabel_join2,$parameter_join2);
        $this->CI->flexigrid->build_query();
        $query['records'] = $this->db->get();
        
        $this->db->select('*');
        $this->db->from($tabel);
        $this->db->join($tabel_join,$parameter_join);
        $this->db->join($tabel_join2,$parameter_join2);
        $this->CI->flexigrid->build_query(FALSE);
        $query['record_count'] = $this->db->count_all_results();
        return $query;
    }
    function get_jumlah_ikk($kd_pengajuan) {
        $this->db->select('i.*');
        $this->db->from('data_kegiatan d');
        $this->db->join('ref_ikk i', 'i.KodeKegiatan=d.KodeKegiatan');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_jumlah_iku($kd_pengajuan) {
        $this->db->select('i.*');
        $this->db->from('data_program d');
        $this->db->join('ref_iku i', 'i.KodeProgram=d.KodeProgram');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_ikk_by_kdpengajuan($kd_pengajuan) {
        $this->db->select('*');
        $this->db->from('data_ikk');
        $this->db->where('KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_iku_by_kdpengajuan($kd_pengajuan) {
        $this->db->select('*');
        $this->db->from('data_iku');
        $this->db->where('KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_ikk_by_kodeikk($kd_pengajuan, $kd_ikk) {
        $this->db->select('*');
        $this->db->from('data_ikk d');
        $this->db->join('ref_ikk2 t','d.KodeIkk=t.KodeIkk');
        $this->db->join('ref_tahun_anggaran a','a.idThnAnggaran=t.idThnAnggaran');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIkk',$kd_ikk);
        $this->db->where('a.thn_anggaran',$this->session->userdata('thn_anggaran'));
        return $this->db->get()->row();
    }
    function get_iku_by_kodeiku($kd_pengajuan, $kd_iku) {
        $this->db->select('*');
        $this->db->from('data_iku d');
        $this->db->join('target_iku t','d.KodeIku=t.KodeIku');
        $this->db->join('ref_tahun_anggaran a','a.idThnAnggaran=t.idThnAnggaran');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIku',$kd_iku);
        $this->db->where('a.thn_anggaran',$this->session->userdata('thn_anggaran'));
        return $this->db->get()->row();
    }
    function getTargetIkk($kd_pengajuan) {
        $this->db->select('*');
        $this->db->from('data_ikk');
        $this->db->where('KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_ikk_by_satker($kd_pengajuan) {
        $this->db->select('*');
        $this->db->from('data_ikk d');
        $this->db->join('ref_ikk t','d.KodeIkk=t.KodeIkk');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_iku_by_satker($kd_pengajuan) {
        $this->db->select('*');
        $this->db->from('data_iku d');
        $this->db->join('ref_iku t','d.KodeIku=t.KodeIku');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        return $this->db->get();
    }
    function get_ikk_by_kdpengajuan_ikk($kd_pengajuan, $kd_ikk) {
        $this->db->select('*');
        $this->db->from('data_ikk d');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIkk',$kd_ikk);
        return $this->db->get();
    }
    function get_iku_by_kdpengajuan_iku($kd_pengajuan, $kd_iku) {
        $this->db->select('*');
        $this->db->from('data_iku d');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIku',$kd_iku);
        return $this->db->get();
    }
    function get_targetikk_by_kdpengajuan_ikk($kd_pengajuan, $kd_ikk, $thn) {
        $this->db->select('*');
        $this->db->from('data_ikk d');
        $this->db->join('target_ikk i', 'i.KodeIkk=d.KodeIkk');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIkk',$kd_ikk);
        $this->db->where('i.idThnAnggaran',$thn);
        return $this->db->get();
    }
    function get_targetiku_by_kdpengajuan_iku($kd_pengajuan, $kd_iku, $thn) {
        $this->db->select('*');
        $this->db->from('data_iku d');
        $this->db->join('target_iku i', 'i.KodeIku=d.KodeIku');
        $this->db->where('d.KD_PENGAJUAN',$kd_pengajuan);
        $this->db->where('d.KodeIku',$kd_iku);
        $this->db->where('i.idThnAnggaran',$thn);
        return $this->db->get();
    }
     function get_pagu_bok($provinsi, $kabupaten){
        $this->db->select("menu_nf.id_menu_nf as id, menu_nf.NAMA_MENU as NAMA, pagu_bok.PAGU as PAGU");
        $this->db->from("pagu_bok");
        $this->db->join("menu_nf", "menu_nf.id_menu_nf = pagu_bok.id_menu_nf");
        $this->db->where("KodeProvinsi", $provinsi);
        $this->db->where("KodeKabupaten", $kabupaten);
        return $this->db->get();

    }
    function realisasi_menu($provinsi, $kabupaten, $id_menu, $tahun, $waktu ){
        $this->db->select("realisasi, fisik");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("data_monev_rka", "pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan");
        $this->db->where("KodeProvinsi", $provinsi);
        $this->db->where("KodeKabupaten", $kabupaten);
        $this->db->where("kode_menu", $id_menu);
        $this->db->where("TAHUN_ANGGARAN", $tahun);
        $this->db->where("WAKTU_LAPORAN", $waktu);
        return $this->db->get();
    }

    function realisasi_menu_rujukan($rs, $ID_SUBBIDANG, $id_menu, $waktu, $tahun){
        $this->db->select("realisasi, fisik");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("data_monev_rka", "pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan");
        $this->db->where("pengajuan_monev_dak.KD_RS", $rs);
        $this->db->where("kode_menu", $id_menu);
        $this->db->where("ID_SUBBIDANG", $ID_SUBBIDANG);
        $this->db->where("TAHUN_ANGGARAN", $tahun);
        $this->db->where("WAKTU_LAPORAN", $waktu);
        return $this->db->get();
    }
    function realisasi_menu_nf($p, $k, $m, $w, $t){
        $this->db->select("realisasi, fisik");
        $this->db->from("pengajuan_monev_nf");
        $this->db->join("data_monev_nf", "pengajuan_monev_nf.id_pengajuan = data_monev_nf.id_pengajuan");
        $this->db->where("pengajuan_monev_nf.KodeProvinsi", $p);
        $this->db->where("pengajuan_monev_nf.KodeKabupaten", $k);
        $this->db->where("kode_menu", $m);
        $this->db->where("TAHUN_ANGGARAN", $t);
        $this->db->where("waktu_laporan", $w);
        return $this->db->get();
    }

    function pagu_rs($p, $jenis){
        $this->db->select("*");
        $this->db->from("pagu_rs");
        $this->db->where("KodeProvinsi", $p);
        $this->db->where("ID_Jenis_DAK", $jenis);
         $this->db->where('PAGU_SELURUH > 0');
        $this->db->group_by("KODE_RS");
        return $this->db->get();

    }
    function pagu_puskes($p){
        $this->db->select("*");
        $this->db->from("pagu_puskesmas");
        $this->db->where("KodeProvinsi", $p);
        $this->db->where('PAGU_SELURUH > 0');
        $this->db->group_by("KodePuskesmas");
        return $this->db->get();
    }

    function get_pagu_dashboard($jenis_dak, $tahun){
        $this->db->select("SUM(pagu_seluruh.pagu_seluruh) as pagu");
        $this->db->from("pagu_seluruh");
        $this->db->where("ID_SUBBIDANG", $jenis_dak);
        $this->db->where("TAHUN_ANGGARAN", $tahun);
        return $this->db->get();

    }
    function get_realisasi_dashboard($jenis_dak, $tw, $tahun){
        $this->db->select("SUM(data_monev_rka.realisasi) as realisasi");
        $this->db->from("data_monev_rka");
        $this->db->join("pengajuan_monev_dak", "pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan");
        $this->db->where("pengajuan_monev_dak.ID_SUBBIDANG", $jenis_dak);
        $this->db->where("pengajuan_monev_dak.TAHUN_ANGGARAN", $tahun);
        $this->db->where("pengajuan_monev_dak.WAKTU_LAPORAN", $tw);
        return $this->db->get();

    }
    function get_realisasi_dashboard2($jenis_dak, $tw, $tahun){
        $kdrs='0';
        $this->db->select("SUM(data_monev_rka.realisasi) as realisasi");
        $this->db->from("data_monev_rka");
        $this->db->join("pengajuan_monev_dak", "pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan");
        $this->db->where("pengajuan_monev_dak.ID_SUBBIDANG", $jenis_dak);
        $this->db->where("pengajuan_monev_dak.TAHUN_ANGGARAN", $tahun);
        $this->db->where("pengajuan_monev_dak.WAKTU_LAPORAN", $tw);
        $this->db->where("pengajuan_monev_dak.KD_RS", $kdrs);
        return $this->db->get();

    }
    function get_realisasi_fisik($array_pengajuan){
        $this->db->select("SUM(REALISASI_KEUANGAN_PELAKSANAAN) as realisasi, AVG(REALISASI_FISIK_PELAKSANAAN) as fisik");
        $this->db->from("dak_kegiatan");
        $this->db->where_in("ID_LAPORAN_DAK", $array_pengajuan);
        return $this->db->get();
    }

    function get_data_nonrujukan($provinsi, $kabupaten, $id_subbidang, $waktu_laporan){
        $sql = "SELECT data_monev_rka.kode_menu kode_menu, pagu.volume volume, pagu.PAGU pagu, ref_kabupaten.NamaKabupaten NamaKabupaten, pengajuan_monev_dak.ID_SUBBIDANG ID_SUBBIDANG, data_monev_rka.nama_menu nama_menu, data_monev_rka.realisasi realisasi, data_monev_rka.fisik fisik FROM ref_kabupaten JOIN pengajuan_monev_dak ON ref_kabupaten.KodeProvinsi = pengajuan_monev_dak.KodeProvinsi and ref_kabupaten.KodeKabupaten = pengajuan_monev_dak.KodeKabupaten  JOIN data_monev_rka ON pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan JOIN pagu ON pagu.KodeProvinsi = ref_kabupaten.KodeProvinsi and pagu.KodeKabupaten = ref_kabupaten.KodeKabupaten and pagu.ID_JENIS_DAK = pengajuan_monev_dak.ID_SUBBIDANG and data_monev_rka.kode_menu = pagu.ID_MENU WHERE ref_kabupaten.KodeProvinsi = ". $provinsi . " and ref_kabupaten.KodeKabupaten =  " . $kabupaten . " and ID_SUBBIDANG = ". $id_subbidang . " AND waktu_laporan = ". $waktu_laporan ." GROUP BY data_monev_rka.kode_menu";
        $query = $this->db->query($sql);
        return $query;
    }
    function get_data_rujukan($kd_rs, $id_subbidang, $waktu_laporan){
        $sql = "SELECT data_monev_rka.kode_menu kode_menu, pagu.volume volume, pagu.PAGU pagu, ref_kabupaten.NamaKabupaten NamaKabupaten, pengajuan_monev_dak.ID_SUBBIDANG ID_SUBBIDANG, data_monev_rka.nama_menu nama_menu, data_monev_rka.realisasi realisasi, data_monev_rka.fisik fisik FROM ref_kabupaten JOIN pengajuan_monev_dak ON ref_kabupaten.KodeProvinsi = pengajuan_monev_dak.KodeProvinsi and ref_kabupaten.KodeKabupaten = pengajuan_monev_dak.KodeKabupaten  JOIN data_monev_rka ON pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan JOIN pagu ON pagu.KodeProvinsi = ref_kabupaten.KodeProvinsi and pagu.KodeKabupaten = ref_kabupaten.KodeKabupaten and pagu.ID_JENIS_DAK = pengajuan_monev_dak.ID_SUBBIDANG and data_monev_rka.kode_menu = pagu.ID_MENU WHERE KD_RS = '". $kd_rs ."' and ID_SUBBIDANG = ". $id_subbidang . " AND waktu_laporan = ". $waktu_laporan ." GROUP BY data_monev_rka.kode_menu";
        $query = $this->db->query($sql);
        return $query;
    }

    function get_realisasi_dak($id_subbidang, $waktu_laporan){
        $this->db->select("*");
        $this->db->from("dak_realisasi");
        $this->db->where("ID_SUBBIDANG", $id_subbidang);
        $this->db->where("waktu_laporan", $waktu_laporan);
        $this->db->where("tahun", $this->session->userdata('thn_anggaran'));    
        return $this->db->get();
    }

    function get_realisasi_dak_2018($kdprov, $kdkab, $kdrs, $dak, $waktu, $tahun){
        $sql =  "SELECT data_monev_rka.kode_menu, data_monev_rka.nama_menu, data_monev_rka.realisasi, data_monev_rka.fisik,
            (SELECT pagu.volume from pagu where pagu.`status` = 1 and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and data_monev_rka.kode_menu = pagu.ID_MENU and pengajuan_monev_dak.TAHUN_ANGGARAN = pagu.TAHUN_ANGGARAN and pengajuan_monev_dak.KD_RS = pagu.KODE_RS and pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten) as volume,
            (SELECT pagu.PAGU from pagu where pagu.`status` = 1 and pengajuan_monev_dak.ID_SUBBIDANG = pagu.ID_JENIS_DAK and data_monev_rka.kode_menu = pagu.ID_MENU and pengajuan_monev_dak.TAHUN_ANGGARAN = pagu.TAHUN_ANGGARAN and pengajuan_monev_dak.KD_RS = pagu.KODE_RS and pengajuan_monev_dak.KodeProvinsi = pagu.KodeProvinsi and pengajuan_monev_dak.KodeKabupaten = pagu.KodeKabupaten) as pagu
            FROM pengajuan_monev_dak
            JOIN data_monev_rka ON pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan
            WHERE pengajuan_monev_dak.KodeProvinsi = '".$kdprov."' and pengajuan_monev_dak.KodeKabupaten = '".$kdkab."' and pengajuan_monev_dak.ID_SUBBIDANG = ".$dak."
            AND pengajuan_monev_dak.TAHUN_ANGGARAN = ". $tahun. " and pengajuan_monev_dak.waktu_laporan = ".$waktu." AND pengajuan_monev_dak.KD_RS = '".$kdrs."'
        ";
        $query = $this->db->query($sql);
        return $query;
    }

    function generate_report(){
        $sql  = "
            SELECT p.KodeProvinsi, p.KodeKabupaten, p.KODE_RS, ref_kabupaten.NamaKabupaten, p.ID_JENIS_DAK ID_SUBBIDANG, p.TAHUN_ANGGARAN as tahun , s.waktu_laporan, p.ID_MENU, p.volume, p.pagu, p.perubahan, s.realisasi, s.persentase, s.fisik from ref_kabupaten 
            LEFT JOIN (select * from pagu WHERE status = 1 ) p ON p.KodeProvinsi = ref_kabupaten.KodeProvinsi and p.KodeKabupaten = ref_kabupaten.KodeKabupaten
            LEFT JOIN (select q.waktu_laporan, q.ID_SUBBIDANG, q.KodeProvinsi, q.KD_RS KODE_RS, q.KodeKabupaten, q.id_pengajuan, r.kode_menu, r.persentase, r.realisasi, r.fisik, q.TAHUN_ANGGARAN from pengajuan_monev_dak q 
            JOIN data_monev_rka r ON q.id_pengajuan = r.id_pengajuan) s 
            ON s.kode_menu = p.ID_MENU and p.KodeProvinsi = s.KodeProvinsi and p.KodeKabupaten = s.KodeKabupaten and p.ID_JENIS_DAK = s.ID_SUBBIDANG and s.ID_SUBBIDANG = p.ID_JENIS_DAK and s.KODE_RS = p.KODE_RS and p.TAHUN_ANGGARAN = s.TAHUN_ANGGARAN
            GROUP BY ref_kabupaten.KodeProvinsi, ref_kabupaten.KodeKabupaten, p.ID_JENIS_DAK, s.waktu_laporan, p.ID_MENU, p.TAHUN_ANGGARAN
            ORDER BY ref_kabupaten.id

        ";
        $query = $this->db->query($sql);
        return $query;
    }

    function get_pagu_all(){
        $this->db->select("KodeProvinsi, KodeKabupaten, Volume, PAGU, KODE_RS, ID_JENIS_DAK");
        $this->db->from("pagu");
        $this->db->where("status", 1);
        $this->db->where('TAHUN_ANGGARAN', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_pagu_seluruh(){
        $this->db->select('KodeProvinsi, KodeKabupaten, ID_SUBBIDANG, pagu_seluruh');
        $this->db->from("pagu_seluruh");
        $this->db->where("TAHUN_ANGGARAN", date('Y'));
        return $this->db->get();
    }

    function get_seluruh_realisasi(){
        $this->db->select("KodeProvinsi, KodeKabupaten, ID_SUBBIDANG, kode_menu, nama_menu, realisasi, fisik");
        $this->db->from("pengajuan_monev_dak");
        $this->db->join("data_monev_rka", "pengajuan_monev_dak.id_pengajuan = data_monev_rka.id_pengajuan");
        $this->db->where("TAHUN_ANGGARAN", date('Y'));
        return $this->db->get();
    }

    function get_usulan_kd($status){
        $this->db->select('*');
        $this->db->from('apbn_planning');
        $this->db->where_in('KodeJenisSatker', array('5'));
        if($status != ''){
            $this->db->where('status', $status);
        }
        $this->db->where('thang', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

         function get_usulan_kd2($status){
        $this->db->select('*');
        $this->db->from('apl_pengajuan');
        $this->db->where_in('kewenangan', array('5'));
        if($status != ''){
            $this->db->where('status_pengajuan', $status);
        }
        $this->db->where('tahun', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }


    function get_usulan_kp($status){
        $this->db->select('*');
        $this->db->from('apbn_planning');
        $this->db->where_in('KodeJenisSatker', array('3','4'));
        if($status != ''){
            $this->db->where('status', $status);
        }
        $this->db->where('thang', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

     function get_usulan_kp2($status){
        $this->db->select('*');
        $this->db->from('apl_pengajuan');
        $this->db->where_in('kewenangan', array('3','4'));
        if($status != ''){
            $this->db->where('status_pengajuan', $status);
        }
        $this->db->where('tahun', $this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_proses_realisasi_nonfisik_kabupaten(){
        $provinsi = "";
        $kabupaten = "";
        $this->db->select("AVG(dak_nf_rka.fisik) as fisik, SUM(dak_nf_rka.realisasi) as realisasi");
        $this->db->from('dak_nf_rka');
        $this->db->join('dak_nf_laporan','dak_nf_rka.id_pengajuan = dak_nf_laporan.id_pengajuan');
        $this->db->join('dak_nf_pagus','dak_nf_laporan.KodeProvinsi=dak_nf_pagus.KodeProvinsi and dak_nf_laporan.KodeKabupaten=dak_nf_pagus.KodeKabupaten and dak_nf_laporan.id_dak_nf = dak_nf_pagus.id_dak_nf');        
        $provinsi;
        $this->db->where('dak_nf_laporan.WAKTU_LAPORAN', $waktu);
        $this->db->where('dak_nf_pagus.TAHUN_ANGGARAN', $tahun);
        $this->db->where('dak_nf_laporan.id_dak_nf', $j);
         $provinsi = $this->db->where('dak_nf_laporan.KodeProvinsi', $p); 
        $kabupaten = $this->db->where('dak_nf_laporan.KodeKabupaten', $k);          
        return $this->db->get();
    } 

    function get_laporan_nonfisik_2017(){
        $this->db->select("*");
        $this->db->from("dak_nf_laporan");
        $this->db->join("dak_nf_rka", "dak_nf_laporan.id_pengajuan = dak_nf_rka.id_pengajuan");
        $this->db->where("TAHUN_ANGGARAN",$this->session->userdata('thn_anggaran'));
        return $this->db->get();
    }

    function get_ref_wilayah_urut(){
        $sql = "SELECT DISTINCT rw.kdbpprov, rw.kdbpkab, rk.KodeProvinsi, rk.KodeKabupaten ,rw.nama FROM `ref_wilayah` rw 
                LEFT JOIN ref_kabupaten rk ON rw.kdprovinsi = rk.KodeProvinsi and rw.kdkabupaten = rk.KodeKabupaten 
                where level in ('provinsi','kabupaten')
                ORDER BY rk.id";
        $query = $this->db->query($sql);
        return $query;
    }

    function get_data_rs_rakontek($periode, $kode_subbidang, $kdbprs){
        $sql ="
                SELECT sum(rakontek_usulan.nilai_usulan) as usulan, sum(rakontek_hasil_verifikasi.jumlah) as hasil_verifikasi from rakontek_usulan
                LEFT JOIN (SELECT DISTINCT id_detail_rincian, rakontek_hasil_verifikasi.jumlah FROM rakontek_hasil_verifikasi) rakontek_hasil_verifikasi ON rakontek_usulan.id = rakontek_hasil_verifikasi.id_detail_rincian
                where rakontek_usulan.periode = ".$periode." and rakontek_usulan.kode_subbidang = '".$kode_subbidang."' and kode_referensi = '".$kdbprs."' and rakontek_usulan.status_usulan in (1,2)

        ";
        $query = $this->db->query($sql);
        return $query;
    }
    function get_data_rs_rakontek_null($periode, $kdbpkab, $kode_subbidang, $kdbprs){
        $sql ="
                SELECT sum(rakontek_usulan.nilai_usulan) as usulan, sum(rakontek_hasil_verifikasi.jumlah) as hasil_verifikasi from rakontek_usulan
                LEFT JOIN (SELECT DISTINCT id_detail_rincian, rakontek_hasil_verifikasi.jumlah FROM rakontek_hasil_verifikasi) rakontek_hasil_verifikasi ON rakontek_usulan.id = rakontek_hasil_verifikasi.id_detail_rincian
                where rakontek_usulan.periode = ".$periode." and rakontek_usulan.kode_subbidang = '".$kode_subbidang."' and kode_referensi is null and rakontek_usulan.status_usulan in (1,2) and rakontek_usulan.kode_kabupaten = ".$kdbpkab."

        ";
        $query = $this->db->query($sql);
        return $query;
    }public function getKodekategori($prov='', $kab='', $thn='')
    {
        $sql="SELECT
                  mc.kategori AS id_dak_nf,
          k.nama_kategori AS nama_dak_nf
                    FROM budgetdaknf_pengajuan mc
          INNER JOIN dak_nf_kategori k ON k.id_kategori_nf=mc.kategori
           WHERE mc.kdprovinsi='$prov' AND mc.kdkabupaten='$kab' AND mc.tahun_anggaran='$thn'
           GROUP BY mc.kategori";
        return $this->db->query($sql);
    }public function getIdbudgeting($prov='',$kab='',$kategori='',$thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.status_revisicovid,
                bp.kategori
                FROM budgetdaknf_pengajuan bp
                WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.kategori='$kategori' AND bp.tahun_anggaran='$thn' AND bp.status='1'";
        return $this->db->query($sql);
    }public function getIdbudgeting_rs($prov='',$kab='',$kategori='',$thn='',$kdrs='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.status_revisicovid,
                bp.kategori
                FROM budgetdaknf_pengajuan bp
                WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.kategori='$kategori' AND bp.tahun_anggaran='$thn' AND bp.status='1' AND bp.kdrumahsakit='$kdrs'";
        return $this->db->query($sql);
    }public function getIdbudgeting2($prov='',$kab='',$kategori='',$thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.status_revisicovid,
                bp.kategori
                FROM budgetdaknf_pengajuan bp
                WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.kategori='$kategori' AND bp.tahun_anggaran='$thn' ";
        return $this->db->query($sql);
    }public function getBudegetingRevisi($prov='',$kab='',$thn='')
    {
        $sql=" SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                 bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.tahun_anggaran='$thn' ";
        return $this->db->query($sql);
    }public function getBudegetingRevisiadmin($rev='',$thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                 bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT date_time FROM budgetdaknf_history WHERE id_pengajuan=bp.id_pengajuan  ORDER BY date_time ASC limit 0,1) AS tgl
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest='$rev'
                ORDER BY tgl ASC  ";
        return $this->db->query($sql);
    }public function getBudegetingRevisiadminAll($thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                 bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest !='0' ";
        return $this->db->query($sql);
    }public function getBudegetingRevisiadminLaporan($thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                bd.*,
                bdr.*
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                LEFT JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                LEFT JOIN budgetdaknf_data_revisi bdr ON bdr.id_pengajuan=bp.id_pengajuan and bdr.id_detail_pengajuan=bd.id
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest !='0'   ";
                // and ( bdr.status_rev='1'  or bdr.status_rev='0' )
        return $this->db->query($sql);
    }public function getLaporanRevBudget($thn='')
    {
       $sql="SELECT
                bp.*,
                bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest !='0'
                ORDER BY rp.namaprovinsi,rk.namakabupaten";
        return $this->db->query($sql);
    }
    public function getBudegetingRevisiadminLaporan_prov($thn='',$prov='')
    {
         $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                bd.*,
                bdr.*
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                LEFT JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                LEFT JOIN budgetdaknf_data_revisi bdr ON bdr.id_pengajuan=bp.id_pengajuan and bdr.id_detail_pengajuan=bd.id
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest !='0' AND bp.kdprovinsi='$prov' ";
        return $this->db->query($sql);
    }public function getBudegetingRevisiadminLaporan_kab($thn='',$prov='',$kab='')
    {
         $sql="  SELECT
                bp.id_pengajuan,
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kdsatker,
                bp.kdrumahsakit,
                bp.tahun_anggaran,
                bp.kategori,
                bp.revisiActive,
                bp.revisiRequest,
                bp.create_at AS tanggal_pembuatan,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                bd.*,
                bdr.*
                FROM budgetdaknf_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                LEFT JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                LEFT JOIN budgetdaknf_data_revisi bdr ON bdr.id_pengajuan=bp.id_pengajuan and bdr.id_detail_pengajuan=bd.id
                WHERE bp.tahun_anggaran='$thn' AND bp.revisiRequest !='0' AND bp.kdprovinsi='$prov' AND bp.kdprovinsi='$kab'";
        return $this->db->query($sql);
    }
    public function getBudgetingPagu($prov='',$kab='',$kategori='',$thn='')
    {
       $sql="SELECT
            bp.nominal
            FROM budgetdaknf_pagu bp
            WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.id_kategori='$kategori' AND bp.tahun='$thn'";
        return $this->db->query($sql);
    } public function getBudgetingPagu_rs($prov='',$kab='',$kategori='',$thn='',$kdrs='')
    {
       $sql="SELECT
            bp.nominal
            FROM budgetdaknf_pagu bp
            WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.id_kategori='$kategori' AND bp.tahun='$thn' AND bp.kdrumahsakit='$kdrs'";
        return $this->db->query($sql);
    }public function getBudgetingData($id='')
    {
        $sql="  SELECT
                bd.kode_menu AS id_menu,
                bd.nama_menu,
                bd.volume,
                bd.harga_satuan,
                bd.jumlah
                FROM budgetdaknf_data bd
                WHERE bd.id_pengajuan='$id'";
        return $this->db->query($sql);
    }public function getIddak_nf_laporan($prov='',$kab='',$kategori='',$waktu='',$thn='')
    {
        $sql="  SELECT
                bp.id_pengajuan
                FROM dak_nf_laporan bp
                WHERE bp.kodeprovinsi='$prov' 
                AND bp.kodekabupaten='$kab' 
                AND bp.id_kategori_nf='$kategori' 
                AND bp.waktu_laporan='$waktu'
                AND bp.tahun_anggaran='$thn'";
        return $this->db->query($sql);
    }public function getDataDak_nf($id='')
    {
         $sql="  SELECT
                *
                FROM dak_nf_rka bp
                WHERE bp.id_pengajuan='$id' AND bp.id_dak_nf >= '13'";
        return $this->db->query($sql);
    }function get_pagu_nf_tecno($p, $k, $jenis, $tahun){
        $this->db->select('*');
        $this->db->from('dak_nf_pagu');
        $this->db->join("dak_nf_menu", "dak_nf_pagu.id_menu_nf = dak_nf_menu.id_menu and (dak_nf_pagu.id_dak_nf = dak_nf_menu.id_dak_nf or dak_nf_pagu.id_dak_nf = dak_nf_menu.id_kategori_nf)");
        $this->db->where('dak_nf_pagu.KodeProvinsi', $p);
        $this->db->where('dak_nf_pagu.KodeKabupaten', $k); 
        $this->db->where('dak_nf_pagu.id_dak_nf', $jenis); 
        $this->db->where('dak_nf_pagu.TAHUN_ANGGARAN', $tahun); 
        $this->db->where('dak_nf_menu.Status', 1); 
        return $this->db->get();      
    }function get_pagu_nf_tecno2($p, $k, $jenis, $kdrs, $tahun){
        $this->db->select('*');
        $this->db->from('dak_nf_pagu');
        $this->db->join("dak_nf_menu", "dak_nf_pagu.id_menu_nf = dak_nf_menu.id_menu and (dak_nf_pagu.id_dak_nf = dak_nf_menu.id_dak_nf or dak_nf_pagu.id_dak_nf = dak_nf_menu.id_kategori_nf)");
        $this->db->where('dak_nf_pagu.KodeProvinsi', $p);
        $this->db->where('dak_nf_pagu.KodeKabupaten', $k); 
        $this->db->where('dak_nf_pagu.id_dak_nf', $jenis); 
        $this->db->where('dak_nf_pagu.kdrumahsakit', $kdrs); 
        $this->db->where('dak_nf_pagu.TAHUN_ANGGARAN', $tahun); 
        $this->db->where('dak_nf_menu.Status', 1); 
        return $this->db->get();      
    }public function getTahun($value='')
    {
        $sql="  SELECT
                bp.tahun_anggaran
                FROM dak_nf_kategori bp
                GROUP BY bp.tahun_anggaran";
        return $this->db->query($sql);
    }public function cariProv($where='')
    {
        $sql="  SELECT
                *
                FROM ref_kabupaten bp
                $where ";
        return $this->db->query($sql);
    }public function getBatasAkhir($kat='',$thn='')
    {
        $sql=" SELECT
                *
                FROM data_akses_tch da
                WHERE da.id_akses_kategori='$kat' AND da.thn_anggaran='$thn' ";
        return $this->db->query($sql);
    }public function getDataFisik($kab='',$prov='',$id='',$tahun='')
    {
        $sql=" SELECT
                *
                FROM data_fisik_tch da
                WHERE da.kodekabupaten='$kab'  AND da.kodeprovinsi='$prov' AND da.id_jenis_dak='$id' AND da.tahun_anggaran='$tahun' ";
        return $this->db->query($sql);
    }public function getMaxid($field='',$table='',$where='')
    {
         $sql=" SELECT
                max($field) as jml
                FROM $table 
                $where";
        return $this->db->query($sql);
    }public function getAllWhere($table='',$where='')
    {
         $sql=" SELECT
                *
                FROM $table 
                $where";
        return $this->db->query($sql);
    }public function updateData($id,$table,$data){
        $this->db->where($id,$data[$id]); 
        $this->db->update($table,$data);
    }public function cekUplodDukung($idp='')
    {
        $sql="  SELECT
                COUNT(1) AS jml
                FROM file_dukung_nf da
                INNER JOIN data_nf_rka dnr ON dnr.id=da.id_rka_nf
                WHERE dnr.id_pengajuan='$idp'";
        return $this->db->query($sql);
    }public function getDetailMonevKabupaten($prov='',$kab='',$thn='')
    {
      $sql="    SELECT
                df.*,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                s.Satuan
                FROM dak_fisik_2020 df
                INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
                INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
                INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE df.tahun='$thn' and df.kodekabupaten='$kab' and df.kodeprovinsi='$prov'
                ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI ";
        return $this->db->query($sql);
    }public function getDetailMonevProvinsi($prov='',$thn='')
    {
      $sql="    SELECT
                df.*,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                s.Satuan
                FROM dak_fisik_2020 df
                INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
                INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
                INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE df.tahun='$thn' 
                -- and  df.menu LIKE '%obat%'
                -- and  djd.ID_JENIS_DAK='49'
                and df.kodeprovinsi='$prov' 
                ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI ";
        return $this->db->query($sql);
    }
    public function getDetailMonevALL($thn='')
    {
      $sql="    SELECT
                df.*,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                s.Satuan
                FROM dak_fisik_2020 df
                INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
                INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
                INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE df.tahun='$thn' 
                ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI ";
        return $this->db->query($sql);
    }
    public function getDetailpenunjangKabupaten($prov='',$kab='',$thn='')
    {
         $sql="
            SELECT
            df.*,
            k.NAMA_KATEGORI,
            djd.NAMA_JENIS_DAK,
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            s.Satuan
            FROM dak_penunjang df
            INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
            INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
            INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
            INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE df.kodekabupaten='$kab' AND df.kodeprovinsi='$prov' AND df.tahun='$thn'
            ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI
            ";
        return $this->db->query($sql);
    }

    public function getDetailpenunjangProv($prov='',$thn='')
    {
         $sql="
            SELECT
            df.*,
            k.NAMA_KATEGORI,
            djd.NAMA_JENIS_DAK,
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            s.Satuan
            FROM dak_penunjang df
            INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
            INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
            INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
            INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE  df.kodeprovinsi='$prov' AND df.tahun='$thn'
            ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI
            ";
        return $this->db->query($sql);
    }

     public function getDetailpenunjangAll($thn='')
    {
         $sql="
            SELECT
            df.*,
            k.NAMA_KATEGORI,
            djd.NAMA_JENIS_DAK,
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            s.Satuan
            FROM dak_penunjang df
            INNER JOIN kategori k ON k.ID_KATEGORI=df.id_kategori
            INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=df.id_jenis_dak
            INNER JOIN ref_satuan s ON s.KodeSatuan=df.kd_satuan
            INNER JOIN ref_kabupaten rk ON df.kodekabupaten=rk.kodekabupaten AND df.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE df.tahun='$thn'
            ORDER BY rp.NamaProvinsi,rk.kodekabupaten,k.NAMA_KATEGORI
            ";
        return $this->db->query($sql);
    }

    public function getIsIMonevFisik($prov='',$kab='',$thn='',$tw='',$kdmenu='')
    {
         $sql=" SELECT
                da.*,
                dmr.*,
                pd.Masalah
                from pengajuan_monev_dak da
                INNER JOIN data_monev_rka_2020  dmr ON dmr.id_pengajuan=da.id_pengajuan
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dmr.KodeMasalah
                WHERE da.TAHUN_ANGGARAN='$thn' and da.KodeProvinsi='$prov' and da.KodeKabupaten='$kab' and waktu_laporan='$tw' and dmr.kode_menu ='$kdmenu' ";
        return $this->db->query($sql);
    }

    public function getIsIMonevNF($prov='',$kab='',$thn='',$tw='',$kat='',$kdmenu='')
    {
        $sql="SELECT
            pmd.tanggal_pembuatan,
            dnr.*,
            pd.Masalah
            from 
            dak_nf_laporan pmd
            INNER JOIN dak_nf_rka dnr ON dnr.id_pengajuan =  pmd.id_pengajuan
            INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dnr.KodeMasalah
            where pmd.kodekabupaten='$kab' AND pmd.kodeprovinsi='$prov' And pmd.tahun_anggaran='$thn' and pmd.waktu_laporan='$tw' and pmd.id_kategori_nf='$kat' AND dnr.id_menu_nf='$kdmenu' ";
        return $this->db->query($sql);
    }public function getIsiPenunjang($id='',$tw='')
    {
         $sql="SELECT
                COUNT(dp.id) as par,
                dp.*,
                pd.Masalah
                from dak_penunjang_input dp
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dp.kode_masalah
                WHERE dp.id_penunjang='$id' and dp.triwulan='$tw'";
        return $this->db->query($sql);
    }

    public function cekInputMonevNF($prov='',$kab='',$thn='',$tw='',$kat='')
    {
        $sql="
                SELECT
                COUNT(1) AS jml
                FROM dak_nf_laporan pmd
                where pmd.kodekabupaten='$kab' AND pmd.kodeprovinsi='$prov' And pmd.tahun_anggaran='$thn' and pmd.waktu_laporan='$tw' and pmd.id_kategori_nf='$kat'";
            return $this->db->query($sql);
    }

    public function getDataPengajuanProvinsi($prov='',$kab='',$thn='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.id_adm_verifikator,
                da.date_verifikasi,
                da.status_ver_menu,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.kdProvinsi='$prov' AND da.kdKabupaten='$kab' AND da.tahun_anggaran='$thn' ";
        return $this->db->query($sql);
    }public function getDataPengajuanMon($prov='',$thn='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.id_adm_verifikator,
                da.date_verifikasi,
                da.status_ver_menu,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.kdProvinsi='$prov' AND da.tahun_anggaran='$thn' ";
        return $this->db->query($sql);
    }public function getDataPengajuanBudget1($value='')
    {
        $sql=" SELECT * from (
                                SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_adm_verifikator,
                da.date_verifikasi,
                da.status_ver_menu,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                                (SELECT bp.id_pengajuan FROM budgetdaknf_pengajuan bp 
                                INNER JOIN budgetdaknf_data bd ON bp.id_pengajuan=bd.id_pengajuan WHERE bp.kdprovinsi=da.kdprovinsi and bp.kdkabupaten=da.kdkabupaten and bp.kategori=da.kategori and bp.tahun_anggaran=da.tahun_anggaran
                            GROUP BY bp.id_pengajuan    ) as idbudgeting
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='2021' ) as qty
                                WHERE qty.idbudgeting IS NULL";
        return $this->db->query($sql);
    }public function getDataPengajuanBudget2($tahun='')
    {
        $sql=" SELECT * from (
                                SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_adm_verifikator,
                da.date_verifikasi,
                da.status_ver_menu,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                                (SELECT bp.id_pengajuan FROM budgetdaknf_pengajuan bp 
                                INNER JOIN budgetdaknf_data bd ON bp.id_pengajuan=bd.id_pengajuan WHERE bp.kdprovinsi=da.kdprovinsi and bp.kdkabupaten=da.kdkabupaten and bp.kategori=da.kategori and bp.tahun_anggaran=da.tahun_anggaran
                            GROUP BY bp.id_pengajuan    ) as idbudgeting
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun' ) as qty
                                WHERE qty.idbudgeting IS NOT NULL";
        return $this->db->query($sql);
    }public function getDataPengajuanBudget3($tahun='')
    {
        $sql="  SELECT * FROM(
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
                FROM
                budgetdaknf_pengajuan da
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=da.id_pengajuan
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun' and da.status='0'
                GROUP BY da.id_pengajuan) AS QTY
                WHERE QTY.ttdpusat IS NULL AND QTY.ttddaerah IS NULL";
        return $this->db->query($sql);
    }
    public function getDataPengajuanBudget4($tahun='')
    {
        $sql="  SELECT * FROM(
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
                FROM
                budgetdaknf_pengajuan da
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=da.id_pengajuan
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun' and da.status='1'
                GROUP BY da.id_pengajuan) AS QTY
                WHERE QTY.ttdpusat IS NULL AND QTY.ttddaerah IS NULL";
        return $this->db->query($sql);
    }

    public function getDataPengajuanBudget5($tahun='')
    {
        $sql="  SELECT * FROM(
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
                FROM
                budgetdaknf_pengajuan da
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=da.id_pengajuan
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun' 
                GROUP BY da.id_pengajuan) AS QTY
                WHERE QTY.ttdpusat IS NULL AND QTY.ttddaerah IS NOT NULL";
        return $this->db->query($sql);
    }

    public function getDataPengajuanBudget6($tahun='')
    {
        $sql="  SELECT * FROM(
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
                FROM
                budgetdaknf_pengajuan da
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=da.id_pengajuan
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun'
                GROUP BY da.id_pengajuan) AS QTY
                WHERE QTY.ttdpusat IS NOT NULL AND QTY.ttddaerah IS NOT NULL";
        return $this->db->query($sql);
    }
    public function getDataPengajuanBudget7($tahun='')
    {
        $sql="  SELECT * FROM(
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(brm.id_kategori_nf) FROM budgetdaknf_revisirk_menu brm WHERE brm.id_kategori_nf=da.kategori) AS par_kategori,
                (SELECT COUNT(brp.id_pengajuan) FROM budgetdaknf_revisirk_pengajuan brp WHERE brp.id_pengajuan_rk=da.id_pengajuan) AS revisi,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
                (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=da.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
                FROM
                budgetdaknf_pengajuan da
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=da.id_pengajuan
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun'
                GROUP BY da.id_pengajuan) AS QTY
                 WHERE QTY.ttdpusat IS NOT NULL AND QTY.ttddaerah IS NOT NULL AND QTY.par_kategori !='0' AND QTY.revisi='0'";
        return $this->db->query($sql);
    }

    public function getlistRevisiRK($tahun='')
    {
        $sql="
                SELECT
                da.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori,
                (SELECT COUNT(bs.id_pengajuan_rk) FROM budgetdaknf_revisirk_signature bs WHERE bs.id_pengajuan_rk=da.id_pengajuan_rk AND bs.flag='1' GROUP BY bs.id_pengajuan_rk) AS ttdkadinkes,
                (SELECT COUNT(bs.id_pengajuan_rk) FROM budgetdaknf_revisirk_signature bs WHERE bs.id_pengajuan_rk=da.id_pengajuan_rk AND bs.flag='3' GROUP BY bs.id_pengajuan_rk) AS ttdapip
                                FROM
                budgetdaknf_revisirk_pengajuan da
                INNER JOIN budgetdaknf_revisirk_data bd ON bd.id_pengajuan_rk=da.id_pengajuan_rk
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.tahun_anggaran='$tahun'
                GROUP BY da.id_pengajuan";
        return $this->db->query($sql);
    }
    public function getDatalaporanbudgeting($tahun='',$where='')
    {
       $sql="SELECT
            bp.*,
            SUM(bd.jumlah) AS jumlah,
            SUM(bd.dak) AS dak,
            rp.namaprovinsi,
            rk.namakabupaten,
            dnk.nama_kategori,
            (SELECT pagu from budget_nf_pagu_tch bpt WHERE bpt.KodeProvinsi=bp.kdprovinsi and bpt.KodeKabupaten=bp.kdkabupaten and bpt.id_kategori=bp.kategori and bpt.tahun='$tahun') AS pagu,
            (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=bp.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
            (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=bp.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
            from budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd on bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
            WHERE bp.tahun_anggaran='$tahun' $where
            GROUP BY bp.id_pengajuan
            ORDER BY rp.namaprovinsi,rk.kodekabupaten";
        return $this->db->query($sql);
    }
    public function getDatalaporanbudgeting2($tahun='',$where='')
    {
    $sql="SELECT
            bp.*,
            bd.*,
            rp.namaprovinsi,
            rk.namakabupaten,
            dnk.nama_kategori,
            (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=bp.id_pengajuan AND bs.kode_signature='0' GROUP BY bs.id_pengajuan) AS ttdpusat,
            (SELECT COUNT(bs.id_pengajuan) FROM budgeting_signature bs WHERE bs.id_pengajuan=bp.id_pengajuan AND bs.kode_signature='1' GROUP BY bs.id_pengajuan) AS ttddaerah
            FROM
            budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd on bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
            WHERE bp.tahun_anggaran='$tahun' $where
            ORDER BY rp.namaprovinsi,rk.kodekabupaten";
    return $this->db->query($sql);
    }

    public function getDataBudgetPuskesmas($thn='',$where='')
    {
        $sql="
            SELECT
            *,
            dnk.nama_kategori,
            (SELECT komfirmasi FROM budgetdaknf_pengajuan jbp
            INNER JOIN budgetdaknf_data jbd ON jbp.id_pengajuan=jbd.id_pengajuan
            WHERE jbp.tahun_anggaran='$thn' and jbp.kdprovinsi=pr.kdprovinsi and jbp.kdkabupaten=pr.kdkabupaten and pr.kdkategori=jbp.kategori and jbd.kode_menu= dnf.id_menu) AS komfismasi,
            (SELECT jbp.status FROM budgetdaknf_pengajuan jbp
            INNER JOIN budgetdaknf_data jbd ON jbp.id_pengajuan=jbd.id_pengajuan
            WHERE jbp.tahun_anggaran='2021' and jbp.kdprovinsi=pr.kdprovinsi and jbp.kdkabupaten=pr.kdkabupaten and pr.kdkategori=jbp.kategori and jbd.kode_menu= dnf.id_menu) AS statusnilai,
                        (SELECT jbp.id_pengajuan FROM budgetdaknf_pengajuan jbp
            INNER JOIN budgetdaknf_data jbd ON jbp.id_pengajuan=jbd.id_pengajuan
            WHERE jbp.tahun_anggaran='2021' and jbp.kdprovinsi=pr.kdprovinsi and jbp.kdkabupaten=pr.kdkabupaten and pr.kdkategori=jbp.kategori and jbd.kode_menu= dnf.id_menu) AS idpengajuan
            from data_rka_nf_puskesmas pr
            INNER JOIN ref_puskesmas_2020 dk ON dk.id=pr.id_puskesmas
            INNER JOIN dak_nf_menu dnf ON pr.id_menu=dnf.id
            INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=pr.kdkategori
            INNER JOIN ref_kabupaten rk ON pr.kdkabupaten=rk.kodekabupaten AND pr.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            $where
            ORDER BY rp.namaprovinsi,rk.kodekabupaten,dk.kecamatan,dk.nama ";
    return $this->db->query($sql);
    }

    public function getDataPengajuanProvinsiadm($thn='',$rev='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.id_adm_verifikator,
                da.date_verifikasi,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.id_kategori_nf,
                dnk.nama_kategori
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.status_revisi='$rev'  AND da.tahun_anggaran='$thn'";
        return $this->db->query($sql);
    }public function getDataPengajuanProvinsi2($prov='',$kab='',$kat='',$thn='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.status,
                da.id_adm_verifikator,
                da.date_verifikasi,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.nama_kategori
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.kdProvinsi='$prov' AND da.kdKabupaten='$kab' AND da.kategori='$kat' AND da.tahun_anggaran='$thn'";
        return $this->db->query($sql);
    }public function getDataPengajuanProvinsiComplate($status='',$status_ver='',$thn)
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                da.status,
                da.id_adm_verifikator,
                da.date_verifikasi,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.nama_kategori
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                WHERE da.status='$status' AND da.status_ver_menu='$status_ver' AND da.tahun_anggaran='$thn'";
        return $this->db->query($sql);
    }public function getListPengajuanProv($prov='',$thn='')
    {
            $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.nama_kategori,
                dnr.*
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                WHERE da.kdprovinsi='$prov' AND da.tahun_anggaran='$thn'
                ORDER BY da.kdkabupaten ASC";
            return $this->db->query($sql);
    }public function getDataUsulanZip($prov='',$thn='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_user,
                da.tanggal_pembuatan,
                da.tahun_anggaran,
                da.kategori,
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.nama_kategori,
                dnr.*,
                fd.judul_file,
                fd.kategori_file
                FROM pengajuan_rka_nf da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=da.kategori
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN file_dukung_nf fd ON fd.id_rka_nf=dnr.id 
                WHERE da.kdprovinsi='$prov' AND da.tahun_anggaran='$thn'
                ORDER BY da.kdkabupaten ASC ";
            return $this->db->query($sql);
    }public function getListDataMonevFisik($prov='',$kab='',$thn='')
    {
       $sql="   SELECT
                COUNT(1) as jml,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                dft.*
                from data_fisik_tch dft
                INNER JOIN kategori k ON k.ID_KATEGORI=dft.id_kategori
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=dft.id_jenis_dak
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=dft.kodeprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeProvinsi=dft.kodeprovinsi AND rk.KodeKabupaten=dft.kodekabupaten
                WHERE dft.KodeProvinsi='$prov' AND dft.KodeKabupaten='$kab' AND dft.tahun_anggaran='$thn' 
                GROUP BY dft.id_jenis_dak";
            return $this->db->query($sql);
    }public function get_monev_detail_new($p, $k,$jenis_dak,$kategori, $waktu_laporan, $tahun, $rs)
    {
         $sql=" SELECT
                * 
                from pengajuan_monev_dak pmd
                INNER JOIN data_monev_rka  dmr ON dmr.id_pengajuan=pmd.id_pengajuan
                INNER JOIN menu m ON m.ID_MENU=dmr.kode_menu 
                INNER JOIN pagu p ON p.KodeProvinsi=pmd.KodeProvinsi AND p.KodeKabupaten=pmd.KodeKabupaten 
                AND p.TAHUN_ANGGARAN=pmd.TAHUN_ANGGARAN AND p.ID_JENIS_DAK=pmd.ID_SUBBIDANG AND p.ID_KATEGORI=pmd.ID_KATEGORI
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dmr.KodeMasalah
                INNER JOIN dak_capaian_output dco ON dco.id_pengajuan=pmd.id_pengajuan and dco.ID_MENU=dmr.kode_menu
                INNER JOIN ref_satuan r ON r.KodeSatuan = m.KodeSatuan
                WHERE pmd.KodeKabupaten='$k' AND pmd.KodeProvinsi='$p' And pmd.TAHUN_ANGGARAN='$tahun' 
                AND pmd.ID_SUBBIDANG='$jenis_dak' AND pmd.ID_KATEGORI='$kategori' AND pmd.waktu_laporan='$waktu_laporan' AND pmd.KD_RS='$rs'
                GROUP BY dmr.kode_menu";
            return $this->db->query($sql);
    }public function getSumRealisai($kab='',$prov='',$kat='',$jd='',$thn='')
    {
        $sql="
            SELECT
            SUM(realisasi) as jml
            FROM pengajuan_monev_dak
            INNER JOIN data_monev_rka on data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan
            WHERE KodeKabupaten='$kab' AND KodeProvinsi='$prov'  AND ID_KATEGORI='$kat' AND ID_SUBBIDANG='$jd'AND TAHUN_ANGGARAN='$thn'";
            return $this->db->query($sql);
    }

    public function getSumRKsetuju($prov='',$thn='')
    {
        $sql="
           SELECT
            SUM(qty.dak) as jml
            FROM(
            SELECT dr.* from budgetdaknf_pengajuan da
            INNER JOIN budgetdaknf_data dr ON dr.id_pengajuan=da.id_pengajuan
            INNER JOIN budgeting_signature bs ON bs.id_pengajuan=da.id_pengajuan
            WHERE da.tahun_anggaran='$thn' AND da.kdprovinsi='$prov' 
            GROUP BY da.id_pengajuan,dr.id) AS qty";
            return $this->db->query($sql);
    }

     public function getSumRKsetujukab($prov='',$kab='',$thn='')
    {
        $sql="
           SELECT
            SUM(qty.dak) as jml
            FROM(
            SELECT dr.* from budgetdaknf_pengajuan da
            INNER JOIN budgetdaknf_data dr ON dr.id_pengajuan=da.id_pengajuan
            INNER JOIN budgeting_signature bs ON bs.id_pengajuan=da.id_pengajuan
            WHERE da.tahun_anggaran='$thn' and da.kdprovinsi='$prov' and da.kdkabupaten='$kab'
            GROUP BY da.id_pengajuan,dr.id) AS qty";
            return $this->db->query($sql);
    }
     public function getSumRKsetujukat($prov='',$kat='',$thn='')
    {
        $sql="
           SELECT
            SUM(qty.dak) as jml
            FROM(
            SELECT dr.* from budgetdaknf_pengajuan da
            INNER JOIN budgetdaknf_data dr ON dr.id_pengajuan=da.id_pengajuan
            INNER JOIN budgeting_signature bs ON bs.id_pengajuan=da.id_pengajuan
            WHERE da.tahun_anggaran='$thn' and da.kdprovinsi='$prov'  and da.kategori='$kat'
            GROUP BY da.id_pengajuan,dr.id) AS qty";
            return $this->db->query($sql);
    }

     public function getSumRKsetujukabkat($prov='',$kab='',$kat='',$thn='')
    {
        $sql="
           SELECT
            SUM(qty.dak) as jml
            FROM(
            SELECT dr.* from budgetdaknf_pengajuan da
            INNER JOIN budgetdaknf_data dr ON dr.id_pengajuan=da.id_pengajuan
            INNER JOIN budgeting_signature bs ON bs.id_pengajuan=da.id_pengajuan
            WHERE da.tahun_anggaran='$thn' and da.kdprovinsi='$prov' and da.kdkabupaten='$kab' and da.kategori='$kat'
            GROUP BY da.id_pengajuan,dr.id) AS qty";
            return $this->db->query($sql);
    }
    public function getSumWhere($field='',$table='',$where='')
    {
        $sql="
                SELECT
                SUM($field) AS jml
                FROM $table
                $where ";
            return $this->db->query($sql);
    }
    public function getCountWhere($table='',$where='')
    {
        $sql="
                SELECT
                COUNT(1) AS jml
                FROM $table
                $where ";
            return $this->db->query($sql);
    }
    public function getCountWhere_id($field='',$table='',$where='')
    {
        $sql="
                SELECT
                COUNT(1) AS jml,
                $field AS id
                FROM $table
                $where ";
            return $this->db->query($sql);
    }
    public function getMaxRealisasi($kab='',$prov='',$kat='',$jd='',$thn='')
    {
         $sql="
                SELECT
                max(realisasi) AS jml
                from pengajuan_monev_dak 
                INNER JOIN data_monev_rka on data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan
                where KodeKabupaten='$kab' AND KodeProvinsi='$prov'  AND  ID_KATEGORI='$kat' AND ID_SUBBIDANG='$jd' AND TAHUN_ANGGARAN='$thn'";
            return $this->db->query($sql);
    }public function getSumRealisasi2($kab='',$prov='',$thn='')
    {
       $sql="   SELECT
                SUM(qty.jumlah) AS jml
                FROM (
                SELECT
                KodeProvinsi,
                KodeKabupaten,
                ID_SUBBIDANG,
                max(realisasi) AS jumlah
                from pengajuan_monev_dak 
                INNER JOIN data_monev_rka on data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan
                where KodeKabupaten='$kab' AND KodeProvinsi='$prov' AND  TAHUN_ANGGARAN='$thn'
                GROUP BY ID_SUBBIDANG,KodeKabupaten ) AS qty";
                return $this->db->query($sql);

    }public function getSumRealisasiProv($prov='',$thn='')
    {
       $sql="   SELECT
                SUM(qty.jumlah) AS jml
                FROM (
                SELECT
                KodeProvinsi,
                KodeKabupaten,
                ID_SUBBIDANG,
                max(realisasi) AS jumlah
                from pengajuan_monev_dak 
                INNER JOIN data_monev_rka on data_monev_rka.id_pengajuan=pengajuan_monev_dak.id_pengajuan
                where KodeProvinsi='$prov' AND TAHUN_ANGGARAN='$thn' 
                GROUP BY ID_SUBBIDANG,KodeKabupaten ) AS qty";
                return $this->db->query($sql);
    }public function getBpomListpagu($prov='',$kab='',$thn='')
    {
        $sql="  SELECT
                bp.id_pagu,
                bp.id_menu,
                bp.satuan,
                bp.pagu,
                bp.tahun,
                bm.menu,
                bm.id,
                bp.kdprovinsi,
                bp.kdkabupaten,
                rp.NamaProvinsi,
                rk.NamaKabupaten
                FROM bpom_pagu bp
                INNER JOIN bpom_menu bm ON bp.id_menu=bm.id
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=bp.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=bp.kdkabupaten AND rk.KodeProvinsi=bp.kdprovinsi
                WHERE bp.kdprovinsi='$prov' AND bp.kdkabupaten='$kab' AND bp.tahun='$thn'";
                return $this->db->query($sql);
    }public function hitungInputbudgeitng($prov='',$kab='',$thn='')
    {
        $sql="  SELECT COUNT(1) as jml from
                (SELECT id_pengajuan  from budgetdaknf_pengajuan
                 WHERE  kdprovinsi='$prov' and kdkabupaten='$kab' AND tahun_anggaran='$thn' and kategori !='0'
                 GROUP BY kategori) as row";
                return $this->db->query($sql);
    }public function hitungPengajuanbudgeitng($prov='',$kab='',$thn='')
    {
         $sql=" SELECT
                COUNT(1) AS jml
                FROM pengajuan_rka_nf da
                WHERE da.kdProvinsi='$prov' AND da.kdKabupaten='$kab' AND da.tahun_anggaran='$thn'  AND status_hide='0' and da.status_ver_menu !='3'";
        return $this->db->query($sql);
    }public function hitungPengajuanTolakkab($prov='',$kab='',$thn='')
    {
         $sql=" SELECT
                COUNT(1) AS jml
                FROM pengajuan_rka_nf da
                WHERE da.kdProvinsi='$prov' AND da.kdKabupaten='$kab' AND da.tahun_anggaran='$thn'  AND status_hide='0' and da.status_ver_menu ='3'";
        return $this->db->query($sql);
    }public function hitungInputbudgeitng2($prov='',$thn='')
    {
        $sql="  SELECT COUNT(1) as jml from
                (SELECT id_pengajuan  from budgetdaknf_pengajuan
                 WHERE  kdprovinsi='$prov' AND tahun_anggaran='$thn' and kategori !='0'
                 GROUP BY kategori) as row";
                return $this->db->query($sql);
    }public function hitungPengajuanbudgeitng2($prov='',$thn='')
    {
         $sql=" SELECT
                COUNT(1) AS jml
                FROM pengajuan_rka_nf da
                WHERE da.kdProvinsi='$prov' AND da.tahun_anggaran='$thn' AND status_hide='0'";
        return $this->db->query($sql);
    }
    public function hitungPengajuanTolak($prov='',$thn='')
    {
         $sql=" SELECT
                COUNT(1) AS jml
                FROM pengajuan_rka_nf da
                WHERE da.kdProvinsi='$prov' AND da.tahun_anggaran='$thn' AND status_hide='0' and da.status_ver_menu ='3'";
        return $this->db->query($sql);
    }
    public function dataLaporanBudgeting($value='')
    {
        $sql=" SELECT 
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.tahun_anggaran,
                k.nama_kategori,
                bd.nama_menu,
                bd.jumlah,
                bd.dak
                FROM budgetdaknf_pengajuan bp
                INNER JOIN dak_nf_kategori k ON k.id_kategori_nf=bp.kategori
                INNER JOIN budgetdaknf_data bd ON bp.id_pengajuan=bd.id_pengajuan
                WHERE bp.tahun_anggaran='2020' AND k.TAHUN_ANGGARAN='2020'";
        return $this->db->query($sql);
    }public function dataLaporanBudgetingSubbidang($value='')
    {
       $sql="  SELECT 
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.tahun_anggaran,
                bp.kategori,
                k.nama_kategori,
                bd.nama_menu,
                SUM(bd.jumlah) AS jumlah,
                SUM(bd.dak) AS dak
                FROM budgetdaknf_pengajuan bp
                INNER JOIN dak_nf_kategori k ON k.id_kategori_nf=bp.kategori
                INNER JOIN budgetdaknf_data bd ON bp.id_pengajuan=bd.id_pengajuan
                WHERE bp.tahun_anggaran='2020' AND k.TAHUN_ANGGARAN='2020'
                GROUP BY  bp.kdprovinsi,bp.kdkabupaten,bp.kategori
                ORDER BY bp.kdprovinsi,bp.kdkabupaten";
        return $this->db->query($sql);
    }

    public function getMonevSubbidang($prov='',$kab='',$thn='',$sub='')
    {
        $sql="  SELECT
                MAX(qty.param) AS jmlparam,
                MAX(qty.realisasi) AS jmlrealisasi,
                MAX(qty.nilai) AS jmlnilai,
                qty.lokasi
                FROM (
                SELECT
                count(pmd.waktu_laporan)*100 AS param,
                SUM(dmr.realisasi) AS realisasi,
                SUM(dmr.fisik) AS nilai,
                dmr.lokasi
                from pengajuan_monev_dak pmd
                INNER JOIN data_monev_rka dmr ON dmr.id_pengajuan = pmd.id_pengajuan
                where pmd.kodekabupaten='$kab' AND pmd.kodeprovinsi='$prov' And pmd.tahun_anggaran='$thn'  AND pmd.ID_SUBBIDANG='$sub'
                GROUP BY pmd.waktu_laporan) AS qty";
        return $this->db->query($sql);
    }public function getMonevSubbidangnf($prov='',$kab='',$thn='',$sub='')
    {
        $sql="  SELECT
                MAX(qty.param) AS jmlparam,
                MAX(qty.realisasi) AS jmlrealisasi,
                MAX(qty.nilai) AS jmlnilai
                FROM (
                SELECT
                pmd.waktu_laporan,
                count(pmd.waktu_laporan)*100 AS param,
                SUM(dnr.realisasi) AS realisasi,
                SUM(dnr.fisik) AS nilai
                FROM
                dak_nf_laporan pmd
                INNER JOIN dak_nf_rka dnr ON dnr.id_pengajuan =  pmd.id_pengajuan
                where pmd.kodekabupaten='$kab' AND pmd.kodeprovinsi='$prov' And pmd.tahun_anggaran='$thn' AND pmd.id_kategori_nf='$sub'
                GROUP BY pmd.waktu_laporan ) AS qty";
        return $this->db->query($sql);
    }public function realisasiByid($id='',$tw='',$tahun='')
    {
        $sql="  SELECT
                dnl.id_kategori_nf,
                dnk.nama_kategori,
                SUM(dnr.realisasi) as realisasi
                FROM dak_nf_laporan dnl
                JOIN dak_nf_rka dnr ON dnr.id_pengajuan=dnl.id_pengajuan
                JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf = dnl.id_kategori_nf 
                WHERE dnl.TAHUN_ANGGARAN=$tahun AND dnl.waktu_laporan=$tw AND dnl.id_kategori_nf=$id";
        return $this->db->query($sql);
    }public function realisasiByid2($tahun='',$id='',$tw='',$prov='')
    {
        $sql="  SELECT
                dnl.id_kategori_nf,
                dnk.nama_kategori,
                SUM(dnr.realisasi) as realisasi
                FROM dak_nf_laporan dnl
                JOIN dak_nf_rka dnr ON dnr.id_pengajuan=dnl.id_pengajuan
                JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf = dnl.id_kategori_nf 
                WHERE dnl.TAHUN_ANGGARAN=$tahun AND dnl.waktu_laporan=$tw AND dnl.id_kategori_nf=$id AND  dnl.KodeProvinsi=$prov";
        return $this->db->query($sql);
    }

    public function ambilNilaipagu($id="",$prov="",$thn="")
    {
        $sql="  SELECT
                SUM(PAGU_SELURUH) as pagu,
                COUNT(1) AS jml
                FROM data_fisik_pagu_tch
                WHERE TAHUN_ANGGARAN='$thn' AND ID_SUBBIDANG='$id' AND KodeProvinsi='$prov'";
        return $this->db->query($sql);
    }public function ambilNilaipagunf($id="",$prov="",$thn="")
    {
       $sql="   SELECT
                SUM(nominal) as pagu,
                COUNT(1) AS jml
                FROM budgetdaknf_pagu
                WHERE tahun='$thn' AND id_jenisdak='$id' AND kdprovinsi='$prov' ";
        return $this->db->query($sql);
    }
    public function allKabupaten($value='')
    {
       $sql="   SELECT * from ref_kabupaten rk
                INNER JOIN ref_provinsi rp on rp.KodeProvinsi=rk.KodeProvinsi
                ORDER BY NamaProvinsi,NamaKabupaten ASC ";
        return $this->db->query($sql);
    }
    public function getProvKab($kp='',$kk='')
    {
       $sql="   SELECT * from ref_kabupaten rk
                INNER JOIN ref_provinsi rp on rp.KodeProvinsi=rk.KodeProvinsi
                WHERE rk.KodeProvinsi='$kp' and rk.KodeKabupaten='$kk'
                ORDER BY NamaProvinsi,NamaKabupaten ASC ";
        return $this->db->query($sql);
    }


    public function ambilMonev2020($prov='',$kab='',$id='',$thn='',$tw='',$menu='')
    {
       $sql="SELECT
             * 
             from dak_nf_laporan dnl
             INNER JOIN dak_nf_rka dnr ON dnr.id_pengajuan=dnl.id_pengajuan
             WHERE dnl.TAHUN_ANGGARAN='$thn' and dnl.KodeProvinsi='$prov' and dnl.KodeKabupaten='$kab' and dnl.id_dak_nf='$id' and dnl.waktu_laporan='$tw' and dnr.id_menu_nf='$menu' ";
        return $this->db->query($sql);
    }

    public function getNIlaikodemenu($kodemenu='')
    {
        $sql=" SELECT
                SUM(asd.realisasi) as realisasi,
                SUM(asd.realisasi_k) as kontrak,
                MAX(asd.fisik) AS output
                FROM(
                SELECT
                *
                FROM(
                SELECT
                *
                FROM data_monev_rka_2020
                WHERE kode_menu='$kodemenu'
                ORDER BY datetime DESC
                 ) as qty
                 GROUP BY qty.triwulan) as asd";
                return $this->db->query($sql);
    }
    public function penunjangInput($kp='',$kk='',$jenis='',$tw='',$tahun='')
    {
         $sql=" SELECT * FROM (
                SELECT
                dp.idpenunjang,
                dp.kodekabupaten,
                dp.kodeprovinsi,
                dp.id_jenis_dak,
                dp.penunjang,
                dp.usulan,
                dp.volume AS volume_p,
                dp.kd_satuan,
                dp.catatan,
                dpi.* 
                FROM dak_penunjang dp INNER JOIN dak_penunjang_input dpi ON dp.idpenunjang= dpi.id_penunjang 
                WHERE dp.kodeprovinsi='$kp' AND dp.kodekabupaten='$kk' AND dp.id_jenis_dak='$jenis' AND dpi.triwulan='$tw' AND dp.tahun='$tahun'
                ORDER BY dpi.datetime DESC ) as qty
                GROUP BY qty.id_penunjang";
                return $this->db->query($sql);
    }

    public function getDetailPenunjangInput($id='')
    {
        $sql=" SELECT
                dp.idpenunjang,
                dp.penunjang,
                dp.usulan,
                dp.volume AS volume_p,
                dp.kd_satuan,
                dp.catatan,
                dpi.* 
                FROM dak_penunjang dp INNER JOIN dak_penunjang_input dpi ON dp.idpenunjang= dpi.id_penunjang 
                WHERE dpi.id='$id'";
                return $this->db->query($sql);
    }
     public function penunjangInputid($kp='',$kk='',$jenis='',$tw='',$tahun='',$id)
    {
         $sql=" SELECT * FROM (
                SELECT
                dp.penunjang,
                dp.usulan,
                dp.volume AS volume_p,
                dp.kd_satuan,
                dp.catatan,
                dpi.* 
                FROM dak_penunjang dp INNER JOIN dak_penunjang_input dpi ON dp.idpenunjang= dpi.id_penunjang 
                WHERE dp.kodeprovinsi='$kp' AND dp.kodekabupaten='$kk' AND dp.id_jenis_dak='$jenis' AND dpi.triwulan='$tw' AND dp.tahun='$tahun' and dp.idpenunjang='$id'
                ORDER BY dpi.datetime DESC ) as qty
                GROUP BY qty.id_penunjang";
                return $this->db->query($sql);
    }
    public function penunjangOutputid($kp='',$kk='',$jenis='',$tahun='',$id)
    {
         $sql=" SELECT
                max(qty.output) as jml
                FROM (
                SELECT
                dp.penunjang,
                dp.usulan,
                dp.volume AS volume_p,
                dp.kd_satuan,
                dp.catatan,
                dpi.* 
                FROM dak_penunjang dp INNER JOIN dak_penunjang_input dpi ON dp.idpenunjang= dpi.id_penunjang 
                WHERE dp.kodeprovinsi='$kp' AND dp.kodekabupaten='$kk' AND dp.id_jenis_dak='$jenis' AND dp.tahun='$tahun' and dp.idpenunjang='$id'
                ORDER BY dpi.datetime DESC
                ) as qty";
                return $this->db->query($sql);
    }
    public function rekapUsulan2021($thn='')
    {
        $sql="  SELECT
                pr.id_pengajuan,
                dr.id,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dr.nama_menu,
                dr.jumlah
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                -- LEFT JOIN pengajuan_nf_verifikasi pnv ON pnv.idpengajuan=pr.id_pengajuan and pnv.iddetail_pengajuan=dr.id
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn'
                ORDER BY pr.id_pengajuan ASC";
                return $this->db->query($sql);
    }
    public function rekapUsulan2021Kat($thn='',$kat='')
    {
        $sql="  SELECT
                pr.id_pengajuan,
                dr.id,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dr.nama_menu,
                dr.jumlah
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                -- LEFT JOIN pengajuan_nf_verifikasi pnv ON pnv.idpengajuan=pr.id_pengajuan and pnv.iddetail_pengajuan=dr.id
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn' and pr.kategori='$kat'
                ORDER BY pr.id_pengajuan ASC";
                return $this->db->query($sql);
    }
    public function listUsulanAll($thn='')
    {
          $sql="SELECT
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_revisi,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rp.lambang,
                rk.NamaKabupaten,
                dk.nama_kategori,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran=2021 
                GROUP BY id_pengajuan
                ORDER BY rk.NamaKabupaten ASC";
        return $this->db->query($sql);
    }
    public function listUsulanStatus($thn='',$status='')
    {
          $sql="SELECT
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_revisi,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rp.lambang,
                rk.NamaKabupaten,
                dk.nama_kategori,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn' AND  pr.status_ver_menu='$status'  
                GROUP BY id_pengajuan
                ORDER BY rk.NamaKabupaten ASC";
        return $this->db->query($sql);
    }
    public function listUsulanKat($thn='',$kat='')
    {
          $sql="SELECT
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_revisi,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rp.lambang,
                rk.NamaKabupaten,
                dk.nama_kategori,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn' and  pr.kategori='$kat'
                GROUP BY id_pengajuan
                ORDER BY rk.NamaKabupaten ASC";
        return $this->db->query($sql);
    }

    public function listUsulanProv($prov='',$thn='')
    {
          $sql="SELECT
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_revisi,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rp.lambang,
                rk.NamaKabupaten,
                dk.nama_kategori,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran=2021 and pr.kdprovinsi='$prov'
                GROUP BY id_pengajuan
                ORDER BY rk.NamaKabupaten ASC";
        return $this->db->query($sql);
    }public function listUsulanKab($prov='',$kab='',$thn='')
    {
          $sql="SELECT
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.kategori,
                pr.kdprovinsi,
                pr.kdkabupaten,
                pr.status_revisi,
                pr.status_ver_menu,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rp.lambang,
                rk.NamaKabupaten,
                dk.nama_kategori,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran=$thn and pr.kdprovinsi='$prov' and pr.kdkabupaten='$kab'
                GROUP BY id_pengajuan";
        return $this->db->query($sql);
    }

    public function getSumJenis($thn='',$prov='',$kab='',$jenis='')
    {
         $sql="SELECT
                pr.kdprovinsi,
                pr.kdkabupaten,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.jenis,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn' and pr.kdprovinsi='$prov' and pr.kdkabupaten='$kab' AND dk.jenis='$jenis'
                GROUP BY dk.jenis";
        return $this->db->query($sql);
    }

    public function getSumJenisTotal($thn='',$prov='',$jenis='')
    {
         $sql="SELECT
                pr.kdprovinsi,
                pr.kdkabupaten,
                rp.KodeProvinsi,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.jenis,
                sum(dr.jumlah) as usulan,
                sum(dr.dak) as dak
                FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pr.kategori
                INNER JOIN ref_provinsi rp ON rp.KodeProvinsi=pr.kdprovinsi
                INNER JOIN ref_kabupaten rk ON rk.KodeKabupaten=pr.kdkabupaten AND rk.KodeProvinsi=pr.kdprovinsi
                WHERE pr.tahun_anggaran='$thn' and pr.kdprovinsi='$prov' AND dk.jenis='$jenis'
                GROUP BY dk.jenis";
        return $this->db->query($sql);
    }public function pengajuanChat($id='',$dari='',$row='')
    {
       $sql="SELECT
        p.*,
        u.KDUNIT,
        u.NAMA_USER,
        u.kdsatker
        from pengajuan_nf_chat p
        INNER JOIN users u ON u.USER_ID=p.id_from
        WHERE p.idpengajuan='$id'
        ORDER BY p.datetime ASC
        LIMIT $dari,$row ";
        return $this->db->query($sql);
    }public function budgeting_pagu($kd_prov='',$thn='')
    {
       $sql="SELECT
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            dk.nama_kategori,
            bp.id_pengajuan,
            bp.kdprovinsi,
            bp.kdkabupaten,
            bp.kategori,
            bp.tahun_anggaran,
            SUM(bd.jumlah) AS alokasi,
            SUM(bd.dak) AS rk
            FROM budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE bp.tahun_anggaran ='$thn' and bp.isActive=1 and bp.kdprovinsi='$kd_prov'
            GROUP BY bp.id_pengajuan
            ORDER BY bp.kdkabupaten,bp.kdkabupaten";
        return $this->db->query($sql);
    }public function getDatapengajuanNF_KAB($prov='',$kab='',$thn='')
    {
         $sql="
            SELECT
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            dk.nama_kategori,
            bp.id_pengajuan,
            bp.kdprovinsi,
            bp.kdkabupaten,
            bp.kategori,
            bp.tahun_anggaran,
                        bd.*
            FROM budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE bp.tahun_anggaran ='$thn' and bp.isActive=1 and bp.kdprovinsi='$prov' and bp.kdkabupaten='$kab'
            ORDER BY bp.kdkabupaten,bp.kdkabupaten
            ";
        return $this->db->query($sql);
    }

    public function getDatapengajuanNF_PROV($prov='',$thn='')
    {
         $sql="
            SELECT
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            dk.nama_kategori,
            bp.id_pengajuan,
            bp.kdprovinsi,
            bp.kdkabupaten,
            bp.kategori,
            bp.tahun_anggaran,
                        bd.*
            FROM budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE bp.tahun_anggaran ='$thn' and bp.isActive=1 
            -- and bp.kategori='40'
            and bp.kdprovinsi='$prov'
            ORDER BY bp.kdkabupaten,bp.kdkabupaten
            ";
        return $this->db->query($sql);
    }

    public function getDatapengajuanNF_ALL($thn='')
    {
         $sql="
            SELECT
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            dk.nama_kategori,
            bp.id_pengajuan,
            bp.kdprovinsi,
            bp.kdkabupaten,
            bp.kategori,
            bp.tahun_anggaran,
                        bd.*
            FROM budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE bp.tahun_anggaran ='$thn' and bp.isActive=1 
            ORDER BY bp.kdkabupaten,bp.kdkabupaten
            ";
        return $this->db->query($sql);
    }

    public function budgeting_pagu_all($thn='')
    {
       $sql="SELECT
            rk.NamaKabupaten,
            rp.NamaProvinsi,
            dk.nama_kategori,
            bp.id_pengajuan,
            bp.kdprovinsi,
            bp.kdkabupaten,
            bp.kategori,
            bp.tahun_anggaran,
            SUM(bd.jumlah) AS alokasi,
            SUM(bd.dak) AS rk
            FROM budgetdaknf_pengajuan bp
            INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE bp.tahun_anggaran ='$thn' and bp.isActive=1
            GROUP BY bp.id_pengajuan
            ORDER BY bp.kdkabupaten,bp.kdkabupaten";
        return $this->db->query($sql);
    }public function dataFiksiPagu($kd_prov='',$thn='')
    {
         $sql=" SELECT
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                da.kodekabupaten,
                da.kodeprovinsi,
                da.id_kategori,
                da.id_jenis_dak,
                SUM(da.usulan) AS pagu
                FROM dak_fisik_2020 da
                INNER JOIN kategori k ON k.ID_KATEGORI=da.ID_KATEGORI
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=da.id_jenis_dak
                INNER JOIN ref_kabupaten rk ON da.kodekabupaten=rk.kodekabupaten AND da.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.tahun='$thn'  and da.kodeprovinsi='$kd_prov'
                GROUP BY da.kodekabupaten,da.kodeprovinsi,da.id_jenis_dak
                ORDER BY da.kodeprovinsi,da.kodekabupaten";
        return $this->db->query($sql);
    }public function dataFiksiPagu_all($thn='')
    {
        $sql=" SELECT
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                da.kodekabupaten,
                da.kodeprovinsi,
                da.id_kategori,
                da.id_jenis_dak,
                SUM(da.usulan) AS pagu
                FROM dak_fisik_2020 da
                INNER JOIN kategori k ON k.ID_KATEGORI=da.ID_KATEGORI
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=da.id_jenis_dak
                INNER JOIN ref_kabupaten rk ON da.kodekabupaten=rk.kodekabupaten AND da.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.tahun='$thn'  
                GROUP BY da.kodekabupaten,da.kodeprovinsi,da.id_jenis_dak
                ORDER BY da.kodeprovinsi,da.kodekabupaten";
        return $this->db->query($sql);
    }public function dataFisikListSinkron($kdjenis='')
    {
        $sql=" SELECT
            kdkabupaten,
            kdprovinsi,
            kdjenis,
            kd_subbidang,
            pengusul_nama,
            provinsi_nama,
            sub_bidang,
            jenis
            from singkronisasi_fisik_2021
            WHERE kd_subbidang='$kdjenis' 
            GROUP BY kdprovinsi,kdkabupaten";
        return $this->db->query($sql);
    }
    public function dataFisikListSinkron_baru($thn='',$kdunti='')
    {
        $sql=" SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
             sp.prioritas_kegiatan,
            sp.menu
            from singkronisasi_fisik_2021 sp
            WHERE sp.tahun=$thn and sp.kd_unit='$kdunti' 
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }

    public function datasinkronisasiFisik($thn='',$kdunti='',$status='')
    {
        $sql=" 
            SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
             sp.prioritas_kegiatan,
            sa.status
            from singkronisasi_fisik_2021 sp
            INNER JOIN sinkronisasi_approval_sinkron sa ON sa.id=sp.id_group
            WHERE sp.tahun=$thn and sp.kd_unit='$kdunti' and sa.status='$status'
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian
            ORDER BY sp.provinsi_nama,sp.pengusul_nama,sp.sub_bidang,sp.kd_menu,sp.rincian";
        return $this->db->query($sql);
    }

    public function datasinkronisasiFisik1($where='')
    {
        $sql=" 
           SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
             sp.prioritas_kegiatan,
            sp.menu
            from singkronisasi_fisik_2021 sp
            INNER JOIN sinkronisasi_input si ON si.id_drk=sp.id_drk and si.tahun=sp.tahun
            $where
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian
            ORDER BY sp.provinsi_nama,sp.pengusul_nama,sp.sub_bidang,sp.kd_menu,sp.rincian";
        return $this->db->query($sql);
    }


     public function datasinkronisasiFisikView($where='')
    {
        $sql=" 
            SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
            sp.prioritas_kegiatan,
            sp.menu
            from singkronisasi_fisik_2021 sp
            $where
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian
            ORDER BY sp.provinsi_nama,sp.pengusul_nama,sp.sub_bidang,sp.kd_menu,sp.rincian";
        return $this->db->query($sql);
    }
    public function dataFisikListSinkron1($thn='',$kdunti='')
    {
        $sql=" SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
             sp.prioritas_kegiatan,
            sa.status
            from singkronisasi_fisik_2021 sp
            left JOIN sinkronisasi_approval sa ON sa.kdkabupaten=sp.kdkabupaten and sa.kdprovinsi=sp.kdprovinsi and sa.kd_subbidang=sp.kd_subbidang and sa.kd_menu=sp.kd_menu and sa.kd_rincian=sp.kd_rincian
            WHERE sp.tahun=$thn and sp.kd_unit='$kdunti' and (sa.status is null OR sa.status='0')
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }
    public function dataFisikListSinkron2($thn='',$kdunti='',$status='')
    {
        $sql=" SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
             sp.prioritas_kegiatan,
            sa.status
            from singkronisasi_fisik_2021 sp
            
            INNER JOIN sinkronisasi_approval sa ON sa.kdkabupaten=sp.kdkabupaten and sa.kdprovinsi=sp.kdprovinsi and sa.kd_subbidang=sp.kd_subbidang and sa.kd_menu=sp.kd_menu and sa.kd_rincian=sp.kd_rincian
            WHERE sp.tahun=$thn and sp.kd_unit='$kdunti' and sa.status=$status
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }
    public function dataFisikListSinkron3($thn='',$kdunti='',$status='')
    {
        $sql="  SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.rincian,
            sp.kd_rincian,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
             sp.prioritas_kegiatan,
            sa.aproval_pi
            from singkronisasi_fisik_2021 sp
            INNER JOIN sinkronisasi_input_awal sa ON sa.id_drk=sp.id_drk
            WHERE sp.tahun=2022 and sp.kd_unit='04' and sa.aproval_pi !='0'
            GROUP BY sp.kdprovinsi,sp.kdkabupaten,sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }
    public function subbidang_unit($thn='',$kdunti='')
    {
         $sql="SELECT
                sp.kd_subbidang,
                sp.sub_bidang,
                sp.rincian,
                sp.kd_rincian,
                sp.kd_menu,
                sp.menu,
                count(1) as hitung,
                sum(nilai_usulan) as jml
                from singkronisasi_fisik_2021 sp
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=sp.kd_subbidang
                WHERE sp.tahun='$thn' and djd.kd_unit='$kdunti' 
                GROUP BY sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }
    public function subbidang_unit2($thn='',$ks='')
    {
         $sql="SELECT
                sp.kd_subbidang,
                sp.sub_bidang,
                sp.rincian,
                sp.kd_rincian,
                sp.kd_menu,
                sp.menu,
                count(1) as hitung,
                sum(nilai_usulan) as jml
                from singkronisasi_fisik_2021 sp
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=sp.kd_subbidang
                WHERE sp.tahun='$thn' and djd.ID_JENIS_DAK='$ks' 
                GROUP BY sp.kd_subbidang,sp.kd_menu,sp.kd_rincian";
        return $this->db->query($sql);
    }
    public function ambilNilaiSinkronAproved($sb='',$st='')
    {
        
        $sql="
            SELECT
            SUM(qty.jml) AS nilai
            FROM
            (
            SELECT
            CASE
            WHEN si.id_sinkron IS NULL THEN sf.usulan_pusat
            ELSE si.p_nilai
            END AS jml,
            CASE
                WHEN si.id_sinkron IS NULL THEN sf.approval_sum
                ELSE si.status_nilai
            END AS statusnya
            from singkronisasi_fisik_2021 sf
            LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
            WHERE sf.kd_subbidang='$sb') AS qty
            WHERE qty.statusnya='$st'";
        return $this->db->query($sql);
    }public function getDetailSinkronFisik($sb='')
    {
        $sql="      SELECT
                        prh.pengusul_nama,
                        prh.provinsi_nama,
                        prh.sub_bidang,
                        prh.jenis,
                        prh.kdjenis,
                        prh.kdprovinsi,
                        prh.kdkabupaten,
                        SUM(prh.usulan) AS usulan,
                        SUM(prh.usulan_pusat) AS usulan_pusat,
                        SUM(prh.nilai_err) AS nilai_err,
                        SUM(prh.nilai_unit) AS total_penilaian_unit,
                        SUM(prh.nilai_approve) as nilai_approve,
                        SUM(prh.nilai_reject) AS nilai_reject,
                        SUM(prh.nilai_discus) AS nilai_discus,
                        SUM(prh.par_ditolak) AS par_tolak
                        FROM
                        (
                        SELECT
                        qty.*,
                        CASE 
                        WHEN qty.status_unit ='1' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_approve,
                        CASE 
                        WHEN qty.status_unit ='2' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_reject,
                        CASE 
                        WHEN qty.status_unit ='3' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_discus
                        FROM
                        (
                        SELECT
                        sf.pengusul_nama,
                        sf.provinsi_nama,
                        sf.sub_bidang,
                        sf.jenis,
                        sf.kdjenis,
                        sf.kdprovinsi,
                        sf.kdkabupaten,
                        sf.nilai_usulan as usulan,
                        sf.usulan_pusat as usulan_pusat,
                        CASE
            WHEN si.id_sinkron IS NULL THEN 0
            ELSE si.p_nilai
            END AS nilai_err,
            CASE
            WHEN si.id_sinkron IS NULL THEN sf.usulan_pusat
            ELSE si.p_nilai
            END AS nilai_unit,
            CASE
                WHEN si.id_sinkron IS NULL THEN sf.approval_sum
                ELSE si.status_nilai
            END AS status_unit,
                        CASE
                WHEN sf.approval_sum ='2' THEN 0
                ELSE 1
            END AS par_ditolak
                        from singkronisasi_fisik_2021 sf
                        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
                        WHERE  sf.kd_subbidang='$sb' ) as qty 
                        ) AS prh
                        GROUP BY prh.kdprovinsi,prh.kdkabupaten";
        return $this->db->query($sql);
    }public function getDetailSinkronFisik2($sb='')
    {
        $sql="  SELECT
                        prh.pengusul_nama,
                        prh.provinsi_nama,
                        prh.sub_bidang,
                         prh.kd_subbidang,
                        prh.jenis,
                        prh.kdjenis,
                        prh.kdprovinsi,
                        prh.kdkabupaten,
                        SUM(prh.usulan) AS usulan,
                        SUM(prh.usulan_pusat) AS usulan_pusat,
                        SUM(prh.nilai_err) AS nilai_err,
                        SUM(prh.nilai_unit) AS total_penilaian_unit,
                        SUM(prh.nilai_approve) as nilai_approve,
                        SUM(prh.nilai_reject) AS nilai_reject,
                        SUM(prh.nilai_discus) AS nilai_discus,
                        SUM(prh.stok_program) AS stok_program,
                        SUM(prh.discus_stts) AS discus_stts,
                        SUM(prh.par_ditolak) AS par_tolak
                        FROM
                        (
                        SELECT
                        qty.*,
                        CASE 
                        WHEN qty.status_unit ='1' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_approve,
                        CASE 
                        WHEN qty.status_unit ='2' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_reject,
                        CASE 
                        WHEN qty.status_unit ='3' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_discus
                        FROM
                        (
                        SELECT
                        sf.pengusul_nama,
                        sf.provinsi_nama,
                        sf.sub_bidang,
                        sf.kd_subbidang,
                        sf.jenis,
                        sf.kdjenis,
                        sf.kdprovinsi,
                        sf.kdkabupaten,
                        sf.nilai_usulan as usulan,
                        sf.usulan_pusat as usulan_pusat,
                        CASE
            WHEN si.id_sinkron IS NULL THEN 0
            ELSE si.p_nilai
            END AS nilai_err,
            CASE
            WHEN si.id_sinkron IS NULL THEN sf.usulan_pusat
            ELSE si.p_nilai
            END AS nilai_unit,
            CASE
                WHEN si.id_sinkron IS NULL THEN sf.approval_sum
                ELSE si.status_nilai
            END AS status_unit,
                        
                        CASE
            WHEN si.status_nilai='3' and sf.approval_sum !='2' THEN si.p_nilai
            ELSE 0
            END AS stok_program,
                        
                        CASE
            WHEN si.status_nilai='3' THEN 1
            ELSE 0
            END AS discus_stts,
                        
                        CASE
                WHEN sf.approval_sum ='2' THEN 0
                ELSE 1
            END AS par_ditolak
                        from singkronisasi_fisik_2021 sf
                        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
                        WHERE  sf.kd_subbidang='$sb') as qty 
                        ) AS prh
                        GROUP BY prh.kdprovinsi,prh.kdkabupaten";
        return $this->db->query($sql);
    }public function getDetailSinkronFisik3()
    {
        $sql="  SELECT
                        prh.pengusul_nama,
                        prh.provinsi_nama,
                        prh.sub_bidang,
                         prh.kd_subbidang,
                        prh.jenis,
                        prh.kdjenis,
                        prh.kdprovinsi,
                        prh.kdkabupaten,
                        prh.kd_subbidang,
                        SUM(prh.usulan) AS usulan,
                        SUM(prh.discus_unit) AS discus_unit,
                        SUM(prh.approval_units) AS approval_units,
                        SUM(prh.usulan_pusat) AS usulan_pusat,
                        SUM(prh.nilai_err) AS nilai_err,
                        SUM(prh.nilai_unit) AS total_penilaian_unit,
                        SUM(prh.nilai_approve) as nilai_approve,
                        SUM(prh.nilai_reject) AS nilai_reject,
                        SUM(prh.nilai_discus) AS nilai_discus,
                        SUM(prh.stok_program) AS stok_program,
                        SUM(prh.discus_stts) AS discus_stts,
                        SUM(prh.par_ditolak) AS par_tolak
                        FROM
                        (
                        SELECT
                        qty.*,
                        CASE 
                        WHEN qty.status_unit ='1' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_approve,
                        CASE 
                        WHEN qty.status_unit ='2' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_reject,
                        CASE 
                        WHEN qty.status_unit ='3' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_discus
                        FROM
                        (
                        SELECT
                        sf.pengusul_nama,
                        sf.provinsi_nama,
                        sf.sub_bidang,
                        sf.kd_subbidang,
                        sf.jenis,
                        sf.kdjenis,
                        sf.kdprovinsi,
                        sf.kdkabupaten,
                        sf.nilai_usulan as usulan,
                        sf.usulan_pusat as usulan_pusat,
                        CASE
            WHEN sf.approval_sum=1 THEN sf.usulan_pusat
            ELSE 0
            END AS approval_units,
                        
                        CASE
            WHEN sf.approval_sum=0 OR sf.approval_sum=3 THEN sf.usulan_pusat
            ELSE 0
            END AS discus_unit,
                        CASE
            WHEN si.id_sinkron IS NULL THEN 0
            ELSE si.p_nilai
            END AS nilai_err,
            CASE
            WHEN si.id_sinkron IS NULL THEN sf.usulan_pusat
            ELSE si.p_nilai
            END AS nilai_unit,
            CASE
                WHEN si.id_sinkron IS NULL THEN sf.approval_sum
                ELSE si.status_nilai
            END AS status_unit,
                        
                        CASE
            WHEN si.status_nilai='3' and sf.approval_sum !='2' THEN si.p_nilai
            ELSE 0
            END AS stok_program,
                        
                        CASE
            WHEN si.status_nilai='3' THEN 1
            ELSE 0
            END AS discus_stts,
                        
                        CASE
                WHEN sf.approval_sum ='2' THEN 0
                ELSE 1
            END AS par_ditolak
                        from singkronisasi_fisik_2021 sf
                        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk) as qty 
                        ) AS prh
                        GROUP BY prh.kd_subbidang";
        return $this->db->query($sql);
    }public function getDetailSinkronFisikDetail2($sb='')
    {
         $sql="SELECT
                        qty.*,
                        CASE 
                        WHEN qty.status_unit ='1' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_approve,
                        CASE 
                        WHEN qty.status_unit ='2' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_reject,
                        CASE 
                        WHEN qty.status_unit ='3' THEN qty.nilai_unit
                        ELSE 0
                        END AS nilai_discus
                        FROM
                        (
                        SELECT
                        sf.*,
                        sf.nilai_usulan as usulan,
                        
                        CASE
            WHEN sf.approval_sum=1 THEN sf.usulan_pusat
            ELSE 0
            END AS approval_units,
                        
                        CASE
            WHEN sf.approval_sum=0 OR sf.approval_sum=3 THEN sf.usulan_pusat
            ELSE 0
            END AS discus_unit,
                        
                        CASE
            WHEN si.id_sinkron IS NULL THEN 0
            ELSE si.p_nilai
            END AS nilai_err,
                        
                        
            CASE
            WHEN si.id_sinkron IS NULL THEN sf.usulan_pusat
            ELSE si.p_nilai
            END AS nilai_unit,
            CASE
                WHEN si.id_sinkron IS NULL THEN sf.approval_sum
                ELSE si.status_nilai
            END AS status_unit,
                        
                        CASE
            WHEN si.status_nilai='3' THEN si.p_nilai
            ELSE 0
            END AS stok_program,
                        
                        CASE
            WHEN si.status_nilai='3' THEN 1
            ELSE 0
            END AS discus_stts,
                        
                        CASE
                WHEN sf.approval_sum ='2' THEN 0
                ELSE 1
            END AS par_ditolak
                        from singkronisasi_fisik_2021 sf
                        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
                        WHERE sf.kd_subbidang='$sb'
                        ) as qty ";
        return $this->db->query($sql);
    }public function getDetailSinkronFisikDetail($sb='')
    {
         $sql="SELECT
        * 
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        WHERE  sf.kd_subbidang='$sb'";
        return $this->db->query($sql);
    }
    public function getDetailSinkronFisikDetailKegiatan2($sb='',$rincian='')
    {
        $sql="SELECT
        * 
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        WHERE  sf.kd_subbidang='$sb' AND sf.rincian='$rincian' ";
        return $this->db->query($sql);
    }

    public function getDetailSinkronFisikDetailKegiatan($sb='',$rincian='')
    {
         $sql="SELECT
        sf.id_drk AS drkid,
        sf.*,
        si.*
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        WHERE  sf.kd_subbidang='$sb' AND sf.rincian='$rincian' ";
        return $this->db->query($sql);

        // AND sf.approval_sum !='2'
    }

     public function getDetailSinkronFisikDetailKegiatanXYZ($sb='',$rincian='')
    {
         $sql="SELECT
        sf.id_drk AS drkid,
        sf.*,
        si.*
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        WHERE  sf.menu='02-Penguatan Sarana Pelayanan Ibu dan Anak RS PONEK'  AND sf.approval_sum !='2'";
        return $this->db->query($sql);
    }

    public function getDetailSinkronFisikDetailKegiatanProvinsi($sb='',$rincian='')
    {
         $sql="SELECT
        sf.id_drk AS drkid,
        sf.*,
        si.*,
                sr.kd_unit
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
                LEFT JOIN sinkronisasi_unit_rincian sr ON sr.rincian_menu =sf.rincian
        WHERE kd_subbidang='47'  AND sf.approval_sum !='2' and sf.rincian !='01-BMHP'
                GROUP BY sf.id_drk
        --  SELECT
        -- sf.id_drk AS drkid,
        -- sf.*,
        -- si.*
        -- FROM singkronisasi_fisik_2021 sf
        -- LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        -- WHERE kd_subbidang='47'  AND sf.approval_sum !='2' 
        -- WHERE si.status_nilai='1' and si.p_volume='0' and si.p_nilai='0' and si.p_cost !='0'
        -- menu LIKE '%PONEK%' AND sf.kdkabupaten='14' AND sf.kdprovinsi='02'

        --  ";
        return $this->db->query($sql);
    }

     public function getDetailSinkronFisikDetailKegiatanProvinsi2($sb='',$rincian='')
    {
         $sql="
                SELECT * from singkronisasi_fisik_2021 sf
                INNER JOIN sinkronisasi_input2 si ON si.id_drk=sf.id_drk
                WHERE sf.kd_subbidang='47'
                 ";
        return $this->db->query($sql);
    }

    public function ambilPerkegiatan($iddrk='')
    {
        $sql="
        SELECT
        *,
        CASE
        WHEN si.id_sinkron IS NULL THEN 0
        ELSE 1
        END AS par_input
        FROM singkronisasi_fisik_2021 sf
        LEFT JOIN sinkronisasi_input si ON sf.id_drk=si.id_drk
        WHERE sf.id_drk='$iddrk' ";
        return $this->db->query($sql);
    }public function ambilKegiatandata($sb='')
    {
       $sql="SELECT
            sf.rincian,
            COUNT(sf.id) AS jml
            FROM singkronisasi_fisik_2021 sf
            WHERE sf.kd_subbidang='$sb'
            GROUP BY rincian";
        return $this->db->query($sql);
    }public function ambilDataExport($sb='')
    {
       $sql="SELECT
       *
            FROM singkronisasi_fisik_2021_export
            LIMIT 0,200 ";
        return $this->db->query($sql);
    }public function getDataSinkronInput($iddrk='')
    {
       $sql="SELECT
            *
            FROM sinkronisasi_input
            WHERE id_drk='$iddrk' ";
        return $this->db->query($sql);
    }public function getDataJsonSinkronALL($value='')
    {
        $sql="  SELECT
sf.*,
si.tindak_lanjut,
si.status_nilai,
si.p_nilai,
si.p_volume,
si.p_cost,
si.catatan,
        CASE
        WHEN si.id_sinkron IS NULL THEN 0
        ELSE 1
        END AS par_input
FROM singkronisasi_fisik_2021_export sf
LEFT JOIN sinkronisasi_input si ON si.id_drk=sf.id_drk";
        return $this->db->query($sql);
    }public function getDataJsonSinkronALL2($value='')
    {
        $sql="  SELECT
sf.*,
si.tindak_lanjut,
si.status_nilai,
si.p_nilai,
si.p_volume,
si.p_cost,
si.catatan,
        CASE
        WHEN si.id_sinkron IS NULL THEN 0
        ELSE 1
        END AS par_input
FROM singkronisasi_fisik_2021 sf
LEFT JOIN sinkronisasi_input si ON si.id_drk=sf.id_drk
WHERE sf.id_drk='376344'
-- sf.kdkabupaten='51' AND sf.kdprovinsi='90' and sf.kd_subbidang=46
";
        return $this->db->query($sql);
    }
    public function getDaftarBpom($tahun='')
    {
         $sql=" SELECT
                bp.id_bpompengajuan,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                bm.menu,
                bk.kegiatan,
                bs.sub_kegiatan,
                bp.alokasi
                from bpom_pengajuan bp
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN bpom_menu bm ON bm.id=bp.id_menu
                INNER JOIN bpom_kegiatan bk ON bk.id_kegiatan=bp.id_kegiatan
                INNER JOIN bpom_subkegiatan bs ON bs.id_subkegiatan=bp.id_subkegiatan 
                WHERE bp.tahun='$tahun'
                ORDER BY rp.NamaProvinsi,rk.NamaKabupaten ";
        return $this->db->query($sql);
    }
    public function getIsiMonevBpom($id='',$tw='')
    {
        $sql="
        SELECT *
        from bpom_monev 
        WHERE id_pengajuan='$id' and triwulan='$tw' ";
        return $this->db->query($sql);
    }

    public function getParMonevBpom($id='',$tw='')
    {
        $sql="
        SELECT 
        COUNT(id_monev) as jml
        from bpom_monev 
        WHERE id_pengajuan='$id' and triwulan='$tw' ";
        return $this->db->query($sql);
    }
    public function kategoriRevisirk($tahun='')
    {
         $sql=" SELECT
                dk.*
                from budgetdaknf_revisirk_menu bm
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bm.id_kategori_nf
                WHERE bm.tahun='$tahun'
                GROUP BY bm.id_kategori_nf ";
        return $this->db->query($sql);
    }public function getDatalaporanrevisiRk($tahun='',$where='')
    {
        $sql=" SELECT
            rp.namaprovinsi,
            rk.namakabupaten,
            dnk.nama_kategori,
            SUM(bd.nilai_alokasi) AS jumlah,
            (SELECT COUNT(bs.id_pengajuan_rk) FROM budgetdaknf_revisirk_signature bs WHERE bs.id_pengajuan_rk=bp.id_pengajuan_rk AND bs.flag='1' GROUP BY bs.id_pengajuan_rk) AS ttdkadinkes,
            (SELECT COUNT(bs.id_pengajuan_rk) FROM budgetdaknf_revisirk_signature bs WHERE bs.id_pengajuan_rk=bp.id_pengajuan_rk AND bs.flag='3' GROUP BY bs.id_pengajuan_rk) AS ttdapip
            from budgetdaknf_revisirk_pengajuan bp
            INNER JOIN budgetdaknf_revisirk_data bd ON bd.id_pengajuan_rk=bp.id_pengajuan_rk
            INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
            WHERE bp.tahun_anggaran='$tahun' $where
            GROUP BY bp.id_pengajuan_rk
            ORDER BY rp.namaprovinsi,rk.kodekabupaten ";
        return $this->db->query($sql);
    }

    public function getDatalaporanrevisiRk_menu($tahun='',$where='')
    {
        $sql=" SELECT
                bm.*,
                rp.namaprovinsi,
                rk.namakabupaten,
                bd.nilai_alokasi,
                dnk.nama_kategori
                from budgetdaknf_revisirk_pengajuan bp
                INNER JOIN budgetdaknf_revisirk_data bd ON bd.id_pengajuan_rk=bp.id_pengajuan_rk
                INNER JOIN budgetdaknf_revisirk_menu bm ON bm.id=bd.id_revisi_menu
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.tahun_anggaran='$tahun' $where
                ORDER BY rp.namaprovinsi,rk.kodekabupaten";
        return $this->db->query($sql);
    }
    public function getDatalaporanrevisiRk_menupus($tahun='',$where='')
    {
        $sql="  SELECT
                rp.namaprovinsi,
                rk.namakabupaten,
                dnk.nama_kategori,
                rpm.nama,
                rpm.kecamatan,
                bm.*,
                bdp.alokasi
                from budgetdaknf_revisirk_pengajuan bp
                INNER JOIN budgetdaknf_revisirk_data bd ON bd.id_pengajuan_rk=bp.id_pengajuan_rk
                INNER JOIN budgetdaknf_revisirk_menu bm ON bm.id=bd.id_revisi_menu
                INNER JOIN budgetdaknf_revisirk_data_puskesmas bdp ON bdp.id_pengajuan=bp.id_pengajuan_rk and bdp.id_menu=bd.id_revisi_menu
                INNER JOIN ref_puskesmas_2020 rpm ON rpm.id=bdp.id_puskesmas
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dnk ON dnk.id_kategori_nf=bp.kategori
                WHERE bp.tahun_anggaran='$tahun' $where
                ORDER BY rp.namaprovinsi,rk.kodekabupaten";
        return $this->db->query($sql);
    }public function getLapMonevNF_ALL($value='')
    {
        $sql="  SELECT
                da.id_pengajuan,
                da.ID_KATEGORI,
                da.tanggal_pembuatan,
                da.KodeProvinsi,
                da.KodeKabupaten,
                da.TAHUN_ANGGARAN,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                pd.Masalah,
                r.Satuan,
                dmr.*
                from pengajuan_monev_dak da
                INNER JOIN data_monev_rka_2020 dmr ON dmr.id_pengajuan=da.id_pengajuan
                INNER JOIN kategori k ON k.ID_KATEGORI=da.ID_KATEGORI
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=da.ID_SUBBIDANG
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dmr.KodeMasalah
                INNER JOIN ref_satuan r ON r.KodeSatuan = dmr.kd_satuan
                INNER JOIN ref_kabupaten rk ON da.KodeKabupaten=rk.kodekabupaten AND da.KodeProvinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE  da.ID_SUBBIDANG='45' and  da.WAKTU_LAPORAN='1'  and  da.TAHUN_ANGGARAN='2021'";
        return $this->db->query($sql);
    }public function carikoders($nama='')
    {
        $sql="
            SELECT * FROM ref_rumahsakit
            WHERE nama_rs LIKE '%$nama%' ";
        return $this->db->query($sql);
    }public function carikodealat2($nama='')
    {
        $sql="
            SELECT * FROM rakontek_alat
            WHERE nomenklatur LIKE '%$nama%' and kdalat is not null  ";
        return $this->db->query($sql);
    }public function carikodealat($nama='')
    {
        $sql="
            SELECT * FROM ref_alat
            WHERE nomenklatur = '$nama' and kdalat is not null  ";
        return $this->db->query($sql);
    }

    public function getMonevLaporanview($where='')
    {
         $sql=" SELECT
                da.id_pengajuan as pengajuan_id,
                da.ID_KATEGORI,
                da.tanggal_pembuatan,
                da.KodeProvinsi,
                da.KodeKabupaten,
                da.TAHUN_ANGGARAN,
                da.waktu_laporan,
                k.NAMA_KATEGORI,
                djd.NAMA_JENIS_DAK,
                pd.Masalah,
                r.Satuan,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                dmr.*
                from pengajuan_monev_dak da
                INNER JOIN data_monev_rka_2020 dmr ON dmr.id_pengajuan=da.id_pengajuan 
                INNER JOIN kategori k ON k.ID_KATEGORI=da.ID_KATEGORI 
                INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=da.ID_SUBBIDANG 
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dmr.KodeMasalah
                INNER JOIN ref_satuan r ON r.KodeSatuan = dmr.kd_satuan
                INNER JOIN ref_kabupaten rk ON da.KodeKabupaten=rk.kodekabupaten AND da.KodeProvinsi=rk.kodeprovinsi 
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                $where";
        return $this->db->query($sql);
    }
    public function getMonevLaporanview_nf($where='')
    {
         $sql=" SELECT
                da.id_pengajuan,
                da.id_kategori_nf,
                da.tanggal_pembuatan,
                da.KodeProvinsi,
                da.KodeKabupaten,
                da.waktu_laporan,
                da.TAHUN_ANGGARAN,
                dk.nama_kategori,
                rk.NamaKabupaten,
                rp.NamaProvinsi,
                dnr.*
                FROM dak_nf_laporan da
                INNER JOIN dak_nf_rka dnr ON dnr.id_pengajuan=da.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori_nf
                INNER JOIN ref_kabupaten rk ON da.KodeKabupaten=rk.kodekabupaten AND da.KodeProvinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                $where ";
                return $this->db->query($sql);
    }public function getMonevDetail_nf($idpengajuan='')
    {
         $sql=" SELECT
                dnr.*,
                pd.masalah
                FROM dak_nf_rka dnr
                INNER JOIN permasalahan_dak pd ON pd.KodeMasalah=dnr.KodeMasalah
                WHERE dnr.id_pengajuan='$idpengajuan'";
                return $this->db->query($sql);
    }public function getMonevpuskesmas_rev($idp='',$idm='')
    {
         $sql=" SELECT * from ref_puskesmas_2020 rp
                INNER JOIN budgetdaknf_revisirk_data_puskesmas  bp ON rp.id=bp.id_puskesmas
                WHERE bp.id_pengajuan='$idp' and bp.id_menu='$idm'";
                return $this->db->query($sql);
    }public function getMonevpuskesmas_nonrev($kp='',$kk='',$kat='',$idm='')
    {
         $sql=" SELECT * from data_rka_nf_puskesmas pr
                INNER JOIN ref_puskesmas_2020 dk ON dk.id=pr.id_puskesmas
                INNER JOIN dak_nf_menu dnf ON pr.id_menu=dnf.id
                WHERE dnf.id_menu='$idm' AND pr.kdprovinsi='$kp' AND pr.kdkabupaten='$kk' AND pr.kdkategori='$kat'";
                return $this->db->query($sql);
    } 
    public function getdataFisikawal2($value='')
    {
        // asdsa
       $sql="SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
            sp.rincian,
            sp.kd_rincian,
            sa.status
            from singkronisasi_fisik_2021 sp
            INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=sp.kd_subbidang
            left JOIN sinkronisasi_approval sa ON sa.kdkabupaten=sp.kdkabupaten and sa.kdprovinsi=sp.kdprovinsi and sa.kd_subbidang=sp.kd_subbidang and sa.kd_menu=sp.kd_menu and sa.kd_rincian=sp.kd_rincian
             $where
            ";
                return $this->db->query($sql);
    }

    public function getdataFisikawal($where='')
    {
         $sql="SELECT
            sp.kdkabupaten,
            sp.kdprovinsi,
            sp.kdjenis,
            sp.kd_subbidang,
            sp.pengusul_nama,
            sp.provinsi_nama,
            sp.sub_bidang,
            sp.jenis,
            sp.kd_menu,
            sp.menu,
            sp.rincian,
            sp.kd_rincian,
            sa.status
            from singkronisasi_fisik_2021 sp
            INNER JOIN dak_jenis_dak djd ON djd.ID_JENIS_DAK=sp.kd_subbidang
            left JOIN sinkronisasi_approval sa ON sa.kdkabupaten=sp.kdkabupaten and sa.kdprovinsi=sp.kdprovinsi and sa.kd_subbidang=sp.kd_subbidang and sa.kd_menu=sp.kd_menu and sa.kd_rincian=sp.kd_rincian
             $where
            ";
                return $this->db->query($sql);
    }public function getDatalaporanAwalsub($ks='',$thn='')
    {
       $sql=" SELECT
        sp.*,
        sa.id_drk as idkrisna,
        sa.p_volume,
        sa.p_cost,
        sa.p_nilai,
        sa.aproval_kr,
        sa.catatan_kr,
        sa.aproval_unit,
        sa.catatan,
        sa.aproval_pi,
        sa.catatan_pi,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input_awal sa ON sa.id_drk=sp.id_drk
        WHERE sp.kd_subbidang=$ks and  sp.tahun=$thn AND sp.approval_sum !='2'
        ORDER by sp.provinsi_nama,sp.pengusul_nama,sp.kd_menu,sp.kd_rincian ASC";
                return $this->db->query($sql);
    }

    public function getDatalaporanAwalsub_sinkron($ks='',$thn='')
    {
       $sql=" SELECT
        sp.*,
        sa.id_drk as idkrisna,
        sa.p_volume,
        sa.p_cost,
        sa.p_nilai,
        sa.aproval_kr,
        sa.catatan_kr,
        sa.aproval_unit,
        sa.catatan,
        sa.aproval_pi,
        sa.catatan_pi,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input sa ON sa.id_drk=sp.id_drk
        WHERE sp.kd_subbidang=$ks and  sa.tahun=$thn AND sp.approval_sum !='2'
        ORDER by sp.provinsi_nama,sp.pengusul_nama,sp.kd_menu,sp.kd_rincian ASC";
                return $this->db->query($sql);
    }

     public function getDatalaporanAwalsub_sinkron_2($ks='',$thn='',$p='')
    {
       $sql=" SELECT
        sp.*,
        sa.id_drk as idkrisna,
        sa.p_volume,
        sa.p_cost,
        sa.p_nilai,
        sa.aproval_kr,
        sa.catatan_kr,
        sa.aproval_unit,
        sa.catatan,
        sa.aproval_pi,
        sa.catatan_pi,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input sa ON sa.id_drk=sp.id_drk
        WHERE sp.kd_subbidang=$ks and  sa.tahun=$thn AND sp.approval_sum !='2' AND sp.prioritas_kegiatan='$p'
        ORDER by sp.provinsi_nama,sp.pengusul_nama,sp.kd_menu,sp.kd_rincian ASC";
                return $this->db->query($sql);
    }

    public function getDatalaporanAwalforkrisna($thn='')
    {
       $sql="
            SELECT
        sp.id_drk as id_detail_rincian,
        sa.aproval_pi AS approval_kl,
        sa.p_volume as volume_kl,
        sa.p_cost AS unit_cost_kl,
        sa.catatan_pi AS note_kl,
        sa.rincian_penilaian,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input_awal sa ON sa.id_drk=sp.id_drk
        WHERE sp.tahun=2022 AND sp.approval_sum !='2' 
        ORDER by sa.id_drk ASC
         ";
        return $this->db->query($sql);
    }

    public function getDatalaporanAwalforkrisna3($thn='')
    {
       $sql="
            SELECT
        sp.id_drk as id_detail_rincian,
        sa.aproval_pi AS approval_kl,
        sa.p_volume as volume_kl,
        sa.p_cost AS unit_cost_kl,
        sa.catatan_pi AS note_kl,
        sa.rincian_penilaian,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input sa ON sa.id_drk=sp.id_drk
        WHERE sa.tahun=2022 
        AND sp.approval_sum !='2' 
        AND sp.approval_awal !='2'
        ORDER by sa.id_drk ASC
         ";
        return $this->db->query($sql);
    }

     public function getDatalaporanAwalforkrisna_coba($thn='')
    {
       $sql="
            SELECT
        sp.id_drk as id_detail_rincian,
        sa.aproval_pi AS approval_kl,
        sa.p_volume as volume_kl,
        sa.p_cost AS unit_cost_kl,
        sa.catatan_pi AS note_kl,
        sa.rincian_penilaian,
        sa.array_aspak,
        sa.array_usulan
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input sa ON sa.id_drk=sp.id_drk
        WHERE sa.tahun=2022 
        AND sp.approval_sum !='2' 
        -- AND sp.kd_unit ='04' 
        AND sp.approval_awal !='2'
        AND sp.id_drk='394073'
        ORDER by sa.id_drk ASC
        LIMIT 0,50
         ";
        return $this->db->query($sql);
    }

    public function getDatalaporanAwalforkrisna2($thn='')
    {
       $sql="
            SELECT
        sp.id_drk as id_detail_rincian,
        sa.aproval_pi AS approval_kl,
        sa.p_volume as volume_kl,
        sa.p_cost AS unit_cost_kl,
        sa.catatan_pi AS note_kl,
        sa.array_aspak,
        sa.array_usulan,
        sa.rincian_penilaian
        from singkronisasi_fisik_2021 sp
        LEFT JOIN sinkronisasi_input_awal_copy1 sa ON sa.id_drk=sp.id_drk
        WHERE sp.tahun=2022 AND sp.approval_sum !='2' and sa.aproval_pi !='0' and (sa.id_drk !='0' or sa.id_drk !=null)
        ORDER by sa.id_drk ASC
         ";
        return $this->db->query($sql);
    }public function getlistdatadakungsinkron($kk='',$kp='',$ks='',$thn='')
    {
            $sql=" 
                SELECT
                sp.kdkabupaten,
                sp.kdprovinsi,
                sp.kdjenis,
                sp.kd_subbidang,
                sp.pengusul_nama,
                sp.provinsi_nama,
                sp.sub_bidang,
                sp.detail_rincian,
                sp.subtopik
                from 
                singkronisasi_fisik_2021 sp
                WHERE sp.tahun=$thn 
                AND  sp.kdkabupaten='$kk' AND sp.kdprovinsi='$kp'  AND sp.kd_subbidang='$ks'
                GROUP BY sp.detail_rincian";
        return $this->db->query($sql);
    }public function getlistdatadakungsinkron1($kk='',$kp='',$ks='',$thn='')
    {
        $sql=" SELECT
                sp.kdkabupaten,
                sp.kdprovinsi,
                sp.kdjenis,
                sp.kd_subbidang,
                sp.pengusul_nama,
                sp.provinsi_nama,
                sp.sub_bidang,
                sp.kd_subbidang,
                sp.menu,
                sp.kd_menu,
                sp.detail_rincian,
                sp.subtopik
                from 
                singkronisasi_fisik_2021 sp
                WHERE sp.tahun=$thn 
                AND  sp.kdkabupaten='$kk' AND sp.kdprovinsi='$kp'  AND sp.kd_subbidang='$ks' and ( sp.subtopik !='Puskesmas' and sp.subtopik is not null)
                GROUP BY sp.detail_rincian,sp.menu";
        return $this->db->query($sql);
    }public function getlistdatadakungsinkron2($kk='',$kp='',$ks='',$thn='')
    {
        $sql="  SELECT
                sp.kdkabupaten,
                sp.kdprovinsi,
                sp.kdjenis,
                sp.kd_subbidang,
                sp.pengusul_nama,
                sp.provinsi_nama,
                sp.sub_bidang,
                sp.kd_subbidang,
                sp.menu,
                sp.kd_menu,
                sp.detail_rincian,
                'Dinas Kesehatan' as subtopik
                from 
                singkronisasi_fisik_2021 sp
                WHERE sp.tahun=$thn 
                AND  sp.kdkabupaten='$kk' AND sp.kdprovinsi='$kp'  AND sp.kd_subbidang='$ks' and (sp.subtopik='Puskesmas' or sp.subtopik is null)
                GROUP BY sp.menu";
        return $this->db->query($sql);
    }

    public function getlistdatadakungsinkron1_new($ks='',$thn='')
    {
        $sql=" SELECT
                sp.kdkabupaten,
                sp.kdprovinsi,
                sp.kdjenis,
                sp.kd_subbidang,
                sp.pengusul_nama,
                sp.provinsi_nama,
                sp.sub_bidang,
                sp.kd_subbidang,
                sp.menu,
                sp.kd_menu,
                sp.detail_rincian,
                sp.subtopik
                from 
                singkronisasi_fisik_2021 sp
                WHERE sp.tahun=$thn 
                AND   sp.kd_subbidang='$ks' and ( sp.subtopik !='Puskesmas' and sp.subtopik is not null)
                GROUP BY sp.detail_rincian,sp.menu";
        return $this->db->query($sql);
    }public function getlistdatadakungsinkron2_new($ks='',$thn='')
    {
        $sql="  SELECT
                sp.kdkabupaten,
                sp.kdprovinsi,
                sp.kdjenis,
                sp.kd_subbidang,
                sp.pengusul_nama,
                sp.provinsi_nama,
                sp.sub_bidang,
                sp.kd_subbidang,
                sp.menu,
                sp.kd_menu,
                sp.detail_rincian,
                'Dinas Kesehatan' as subtopik
                from 
                singkronisasi_fisik_2021 sp
                WHERE sp.tahun=$thn 
                AND   sp.kd_subbidang='$ks' and (sp.subtopik='Puskesmas' or sp.subtopik is null)
                GROUP BY sp.menu";
        return $this->db->query($sql);
    }

    public function getdatamonevdakcovid($kat='',$thn='')
    {
        $sql="SELECT
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kategori,
                bp.tahun_anggaran,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                SUM(jumlah) as pagu,
                (SELECT sum(pagu) from budget_nf_pagu_tch bt
                WHERE bt.tahun='$thn'   and bt.id_kategori='$kat' and bt.KodeProvinsi=bp.kdprovinsi AND bt.KodeKabupaten=bp.kdkabupaten) as pagu_perpres,
                (SELECT
                sum(nilai_alokasi)
                from budgetdaknf_pengajuan bd
                INNER JOIN budgetdaknf_revisirk_data bdt ON bdt.id_pengajuan_rk=bd.id_pengajuan
                INNER JOIN budgetdaknf_revisirk_menu bm ON bm.id=bdt.id_revisi_menu 
                where   bd.tahun_anggaran='$thn' and bd.kategori='$kat' and bm.`group`='1' and bd.kdprovinsi=bp.kdprovinsi and bd.kdkabupaten=bp.kdkabupaten) as covid,
                (SELECT
                sum(nilai_alokasi)
                from budgetdaknf_pengajuan bd
                INNER JOIN budgetdaknf_revisirk_data bdt ON bdt.id_pengajuan_rk=bd.id_pengajuan
                INNER JOIN budgetdaknf_revisirk_menu bm ON bm.id=bdt.id_revisi_menu 
                where   bd.tahun_anggaran='$thn' and bd.kategori='$kat' and bm.`group`='2' and bd.kdprovinsi=bp.kdprovinsi and bd.kdkabupaten=bp.kdkabupaten) as esential,
                (SELECT
                sum(nilai_alokasi)
                from budgetdaknf_pengajuan bd
                INNER JOIN budgetdaknf_revisirk_data bdt ON bdt.id_pengajuan_rk=bd.id_pengajuan
                INNER JOIN budgetdaknf_revisirk_menu bm ON bm.id=bdt.id_revisi_menu 
                where   bd.tahun_anggaran='$thn' and bd.kategori='$kat'  and bd.kdprovinsi=bp.kdprovinsi and bd.kdkabupaten=bp.kdkabupaten) as totalcovid
                from 
                budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                INNER JOIN ref_kabupaten rk ON bp.kdkabupaten=rk.kodekabupaten AND bp.kdprovinsi =rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                where   bp.tahun_anggaran='$thn' and bp.kategori='$kat'
                GROUP BY bp.kdprovinsi,bp.kdkabupaten
                ORDER BY rp.NamaProvinsi,rk.NamaKabupaten asc";
        return $this->db->query($sql);
    }public function getDatasubbidangadvokasi($kp='',$kk='',$thn='')
    {
        $sql="SELECT
                sa.pengusul_nama,
                sa.sub_bidang,
                sum(sp.nilai) as jml
                from singkronisasi_fisik_advokasi sa
                INNER JOIN sinkronisasi_pernyataan sp ON sp.id_drk=sa.id_drk
                WHERE sa.kdkabupaten=$kk AND sa.kdprovinsi=$kp   AND sa.tahun=$thn
                GROUP BY sa.kdprovinsi,sa.kdkabupaten,sa.kd_subbidang";
        return $this->db->query($sql);
    }public function getDatasubbidangadvokasi_tambahan($value='')
    {
        $sql="SELECT
                sa.pengusul_nama,
                sa.menu as sub_bidang,
                sum(sa.nilai) as jml
                from singkronisasi_fisik_tambahan sa
                WHERE sa.kdkabupaten=19 AND sa.kdprovinsi=03   AND sa.tahun=2022
                GROUP BY sa.kdprovinsi,sa.kdkabupaten,sa.menu";
        return $this->db->query($sql);
    }public function getnamakepaladinas_tambahan($kp='',$kk='')
    {
        $sql="SELECT 
                nama_kd as nama_kd,
                jabatan_kd as jabatan_kd
                from
                sinkronisasi_pernyataan_file sa
                WHERE sa.kdkabupaten=$kk AND sa.kdprovinsi=$kp";
        return $this->db->query($sql); 
    }public function getNilaiper_submenu($kp='',$kk='',$kat='',$thn='',$menu='')
    {
        $sql=" SELECT
                SUM(dnr.jumlah) AS jml
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_menu dnm ON pr.kategori=dnm.id_kategori_nf and dnm.id_menu=dnr.kode_menu
                WHERE pr.kdprovinsi='$kp' and pr.kdkabupaten='$kk' and pr.kategori='$kat' and pr.tahun_anggaran='$thn'
                and dnm.menu='$menu'";
        return $this->db->query($sql); 
    }

    public function getNilaiper_submenu2($kp='',$kk='',$kat='',$thn='',$menu='')
    {
        $sql=" SELECT
                SUM(dnr.jumlah) AS jml
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_menu dnm ON pr.kategori=dnm.id_kategori_nf and dnm.id_menu=dnr.kode_menu
                WHERE pr.kdprovinsi='$kp' and pr.kdkabupaten='$kk' and pr.kategori='$kat' and pr.tahun_anggaran='$thn'
                and dnm.ID_PENGELOMPOKAN='$menu'";
        return $this->db->query($sql); 
    }

    public function getNilaikomponen($kp='',$kk='',$kat='',$menu='',$komponen='')
    {
        $sql="  SELECT
                sum(jumlah) as jml,
                sum(dak) as dak
                from data_nf_rka_komponen_puskesmas
                WHERE kdprovinsi='$kp' and kdkabupaten='$kk' and id_kategori_nf='$kat' and id_menu='$menu' and id_komponen='$komponen'";
                return $this->db->query($sql); 
    }

    public function getNilaiMenu($kp='',$kk='',$kat='',$menu='')
    {
        $sql="  SELECT
                sum(jumlah) as jml
                from data_nf_rka_komponen_puskesmas
                WHERE kdprovinsi='$kp' and kdkabupaten='$kk' and id_kategori_nf='$kat' and id_menu='$menu'";
                return $this->db->query($sql); 
    }

     public function getNilaiMenu_tanpagizi($kp='',$kk='',$kat='',$menu='')
    {
        $sql="  SELECT
                sum(dak) as jml
                from data_nf_rka_komponen_puskesmas
                WHERE kdprovinsi='$kp' and kdkabupaten='$kk' and id_kategori_nf='$kat' and id_menu='$menu'
                and id_komponen !='85' and id_komponen !='155' ";
                return $this->db->query($sql); 
    }

     public function getNilaiallMenu($kp='',$kk='',$kat='',$menu='')
    {
        $sql="  SELECT
                sum(dp.jumlah) as jml
                from data_nf_rka_komponen_puskesmas dp
                INNER JOIN dak_nf_menu dm ON dp.id_menu=dm.id
                WHERE dp.kdprovinsi='$kp' and dp.kdkabupaten='$kk' and dp.id_kategori_nf='$kat' and dm.menu='$menu'";
                return $this->db->query($sql); 
    }

     public function getNilaiallMenu2($kp='',$kk='',$kat='',$menu='')
    {
        $sql="  SELECT
                sum(dp.jumlah) as jml
                from data_nf_rka_komponen_puskesmas dp
                INNER JOIN dak_nf_menu dm ON dp.id_menu=dm.id
                WHERE dp.kdprovinsi='$kp' and dp.kdkabupaten='$kk' and dp.id_kategori_nf='$kat' and dm.ID_PENGELOMPOKAN='$menu'";
                return $this->db->query($sql); 
    }
    public function listusulan2022($tahun='')
    {
        $sql=" SELECT *
                FROM
                (
                SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                da.alokasi,
                (SELECT SUM(dr.jumlah) from pengajuan_rka_nf pr
                INNER JOIN  data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and pr.kdkabupaten=da.kdkabupaten and pr.kategori=da.id_kategori
                GROUP BY pr.kdprovinsi,pr.kdkabupaten,pr.kategori ) as usulan
                from dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.id_kategori !='47' AND da.tahun='$tahun'
                ORDER BY rp.NamaProvinsi,
                rk.kodekabupaten,
                dk.nama_kategori asc) as qty
                WHERE qty.usulan IS NOT NULL AND qty.usulan !='0'";
                        return $this->db->query($sql); 
    }
    public function listusulan2022_old($tahun='')
    {
        $sql="  SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.tanggal_update,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                da.alokasi,
                SUM(dr.jumlah) AS usulan
                FROM dak_nf_alokasi da
                INNER JOIN pengajuan_rka_nf pr ON pr.kdprovinsi=da.kdprovinsi and pr.kdkabupaten=da.kdkabupaten and pr.kategori=da.id_kategori and pr.tahun_anggaran=da.tahun
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.tahun='$tahun'
                GROUP BY da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun
                ORDER BY rp.NamaProvinsi,
                da.kdkabupaten,
                da.id_kategori";
        return $this->db->query($sql); 
    }public function listusulan2022_2($tahun='')
    {
        $sql="  SELECT *
                FROM
                (
                SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                da.alokasi,
                (SELECT SUM(dr.jumlah) from pengajuan_rka_nf pr
                INNER JOIN  data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and pr.kdkabupaten=da.kdkabupaten and pr.kategori=da.id_kategori
                GROUP BY pr.kdprovinsi,pr.kdkabupaten,pr.kategori ) as usulan
                from dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.id_kategori !='47' AND da.tahun='$tahun'
                ORDER BY rp.NamaProvinsi,
                rk.kodekabupaten,
                dk.nama_kategori asc) as qty
                WHERE qty.usulan IS NULL or qty.usulan='0'
                ";
        return $this->db->query($sql);
    }


    public function listusulan2022_2_old($tahun='')
    {
        $sql="  SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                pr.id_pengajuan,
                pr.tanggal_pembuatan,
                pr.tanggal_update,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                da.alokasi,
                SUM(dr.jumlah) AS usulan
                FROM dak_nf_alokasi da
                LEFT JOIN pengajuan_rka_nf pr ON pr.kdprovinsi=da.kdprovinsi and pr.kdkabupaten=da.kdkabupaten and pr.kategori=da.id_kategori and pr.tahun_anggaran=da.tahun
                LEFT JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.tahun='$tahun' and pr.id_pengajuan IS NULL
                GROUP BY da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun
                ORDER BY rp.NamaProvinsi,
                da.kdkabupaten,
                da.id_kategori";
        return $this->db->query($sql); 
    }public function getnilaidalamkomponen($menu='',$kp='',$kk='',$kat='')
    {
        $sql=" SELECT
                dp.*,
                dr.jumlah,
                (SELECT  sum(jumlah) as jml   from data_nf_rka_komponen_puskesmas ds
                WHERE dr.kdprovinsi=ds.kdprovinsi and dr.kdkabupaten=ds.kdkabupaten and dr.id_kategori_nf=ds.id_kategori_nf AND dr.id_menu=ds.id_menu and ds.id_komponen=dr.id_komponen) as jumlahnya
                from dak_nf_komponen dp
                INNER JOIN data_nf_rka_komponen dr ON dr.id_menu=dp.id_kegiatan and dr.id_komponen=dp.id
                WHERE dp.id_kegiatan='$menu' and dr.kdprovinsi='$kp' and dr.kdkabupaten='$kk' and dr.id_kategori_nf='$kat'";
        return $this->db->query($sql); 
    }public function getnilaidalammenu($tahun='',$klp='',$kp='',$kk='',$kat='')
    {
         $sql=" SELECT dm.*,
                (SELECT
                sum(jumlah) as jml
                from data_nf_rka_komponen_puskesmas dp
                WHERE dp.kdprovinsi='$kp' and dp.kdkabupaten='$kk' and dp.id_kategori_nf='$kat' and id_menu=dm.id) AS jumlahnya
                from dak_nf_menu dm 
                WHERE dm.id_kategori_nf='$kat' and dm.TAHUN='$tahun' and dm.ID_PENGELOMPOKAN='$klp'";
                
        return $this->db->query($sql); 
    }public function  getnilaidalamkomponen2($menu='',$kp='',$kk='',$kat='')
    {
        $sql="SELECT 
        dp.*,
         (SELECT
               sum(jumlah) as jml
                from data_nf_rka_komponen_puskesmas ds
                WHERE ds.kdprovinsi='$kp' and ds.kdkabupaten='$kk' and ds.id_kategori_nf=dp.id_kategori_nf and ds.id_menu=dp.id_kegiatan and ds.id_komponen=dp.id) as jumlahnya
        FROM
        dak_nf_komponen dp
        WHERE 
        dp.id_kegiatan='$menu' and dp.id_kategori_nf='$kat' ";
                
        return $this->db->query($sql); 
    }public function getDaftaralokasiprovinsi($value='')
    {
        $sql="SELECT
            rk.* ,
            (SELECT
            COUNT(da.id)
            from dak_nf_alokasi da
            WHERE da.kdprovinsi=rk.KodeProvinsi AND da.id_kategori !='47') AS jml_alokasi
            from ref_kabupaten rk
            WHERE rk.KodeKabupaten='00'";
                
        return $this->db->query($sql); 
    }public function getDaftaralokasikabupaten($kp='')
    {
        $sql="
            SELECT
            rk.* ,
            (SELECT
            COUNT(da.id)
            from dak_nf_alokasi da
            WHERE da.kdprovinsi=rk.KodeProvinsi AND da.kdkabupaten=rk.KodeKabupaten AND da.id_kategori !='47') AS jml_alokasi
            from ref_kabupaten rk
            WHERE rk.KodeProvinsi='$kp'";
                
        return $this->db->query($sql); 
    }

    public function getcountalokasiperprovinsi($tahun='',$kp='')
    {
        $sql="  SELECT
                COUNT(rty.pengajuan) AS jml
                from
                (
                SELECT pr.id_pengajuan as pengajuan FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                WHERE pr.tahun_anggaran='$tahun' and pr.kdprovinsi='$kp'
                GROUP BY pr.id_pengajuan) as rty";
                
        return $this->db->query($sql); 
    }

    public function getcountalokasiperkab($tahun='',$kp='',$kk='')
    {
        $sql="  SELECT
                COUNT(rty.pengajuan) AS jml
                from
                (
                SELECT pr.id_pengajuan as pengajuan FROM pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                WHERE pr.tahun_anggaran='$tahun' and pr.kdprovinsi='$kp' and pr.kdkabupaten='$kk'
                GROUP BY pr.id_pengajuan) as rty";
                
        return $this->db->query($sql); 
    }public function getDaftaralokasikabupatenall($value='')
    {
        $sql=" 
            SELECT
            rk.* ,
            rp.NamaProvinsi,
            (SELECT
            COUNT(da.id)
            from dak_nf_alokasi da
            WHERE da.kdprovinsi=rk.KodeProvinsi AND da.kdkabupaten=rk.KodeKabupaten AND da.id_kategori !='47') AS jml_alokasi
            from ref_kabupaten rk
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            ORDER BY rp.NamaProvinsi,rk.KodeKabupaten ";
                
        return $this->db->query($sql); 
    }public function getListbudgeting_nf($tahun='',$kp='',$kk='')
    {
          $sql="SELECT
                da.*,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                (SELECT
                sum(jumlah)
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and da.kdkabupaten=pr.kdkabupaten and da.id_kategori=pr.kategori ) as usulan
                FROM dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.kdprovinsi='$kp' and da.kdkabupaten='$kk' and da.tahun='$tahun'";       
        return $this->db->query($sql); 
    }

    public function getListbudgeting_nf_Pagu($tahun='',$kp='',$kk='')
    {
          $sql="SELECT
                da.*,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                (SELECT
                sum(jumlah)
                from budgetdaknf_pengajuan pr
                INNER JOIN budgetdaknf_data dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and da.kdkabupaten=pr.kdkabupaten and da.id_kategori=pr.kategori ) as usulan
                FROM dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.kdprovinsi='$kp' and da.kdkabupaten='$kk' and da.tahun='$tahun'";       
        return $this->db->query($sql); 
    }public function nilai_usulanRK($tahun='',$kp='',$kk='',$kat='')
    {
        $sql="  SELECT
                SUM(dnr.jumlah) jumlah
                from budgetdaknf_pengajuan pr
                INNER JOIN budgetdaknf_data dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=$kp and pr.kdkabupaten=$kk and pr.kategori=$kat AND pr.tahun_anggaran=$tahun ";       
        return $this->db->query($sql); 
    }
    public function getListbudgeting_nf2($tahun='',$kp='',$kk='',$kat='')
    {
          $sql="SELECT
                da.*,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                (SELECT
                sum(jumlah)
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and da.kdkabupaten=pr.kdkabupaten and da.id_kategori=pr.kategori ) as usulan
                FROM dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                WHERE da.kdprovinsi='$kp' and da.kdkabupaten='$kk' and da.id_kategori='$kat' and da.tahun='$tahun'";       
        return $this->db->query($sql); 
    }public function getnilaiRkperpuskesmas($kdprovinsi='',$kdkabupaten='',$kdpuskesmas='')
    {
        $sql="  SELECT
                dp.*,
                dm.nama_menu,
                dm.menu,
                 dnk.komponen,
                dk.nama_kategori
                from data_nf_rka_komponen_puskesmas dp
                INNER JOIN dak_nf_menu dm ON dp.id_menu=dm.id
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=dp.id_kategori_nf
                INNER JOIN dak_nf_komponen dnk ON dnk.id = dp.id_komponen
                WHERE kdprovinsi='$kdprovinsi' and kdkabupaten='$kdkabupaten'
                and id_puskesmas='$kdpuskesmas'
                ORDER BY dm.ID_PENGELOMPOKAN,dm.nama_menu,dnk.id";       
        return $this->db->query($sql); 
    }public function getAllrekapusulan_2022($tahun='')
    {
       $sql="SELECT
            da.kdprovinsi,
            da.kdkabupaten,
            da.id_kategori,
            da.tahun,
            rp.NamaProvinsi,
            rk.NamaKabupaten,
            dk.nama_kategori,
            da.alokasi
            from dak_nf_alokasi da
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
            INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            WHERE da.id_kategori ='42' AND da.tahun='$tahun' 
            ORDER BY rp.NamaProvinsi,
            rk.kodekabupaten,
            dk.nama_kategori asc";       
        return $this->db->query($sql); 
    }public function getJMLSIGNATUR($tahun='',$kp='')
    {
        $sql="  SELECT
                qty.kdprovinsi,
                sum(qty.jml) as jumlah
                FROM 
                (
                SELECT
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kategori,
                (SELECT COUNT(DISTINCT bs.id_pengajuan) FROM budgeting_signature bs
                WHERE bs.id_pengajuan=bp.id_pengajuan and bs.kode_signature='0') as jml
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd on bp.id_pengajuan=bd.id_pengajuan 
                WHERE bp.tahun_anggaran='$tahun' and  bp.kdprovinsi='$kp'
                GROUP BY bp.id_pengajuan) AS qty
                GROUP BY qty.kdprovinsi";       
        return $this->db->query($sql); 
    }
    public function getJMLSIGNATU2($tahun='',$kp='',$kk='')
    {
        $sql="  SELECT
                qty.kdprovinsi,
                sum(qty.jml) as jumlah
                FROM 
                (
                SELECT
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kategori,
                (SELECT COUNT(DISTINCT bs.id_pengajuan) FROM budgeting_signature bs
                WHERE bs.id_pengajuan=bp.id_pengajuan and bs.kode_signature='0') as jml
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd on bp.id_pengajuan=bd.id_pengajuan 
                WHERE bp.tahun_anggaran='$tahun' and  bp.kdprovinsi='$kp' and  bp.kdkabupaten='$kk'
                GROUP BY bp.id_pengajuan) AS qty
                GROUP BY qty.kdprovinsi";       
        return $this->db->query($sql); 
    }

    public function getJMLSIGNATU3($tahun='',$kp='',$kk='',$kat='')
    {
        $sql="  SELECT
                qty.kdprovinsi,
                sum(qty.jml) as jumlah
                FROM 
                (
                SELECT
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kategori,
                (SELECT COUNT(id_signature) FROM budgeting_signature bs
                WHERE bs.id_pengajuan=bp.id_pengajuan and bs.kode_signature='0') as jml
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd on bp.id_pengajuan=bd.id_pengajuan 
                WHERE bp.tahun_anggaran='$tahun' and  bp.kdprovinsi='$kp' and  bp.kdkabupaten='$kk'  and  bp.kategori='$kat'
                GROUP BY bp.id_pengajuan) AS qty
                GROUP BY qty.kdprovinsi";       
        return $this->db->query($sql); 
    }public function getJMLSIGNATU4($tahun='',$kp='',$kk='',$kat='',$kode='')
    {
        $sql="  SELECT
                qty.kdprovinsi,
                sum(qty.jml) as jumlah
                FROM 
                (
                SELECT
                bp.kdprovinsi,
                bp.kdkabupaten,
                bp.kategori,
                (SELECT COUNT(id_signature) FROM budgeting_signature bs
                WHERE bs.id_pengajuan=bp.id_pengajuan and bs.kode_signature='$kode' and bs.tahun='$tahun') as jml
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd on bp.id_pengajuan=bd.id_pengajuan 
                WHERE bp.tahun_anggaran='$tahun' and  bp.kdprovinsi='$kp' and  bp.kdkabupaten='$kk'  and  bp.kategori='$kat'
                GROUP BY bp.id_pengajuan) AS qty
                GROUP BY qty.kdprovinsi";       
        return $this->db->query($sql); 
    }

    public function getKomfimasibudgeting($tahun='',$kp='',$kk='',$kat='',$kdmenu='')
    {
        $sql="  SELECT
                bp.id_pengajuan,
                bd.jumlah,
                bd.dak,
                bd.komfirmasi
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd on bp.id_pengajuan=bd.id_pengajuan
                WHERE bp.tahun_anggaran='$tahun' and  bp.kdprovinsi='$kp' AND bp.kdkabupaten='$kk' and kategori='$kat'
                and bd.kode_menu='$kdmenu'";       
        return $this->db->query($sql); 
    }public function getJMLusulan_prov($tahun='',$kp='')
    {
         $sql=" SELECT
                COUNT(QTY.id_pengajuan) AS jml
                FROM 
                (
                SELECT
                pr.id_pengajuan,
                pr.kdprovinsi,pr.kdkabupaten,pr.kategori,
                SUM(dnr.dak) as dak
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.tahun_anggaran='$tahun' and pr.kdprovinsi='$kp'
                GROUP BY pr.id_pengajuan ) AS QTY
                WHERE QTY.dak !='0'";       
        return $this->db->query($sql); 
    }
    public function getJMLusulan_prov2($tahun='',$kp='',$kk='')
    {
         $sql=" SELECT
                COUNT(QTY.id_pengajuan) AS jml
                FROM 
                (
                SELECT
                pr.id_pengajuan,
                pr.kdprovinsi,pr.kdkabupaten,pr.kategori,
                SUM(dnr.dak) as dak
                from pengajuan_rka_nf pr
                INNER JOIN data_nf_rka dnr ON dnr.id_pengajuan=pr.id_pengajuan
                WHERE pr.tahun_anggaran='$tahun' and pr.kdprovinsi='$kp'  and pr.kdkabupaten='$kk'
                GROUP BY pr.id_pengajuan ) AS QTY
                WHERE QTY.dak !='0'";       
        return $this->db->query($sql); 
    }public function cekMonevinputan($kp='',$kk='',$namakat='',$thn='')
    {
         $sql=" 
                SELECT
                count(pmd.id_pengajuan) as jml from 
                dak_nf_laporan pmd
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=pmd.id_kategori_nf
               WHERE pmd.kodeprovinsi='$kp' And pmd.kodekabupaten='$kk' and pmd.waktu_laporan='3'and dk.nama_kategori='$namakat' and pmd.TAHUN_ANGGARAN='$thn'";       
        return $this->db->query($sql); 
    }public function cekBudgetinputan($kp='',$kk='',$namakat='',$thn='')
    {
         $sql=" SELECT
                COUNT(bp.id_pengajuan) as jml
                FROM budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
                WHERE  bp.kdprovinsi='$kp' and bp.kdkabupaten='$kk' and bp.tahun_anggaran='$thn' and dk.nama_kategori='$namakat'";       
        return $this->db->query($sql); 
    }public function cekBudgetinputan2($kp='',$kk='',$namakat='',$thn='')
    {
         $sql=" SELECT
                COUNT(bp.id_pengajuan) as jml
                FROM budgetdaknf_revisirk_pengajuan bp
                INNER JOIN budgetdaknf_revisirk_data bd ON bd.id_pengajuan=bp.id_pengajuan
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=bp.kategori
                WHERE  bp.kdprovinsi='$kp' and bp.kdkabupaten='$kk' and bp.tahun_anggaran='$thn' and dk.nama_kategori='$namakat'";       
        return $this->db->query($sql); 
    }

    public function lapBudgetperkategori($where='')
    {
        $sql=" SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                da.alokasi,
                (SELECT SUM(dr.jumlah) from pengajuan_rka_nf pr
                INNER JOIN  data_nf_rka dr ON dr.id_pengajuan=pr.id_pengajuan
                WHERE pr.kdprovinsi=da.kdprovinsi and pr.kdkabupaten=da.kdkabupaten and pr.kategori=da.id_kategori
                GROUP BY pr.kdprovinsi,pr.kdkabupaten,pr.kategori ) as usulan
                from dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                $where
                ORDER BY rp.NamaProvinsi,
                rk.kodekabupaten,
                dk.nama_kategori asc";
        return $this->db->query($sql); 
    }public function lapBudgetperkatekegiatan($where='')
    {
       $sql="   SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                da.alokasi,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.id,
                dm.menu,
                dm.ID_PENGELOMPOKAN
                from dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_menu dm ON dm.id_kategori_nf=da.id_kategori
                $where 
                GROUP BY
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                dm.ID_PENGELOMPOKAN
                ORDER BY 
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.ID_PENGELOMPOKAN";
        return $this->db->query($sql); 
    }public function dakBudgetingkelompok($kp='',$kk='',$kat='',$thn='',$kel='')
    {
         $sql=" SELECT
                SUM(bd.dak) as jml
                from budgetdaknf_pengajuan bp
                INNER JOIN budgetdaknf_data bd ON bd.id_pengajuan=bp.id_pengajuan
                INNER JOIN dak_nf_menu dm ON dm.id_kategori_nf=bp.kategori and dm.id_menu=bd.kode_menu
                WHERE bp.kdprovinsi='$kp' and bp.kdkabupaten='$kk' and bp.tahun_anggaran='$thn' and
                bp.kategori='$kat' 
                and bd.komfirmasi='3' 
                and dm.ID_PENGELOMPOKAN='$kel'";
                        return $this->db->query($sql); 
    }public function lapBudgetperRinciankegiatan($where='')
    {
       $sql="SELECT
            da.kdprovinsi,
            da.kdkabupaten,
            da.id_kategori,
            da.tahun,
            da.alokasi,
            rp.NamaProvinsi,
            rk.NamaKabupaten,
            dk.nama_kategori,
            dm.id,
            dm.id_menu,
            dm.menu,
            dm.nama_menu
            from dak_nf_alokasi da
            INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
            INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
            INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
            INNER JOIN dak_nf_menu dm ON dm.id_kategori_nf=da.id_kategori
            $where
            ORDER BY 
            rp.NamaProvinsi,
            rk.NamaKabupaten,
            dk.nama_kategori,
            dm.ID_PENGELOMPOKAN,
            dm.id_menu";
        return $this->db->query($sql); 
    }public function lapBudgetperKomponen($where='')
    {
        $sql=" SELECT
                da.kdprovinsi,
                da.kdkabupaten,
                da.id_kategori,
                da.tahun,
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.id,
                dm.id_menu,
                dm.nama_menu,
                dm.menu,
                dnk.id AS id_komponen,
                dnk.komponen
                from dak_nf_alokasi da
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_menu dm ON dm.id_kategori_nf=da.id_kategori
                INNER JOIN dak_nf_komponen dnk ON dnk.id_kegiatan=dm.id
                $where
                ORDER BY 
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.ID_PENGELOMPOKAN,
                dm.id_menu,
                dnk.id";
        return $this->db->query($sql); 

    }public function lapBudgetperPuskesmas($where='')
    {
        $sql=" SELECT
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.id_menu as menu_id,
                dm.nama_menu,
                dm.menu,
                dnk.id as komponen_id,
                dnk.komponen,
                da.*
                from data_nf_rka_komponen_puskesmas da
                INNER JOIN ref_kabupaten rk ON da.kdkabupaten=rk.kodekabupaten AND da.kdprovinsi=rk.kodeprovinsi
                INNER JOIN ref_provinsi rp ON rp.kodeprovinsi=rk.kodeprovinsi
                INNER JOIN dak_nf_kategori dk ON dk.id_kategori_nf=da.id_kategori_nf
                INNER JOIN dak_nf_menu dm ON dm.id=da.id_menu
                INNER JOIN dak_nf_komponen dnk ON dnk.id=da.id_komponen
                $where
                ORDER BY 
                rp.NamaProvinsi,
                rk.NamaKabupaten,
                dk.nama_kategori,
                dm.ID_PENGELOMPOKAN,
                dm.id_menu,
                dnk.id";
        return $this->db->query($sql); 
    }public function getlistAlokasinull($kp='',$kk='',$kat='',$thn='')
    {
       $sql="       SELECT
                    dam.*,
                    dm.id_menu AS kod_menu,
                    dm.nama_menu
                    from dak_nf_alokasi_menu dam
                    INNER JOIN dak_nf_menu dm ON dam.id_menu=dm.id
                    WHERE dam.tahun='$thn' and dam.id_menu !='0'
                    and dam.kdprovinsi='$kp'
                    and dam.kdkabupaten='$kk'
                    and dam.id_kategori='$kat'";
        return $this->db->query($sql); 
    }public function sumRealisasimonev($kodemenu='')
    {
       $sql="       SELECT 
                        SUM(realisasi) AS jml,
                        sum(fisik) as  t_fisik,
                        sum(volume_ril) as  t_volume,
                        sum(volume_k) as  t_volume_k,
                        sum(realisasi_k) as  t_realisasi_k
                    from data_monev_rka_2020 dm
                    INNER JOIN pengajuan_monev_dak pm ON pm.id_pengajuan=dm.id_pengajuan
                    where dm.kode_menu='$kodemenu'";
        return $this->db->query($sql); 
    }public function get_nilai_monev_komponen($kp='',$kk='',$kt='')
    {
        $sql="  SELECT
                SUM(triwulan1) as tw_1,
                SUM(triwulan2) as tw_2,
                SUM(triwulan3) as tw_3,
                SUM(triwulan4) as tw_4
                from data_nf_rka_komponen
                WHERE kdprovinsi='$kp' and kdkabupaten='$kk' and id_kategori_nf='$kt'";
        return $this->db->query($sql); 
    }

    public function get_nilai_monev_katmenu($kp='',$kk='',$kt='',$kel='')
    {
         $sql="SELECT          
                dm.menu,
                SUM(dnr.dak) as dak,
                SUM(dnr.triwulan1) as tw_1,
                SUM(dnr.triwulan2) as tw_2,
                SUM(dnr.triwulan3) as tw_3,
                SUM(dnr.triwulan4) as tw_4
                from data_nf_rka_komponen dnr
                INNER JOIN dak_nf_menu dm ON dm.id_kategori_nf=dnr.id_kategori_nf and dm.id=dnr.id_menu
                WHERE dnr.kdprovinsi='$kp' 
                and dnr.kdkabupaten='$kk'
                and dnr.id_kategori_nf='$kt' 
                and dm.ID_PENGELOMPOKAN='$kel' ";
        return $this->db->query($sql);  
    }
    public function get_nilai_monev_komponen_menu($kp='',$kk='',$kt='',$menu='')
    {
        $sql="  SELECT
                SUM(triwulan1) as tw_1,
                SUM(triwulan2) as tw_2,
                SUM(triwulan3) as tw_3,
                SUM(triwulan4) as tw_4
                from data_nf_rka_komponen
                WHERE kdprovinsi='$kp' and kdkabupaten='$kk' and id_kategori_nf='$kt' and id_menu='$menu'";
        return $this->db->query($sql); 
    }

    public function get_nilai_monev_komponen_kelompok($kp='',$kk='',$kt='',$kel='')
    {
        $sql="  SELECT
                SUM(dk.triwulan1) as tw_1,
                SUM(dk.triwulan2) as tw_2,
                SUM(dk.triwulan3) as tw_3,
                SUM(dk.triwulan4) as tw_4
                from data_nf_rka_komponen dk
                INNER JOIN dak_nf_menu ru ON ru.id=dk.id_menu
                WHERE dk.kdprovinsi='06' and dk.kdkabupaten='$kk' and dk.id_kategori_nf='$kt' and ru.ID_PENGELOMPOKAN='$kel'";
        return $this->db->query($sql); 
    }
    // untuk latihan aja cuy
}
