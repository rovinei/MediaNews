<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class Category extends Model
{

    /**
     * The guard which protect this model and table
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categories';


    /**
     * use with softdeleted, when record deleted this attribute is set to
     * date that it have been deleted
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function mediaTypes(){
        return $this->belongsToMany(
                        'App\Models\CategoryType',
                        'category_type',
                        'category_id',
                        'mediatype_id')->withTimestamps();
    }

    public function createdBy(){
        return $this->belongsTo('App\Models\Admin', 'created_by');
    }

    public function updatedBy(){
        return $this->belongsTo('App\Models\Admin', 'updated_by');
    }

    public function latestArticle()
    {
      return $this->hasOne('App\Models\Post')->where([
                    ['mediatype_id', 1],
                    ['status', 1]
                  ])->latest();
    }

    public function latestArticleSerie(){
        return $this->hasOne('App\Models\PlaylistSerie')
                    ->where(['mediatype_id', 1])
                    ->latest();
    }

    public function latestAudio()
    {
      return $this->hasOne('App\Models\Post')->where([
                    ['mediatype_id', 2],
                    ['status', 1]
                  ])->latest();
    }

    public function latestAudioAlbum(){
        return $this->hasOne('App\Models\PlaylistSerie')
                    ->where('mediatype_id', 2)
                    ->whereHas('posts')
                    ->latest();
    }

    public function latestVideo()
    {
      return $this->hasOne('App\Models\Post')->where([
                    ['mediatype_id', 3],
                    ['status', 1]
                  ])->latest();
    }

    public function latestVideoPlaylist(){
        return $this->hasOne('App\Models\PlaylistSerie')
                    ->where(['mediatype_id', 3])
                    ->latest();
    }

    public function album(){
        $this->hasMany('App\Models\PlaylistSerie');
    }

    public function posts($type=null){
        if(!$type==null){
            return $this->hasMany('App\Models\Post')->where([
                          ['mediatype_id', $type],
                          ['status', 1]
                        ]);
        }

        return $this->hasMany('App\Models\Post')->where('status', 1);
    }


    public function all_posts(){
      return $this->hasMany('App\Models\Post');
    }


    /**
     * @override boot function in order to fire up model events
     * creating, created, update, deleting ...
     *
     */
    public static function boot(){
        parent::boot();

        static::deleting(function($category){
            try {
                $category->mediaTypes()->sync([]);
                $category->all_posts()->each(function($post){
                    try{
                        $post->category()->dissociate();
                    }catch(Exception $e){
                        throw new Exception("Error Processing while remove relationship with posts", 1);
                    }
                });
            } catch (Exception $e) {
                throw new Exception("Error Processing while deleting", 1);
            }
        });
    }
}
