<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
Use Maatwebsite\Excel\Concerns\FromView;

class ItemsExport implements FromView
{
    private $items;
    private $user_name;
    private $user_curp;
    private $area_name;
    private $user_position;

    function __construct($items, $user_name, $user_curp, $area_name, $user_position){
        $this->items = $items;
        $this->user_name = $user_name;
        $this->user_curp = $user_curp;
        $this->area_name = $area_name;
        $this->user_position = $user_position;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : View
    {
        return view('exportItems', [
            // 'items' => Item::all()
            'items' => $this->items,
            'user_name' => $this->user_name,
            'user_curp' => $this->user_curp,
            'area_name' => $this->area_name,
            'user_position' => $this->user_position
        ]);
    }
}
