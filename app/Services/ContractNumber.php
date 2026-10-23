<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Support\Facades\DB;

class ContractNumber
{
    public static function peek()
    {
        return self::format(self::lastNumber() + 1);
    }

    public static function issue()
    {
        $period = now()->format('Y-m');
        $next = self::lastNumber() + 1;
        $number = self::format($next);

        while (Contract::where('number', $number)->exists()) {
            $next++;
            if ($next > 99) {
                throw new \RuntimeException('Urutan kontrak bulan ini sudah penuh.');
            }
            $number = self::format($next);
        }

        $exists = DB::table('contract_sequences')->where('period', $period)->exists();

        if ($exists) {
            DB::table('contract_sequences')->where('period', $period)->update([
                'last_number' => $next,
            ]);
        } else {
            DB::table('contract_sequences')->insert([
                'period' => $period,
                'last_number' => $next,
            ]);
        }

        return $number;
    }

    public static function withLock(callable $callback)
    {
        $path = storage_path('framework/cache/contract-number.lock');
        $handle = fopen($path, 'c');

        if ($handle === false) {
            throw new \RuntimeException('Nomor kontrak tidak bisa dikunci.');
        }

        try {
            if (! flock($handle, LOCK_EX)) {
                throw new \RuntimeException('Nomor kontrak tidak bisa dikunci.');
            }

            return DB::transaction($callback);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private static function lastNumber()
    {
        return (int) DB::table('contract_sequences')
            ->where('period', now()->format('Y-m'))
            ->value('last_number');
    }

    private static function format($sequence)
    {
        return sprintf('INV/%s/%02d', now()->format('m'), $sequence);
    }
}
