<?php

namespace Koneko\VuexyAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'imageable_type',
        'imageable_id',
        'type',
        'sub_type',
        'url',
        'path',
        'title',
        'description',
        'order',
    ];

    // the list of types values that can be stored in table
    const TYPE_CARD        = 1;
    const TYPE_BANNER      = 2;
    const TYPE_COVER       = 3;
    const TYPE_GALLERY     = 4;
    const TYPE_BANNER_HOME = 5;
    const TYPE_CARD2       = 6;
    const TYPE_BANNER2     = 7;
    const TYPE_COVER2      = 8;

    /**
     * List of names for each types.
     * @var array
     */
    public static $typesList = [
        self::TYPE_CARD        => 'Card',
        self::TYPE_BANNER      => 'Banner',
        self::TYPE_COVER       => 'Cover',
        self::TYPE_GALLERY     => 'Gallery',
        self::TYPE_BANNER_HOME => 'Banner Home',
        self::TYPE_CARD2       => 'Card 2',
        self::TYPE_BANNER2     => 'Banner 2',
        self::TYPE_COVER2      => 'Cover 2',
    ];

    /**
     * Get the parent imageable model (user or post).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
