<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * @package App\Models
 */
class File extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'files';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'extension',
        'storage_path',
    ];
}
