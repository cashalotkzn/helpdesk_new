<?php

use Illuminate\Database\Seeder;

class IssueStatusColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('issue_status_colors')->insert([
            'title' => 'blue'
        ]);
        DB::table('issue_status_colors')->insert([
            'title' => 'green'
        ]);
    }
}