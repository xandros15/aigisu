<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-19
 * Time: 23:29
 */

namespace Aigisu\Models;


use Aigisu\Components\Auth\IdentInterface;
use Aigisu\Core\Model;

/**
 * @property string $name
 * @property string $password
 * @property string $email
 * @property string $is_confirmed
 * @property string $role
 * @property string $recovery_hash
 * @property string $remember_identifier
 * @property string $remember_hash
 * @property string $refresh_token
 */
class User extends Model implements IdentInterface
{
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * @return array
     */
    public static function getRoleList(): array
    {
        return ['moderator', 'admin', 'owner'];
    }

    /**
     * @param string $email
     *
     * @return static|null
     */
    public static function findByEmail(string $email)
    {
        return static::where(['email' => $email, 'is_confirmed' => true])->get()->first();
    }

    /**
     * @param string $hash
     *
     * @return static|null
     */
    public static function findByRecoveryHash(string $hash)
    {
        return static::where(['recovery_hash' => $hash])->get()->first();
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public static function isValidRecoveryHash(string $hash): bool
    {
        list(, $timestamp) = explode('_', $hash);

        return time() - $timestamp < 0;
    }

    /**
     * @param string $role
     */
    public function changeRole(string $role)
    {
        $this->role = $role;
        $this->saveOrFail();
    }

    public function generateRecoveryHash(): void
    {
        $this->recovery_hash = bin2hex(random_bytes(32)) . '_' . (new \DateTime('+1 hour'))->getTimestamp();
        $this->saveOrFail();
    }

    /**
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->is_confirmed;
    }

    public function activate(): void
    {
        $this->is_confirmed = true;
        $this->saveOrFail();
    }

    public function deactivate(): void
    {
        $this->is_confirmed = false;
        $this->saveOrFail();
    }

    /**
     * @param string $password
     */
    public function changePassword(string $password): void
    {
        $this->setPassword($password);
        $this->removeRecoveryHash();
        $this->saveOrFail();
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role == 'admin';
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->role == 'owner';
    }

    private function removeRecoveryHash(): void
    {
        $this->recovery_hash = null;
    }
}
