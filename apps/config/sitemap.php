<?php
/**
 * Later this file will be automatically auto-generated...
 * Menu are stored in database but we create this file for faster menu creation
 */

// Load required library
require_once(LIBRARY . "node.php");

// This act as menu container
$root = new Node("[ROOT]");
$root->AddNode("HOME", "main");
$menu = $root->AddNode("MASTER DATA", null, "menu");
    $menu->AddNode("Fasilitas Medis", null, "title");
        $menu->AddNode("Data Dokter", "master.dokter");
        $menu->AddNode("Data Poliklinik", "master.poliklinik");
        $menu->AddNode("Data Kamar Rawat", "master.kamar");
        $menu->AddNode("Data Laboratorium", "master.lab");
    $menu->AddNode("Jasa/Layanan/Tindakan", null, "title");
        $menu->AddNode("Kelompok Billing", "master.klpbilling");
        $menu->AddNode("Kelompok Jasa/Tindakan", "master.klpjasa");
        $menu->AddNode("Daftar Jasa/Tindakan", "master.jasa");
    $menu->AddNode("Data Coding Penyakit", null, "title");
        $menu->AddNode("Kelompok Penyakit", "master.klppenyakit");
        $menu->AddNode("Daftar Jenis Penyakit", "master.penyakit");

$menu = $root->AddNode("PELAYANAN", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Daftar Pasien", "pelayanan.pasien");
    $menu->AddNode("Transaksi", null, "title");
        $menu->AddNode("Dokter Jaga", "pelayanan.dokterjaga");
        $menu->AddNode("Daftar Pasien Dirawat", "pelayanan.perawatan");
        //$menu->AddNode("Data Layanan & Tindakan", "pelayanan.tindakan");
        $menu->AddNode("Pembayaran Billing Pasien", "pelayanan.billing");
    $menu->AddNode("Laporan", null, "title");
        $subMenu = $menu->AddNode("Pendapatan", null, "submenu");
            $subMenu->AddNode("Pendapatan Medrek", "report.medrek");
            $subMenu->AddNode("Pendapatan Jasa/Tindakan", "report.jasatindakan");
            $subMenu->AddNode("Pendapatan Laboratorium", "report.lab");
            $subMenu->AddNode("Pendapatan Gizi", "report.gizi");
            $subMenu->AddNode("Pendapatan Ambulance", "report.ambulance");
        $subMenu = $menu->AddNode("Jasa Medis", null, "submenu");
            $subMenu->AddNode("JM Tindakan Khusus", "report.jmtikus");
            $subMenu->AddNode("JM Visit Dokter", "report.jasmed");
            $subMenu->AddNode("Fee Dokter Jaga", "report.dokterjaga");
        //$subMenu = $menu->AddNode("Jasa Medis", null, "submenu");
        $subMenu = $menu->AddNode("Kunjungan Pasien", "report.kunjungan");
        $subMenu = $menu->AddNode("Rekapitulasi Bulanan", "report.rekapitulasi");

$menu = $root->AddNode("KAS & BANK", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Daftar Kas/Bank", "master.bank");
    $menu->AddNode("Transaksi Kas/Bank", null, "title");
        $menu->AddNode("Transaksi Kas/Bank", "cashbook.cbtrx");
        $menu->AddNode("Pencairan Klaim BPJS", "cashbook.pencairan");
        $menu->AddNode("Penerimaan Piutang", "cashbook.penerimaan");
        $menu->AddNode("Pembayaran Hutang", "cashbook.pembayaran");
    $menu->AddNode("Laporan Kas/Bank", null, "title");
        $menu->AddNode("Laporan Kas/Bank", "cashbook.report");

$menu = $root->AddNode("ASSET", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Daftar Relasi", "master.relasi");
        $menu->AddNode("Kelompok Asset", "asset.klpasset");
        $menu->AddNode("Daftar Asset", "asset.assetlist");
    $menu->AddNode("Transaksi Asset", null, "title");
        $menu->AddNode("Pembelian Asset", "asset.pembelian");
        //$menu->AddNode("Penjualan Asset", "asset.penjualan");
    $menu->AddNode("Laporan Asset", null, "title");
        $menu->AddNode("Laporan Penyusutan", "asset.assetlist/report");

$menu = $root->AddNode("APOTEK", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $subMenu = $menu->AddNode("Master Data Obat", null, "submenu");
            $subMenu->AddNode("Daftar Obat", "apotek/aptitems");
            $subMenu->AddNode("Jenis Obat", "apotek/itemtype");
            $subMenu->AddNode("Golongan Obat", "apotek/itemgroup");
            $subMenu->AddNode("Satuan Barang", "apotek/itemunit");
        $menu->AddNode("Daftar Relasi", "master.relasi");
    $menu->AddNode("Transaksi Penjualan", null, "title");
        $menu->AddNode("Penjualan", "apotek.invoice");
        $menu->AddNode("Retur Penjualan", "apotek.arretur");
        $menu->AddNode("Penerimaan Piutang", "apotek.receipt");
    $menu->AddNode("Transaksi Pembelian", null, "title");
        $menu->AddNode("Pembelian Barang", "apotek.purchase");
        $menu->AddNode("Retur Pembelian", "apotek.apretur");
        $menu->AddNode("Pembayaran Hutang", "apotek.payment");
    $menu->AddNode("Operasional Apotek", null, "title");
        $menu->AddNode("Biaya Operasional", "apotek.cost");
        $menu->AddNode("Laporan Biaya Operasional", "apotek.cost/report");
    $menu->AddNode("Laporan Apotek", null, "title");
        $menu->AddNode("Laporan Penjualan", "apotek.sale/report");
        $menu->AddNode("Laporan Pembelian", "apotek.purchase/report");

$menu = $root->AddNode("AKUNTANSI", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Data Header Akun", "master.coagroup");
        $menu->AddNode("Data Akun Perkiraan", "master.coadetail");
        $menu->AddNode("Jenis Transaksi", "master.trxtype");
        $menu->AddNode("Saldo Awal Akun", "accounting.obal");
    $menu->AddNode("Transaksi", null, "title");
        $menu->AddNode("Jurnal Akuntansi", "accounting.jurnal");
        //$menu->AddNode("Print Voucher/Jurnal", "accounting.jurnal/print_all");
    $menu->AddNode("Laporan", null, "title");
        $subMenu = $menu->AddNode("Laporan Jurnal/Voucher", null, "submenu");
            $subMenu->AddNode("Detail", "accounting.report/journal");
            $subMenu->AddNode("Rekapitulasi", "accounting.report/recap");
        $subMenu = $menu->AddNode("Laporan Ledger", null, "submenu");
            $subMenu->AddNode("Detail", "accounting.bukutambahan/detail");
            $subMenu->AddNode("Rekapitulasi", "accounting.bukutambahan/recap");
        //$menu->AddNode("Cost & Revenue", "accounting.bukutambahan/costrevenue");
        $menu->AddNode("Trial Balance", "accounting.trialbalance/recap");
        $menu->AddNode("Worksheet Balance", "accounting.worksheetbalance/recap");

     $menu = $root->AddNode("PERSONALIA", null, "menu");
    $menu->AddNode("Master Data", null, "title");
        $menu->AddNode("Data Investor", "master.investor");
        $menu->AddNode("Data Bagian", "master.department");
        $menu->AddNode("Data Karyawan", "master.karyawan");
        $menu->AddNode("Master Gaji Karyawan", "personalia.payroll");
    $menu->AddNode("Transaksi", null, "title");
        $menu->AddNode("Proses Hitung Gaji", "personalia.payroll/proses");
    $menu->AddNode("Laporan", null, "title");
        $menu->AddNode("Laporan Gaji Karyawan", "personalia.payroll/view");
        $menu->AddNode("Kartu Gaji Karyawan", "personalia.payroll/card");

$menu = $root->AddNode("PENGATURAN", null, "menu");
    $menu->AddNode("Data Umum", null, "title");
        $menu->AddNode("Data Perusahaan", "master.company");
        $menu->AddNode("Bisnis Unit", "master.sbu");

    $menu->AddNode("Pemakai System", null, "title");
        $menu->AddNode("Pemakai & Hak Akses", "master.useradmin");
    $menu->AddNode("Pengaturan System", null, "title");
        //$menu->AddNode("Setting Pengumuman", "master.attention");
        $menu->AddNode("Set Periode Akuntansi", "main/set_periode");
        $menu->AddNode("Ganti Password Sendiri", "main/change_password");
        $menu->AddNode("Daftar Hak Akses", "main/aclview");
// Special access for corporate
$persistence = PersistenceManager::GetInstance();
$isCorporate = $persistence->LoadState("is_corporate");
$forcePeriode = $persistence->LoadState("force_periode");
/*
if ($forcePeriode) {
	$root->AddNode("Ganti Periode", "main/set_periode");
}
$root->AddNode("Ganti Password", "main/change_password");
*/
//$root->AddNode("Notifikasi", "main");
$root->AddNode("LOGOUT", "home/logout");

// End of file: sitemap.php.php
