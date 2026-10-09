<?php

return [
    'nama' => env('KANTOR_NAMA', 'PT Indokom Grup'),
    'latitude' => (float) env('KANTOR_LATITUDE', -5.402922),
    'longitude' => (float) env('KANTOR_LONGITUDE', 105.362625),
    'radius_meter' => (int) env('KANTOR_RADIUS_METER', 300),
];
