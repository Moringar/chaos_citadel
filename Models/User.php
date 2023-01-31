<?php

namespace App\Models;

use App\Models\Database;

class User
{


    public function createUser($pseudo, $mail, $password)
    {
        Database::prepReq('INSERT INTO user (pseudo, mail, password ) VALUES (:pseudo, :mail, :password)', ["pseudo" => $pseudo, "mail" => $mail, "password" => $password]);
        return Database::fetchData();
    }

    public function createUserInventory($id)
    {
        Database::prepReq('INSERT INTO inventory (id_user) VALUES (:id)', ["id" => $id]);
        return Database::fetchData();
    }

    public function createUserFormula($id)
    {
        Database::prepReq('INSERT INTO formula (id_user) VALUES (:id)', ["id" => $id]);
        return Database::fetchData();
    }

    public function deleteUser($id)
    {
        Database::prepReq('DELETE FROM user WHERE id = :id', ["id" => $id]);
        return Database::fetchData();
    }
    public function deleteUserFormula($id)
    {
        Database::prepReq('DELETE FROM formula WHERE id_user = :id', ["id" => $id]);
        return Database::fetchData();
    }
    public function deleteUserInventory($id)
    {
        Database::prepReq('DELETE FROM inventory WHERE id_user = :id', ["id" => $id]);
        return Database::fetchData();
    }


    public function getUserByName($pseudo)
    {
        Database::prepReq('SELECT * FROM user WHERE pseudo = :pseudo', ["pseudo" => $pseudo]);
        return Database::fetchData();
    }

    public function getUserById($id)
    {
        Database::prepReq('SELECT * FROM user WHERE id = :id', ["id" => $id]);
        return Database::fetchData();
    }


    public function getFormulaByUserId($id)
    {
        Database::prepReq('SELECT * FROM formula WHERE id_user = :id', ["id" => $id]);
        return Database::fetchData();
    }

    public function getInventoryByUserId($id)
    {
        Database::prepReq('SELECT * FROM inventory WHERE id_user = :id', ["id" => $id]);
        return Database::fetchData();
    }


    public function setInventoryByUserId($myriad, $spider_jar, $berry, $enchanted_dagger, $fire_wine, $berce_bottle, $green_liquid_bottle, $golden_fleece, $silver_mirror, $comb, $gold, $enchanted_amulet, $copper_key, $id)
    {

        $params = [
            "myriad" => $myriad, 
            "spider_jar" => $spider_jar, 
            "berry" => $berry,
            "enchanted_dagger" => $enchanted_dagger, 
            "fire_wine" => $fire_wine, 
            "berce_bottle" => $berce_bottle,
            "green_liquid_bottle" => $green_liquid_bottle,
            "golden_fleece" => $golden_fleece,
            "silver_mirror" => $silver_mirror,
            "comb" => $comb, 
            "gold" => $gold, 
            "enchanted_amulet" => $enchanted_amulet,
            "copper_key" => $copper_key,
            "id" => $id
        ];

        Database::prepReq('UPDATE inventory SET 
        myriad = :myriad, 
        spider_jar = :spider_jar, 
        berry = :berry, 
        enchanted_dagger = :enchanted_dagger, 
        fire_wine = :fire_wine, 
        berce_bottle = :berce_bottle, 
        green_liquid_bottle = :green_liquid_bottle,
        golden_fleece = :golden_fleece, 
        silver_mirror = :silver_mirror, 
        comb = :comb, 
        gold = :gold, 
        enchanted_amulet = :enchanted_amulet, 
        copper_key = :copper_key
        WHERE id_user = :id ',
         $params);

        return Database::fetchData();
    }



    public function setFormulaByUserId($formula_luck, $formula_copy, $formula_life, $formula_weakness, $formula_fire, $formula_force, $formula_ability, $formula_illusion, $formula_levitation, $formula_gold, $formula_protection, $formula_telepathy, $id)
    {

        $params = [
            "formula_luck" => $formula_luck, 
            "formula_copy" => $formula_copy, 
            "formula_life" => $formula_life,
            "formula_weakness" => $formula_weakness, 
            "formula_fire" => $formula_fire, 
            "formula_force" => $formula_force,
            "formula_ability" => $formula_ability,
            "formula_illusion" => $formula_illusion,
            "formula_levitation" => $formula_levitation,
            "formula_gold" => $formula_gold,  
            "formula_protection" => $formula_protection,
            "formula_telepathy" => $formula_telepathy,
            "id" => $id
        ];

        Database::prepReq('UPDATE formula SET 
        formula_luck = :formula_luck, 
        formula_copy = :formula_copy, 
        formula_life = :formula_life, 
        formula_weakness = :formula_weakness, 
        formula_fire = :formula_fire, 
        formula_force = :formula_force, 
        formula_ability = :formula_ability,
        formula_illusion = :formula_illusion, 
        formula_levitation = :formula_levitation, 
        formula_gold = :formula_gold, 
        formula_protection = :formula_protection, 
        formula_telepathy = :formula_telepathy
        WHERE id_user = :id ',
         $params);

        return Database::fetchData();
    }

    public function setUserCharacteristicsByUserId($ability_max, $ability_current, $life_max, $life_current, $chance_max, $chance_current, $magic_max, $magic_current, $id)
    {

        $params = [
            "ability_max" => $ability_max, 
            "ability_current" => $ability_current,
            "life_max" => $life_max, 
            "life_current" => $life_current,
            "chance_max" => $chance_max, 
            "chance_current" => $chance_current, 
            "magic_max" => $magic_max,
            "magic_current" => $magic_current,
            "id" => $id
        ];

        Database::prepReq('UPDATE user SET 
        ability_max = :ability_max, 
        ability_current = :ability_current, 
        life_max = :life_max, 
        life_current = :life_current, 
        chance_max = :chance_max, 
        chance_current = :chance_current, 
        magic_max = :magic_max,
        magic_current = :magic_current
        WHERE id = :id ',
         $params);

        return Database::fetchData();
    }
    
    public function setUserStepByUserId($step, $id)
    {

        $params = [
            "step" => $step,
            "id" => $id
        ];

        Database::prepReq('UPDATE user SET 
        current_step = :step
        WHERE id = :id ',
         $params);

        return Database::fetchData();
    }

}