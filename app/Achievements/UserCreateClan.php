<?php
declare(strict_types=1);

namespace App\Achievements;

use Assada\Achievements\Achievement;

/**
 * Class Registered
 *
 * @package App\Achievements
 */
class UserCreateClan extends Achievement
{
    /*
     * The achievement name
     */
    public $name = 'Лидер';

    /*
     * A small description for the achievement
     */
    public $description = 'Создать свой клан';
}
