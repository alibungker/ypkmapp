@props(['value'])
@php
    $nik = preg_replace('/\D/', '', (string) $value);
    $length = strlen($nik);

    if ($length === 0) {
        $masked = '-';
    } elseif ($length <= 4) {
        $masked = str_repeat('•', $length);
    } else {
        $visible = $length >= 12 ? 4 : 2;
        $masked = substr($nik, 0, $visible)
            . str_repeat('•', max(1, $length - ($visible * 2)))
            . substr($nik, -$visible);
    }
@endphp
<span {{ $attributes }}>{{ $masked }}</span>
