<?php
namespace App\Models\Google\Analytics;

use Google_Model;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EntityUserLink
 * @package App\Models\Google\Analytics
 */
class EntityUserLink extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'google_analytics_entity_user_link';

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'version' => 1
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'version',
        'userLinkId',
        'accountId',
        'selfLink',
        'kind',
        'permissions',
        'created_at',
        'updated_at',
    ];

    /**
     * Find last a account by userLinkId
     *
     * @param string $userLinkId
     * @return User
     */
    public static function findLastByUserLinkId($userLinkId)
    {
        return self::where('userLinkId', $userLinkId)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * Create a new account from Google Model.
     *
     * @param Google_Model $model
     * @return EntityUserLink
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function transform(Google_Model $model)
    {
        $object = $model->toSimpleObject();
        
        // convert object to associative array
        $attributes = json_decode(json_encode($object), TRUE);

        $attributes['userLinkId'] = $attributes['id'];

        $attributes['permissions'] = json_encode($attributes['permissions']);

        unset($attributes['id']);
        
        return $this->newInstance($attributes);
    }

    /**
     * Compare models
     *
     * @param EntityUserLink $entityUserLink
     * @return bool
     */
    public function isDiff($entityUserLink)
    {
        // TODO
        return false;
    }

}
