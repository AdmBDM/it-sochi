<?php

use yii\db\Migration;

/**
 * Organizational structure: organizations, buildings, departments, locations.
 */
class m250625_142636_create_organization_and_location_structure extends Migration
{
    /**
     * @return void
     */
    public function safeUp()
    {
        // organizations
        $this->createTable('{{%organizations}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_organizations BEFORE UPDATE ON organizations FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // buildings
        $this->createTable('{{%buildings}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(255),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_buildings BEFORE UPDATE ON buildings FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // organization_building (pivot)
        $this->createTable('{{%organization_building}}', [
            'id' => $this->primaryKey(),
            'organization_id' => $this->integer()->notNull(),
            'building_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_org_building_org', 'organization_building', 'organization_id', 'organizations', 'id', 'CASCADE');
        $this->addForeignKey('fk_org_building_building', 'organization_building', 'building_id', 'buildings', 'id', 'CASCADE');
        $this->execute("CREATE TRIGGER trg_set_updated_at_organization_building BEFORE UPDATE ON organization_building FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // departments
        $this->createTable('{{%departments}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->execute("CREATE TRIGGER trg_set_updated_at_departments BEFORE UPDATE ON departments FOR EACH ROW EXECUTE FUNCTION set_updated_at();");

        // locations
        $this->createTable('{{%locations}}', [
            'id' => $this->primaryKey(),
            'building_id' => $this->integer()->notNull(),
            'floor' => $this->string(20)->notNull(),
            'room' => $this->string(50)->notNull(),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('now()'),
        ]);
        $this->addForeignKey('fk_locations_building', 'locations', 'building_id', 'buildings', 'id', 'CASCADE');
        $this->execute("CREATE TRIGGER trg_set_updated_at_locations BEFORE UPDATE ON locations FOR EACH ROW EXECUTE FUNCTION set_updated_at();");
    }

    /**
     * @return void
     */
    public function safeDown()
    {
        $tables = [
            'locations',
            'departments',
            'organization_building',
            'buildings',
            'organizations',
        ];

        foreach ($tables as $table) {
            $this->execute("DROP TRIGGER IF EXISTS trg_set_updated_at_{$table} ON {$table};");
        }

        $this->dropForeignKey('fk_locations_building', 'locations');
        $this->dropForeignKey('fk_org_building_building', 'organization_building');
        $this->dropForeignKey('fk_org_building_org', 'organization_building');

        foreach ($tables as $table) {
            $this->dropTable("{{%$table}}");
        }
    }
}
