<?php


use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    /**
     * Run migration
     */
    public function up()
    {
        $users = $this->table('users');
        $users
            ->addColumn('username', 'string', ['limit' => 120])
            ->addColumn('password', 'string', ['limit' => 255])
            ->addColumn('firstname', 'string', ['limit' => 30])
            ->addColumn('lastname', 'string', ['limit' => 30])
            ->addColumn('age', 'integer')
            ->addIndex(['username'], ['unique' => true])
            ->save();

        $tracker = $this->table('trackers');
        $tracker
            ->addColumn('id_user', 'integer', ['null' => true])
            ->addColumn('button_label', 'string', ['limit' => 255])
            ->addColumn('date_created', 'datetime')
            ->addForeignKey('id_user', 'users', 'id', [
                'delete'=> 'NO_ACTION',
                'update'=> 'NO_ACTION'
            ])
            ->save();
    }

    /**
     * Roll back migration
     */
    public function down()
    {
        $this->dropTable('users');
        $this->dropTable('trackers');
    }
}
