<?php
namespace App\Models\Google\Analytics;

use Google_Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class Account
 * @package App\Models\Google\Analytics
 */
class Account extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'google_analytics_accounts';

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
        'account_id',
        'kind',
        'selfLink',
        'name',
        'permissions',
        'starred',
        'created_at',
        'updated_at',
    ];

    /**
     * Find last a account by accountId
     *
     * @param string $accountId
     * @return Account
     */
    public static function findLastByAccountId($accountId)
    {
        return self::where('account_id', $accountId)
            ->orderBy('version', 'desc')
            ->first();
    }

    /**
     * Find accounts by accountId
     *
     * @param string $accountId
     * @return Account
     */
    public static function findByAccountId($accountId)
    {
        return self::where('account_id', $accountId)
            ->orderBy('version', 'desc');
    }

    /**
     * Apply request params to the builder instance.
     *
     * @param Request $request
     * @param Builder|null $builder
     * @param string|null $roleName
     * @return \Illuminate\Support\Collection
     */
    public static function filter(Request $request, Builder $builder = null, $roleName = null)
    {
        $query = $builder ?? self::query();
        $query = self::applyFilters($query, $request->input('filter'));
        return $query;
    }

    /**
     * @param Builder $query
     * @param array|null $filters
     * @return Builder
     */
    private static function applyFilters(Builder $query, ?array $filters)
    {
        if (!is_array($filters) || count($filters) < 1) {
            return $query;
        }

        if (!empty($filters['query'])) {
            $value = '%' . $filters['query'] . '%';

            $query->where(function (Builder $q) use ($value) {
                return $q->orWhere('id', 'LIKE', '%' . $value . '%')
                    ->orWhere('account_id', 'LIKE', '%' . $value . '%')
                    ->orWhere('name', 'LIKE', '%' . $value . '%');
            });
        }

        return $query;
    }

    /**
     * Create a new account from Google Model.
     *
     * @param Google_Model $model
     * @return Account
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function transform(Google_Model $model)
    {
        $object = $model->toSimpleObject();
        
        // convert object to associative array
        $attributes = json_decode(json_encode($object), TRUE);

        $attributes['account_id'] = $attributes['id'];

        $attributes['permissions'] = json_encode($attributes['permissions']);

        unset($attributes['id']);
        
        return $this->newInstance($attributes);
    }

    /**
     * Compare models
     *
     * @param Account $account
     * @return bool
     */
    public function isDiff($account)
    {
        if (strcmp($this->name, $account->name) !== 0) {
            return true;
        }

        /*if ($this->starred !== $account->starred) {
            return true;
        }*/

        /*if ($this->permissions != $account->permissions) {
            return true;
        }*/

        return false;
    }
}
