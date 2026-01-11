<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class ContentSeeder extends Seeder
{
    private $forum_data = [
        'staff' => ['Staff', 'Important staff boards. Nothing in this should be public or allow anything without the forum boost power.', 1],
        'mngen' => ['Site General', 'Discussion about the site itself such as suggestions and bug reports.', 2],
        'games' => ['Fun and Games', 'Discussion for site-themed forum games or raffles.', 5],
        'oftop' => ['Off-Topic', 'Discussion for non-site things.', 6],
        'sales' => ['Trades and Sales', 'Discussion for trades and sales of site things.', 4],
        'discs' => ['Discussion', 'Discussion about playing the site such as introductions and player guides.', 3],
    ];

    private $board_data = [
        'staffgen' => ['General Staff Discussion', 'staf_gen', 'I guess? I dunno...', ['staff', null, 0], [0, null, null], [0, 0, 0], 'chatter'],


        'anns' => ['Announcements', 'anns', 'Official announcements of the site.', ['mngen', null, 1], [1, null, null], [1, 1, 0], 'bulletin'],
        'bugs' => ['Bug Reports', 'bugs', 'Having trouble with a bug, or want to report something not working like it should?', ['mngen', null, 2], [0, null, null], [1, 1, 1], 'bug'],
        'resbugs' => ['Resolved Bug Reports', 'rsbg', 'Resolved bug report threads will be moved here.', ['mngen', 'bugs', 1], [0, null, null], [1, 0, 0], 'resolved bugs'],
        'suggs' => ['Suggestions', 'sgst', 'Suggestions and requests for updates and changes to the site.', ['mngen', null, 3], [1, null, null], [1, 1, 1], 'ballot'],
        'intros' => ['Introductions', 'intr', 'Introduce yourself and greet new players.', ['mngen', null, 4], [1, null, null], [1, 1, 1], 'intro'],


        'conts' => ['Contests', 'cont', 'Feel like running a contest? May the best tribe member win!', ['games', null, 4], [1, null, 'forum-sale'], [1, 1, 1], 'trophy'],
        'raffs' => ['Raffles', 'raff', 'Raffles.', ['games', 'conts', 4], [1, null, 'forum-sale'], [1, 1, 1], 'ticket'],
        'gives' => ['Giveaways', 'givs', 'Who doesn\'t love something free? Come stop by if you\'d like some goodies!', ['games', null, 4], [1, null, 'forum-sale'], [1, 1, 1], 'gift'],
        'newgives' => ['Newbie Giveaways', 'nbgvs', 'Giveaways specifically for newer players.', ['games', 'gives', 4], [1, null, 'forum-sale'], [1, 1, 1], 'newbie gift'],
        'fgames' => ['Forum Games', 'fgam', 'Forum Games.', ['games', null, 4], [1, null, null], [1, 1, 1], 'gaming'],
        'rplay' => ['Roleplaying', 'rply', 'Roleplaying.', ['games', null, 4], [1, null, null], [1, 1, 1], 'chatter'],


        'petsales' => ['Pet Sales', 'psal', 'Sell and trade pets.', ['sales', null, 4], [1, null, 'forum-sale'], [1, 1, 1], 'pedigree'],
        'itmsales' => ['Item Sales', 'isal', 'Sell and trade items.', ['sales', null, 4], [1, null, 'forum-sale'], [1, 1, 1], 'sales'],
        'artsales' => ['Creative Sales', 'csal', 'Sell and trade creative works such as writing and drawing.', ['sales', null, 4], [1, null, 'forum-sale'], [1, 1, 1], 'artwork'],
    ];


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Storage::cleanDirectory(public_path() . '/images/forums/boards');

        $forums = DB::table('forums');
        foreach($this->forum_data as $key => $data) {
            $forum = [
                'name' => $data[0],
                'description' => $data[1],
                'sort' => $data[2],
            ];
            $id = $forums->insertGetId($forum);
            $this->forum_data[$key] = $id;
        }

        $boards = DB::table('forum_boards');
        foreach($this->board_data as $key => $data) {
            $board = [
                'name' => $data[0],
                'slug' => $data[1],
                'description' => $data[2],
                'category' => $this->forum_data[$data[3][0]],
                'parent_board' => ($data[3][1] ? $this->board_data[$data[3][1]] : null),
                'sort' => $data[3][2],
                'is_public' => $data[4][0],
                'taggable_type' => $data[4][2],
                'icon' => $data[6],
                'can_read' => $data[5][0],
                'can_post' => $data[5][1],
                'can_new' => $data[5][2],
            ];
            $id = $boards->insertGetId($board);

            // $this->addImage($id, $data[1], $data[6]);

            $this->board_data[$key] = $id; // This makes it so the parent_board will associate the correct ID.
        }
    }


    // public function addImage($id, $slug, $file)
    // {
    //  $file = database_path() . "/seeds/data/images/boards/{$file}.png";
    //  return copy($file, public_path() . "/images/forums/boards/{$id}-{$slug}.png");
    // }
}
