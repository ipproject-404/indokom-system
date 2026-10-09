<?php

return [
    'nama' => env('KANTOR_NAMA', 'PT Nama Perusahaan Anda'),
    'latitude' => (float) env('KANTOR_LATITUDE', -6.123456),
    'longitude' => (float) env('KANTOR_LONGITUDE', 106.123456),
    'radius_meter' => (int) env('KANTOR_RADIUS_METER', 100),
];
