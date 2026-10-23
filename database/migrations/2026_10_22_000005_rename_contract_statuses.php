<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameContractStatuses extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        DB::table('contracts')->where('status', 'menunggu')->update(['status' => 'pending']);
        DB::table('contracts')->where('status', 'disetujui')->update(['status' => 'approved']);
        DB::table('contracts')->where('status', 'ditolak')->update(['status' => 'rejected']);
    }

    /**
     * @return void
     */
    public function down()
    {
        DB::table('contracts')->where('status', 'pending')->update(['status' => 'menunggu']);
        DB::table('contracts')->where('status', 'approved')->update(['status' => 'disetujui']);
        DB::table('contracts')->where('status', 'rejected')->update(['status' => 'ditolak']);
    }
}
