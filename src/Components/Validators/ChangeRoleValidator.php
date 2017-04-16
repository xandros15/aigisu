<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 18:59
 */

namespace Aigisu\Components\Validators;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

class ChangeRoleValidator extends AbstractValidator
{
    /** @var array */
    private $roles;

    /**
     * ChangeRoleValidator constructor.
     *
     * @param array $accesses
     */
    public function __construct(array $accesses)
    {
        $this->setRoles($accesses);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'role' => v::in($this->roles),
        ];
    }

    /**
     * @param array $accesses
     */
    private function setRoles(array $accesses)
    {
        $roles = [];
        foreach ($accesses as $access) {
            $roles[] = $access['role'];
        }

        if (!$roles) {
            throw new InvalidArgumentException('Missing roles in access param. Check configuration params');
        }

        $this->roles = $roles;
    }

}
