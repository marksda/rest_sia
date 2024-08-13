<?php

namespace MyApp\Entities;

enum EnumJenisPembelian: int {
    case perlengkapan = 1;
    case peralatan = 2;
    case bahan_baku = 3;
    case barang_dagangan = 4;
    case lain_lain = 5;
}