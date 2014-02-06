<?php

class Character extends Base\Char
{
    /* Equipment Slots */

    const EQUIPMENT_SLOT_START     = 0;
    const EQUIPMENT_SLOT_HEAD      = 0;
    const EQUIPMENT_SLOT_NECK      = 1;
    const EQUIPMENT_SLOT_SHOULDERS = 2;
    const EQUIPMENT_SLOT_BODY      = 3;
    const EQUIPMENT_SLOT_CHEST     = 4;
    const EQUIPMENT_SLOT_WAIST     = 5;
    const EQUIPMENT_SLOT_LEGS      = 6;
    const EQUIPMENT_SLOT_FEET      = 7;
    const EQUIPMENT_SLOT_WRISTS    = 8;
    const EQUIPMENT_SLOT_HANDS     = 9;
    const EQUIPMENT_SLOT_FINGER1   = 10;
    const EQUIPMENT_SLOT_FINGER2   = 11;
    const EQUIPMENT_SLOT_TRINKET1  = 12;
    const EQUIPMENT_SLOT_TRINKET2  = 13;
    const EQUIPMENT_SLOT_BACK      = 14;
    const EQUIPMENT_SLOT_MAINHAND  = 15;
    const EQUIPMENT_SLOT_OFFHAND   = 16;
    const EQUIPMENT_SLOT_RANGED    = 17;
    const EQUIPMENT_SLOT_TABARD    = 18;
    const EQUIPMENT_SLOT_END       = 19;
    const CLASS_WARRIOR            = 1;
    const CLASS_PALADIN            = 2;
    const CLASS_HUNTER             = 3;
    const CLASS_ROGUE              = 4;
    const CLASS_PRIEST             = 5;
    const CLASS_DK                 = 6;
    const CLASS_SHAMAN             = 7;
    const CLASS_MAGE               = 8;
    const CLASS_WARLOCK            = 9;
    const CLASS_DRUID              = 11;
    const MAX_CLASSES              = 12;
    const RACE_HUMAN               = 1;
    const RACE_ORC                 = 2;
    const RACE_DWARF               = 3;
    const RACE_NIGHTELF            = 4;
    const RACE_UNDEAD              = 5;
    const RACE_TAUREN              = 6;
    const RACE_GNOME               = 7;
    const RACE_TROLL               = 8;
    const FACTION_ALLIANCE         = 1;
    const FACTION_HORDE            = 2;
    const POWER_HEALTH             = 0xFFFFFFFE;
    const POWER_MANA               = 0;
    const POWER_RAGE               = 1;
    const POWER_FOCUS              = 2;
    const POWER_ENERGY             = 3;
    const POWER_HAPPINESS          = 4;
    const POWER_RUNE               = 5;
    const POWER_RUNIC_POWER        = 6;
    const MAX_POWERS               = 7;
    const ROLE_MELEE               = 1;
    const ROLE_RANGED              = 2;
    const ROLE_CASTER              = 3;
    const ROLE_HEALER              = 4;
    const ROLE_TANK                = 5;
    const SKILL_BLACKSMITHING      = 164;
    const SKILL_LEATHERWORKING     = 165;
    const SKILL_ALCHEMY            = 171;
    const SKILL_HERBALISM          = 182;
    const SKILL_MINING             = 186;
    const SKILL_TAILORING          = 197;
    const SKILL_ENGINERING         = 202;
    const SKILL_ENCHANTING         = 333;
    const SKILL_SKINNING           = 393;
    const SKILL_JEWELCRAFTING      = 755;
    const SKILL_INSCRIPTION        = 773;

    public $class_text = false;
    public $race_text  = false;
    public $realm      = false;
    public $faction;
    private $_items     = array();
    private $_spells = array();
    private $_talents = array();
    private $_professions = false;
    private $_power_type;
    private $_role;
    private $_item_level;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'characters';
    }

    public function rules()
    {
        return array(
            array('account, name, guid, level, class_id, race', 'safe', 'on' => 'search'),
            array('account, level, class_id, race, faction, honor_standing', 'numerical', 'integerOnly' => true),
            array('name, level, class_id, race, honor_standing, faction', 'safe', 'on' => 'pvp, pvp_current'),
            array('account, name, race, class_id, gender, level, money, playerBytes, playerBytes2', 'safe', 'on' => 'update'),
        );
    }

    public function relations()
    {
        return array(
            'honor' => array(self::HAS_ONE, 'CharacterHonorStatic', 'guid'),
            'stats' => array(self::HAS_ONE, 'CharacterStats', 'guid'),
            'reputation' => array(
                self::HAS_MANY,
                'CharacterReputation',
                'guid',
                'condition' => '`reputation`.`flags` & ' . CharacterReputation::FACTION_FLAG_VISIBLE,
                'index'     => 'faction',
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'class_id'           => 'Class',
            'honor_highest_rank' => 'Max Rank',
            'honor_standing'     => 'Standing',
            'honor_rank_points'  => 'RP',
        );
    }

    public static function itemAlias($type, $code = NULL)
    {
        $_items = array(
            'class' => array(
                self::CLASS_WARRIOR => Wow::t('Warrior'),
                self::CLASS_PALADIN => Wow::t('Paladin'),
                self::CLASS_HUNTER  => Wow::t('Hunter'),
                self::CLASS_ROGUE   => Wow::t('Rogue'),
                self::CLASS_PRIEST  => Wow::t('Priest'),
                self::CLASS_SHAMAN  => Wow::t('Shaman'),
                self::CLASS_MAGE    => Wow::t('Mage'),
                self::CLASS_WARLOCK => Wow::t('Warlock'),
                self::CLASS_DRUID   => Wow::t('Druid'),
            ),
            'race'              => array(
                self::RACE_HUMAN    => 'human',
                self::RACE_ORC      => 'orc',
                self::RACE_DWARF    => 'dwarf',
                self::RACE_NIGHTELF => 'nightelf',
                self::RACE_UNDEAD   => 'undead',
                self::RACE_TAUREN   => 'tauren',
                self::RACE_GNOME    => 'gnome',
                self::RACE_TROLL    => 'troll',
            ),
            'gender'            => array(
                0       => 'male',
                1       => 'female',
            ),
            'power' => array(
                self::POWER_MANA   => 'Mana',
                self::POWER_RAGE   => 'Rage',
                self::POWER_ENERGY => 'Energy',
            ),
            'faction'          => array(
                self::FACTION_ALLIANCE => Wow::t('Alliance'),
                self::FACTION_HORDE    => Wow::t('Horde'),
            ),
        );

        if(isset($code))
            return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
        else
            return isset($_items[$type]) ? $_items[$type] : false;
    }

    public function search($all_realms = false)
    {
        $criteria = new CDbCriteria;
        $sort     = new CSort;

        $criteria->compare('guid', $this->guid);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('race', $this->race);
        $criteria->compare('account', $this->account);
        $criteria->compare('class_id', $this->class_id);
        $criteria->compare('level', $this->level);
        $criteria->compare('online', $this->online);
        $criteria->compare('honor_standing', $this->honor_standing);
        $criteria->addCondition('account > 0');

        if ($this->scenario == 'pvp' || $this->scenario == 'pvp_current')
        {
            $criteria->with = 'honor';
            $criteria->together = true;

            if ($this->scenario == 'pvp')
                $criteria->addCondition('honor.thisWeek_kills > 25 OR honor_standing > 0');

            $sort->attributes = array(
                'honor.hk',
                'honor.thisWeek_cp',
                'honor.thisWeek_kills',
                '*'
            );

            $sort->defaultOrder = 'honor.thisWeek_cp DESC';
        }

        switch($this->faction)
        {
            case self::FACTION_ALLIANCE:
                $criteria->compare('race', array(
                    self::RACE_HUMAN,
                    self::RACE_DWARF,
                    self::RACE_NIGHTELF,
                    self::RACE_GNOME
                ));
                break;
            case self::FACTION_HORDE:
                $criteria->compare('race', array(
                    self::RACE_ORC,
                    self::RACE_UNDEAD,
                    self::RACE_TAUREN,
                    self::RACE_TROLL
                ));
                break;
            default : break;
        }

        return new CActiveDataProvider(get_class($this), array(
                'criteria'   => $criteria,
                'pagination' => array(
                    'pageSize' => 40,
                ),
                'sort'     => $sort,
            ));
    }

    public function getClass() {
        return $this->class_id;
    }

    public function getHonorRank()
    {
        $rank = 0;
        if($this->honor_rank_points <= -2000.0)
            $rank = 1;       // Pariah (-4)
        else if($this->honor_rank_points <= -1000.0)
            $rank = 2;  // Outlaw (-3)
        else if($this->honor_rank_points <= -500.0)
            $rank = 3;   // Exiled (-2)
        else if($this->honor_rank_points < 0.0)
            $rank = 4;       // Dishonored (-1)
        else if($this->honor_rank_points == 0)
            $rank = 0;
        else if($this->honor_rank_points < 2000.00)
            $rank = 5;
        else if($this->honor_rank_points > (13) * 5000)
            $rank = 21;
        else
            $rank = 6 + (int) ($this->honor_rank_points / 5000);

        return $rank;
    }

    public function getHonorBar()
    {
        $bar['percent'] = 0;
        $bar['cap']     = 0;
        if($this->honor_rank_points <= -2000.0)
            $bar['cap']     = -2000;
        else if($this->honor_rank_points <= -1000.0)
        {
            $bar['percent'] = round((2000 + $this->honor_rank_points) / 10);
            $bar['cap']     = -1000;
        }
        else if($this->honor_rank_points <= -500.0)
        {
            $bar['percent'] = round((1000 + $this->honor_rank_points) / 5);
            $bar['cap']     = -500;
        }
        else if($this->honor_rank_points < 0.0)
            $bar['percent'] = round((500 + $this->honor_rank_points) / 5);
        else if($this->honor_rank_points < 2000.00)
        {
            $bar['percent'] = round($this->honor_rank_points / 20);
            $bar['cap']     = 2000;
        }
        else if($this->honor_rank_points > (13) * 5000)
        {
            $bar['percent'] = 100;
            $bar['cap']     = 65000;
        }
        else
        {
            $bar['percent'] = round(($this->honor_rank_points % 5000) / 50);
            $bar['cap']     = floor($this->honor_rank_points / 5000) * 5000 + 5000;
        }

        return $bar;
    }

    public function loadAdditionalData()
    {
        $column = 'name_' . Yii::app()->language;

        $connection = Yii::app()->db;
        $command    = $connection->createCommand()
            ->select("r.$column AS race, c.$column AS class")
            ->from('wow_races r, wow_classes c')
            ->where('r.id = ? AND c.id = ?', array($this->race, $this->class_id))
            ->limit(1);
        $row = $command->queryRow();

        $this->race_text = $row['race'];
        $this->class_text = $row['class'];

        $this->_spells = $this->dbConnection
            ->createCommand("SELECT spell FROM character_spell WHERE guid = {$this->guid} AND disabled = 0")
            ->queryColumn();
    }

    protected function afterFind()
    {
        parent::afterFind();
        $this->realm = 'Valkyrie';
        $this->equipmentCache = explode(' ', $this->equipmentCache);

        switch($this->race)
        {
            case self::RACE_HUMAN:
            case self::RACE_DWARF:
            case self::RACE_NIGHTELF:
            case self::RACE_GNOME:
                $this->faction = self::FACTION_ALLIANCE;
                break;
            default :
                $this->faction = self::FACTION_HORDE;
                break;
        }
    }

    public function getItems()
    {
        if (!empty($this->_items))
            return $this->_items;

        $this->_items = array(
            self::EQUIPMENT_SLOT_HEAD      => 1,
            self::EQUIPMENT_SLOT_NECK      => 2,
            self::EQUIPMENT_SLOT_SHOULDERS => 3,
            self::EQUIPMENT_SLOT_BACK      => 16,
            self::EQUIPMENT_SLOT_CHEST     => 5,
            self::EQUIPMENT_SLOT_BODY      => 4,
            self::EQUIPMENT_SLOT_TABARD    => 19,
            self::EQUIPMENT_SLOT_WRISTS    => 9,
            self::EQUIPMENT_SLOT_HANDS     => 10,
            self::EQUIPMENT_SLOT_WAIST     => 6,
            self::EQUIPMENT_SLOT_LEGS      => 7,
            self::EQUIPMENT_SLOT_FEET      => 8,
            self::EQUIPMENT_SLOT_FINGER1   => 11,
            self::EQUIPMENT_SLOT_FINGER2   => 11,
            self::EQUIPMENT_SLOT_TRINKET1  => 12,
            self::EQUIPMENT_SLOT_TRINKET2  => 12,
            self::EQUIPMENT_SLOT_MAINHAND  => 21,
            self::EQUIPMENT_SLOT_OFFHAND   => 22,
            self::EQUIPMENT_SLOT_RANGED    => 28,
        );

        $items = array();
        for($i       = 0; $i < 37; $i += 2)
            if($this->equipmentCache[$i])
                $items[] = $this->equipmentCache[$i];

        $models = ItemTemplate::model()->findAllByPk($items);
        foreach($models as $proto)
        {
            $pos               = array_search($proto->entry, $this->equipmentCache);
            $slot              = $pos / 2;
            $this->_items[$slot] = array(
                'entry'         => $proto->entry,
                'icon'          => $proto->icon,
                'name'          => $proto->name,
                'display_id'    => $proto->displayid,
                'quality'       => $proto->Quality,
                'item_level'    => $proto->ItemLevel,
                'class'         => $proto->class,
                'slot'          => $proto->InventoryType,
                'can_displayed' => !in_array($proto->InventoryType, array(2, 11, 12)),
                'can_enchanted' => !in_array($slot, array(3, 17, 1, 5, 10, 11, 12, 13, 16, 18)),
            );

            $enchant_id = $this->equipmentCache[$pos + 1];
            $data       = array();

            if($enchant_id)
            {
                $column = 'text_' . Yii::app()->language;
                $info   = Yii::app()->db
                    ->createCommand("
                                SELECT wow_enchantment.$column AS text, wow_spellenchantment.id AS spellId
                                FROM wow_enchantment
                                LEFT JOIN wow_spellenchantment ON wow_spellenchantment.Value = wow_enchantment.id
                                WHERE wow_enchantment.id = {$enchant_id} LIMIT 1")
                    ->queryRow();
                if(is_array($info))
                {
                    $this->_items[$slot]['enchant_text'] = $info['text'];
                    if($info['spellId'])
                    {
                        $item = ItemTemplate::model()->getDbConnection()
                            ->createCommand("
                                        SELECT entry, name
                                        FROM item_template
                                        WHERE
                                        spellid_1 = {$info['spellId']} OR
                                        spellid_2 = {$info['spellId']} OR
                                        spellid_3 = {$info['spellId']} OR
                                        spellid_4 = {$info['spellId']} OR
                                        spellid_5 = {$info['spellId']} LIMIT 1")
                            ->queryRow();
                        if($item)
                        {
                            $this->_items[$slot]['enchant_text'] = $item['name'];
                            $this->_items[$slot]['enchant_item'] = $item['entry'];
                        }
                    }
                }
                
                $data[] = "data[enchant_id]={$enchant_id}";
            }

            if($proto->itemset)
            {
                $set        = ItemTemplate::model()->getDbConnection()
                    ->createCommand("SELECT entry FROM item_template WHERE itemset = {$proto->itemset}")
                    ->queryColumn();
                $set_pieces = array();
                for($k                         = 0; $k < 37; $k += 2)
                    if(in_array($this->equipmentCache[$k], $set))
                        $set_pieces[]              = $this->equipmentCache[$k];
                $data[]                    = 'data[set]=' . implode(',', $set_pieces);
            }
            $this->_items[$slot]['data'] = implode('&', $data);
        }
        return $this->_items;
    }

    public function isEquipped($entry)
    {
        for($i = 0; $i < 37; $i += 2)
            if($entry == $this->equipmentCache[$i])
                return true;
        return false;
    }

    public function isOffhandWeapon()
    {
        return(isset($this->items[self::EQUIPMENT_SLOT_OFFHAND]['class']) && $this->items[self::EQUIPMENT_SLOT_OFFHAND]['class'] == ItemTemplate::ITEM_CLASS_WEAPON);
    }

    public function isRangedWeapon()
    {
        return(
            isset($this->items[self::EQUIPMENT_SLOT_RANGED]['class']) &&
            $this->items[self::EQUIPMENT_SLOT_RANGED]['class'] == ItemTemplate::ITEM_CLASS_WEAPON);
    }

    public function getPowerType()
    {
        if(!$this->_power_type)
        {
            switch($this->class_id)
            {
                case self::CLASS_WARRIOR:
                    $this->_power_type = self::POWER_RAGE;
                    break;
                case self::CLASS_ROGUE:
                    $this->_power_type = self::POWER_ENERGY;
                    break;
                case self::CLASS_DK:
                    $this->_power_type = self::POWER_RUNIC_POWER;
                    break;
                /*
                  case self::CLASS_HUNTER:
                  $this->_power_type = self::POWER_FOCUS;
                  break;
                 */
                default:
                    $this->_power_type = self::POWER_MANA;
                    break;
            }
        }

        return $this->_power_type;
    }

    public function getPowerValue()
    {
        if(is_object($this->stats))
            $power = $this->stats->{'maxpower' . ($this->powerType + 1)};
        else
            $power = $this->{'power' . ($this->powerType + 1)};
        if($this->class_id == self::CLASS_WARRIOR)
            $power /= 10;
        return $power;
    }

    public function getTalents()
    {
        if(empty($this->_talents))
        {
            $talentHandler = new WowTalents($this->class_id);


            $this->_talents = $talentHandler->talentTrees;

            $build = null;

            foreach($this->_talents as $i => $tree)
            {
                $this->_talents[$i]['count'] = 0;
                foreach($tree['talents'] as $k => $tal)
                {
                    $checked = false;
                    $points  = 0;
                    if($tal['keyAbility'])
                    {
                        $tSpell     = Spell::model()->findByPk($tal['ranks'][0]['id']);
                        $name       = $tSpell->spellname_loc0;
                        $spellRanks = Yii::app()->db->createCommand(
                                "SELECT spellID
                                FROM wow_spells
                                WHERE spellicon = {$tSpell->spellicon} AND
                                    spellname_loc0 = :name")
                            ->bindParam(':name', $name)
                            ->queryColumn();

                        foreach($spellRanks as $spell)
                            if(in_array($spell, $this->_spells))
                            {
                                $checked = true;
                                $build .= 1;
                                $points  = 1;
                                $this->_talents[$i]['count']++;
                                break;
                            }
                    }
                    else
                    {
                        foreach($tal['ranks'] as $j => $spell)
                            if(in_array($spell['id'], $this->_spells))
                            {
                                $checked = true;
                                $build .= $j + 1;
                                $points  = $j + 1;
                                $this->_talents[$i]['count'] += $j + 1;
                                break;
                            }
                    }

                    if(!$checked)
                        $build .= 0;

                    $this->_talents[$i]['talents'][$k]['points'] = $points;
                }
            }

            $this->_talents['build'] = $build;

            $this->_talents['maxTreeNo'] = 0;
            for($i = 0; $i < 3; $i++)
                if($this->_talents[$i]['count'] > $this->_talents[$this->_talents['maxTreeNo']]['count'])
                    $this->_talents['maxTreeNo'] = $i;

            $this->_talents['name'] = $this->_talents[$this->_talents['maxTreeNo']]['name'];
            $this->_talents['icon'] = $this->_talents[$this->_talents['maxTreeNo']]['icon'];

            if($this->_talents[0]['count'] == 0 && $this->_talents[1]['count'] == 0 && $this->_talents[2]['count'] == 0)
            {
                // have no talents
                $this->_talents['maxTreeNo'] = -1;
                $this->_talents['icon'] = 'inv_misc_questionmark';
                $this->_talents['name'] = 'No Talents';
            }
        }

        return $this->_talents;
    }

    public function getRole()
    {
        if($this->_role > 0)
            return $this->_role;

        switch($this->class_id)
        {
            case self::CLASS_WARRIOR:
                if($this->talents[2]['count'] > $this->talents[1]['count'] && $this->talents[2]['count'] > $this->talents[0]['count'])
                    $this->_role = self::ROLE_TANK;
                else
                    $this->_role = self::ROLE_MELEE;
                break;
            case self::CLASS_ROGUE:
            case self::CLASS_DK:
                $this->_role = self::ROLE_MELEE;
                break;
            case self::CLASS_PALADIN:
            case self::CLASS_DRUID:
            case self::CLASS_SHAMAN:
                // Hybrid classes. Need to check active talent tree.
                if($this->talents[0]['count'] > $this->talents[1]['count'] && $this->talents[0]['count'] > $this->talents[2]['count'])
                    if($this->class_id == self::CLASS_PALADIN)
                        $this->_role = self::ROLE_HEALER;
                    else
                        $this->_role = self::ROLE_CASTER;
                elseif($this->talents[1]['count'] > $this->talents[0]['count'] && $this->talents[1]['count'] > $this->talents[2]['count'])
                    if($this->class_id == self::CLASS_PALADIN)
                        $this->_role = self::ROLE_TANK; // Paladin: Protection
                    else
                        $this->_role = self::ROLE_MELEE; //Druid: Feral, Shaman: Enhancemenet
                        else
                if($this->class_id == self::CLASS_PALADIN)
                    $this->_role = self::ROLE_MELEE;
                else
                    $this->_role = self::ROLE_HEALER;
                break;
            case self::CLASS_PRIEST:
                if($this->talents[2]['count'] > $this->talents[0]['count'] && $this->talents[2]['count'] > $this->talents[1]['count'])
                    $this->_role = self::ROLE_CASTER;
                else
                    $this->_role = self::ROLE_HEALER;
                break;
            case self::CLASS_MAGE:
            case self::CLASS_WARLOCK:
                $this->_role = self::ROLE_CASTER;
                break;
            case self::CLASS_HUNTER:
                $this->_role = self::ROLE_RANGED;
                break;
        }

        return $this->_role;
    }

    public function getProfessions()
    {
        if($this->_professions !== false)
            return $this->_professions;

        $skill_professions = array(
            self::SKILL_BLACKSMITHING,
            self::SKILL_LEATHERWORKING,
            self::SKILL_ALCHEMY,
            self::SKILL_HERBALISM,
            self::SKILL_MINING,
            self::SKILL_TAILORING,
            self::SKILL_ENGINERING,
            self::SKILL_ENCHANTING,
            self::SKILL_SKINNING,
            self::SKILL_JEWELCRAFTING,
            self::SKILL_INSCRIPTION
        );
        $skill_professions = implode(', ', $skill_professions);

        $professions = $this->dbConnection
            ->createCommand("SELECT * FROM character_skills WHERE guid = {$this->guid} AND skill IN ({$skill_professions}) LIMIT 2")
            ->queryAll();
        if(!is_array($professions))
            return false;

        $this->_professions = array();
        $i      = 0;
        $column = 'name_' . Yii::app()->language;
        foreach($professions as $prof)
        {
            $this->_professions[$i] = Yii::app()->db
                ->createCommand("SELECT id, $column AS name, icon FROM wow_professions WHERE id = {$prof['skill']} LIMIT 1")
                ->queryRow();
            if(!$this->_professions[$i])
                continue;
            $this->_professions[$i]['value'] = $prof['value'];
            $this->_professions[$i]['max'] = 300;
            $i++;
        }

        return $this->_professions;
    }

    public function getItemLevel()
    {
        if($this->_item_level)
            return $this->_item_level;

        $total_iLvl = 0;
        $maxLvl     = 0;
        $minLvl     = 500;
        $i          = 0;
        $this->_item_level = array('avgEquipped' => 0, 'avg'         => 0);
        foreach($this->items as $slot => $item)
        {
            if(!in_array($slot, array(self::EQUIPMENT_SLOT_BODY, self::EQUIPMENT_SLOT_TABARD)))
            {
                if(isset($item['item_level']))
                {
                    $total_iLvl += $item['item_level'];
                    if($item['item_level'] < $minLvl)
                        $minLvl = $item['item_level'];
                    if($item['item_level'] > $maxLvl)
                        $maxLvl = $item['item_level'];
                }
                $i++;
            }
        }
        if($i == 0)
        {
            // Prevent divison by zero.
            return $this->_item_level;
        }
        $this->_item_level['avgEquipped'] = round(($maxLvl + $minLvl) / 2);
        $this->_item_level['avg'] = round($total_iLvl / $i);
        return $this->_item_level;
    }

    public function getFeed($count)
    {
        $feed = array();

        $feed = $this->dbConnection
            ->createCommand("SELECT * FROM character_feed_log WHERE guid = {$this->guid} ORDER BY date DESC LIMIT {$count}")
            ->queryAll();

        for($i = 0; $i < count($feed); $i++)
            switch($feed[$i]['type'])
            {
                case 2:
                    $feed[$i]['item']     = ItemTemplate::model()->findByPk($feed[$i]['data']);
                    $feed[$i]['equipped'] = $this->isEquipped($feed[$i]['data']);
                    break;
                case 3:
                    $feed[$i]['count']    = $this->dbConnection
                        ->createCommand("SELECT COUNT(1)
                            FROM character_feed_log
                            WHERE
                                guid = {$this->guid}
                                AND type = 3
                                AND data = {$feed[$i]['data']}
                                AND date <= {$feed[$i]['date']}")
                        ->queryScalar();
                    $feed[$i]['data']     = CreatureTemplate::model()->findByPk($feed[$i]['data']);
                    break;
            }

        return $feed;
    }

    public function getRaidProgression()
    {
        $raid_encounters = array(
            12118 => array('raid' => 'mc', 'id' => 0), // Lucifron
            11982 => array('raid' => 'mc', 'id' => 1), // Magmadar
            12259 => array('raid' => 'mc', 'id' => 2), // Gehennas
            12057 => array('raid' => 'mc', 'id' => 3), // Garr
            12056 => array('raid' => 'mc', 'id' => 4), // Baron Geddon
            12264 => array('raid' => 'mc', 'id' => 5), // Shazzrah
            12098 => array('raid' => 'mc', 'id' => 6), // Sulfuron Harbinger
            11988 => array('raid' => 'mc', 'id' => 7), // Golemagg the Incinerator
            12018 => array('raid' => 'mc', 'id' => 8), // Majordomo Executus
            11502 => array('raid' => 'mc', 'id' => 9), // Ragnaros

            10184 => array('raid' => 'mc', 'id' => 0), // Onyxia

            12435 => array('raid' => 'bwl', 'id' => 0), // Razorgore the Untamed
            13020 => array('raid' => 'bwl', 'id' => 1), // Vaelastrasz the Corrupt
            12017 => array('raid' => 'bwl', 'id' => 2), // Broodlord Lashlayer
            11983 => array('raid' => 'bwl', 'id' => 3), // Firemaw
            14601 => array('raid' => 'bwl', 'id' => 4), // Ebonroc
            11981 => array('raid' => 'bwl', 'id' => 5), // Flamegor
            14020 => array('raid' => 'bwl', 'id' => 6), // Chromaggus
            11583 => array('raid' => 'bwl', 'id' => 7), // Nefarian

            14517 => array('raid' => 'zg', 'id' => 0), // High Priestess Jeklik
            14507 => array('raid' => 'zg', 'id' => 1), // High Priest Venoxis
            14510 => array('raid' => 'zg', 'id' => 2), // High Priestess Mar'li
            14509 => array('raid' => 'zg', 'id' => 3), // High Priest Thekal
            14515 => array('raid' => 'zg', 'id' => 4), // High Priestess Arlokk
            14834 => array('raid' => 'zg', 'id' => 5), // Hakkar the Soulflayer
            11382 => array('raid' => 'zg', 'id' => 6), // Bloorlord Mandokir
            11380 => array('raid' => 'zg', 'id' => 7), // Jin'do the Hexxer
            15114 => array('raid' => 'zg', 'id' => 8), // Gahz'ranka
            15082 => array('raid' => 'zg', 'id' => 9), // Gri'lek
            15084 => array('raid' => 'zg', 'id' => 9), // Renataki
            15083 => array('raid' => 'zg', 'id' => 9), // Hazza'rah
            15085 => array('raid' => 'zg', 'id' => 9)  // Wushoolay
        );

        $bosses = $this->dbConnection
            ->createCommand("SELECT data
                FROM character_feed_log
                WHERE
                     guid = {$this->guid}
                    AND type = 3")
            ->queryAll();

        $progress = array(
            'mc'  => array_fill(0, 10, 0),
            'ony' => array_fill(0,  1, 0),
            'bwl' => array_fill(0,  8, 0),
            'zg'  => array_fill(0, 10, 0)
        );

        for ($i = 0; $i < count($bosses); $i++) {
            $entry = $bosses[$i]['data'];

            if (!isset($raid_encounters[$entry]))
                continue;

            $encounter = $raid_encounters[$entry];

            $progress[$encounter['raid']][$encounter['id']]++;
        }

        foreach ($progress as $raid => $encounters) {
            $total = count($encounters);
            $done = 0;

            foreach ($encounters as $count)
                if ($count > 0)
                    $done++;

            if ($done == $total)
                $progress[$raid]['status'] = 'completed';
            else if ($done > 0)
                $progress[$raid]['status'] = 'in-progress';
            else
                $progress[$raid]['status'] = 'incomplete';
        }

        return $progress;
    }

    public function getFactions()
    {
        $_factions = array();
        foreach($this->reputation as $id => $data)
            $_factions[] = $id;

        $_factions = implode(', ', $_factions);

        $column   = 'name_' . Yii::app()->language;
        $factions = Yii::app()->db
            ->createCommand("SELECT `id`, `category`, $column AS `name`, `baseValue`
                    FROM `wow_factions` WHERE `id` IN ($_factions)
                    ORDER BY `id` DESC")
            ->queryAll();

        // Default categories
        $categories = array(
            // World of Warcraft (Classic)
            1118 => array(
                // Horde
                67 => array(
                    'order' => 1,
                    'side'  => CharacterReputation::FACTION_HORDE
                ),
                // Horde Forces
                892     => array(
                    'order' => 2,
                    'side'  => CharacterReputation::FACTION_HORDE
                ),
                // Alliance
                469     => array(
                    'order' => 1,
                    'side'  => CharacterReputation::FACTION_ALLIANCE
                ),
                // Alliance Forces
                891     => array(
                    'order' => 2,
                    'side'  => CharacterReputation::FACTION_ALLIANCE
                ),
                // Steamwheedle Cartel
                169     => array(
                    'order' => 3,
                    'side'  => -1
                )
            ),
            // Other
            0       => array(
                // Wintersaber trainers
                589 => array(
                    'order' => 1,
                    'side'  => CharacterReputation::FACTION_ALLIANCE
                ),
                // Syndicat
                70      => array(
                    'order'  => 2,
                    'side'   => -1
                )
            )
        );
        $storage = array();
        $i = 0;
        foreach($factions as $faction)
        {
            // Standing & adjusted values
            $standing     = min(42999, $this->reputation[$faction['id']]['standing'] + $faction['baseValue']);
            $type         = CharacterReputation::REP_EXALTED;
            $rep_cap      = 999;
            $rep_adjusted = $standing - 42000;
            if($standing < CharacterReputation::REPUTATION_VALUE_HATED)
            {
                $type         = CharacterReputation::REP_HATED;
                $rep_cap      = 36000;
                $rep_adjusted = $standing + 42000;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_HOSTILE)
            {
                $type         = CharacterReputation::REP_HOSTILE;
                $rep_cap      = 3000;
                $rep_adjusted = $standing + 6000;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_UNFRIENDLY)
            {
                $type         = CharacterReputation::REP_UNFRIENDLY;
                $rep_cap      = 3000;
                $rep_adjusted = $standing + 3000;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_NEUTRAL)
            {
                $type         = CharacterReputation::REP_NEUTRAL;
                $rep_cap      = 3000;
                $rep_adjusted = $standing;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_FRIENDLY)
            {
                $type         = CharacterReputation::REP_FRIENDLY;
                $rep_cap      = 6000;
                $rep_adjusted = $standing - 3000;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_HONORED)
            {
                $type         = CharacterReputation::REP_HONORED;
                $rep_cap      = 12000;
                $rep_adjusted = $standing - 9000;
            }
            elseif($standing < CharacterReputation::REPUTATION_VALUE_REVERED)
            {
                $type                = CharacterReputation::REP_REVERED;
                $rep_cap             = 21000;
                $rep_adjusted        = $standing - 21000;
            }
            $faction['standing'] = $this->reputation[$faction['id']]['standing'];
            $faction['type']     = $type;
            $faction['cap']      = $rep_cap;
            $faction['adjusted'] = $rep_adjusted;
            $faction['percent']  = round($rep_adjusted * 100 / $rep_cap);

            if(isset($categories[$faction['category']])
                and $faction['id'] != 67
                and $faction['id'] != 469)
            {
                if(!isset($storage[$faction['category']]))
                    $storage[$faction['category']] = array();
                $storage[$faction['category']][$i++] = $faction;
            }

            else
            {
                foreach($categories as $catId => $subcat)
                {
                    if(isset($categories[$catId][$faction['category']]))
                        if($subcat[$faction['category']]['side'] == -1
                            or $subcat[$faction['category']]['side'] == $this->faction)
                        {
                            if(!isset($categories[$catId][$faction['category']]))
                                $categories[$catId][$faction['category']] = array();
                            $storage[$catId][$faction['category']][] = $faction;
                        }
                }
            }
        }
        ksort($storage[1118]);
        return $storage;
    }

    public function getPvpTitle($rank)
    {
        switch($rank)
        {
            case 1: $title = 'Pariah';
                break;
            case 2: $title = 'Outlaw';
                break;
            case 3: $title = 'Exiled';
                break;
            case 4: $title = 'Dishonored';
                break;
            default: $title = false;
                break;
        }

        $rank = $rank - 4;
        if($rank < 1)
            return $title;

        $column = 'title_';
        if($this->gender == 0)
            $column .= 'M_';
        else
            $column .= 'F_';
        $column .= Yii::app()->language;

        $id = 14 * ($this->faction - 1) + $rank;
        return Yii::app()->db
                ->createCommand("SELECT $column
                    FROM `wow_titles` WHERE `id` = $id")
                ->queryScalar();
    }

}
