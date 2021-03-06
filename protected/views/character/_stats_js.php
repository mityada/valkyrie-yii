<script type="text/javascript"> 
	//<![CDATA[
		$(document).ready(function() {
			new Summary.Stats({
 
			"health": <?=$model->maxhealth?>,
			"power": <?=$model->character->powerValue?>,
			"powerTypeId": <?=$model->character->powerType?>,
			"hasOffhandWeapon": <?=($model->character->isOffhandWeapon()) ? 'true' : 'false'?>,
			"averageItemLevelEquipped": <?=$model->character->itemLevel['avg']?>,
			"averageItemLevelBest": <?=$model->character->itemLevel['avg']?>,
			"agiBase": <?=$model->levelStats['agility']?>,
			"critPercent": <?=$model->critPct?>,
			"rangeCritPercent": <?=$model->rangedCritPct?>,
			"parry": <?=$model->parryPct?>,
			"atkPowerBase": <?=$model->attackPower?>,
			"dmgMainSpeed": <?=$model->mainAttSpeed?>,
			"rangeAtkPowerBonus": <?=$model->rangedAttackPowerMod?>,
			"shadowDamage": 3191,
			"spellDmg_petSpellDmg": -1,
			"shadowResist": <?=$model->resShadow?>,
			"resistNature_pet": -1,
			"armorPercent": <?=$model->armorReducedDamage ?>,
			"manaRegenPerFive": 1036,
			"dmgRangeDps": <?=$model->rangedDps?>,
			"frostCrit": <?=$model->spellCritPctFrost?>,
			"resistShadow_pet": -1,
			"natureResist": <?=$model->resNature?>,
			"intTotal": <?=$model->intellect?>,
			"frostResist": <?=$model->resFrost?>,
			"int_mp": <?=$model->manaBonusFromIntellect?>,
			"arcaneCrit": <?=$model->spellCritPctArcane?>,
			"holyCrit": <?=$model->spellCritPctHoly?>,
			"natureCrit": <?=$model->spellCritPctNature?>,
			"sprBase": <?=$model->levelStats['spirit']?>,
			"agi_ap": -1,
			"dodge": <?=$model->dodgePct?>,
			"atkPowerBonus": <?=$model->attackPowerMod?>,
			"spr_regen": <?=$model->manaPerFiveSeconds?>,
			"manaRegenCombat": 526,
			"sprTotal": <?=$model->spirit?>,
			"intBase": <?=$model->levelStats['intellect']?>,
			"strBase": <?=$model->levelStats['strength']?>,
			"dmgRangeMin": <?=$model->rangeMinDmg?>,
			"dmgOffSpeed": <?=$model->offAttSpeed?>,
			"resistFire_pet": -1,
			"defense": 0,
			"strTotal": <?=$model->strength?>,
			"fireCrit": <?=$model->spellCritPctFire?>,
			"natureDamage": 3191,
			"dmgMainMax": <?=$model->mainMaxDmg?>,
			"dmgMainMin": <?=$model->mainMinDmg?>,
			"holyResist": <?=$model->resHoly?>,
			"rangeAtkPowerBase": <?=$model->rangedAttackPower?>,
			"dmgOffMin": <?=$model->offMinDmg?>,
			"spellDmg_petAp": -1,
			"agi_armor": 102,
			"resistHoly_pet": -1,
			"str_ap": <?=$model->meleeAPFromStrength?>,
			"block_damage": 30,
			"dmgOffMax": <?=$model->offMaxDmg?>,
			"defensePercent": 0,
			"armor_petArmor": -1,
			"block": <?=$model->blockPct?>,
			"dmgOffDps": <?=$model->offDps?>,
			"dmgRangeMax": <?=$model->rangeMaxDmg?>,
			"resistArcane_pet": -1,
			"dmgMainDps": <?=$model->mainDps?>,
			"healing": 3191,
			"str_block": -1,
			"rangeAtkPowerLoss": 0,
			"fireDamage": 3191,
			"shadowCrit": <?=$model->spellCritPctShadow?>,
			"arcaneDamage": 3191,
			"agiTotal": <?=$model->agility?>,
			"ap_dps": 4.857142925262451,
			"atkPowerLoss": 0,
			"staBase": <?=$model->levelStats['stamina']?>,
			"fireResist": <?=$model->resFire?>,
			"int_crit": <?=$model->spellCritFromIntellect?>,
			"rap_petSpellDmg": -1,
			"arcaneResist": <?=$model->resArcane?>,
			"resistFrost_pet": -1,
			"dmgRangeSpeed": <?=$model->rangeAttSpeed?>,
			"frostDamage": 3191,
			"sta_hp": <?=$model->healthBonusFromStamina?>,
			"agi_crit": <?=$model->meleeCritFromAgility?>,
			"armorTotal": <?=$model->armor?>,
			"sta_petSta": -1,
			"armorBase": <?=$model->armor?>,
			"spellCritPercent": <?=max($model->spellCritPctHoly, $model->spellCritPctFire, $model->spellCritPctNature, $model->spellCritPctFrost, $model->spellCritPctShadow, $model->spellCritPctArcane)?>,
			"staTotal": <?=$model->stamina?>,
			"rap_petAp": -1,
			"holyDamage": 3191,
	"foo": true
});
		});
	//]]>
	</script> 
