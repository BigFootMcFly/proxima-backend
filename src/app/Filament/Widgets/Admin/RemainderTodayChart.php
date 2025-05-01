<?php

namespace App\Filament\Widgets\Admin;

use App\Models\Remainder;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RemainderTodayChart extends ChartWidget
{
    protected static ?string $heading = 'Todays Remainders due at';

    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = 2;

    protected static string $color = 'success';

    //protected int | string | array $columnSpan = 2;

    //protected static ?string $maxHeight = '10rem';

    protected function getData(): array
    {
        $data = Trend::model(Remainder::class)
        ->dateColumn('due_at')
        ->between(
            start: Carbon::today(),
            end: Carbon::today()->endOfDay(),
        )
        ->perHour()
        ->count()
        ;

        return [
            'datasets' => [
                [
                    'label' => 'Remainders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => true,
                    'tension' =>  0.5,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => explode(' ', $value->date)[1]),
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
        return 'The number of remainders due at today.';
    }

}
