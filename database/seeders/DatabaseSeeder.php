<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\Institution;
use App\Models\Organization;
use App\Models\Student;
use App\Models\SkillCategory;
use App\Models\SkillProgram;
use App\Models\TrainingSession;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\AuditLog;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Roles ─────────────────────────────────────────────────────────────
        $roles = [
            ['name' => 'admin',        'display_name' => 'System Administrator', 'description' => 'Full system access'],
            ['name' => 'institution',  'display_name' => 'Institution Admin',     'description' => 'Institution-level access'],
            ['name' => 'organization', 'display_name' => 'Organization Admin',    'description' => 'Organization-level access'],
            ['name' => 'student',      'display_name' => 'Student',               'description' => 'Personal account access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $instRole  = Role::where('name', 'institution')->first();
        $orgRole   = Role::where('name', 'organization')->first();
        $studRole  = Role::where('name', 'student')->first();

        // ── 2. Default Admin ─────────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@skillconnect.co.ke'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('Password123'),
                'role_id'  => $adminRole->id,
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );

        // ── 3. Institutions ──────────────────────────────────────────────────────
        // Active Inst 1
        $uInst1 = User::firstOrCreate(
            ['email' => 'institution@skillconnect.co.ke'],
            [
                'name'     => 'Nairobi Technical Institute',
                'password' => Hash::make('Password123'),
                'role_id'  => $instRole->id,
                'phone'    => '+254700000001',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $inst1 = Institution::firstOrCreate(
            ['user_id' => $uInst1->id],
            [
                'name' => 'Nairobi Technical Institute',
                'registration_number' => 'NTI-2026-001',
                'location' => 'Nairobi, Ngong Road',
                'phone' => '+254700000001',
                'status' => 'active',
            ]
        );

        // Active Inst 2
        $uInst2 = User::firstOrCreate(
            ['email' => 'kise@skillconnect.co.ke'],
            [
                'name'     => 'Kenya Institute of Software Engineering',
                'password' => Hash::make('Password123'),
                'role_id'  => $instRole->id,
                'phone'    => '+254711111111',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $inst2 = Institution::firstOrCreate(
            ['user_id' => $uInst2->id],
            [
                'name' => 'Kenya Institute of Software Engineering',
                'registration_number' => 'KISE-2026-902',
                'location' => 'Mombasa, Nyali',
                'phone' => '+254711111111',
                'status' => 'active',
            ]
        );

        // Pending Inst 3
        $uInst3 = User::firstOrCreate(
            ['email' => 'mombasa.tvet@skillconnect.co.ke'],
            [
                'name'     => 'Mombasa College of TVET',
                'password' => Hash::make('Password123'),
                'role_id'  => $instRole->id,
                'phone'    => '+254722222222',
                'status'   => 'pending',
            ]
        );
        $inst3 = Institution::firstOrCreate(
            ['user_id' => $uInst3->id],
            [
                'name' => 'Mombasa College of TVET',
                'registration_number' => 'MCT-2026-304',
                'location' => 'Mombasa Town',
                'phone' => '+254722222222',
                'status' => 'pending',
            ]
        );

        // ── 4. Organizations ─────────────────────────────────────────────────────
        // Active Org 1
        $uOrg1 = User::firstOrCreate(
            ['email' => 'org@skillconnect.co.ke'],
            [
                'name'     => 'TechSkills Kenya Ltd',
                'password' => Hash::make('Password123'),
                'role_id'  => $orgRole->id,
                'phone'    => '+254700000002',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $org1 = Organization::firstOrCreate(
            ['user_id' => $uOrg1->id],
            [
                'name' => 'TechSkills Kenya Ltd',
                'contact_person' => 'Dennis Mwangi',
                'phone' => '+254700000002',
                'description' => 'Premier software engineering and technology bootcamp provider in East Africa.',
                'status' => 'active',
            ]
        );

        // Active Org 2
        $uOrg2 = User::firstOrCreate(
            ['email' => 'ecopower@skillconnect.co.ke'],
            [
                'name'     => 'EcoPower Vocational Training',
                'password' => Hash::make('Password123'),
                'role_id'  => $orgRole->id,
                'phone'    => '+254733333333',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $org2 = Organization::firstOrCreate(
            ['user_id' => $uOrg2->id],
            [
                'name' => 'EcoPower Vocational Training',
                'contact_person' => 'Faith Mutua',
                'phone' => '+254733333333',
                'description' => 'Providing solar and green energy installation courses.',
                'status' => 'active',
            ]
        );

        // Pending Org 3
        $uOrg3 = User::firstOrCreate(
            ['email' => 'ajira.nairobi@skillconnect.co.ke'],
            [
                'name'     => 'Ajira Digital Center Nairobi',
                'password' => Hash::make('Password123'),
                'role_id'  => $orgRole->id,
                'phone'    => '+254744444444',
                'status'   => 'pending',
            ]
        );
        $org3 = Organization::firstOrCreate(
            ['user_id' => $uOrg3->id],
            [
                'name' => 'Ajira Digital Center Nairobi',
                'contact_person' => 'John Mwangi',
                'phone' => '+254744444444',
                'description' => 'Empowering youth with digital skills for online jobs.',
                'status' => 'pending',
            ]
        );

        // ── 5. Students ──────────────────────────────────────────────────────────
        // Student 1 (Jane Wambui - NTI student)
        $uStud1 = User::firstOrCreate(
            ['email' => 'student@skillconnect.co.ke'],
            [
                'name'     => 'Jane Wambui',
                'password' => Hash::make('Password123'),
                'role_id'  => $studRole->id,
                'phone'    => '+254700000003',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $stud1 = Student::firstOrCreate(
            ['user_id' => $uStud1->id],
            [
                'institution_id' => $inst1->id,
                'registration_number' => 'NTI/2026/0912',
                'phone' => '+254700000003',
            ]
        );

        // Student 2 (David Kimani - KISE student)
        $uStud2 = User::firstOrCreate(
            ['email' => 'david@skillconnect.co.ke'],
            [
                'name'     => 'David Kimani',
                'password' => Hash::make('Password123'),
                'role_id'  => $studRole->id,
                'phone'    => '+254755555555',
                'status'   => 'active',
                'email_verified_at' => now(),
            ]
        );
        $stud2 = Student::firstOrCreate(
            ['user_id' => $uStud2->id],
            [
                'institution_id' => $inst2->id,
                'registration_number' => 'KISE/2026/0122',
                'phone' => '+254755555555',
            ]
        );

        // ── 6. Skill Categories ──────────────────────────────────────────────────
        $categories = [
            ['name' => 'Digital Skills',     'slug' => 'digital-skills',     'icon' => '💻'],
            ['name' => 'Soft Skills',        'slug' => 'soft-skills',        'icon' => '🤝'],
            ['name' => 'Vocational Skills',  'slug' => 'vocational-skills',  'icon' => '🔨'],
            ['name' => 'Entrepreneurship',   'slug' => 'entrepreneurship',   'icon' => '💼'],
        ];

        foreach ($categories as $cat) {
            SkillCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $digitalCat = SkillCategory::where('slug', 'digital-skills')->first();
        $softCat    = SkillCategory::where('slug', 'soft-skills')->first();
        $vocalCat   = SkillCategory::where('slug', 'vocational-skills')->first();

        // ── 7. Skill Programs ────────────────────────────────────────────────────
        // Program 1: React & Next.js under Org 1
        $prog1 = SkillProgram::firstOrCreate(
            ['name' => 'React & Next.js Professional'],
            [
                'organization_id' => $org1->id,
                'category_id' => $digitalCat->id,
                'name' => 'React & Next.js Professional',
                'description' => 'A comprehensive course covering React essentials, Next.js routing, API routes, data fetching, Tailwind CSS layout design, and full deployment workflows in production.',
                'duration' => '6 Weeks',
                'cost' => 15000.00,
                'mode' => 'hybrid',
                'venue' => 'TechSkills Labs, Ngong Rd / Zoom',
                'capacity' => 30,
                'requirements' => 'Proficiency in basic HTML, CSS, and Javascript variables, arrays, and functions.',
                'learning_outcomes' => 'Build reactive single-page applications; orchestrate server-side rendering and static site generation; configure Tailwind CSS custom tokens; integrate external JSON REST APIs; deploy on Vercel.',
                'status' => 'published',
            ]
        );

        // Program 2: Solar Panel Installation Tech under Org 2
        $prog2 = SkillProgram::firstOrCreate(
            ['name' => 'Solar Panel Installation Tech'],
            [
                'organization_id' => $org2->id,
                'category_id' => $vocalCat->id,
                'name' => 'Solar Panel Installation Tech',
                'description' => 'Hands-on practical training covering solar photovoltaic system design, off-grid and hybrid solar installations, battery bank sizing, and safety standards.',
                'duration' => '8 Weeks',
                'cost' => 25000.00,
                'mode' => 'in_person',
                'venue' => 'EcoPower Workshop, Thika Road',
                'capacity' => 20,
                'requirements' => 'No prior experience required. High school graduates and technical students encouraged.',
                'learning_outcomes' => 'Analyze solar radiation profiles; wire solar charge controllers, inverters, and batteries; run cabling and structural mounting; perform electrical system troubleshooting.',
                'status' => 'published',
            ]
        );

        // Program 3: Financial Literacy for Entrepreneurs under Org 1
        $prog3 = SkillProgram::firstOrCreate(
            ['name' => 'Financial Literacy for Entrepreneurs'],
            [
                'organization_id' => $org1->id,
                'category_id' => $softCat->id,
                'name' => 'Financial Literacy for Entrepreneurs',
                'description' => 'Learn basic record-keeping, cash flow management, budgeting, tax compliance, and scaling strategies for small and medium enterprises.',
                'duration' => '2 Weeks',
                'cost' => 0.00, // Free
                'mode' => 'online',
                'venue' => 'Google Meet Classroom',
                'capacity' => 100,
                'requirements' => 'Owner of a micro or small enterprise or aspiring business owner.',
                'learning_outcomes' => 'Prepare simple profit-and-loss statements; separate personal and business bank accounts; understand KRA tax obligations; structure pricing models.',
                'status' => 'published',
            ]
        );

        // ── 8. Training Sessions ─────────────────────────────────────────────────
        // Sessions for React & Next.js
        $sess1 = TrainingSession::firstOrCreate(
            ['title' => 'Session 1: Getting Started with React'],
            [
                'program_id' => $prog1->id,
                'title' => 'Session 1: Getting Started with React',
                'description' => 'JSX, Component design, props, and simple state management.',
                'start_date' => now()->subDays(5)->setHour(14)->setMinute(0)->setSecond(0),
                'end_date' => now()->subDays(5)->setHour(16)->setMinute(0)->setSecond(0),
                'venue' => 'Zoom Video Conference',
                'meeting_link' => 'https://zoom.us/j/9902012399',
                'max_participants' => 30,
                'trainer_information' => 'Dennis Mwangi - Dennis is a Lead Dev with 8 years of React expertise.',
            ]
        );

        $sess2 = TrainingSession::firstOrCreate(
            ['title' => 'Session 2: Next.js Routing & App Router'],
            [
                'program_id' => $prog1->id,
                'title' => 'Session 2: Next.js Routing & App Router',
                'description' => 'Explore folders-as-routes, layout files, pages, dynamic segments, and navigation hooks.',
                'start_date' => now()->subDays(3)->setHour(14)->setMinute(0)->setSecond(0),
                'end_date' => now()->subDays(3)->setHour(16)->setMinute(0)->setSecond(0),
                'venue' => 'Zoom Video Conference',
                'meeting_link' => 'https://zoom.us/j/9902012399',
                'max_participants' => 30,
                'trainer_information' => 'Dennis Mwangi - Lead React Engineer',
            ]
        );

        $sess3 = TrainingSession::firstOrCreate(
            ['title' => 'Session 3: Styling with Tailwind CSS & CSS Tokens'],
            [
                'program_id' => $prog1->id,
                'title' => 'Session 3: Styling with Tailwind CSS & CSS Tokens',
                'description' => 'Setting up modern styling layout, hover effects, dark-theme configuration, and premium CSS tokens.',
                'start_date' => now()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0),
                'end_date' => now()->addDays(2)->setHour(16)->setMinute(0)->setSecond(0),
                'venue' => 'TechSkills Labs, Ngong Rd',
                'meeting_link' => '',
                'max_participants' => 30,
                'trainer_information' => 'Dennis Mwangi',
            ]
        );

        // Sessions for Financial Literacy
        $sess4 = TrainingSession::firstOrCreate(
            ['title' => 'Session 1: Cashflow Management & Recordkeeping'],
            [
                'program_id' => $prog3->id,
                'title' => 'Session 1: Cashflow Management & Recordkeeping',
                'description' => 'Basic cash books, invoicing, tracking expenses, and budgeting.',
                'start_date' => now()->subDays(2)->setHour(9)->setMinute(0)->setSecond(0),
                'end_date' => now()->subDays(2)->setHour(11)->setMinute(0)->setSecond(0),
                'venue' => 'Google Meet Platform',
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'max_participants' => 100,
                'trainer_information' => 'Dennis Mwangi',
            ]
        );

        // ── 9. Enrollments & Payments ────────────────────────────────────────────
        // Jane Wambui enrolled in React & Next.js (Paid program - approved status)
        $enr1 = Enrollment::firstOrCreate(
            ['student_id' => $stud1->id, 'program_id' => $prog1->id],
            ['status' => 'approved']
        );
        Payment::firstOrCreate(
            ['enrollment_id' => $enr1->id],
            [
                'student_id' => $stud1->id,
                'amount' => 15000.00,
                'payment_method' => 'M-Pesa',
                'transaction_reference' => 'QRF82KJS91',
                'status' => 'paid',
            ]
        );

        // David Kimani enrolled in Financial Literacy (Free program - completed status)
        $enr2 = Enrollment::firstOrCreate(
            ['student_id' => $stud2->id, 'program_id' => $prog3->id],
            ['status' => 'completed']
        );
        Payment::firstOrCreate(
            ['enrollment_id' => $enr2->id],
            [
                'student_id' => $stud2->id,
                'amount' => 0.00,
                'payment_method' => 'Free',
                'transaction_reference' => 'FREE-TX-8822',
                'status' => 'paid',
            ]
        );

        // ── 10. Attendance ───────────────────────────────────────────────────────
        // Jane attended React Session 1 & 2
        Attendance::firstOrCreate(
            ['session_id' => $sess1->id, 'student_id' => $stud1->id],
            ['status' => 'present', 'verification_method' => 'manual', 'marked_at' => now()->subDays(5)->setHour(14)->setMinute(15)]
        );
        Attendance::firstOrCreate(
            ['session_id' => $sess2->id, 'student_id' => $stud1->id],
            ['status' => 'present', 'verification_method' => 'qr_scan', 'marked_at' => now()->subDays(3)->setHour(14)->setMinute(05)]
        );

        // David attended Financial Session 1
        Attendance::firstOrCreate(
            ['session_id' => $sess4->id, 'student_id' => $stud2->id],
            ['status' => 'present', 'verification_method' => 'manual', 'marked_at' => now()->subDays(2)->setHour(9)->setMinute(10)]
        );

        // ── 11. Certificates ─────────────────────────────────────────────────────
        // David gets a certificate because enrollment is completed
        Certificate::firstOrCreate(
            ['enrollment_id' => $enr2->id],
            [
                'student_id' => $stud2->id,
                'certificate_number' => 'SC-2026-CERT0001',
                'verification_code' => 'VERIFY-DK-891',
                'issue_date' => now()->subDay(),
            ]
        );

        // ── 12. Audit Logs ───────────────────────────────────────────────────────
        AuditLog::log($uInst1->id, 'register_institution', 'Institution profile registered for Nairobi Technical Institute.');
        AuditLog::log($uOrg1->id, 'register_organization', 'Organization profile registered for TechSkills Kenya Ltd.');
        AuditLog::log($uStud1->id, 'enroll_program', 'Student enrolled in React & Next.js Professional.');
    }
}

