<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Room::firstOrCreate(['nome'=>'A132','assentos'=>45]);
        Room::firstOrCreate(['nome'=>'A241','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A242','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A243','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A249','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A252','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A259','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A266','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A267','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'A268','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'B01','assentos'=>70]);
        Room::firstOrCreate(['nome'=>'B02','assentos'=>70]);
        Room::firstOrCreate(['nome'=>'B03','assentos'=>80]);
        Room::firstOrCreate(['nome'=>'B04','assentos'=>50]);
        Room::firstOrCreate(['nome'=>'B05','assentos'=>150]);
        Room::firstOrCreate(['nome'=>'B06','assentos'=>70]);
        Room::firstOrCreate(['nome'=>'B07','assentos'=>20]);
        Room::firstOrCreate(['nome'=>'B09','assentos'=>100]);
        Room::firstOrCreate(['nome'=>'B10','assentos'=>90]);
        Room::firstOrCreate(['nome'=>'B16','assentos'=>100]);
        Room::firstOrCreate(['nome'=>'B101','assentos'=>100]);
        Room::firstOrCreate(['nome'=>'B138','assentos'=>30]);
        Room::firstOrCreate(['nome'=>'B139','assentos'=>60]);
        Room::firstOrCreate(['nome'=>'B142','assentos'=>60]);
        Room::firstOrCreate(['nome'=>'B143','assentos'=>60]);
        Room::firstOrCreate(['nome'=>'B144','assentos'=>80]);
    }
}
