<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Space;
use App\Models\User;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch comments from JSON file
        $path = 'C:\\temp\\baleart\\comentaris.json';
        $json = file_get_contents($path);
        $comments = json_decode($json, true);

        // Create comments
        foreach ($comments['comentaris']['comentari'] as $comment) {
            $dateTime = DateTime::createFromFormat('d-m-Y H:i:s', $comment['data'] . ' ' . $comment['hora']);
            Comment::create([
                'comment' => $comment['text'],
                'score' => $comment['puntuacio'],
                'status' => fake()->randomElement(['y', 'n']),
                'space_id' => Space::where('reg_number', $comment['espai'])->first()->id,
                'user_id' => User::where('email', $comment['usuari'])->first()->id,
                'created_at' => $dateTime->format('Y-m-d H:i:s'),
                'updated_at' => $dateTime->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
