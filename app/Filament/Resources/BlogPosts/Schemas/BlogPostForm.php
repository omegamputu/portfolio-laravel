<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Models\BlogPost;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)->schema([
                    //
                    Section::make('Blog Post Details')->schema([
                        //
                        TextInput::make('title')
                            ->placeholder('Enter the blog post title')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (?string $state, callable $set, $record) {
                                if (! $state) {
                                    return;
                                }

                                $set('slug', self::generateUniqueSlug($state, $record?->id));
                            }),
                        
                        TextInput::make('slug') 
                            ->required()
                            ->unique('blog_posts', 'slug', ignoreRecord: true)
                            ->helperText('Auto-generated from the title. You can still adjust it manually if needed.'),
                        // Other form components can be added here
                        RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                        
                        FileUpload::make('cover_image')
                            ->image()
                            ->disk('public')
                            ->directory('blog/covers')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),
                    
                    Section::make('Publishing Details')->schema([
                        //
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->live(),
                        
                        DateTimePicker::make('published_at')
                            //->native(false)
                            //->weekStartsOnMonday()
                            ->visible(fn (callable $get) => $get('status') === 'published')
                            ->required(fn (callable $get) => $get('status') === 'published')
                            ->seconds(false),
                        
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->required()
                            ->default(fn () => auth()->id()),
                    ]),
                ]),

                Grid::make(1)->schema([
                    //
                    Section::make('Category & Tags')->schema([
                        //
                        Select::make('category')
                            ->relationship('category', 'name')
                            ->label('Category')
                            ->searchable()
                            ->preload(),
                        
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),

                    Section::make('SEO Details')->schema([
                        //
                        TextInput::make('seo_title')
                            ->maxLength(70)
                            ->helperText('Optimal length is 50-70 characters.'),
                        
                        Textarea::make('seo_description')
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText('Optimal length is 150-160 characters.'),
                    ])->collapsed(true),
                ]),
            ]);
    }

     private static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        $query = BlogPost::query();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ((clone $query)->where('slug', '=', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
