<?php

namespace App\Exports;

use App\Models\Voucher;
use Maatwebsite\Excel\Concerns\FromCollection;

class VouchersExport implements FromCollection
{
    protected $voucher_order_id;
    public function __construct($voucher_order_id)
    {
        $this->voucher_order_id = $voucher_order_id;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Voucher::where('order_id',$this->voucher_order_id)->get();
    }
    public function map($voucher): array
    {
        return [
            $voucher->code
        ];
    }
}
