<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{

    public function createUser($pseudo, $mail, $password)
    {
        $user = new User();
        $user = $user->createUser($pseudo, $mail, $password);
        return $user;
    }


    public function createUserAssets($id)
    {
        $userInventory  = new User();
        $userFormula = new User();
        $userFormula->createUserFormula($id);
        $userInventory->createUserInventory($id);
    }


    public function getUserByPseudo($pseudo)
    {
        $user = new User();
        $user = $user->getUserByName($pseudo);
        return $user;
    }


    public function getUserById($id)
    {
        $user = new User();
        $user = $user->getUserById($id);
        return $user;
    }


    public function getFormulaByUserId($id)
    {
        $user = new User();
        $user = $user->getFormulaByUserId($id);
        return $user;
    }


    public function getInventoryByUserId($id)
    {
        $user = new User();
        $user = $user->getInventoryByUserId($id);
        return $user;
    }


    public function setInventoryByUserId($myriad, $spider_jar, $berry, $enchanted_dagger, $fire_wine, $berce_bottle, $green_liquid_bottle, $golden_fleece, $silver_mirror, $comb, $gold, $enchanted_amulet, $copper_key, $id)
    {
        $user = new User();
        $user = $user->setInventoryByUserId($myriad, $spider_jar, $berry, $enchanted_dagger, $fire_wine, $berce_bottle, $green_liquid_bottle, $golden_fleece, $silver_mirror, $comb, $gold, $enchanted_amulet, $copper_key, $id);
        return $user;
    }


    public function setFormulaByUserId($formula_luck, $formula_copy, $formula_life, $formula_weakness, $formula_fire, $formula_force, $formula_ability, $formula_illusion, $formula_levitation, $formula_gold, $formula_protection, $formula_telepathy, $id)
    {
        $user = new User();
        $user = $user->setFormulaByUserId($formula_luck, $formula_copy, $formula_life, $formula_weakness, $formula_fire, $formula_force, $formula_ability, $formula_illusion, $formula_levitation, $formula_gold, $formula_protection, $formula_telepathy, $id);
        return $user;
    }


    public function setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $id)
    {
        $user = new User();
        $user = $user->setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $id);
        return $user;
    }

    public function setUserStepByUserId($step, $id)
    {
        $user = new User();
        $user = $user->setUserStepByUserId($step, $id);
        return $user;
    }

    public function deleteUserById($id){
        $user = new USER();
        $user->deleteUser($id);
        $user->deleteUserFormula($id);
        $user->deleteUserInventory($id);
    }
}