<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $users = [
            'sinta' => $this->user('Sinta Wijaya', 'supervisor@mail.com', 'supervisor', 'supervisor'),
            'raka' => $this->user('Raka Putra', 'staff@mail.com', 'staff123', 'staff'),
            'dimas' => $this->user('Dimas Aji', 'dimas@mail.com', 'staff123', 'staff'),
        ];

        $stored = [];
        foreach ($this->rows() as $row) {
            $contract = $this->storeContract($row, $users);
            $stored[$contract->number] = $contract;
        }

        $this->sequences();

        $this->activity($users['sinta'], $stored['INV/10/02'], 'Sinta Wijaya menyetujui INV/10/02', '2026-10-08 15:05:00');
        $this->activity(null, $stored['INV/10/05'], 'Sistem membuat nomor INV/10/05', '2026-10-21 11:20:00');
        $this->activity($users['raka'], $stored['INV/10/04'], 'Raka Putra mengunggah lampiran INV/10/04', now());
    }

    private function user($name, $email, $password, $role)
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);
    }

    private function rows()
    {
        return [
            $this->row('INV/10/04', 'Sewa kapasitas VSAT site Balikpapan', '2026-10-12', '2026-10-12 09:14:00', 'pending', 'raka', [
                ['name' => 'Kontrak_VSAT_Balikpapan.pdf', 'kind' => 'pdf', 'today' => true],
            ]),
            $this->row('INV/10/03', 'Perpanjangan link VSAT kapal niaga', '2026-10-09', '2026-10-09 11:20:00', 'pending', 'raka', [
                ['name' => 'Kontrak_kapal_niaga.pdf', 'kind' => 'pdf'],
                ['name' => 'Lampiran_rute_kapal.png', 'kind' => 'image'],
            ]),
            $this->row('INV/10/02', 'Instalasi VSAT kantor cabang Makassar', '2026-10-05', '2026-10-05 08:40:00', 'approved', 'raka', $this->pdf('INV/10/02'), [
                'approver' => 'sinta',
                'decided_at' => '2026-10-08 15:05:00',
            ]),
            $this->row('INV/09/18', 'Relokasi antena site Sorong', '2026-09-21', '2026-09-21 10:00:00', 'rejected', 'raka', [
                ['name' => 'Foto_antena_Sorong.png', 'kind' => 'image'],
            ], [
                'approver' => 'sinta',
                'decided_at' => '2026-09-24 13:40:00',
                'reason' => 'Koordinat site belum diverifikasi ulang.',
            ]),
            $this->row('INV/09/14', 'Kontrak bandwidth backbone Papua', '2026-09-18', '2026-09-18 08:20:00', 'approved', 'dimas', $this->pdf('INV/09/14'), [
                'approver' => 'sinta',
                'decided_at' => '2026-09-20 16:10:00',
            ]),
            $this->row('INV/10/01', 'Sewa transponder link site Tarakan', '2026-10-02', '2026-10-02 10:00:00', 'pending', 'raka', $this->pdf('INV/10/01', true)),
            $this->row('INV/10/05', 'Kalibrasi antena site Pekanbaru', '2026-10-16', '2026-10-21 11:20:00', 'pending', 'raka', $this->pdf('INV/10/05', true)),
            $this->row('INV/10/07', 'Kontrak uplink site Timika', '2026-10-19', '2026-10-19 08:50:00', 'pending', 'raka', $this->pdf('INV/10/07', true)),
            $this->row('INV/10/08', 'Penambahan bandwidth site Ambon', '2026-10-20', '2026-10-20 13:30:00', 'pending', 'raka', $this->pdf('INV/10/08', true)),
            $this->row('INV/10/09', 'Instalasi VSAT rig lepas pantai', '2026-10-21', '2026-10-21 16:40:00', 'pending', 'dimas', $this->pdf('INV/10/09')),
            $this->row('INV/08/11', 'Sewa kapasitas site Pontianak', '2026-08-14', '2026-08-14 09:00:00', 'pending', 'raka', $this->pdf('INV/08/11')),
            $this->approved('INV/10/14', 'Perpanjangan SLA hub Jakarta', '2026-10-01', '2026-10-01 09:00:00', '2026-10-02 11:10:00', 'raka'),
            $this->approved('INV/10/15', 'Sewa bandwidth site Manado', '2026-10-03', '2026-10-03 10:15:00', '2026-10-04 16:40:00', 'dimas'),
            $this->approved('INV/10/16', 'Kontrak pemeliharaan antena site Medan', '2026-10-04', '2026-10-04 08:05:00', '2026-10-06 09:30:00', 'raka'),
            $this->approved('INV/10/17', 'Instalasi VSAT tambang Sumbawa', '2026-10-06', '2026-10-06 13:20:00', '2026-10-07 10:00:00', 'dimas'),
            $this->approved('INV/10/18', 'Penambahan carrier site Jayapura', '2026-10-07', '2026-10-07 09:45:00', '2026-10-09 14:15:00', 'raka'),
            $this->approved('INV/10/19', 'Relokasi terminal site Palu', '2026-10-08', '2026-10-08 11:00:00', '2026-10-10 09:20:00', 'dimas'),
            $this->approved('INV/10/20', 'Sewa kapasitas backhaul site Batam', '2026-10-10', '2026-10-10 08:30:00', '2026-10-11 15:45:00', 'raka'),
            $this->approved('INV/10/21', 'Kontrak dukungan NOC 24 jam', '2026-10-11', '2026-10-11 14:10:00', '2026-10-13 10:05:00', 'dimas'),
            $this->approved('INV/10/22', 'Upgrade modem site Samarinda', '2026-10-13', '2026-10-13 09:25:00', '2026-10-14 16:00:00', 'raka'),
            $this->approved('INV/10/23', 'Sewa transponder Ku-band', '2026-10-14', '2026-10-14 10:40:00', '2026-10-15 11:30:00', 'dimas'),
            $this->approved('INV/10/24', 'Instalasi VSAT kapal patroli', '2026-10-15', '2026-10-15 08:55:00', '2026-10-16 13:20:00', 'raka'),
            $this->approved('INV/10/25', 'Perpanjangan site Kupang', '2026-10-16', '2026-10-16 15:10:00', '2026-10-17 09:40:00', 'dimas'),
            $this->approved('INV/10/26', 'Kontrak energi site remote Natuna', '2026-10-17', '2026-10-17 09:00:00', '2026-10-18 14:25:00', 'raka'),
            $this->rejected('INV/10/10', 'Relokasi antena site Banjarmasin', '2026-10-06', '2026-10-06 16:00:00', '2026-10-11 10:20:00', 'raka', 'Lampiran NPWP buram, mohon unggah ulang.'),
            $this->rejected('INV/10/11', 'Sewa kapasitas site Palembang', '2026-10-09', '2026-10-09 09:30:00', '2026-10-15 11:05:00', 'dimas', 'Jadwal aktivasi bentrok dengan pemeliharaan hub.'),
            $this->rejected('INV/10/12', 'Perpanjangan link site Kendari', '2026-10-12', '2026-10-12 14:45:00', '2026-10-18 09:15:00', 'raka', 'Nilai kontrak tidak sesuai lampiran penawaran.'),
        ];
    }

    private function approved($number, $subject, $activation, $created, $decided, $creator)
    {
        return $this->row($number, $subject, $activation, $created, 'approved', $creator, $this->pdf($number), [
            'approver' => 'sinta',
            'decided_at' => $decided,
        ]);
    }

    private function rejected($number, $subject, $activation, $created, $decided, $creator, $reason)
    {
        return $this->row($number, $subject, $activation, $created, 'rejected', $creator, $this->pdf($number), [
            'approver' => 'sinta',
            'decided_at' => $decided,
            'reason' => $reason,
        ]);
    }

    private function pdf($number, $today = false)
    {
        return [[
            'name' => 'Kontrak_'.str_replace('/', '_', $number).'.pdf',
            'kind' => 'pdf',
            'today' => $today,
        ]];
    }

    private function row($number, $subject, $activation, $created, $status, $creator, array $files, array $extra = [])
    {
        return array_merge([
            'number' => $number,
            'subject' => $subject,
            'activation' => $activation,
            'created_at' => $created,
            'status' => $status,
            'creator' => $creator,
            'files' => $files,
        ], $extra);
    }

    private function storeContract(array $row, array $users)
    {
        $contract = new Contract();
        $contract->number = $row['number'];
        $contract->subject = $row['subject'];
        $contract->activation_date = $row['activation'];
        $contract->created_by = $users[$row['creator']]->id;
        $contract->status = $row['status'];
        $contract->approved_by = isset($row['approver']) ? $users[$row['approver']]->id : null;
        $contract->approved_at = isset($row['decided_at']) ? $row['decided_at'] : null;
        $contract->rejection_reason = isset($row['reason']) ? $row['reason'] : null;
        $contract->created_at = $row['created_at'];
        $contract->updated_at = $row['created_at'];
        $contract->save();

        foreach ($row['files'] as $file) {
            $this->attachFile($contract, $file);
        }

        return $contract;
    }

    private function attachFile(Contract $contract, array $file)
    {
        if ($file['kind'] === 'image') {
            $bytes = $this->png($contract->number, $contract->subject);
            $mime = 'image/png';
        } else {
            $bytes = $this->makePdf($contract->number, $contract->subject);
            $mime = 'application/pdf';
        }

        $path = 'contracts/'.$contract->id.'/'.$file['name'];
        Storage::disk('local')->put($path, $bytes);
        $at = ! empty($file['today']) ? now() : $contract->created_at;

        $document = new ContractDocument();
        $document->contract_id = $contract->id;
        $document->original_name = $file['name'];
        $document->path = $path;
        $document->mime = $mime;
        $document->size = strlen($bytes);
        $document->created_at = $at;
        $document->updated_at = $at;
        $document->save();
    }

    private function sequences()
    {
        $max = [];

        foreach (Contract::all() as $contract) {
            if (! preg_match('/^INV\/(\d{2})\/(\d+)$/', $contract->number, $matches)) {
                continue;
            }
            $period = $contract->created_at->format('Y').'-'.$matches[1];
            $sequence = (int) $matches[2];
            if (! isset($max[$period]) || $sequence > $max[$period]) {
                $max[$period] = $sequence;
            }
        }

        foreach ($max as $period => $last) {
            DB::table('contract_sequences')->insert([
                'period' => $period,
                'last_number' => $last,
            ]);
        }
    }

    private function activity($user, Contract $contract, $description, $at)
    {
        $activity = new Activity();
        $activity->user_id = $user ? $user->id : null;
        $activity->contract_id = $contract->id;
        $activity->description = $description;
        $activity->created_at = $at;
        $activity->updated_at = $at;
        $activity->save();
    }

    private function makePdf($number, $title)
    {
        $number = $this->pdfEscape($number);
        $title = $this->pdfEscape($this->short($title, 78));
        $stream = "BT /F1 22 Tf 48 760 Td (SKYREACH) Tj ET\n"
            ."BT /F1 10 Tf 48 736 Td (Everywhere Excellence) Tj ET\n"
            ."BT /F1 13 Tf 48 680 Td ($number) Tj ET\n"
            ."BT /F1 12 Tf 48 650 Td ($title) Tj ET\n"
            ."BT /F1 11 Tf 48 610 Td (Dokumen pendukung kontrak internal.) Tj ET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            '<< /Length '.strlen($stream)." >>\nstream\n".$stream.'endstream',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $body) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$body."\nendobj\n";
        }

        $xref = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 ".$count."\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < $count; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size ".$count." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }

    private function png($number, $title)
    {
        $image = imagecreatetruecolor(800, 1040);
        $navy = imagecolorallocate($image, 2, 60, 142);
        $ink = imagecolorallocate($image, 14, 26, 43);
        $gold = imagecolorallocate($image, 239, 166, 70);
        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocate($image, 98, 114, 137);
        $canvas = imagecolorallocate($image, 244, 247, 251);

        imagefilledrectangle($image, 0, 0, 800, 1040, $white);
        imagefilledrectangle($image, 0, 0, 800, 160, $navy);
        imagefilledrectangle($image, 48, 132, 196, 138, $gold);
        imagefilledrectangle($image, 48, 220, 752, 860, $canvas);

        $font = null;
        foreach (['/System/Library/Fonts/Supplemental/Arial.ttf', '/Library/Fonts/Arial.ttf'] as $path) {
            if (is_file($path)) {
                $font = $path;
                break;
            }
        }

        if ($font) {
            imagettftext($image, 28, 0, 48, 78, $white, $font, 'SKYREACH');
            imagettftext($image, 14, 0, 48, 110, $gold, $font, 'Everywhere Excellence');
            imagettftext($image, 18, 0, 72, 280, $navy, $font, $number);
            $this->writeLines($image, 16, 72, 330, $ink, $font, $title, 640);
            imagettftext($image, 13, 0, 72, 820, $muted, $font, 'Dokumen pendukung kontrak internal');
        } else {
            imagestring($image, 5, 48, 60, 'SKYREACH '.$number, $white);
            imagestring($image, 4, 72, 260, $number, $navy);
        }

        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    private function writeLines($image, $size, $x, $y, $color, $font, $text, $maxWidth)
    {
        $line = '';
        foreach (preg_split('/\s+/', $text) as $word) {
            $trial = trim($line.' '.$word);
            $box = imagettfbbox($size, 0, $font, $trial);
            $width = $box ? $box[2] - $box[0] : 0;
            if ($line !== '' && $width > $maxWidth) {
                imagettftext($image, $size, 0, $x, $y, $color, $font, $line);
                $y += $size + 8;
                $line = $word;
            } else {
                $line = $trial;
            }
        }
        if ($line !== '') {
            imagettftext($image, $size, 0, $x, $y, $color, $font, $line);
        }
    }

    private function pdfEscape($text)
    {
        $converted = function_exists('iconv')
            ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text)
            : $text;
        if ($converted === false || $converted === null) {
            $converted = preg_replace('/[^\x20-\x7E]/', '', $text);
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $converted);
    }

    private function short($text, $limit)
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return mb_substr($text, 0, $limit - 3).'...';
    }
}
