<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:33
 */

namespace Aigisu\Components\Validators;


use InvalidArgumentException;
use Respect\Validation\Validator as v;

class CreateUserValidator extends AbstractValidator
{
    /** @var array */
    private $accesses = [];

    /**
     * CreateUserValidator constructor.
     * @param array $accesses
     */
    public function __construct(array $accesses)
    {
        $this->accesses = $accesses;
    }

    /**
     * @return array
     */
    protected function rules() : array
    {
        return [
            'name' => v::stringType()->length(4, 15),
            'email' => v::email(),
            'password' => v::stringType()->length(8, 32),
            'role' => v::in($this->getEnumRoles()),
        ];
    }

    /**
     * @return array
     */
    protected function getEnumRoles() : array
    {
        $roles = [];
        foreach ($this->accesses as $access) {
            $roles[] = $access['role'];
        }

        if (!$roles) {
            throw new InvalidArgumentException('Missing roles in access param. Check configuration params');
        }

        return $roles;
    }
}
