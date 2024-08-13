<?php

namespace MyApp\Controllers;

use Phalcon\Mvc\Controller;
use MyApp\Entities\SifatPembelian;
use MyApp\Entities\JenisPembelian;

class TransaksiController extends Controller
{
    /**
     * Returns void
     * 
     * catatan:
     * Traksaksi pembelian barang dagangan secara kredit melibatkan
     * akun pembelian(+) dan akun utang dagang(+) yang dicatat kedalam 
     * jurnal khusus pembelian. 
     *
     * @param string $idPerusahaan
     * @param string $priodeAkuntansi
     * @return array
     */
    public function setPembelianAction() {
        $dataPembelian = $this->request->getJsonRawBody();

        if($data->pembelian->sifat_pembelian == SifatPembelian::tunai) {  //jurnal khusus pengeluaran kas
            pembelianTunai($dataPembelian);
        }
        else if($data->pembelian->sifat_pembelian == SifatPembelian::kredit) {  //jurnal khusus pembelian
            //proses
        }        
    }

    /**
	 * proses pembelian tunai
	 *
	 * @param stdClass $jurnalData
	 * @param stdClass $jenisJurnal
	 */
    private function pembelianTunai($dataPembelian) {

        $dataJurnal = new stdClass;
        $dataJurnal->id = null;
        $jurnalData->keterangan = $dataPembelian->keterangan;
        // $jurnalData->tanggal
        // $jurnalData->jenis_jurnal
        // $jurnalData->perusahaan->id
        // $jurnalData->office_store_outlet->id
        // $jurnalData->ref_bukti
        // $jurnalData->daftarItemJurnal;
        
        // $this->jurnalService->createJurnal($dataJurnal);
    }

    private function pembelianKredit() {

    }
}

