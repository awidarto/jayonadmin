<?php

class Admin extends Application
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if(logged_in())
		{
			$this->breadcrumb->add_crumb('Home','admin/dashboard');
			$this->breadcrumb->add_crumb('Dashboard','admin/dashboard');

			$year = date('Y',time());
			$month = date('m',time());

			$devices = $this->db->distinct()
				->select('identifier')
				->get($this->config->item('location_log_table'))
				->result();

			$locations = array();

			foreach($devices as $d){
				$loc = $this->db
					->select('identifier,timestamp,latitude as lat,longitude as lng')
					->where('identifier',$d->identifier)
					->like('timestamp',date('Y-m-d',time()),'after')
					->limit(1,0)
					->order_by('timestamp','desc')
					->get($this->config->item('location_log_table'));

				if($loc->num_rows() > 0){
					$loc = $loc->row();

					$locations[] = array(
						'lat'=>(double)$loc->lat,
						'lng'=>(double)$loc->lng,
						'data'=>array(
								'timestamp'=>$loc->timestamp,
								'identifier'=>$loc->identifier
							)
						);
				}
			}

			$page['locdata'] = json_encode($locations);


			$page['period'] = ' - '.date('M Y',time());
			$page['page_title'] = 'Dashboard';
			$this->ag_auth->view('dashboard',$page);
		}
		else
		{
			$this->login();
		}
	}

    public function fixcodtariff($merchant_id,$wrong,$right){
        $result = $this->db->where('merchant_id',$merchant_id)
            ->from($this->config->item('incoming_delivery_table'))
            ->get()->result_array();

        $total = 0;
        $wtotal = 0;
        foreach ($result as $r) {
            $total++;
            if($r['delivery_cost']%$wrong == 0){
                print "\r\n";
                print $r['delivery_id']."\r\n";
                print $r['delivery_type']."\r\n";
                print $r['total_price']."\r\n";
                print $r['delivery_cost']."\r\n".$r['weight']."\r\n".$r['chargeable_amount'];
                //print_r($r);
                $wtotal++;
            }

        }

        print 'totals '.$total.' '.$wtotal;

    }


	public function uichanges(){
		$last = $this->input->post('lastupdate');
		$last = date('Y-m-d H:i:s',abs($last));
		$total_changed = $this->db->where('created > ', $last)
			->count_all_results($this->config->item('incoming_delivery_table'));

		print json_encode(array('total_changed'=>$total_changed,'query'=>$this->db->last_query() ));
	}

    public function geoprocess(){
        set_time_limit(0);
        $tagged = $this->db->where('photo_lat != ',0)
                ->where('photo_lon != ',0)
                ->distinct('delivery_id,photo_lat,photo_lon')
                ->group_by('delivery_id')
                //->limit(100)
                ->from($this->config->item('phototag_table'))->get()->result_array();

        $counter = 1;

        foreach($tagged as $tag){

            if($counter % 10 == 0){
                print "sleep 2 sec\r\n";
                usleep(2000000);
            }

            if($photo_tag = $this->get_phototag($tag['delivery_id'])){
                $locdata['dir_lat'] = $photo_tag['photo_lat'];
                $locdata['dir_lon'] = $photo_tag['photo_lon'];
                $locdata['latitude'] = $photo_tag['photo_lat'];
                $locdata['longitude'] = $photo_tag['photo_lon'];

                print $tag['delivery_id'].' : '.$photo_tag['photo_lat'].' : '.$photo_tag['photo_lon']."\r\n";

                $this->db->where('delivery_id',$tag['delivery_id'])->update($this->config->item('incoming_delivery_table'),$locdata);
            }

            $counter++;
        }

    }

    public function testdistance(){

    }

	public function testmail(){
		$subject = 'Processed order';
		$to = 'andy.awidarto@gmail.com';
		$template = '';
		$data = '';
		if(send_notification($subject,$to,$template,$data)){
			print "notification sent";
		}else{
			print "failed to send notification";
		}
	}

    public function jsonarray(){
        $json = '{"status":"OK:DEVSYNC","data":[{"delivery_id":"298-25-062014-47781","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"85957","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"Margaretha Aprilia (85957)","by_email":"andrew@bukukita.com","by_phone":"62213157585#088801357883","rec_name":"Margaretha Aprilia (85957)","seq":"0","rec_sign":null,"tot_price":"96199","tot_disc":"0","tot_tax":"","chg_amt":"102199","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":"OMEGA SERVICE CENTER\nPT.JayGee Enterprises.\nJL.KH Wahid Hasyim no.79-81,\nMenteng.","ship_dir":"(Sebelah\nSeven eleven minimarket atau\nOria hotel)","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"96.199,00"},{"delivery_id":"298-25-062014-47792","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"25628","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"Ian Leonardo (25628)","by_email":"andrew@bukukita.com","by_phone":"6281586500234#081513011333","rec_name":"Ian Leonardo (25628)","seq":"0","rec_sign":null,"tot_price":"304450","tot_disc":"0","tot_tax":"","chg_amt":"310450","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":"Bank Danamon Lt 7\nJalan Kebon Sirih No 15","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"304.450,00"},{"delivery_id":"8163-25-062014-47817","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Brodo","mc_street":"Jl. Kemang Selatan 8 No. 64 B Jakarta Selatan","mc_district":"Jakarta Selatan","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"1400007","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"Agus Surono","by_email":"noemail","by_phone":"6281389953119","rec_name":"Agus Surono","seq":"0","rec_sign":null,"tot_price":"225000","tot_disc":"0","tot_tax":"","chg_amt":"239000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"Jl. Diponegoro No. 64 Jakarta Pusat","ship_dir":"Lt.2","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"232.500,00"},{"delivery_id":"008059-25-062014-00047868","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15484","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"KIKI RIFKI PERMANA","by_email":"noemail","by_phone":"6281218571370","rec_name":"KIKI RIFKI PERMANA","seq":"0","rec_sign":null,"tot_price":"101000","tot_disc":"0","tot_tax":"","chg_amt":"115000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":" \nACEMARK\nACEMARK BUILDING\nJL CIKINI RAYA NO 58 GH\nMENTENG, JAKARTA RAYA 10330\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"108.500,00"},{"delivery_id":"006754-25-062014-00047883","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004785413","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"EVY ALVIONITA YURNA","by_email":"noemail","by_phone":"6285754111643","rec_name":"EVY ALVIONITA YURNA","seq":"0","rec_sign":null,"tot_price":"129625","tot_disc":"0","tot_tax":"","chg_amt":"139625","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"jl.teuku umar no 09, menteng. jakarta pusat, jl. ayani\nkm 8, komplek persadamas blok bumi laras selatan 1\nno 09\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"132.625,00"},{"delivery_id":"006754-25-062014-00047892","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004782134","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"EVY ALVIONITA YURNA","by_email":"noemail","by_phone":"6285754111643","rec_name":"EVY ALVIONITA YURNA","seq":"0","rec_sign":null,"tot_price":"161500","tot_disc":"0","tot_tax":"","chg_amt":"171500","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"jl.teuku umar no 09, menteng. jakarta pusat, jl. ayani\nkm 8, komplek persadamas blok bumi laras selatan 1\nno 09\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"164.500,00"},{"delivery_id":"006754-25-062014-00047927","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004783324","by_time":"2014-06-26 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"ULFAH WIDYASTUTI","by_email":"noemail","by_phone":"628128272130","rec_name":"ULFAH WIDYASTUTI","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"7000","delivery_cost":"7000","cod_cost":"0","cod_curr":"IDR","ship_addr":"Gedung BPPT II Lt 21 Jl MH Thamrin No. 8\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"82-26-062014-47947","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"adikbayi.com","mc_street":"Jl. Kiangsana no. 1, Taman Rejeki","mc_district":"Cibinong","mc_province":"West Java","mc_city":"Bogor","mc_trans_id":"BBM20000","by_time":"2014-06-26 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"Erny ","by_email":"noemail","by_phone":"62818409095","rec_name":"Erny ","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"JL Kesehatan 3 No 2 (Rumah Di Hook, Masuk Dari Paggar di Jl Persatuan Guru)\n10160 Jakarta Pusat","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"008113-26-062014-00047966","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"benlieschoice.com","mc_street":"Aptm. Mediterania 2, Tower F lt.12FR Tanjung Duren, Jakarta Barat 11470","mc_district":"Jakarta Barat","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"-","by_time":"2014-06-27 00:00:00","by_zone":"Sawah Besar","by_city":"Jakarta Pusat","by_name":"LIKE WIRJANTY","by_email":"noemail","by_phone":"628115031045","rec_name":"LIKE WIRJANTY","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"JL DWIWARNA, GANG FAJAR 10\/45 RT 015 RW 008\nSAWAH BESAR\nJAKARTA","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"008157-26-062014-00047972","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"RODADUA.NET","mc_street":"Jl. Ciledug Raya,  Puri Beta 1 Petos Junction Lot.7 No.26 (posisi toko diseberang Pizza Hut Delivery- PHD )Ciledug - Tangerang","mc_district":"Petukangan Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta Selatan","mc_trans_id":"-","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"FIKY SAPUTRA","by_email":"noemail","by_phone":"6285780493894","rec_name":"FIKY SAPUTRA","seq":"0","rec_sign":null,"tot_price":"358500","tot_disc":"0","tot_tax":"","chg_amt":"392000","delivery_cost":"26000","cod_cost":"7500","cod_curr":"IDR","ship_addr":"PT UOB KAY HIAN SECURITIES\nPLAZA UOB LT.36\nJL MH THAMRIN KAV 8-10\nJAKARTA PUSAT","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"366.000,00"},{"delivery_id":"298-26-062014-47987","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"491730","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"Fitriani (491730)","by_email":"andrew@bukukita.com","by_phone":"6283898061543","rec_name":"Fitriani (491730)","seq":"0","rec_sign":null,"tot_price":"42649","tot_disc":"0","tot_tax":"","chg_amt":"48649","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":" \tjalan kh hasyim ashari , pusat niaga roxy mas blok e2 no 32-34 jakarta pusat","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"42.649,00"},{"delivery_id":"298-26-062014-47979","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"22338","by_time":"2014-06-27 00:00:00","by_zone":"Sawah Besar","by_city":"Jakarta Pusat","by_name":"Mita Ekawati (22338)","by_email":"andrew@bukukita.com","by_phone":"628111808085","rec_name":"Mita Ekawati (22338)","seq":"0","rec_sign":null,"tot_price":"84299","tot_disc":"0","tot_tax":"","chg_amt":"90299","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":"Gedung Soemitro Djojohadikusumo\nLt 9, Otoritas Jasa Keuangan,\nKomplek Kementerian Keuangan,\nJl. Lapangan Banteng Timur No. 2-\n4, Jakarta Pusat","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"84.299,00"},{"delivery_id":"298-26-062014-47980","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"311934","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"NOVIE IMAWATY (311934)","by_email":"andrew@bukukita.com","by_phone":"62215728592#0811187011","rec_name":"NOVIE IMAWATY (311934)","seq":"0","rec_sign":null,"tot_price":"176312","tot_disc":"0","tot_tax":"","chg_amt":"182312","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":" \tBNI Unit Service Quality\nGd. BNI Kantor Pusat, Lantai 4\nJl. Jend. Sudirman Kav. 1","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"176.312,00"},{"delivery_id":"298-26-062014-47981","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"bukukita.com","mc_street":"Jl.Topaz Raya C2 No.12, Komplek Permata Puri Media,","mc_district":"Kembangan","mc_province":"DKI Jakarta","mc_city":"Jakarta Barat","mc_trans_id":"20566","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"Lestyowati (20566)","by_email":"andrew@bukukita.com","by_phone":"6282121612172","rec_name":"Lestyowati (20566)","seq":"0","rec_sign":null,"tot_price":"67299","tot_disc":"0","tot_tax":"","chg_amt":"73299","delivery_cost":"6000","cod_cost":"0","cod_curr":"IDR","ship_addr":"PT. Tanjung Bersinar Cemerlang\/Lesty\nWisma Nusantara lantai 24\nJl. MH. Thamrin No. 59 jakarta","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"67.299,00"},{"delivery_id":"6971-26-062014-47996","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Rumah Parfum","mc_street":"Jl. Angkasa Kav B-6, Kota Baru Bandar Kemayoran","mc_district":"Mega Glodok Kemayoran, GF C5\/2","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"TRX_6971_0342359001403759502","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":" Irlan Charmansyah","by_email":"irlancharmansyah@yahoo.co.uk","by_phone":"6281212348787","rec_name":" Irlan Charmansyah","seq":"0","rec_sign":null,"tot_price":"525000","tot_disc":"0","tot_tax":"","chg_amt":"546500","delivery_cost":"6500","cod_cost":"15000","cod_curr":"IDR","ship_addr":"INPEX Corporation TCC (The City Center) Building, 40th Floor, Jl. K.H. Mas Mansyur Kav.126, Jakarta\nJakarta Pusat - Tanah Abang","ship_dir":"z","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"540.000,00"},{"delivery_id":"8197-26-062014-48014","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"www.gadgetsvillage.net","mc_street":"Ruko Dasana Xentre Blok AD\/29, Kel. Bojong Nangka","mc_district":"Kelapa Dua","mc_province":"Banten","mc_city":"Tangerang","mc_trans_id":"105528","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"Monika Iskandar","by_email":"monikaiskandar@gmail.com","by_phone":"62811197659","rec_name":"Monika Iskandar","seq":"0","rec_sign":null,"tot_price":"150000","tot_disc":"0","tot_tax":"","chg_amt":"164000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":" jl. Batutulis raya no.2 Jakarta Pusat 10120","ship_dir":" jl. Batutulis raya no.2 Jakarta Pusat 10120","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"157.500,00"},{"delivery_id":"8165-26-062014-48013","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"VIP Plaza","mc_street":"Indosurya Plaza Lt. 3A Jl. M. H. Thamrin No. 8 - 9 Jakarta Pusat 10230 Indonesia","mc_district":"Jakarta Pusat","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"100005997","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"DANDY  STIADY","by_email":"noemail","by_phone":"","rec_name":"DANDY  STIADY","seq":"0","rec_sign":null,"tot_price":"171000","tot_disc":"0","tot_tax":"","chg_amt":"185000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"JL.MEDAN MERDEKA TIMUR NO.16 GED MINA BAHARI 1 LT 1 PUSKITA GAMBIR JAKARTA PUSAT 10110","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"178.500,00"},{"delivery_id":"007995-26-062014-00048019","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"http:\/\/www.tokoayesha.com\/","mc_street":"Jl. W.R.Supratman Gg. Pepaya No.18 RT.01\/RW.05 Kp.Utan, Ciputat Timur, TangSel 15412","mc_district":"Tangerang Selatan","mc_province":"Banten","mc_city":"Tangerang Selatan","mc_trans_id":"2L1F5BM5O8D3","by_time":"0000-00-00 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"","by_email":"","by_phone":"","rec_name":"TARIDA","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":null,"tot_tax":null,"chg_amt":"","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"JL PETOJO BINATU NO.3G RT 002 RW 007\nPETOJO UTARA, GAMBIR\nJAKARTA ","ship_dir":"","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":"","dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"8197-26-062014-48021","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"www.gadgetsvillage.net","mc_street":"Ruko Dasana Xentre Blok AD\/29, Kel. Bojong Nangka","mc_district":"Kelapa Dua","mc_province":"Banten","mc_city":"Tangerang","mc_trans_id":"1015533","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"THOMAS YUDHIANTO","by_email":"thomasyudhianto19@gmail.com","by_phone":"622196749465","rec_name":"THOMAS YUDHIANTO","seq":"0","rec_sign":null,"tot_price":"125000","tot_disc":"0","tot_tax":"","chg_amt":"139000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"JL.K.H WAHID HASYIM NO.164 JAKARTA PUSAT ","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"132.500,00"},{"delivery_id":"8165-26-062014-48025","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"VIP Plaza","mc_street":"Indosurya Plaza Lt. 3A Jl. M. H. Thamrin No. 8 - 9 Jakarta Pusat 10230 Indonesia","mc_district":"Jakarta Pusat","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"100006093","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"Jackson Simanullang","by_email":"noemail","by_phone":"","rec_name":"Jackson Simanullang","seq":"0","rec_sign":null,"tot_price":"489300","tot_disc":"0","tot_tax":"","chg_amt":"503300","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"PT ASIA REP MID PLAZA 2 LT 16 JL. JEND SUDIRMAN KAV 10-11 TANAH ABANG JAKARTA PUSAT 10220","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"496.800,00"},{"delivery_id":"8171-26-062014-48030","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Dunia Digital","mc_street":"Pergudangan Daan Mogot Prima, Jl daan mogot km 12,8 blok A no.10","mc_district":"Cengkareng","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"Z06000185","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"ISYEU \/ HIRNO","by_email":"noemail","by_phone":"","rec_name":"ISYEU \/ HIRNO","seq":"0","rec_sign":null,"tot_price":"3920000","tot_disc":"0","tot_tax":"","chg_amt":"3939500","delivery_cost":"19500","cod_cost":"0","cod_curr":"IDR","ship_addr":"PT. WIPA WIJAPALMA JL. KEBON KACANG4 NO. 24B KEL. KEBON KACANG KEC. TANAH ABANG, JAKARTA PUSAT","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"3.920.000,00"},{"delivery_id":"8169-26-062014-48033","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"mysecretcollection.com","mc_street":"Toko Best Kept Secret, Pluit Village Mall Lt 2 Unit 10 Jakarta","mc_district":"Penjaringan","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"#3952","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"richa vidya yustikaningrum","by_email":"noemail","by_phone":"6281297887850","rec_name":"richa vidya yustikaningrum","seq":"0","rec_sign":null,"tot_price":"271.500","tot_disc":"0","tot_tax":"271.500","chg_amt":"271.500","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"jl pembangunan II no 1c rt 011 rw 002 petojo utara, gambir\njakarta pusat JK 10130","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"550.500,00"},{"delivery_id":"8165-26-062014-48042","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Barat","mc_name":"VIP Plaza","mc_street":"Indosurya Plaza Lt. 3A Jl. M. H. Thamrin No. 8 - 9 Jakarta Pusat 10230 Indonesia","mc_district":"Jakarta Pusat","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"100006108","by_time":"2014-06-27 00:00:00","by_zone":"Taman Sari","by_city":"Jakarta Barat","by_name":"Julianus Silalahi","by_email":"noemail","by_phone":"","rec_name":"Julianus Silalahi","seq":"0","rec_sign":null,"tot_price":"499500","tot_disc":"0","tot_tax":"","chg_amt":"513500","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"Jl. Thalib IV no.8 rt.12\/5 krukut taman sari jakarta barat 11140","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"507.000,00"},{"delivery_id":"82-26-062014-48079","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"adikbayi.com","mc_street":"Jl. Kiangsana no. 1, Taman Rejeki","mc_district":"Cibinong","mc_province":"West Java","mc_city":"Bogor","mc_trans_id":"WEB19534","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":" sugianto","by_email":"noemail","by_phone":"","rec_name":" sugianto","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nMRT Business University Gedung Jaya Lt. 5, Jl. MH Thamrin No. 12 Jakarta Pusat\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"006754-26-062014-00048090","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004791232","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"PUTRA","by_email":"noemail","by_phone":"628999971313","rec_name":"PUTRA","seq":"0","rec_sign":null,"tot_price":"34300","tot_disc":"0","tot_tax":"","chg_amt":"44300","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"\ngedung graha metro lantai 4 jln penjernihan 1 no 8,\npejompongan jakarta pusat 10210\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"37.300,00"},{"delivery_id":"006754-26-062014-00048099","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Selatan","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 100478738","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"EVELINE GOMAIDI","by_email":"noemail","by_phone":"628161177228","rec_name":"EVELINE GOMAIDI","seq":"0","rec_sign":null,"tot_price":"138975","tot_disc":"0","tot_tax":"","chg_amt":"148975","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"Intiland Tower lantai 8, Jl. Jend Sudirman Kav 32\nJakarta Selatan, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"141.975,00"},{"delivery_id":"006754-26-062014-00048104","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 10047881","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"NUNGKI INDRIANTI","by_email":"noemail","by_phone":"628122766645","rec_name":"NUNGKI INDRIANTI","seq":"0","rec_sign":null,"tot_price":"82400","tot_disc":"0","tot_tax":"","chg_amt":"92400","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"\nGedung II BPPT Lantai 21 JL MH Thamrin 8\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"85.400,00"},{"delivery_id":"006754-26-062014-00048105","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004791523","by_time":"2014-06-27 00:00:00","by_zone":"Sawah Besar","by_city":"Jakarta Pusat","by_name":"MARIAH KIPTIA","by_email":"noemail","by_phone":"628988610672","rec_name":"MARIAH KIPTIA","seq":"0","rec_sign":null,"tot_price":"96050","tot_disc":"0","tot_tax":"","chg_amt":"106050","delivery_cost":"7000","cod_cost":"3000","cod_curr":"IDR","ship_addr":"\nJalan Raya Gunung Sahari No.32, Gedung\nWahanaartha Honda\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"99.050,00"},{"delivery_id":"006754-26-062014-00048106","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 1004790818","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"SWEDINA YOLANDA","by_email":"noemail","by_phone":"6281344758700","rec_name":"SWEDINA YOLANDA","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"7000","delivery_cost":"7000","cod_cost":"0","cod_curr":"IDR","ship_addr":"\njl. cikini ampiun ruko 6A (sebelah warung padang) rt\n2 rw 1 pegangsaan, cikini, jakarta pusat\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"006754-26-062014-00048111","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 10047875","by_time":"2014-06-27 00:00:00","by_zone":"Sawah Besar","by_city":"Jakarta Pusat","by_name":"MARIAH KIPTIA","by_email":"noemail","by_phone":"628988610672","rec_name":"MARIAH KIPTIA","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"7000","delivery_cost":"7000","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nJalan Raya Gunung Sahari No.32, Gedung\nWahanaartha Honda\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"006754-26-062014-00048132","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 100478663","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"TRI WAHYUNINGSIH","by_email":"noemail","by_phone":"6285692233929","rec_name":"TRI WAHYUNINGSIH","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"7000","delivery_cost":"7000","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nBappenas, Ruang Inspektorat Bidang Administrasi\nUmum, Gedung Baru Lantai 5 , Jalan Taman Suropati\nNo. 2\nJakarta Pusat, DKI Jakarta\n-\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"006754-26-062014-00048145","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Bukabuku.com","mc_street":"Jalan Kramat Lontar NO: J-108 Jakarta Pusat","mc_district":"Senen","mc_province":"DKI Jakarta","mc_city":"Jakarta Pusat","mc_trans_id":"AWB: 10047885","by_time":"2014-06-27 00:00:00","by_zone":"Sawah Besar","by_city":"Jakarta Pusat","by_name":"FITA NURMAYASARI","by_email":"noemail","by_phone":"6281287336313","rec_name":"FITA NURMAYASARI","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"7000","delivery_cost":"7000","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nBiro KLI Kemenkeu RI, Gd Djuanda 1\/12, Jl. Dr.\nWahidin Raya No.1\nJakarta Pusat, DKI Jakarta\n\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"007075-26-062014-00048161","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"id.bestbuy-world.com","mc_street":"GRAND SLIPI TOWER, JL. S. PARMAN KAV.22-24 JAKARTA BARAT 11480 Lt. 18","mc_district":"Palmerah","mc_province":"DKI Jakarta","mc_city":"Jawa Barat","mc_trans_id":"29597","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"Namiko Abe ","by_email":"noemail","by_phone":"6282114491436","rec_name":"Namiko Abe ","seq":"0","rec_sign":null,"tot_price":"1321000","tot_disc":"0","tot_tax":"","chg_amt":"1342500","delivery_cost":"6500","cod_cost":"15000","cod_curr":"IDR","ship_addr":"Jl. Jend. Sudirman kav. 10-11 InterContinetal Jakarta\nMidplaza Jakarta 10220 Jakarta Indonesia ","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"1.336.000,00"},{"delivery_id":"008059-26-062014-00048166","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15604","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"MARIA HELENA ","by_email":"noemail","by_phone":"628170121018","rec_name":"MARIA HELENA ","seq":"0","rec_sign":null,"tot_price":"325500","tot_disc":"0","tot_tax":"","chg_amt":"339500","delivery_cost":"13000","cod_cost":"7500","cod_curr":"IDR","ship_addr":"BANK UOB INDONESIA\nGEDUNG UOB INDONESIA DIVISI RETAIL KREDIT MANAGEMENT, JL. GADJAH MADA NO. 1A LT. 8\nJAKARTA PUSAT, JAKARTA RAYA 10130\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"333.000,00"},{"delivery_id":"008059-26-062014-00048167","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15602","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"TRI LEKSONO (PPBRI) ","by_email":"noemail","by_phone":"628112138494","rec_name":"TRI LEKSONO (PPBRI) ","seq":"0","rec_sign":null,"tot_price":"244000","tot_disc":"0","tot_tax":"","chg_amt":"258000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"\n\nGEDUNG BRI KANWIL JAKARTA 1 LT. 4, JL. VETERAN II NO. 8\nGAMBIR (MASUK DR PINTU SAMPING, JANGAN PINTU DEPAN)\nJAKARTA PUSAT, JAKARTA RAYA\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"251.500,00"},{"delivery_id":"008059-26-062014-00048172","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15530","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"MEILANY PERULINA ","by_email":"noemail","by_phone":"628816131863","rec_name":"MEILANY PERULINA ","seq":"0","rec_sign":null,"tot_price":"265500","tot_disc":"0","tot_tax":"","chg_amt":"279500","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"\nJL. RIAU NO. 23, MENTENG\nJAKARTA PUSAT, JAKARTA RAYA 10350\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"273.000,00"},{"delivery_id":"008059-26-062014-00048177","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15572","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"IRNI DINIATI ","by_email":"noemail","by_phone":"628122124695","rec_name":"IRNI DINIATI ","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nKEMDIKBUD DIT. PPTK PAUDNI\nKOMP. KEMDIKBUD GD. C LT. 13 JL. JEND. SUDIRMAN SENAYAN JAKARTA\nJAKARTA PUSAT, JAKARTA RAYA\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"008059-26-062014-00048184","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Barat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15540","by_time":"2014-06-27 00:00:00","by_zone":"Tambora","by_city":"Jakarta Barat","by_name":"ESTER SUGIAWATY ","by_email":"noemail","by_phone":"62811936373","rec_name":"ESTER SUGIAWATY ","seq":"0","rec_sign":null,"tot_price":"114000","tot_disc":"0","tot_tax":"","chg_amt":"128000","delivery_cost":"6500","cod_cost":"7500","cod_curr":"IDR","ship_addr":"\nPT. SUMBERMAS, JL. JEMBATAN LIMA RAYA NO. 150A - 152\nJL. DWIWARNA GG 1 NO. 4 RT. 08\/010 \nJAKARTA BARAT, JAKARTA RAYA 11210\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"COD","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"121.500,00"},{"delivery_id":"008059-26-062014-00048186","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15514","by_time":"2014-06-27 00:00:00","by_zone":"Menteng","by_city":"Jakarta Pusat","by_name":"LANY HENDRADANI","by_email":"noemail","by_phone":"628557992280","rec_name":"LANY HENDRADANI","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":" \nPT. MNI - KORAN SINDO\nGED. SINDO - LT. 5 D\/A. JL. WAHID HASYIM 38\n(DEKAT STATSIUN GONDANGDIA)\nJAKARTA PUSAT, JAKARTA RAYA 10340\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"008059-26-062014-00048187","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"babyzania","mc_street":"Mall of Indonesia Rukan French Walk F-12, Kelapa Gading 14240 Jakarta Utara","mc_district":"Jakarta Utara","mc_province":"DKI Jakarta","mc_city":"Jakarta","mc_trans_id":"15508","by_time":"2014-06-27 00:00:00","by_zone":"Tanah Abang","by_city":"Jakarta Pusat","by_name":"WIWIN MEINARNI ","by_email":"noemail","by_phone":"6281327230888","rec_name":"WIWIN MEINARNI ","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"\nJL.ASIA AFRIKA LOT 19\nGED.PANIN TOWER LT.7\nJAKARTA PUSAT, JAKARTA RAYA 10270\n","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"},{"delivery_id":"001458-26-062014-00048198","as_date":"2014-06-27","as_timeslot":"2","as_zone":"0","as_city":"Jakarta Pusat","mc_name":"Saqina.com","mc_street":"Jl. Duren Tiga Raya No.16","mc_district":"Pancoran","mc_province":"DKI Jakarta","mc_city":"Jakarta Selatan","mc_trans_id":"-","by_time":"2014-06-27 00:00:00","by_zone":"Gambir","by_city":"Jakarta Pusat","by_name":"IBU YESSI TRISMASARI","by_email":"noemail","by_phone":"6282110116889","rec_name":"IBU YESSI TRISMASARI","seq":"0","rec_sign":null,"tot_price":"0","tot_disc":"0","tot_tax":"","chg_amt":"6500","delivery_cost":"6500","cod_cost":"0","cod_curr":"IDR","ship_addr":"JL MEDAN MERDEKA TIMUR 1A\nGEDUNG UTAMA PERTAMINA LT.9\nJAKARTA","ship_dir":"-","ship_lat":null,"ship_lon":null,"dl_time":null,"dl_type":"Delivery Only","dl_status":"cr_assigned","dl_note":null,"dl_lat":null,"dl_lon":null,"res_ref":null,"rev_ref":null,"pc":"0","cod_cost_fmt":"0,00"}],"timestamp":1403840045}' ;
        print_r(json_decode($json));
    }

    public function testjson(){
        print json_encode(
                            array( 'api_key' => '68dddf9790b9bb891e5a4a0e875ec37ad2c0843f',
                                'buyer_name' => 'RICHARD HARISON',
                                'recipient_name' => 'RICHARD HARISON',
                                'shipping_address' => 'PT. KARTIKA EKA YUDHA MARITIM WISMA MITRA SUNTER LT.10-04 MITRA SUNTER BOULEVARD BLOCK C.2 JL.YOS SUDARSO KAV.89 SUNTER JAYA, JAKARTA',
                                'buyerdeliveryzone' => 'Kelapa Gading',
                                'buyerdeliverycity' => 'Jakarta Utara',
                                'buyerdeliverytime' => '20140620',
                                'buyerdeliveryslot' => 1,
                                'directions' => 'Laksanakan Perintah',
                                'auto_confirm' => true,
                                'email' => 'richard@keymaritim.com',
                                'zip' => '14350',
                                'phone' => '08129625478',
                                'mobile1' => '08129625478',
                                'mobile2' =>'',
                                'total_price' => 339000,
                                'total_discount' => 0,
                                'total_tax' => 0 ,
                                'chargeable_amount' => 339000 ,
                                'delivery_cost' => 6500 ,
                                'cod_cost' => 7500 ,
                                'currency' => 'IDR' ,
                                'status' => 'pending',
                                'merchant_id' => 520,
                                'buyer_id' => 'C_0096421',
                                'trx_detail' => array(
                                    array(
                                                'unit_description' => 'Antonio Banderas Blue Seduction Man 200 ML',
                                                'unit_price' => 1 ,
                                                'unit_quantity' => 1,
                                                'unit_total' => 325000,
                                                'unit_pct_discount' => 0,
                                                'unit_discount' => 0
                                        )
                                ),
                                'width' => 20 ,
                                'height' => 20 ,
                                'length' => 20 ,
                                'weight' => 1 ,
                                'delivery_type' => 'COD' ,
                                'show_merchant' => 1 ,
                                'show_shop' => 1 ,
                                'cod_bearer' => 'buyer',
                                'delivery_bearer' => 'buyer',
                                'cod_method' => 'cash' ,
                                'ccod_method' => 'full'
                            )

            );
    }

    public function testglob($delivery_id){
        $existingpic = glob($this->config->item('picture_path').$delivery_id.'*.jpg');
        print_r($existingpic);
    }

    public function tmnull(){
        $delivery_id = '004670-02-102013-00019892';
        $result = $this->db->where('delivery_id', $delivery_id)
            ->where('latitude is not null', null)
            ->where('longitude is not null', null)
            ->get($this->config->item('incoming_delivery_table'));

        print_r($result->result());

        print $this->db->last_query();

    }

    public function zonezip(){
        ini_set("auto_detect_line_endings", true);

        $zone = '';
        $city = '';
        if (($handle = fopen(FCPATH."public/DKI_postal_code_2.csv", "r")) !== FALSE) {

            $idx = 0;
            $xdata = array();
            while (($data = fgetcsv($handle, 1000, ',','"')) !== FALSE) {
                if($idx > 0){
                    $xdata[] = $data;
                    /*
                    if($city == $data[1] && $zone == $data[2]){
                        $zips[] = $data[4];
                    }else{
                        print_r( array_unique($zips) );

                        print $city.' '.$zone.' '. implode(',', array_unique($zips)) ."\r\n";

                        $z = implode(',', array_unique($zips));

                        if($city != '' && $zone != ''){
                            $this->db->where('city', $city)
                                    ->where('district',$zone)
                                    ->update('districts',array('zips'=>$z));
                        }

                        $zips = array();

                    }

                    $city = $data[1];
                    $zone = $data[2];
                    */
                }
                $idx++;
            }

            //print_r($xdata);

            $zdata = array();
            foreach ($xdata as $key => $v) {
                $zdata[$v[1]][$v[2]][] = $v[4];
            }

            foreach ($zdata as $k => $v) {
                foreach($v as $y => $z){
                    $z = array_unique($z);
                    $m = implode(',', $z);
                    print $k.' '.$y.' -> '.$m."\r\n";

                    $this->db->where('city', $k)
                            ->where('district',$y)
                            ->update('districts',array('zips'=>$m));

                }
            }

            print_r($zdata);

            fclose($handle);
        }
    }

    public function geoinsert(){

        set_time_limit(0);

        $delis = $this->db->where('status','delivered')
            ->select('delivery_id,latitude,longitude')
            ->get($this->config->item('delivery_log_table'));

        $delis = $delis->result();

        foreach ($delis as $o) {
            $geodata = array(
                'latitude'=>$o->latitude,
                'longitude'=>$o->longitude,
                'dir_lat'=>$o->latitude,
                'dir_lon'=>$o->longitude
                );

            $this->db->where('delivery_id',$o->delivery_id)
                ->update($this->config->item('jayon_buyers_table'),$geodata);
        }

    }

    public function extractgeo()
    {
        set_time_limit(0);

        if ($handle = opendir( $this->config->item('picture_path') )) {
            echo "Directory handle: $handle\n";
            echo "Entries:\n";

            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if( $entry != '.' && $entry != '..'){
                    $imgfile = $entry;

                    $exifdata = $this->read_exif_gps($imgfile);

                    if($exifdata){
                        $data = array('geotag'=>$exifdata);
                    }else{
                        $data = array('geotag'=>'none');
                    }

                    echo( json_encode($data) );

                    $jfile = str_replace('.jpg', '.json', $entry);
                    @unlink( $this->config->item('picture_path').$jfile );
                    file_put_contents($this->config->item('picture_path').'/json/'.$jfile, json_encode($data));

                }
            }

            closedir($handle);
        }

    }

    public function read_exif_gps($imgfile){

        $imgfile = $this->config->item('picture_path').$imgfile;

        $exif = exif_read_data($imgfile);

        if(strripos($exif['SectionsFound'], 'GPS')){
            $lat = explode('/' ,$exif['GPSLatitude'][0]);
            $lon = explode('/' ,$exif['GPSLongitude'][0]);

            if(count($lat) > 1){
                $lat = $lat[0] / $lat[1];
            }else{
                $lat = $lat[0];
            }

            if(count($lon) > 1){
                $lon = $lon[0] / $lon[1];
            }else{
                $lon = $lon[0];
            }

            if(strtoupper($exif['GPSLatitudeRef']) == 'S'){
                $lat = -1 * $lat;
            }

            if(strtoupper($exif['GPSLongitudeRef']) == 'W'){
                $lon = -1 * $lon;
            }

            return array(
                'lat'=>$lat,
                'lon'=>$lon
            );
        }else{

            return false;
        }
        //print_r($exif);

    }


    public function testexif(){
        set_time_limit(0);
        $imgfile = $this->config->item('picture_path').'IMG_20140704_114738.jpg';

        $exif = exif_read_data($imgfile);

        if(strripos($exif['SectionsFound'], 'GPS')){
            $lat = explode('/' ,$exif['GPSLatitude'][0]);
            $lon = explode('/' ,$exif['GPSLongitude'][0]);

            if(count($lat) > 1){
                $lat = $lat[0] / $lat[1];
            }else{
                $lat = $lat[0];
            }

            if(count($lon) > 1){
                $lon = $lon[0] / $lon[1];
            }else{
                $lon = $lon[0];
            }

            if(strtoupper($exif['GPSLatitudeRef']) == 'S'){
                $lat = -1 * $lat;
            }

            if(strtoupper($exif['GPSLongitudeRef']) == 'W'){
                $lon = -1 * $lon;
            }

            print $lat."\r\n".$lon."\r\n";

        }else{
            print 'no geotag';
        }
        //print_r($exif);

    }

    public function geopic(){

        set_time_limit(0);

        $delis = $this->db
            ->where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_delivered'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_revoked'))
            ->or_where($this->config->item('assigned_delivery_table').'.status',$this->config->item('trans_status_mobile_noshow'))
            ->select('delivery_id,latitude,longitude')
            ->get($this->config->item('delivered_delivery_table'));

        $delis = $delis->result();

        foreach ($delis as $o) {

            //print_r($o->delivery_id);
            $imgfile = $this->config->item('picture_path').$o->delivery_id.'.jpg';

            if(file_exists($imgfile)){
                $latlon = read_gps_location($imgfile);
                if($latlon){
                    print_r($exifdata);
                }else{
                    print "no geotag\r\n";
                }
            }

            /*
            $geodata = array(
                'latitude'=>$o->latitude,
                'longitude'=>$o->longitude,
                'dir_lat'=>$o->latitude,
                'dir_lon'=>$o->longitude
                );
            */
            //$this->db->where('delivery_id',$o->delivery_id)
            //    ->update($this->config->item('jayon_buyers_table'),$geodata);
        }

    }

	public function monthlygraph($status = null){
		$this->load->library('plot');
		$lineplot = $this->plot->plot(500,130);

		$year = date('Y',time());
		$month = date('m',time());

		if(is_null($status)){
			$status = null;
		}else{
			$status = array('status'=>$status);
		}
		$series = getmonthlydatacountarray($year,$month,$status,null);
		//$series = getmonthlydatacountarray($year,$month,$status,null);

		$lineplot->SetPlotType('bars');
		$lineplot->setShading(0);
		$lineplot->SetDataValues($series);

		$lineplot->SetYDataLabelPos('plotin');

		# With Y data labels, we don't need Y ticks or their labels, so turn them off.
		//$lineplot->SetYTickLabelPos('none');
		//$lineplot->SetYTickPos('none');

		$lineplot->SetYTickIncrement(1);
		$lineplot->SetPrecisionY(0);

		//Turn off X axis ticks and labels because they get in the way:
		$lineplot->SetXTickLabelPos('none');
		$lineplot->SetXTickPos('none');

		//Draw it
		$lineplot->DrawGraph();
	}

	public function resetpass(){

		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|valid_email');

		if($this->form_validation->run() == FALSE)
		{
			$this->ag_auth->view('resetpass');
		}
		else
		{
			$email = set_value('email');
			if($buyer = $this->check_email($email)){
				$password = random_string('alnum', 8);
				$dataset['password'] = $this->ag_auth->salt($password);
				$this->db->where('email',$email)->update($this->config->item('auth_user_table'),$dataset);

				$edata['fullname'] = $buyer->fullname;
				$edata['password'] = $password;
				$subject = 'Password reset request at Jayon Express.';
				send_notification($subject,$email,null,null,'resetpassd',$edata);
				$this->oi->add_success('New password has been sent to your email.');

			}else{
				$this->oi->add_error('Your email can not be found, please consider registering as new member.');
			}

			redirect('resetpass');
		}

	}

	public function changepass()
	{
		$this->form_validation->set_rules('password', 'Password', 'min_length[6]|matches[password_conf]');
		$this->form_validation->set_rules('password_conf', 'Password Confirmation', 'min_length[6]|matches[password]');

		$id = $this->session->userdata('userid');
		$user = $this->get_user($id);
		$data['user'] = $user;

		if($this->form_validation->run() == FALSE)
		{
			$data['groups'] = $this->get_group();
			$data['page_title'] = 'Change Password';
			$this->ag_auth->view('editpass',$data);
		}
		else
		{
			$result = TRUE;

			$dataset['password'] = $this->ag_auth->salt(set_value('password'));

			if( $result = $this->update_user($id,$dataset))
			{
				$this->oi->add_success('Your password is now updated');
				redirect('admin/dashboard');

			} // if($this->ag_auth->register($username, $password, $email) === TRUE)
			else
			{
				$this->oi->add_error('Your password can not be changed.');
				redirect('admin/dashboard');
			}

		} // if($this->form_validation->run() == FALSE)

	} // public function register()


	private function check_email($email){
		$em = $this->db->where('email',$email)->get($this->config->item('auth_user_table'));
		if($em->num_rows() > 0){
			return $em->row_array();
		}else{
			return false;
		}
	}

	private function get_user($id){
		$result = $this->db->where('id', $id)->get($this->ag_auth->config['auth_user_table']);
		if($result->num_rows() > 0){
			return $result->row_array();
		}else{
			return false;
		}
	}

	private function get_group(){
		$this->db->select('id,description');
		$result = $this->db->get($this->ag_auth->config['auth_group_table']);
		foreach($result->result_array() as $row){
			$res[$row['id']] = $row['description'];
		}
		return $res;
	}

	private function update_user($id,$data){
		$result = $this->db->where('id', $id)->update($this->ag_auth->config['auth_user_table'],$data);
		return $result;
	}

    private function get_phototag($delivery_id){
        $tag = $this->db->where('delivery_id',$delivery_id)
                ->where('photo_lat != ',0)
                ->where('photo_lon != ',0)
                ->from($this->config->item('phototag_table'))->get()->result_array();
        if(count($tag) > 0){
            return $tag[0];
        }else{
            return false;
        }
    }


}

/* End of file: dashboard.php */
/* Location: application/controllers/admin/dashboard.php */