<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VisitorExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        return Visitor::with('employee:id,name,department')
            ->when($this->filters['date_from'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '>=', $v))
            ->when($this->filters['date_to'] ?? null, fn ($q, $v) =>
                $q->whereDate('created_at', '<=', $v))
            ->when($this->filters['status'] ?? null, function ($q, $v) {
                match ($v) {
                    'registered'  => $q->whereNull('checkin_at'),
                    'checked_in'  => $q->whereNotNull('checkin_at')->whereNull('checkout_at'),
                    'checked_out' => $q->whereNotNull('checkout_at'),
                    default       => null,
                };
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Tamu', 'Perusahaan', 'No. HP',
            'Host', 'Departemen Host', 'Keperluan',
            'Status', 'Waktu Masuk', 'Waktu Keluar', 'Tanggal Registrasi',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->name,
            $row->company ?? '-',
            $row->phone ?? '-',
            $row->employee?->name ?? '-',
            $row->employee?->department ?? '-',
            $row->purpose ?? '-',
            $row->status_label,
            $row->checkin_at?->format('d/m/Y H:i') ?? '-',
            $row->checkout_at?->format('d/m/Y H:i') ?? '-',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }
}
