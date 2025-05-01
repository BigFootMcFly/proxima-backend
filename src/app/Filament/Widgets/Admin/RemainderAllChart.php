<?php

namespace App\Filament\Widgets\Admin;

use App\Models\Remainder;
//use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RemainderAllChart extends ChartWidget
{
    protected static ?string $heading = 'All Remainders due at';

    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 3;

    protected static string $color = 'info';

    protected function getData(): array
    {
        $data = Trend::model(Remainder::class)
        ->dateColumn('due_at')
        ->between(
            start: Remainder::getFirstDueAt()?->due_at ?? Carbon::now(),
            end: Remainder::getLastDueAt()?->due_at ?? Carbon::now()->addMonth(),
        )
        ->perMonth()
        ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Remainders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'tension' =>  1,

                ],
            ],

            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                ],
            ],
        ];
    }

    public function getDescription(): ?string
    {
        return 'The number of remainders due at per month.';
    }
}
