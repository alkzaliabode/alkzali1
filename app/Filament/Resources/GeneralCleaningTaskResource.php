<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneralCleaningTaskResource\Pages;
use App\Models\GeneralCleaningTask;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Illuminate\Support\HtmlString; // تأكد من استيراد هذا

class GeneralCleaningTaskResource extends Resource
{
    protected static ?string $model = GeneralCleaningTask::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'وحدة النظافة العامة';
    protected static ?string $navigationLabel = 'مهام النظافة العامة';
    protected static ?string $modelLabel = 'مهمة نظافة عامة';
    protected static ?string $pluralModelLabel = 'مهام النظافة العامة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DatePicker::make('date')
                                    ->required()
                                    ->label('التاريخ')
                                    ->default(now()),
                                
                                Select::make('shift')
                                    ->options([
                                        'صباحي' => 'صباحي',
                                        'مسائي' => 'مسائي',
                                        'ليلي' => 'ليلي',
                                    ])
                                    ->required()
                                    ->label('الوجبة'),
                                
                                Select::make('status')
                                    ->options([
                                        'مكتمل' => 'مكتمل',
                                        'قيد التنفيذ' => 'قيد التنفيذ',
                                        'ملغى' => 'ملغى',
                                    ])
                                    ->required()
                                    ->label('الحالة'),
                            ]),
                    ]),
                    
                Section::make('تفاصيل المهمة')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('task_type')
                                    ->options([
                                        'إدامة' => 'إدامة',
                                        'صيانة' => 'صيانة',
                                    ])
                                    ->required()
                                    ->label('نوع المهمة')
                                    ->live(),
                                
                                Select::make('location')
                                    ->options([
                                        // القاعات
                                        'قاعة 1 الأسفل' => 'قاعة 1 الأسفل',
                                        'قاعة 1 الأعلى' => 'قاعة 1 الأعلى',
                                        'قاعة 2 الأسفل' => 'قاعة 2 الأسفل',
                                        'قاعة 2 الأعلى' => 'قاعة 2 الأعلى',
                                        'قاعة 3 الأسفل' => 'قاعة 3 الأسفل',
                                        'قاعة 3 الأعلى' => 'قاعة 3 الأعلى',
                                        'قاعة 4 الأسفل' => 'قاعة 4 الأسفل',
                                        'قاعة 4 الأعلى' => 'قاعة 4 الأعلى',
                                        'قاعة 5 الأسفل' => 'قاعة 5 الأسفل',
                                        'قاعة 5 الأعلى' => 'قاعة 5 الأعلى',
                                        'قاعة 6 الأسفل' => 'قاعة 6 الأسفل',
                                        'قاعة 6 الأعلى' => 'قاعة 6 الأعلى',
                                        'قاعة 7 الأسفل' => 'قاعة 7 الأسفل',
                                        'قاعة 7 الأعلى' => 'قاعة 7 الأعلى',
                                        'قاعة 8 الأسفل' => 'قاعة 8 الأسفل',
                                        'قاعة 8 الأعلى' => 'قاعة 8 الأعلى',
                                        'قاعة 9 الأسفل' => 'قاعة 9 الأسفل',
                                        'قاعة 9 الأعلى' => 'قاعة 9 الأعلى',
                                        'قاعة 10 الأسفل' => 'قاعة 10 الأسفل',
                                        'قاعة 10 الأعلى' => 'قاعة 10 الأعلى',
                                        'قاعة 11 الأسفل' => 'قاعة 11 الأسفل',
                                        'قاعة 11 الأعلى' => 'قاعة 11 الأعلى',
                                        'قاعة 12 الأسفل' => 'قاعة 12 الأسفل',
                                        'قاعة 12 الأعلى' => 'قاعة 12 الأعلى',
                                        'قاعة 13 الأسفل' => 'قاعة 13 الأسفل',
                                        'قاعة 13 الأعلى' => 'قاعة 13 الأعلى',
                                        // المواقع الأخرى
                                        'الترامز' => 'الترامز',
                                        'السجاد' => 'السجاد',
                                        'الحاويات' => 'الحاويات',
                                        'الجامع' => 'الجامع',
                                        'المركز الصحي' => 'المركز الصحي',
                                    ])
                                    ->required()
                                    ->label('الموقع')
                                    ->searchable()
                                    ->live(),
                            ]),
                            
                        Fieldset::make('تفاصيل التنفيذ')
                            ->schema(function ($get) {
                                $location = $get('location');
                                $fields = [];
                                
                                if (str_contains($location, 'قاعة')) {
                                    $fields[] = Grid::make(4)
                                        ->schema([
                                            TextInput::make('mats_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد المنادر المدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('pillows_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد الوسادات المدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('fans_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد المراوح المدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('windows_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد النوافذ المدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('carpets_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد السجاد المدام')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('blankets_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد البطانيات المدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('beds_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد الأسرة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('beneficiaries_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد المستفيدين من القاعة')
                                                ->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الترامز') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('filled_trams_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد الترامز المملوئة والمدامة')
                                                ->columnSpan(1),
                                        ]);
                                } elseif ($location === 'السجاد') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('carpets_laid_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد السجاد المفروش في الساحات')
                                                ->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الحاويات') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('large_containers_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد الحاويات الكبيرة المفرغة والمدامة')
                                                ->columnSpan(1),
                                                
                                            TextInput::make('small_containers_count')
                                                ->numeric()
                                                ->minValue(0)
                                                ->label('عدد الحاويات الصغيرة المفرغة والمدامة')
                                                ->columnSpan(1),
                                        ]);
                                } elseif ($location === 'الجامع' || $location === 'المركز الصحي') {
                                    $fields[] = Grid::make(2)
                                        ->schema([
                                            TextInput::make('maintenance_details')
                                                ->label('تفاصيل الإدامة اليومية')
                                                ->columnSpan(2),
                                        ]);
                                }
                                
                                return $fields;
                            })
                            ->hidden(fn ($get) => empty($get('location'))),
                    ]),
                    
                Section::make('الموارد المستخدمة')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('resources_used')
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('اسم المورد')
                                    ->required()
                                    ->columnSpan(2),
                                    
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->label('الكمية')
                                    ->required(),
                                    
                                Select::make('unit')
                                    ->label('وحدة القياس')
                                    ->options([
                                        'قطعة' => 'قطعة',
                                        'كرتون' => 'كرتون',
                                        'رول' => 'رول',
                                        'لتر' => 'لتر',
                                        'عبوة' => 'عبوة',
                                        'أخرى' => 'أخرى',
                                    ])
                                    ->required(),
                                    
                                TextInput::make('working_hours')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(24)
                                    ->label('ساعات العمل')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->createItemButtonLabel('إضافة مورد جديد')
                            ->defaultItems(1),
                    ]),
                    
                Section::make('المنفذون والتقييم')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('employeeTasks')
                            ->label('')
                            ->relationship('employeeTasks')
                            ->schema([
                                Select::make('employee_id')
                                    ->label('الموظف')
                                    ->options(fn () => Employee::query()
                                        ->orderBy('name')
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(2),
                                    
                                Select::make('employee_rating')
                                    ->label('تقييم الأداء')
                                    ->options([
                                        1 => '★ (ضعيف)',
                                        2 => '★★',
                                        3 => '★★★ (متوسط)',
                                        4 => '★★★★',
                                        5 => '★★★★★ (ممتاز)',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('إضافة منفذ جديد'),
                    ]),
                    
                Section::make('المرفقات')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        FileUpload::make('before_images')
                            ->label('صور قبل التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('general_cleaning_tasks/before')
                            ->imageEditor()
                            ->columnSpan(1),
                            
                        FileUpload::make('after_images')
                            ->label('صور بعد التنفيذ')
                            ->image()
                            ->multiple()
                            ->directory('general_cleaning_tasks/after')
                            ->imageEditor()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('task_type')
                    ->label('نوع المهمة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'إدامة' => 'info',
                        'صيانة' => 'warning',
                        default => 'gray',
                    }),
                    
                // بدء تأثير التمرير (Hover Effect) باستخدام Alpine.js
                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع')
                    ->searchable()
                    ->sortable()
                    ->html() // مهم: يسمح بعرض HTML
                    ->formatStateUsing(function (string $state): HtmlString {
                        // أيقونة Heroicon "truck" (شاحنة)
                        // استخدم h-5 w-5 مباشرة على الـ SVG
                        $iconSvg = '
                            <svg class="h-5 w-5 text-blue-600 transition duration-300 transform group-hover:scale-125" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> 
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18L10.5 20.25M12 10.5H16.5M16.5 6V12M16.5 6L14.25 3.75M9.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10.5M15.75 21H10.5V18.75H15.75V21Z" /> 
                            </svg>
                        ';
                        
                        return new HtmlString(
                            '<div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="group inline-flex items-center justify-center relative cursor-pointer min-w-[70px] h-full overflow-hidden">' . 
                                '<span x-show="!hovered" x-transition:opacity.duration.300 class="text-gray-800 text-center w-full">' . $state . '</span>' .
                                '<span x-show="hovered" x-transition:opacity.duration.300.delay-50 class="absolute inset-0 flex items-center justify-center w-full h-full">' . $iconSvg . '</span>' .
                            '</div>'
                        );
                    }),
                // نهاية تأثير التمرير
                    
                Tables\Columns\TextColumn::make('shift')
                    ->label('الوجبة')
                    ->toggleable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'مكتمل',
                        'warning' => 'قيد التنفيذ',
                        'danger' => 'ملغى',
                    ]),
                    
                Tables\Columns\TextColumn::make('mats_count')
                    ->label('عدد المنادر المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('pillows_count')
                    ->label('عدد الوسادات المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('fans_count')
                    ->label('عدد المراوح المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('windows_count')
                    ->label('عدد النوافذ المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('carpets_count')
                    ->label('عدد السجاد المدام')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('blankets_count')
                    ->label('عدد البطانيات المدامة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('beds_count')
                    ->label('عدد الأسرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('beneficiaries_count')
                    ->label('عدد المستفيدين')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('filled_trams_count')
                    ->label('عدد الترامز المملوئة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('carpets_laid_count')
                    ->label('عدد السجاد المفروش')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('large_containers_count')
                    ->label('عدد الحاويات الكبيرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('small_containers_count')
                    ->label('عدد الحاويات الصغيرة')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('maintenance_details')
                    ->label('تفاصيل الإدامة')
                    ->words(10)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('employeeTasks.employee.name')
                    ->label('المنفذون')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    // تعديل اسم الفئة للتقييم "جيد جداً"
                    ->formatStateUsing(function ($state, $record) {
                        $summary = '';
                        foreach ($record->employeeTasks as $employeeTask) {
                            $employeeName = $employeeTask->employee->name ?? 'غير معروف';
                            $rating = $employeeTask->employee_rating;
                            // تحويل التقييم العددي إلى نص ورمز نجمة
                            $ratingText = match ($rating) {
                                1 => 'ضعيف ★',
                                2 => '★★',
                                3 => 'متوسط ★★★',
                                4 => '★★★★',
                                5 => 'ممتاز ★★★★★',
                                default => 'غير مقيم',
                            };
                            // استخدام اسم فئة صحيح (مع شرطة بدلاً من المسافة)
                            $ratingClass = 'rating-' . str_replace([' ', '★'], ['-', ''], $ratingText); // إزالة النجوم والمسافات
                            $summary .= '<div class="task-item">' . $employeeName . ' (<span class="' . $ratingClass . '">' . $ratingText . '</span>)</div>';
                        }
                        return new HtmlString($summary);
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('task_type')
                    ->label('نوع المهمة')
                    ->options([
                        'إدامة' => 'إدامة',
                        'صيانة' => 'صيانة',
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة المهمة')
                    ->options([
                        'مكتمل' => 'مكتمل',
                        'قيد التنفيذ' => 'قيد التنفيذ',
                        'ملغى' => 'ملغى',
                    ]),
                    
                Tables\Filters\SelectFilter::make('shift')
                    ->label('الوجبة')
                    ->options([
                        'صباحي' => 'صباحي',
                        'مسائي' => 'مسائي',
                        'ليلي' => 'ليلي',
                    ]),
                    
                Tables\Filters\SelectFilter::make('location')
                    ->label('الموقع')
                    ->options([
                        // قاعات
                        'قاعة 1 الأسفل' => 'قاعة 1 الأسفل',
                        'قاعة 1 الأعلى' => 'قاعة 1 الأعلى',
                        'قاعة 2 الأسفل' => 'قاعة 2 الأسفل',
                        'قاعة 2 الأعلى' => 'قاعة 2 الأعلى',
                        'قاعة 3 الأسفل' => 'قاعة 3 الأسفل',
                        'قاعة 3 الأعلى' => 'قاعة 3 الأعلى',
                        'قاعة 4 الأسفل' => 'قاعة 4 الأسفل',
                        'قاعة 4 الأعلى' => 'قاعة 4 الأعلى',
                        'قاعة 5 الأسفل' => 'قاعة 5 الأسفل',
                        'قاعة 5 الأعلى' => 'قاعة 5 الأعلى',
                        'قاعة 6 الأسفل' => 'قاعة 6 الأسفل',
                        'قاعة 6 الأعلى' => 'قاعة 6 الأعلى',
                        'قاعة 7 الأسفل' => 'قاعة 7 الأسفل',
                        'قاعة 7 الأعلى' => 'قاعة 7 الأعلى',
                        'قاعة 8 الأسفل' => 'قاعة 8 الأسفل',
                        'قاعة 8 الأعلى' => 'قاعة 8 الأعلى',
                        'قاعة 9 الأسفل' => 'قاعة 9 الأسفل',
                        'قاعة 9 الأعلى' => 'قاعة 9 الأعلى',
                        'قاعة 10 الأسفل' => 'قاعة 10 الأسفل',
                        'قاعة 10 الأعلى' => 'قاعة 10 الأعلى',
                        'قاعة 11 الأسفل' => 'قاعة 11 الأسفل',
                        'قاعة 11 الأعلى' => 'قاعة 11 الأعلى',
                        'قاعة 12 الأسفل' => 'قاعة 12 الأسفل',
                        'قاعة 12 الأعلى' => 'قاعة 12 الأعلى',
                        'قاعة 13 الأسفل' => 'قاعة 13 الأسفل',
                        'قاعة 13 الأعلى' => 'قاعة 13 الأعلى',
                        // ... جميع القاعات الأخرى
                        'الترامز' => 'الترامز',
                        'السجاد' => 'السجاد',
                        'الحاويات' => 'الحاويات',
                        'الجامع' => 'الجامع',
                        'المركز الصحي' => 'المركز الصحي',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                    
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
                    ->label('تصدير البيانات'),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralCleaningTasks::route('/'),
            'create' => Pages\CreateGeneralCleaningTask::route('/create'),
            'edit' => Pages\EditGeneralCleaningTask::route('/{record}/edit'),
        ];
    }
}
