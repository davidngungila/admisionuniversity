<?php

namespace Database\Seeders;

use App\Models\AdmissionLevel;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MocuProgrammeCatalogueSeeder extends Seeder
{
    protected array $deptCache = [];
    protected array $levelCache = [];

    public function run(): void
    {
        // ---- Campuses ----
        $main = DB::table('campuses')->where('name', 'like', '%Main%')->value('id');
        $kiz  = DB::table('campuses')->where('name', 'like', '%Kizumbi%')->value('id');
        if (!$kiz) {
            $kiz = DB::table('campuses')->insertGetId([
                'name' => 'Kizumbi Institute of Co-operative and Business Education',
                'code' => 'KIZ', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $rows = [
            // ================= CERTIFICATE — Main Campus (1 Year) =================
            ['code'=>'CLIS','name'=>'Certificate in Library and Information Sciences','level'=>'Certificate','dept'=>'Library and Information Science','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CAF','name'=>'Certificate in Accounting and Finance','level'=>'Certificate','dept'=>'Accounting','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CMFM','name'=>'Certificate in Microfinance Management','level'=>'Certificate','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CIT','name'=>'Certificate in Information Technology','level'=>'Certificate','dept'=>'Information Systems','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CLAW','name'=>'Certificate in Law','level'=>'Certificate','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CHRM','name'=>'Certificate in Human Resource Management','level'=>'Certificate','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CCMA','name'=>'Certificate in Co-operative Management and Accounting','level'=>'Certificate','dept'=>'Accounting','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CCQT','name'=>'Certificate in Coffee Quality and Trade','level'=>'Certificate','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],

            // ================= DIPLOMA — Main Campus (2 Years) =================
            ['code'=>'DLIS','name'=>'Diploma in Library and Information Sciences','level'=>'Diploma','dept'=>'Library and Information Science','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DMM','name'=>'Diploma in Microfinance Management','level'=>'Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DBICT','name'=>'Diploma in Business Information and Communication Technology','level'=>'Diploma','dept'=>'Information Systems','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DHRM','name'=>'Diploma in Human Resource Management','level'=>'Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DBEM','name'=>'Diploma in Business and Enterprise Management','level'=>'Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DCMA','name'=>'Diploma in Co-operative Management and Accounting','level'=>'Diploma','dept'=>'Accounting','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DMMD','name'=>'Diploma in Microfinance Management — Distance','level'=>'Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Distance'],

            // ================= BACHELOR — Main Campus (3 Years, unless noted) =================
            ['code'=>'BLIS','name'=>'Bachelor of Library and Information Science','level'=>'Bachelor','dept'=>'Library and Information Science','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAF','name'=>'Bachelor of Accounting and Finance','level'=>'Bachelor','dept'=>'Accounting','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAT','name'=>'Bachelor of Accounting and Taxation','level'=>'Bachelor','dept'=>'Accounting','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BBMF','name'=>'Bachelor of Banking and Microfinance','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BBICT','name'=>'Bachelor of Business Information and Communication Technology','level'=>'Bachelor','dept'=>'Information Systems','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSCS','name'=>'Bachelor of Science in Computer Science','level'=>'Bachelor','dept'=>'Computer Science','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSDS','name'=>'Bachelor of Science in Data Science','level'=>'Bachelor','dept'=>'Computer Science','campus'=>$main,'years'=>4,'mode'=>'Full Time'],
            ['code'=>'LLB','name'=>'Bachelor of Laws','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BHRM','name'=>'Bachelor of Human Resource Management','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BEEM','name'=>'Bachelor of Entrepreneurship and Enterprise Management','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BMM','name'=>'Bachelor of Marketing Management','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BPSCM','name'=>'Bachelor of Procurement and Supply Chain Management','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAccCM','name'=>'Bachelor of Accounting with Co-operative Management','level'=>'Bachelor','dept'=>'Accounting','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAccCM-E','name'=>'Bachelor of Accounting with Co-operative Management — Evening','level'=>'Bachelor','dept'=>'Accounting','campus'=>$main,'years'=>3,'mode'=>'Evening'],
            ['code'=>'BCM','name'=>'Bachelor of Co-operative Management','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BCED','name'=>'Bachelor of Community Economic Development','level'=>'Bachelor','dept'=>'Economics','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BCMA','name'=>'Bachelor of Co-operative Management with Agriculture','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSW','name'=>'Bachelor of Social Work','level'=>'Bachelor','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BBAIS','name'=>'Bachelor of Business Administration with Information Systems','level'=>'Bachelor','dept'=>'Information Systems','campus'=>$main,'years'=>3,'mode'=>'Full Time'],

            // ============ KIZUMBI — Certificate (1 Year) ============
            ['code'=>'KDCED','name'=>'Certificate in Enterprise Development','level'=>'Certificate','dept'=>'Business Administration','campus'=>$kiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDHRM','name'=>'Certificate in Human Resource Management','level'=>'Certificate','dept'=>'Business Administration','campus'=>$kiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDMM','name'=>'Certificate in Microfinance Management','level'=>'Certificate','dept'=>'Business Administration','campus'=>$kiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDCMA','name'=>'Certificate in Co-operative Management and Accounting','level'=>'Certificate','dept'=>'Accounting','campus'=>$kiz,'years'=>1,'mode'=>'Full Time'],

            // ============ KIZUMBI — Diploma (2 Years) ============
            ['code'=>'KDDMM','name'=>'Diploma in Microfinance Management','level'=>'Diploma','dept'=>'Business Administration','campus'=>$kiz,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'KDDCMA','name'=>'Diploma in Co-operative Management and Accounting','level'=>'Diploma','dept'=>'Accounting','campus'=>$kiz,'years'=>2,'mode'=>'Full Time'],

            // ============ KIZUMBI — Bachelor (3 Years) ============
            ['code'=>'KBBBA','name'=>'Bachelor of Business Administration with Information Systems','level'=>'Bachelor','dept'=>'Information Systems','campus'=>$kiz,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'KBBACM','name'=>'Bachelor of Accounting with Co-operative Management','level'=>'Bachelor','dept'=>'Accounting','campus'=>$kiz,'years'=>3,'mode'=>'Full Time'],

            // ================= POSTGRADUATE DIPLOMA (1 Year) =================
            ['code'=>'PGDAF-E','name'=>'Postgraduate Diploma in Accounting and Finance — Evening','level'=>'Postgraduate Diploma','dept'=>'Accounting','campus'=>$main,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDAF-F','name'=>'Postgraduate Diploma in Accounting and Finance — Full Time','level'=>'Postgraduate Diploma','dept'=>'Accounting','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDSCC-F','name'=>'Postgraduate Diploma in Savings and Credit Co-operatives Societies Management — Full Time','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDCBM-F','name'=>'Postgraduate Diploma in Co-operative Business Management — Full Time','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDCBM-E','name'=>'Postgraduate Diploma in Co-operative Business Management — Evening','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDCD-E','name'=>'Postgraduate Diploma in Community Development — Evening','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDCD-F','name'=>'Postgraduate Diploma in Community Development — Full Time','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDSCC-D','name'=>'Postgraduate Diploma in Savings and Credit Co-operatives Societies Management — Distance','level'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$main,'years'=>1,'mode'=>'Distance'],

            // ================= MASTERS (2 Years unless noted) =================
            ['code'=>'MBF','name'=>'Master of Banking and Financial Services','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MBM-E','name'=>'Master of Business Management — Evening','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Evening'],
            ['code'=>'MBM-F','name'=>'Master of Business Management — Full Time','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MPSM-F','name'=>'Master of Arts in Procurement and Supply Management — Full Time','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MACD-E','name'=>'Master of Arts in Co-operative and Community Development — Evening','level'=>'Masters','dept'=>'Economics','campus'=>$main,'years'=>2,'mode'=>'Evening'],
            ['code'=>'MACD-F','name'=>'Master of Arts in Co-operative and Community Development — Full Time','level'=>'Masters','dept'=>'Economics','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MCM','name'=>'Master of Co-operative Management','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MDP','name'=>'Master of Development Planning','level'=>'Masters','dept'=>'Economics','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MCommDev-E','name'=>'Master of Community Development — Evening','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>3,'mode'=>'Evening'],
            ['code'=>'MCommDev-F','name'=>'Master of Community Development — Full Time','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MPPM','name'=>'Master of Project Planning and Management','level'=>'Masters','dept'=>'Business Administration','campus'=>$main,'years'=>2,'mode'=>'Full Time'],

            // ================= PHD =================
            ['code'=>'PHD','name'=>'Doctor of Philosophy','level'=>'PhD','dept'=>'Business Administration','campus'=>$main,'years'=>4,'mode'=>'Full Time'],
        ];

        $inserted = 0; $updated = 0;
        foreach ($rows as $r) {
            $exists = Programme::where('code', $r['code'])->first();
            $data = [
                'department_id'      => $this->dept($r['dept']),
                'campus_id'          => $r['campus'],
                'admission_level_id' => $this->level($r['level']),
                'name'               => $r['name'],
                'code'               => $r['code'],
                'duration_years'     => $r['years'],
                'study_mode'         => $r['mode'],
                'status'             => 'active',
            ];
            if ($exists) {
                $exists->update($data); $updated++;
            } else {
                Programme::create($data); $inserted++;
            }
        }

        echo "MoCU catalogue synced: {$inserted} added, {$updated} updated.\n";
    }

    protected function level(string $name): int
    {
        if (isset($this->levelCache[$name])) return $this->levelCache[$name];
        $id = AdmissionLevel::where('name', $name)->value('id');
        if (!$id) throw new \RuntimeException("Admission level not found: {$name}");
        return $this->levelCache[$name] = $id;
    }

    protected function dept(string $wanted): int
    {
        if (isset($this->deptCache[$wanted])) return $this->deptCache[$wanted];

        $id = Department::where('name', $wanted)->value('id');
        if (!$id) {
            // map to closest existing department by keyword
            $likes = ['Library'=>'Library and Information Science','Information'=>'Information Systems','Accounting'=>'Accounting','Economics'=>'Economics','Business'=>'Business Administration','Computer'=>'Computer Science'];
            $match = null;
            foreach ($likes as $kw => $dept) { if (str_contains($wanted, $kw)) { $match = $dept; break; } }
            $id = Department::where('name', $match ?? 'Business Administration')->value('id')
                ?: (DB::table('departments')->insertGetId([
                    'name' => $wanted, 'faculty_id' => DB::table('faculties')->value('id'),
                    'created_at' => now(), 'updated_at' => now(),
                ]));
        }
        return $this->deptCache[$wanted] = $id;
    }
}
