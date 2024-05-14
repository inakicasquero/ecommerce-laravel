<?php

namespace Webkul\DataGrid\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\DataGrid\Contracts\SavedFilter as SavedFilterContract;

class SavedFilter extends Model implements SavedFilterContract
{
    use HasFactory;

    /**
     * Deinfine model table name.
     *
     * @var string
     */
    protected $table = 'saved_filters';

    /**
     * Fillable property for the model.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'src',
        'name',
        'applied',
    ];

    protected $casts = [
        'applied' => 'json',
    ];

    /**
     * Get the order record of Filter.
     */
    public function filter()
    {
        return $this->belongsTo(SavedFilterProxy::modelClass());
    }
}
