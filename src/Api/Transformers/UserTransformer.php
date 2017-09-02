<?php


namespace Aigisu\Api\Transformers;


use Aigisu\Components\Transformer\TimestampTrait;
use Aigisu\Models\User;
use League\Fractal\TransformerAbstract;


class UserTransformer extends TransformerAbstract
{
    use TimestampTrait;

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'is_confirmed' => (bool) $user->is_confirmed,
            'role' => (string) $user->role,
        ];
    }
}
