<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdmissionLevel;
use App\Models\AdmissionWindow;
use App\Models\Applicant;
use App\Models\ApplicationRound;
use App\Models\ApplicationWorkflowStep;
use App\Models\Campus;
use App\Models\Country;
use App\Models\Department;
use App\Models\District;
use App\Models\Faculty;
use App\Models\Permission;
use App\Models\Programme;
use App\Models\Region;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Countries ──────────────────────────────────────────────
        $countries = [
            ['name' => 'Tanzania', 'code' => 'TZ', 'nationality' => 'Tanzanian'],
            ['name' => 'Kenya', 'code' => 'KE', 'nationality' => 'Kenyan'],
            ['name' => 'Uganda', 'code' => 'UG', 'nationality' => 'Ugandan'],
            ['name' => 'Rwanda', 'code' => 'RW', 'nationality' => 'Rwandan'],
            ['name' => 'Burundi', 'code' => 'BI', 'nationality' => 'Burundian'],
            ['name' => 'Zambia', 'code' => 'ZM', 'nationality' => 'Zambian'],
            ['name' => 'Malawi', 'code' => 'MW', 'nationality' => 'Malawian'],
            ['name' => 'Mozambique', 'code' => 'MZ', 'nationality' => 'Mozambican'],
            ['name' => 'Nigeria', 'code' => 'NG', 'nationality' => 'Nigerian'],
            ['name' => 'South Africa', 'code' => 'ZA', 'nationality' => 'South African'],
            ['name' => 'United States', 'code' => 'US', 'nationality' => 'American'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'nationality' => 'British'],
            ['name' => 'India', 'code' => 'IN', 'nationality' => 'Indian'],
            ['name' => 'China', 'code' => 'CN', 'nationality' => 'Chinese'],
        ];
        foreach ($countries as $c) {
            Country::firstOrCreate(['code' => $c['code']], $c);
        }
        $tz = Country::where('code', 'TZ')->first();

        // ── Regions / Districts / Wards (Tanzania) ─────────────────
        $regionsData = [
            'Dodoma' => ['Dodoma Urban' => ['Kikuyu', 'Viwandani'], 'Bahi' => ['Bahi', 'Chikola']],
            'Dar es Salaam' => ['Ilala' => ['Kivukoni', 'Upanga'], 'Kinondoni' => ['Kawe', 'Mbezi']],
            'Arusha' => ['Arusha City' => ['Kaloleni', 'Sokon I'], 'Meru' => ['Mbuguni', 'Nkoaranga']],
            'Mwanza' => ['Ilemela' => ['Bugogwa', 'Buswelu'], 'Nyamagana' => ['Igogo', 'Mkolani']],
            'Mbeya' => ['Mbeya City' => ['Iyunga', 'Sisimba'], 'Rungwe' => ['Tukuyu', 'Kiwira']],
            'Morogoro' => ['Morogoro Urban' => ['Kihonda', 'Mazimbu'], 'Kilosa' => ['Kilosa', 'Mikumi']],
        ];
        foreach ($regionsData as $rName => $districts) {
            $region = Region::firstOrCreate(['name' => $rName]);
            foreach ($districts as $dName => $wards) {
                $district = District::firstOrCreate(['region_id' => $region->id, 'name' => $dName]);
                foreach ($wards as $wName) {
                    Ward::firstOrCreate(['district_id' => $district->id, 'name' => $wName]);
                }
            }
        }

        // ── Admission Levels ───────────────────────────────────────
        $levels = [
            ['name' => 'Certificate', 'code' => 'CERT', 'short_name' => 'Cert', 'sort_order' => 1],
            ['name' => 'Diploma', 'code' => 'DIP', 'short_name' => 'Dip', 'sort_order' => 2],
            ['name' => 'Bachelor', 'code' => 'BSC', 'short_name' => 'BSc', 'sort_order' => 3],
            ['name' => 'Postgraduate Diploma', 'code' => 'PGD', 'short_name' => 'PGD', 'sort_order' => 4],
            ['name' => 'Masters', 'code' => 'MSC', 'short_name' => 'MSc', 'sort_order' => 5],
            ['name' => 'PhD', 'code' => 'PHD', 'short_name' => 'PhD', 'sort_order' => 6],
        ];
        foreach ($levels as $lv) {
            AdmissionLevel::firstOrCreate(['code' => $lv['code']], $lv);
        }

        // ── Campuses ───────────────────────────────────────────────
        $campuses = [
            ['name' => 'Main Campus', 'code' => 'MAIN', 'location' => 'Dodoma'],
            ['name' => 'City Campus', 'code' => 'CITY', 'location' => 'Dar es Salaam'],
            ['name' => 'Arusha Campus', 'code' => 'ARUSHA', 'location' => 'Arusha'],
        ];
        foreach ($campuses as $c) {
            Campus::firstOrCreate(['code' => $c['code']], $c);
        }
        $mainCampus = Campus::where('code', 'MAIN')->first();

        // ── Faculties & Departments ────────────────────────────────
        $faculties = [
            'College of Informatics & Virtual Education' => ['Software Engineering', 'Information Systems', 'Computer Science'],
            'College of Health Sciences' => ['Medicine', 'Pharmacy', 'Nursing', 'Medical Laboratory'],
            'College of Natural Sciences' => ['Biotechnology', 'Chemistry', 'Physics', 'Mathematics'],
            'College of Business & Economics' => ['Accounting', 'Business Administration', 'Economics'],
            'College of Education' => ['Educational Psychology', 'Curriculum Studies'],
        ];
        foreach ($faculties as $fName => $depts) {
            $faculty = Faculty::firstOrCreate(['name' => $fName]);
            foreach ($depts as $dName) {
                Department::firstOrCreate(['faculty_id' => $faculty->id, 'name' => $dName]);
            }
        }

        // ── Programmes ─────────────────────────────────────────────
        $levelMap = AdmissionLevel::pluck('id', 'code');
        $campusId = $mainCampus->id;

        $programmes = [
            // Bachelor
            ['code' => 'DM005', 'name' => 'Bachelor of Science in Software Engineering', 'level' => 'BSC', 'dept' => 'Software Engineering', 'years' => 4, 'fee' => 1500000, 'capacity' => 150],
            ['code' => 'DM006', 'name' => 'Bachelor of Science in Information Technology with Business Analytics', 'level' => 'BSC', 'dept' => 'Information Systems', 'years' => 3, 'fee' => 1300000, 'capacity' => 120],
            ['code' => 'DM007', 'name' => 'Bachelor of Science in Information Systems', 'level' => 'BSC', 'dept' => 'Information Systems', 'years' => 3, 'fee' => 1300000, 'capacity' => 100],
            ['code' => 'DM008', 'name' => 'Bachelor of Science in Biotechnology and Bioinformatics', 'level' => 'BSC', 'dept' => 'Biotechnology', 'years' => 3, 'fee' => 1400000, 'capacity' => 80],
            ['code' => 'DM009', 'name' => 'Bachelor of Medical Laboratory Sciences', 'level' => 'BSC', 'dept' => 'Medical Laboratory', 'years' => 4, 'fee' => 1800000, 'capacity' => 100],
            ['code' => 'DM010', 'name' => 'Bachelor of Science in Biomedical Engineering', 'level' => 'BSC', 'dept' => 'Medical Laboratory', 'years' => 4, 'fee' => 1700000, 'capacity' => 60],
            ['code' => 'DM011', 'name' => 'Bachelor of Pharmacy', 'level' => 'BSC', 'dept' => 'Pharmacy', 'years' => 4, 'fee' => 2000000, 'capacity' => 80],
            // Diploma
            ['code' => 'DP001', 'name' => 'Diploma in Information Technology', 'level' => 'DIP', 'dept' => 'Information Systems', 'years' => 2, 'fee' => 900000, 'capacity' => 100],
            ['code' => 'DP002', 'name' => 'Diploma in Medical Laboratory', 'level' => 'DIP', 'dept' => 'Medical Laboratory', 'years' => 2, 'fee' => 1100000, 'capacity' => 80],
            // Masters
            ['code' => 'MS001', 'name' => 'Masters of Science in Software Engineering', 'level' => 'MSC', 'dept' => 'Software Engineering', 'years' => 2, 'fee' => 2500000, 'capacity' => 40],
            ['code' => 'MS002', 'name' => 'Masters of Business Administration', 'level' => 'MSC', 'dept' => 'Business Administration', 'years' => 2, 'fee' => 2200000, 'capacity' => 60],
            // PhD
            ['code' => 'PH001', 'name' => 'PhD in Computer Science', 'level' => 'PHD', 'dept' => 'Computer Science', 'years' => 3, 'fee' => 3000000, 'capacity' => 20],
            // PGD
            ['code' => 'PG001', 'name' => 'Postgraduate Diploma in Education', 'level' => 'PGD', 'dept' => 'Curriculum Studies', 'years' => 1, 'fee' => 1200000, 'capacity' => 60],
        ];

        foreach ($programmes as $p) {
            $dept = Department::where('name', $p['dept'])->first();
            if (! $dept) {
                continue;
            }
            $prog = Programme::firstOrCreate(['code' => $p['code']], [
                'department_id'      => $dept->id,
                'campus_id'          => $campusId,
                'admission_level_id' => $levelMap[$p['level']],
                'name'               => $p['name'],
                'duration_years'     => $p['years'],
                'study_mode'         => 'Full Time',
                'tuition_fee'        => $p['fee'],
                'capacity'           => $p['capacity'],
                'status'             => 'active',
            ]);

            // Requirements (for Bachelor programmes, example)
            if ($p['level'] === 'BSC' && $prog->requirements()->count() === 0) {
                $prog->requirements()->createMany([
                    ['subject' => 'Mathematics', 'minimum_grade' => 'D'],
                    ['subject' => 'Physics', 'minimum_grade' => 'D'],
                    ['minimum_principal_passes' => 2, 'requirement_text' => 'Two principal passes'],
                ]);
            }
        }

        // ── Academic Years ─────────────────────────────────────────
        $year2026 = AcademicYear::firstOrCreate(['name' => '2026/2027'], [
            'start_date' => '2026-09-01',
            'end_date'   => '2027-08-31',
            'is_active'  => true,
            'status'     => 'active',
        ]);
        $year2025 = AcademicYear::firstOrCreate(['name' => '2025/2026'], [
            'start_date' => '2025-09-01',
            'end_date'   => '2026-08-31',
            'is_active'  => false,
            'status'     => 'inactive',
        ]);

        // ── Application Rounds ─────────────────────────────────────
        $rounds = [
            ['year' => $year2026->id, 'number' => 1, 'name' => 'Round 1', 'current' => false],
            ['year' => $year2026->id, 'number' => 2, 'name' => 'Round 2', 'current' => true],
            ['year' => $year2026->id, 'number' => 10, 'name' => 'Round 10', 'current' => true],
        ];
        foreach ($rounds as $rd) {
            ApplicationRound::firstOrCreate(
                ['academic_year_id' => $rd['year'], 'round_number' => $rd['number']],
                ['name' => $rd['name'], 'is_current' => $rd['current'], 'is_active' => true]
            );
        }
        $round2  = ApplicationRound::where('academic_year_id', $year2026->id)->where('round_number', 2)->first();
        $round10 = ApplicationRound::where('academic_year_id', $year2026->id)->where('round_number', 10)->first();
        $round1  = ApplicationRound::where('academic_year_id', $year2026->id)->where('round_number', 1)->first();

        // ── Admission Windows (per spec) ───────────────────────────
        // Use Africa/Dar_es_Salaam dates as in spec: Bachelor Round 2 08-21 Sep 2026, Masters/PGD/PhD Round 10 01 Mar - 30 Sep 2026
        $windows = [
            // Masters / PGD / PhD Round 10 — Tanzanian — TZS 50,000
            ['level' => 'MSC', 'round' => $round10->id, 'cat' => 'Tanzanian', 'opens' => '2026-03-01 00:00:00', 'closes' => '2026-09-30 23:59:59', 'fee' => 50000],
            ['level' => 'PGD', 'round' => $round10->id, 'cat' => 'Tanzanian', 'opens' => '2026-03-01 00:00:00', 'closes' => '2026-09-30 23:59:59', 'fee' => 50000],
            ['level' => 'PHD', 'round' => $round10->id, 'cat' => 'Tanzanian', 'opens' => '2026-03-01 00:00:00', 'closes' => '2026-09-30 23:59:59', 'fee' => 50000],
            // Bachelor / Diploma Round 2 — Tanzanian — TZS 10,000
            ['level' => 'BSC', 'round' => $round2->id, 'cat' => 'Tanzanian', 'opens' => '2026-09-08 00:00:00', 'closes' => '2026-09-21 23:59:59', 'fee' => 10000],
            ['level' => 'DIP', 'round' => $round2->id, 'cat' => 'Tanzanian', 'opens' => '2026-09-08 00:00:00', 'closes' => '2026-09-21 23:59:59', 'fee' => 10000],
            // Bachelor / Diploma Round 2 — Foreign — FREE
            ['level' => 'BSC', 'round' => $round2->id, 'cat' => 'Foreign', 'opens' => '2026-09-08 00:00:00', 'closes' => '2026-09-21 23:59:59', 'fee' => 0],
            ['level' => 'DIP', 'round' => $round2->id, 'cat' => 'Foreign', 'opens' => '2026-09-08 00:00:00', 'closes' => '2026-09-21 23:59:59', 'fee' => 0],
            // Historical Round 1 windows (closed)
            ['level' => 'BSC', 'round' => $round1->id, 'cat' => 'Tanzanian', 'opens' => '2026-07-01 00:00:00', 'closes' => '2026-08-15 23:59:59', 'fee' => 10000],
        ];

        foreach ($windows as $w) {
            $levelId = $levelMap[$w['level']];
            $exists = AdmissionWindow::where('admission_level_id', $levelId)
                ->where('application_round_id', $w['round'])
                ->where('applicant_category', $w['cat'])
                ->exists();
            if (! $exists) {
                AdmissionWindow::create([
                    'academic_year_id'     => $year2026->id,
                    'admission_level_id'   => $levelId,
                    'application_round_id' => $w['round'],
                    'applicant_category'   => $w['cat'],
                    'opens_at'             => $w['opens'],
                    'closes_at'            => $w['closes'],
                    'timezone'             => 'Africa/Dar_es_Salaam',
                    'application_fee'      => $w['fee'],
                    'currency'             => 'TZS',
                    'status'               => 'active',
                ]);
            }
        }

        // ── Workflow Steps (dynamic per level) ─────────────────────
        $workflows = [
            'BSC' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Results', 'academic-results'],
                ['Programme Application', 'programme-application'],
                ['Submit Application', 'submit'],
            ],
            'DIP' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Results', 'academic-results'],
                ['Programme Application', 'programme-application'],
                ['Submit Application', 'submit'],
            ],
            'CERT' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Results', 'academic-results'],
                ['Programme Application', 'programme-application'],
                ['Submit Application', 'submit'],
            ],
            'PGD' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Qualifications', 'academic-results'],
                ['Programme Application', 'programme-application'],
                ['Documents', 'documents'],
                ['Submit Application', 'submit'],
            ],
            'MSC' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Qualifications', 'academic-results'],
                ['Programme Application', 'programme-application'],
                ['Research Information', 'documents'],
                ['Documents', 'documents'],
                ['Submit Application', 'submit'],
            ],
            'PHD' => [
                ['Personal Information', 'personal-info'],
                ['Payment', 'payment'],
                ['Academic Background', 'academic-results'],
                ['Programme', 'programme-application'],
                ['Research Proposal', 'documents'],
                ['Supervisor Information', 'documents'],
                ['Documents', 'documents'],
                ['Submit Application', 'submit'],
            ],
        ];

        foreach ($workflows as $code => $steps) {
            $levelId = $levelMap[$code];
            foreach ($steps as $idx => [$name, $route]) {
                // Deduplicate: only first step per route number; on conflicts update name
                ApplicationWorkflowStep::firstOrCreate(
                    ['admission_level_id' => $levelId, 'step_number' => $idx + 1],
                    ['step_name' => $name, 'route' => $route, 'is_required' => true, 'is_active' => true]
                );
            }
        }

        // ── Roles & Permissions ────────────────────────────────────
        $rolesData = [
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'Applicant', 'slug' => 'applicant'],
        ];
        foreach ($rolesData as $rd) {
            Role::firstOrCreate(['slug' => $rd['slug']], $rd);
        }

        $perms = [
            ['name' => 'Manage Academic Years', 'slug' => 'manage-academic-years', 'module' => 'admission'],
            ['name' => 'Manage Admission Windows', 'slug' => 'manage-windows', 'module' => 'admission'],
            ['name' => 'Manage Programmes', 'slug' => 'manage-programmes', 'module' => 'academic'],
            ['name' => 'Manage Applications', 'slug' => 'manage-applications', 'module' => 'admission'],
            ['name' => 'Verify Documents', 'slug' => 'verify-documents', 'module' => 'verification'],
            ['name' => 'Manage Payments', 'slug' => 'manage-payments', 'module' => 'finance'],
            ['name' => 'Run Selection', 'slug' => 'run-selection', 'module' => 'selection'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'module' => 'admin'],
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'reports'],
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['slug' => $p['slug']], $p);
        }

        $adminRole = Role::where('slug', 'super_admin')->first();
        $adminRole?->permissions()->syncWithoutDetaching(Permission::pluck('id'));

        // ── Settings ───────────────────────────────────────────────
        $settings = [
            ['key' => 'university_name', 'value' => 'University of Dodoma', 'group' => 'general'],
            ['key' => 'university_acronym', 'value' => 'UDOM', 'group' => 'general'],
            ['key' => 'admissions_email', 'value' => 'admissions@udom.ac.tz', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Africa/Dar_es_Salaam', 'group' => 'general'],
            ['key' => 'payment_reference_format', 'value' => 'UDOM', 'group' => 'finance'],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // ── Users ──────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@university.ac.tz'], [
            'name'      => 'System Administrator',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->roles()->syncWithoutDetaching(Role::where('slug', 'super_admin')->pluck('id'));

        $staff = User::firstOrCreate(['email' => 'staff@university.ac.tz'], [
            'name'      => 'Admissions Officer',
            'password'  => Hash::make('password'),
            'role'      => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $staff->roles()->syncWithoutDetaching(Role::where('slug', 'staff')->pluck('id'));

        // Demo applicant — Tausila Macarius Mlula
        $tausilaUser = User::firstOrCreate(['email' => 'tausila@example.com'], [
            'name'      => 'Tausila Macarius Mlula',
            'phone'     => '+255779643305',
            'password'  => Hash::make('password'),
            'role'      => 'applicant',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        if (! $tausilaUser->applicant) {
            Applicant::create([
                'user_id'           => $tausilaUser->id,
                'applicant_number'  => 'APP-2026-000001',
                'first_name'        => 'Tausila',
                'middle_name'       => 'Macarius',
                'last_name'         => 'Mlula',
                'date_of_birth'     => '2006-10-25',
                'gender'            => 'Female',
                'citizenship_id'    => $tz->id,
                'phone'             => '+255779643305',
                'email'             => 'tausila@example.com',
                'disability_status' => false,
                'exam_index_number' => 'S1234/0123',
            ]);
        }
    }
}