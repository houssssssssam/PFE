<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ──────────────────────────────────────────────
        $categories = [
            ['name' => 'Médecine',        'slug' => 'medecine',        'icon' => '🏥', 'description' => 'Consultations médicales et conseils de santé'],
            ['name' => 'Droit',           'slug' => 'droit',           'icon' => '⚖️', 'description' => 'Conseils juridiques et assistance légale'],
            ['name' => 'Fiscalité',       'slug' => 'fiscalite',       'icon' => '📊', 'description' => 'Optimisation fiscale et déclarations'],
            ['name' => 'Administration',  'slug' => 'administration',  'icon' => '🏛️', 'description' => 'Démarches administratives et formulaires'],
            ['name' => 'Finance',         'slug' => 'finance',         'icon' => '💰', 'description' => 'Gestion financière et investissements'],
            ['name' => 'Technologie',     'slug' => 'technologie',     'icon' => '💻', 'description' => 'Assistance technique et numérique'],
            ['name' => 'Éducation',       'slug' => 'education',       'icon' => '📚', 'description' => 'Soutien scolaire et orientation'],
            ['name' => 'Entrepreneuriat', 'slug' => 'entrepreneuriat', 'icon' => '🚀', 'description' => 'Création et développement d\'entreprise'],
        ];

        foreach ($categories as $i => $cat) {
            Category::create(array_merge($cat, ['sort_order' => $i, 'is_active' => true]));
        }

        $catIds = Category::pluck('id', 'slug');

        // ── Admin ────────────────────────────────────────────────────
        User::create([
            'name'              => 'Admin Nexora',
            'email'             => 'admin@nexora.ma',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);

        // ── Regular users ────────────────────────────────────────────
        $regularUsers = [
            ['name' => 'Houssam Fatih',  'email' => 'houssam@test.ma'],
            ['name' => 'Fatima Zahra',   'email' => 'fatima@test.ma'],
            ['name' => 'Youssef Alami',  'email' => 'youssef@test.ma'],
            ['name' => 'Nadia Benali',   'email' => 'nadia@test.ma'],
            ['name' => 'Omar Tahiri',    'email' => 'omar@test.ma'],
        ];

        foreach ($regularUsers as $u) {
            User::create(array_merge($u, [
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]));
        }

        // ── Experts (validated) ──────────────────────────────────────
        $experts = [
            [
                'name'        => 'Dr. Karim Bensouda',
                'email'       => 'karim.bensouda@nexora.ma',
                'category'    => 'medecine',
                'bio'         => 'Médecin généraliste avec 15 ans d\'expérience. Spécialisé en médecine interne et prévention des maladies chroniques. Diplômé de la Faculté de Médecine de Casablanca.',
                'hourly_rate' => 350,
                'rating_avg'  => 4.9,
                'total_reviews' => 128,
            ],
            [
                'name'        => 'Me. Salma Chraibi',
                'email'       => 'salma.chraibi@nexora.ma',
                'category'    => 'droit',
                'bio'         => 'Avocate au barreau de Rabat depuis 10 ans. Experte en droit des affaires, droit de la famille et contentieux commercial. Ancienne collaboratrice d\'un cabinet international.',
                'hourly_rate' => 450,
                'rating_avg'  => 4.8,
                'total_reviews' => 95,
            ],
            [
                'name'        => 'Rachid Moussaoui',
                'email'       => 'rachid.moussaoui@nexora.ma',
                'category'    => 'fiscalite',
                'bio'         => 'Expert-comptable et conseiller fiscal agréé. 12 ans d\'expérience en optimisation fiscale pour PME et particuliers. Certifié par l\'Ordre des Experts-Comptables du Maroc.',
                'hourly_rate' => 400,
                'rating_avg'  => 4.7,
                'total_reviews' => 74,
            ],
            [
                'name'        => 'Zineb Hajji',
                'email'       => 'zineb.hajji@nexora.ma',
                'category'    => 'finance',
                'bio'         => 'Conseillère en investissement et gestion de patrimoine. Ancienne analyste chez une grande banque marocaine. Spécialisée en planification financière et retraite.',
                'hourly_rate' => 380,
                'rating_avg'  => 4.6,
                'total_reviews' => 61,
            ],
            [
                'name'        => 'Amine Kettani',
                'email'       => 'amine.kettani@nexora.ma',
                'category'    => 'technologie',
                'bio'         => 'Ingénieur logiciel senior avec 8 ans d\'expérience. Expert en développement web, cybersécurité et transformation digitale. Co-fondateur de deux startups tech.',
                'hourly_rate' => 300,
                'rating_avg'  => 4.8,
                'total_reviews' => 112,
            ],
            [
                'name'        => 'Prof. Laila Mernissi',
                'email'       => 'laila.mernissi@nexora.ma',
                'category'    => 'education',
                'bio'         => 'Professeure universitaire en sciences de l\'éducation. 20 ans d\'expérience dans l\'enseignement supérieur. Spécialisée en pédagogie active et orientation professionnelle.',
                'hourly_rate' => 250,
                'rating_avg'  => 4.9,
                'total_reviews' => 203,
            ],
            [
                'name'        => 'Hassan Ouazzani',
                'email'       => 'hassan.ouazzani@nexora.ma',
                'category'    => 'entrepreneuriat',
                'bio'         => 'Serial entrepreneur et mentor. A fondé et revendu 3 entreprises. Expert en levée de fonds, stratégie business et développement commercial au Maroc et en Afrique.',
                'hourly_rate' => 500,
                'rating_avg'  => 4.7,
                'total_reviews' => 89,
            ],
            [
                'name'        => 'Meryem Lahlou',
                'email'       => 'meryem.lahlou@nexora.ma',
                'category'    => 'administration',
                'bio'         => 'Ancienne fonctionnaire et spécialiste des démarches administratives marocaines. Aide particuliers et entreprises à naviguer les procédures officielles efficacement.',
                'hourly_rate' => 200,
                'rating_avg'  => 4.5,
                'total_reviews' => 47,
            ],
        ];

        foreach ($experts as $i => $data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'role'              => 'expert',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);

            Expert::create([
                'user_id'        => $user->id,
                'category_id'    => $catIds[$data['category']],
                'bio'            => $data['bio'],
                'hourly_rate'    => $data['hourly_rate'],
                'rating_avg'     => $data['rating_avg'],
                'total_reviews'  => $data['total_reviews'],
                'is_available'   => $i !== 2, // Rachid is busy
                'status'         => 'validated',
                'validated_at'   => now(),
                'certifications' => json_encode(['Certifié par ordre professionnel']),
            ]);
        }

        // ── 2 pending experts ────────────────────────────────────────
        $pending = [
            ['name' => 'Khalid Bouchta', 'email' => 'khalid@nexora.ma', 'category' => 'droit'],
            ['name' => 'Sara Fennich',   'email' => 'sara@nexora.ma',   'category' => 'medecine'],
        ];

        foreach ($pending as $data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'role'              => 'expert',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);

            Expert::create([
                'user_id'       => $user->id,
                'category_id'   => $catIds[$data['category']],
                'bio'           => 'Expert en attente de validation.',
                'hourly_rate'   => 300,
                'rating_avg'    => 0,
                'total_reviews' => 0,
                'is_available'  => false,
                'status'        => 'pending',
            ]);
        }
    }
}
