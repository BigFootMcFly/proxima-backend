<?php

namespace App\Filament\Resources\RemainderResource\Pages;

use App\Filament\Resources\RemainderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Concerns\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ListRemainders extends ListRecords
{
    use HasFilters;

    protected static string $resource = RemainderResource::class;

    //---------------------------------------------------------------------------------------------------------------
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    //---------------------------------------------------------------------------------------------------------------
    public function getTabs(): array
    {
        $intervals = [
            'all' => [
                now()->subMillenniaNoOverflow()->startOfMillennium(),
                now()->addMillenniaNoOverflow()->endOfMillennium(),
            ],
            'today' => [
                now()->startOfDay(),
                now()->endOfDay(),
            ],
            'this_week' => [ //NOTE: does not includes previous dates
                now()->endOfDay(),
                now()->endOfWeek(),
            ],
            'this_month' => [ //NOTE: does not includes previous dates
                now()->endOfWeek(),
                now()->endOfMonth(),
            ],
            'this_year' => [ //NOTE: does not includes previous dates
                now()->endOfMonth(),
                now()->endOfYear(),
            ],
            'later' => [ //NOTE: does not includes previous dates
                now()->endOfYear(),
                now()->addMillenniaNoOverflow()->endOfMillennium(),
            ],
            'overdue' => [
                now()->subMillenniaNoOverflow()->startOfMillennium(),
                now()->startOfHour(), //NOTE: no need for higher precision for now
            ],
        ];

        $tabs = array_map($this->intervalTab(...), $intervals);
        $tabs['overdue']->badgeColor('danger');
        return $tabs;
    }

    //---------------------------------------------------------------------------------------------------------------
    /**
     * Creates a filter tab for the table
     *
     * @param array $interval The interval to filter Example: [min:max]
     *
     * @return Tab The filter Tab
     *
     */
    protected function intervalTab(array $interval): Tab
    {
        return Tab::make()
            ->modifyQueryUsing(
                fn (Builder $query): Builder =>
                $query->whereBetween('due_at', $interval)
            )
            ->badge(function () use ($interval) {
                $query = $this
                    ->applyFiltersToTableQuery(static::getResource()::getEloquentQuery())
                    ->whereBetween('due_at', $interval)
                ;

                //NOTE: cache is mainly used to get rid of duplicate queries by filament
                return cache()->remember(
                    key: 'badge_'.crc32($query->toRawSql()),
                    ttl: 5,
                    callback: fn () => Number::format($query->count())
                );
            });
    }

}
