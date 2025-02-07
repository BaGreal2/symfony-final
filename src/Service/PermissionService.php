<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Group;

class PermissionService
{
    /**
     * Checks whether the given user belongs to a group with the specified name.
     *
     * @param User   $user
     * @param string $groupName
     *
     * @return bool
     */
    public function userHasGroup(User $user, string $groupName): bool
    {
        foreach ($user->getGroups() as $group) {
            if ($group->getName() === $groupName) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adds the specified group to the user.
     * The User entity’s addGroup() method is used, which is designed to synchronize both sides of the relation.
     *
     * @param User  $user
     * @param Group $group
     *
     * @return void
     */
    public function addUserToGroup(User $user, Group $group): void
    {
        // This will check if the group is not already present
        // and call both $user->addGroup() and $group->addUser() internally.
        $user->addGroup($group);
    }

    /**
     * Removes the specified group from the user.
     *
     * @param User  $user
     * @param Group $group
     *
     * @return void
     */
    public function removeUserFromGroup(User $user, Group $group): void
    {
        $user->removeGroup($group);
    }
}
