<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomJobResource\Pages;
use App\Filament\Resources\CustomJobResource\RelationManagers;
use App\Models\CustomJob;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomJobResource extends Resource
{
    protected static ?string $model = CustomJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pid')
                    ->required()
                    ->integer(),
                Forms\Components\Select::make('priority')
                    ->options([
                        1 => 'Low',
                        2 => 'Medium',
                        3 => 'High',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        CustomJob::ERROR => 'Error',
                        CustomJob::SUCCESS => 'Success',
                        CustomJob::RUNNING => 'Running',
                        CustomJob::QUEUED => 'Queued',
                        CustomJob::CANCELED => 'Canceled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('payload')
                    ->required(),
                Forms\Components\TextInput::make('attempts')
                    ->required()
                    ->integer(),
                Forms\Components\DateTimePicker::make('finished_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('pid')->sortable(),
            Tables\Columns\TextColumn::make('priority')->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->colors([
                    'danger' => CustomJob::ERROR,
                    'success' => CustomJob::SUCCESS,
                    'warning' => CustomJob::RUNNING,
                    'secondary' => CustomJob::QUEUED,
                    'primary' => CustomJob::CANCELED,
                ]),
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('attempts'),
            Tables\Columns\TextColumn::make('finished_at')
                ->dateTime(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    CustomJob::ERROR => 'Error',
                    CustomJob::SUCCESS => 'Success',
                    CustomJob::RUNNING => 'Running',
                    CustomJob::QUEUED => 'Queued',
                    CustomJob::CANCELED => 'Canceled',
                ]),
        ])

        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomJobs::route('/'),
            'create' => Pages\CreateCustomJob::route('/create'),
            'edit' => Pages\EditCustomJob::route('/{record}/edit'),
        ];
    }
}
