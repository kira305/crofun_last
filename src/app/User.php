<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Concurrently;
use App\Rule_action;
use Common;
use Auth;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable;

    const LOGIN_SUCCESS = 1;
    const LOGIN_FAILURE = 2;
    const LOGIN_IVALID = 3;
    const EXPIRED = 4;

    protected $table          = 'user_mst';
    protected $primaryKey     = 'id';
    protected $remember_token = false;
    public    $timestamps     = true;

    private $state;
    private $storage;

    public function __construct()
    {
        $this->storage = array();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'usr_code', 'pw', 'usr_name', 'rule', 'mail_address', 'company_id', 'headquarter_id', 'department_id', 'group_id', 'retire'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pw',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()
    {
        return $this->pw;
    }
    public function getRuleAction($action_id)
    {
        $rule_action = Rule_action::where('rule_id', $this->rule)->where('action_id', $action_id)->first();
        if ($rule_action) {
            return true;
        } else {
            return false;
        }
    }

    /**$company_id参照先の会社ID**/
    public function checkCompany($company_id)
    {
        $company_id_list  = Common::checkUserCompany(Auth::user()->id);
        if (in_array($company_id, $company_id_list)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIsDisable($user_id)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->headquarter->status == false || $user->department->status == false || $user->group->status == false) {

            return 1;
        }
        return 0;
    }
    public function position()
    {
        return $this->hasOne('App\Position_MST', 'id', 'position_id');
    }

    public function company()
    {
        return $this->hasOne('App\Company_MST', 'id', 'company_id');
    }

    public function headquarter()
    {
        return $this->hasOne('App\Headquarters_MST', 'id', 'headquarter_id');
    }

    public function department()
    {
        return $this->hasOne('App\Department_MST', 'id', 'department_id');
    }

    public function group()
    {
        return $this->hasOne('App\Group_MST', 'id', 'group_id');
    }

    public function concurrently()
    {
        $list_concurrents = Concurrently::where('usr_id', $this->id)->get();
        return $list_concurrents;
    }

    public function concurrent()
    {
        return $this->belongsTo('App\Concurrently', 'id', 'usr_id');
    }

    public function getrole()
    {
        return $this->hasOne('App\Rule_MST', 'id', 'rule');
    }

    public function rule()
    {
        $rule = Rule_MST::where('id', $this->rule)->first();
        return $rule;
    }

}
