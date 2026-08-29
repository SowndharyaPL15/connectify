<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo Users
        $usersData = [
            ['name' => 'Alice Johnson',  'email' => 'alice@demo.com',   'about' => 'Hey there! I am Alice.'],
            ['name' => 'Bob Smith',      'email' => 'bob@demo.com',     'about' => 'Available'],
            ['name' => 'Carol Williams', 'email' => 'carol@demo.com',   'about' => 'Busy 🚀'],
            ['name' => 'Dave Brown',     'email' => 'dave@demo.com',    'about' => 'At work'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $users[$data['email']] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'about'    => $data['about'],
                    'status'   => 'offline',
                ]
            );
        }

        $alice = $users['alice@demo.com'];
        $bob = $users['bob@demo.com'];
        $carol = $users['carol@demo.com'];
        $dave = $users['dave@demo.com'];

        // 2. Create Contacts (Mutual contacts for all demo users)
        $allUsers = [$alice, $bob, $carol, $dave];
        foreach ($allUsers as $u1) {
            foreach ($allUsers as $u2) {
                if ($u1->id !== $u2->id) {
                    Contact::firstOrCreate([
                        'user_id' => $u1->id,
                        'contact_id' => $u2->id,
                    ], [
                        'alias_name' => $u2->name,
                    ]);
                }
            }
        }

        // 3. Create Conversations
        // DM 1: Alice & Bob
        $conversationAliceBob = Conversation::create([
            'is_group' => false,
        ]);
        $conversationAliceBob->users()->attach([$alice->id => ['role' => 'member'], $bob->id => ['role' => 'member']]);

        // DM 2: Alice & Carol
        $conversationAliceCarol = Conversation::create([
            'is_group' => false,
        ]);
        $conversationAliceCarol->users()->attach([$alice->id => ['role' => 'member'], $carol->id => ['role' => 'member']]);

        // Group Chat: Tech Enthusiasts
        $techGroup = Conversation::create([
            'name' => 'Tech Enthusiasts 🚀',
            'description' => 'A group for discussing web dev and AI.',
            'is_group' => true,
        ]);
        $techGroup->users()->attach([
            $alice->id => ['role' => 'admin'],
            $bob->id => ['role' => 'member'],
            $carol->id => ['role' => 'member'],
            $dave->id => ['role' => 'member'],
        ]);

        // 4. Seed Messages in DM (Alice & Bob)
        Message::create([
            'conversation_id' => $conversationAliceBob->id,
            'sender_id' => $alice->id,
            'body' => 'Hey Bob! How is the new chat app project going?',
            'status' => 'read',
            'created_at' => now()->subMinutes(30),
        ]);
        Message::create([
            'conversation_id' => $conversationAliceBob->id,
            'sender_id' => $bob->id,
            'body' => 'Hey Alice! It\'s going awesome. Antigravity just converted our DB to MySQL and seeded everything perfectly! 🚀',
            'status' => 'read',
            'created_at' => now()->subMinutes(25),
        ]);
        Message::create([
            'conversation_id' => $conversationAliceBob->id,
            'sender_id' => $alice->id,
            'body' => 'Wow, that is super cool! Let\'s test out the real-time chat features now.',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(20),
        ]);

        // 5. Seed Messages in DM (Alice & Carol)
        Message::create([
            'conversation_id' => $conversationAliceCarol->id,
            'sender_id' => $carol->id,
            'body' => 'Hi Alice! Are we still reviewing the database ER diagram at 5 PM?',
            'status' => 'read',
            'created_at' => now()->subMinutes(15),
        ]);
        Message::create([
            'conversation_id' => $conversationAliceCarol->id,
            'sender_id' => $alice->id,
            'body' => 'Hey Carol! Yes, absolutely. We will use DBeaver to show the ER diagram, it looks super professional.',
            'status' => 'delivered',
            'created_at' => now()->subMinutes(10),
        ]);

        // 6. Seed Messages in Group Chat (Tech Enthusiasts)
        Message::create([
            'conversation_id' => $techGroup->id,
            'sender_id' => $alice->id,
            'body' => 'Welcome everyone to the Tech Enthusiasts group chat! 🎉',
            'status' => 'read',
            'created_at' => now()->subMinutes(8),
        ]);
        Message::create([
            'conversation_id' => $techGroup->id,
            'sender_id' => $bob->id,
            'body' => 'Hello team! Ready to build some amazing features.',
            'status' => 'read',
            'created_at' => now()->subMinutes(6),
        ]);
        Message::create([
            'conversation_id' => $techGroup->id,
            'sender_id' => $carol->id,
            'body' => 'Hey guys! Super excited to be in this group.',
            'status' => 'read',
            'created_at' => now()->subMinutes(4),
        ]);
        Message::create([
            'conversation_id' => $techGroup->id,
            'sender_id' => $dave->id,
            'body' => 'Hi all, sorry I\'m a bit late! Joining in now.',
            'status' => 'read',
            'created_at' => now()->subMinutes(2),
        ]);

        $this->command->info('✅ Rich demo data seeded successfully!');
        $this->command->table(
            ['Name', 'Email'],
            collect($usersData)->map(fn($u) => [$u['name'], $u['email']])->toArray()
        );
    }
}
