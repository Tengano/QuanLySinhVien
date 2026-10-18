<?php

namespace App\Services;

final class DiemService
{
    /**
     * @return array{diem_tb: float|null, diem_chu: string|null, diem_he4: float|null, dat: bool|null}
     */
    public function tinh(?float $quaTrinh, ?float $thi): array
    {
        if ($quaTrinh === null || $thi === null) {
            return ['diem_tb' => null, 'diem_chu' => null, 'diem_he4' => null, 'dat' => null];
        }

        $tb = round(
            $quaTrinh * (float) config('diem.trong_so_qua_trinh')
            + $thi * (float) config('diem.trong_so_thi'),
            2
        );

        $quyDoi = $this->quyDoi($tb);

        return [
            'diem_tb' => $tb,
            'diem_chu' => $quyDoi['chu'],
            'diem_he4' => (float) $quyDoi['he4'],
            'dat' => $tb >= (float) config('diem.diem_dat'),
        ];
    }

    /**
     * @return array{chu: string, he4: float}
     */
    public function quyDoi(float $diemTb): array
    {
        foreach (config('diem.quy_doi') as $row) {
            if ($diemTb >= (float) $row['tu']) {
                return ['chu' => $row['chu'], 'he4' => (float) $row['he4']];
            }
        }

        return ['chu' => 'F', 'he4' => 0.0];
    }
}
