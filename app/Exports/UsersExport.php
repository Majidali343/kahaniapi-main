<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon; // Import Carbon

class UsersExport implements FromCollection, WithHeadings, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Build the query
        $query = User::query();

        // Apply date filters if provided
        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        // Retrieve relevant fields and format dates
        return $query->get(['name', 'username', 'email', 'phone', 'created_at', ])->map(function ($user) {
            return [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => Carbon::parse($user->created_at)->format('d-m-Y H:i:s'), // Format date

            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'User Name',
            'Email',
            'Phone',
            'Added Date',
            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set the header style
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => Color::COLOR_DARKGREEN],
            ],
        ]);

        // Set the border for the header
        $sheet->getStyle('A1:G1')->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => Color::COLOR_BLACK],
        ]);

        // Set the alignment for all cells
        $sheet->getStyle('A2:G' . $sheet->getHighestRow())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set the font for data rows
        $sheet->getStyle('A2:G' . $sheet->getHighestRow())->applyFromArray([
            'font' => [
                'size' => 12,
            ],
        ]);

        // Set auto column width
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }
}
